<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonorContactRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'blood_donor_id',
        'user_id',
        'requester_name',
        'requester_phone',
        'patient_condition',
        'hospital_name',
        'hospital_location',
        'urgency_level',
        'units_needed',
        'message',
        'status',
    ];

    protected $casts = [
        'units_needed' => 'integer',
    ];

    public function donor(): BelongsTo
    {
        return $this->belongsTo(BloodDonor::class, 'blood_donor_id');
    }

    public function bloodDonor(): BelongsTo
    {
        return $this->donor();
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getUrgencyBadgeClasses(): string
    {
        return match ($this->urgency_level) {
            'emergency' => 'bg-red-100 text-red-700 border-red-300 dark:bg-red-950/60 dark:text-red-300',
            'very_urgent' => 'bg-orange-100 text-orange-700 border-orange-300 dark:bg-orange-950/60 dark:text-orange-300',
            default => 'bg-amber-100 text-amber-700 border-amber-300 dark:bg-amber-950/60 dark:text-amber-300',
        };
    }
}
