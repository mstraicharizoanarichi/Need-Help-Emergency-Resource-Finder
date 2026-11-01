<?php

namespace App\Policies;

use App\Models\Resource;
use App\Models\User;

class ResourcePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isProvider();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Resource $resource): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isProvider() && $resource->user_id === $user->id) {
            return true;
        }

        return $resource->isVerified();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return ($user->isAdmin() || $user->isProvider()) && $user->isActive();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Resource $resource): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isProvider() 
            && $user->isActive() 
            && $resource->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Resource $resource): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isProvider() 
            && $user->isActive() 
            && $resource->user_id === $user->id;
    }

    /**
     * Determine whether the user can verify the model.
     */
    public function verify(User $user): bool
    {
        return $user->isAdmin();
    }
}
