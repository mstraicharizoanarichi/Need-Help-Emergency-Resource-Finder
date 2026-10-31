<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloodDonorAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'emergency_incident_id',
        'blood_donor_id',
        'distance_km',
        'status',
    ];

    protected $casts = [
        'distance_km' => 'float',
    ];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(EmergencyIncident::class, 'emergency_incident_id');
    }

    public function donor(): BelongsTo
    {
        return $this->belongsTo(BloodDonor::class, 'blood_donor_id');
    }
}
