<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BloodDonor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'full_name',
        'blood_group',
        'phone',
        'alternative_phone',
        'email',
        'district',
        'city',
        'area',
        'latitude',
        'longitude',
        'is_available',
        'last_donation_date',
        'total_donations',
        'verification_status',
        'privacy_setting',
        'notes',
        'last_platelet_donation_date',
        'is_apheresis_donor',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_available' => 'boolean',
        'last_donation_date' => 'date',
        'last_platelet_donation_date' => 'date',
        'is_apheresis_donor' => 'boolean',
        'total_donations' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contactRequests(): HasMany
    {
        return $this->hasMany(DonorContactRequest::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(BloodDonorAlert::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function scopeGroup($query, string $bloodGroup)
    {
        return $query->where('blood_group', $bloodGroup);
    }

    // Helpers
    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function canShowPhonePublicly(): bool
    {
        return $this->privacy_setting === 'direct_call';
    }

    public function getNameAttribute(): string
    {
        return $this->full_name;
    }

    public function isEligibleToDonate(): bool
    {
        return $this->getIsEligibleForWholeBloodAttribute();
    }

    public function getIsEligibleForWholeBloodAttribute(): bool
    {
        if (!$this->is_available) {
            return false;
        }

        if (!$this->last_donation_date) {
            return true;
        }

        return Carbon::parse($this->last_donation_date)->addDays(90)->isPast();
    }

    public function getIsEligibleForPlateletsAttribute(): bool
    {
        if (!$this->is_available || !$this->is_apheresis_donor) {
            return false;
        }

        if (!$this->last_platelet_donation_date) {
            return true;
        }

        return Carbon::parse($this->last_platelet_donation_date)->addDays(7)->isPast();
    }

    public function getDaysUntilEligibleAttribute(): int
    {
        if (!$this->last_donation_date) {
            return 0;
        }

        $nextEligibleDate = Carbon::parse($this->last_donation_date)->addDays(90);
        if ($nextEligibleDate->isPast()) {
            return 0;
        }

        return (int) Carbon::now()->diffInDays($nextEligibleDate, false);
    }

    public function getMaskedPhoneAttribute(): string
    {
        $phone = $this->phone;
        if (strlen($phone) <= 4) {
            return '***';
        }

        $start = substr($phone, 0, 5);
        $end = substr($phone, -2);
        return $start . '*****' . $end;
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
}
