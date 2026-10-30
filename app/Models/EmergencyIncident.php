<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class EmergencyIncident extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'public_reference',
        'reporter_user_id',
        'reporter_name',
        'reporter_phone',
        'emergency_type',
        'description',
        'voice_transcript',
        'ai_summary',
        'latitude',
        'longitude',
        'address',
        'area',
        'city',
        'district',
        'number_of_victims',
        'severity_level',
        'priority_score',
        'severity_reasons',
        'required_service',
        'required_equipment',
        'required_skills',
        'status',
        'ai_confidence',
        'is_sos',
        'is_mci',
        'mci_level',
        'mci_triage_counts',
        'mci_hospital_allocations',
        'mci_declared_at',
        'assigned_resource_id',
        'assigned_hospital_id',
        'reported_at',
        'dispatched_at',
        'arrived_at',
        'resolved_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'number_of_victims' => 'integer',
        'priority_score' => 'integer',
        'ai_confidence' => 'float',
        'is_sos' => 'boolean',
        'is_mci' => 'boolean',
        'mci_triage_counts' => 'array',
        'mci_hospital_allocations' => 'array',
        'mci_declared_at' => 'datetime',
        'severity_reasons' => 'array',
        'required_equipment' => 'array',
        'required_skills' => 'array',
        'reported_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'arrived_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($incident) {
            if (empty($incident->public_reference)) {
                $incident->public_reference = self::generatePublicReference();
            }
            if (empty($incident->reported_at)) {
                $incident->reported_at = now();
            }
        });
    }

    /**
     * Generate human-friendly reference: NH-YYYY-XXXXXX
     */
    public static function generatePublicReference(): string
    {
        $year = date('Y');
        do {
            $ref = 'NH-' . $year . '-' . str_pad((string) mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('public_reference', $ref)->exists());

        return $ref;
    }

    // Relationships
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_user_id');
    }

    public function assignedResource(): BelongsTo
    {
        return $this->belongsTo(Resource::class, 'assigned_resource_id');
    }

    public function assignedHospital(): BelongsTo
    {
        return $this->belongsTo(Resource::class, 'assigned_hospital_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(EmergencyIncidentEvent::class, 'incident_id')->orderBy('created_at', 'asc');
    }

    public function dispatches(): HasMany
    {
        return $this->hasMany(EmergencyDispatch::class, 'incident_id')->latest('dispatched_at');
    }

    public function activeDispatch(): HasOne
    {
        return $this->hasOne(EmergencyDispatch::class, 'incident_id')
            ->whereIn('status', ['PENDING', 'ACCEPTED', 'EN_ROUTE', 'ON_SCENE', 'TRANSPORTING'])
            ->latestOfMany('dispatched_at');
    }

    public function cfrAlerts(): HasMany
    {
        return $this->hasMany(CfrAlert::class, 'emergency_incident_id');
    }

    public function activeCfrAlert(): HasOne
    {
        return $this->hasOne(CfrAlert::class, 'emergency_incident_id')
            ->whereIn('status', ['ACCEPTED', 'ON_SCENE'])
            ->latestOfMany('alerted_at');
    }

    public function bloodDonorAlerts(): HasMany
    {
        return $this->hasMany(BloodDonorAlert::class, 'emergency_incident_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['RESOLVED', 'CANCELLED']);
    }

    public function scopeCritical($query)
    {
        return $query->where('severity_level', 'P1');
    }

    public function scopeMci($query)
    {
        return $query->where('is_mci', true);
    }

    public function scopePrioritizedQueue($query)
    {
        return $query->active()->orderByDesc('priority_score')->orderBy('reported_at', 'asc');
    }

    // Helpers
    public function isCritical(): bool
    {
        return $this->severity_level === 'P1';
    }

    public function isMci(): bool
    {
        return (bool) $this->is_mci;
    }

    public function isActive(): bool
    {
        return !in_array($this->status, ['RESOLVED', 'CANCELLED'], true);
    }

    public function logEvent(string $eventType, string $title, ?string $description = null, ?array $metadata = null, ?User $actor = null, ?string $fromStatus = null, ?string $toStatus = null): EmergencyIncidentEvent
    {
        return $this->events()->create([
            'actor_id' => $actor?->id,
            'event_type' => $eventType,
            'from_status' => $fromStatus ?? $this->getOriginal('status'),
            'to_status' => $toStatus ?? $this->status,
            'title' => $title,
            'description' => $description,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }

    public function getSeverityColorClasses(): string
    {
        return match ($this->severity_level) {
            'P1' => 'bg-red-100 text-red-800 border-red-200 dark:bg-red-950/70 dark:text-red-300 dark:border-red-900 animate-pulse',
            'P2' => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950/70 dark:text-amber-300 dark:border-amber-900',
            'P3' => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-950/70 dark:text-blue-300 dark:border-blue-900',
            default => 'bg-slate-100 text-slate-800 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
        };
    }

    public function getStatusColorClasses(): string
    {
        return match ($this->status) {
            'REPORTED', 'ANALYZING' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/50 dark:text-amber-400 dark:border-amber-900',
            'PRIORITIZED', 'AWAITING_DISPATCH' => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/50 dark:text-purple-400 dark:border-purple-900',
            'DISPATCHED', 'RESPONDER_ACCEPTED', 'RESPONDER_EN_ROUTE' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/50 dark:text-blue-400 dark:border-blue-900',
            'RESPONDER_ARRIVED', 'PATIENT_PICKED_UP', 'AT_HOSPITAL' => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/50 dark:text-indigo-400 dark:border-indigo-900',
            'RESOLVED' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-400 dark:border-emerald-900',
            'CANCELLED' => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-400 dark:border-slate-700',
            default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
        };
    }
}
