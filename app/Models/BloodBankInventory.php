<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloodBankInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'resource_id',
        'blood_group',
        'component_type',
        'units',
        'last_updated_at',
    ];

    protected $casts = [
        'units' => 'integer',
        'last_updated_at' => 'datetime',
    ];

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }
}
