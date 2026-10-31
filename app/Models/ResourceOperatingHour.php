<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResourceOperatingHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'resource_id',
        'day_of_week',
        'open_time',
        'close_time',
        'is_closed',
        'is_24_hours',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'is_closed' => 'boolean',
        'is_24_hours' => 'boolean',
    ];

    public static array $days = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    public function getDayNameAttribute(): string
    {
        return self::$days[$this->day_of_week] ?? 'Unknown';
    }

    public function getFormattedHoursAttribute(): string
    {
        if ($this->is_closed) {
            return 'Closed';
        }
        if ($this->is_24_hours) {
            return '24 Hours Open';
        }
        if ($this->open_time && $this->close_time) {
            $open = date('h:i A', strtotime($this->open_time));
            $close = date('h:i A', strtotime($this->close_time));
            return "$open - $close";
        }
        return 'Standard Hours';
    }
}
