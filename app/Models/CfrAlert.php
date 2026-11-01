<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CfrAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'emergency_incident_id',
        'community_first_responder_id',
        'status',
        'distance_km',
        'alerted_at',
        'responded_at',
        'arrived_at',
        'notes',
    ];

    protected $casts = [
        'distance_km' => 'float',
        'alerted_at' => 'datetime',
        'responded_at' => 'datetime',
        'arrived_at' => 'datetime',
    ];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(EmergencyIncident::class, 'emergency_incident_id');
    }

    public function responder(): BelongsTo
    {
        return $this->belongsTo(CommunityFirstResponder::class, 'community_first_responder_id');
    }
}
