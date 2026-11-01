<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'resource_id',
        'name',
        'slug',
        'specialization',
        'department',
        'designation',
        'qualifications',
        'experience_years',
        'visiting_days',
        'visiting_hours',
        'appointment_phone',
        'consultation_fee',
        'is_emergency_available',
        'is_available_today',
        'room_number',
        'rating',
        'total_reviews',
        'gender',
        'avatar',
        'bio',
        'status',
    ];

    protected $casts = [
        'visiting_days' => 'array',
        'is_emergency_available' => 'boolean',
        'is_available_today' => 'boolean',
        'rating' => 'float',
        'total_reviews' => 'integer',
        'experience_years' => 'integer',
        'consultation_fee' => 'float',
    ];

    public static array $popularSpecializations = [
        'Cardiology' => 'Cardiology (Heart & Vascular)',
        'Emergency Medicine' => 'Emergency Medicine & Trauma',
        'Neurology' => 'Neurology & Brain Specialist',
        'Orthopedics' => 'Orthopedics & Bone/Joint Surgery',
        'Pediatrics' => 'Pediatrics & Child Health',
        'Gynecology & Obstetrics' => 'Gynecology & Obstetrics',
        'General Surgery' => 'General & Laparoscopic Surgery',
        'Gastroenterology' => 'Gastroenterology & Liver',
        'Nephrology' => 'Nephrology & Kidney Disease',
        'Pulmonology' => 'Pulmonology & Chest/Respiratory',
        'Dermatology' => 'Dermatology & Skin Care',
        'Ophthalmology' => 'Ophthalmology (Eye Specialist)',
        'ENT' => 'ENT (Ear, Nose, Throat)',
        'Internal Medicine' => 'Internal Medicine & General Physician',
        'Oncology' => 'Oncology & Cancer Care',
        'Psychiatry' => 'Psychiatry & Mental Health',
    ];

    protected static function booted(): void
    {
        static::creating(function ($doctor) {
            if (empty($doctor->slug)) {
                $doctor->slug = Str::slug($doctor->name . '-' . $doctor->specialization) . '-' . Str::lower(Str::random(5));
            }
        });
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeEmergency($query)
    {
        return $query->where('is_emergency_available', true);
    }

    public function scopeSpecialization($query, $specialization)
    {
        if (empty($specialization)) {
            return $query;
        }
        return $query->where('specialization', 'LIKE', "%{$specialization}%");
    }

    public function scopeSearch($query, $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'LIKE', "%{$term}%")
              ->orWhere('specialization', 'LIKE', "%{$term}%")
              ->orWhere('qualifications', 'LIKE', "%{$term}%")
              ->orWhere('department', 'LIKE', "%{$term}%")
              ->orWhere('designation', 'LIKE', "%{$term}%")
              ->orWhereHas('resource', function ($rq) use ($term) {
                  $rq->where('name', 'LIKE', "%{$term}%")
                     ->orWhere('city', 'LIKE', "%{$term}%")
                     ->orWhere('area', 'LIKE', "%{$term}%");
              });
        });
    }

    // Helpers
    public function isVisitingToday(): bool
    {
        if (!$this->is_available_today) {
            return false;
        }

        $days = (array) ($this->visiting_days ?? []);
        if (empty($days)) {
            return true; // daily if unstated
        }

        $todayShort = Carbon::now()->format('D'); // e.g. "Sun", "Mon", "Tue"
        $todayFull = Carbon::now()->format('l'); // e.g. "Sunday", "Monday"

        foreach ($days as $day) {
            if (strcasecmp($day, $todayShort) === 0 || strcasecmp($day, $todayFull) === 0 || str_starts_with(strtolower($day), strtolower($todayShort))) {
                return true;
            }
        }

        return false;
    }

    public function getCleanPhone(): string
    {
        $phone = $this->appointment_phone ?: optional($this->resource)->getCleanPhone();
        return preg_replace('/[^0-9+]/', '', (string)$phone);
    }

    public function getSpecialtyBadgeColor(): string
    {
        return match (strtolower(trim(explode('&', explode('(', $this->specialization)[0])[0]))) {
            'cardiology' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800',
            'emergency medicine' => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-950/50 dark:text-red-300 dark:border-red-800',
            'neurology' => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-950/40 dark:text-indigo-300 dark:border-indigo-800',
            'pediatrics' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800',
            'orthopedics' => 'bg-teal-50 text-teal-700 border-teal-200 dark:bg-teal-950/40 dark:text-teal-300 dark:border-teal-800',
            'gynecology' => 'bg-pink-50 text-pink-700 border-pink-200 dark:bg-pink-950/40 dark:text-pink-300 dark:border-pink-800',
            'general surgery' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800',
            'gastroenterology' => 'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-950/40 dark:text-orange-300 dark:border-orange-800',
            'nephrology' => 'bg-cyan-50 text-cyan-700 border-cyan-200 dark:bg-cyan-950/40 dark:text-cyan-300 dark:border-cyan-800',
            default => 'bg-slate-100 text-slate-700 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
        };
    }
}
