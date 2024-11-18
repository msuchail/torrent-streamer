<?php

namespace App\Policies;

use App\Models\Serie;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SeriePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->active;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Serie $serie): bool
    {
        return $serie->groups()->count() === 0 || $serie->groups()->get()->contains($user->group);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Serie $serie): bool
    {
        return $user->isAdmin()  && config('app.env') === $serie->environment;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Serie $serie): bool
    {
        return $user->isAdmin() && config('app.env') === $serie->environment;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Serie $serie): bool
    {
        return $user->isAdmin() && config('app.env') === $serie->environment;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Serie $serie): bool
    {
        //
    }
}
