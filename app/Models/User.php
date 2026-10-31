<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'status',
        'organization_name',
        'organization_type',
        'address',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isProvider(): bool
    {
        return $this->role === 'provider';
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'reporter_user_id');
    }

    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(Resource::class, 'user_favorites')->withTimestamps();
    }

    public function bloodDonorProfile(): HasOne
    {
        return $this->hasOne(BloodDonor::class);
    }

    public function donorRequests(): HasMany
    {
        return $this->hasMany(DonorContactRequest::class);
    }

    public function reportedIncidents(): HasMany
    {
        return $this->hasMany(EmergencyIncident::class, 'reporter_user_id');
    }

    public function operatorDispatches(): HasMany
    {
        return $this->hasMany(EmergencyDispatch::class, 'dispatched_by_user_id');
    }
}
