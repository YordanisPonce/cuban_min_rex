<?php

namespace App\Policies;

use App\Models\PlayList;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PlayListPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'worker';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PlayList $playList): bool
    {
        return $user->role === 'admin' || $playList->is_public || $playList->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'worker';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PlayList $playList): bool
    {
        return $user->role === 'admin' || $playList->is_public || $playList->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PlayList $playList): bool
    {
        return $user->role === 'admin' || $playList->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PlayList $playList): bool
    {
        return $user->role === 'admin' || $playList->user_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PlayList $playList): bool
    {
        return $user->role === 'admin' || $playList->user_id === $user->id;
    }
}
