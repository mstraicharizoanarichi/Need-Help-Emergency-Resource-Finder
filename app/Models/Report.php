<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'resource_id',
        'blood_donor_id',
        'reporter_user_id',
        'reporter_name',
        'reporter_email',
        'reporter_phone',
        'report_type',
        'description',
        'status',
        'admin_notes',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    public function bloodDonor(): BelongsTo
    {
        return $this->belongsTo(BloodDonor::class);
    }

    public function reporterUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_user_id');
    }

    public function resolvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function getFormattedTypeAttribute(): string
    {
        return match ($this->report_type) {
            'incorrect_phone' => 'Incorrect Phone Number',
            'wrong_location' => 'Wrong Location / Coordinates',
            'closed_service' => 'Service Permanently Closed',
            'outdated_info' => 'Outdated Information',
            'duplicate_resource' => 'Duplicate Resource Entry',
            'incorrect_availability' => 'Incorrect Availability Status',
            default => 'Other Issue',
        };
    }

    public function getStatusBadgeClasses(): string
    {
        return match ($this->status) {
            'resolved' => 'bg-emerald-100 text-emerald-800 border-emerald-300 dark:bg-emerald-950/60 dark:text-emerald-300',
            'reviewing' => 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-950/60 dark:text-blue-300',
            'rejected' => 'bg-slate-100 text-slate-700 border-slate-300 dark:bg-slate-800 dark:text-slate-300',
            default => 'bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-950/60 dark:text-amber-300', // pending
        };
    }
}
