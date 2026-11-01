<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class CommunityFirstResponder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'skills',
        'certification_level',
        'certification_details',
        'is_verified',
        'is_available',
        'status',
        'latitude',
        'longitude',
        'city',
        'area',
        'total_responses',
        'last_active_at',
    ];

    protected $casts = [
        'skills' => 'array',
        'is_verified' => 'boolean',
        'is_available' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'total_responses' => 'integer',
        'last_active_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(CfrAlert::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)
            ->where('status', 'AVAILABLE');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope query to find responders near given latitude/longitude using Haversine formula.
     */
    public function scopeNearby($query, float $lat, float $lng, float $radiusKm = 3.0)
    {
        $haversine = "(6371 * acos(
            cos(radians(?))
            * cos(radians(latitude))
            * cos(radians(longitude) - radians(?))
            + sin(radians(?))
            * sin(radians(latitude))
        ))";

        return $query->select('*')
            ->selectRaw("{$haversine} AS distance", [$lat, $lng, $lat])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance', 'asc');
    }
}
