<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmergencyDispatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_id',
        'resource_id',
        'dispatched_by_user_id',
        'status',
        'match_score',
        'match_rank',
        'match_explanation',
        'estimated_distance_km',
        'estimated_eta_minutes',
        'actual_arrival_minutes',
        'traffic_multiplier_applied',
        'rejection_reason',
        'notes',
        'dispatched_at',
        'responded_at',
        'en_route_at',
        'arrived_at',
        'completed_at',
    ];

    protected $casts = [
        'match_score' => 'float',
        'match_rank' => 'integer',
        'match_explanation' => 'array',
        'estimated_distance_km' => 'float',
        'estimated_eta_minutes' => 'float',
        'actual_arrival_minutes' => 'float',
        'traffic_multiplier_applied' => 'float',
        'dispatched_at' => 'datetime',
        'responded_at' => 'datetime',
        'en_route_at' => 'datetime',
        'arrived_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(EmergencyIncident::class, 'incident_id');
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class, 'resource_id');
    }

    public function dispatchedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatched_by_user_id');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['PENDING', 'ACCEPTED', 'EN_ROUTE', 'ON_SCENE', 'TRANSPORTING']);
    }

    public function getStatusBadgeClasses(): string
    {
        return match ($this->status) {
            'PENDING' => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-950/60 dark:text-amber-300',
            'ACCEPTED' => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-950/60 dark:text-blue-300',
            'EN_ROUTE' => 'bg-indigo-100 text-indigo-800 border-indigo-200 dark:bg-indigo-950/60 dark:text-indigo-300 animate-pulse',
            'ON_SCENE' => 'bg-purple-100 text-purple-800 border-purple-200 dark:bg-purple-950/60 dark:text-purple-300',
            'TRANSPORTING' => 'bg-cyan-100 text-cyan-800 border-cyan-200 dark:bg-cyan-950/60 dark:text-cyan-300',
            'COMPLETED' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300',
            'REJECTED', 'CANCELLED' => 'bg-rose-100 text-rose-800 border-rose-200 dark:bg-rose-950/60 dark:text-rose-300',
            default => 'bg-slate-100 text-slate-800 border-slate-200 dark:bg-slate-800 dark:text-slate-300',
        };
    }
}
