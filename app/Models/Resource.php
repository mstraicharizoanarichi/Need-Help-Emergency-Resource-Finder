<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Resource extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'slug',
        'description',
        'address',
        'area',
        'city',
        'district',
        'postal_code',
        'latitude',
        'longitude',
        'phone',
        'emergency_phone',
        'email',
        'website',
        'verification_status',
        'verified_at',
        'verified_by',
        'rejection_reason',
        'availability_status',
        'is_24_7',
        'operating_hours_text',
        'is_featured',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_24_7' => 'boolean',
        'is_featured' => 'boolean',
        'verified_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($resource) {
            if (empty($resource->slug)) {
                $resource->slug = Str::slug($resource->name) . '-' . Str::random(6);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ResourceCategory::class, 'category_id');
    }

    public function details(): HasOne
    {
        return $this->hasOne(ResourceDetail::class);
    }

    public function operatingHours(): HasMany
    {
        return $this->hasMany(ResourceOperatingHour::class);
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function bloodBankInventories(): HasMany
    {
        return $this->hasMany(BloodBankInventory::class);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_favorites')->withTimestamps();
    }

    public function verifiedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function assignedIncidents(): HasMany
    {
        return $this->hasMany(EmergencyIncident::class, 'assigned_resource_id');
    }

    public function dispatches(): HasMany
    {
        return $this->hasMany(EmergencyDispatch::class, 'resource_id');
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function scopePending($query)
    {
        return $query->where('verification_status', 'pending');
    }

    public function scopeAvailable($query)
    {
        return $query->where('availability_status', 'available');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Helpers
    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function isAvailable(): bool
    {
        return $this->availability_status === 'available';
    }

    public function isOpenNow(): bool
    {
        if ($this->is_24_7) {
            return true;
        }

        if ($this->availability_status !== 'available') {
            return false;
        }

        $now = Carbon::now();
        $dayOfWeek = $now->dayOfWeek; // 0 (Sun) to 6 (Sat)
        $todaySchedule = $this->operatingHours->firstWhere('day_of_week', $dayOfWeek);

        if (!$todaySchedule) {
            return true; // Default fallback if no detailed daily schedule
        }

        if ($todaySchedule->is_closed) {
            return false;
        }

        if ($todaySchedule->is_24_hours) {
            return true;
        }

        if ($todaySchedule->open_time && $todaySchedule->close_time) {
            $currentTime = $now->format('H:i:s');
            return $currentTime >= $todaySchedule->open_time && $currentTime <= $todaySchedule->close_time;
        }

        return true;
    }

    public function getFormattedDistanceAttribute(): ?string
    {
        if (!isset($this->attributes['distance'])) {
            return null;
        }

        $km = (float) $this->attributes['distance'];
        if ($km < 1.0) {
            return round($km * 1000) . ' m';
        }

        return number_format($km, 1) . ' km';
    }

    public function getDirectionsUrl(): string
    {
        return "https://www.google.com/maps/dir/?api=1&destination={$this->latitude},{$this->longitude}";
    }

    public function getCleanPhone(): string
    {
        return preg_replace('/[^0-9+]/', '', $this->emergency_phone ?: $this->phone);
    }
}
