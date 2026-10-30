<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'resource_id',
        'icu_available',
        'icu_beds_count',
        'ambulance_available',
        'oxygen_available',
        'blood_bank_available',
        'emergency_ward',
        'general_beds_count',
        'vehicle_types',
        'paramedic_onboard',
        'home_delivery',
        'online_reports',
        'services_offered',
        'facilities',
        'accepted_insurances',
        'additional_info',
    ];

    protected $casts = [
        'icu_available' => 'boolean',
        'icu_beds_count' => 'integer',
        'ambulance_available' => 'boolean',
        'oxygen_available' => 'boolean',
        'blood_bank_available' => 'boolean',
        'emergency_ward' => 'boolean',
        'general_beds_count' => 'integer',
        'paramedic_onboard' => 'boolean',
        'home_delivery' => 'boolean',
        'online_reports' => 'boolean',
        'services_offered' => 'array',
        'facilities' => 'array',
        'accepted_insurances' => 'array',
    ];

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }
}
