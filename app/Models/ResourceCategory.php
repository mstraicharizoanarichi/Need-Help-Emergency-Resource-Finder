<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ResourceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'color',
        'description',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('display_order', 'asc');
    }

    public function getIconColorClasses(): string
    {
        return match ($this->slug) {
            'hospital' => 'bg-red-50 text-red-600 border-red-200 dark:bg-red-950/40 dark:text-red-400 dark:border-red-900',
            'ambulance' => 'bg-orange-50 text-orange-600 border-orange-200 dark:bg-orange-950/40 dark:text-orange-400 dark:border-orange-900',
            'pharmacy' => 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-900',
            'blood-donor' => 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-900',
            'police-station' => 'bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-900',
            'fire-service' => 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-900',
            'diagnostic-center' => 'bg-purple-50 text-purple-600 border-purple-200 dark:bg-purple-950/40 dark:text-purple-400 dark:border-purple-900',
            default => 'bg-slate-50 text-slate-600 border-slate-200 dark:bg-slate-900 dark:text-slate-400 dark:border-slate-800',
        };
    }
}
