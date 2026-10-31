<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'incident_type',
        'severity',
        'answers',
        'red_flags',
        'emergency_triggered',
    ];

    protected $casts = [
        'answers' => 'array',
        'red_flags' => 'array',
        'emergency_triggered' => 'boolean',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }
}
