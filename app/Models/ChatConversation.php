<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChatConversation extends Model
{
    use HasFactory;

    public const STATE_IDLE = 'IDLE';
    public const STATE_INFORMATION = 'INFORMATION';
    public const STATE_ASSESSMENT = 'ASSESSMENT';
    public const STATE_POSSIBLE_EMERGENCY = 'POSSIBLE_EMERGENCY';
    public const STATE_EMERGENCY_CONFIRMED = 'EMERGENCY_CONFIRMED';
    public const STATE_EMERGENCY_CANCELLED = 'EMERGENCY_CANCELLED';
    public const STATE_COMPLETED = 'COMPLETED';

    protected $fillable = [
        'session_id',
        'user_id',
        'language',
        'status',
        'detected_intent',
        'active_intent',
        'severity',
        'severity_level',
        'emergency_detected',
        'emergency_mode',
        'is_emergency_active',
        'current_question_key',
        'collected_data',
        'assessment_data',
        'emergency_incident_id',
        'metadata',
    ];

    protected $casts = [
        'emergency_detected' => 'boolean',
        'emergency_mode' => 'boolean',
        'collected_data' => 'array',
        'metadata' => 'array',
    ];

    protected $appends = [
        'severity_level',
        'is_emergency_active',
        'active_intent',
        'assessment_data',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function emergencyIncident(): BelongsTo
    {
        return $this->belongsTo(EmergencyIncident::class, 'emergency_incident_id');
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(EmergencyIncident::class, 'emergency_incident_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id')->orderBy('created_at', 'asc');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(ChatAssessment::class, 'conversation_id')->orderBy('created_at', 'asc');
    }

    public function assessment(): HasOne
    {
        return $this->hasOne(ChatAssessment::class, 'conversation_id')->latestOfMany();
    }

    // Accessors & Mutators
    public function getSeverityLevelAttribute(): ?string
    {
        return $this->severity ?? 'P3';
    }

    public function setSeverityLevelAttribute(?string $value): void
    {
        $this->severity = $value;
    }

    public function getIsEmergencyActiveAttribute(): bool
    {
        return (bool) ($this->emergency_mode || $this->emergency_detected);
    }

    public function setIsEmergencyActiveAttribute(bool $value): void
    {
        $this->emergency_mode = (bool) $value;
        $this->emergency_detected = (bool) $value;
    }

    public function getActiveIntentAttribute(): ?string
    {
        return $this->detected_intent;
    }

    public function setActiveIntentAttribute(?string $value): void
    {
        $this->detected_intent = $value;
    }

    public function getAssessmentDataAttribute(): array
    {
        return (array) ($this->collected_data ?? []);
    }

    public function setAssessmentDataAttribute(mixed $value): void
    {
        $this->collected_data = is_array($value) ? $value : (is_string($value) ? (json_decode($value, true) ?: []) : []);
    }

    public function recordAnswer(string $questionKey, mixed $answer): void
    {
        $data = (array) ($this->collected_data ?? []);
        $data[$questionKey] = $answer;
        $this->collected_data = $data;
        $this->current_question_key = $questionKey;
        $this->save();
    }

    public function activateEmergencyMode(?string $intentOrSeverity = 'P1', ?string $severity = null): void
    {
        $this->emergency_detected = true;
        $this->emergency_mode = true;

        if (in_array($intentOrSeverity, ['P1', 'P2', 'P3'], true)) {
            $this->severity = $intentOrSeverity;
            if ($severity) {
                $this->detected_intent = $severity;
            }
        } else {
            $this->detected_intent = $intentOrSeverity;
            $this->severity = $severity ?? 'P1';
        }

        $this->save();
    }

    public function getState(): string
    {
        $status = strtoupper($this->status ?? self::STATE_IDLE);
        if (in_array($status, [
            self::STATE_IDLE,
            self::STATE_INFORMATION,
            self::STATE_ASSESSMENT,
            self::STATE_POSSIBLE_EMERGENCY,
            self::STATE_EMERGENCY_CONFIRMED,
            self::STATE_EMERGENCY_CANCELLED,
            self::STATE_COMPLETED,
        ], true)) {
            return $status;
        }

        if ($status === 'ACTIVE') {
            return $this->is_emergency_active ? self::STATE_ASSESSMENT : self::STATE_IDLE;
        }

        return self::STATE_IDLE;
    }

    public function transitionTo(string $state, array $attributes = []): void
    {
        $this->status = $state;

        if ($state === self::STATE_EMERGENCY_CANCELLED || $state === self::STATE_INFORMATION || $state === self::STATE_IDLE) {
            $this->emergency_detected = false;
            $this->emergency_mode = false;
            $this->current_question_key = null;
            if (empty($attributes['severity'])) {
                $this->severity = 'P3';
            }
        } elseif (in_array($state, [self::STATE_EMERGENCY_CONFIRMED, self::STATE_POSSIBLE_EMERGENCY, self::STATE_ASSESSMENT], true)) {
            $sev = $attributes['severity'] ?? $this->severity ?? 'P1';
            if ($sev === 'P1' || $sev === 'P2') {
                $this->emergency_detected = true;
                $this->emergency_mode = true;
                $this->severity = $sev;
            }
        }

        foreach ($attributes as $key => $val) {
            $this->{$key} = $val;
        }

        $this->save();
    }

    public function resetEmergencyState(string $newState = self::STATE_INFORMATION): void
    {
        $meta = (array) ($this->metadata ?? []);
        if ($this->detected_intent) {
            $meta['previous_intent'] = $this->detected_intent;
        }
        $meta['cancelled_at'] = now()->toIso8601String();

        $assessmentData = (array) ($this->collected_data ?? []);
        unset($assessmentData['current_question_key']);

        $this->status = $newState;
        $this->emergency_detected = false;
        $this->emergency_mode = false;
        $this->severity = 'P3';
        $this->detected_intent = null;
        $this->current_question_key = null;
        $this->collected_data = $assessmentData;
        $this->metadata = $meta;
        $this->save();
    }

    public function clearActiveQuestion(): void
    {
        $this->current_question_key = null;
        $data = (array) ($this->collected_data ?? []);
        unset($data['current_question_key']);
        $this->collected_data = $data;
        $this->save();
    }

    public function linkIncident(EmergencyIncident $incident): void
    {
        $this->emergency_incident_id = $incident->id;
        $this->status = self::STATE_EMERGENCY_CONFIRMED;
        $this->emergency_mode = true;
        $this->emergency_detected = true;
        $this->severity = 'P1';
        $this->save();
    }
}
