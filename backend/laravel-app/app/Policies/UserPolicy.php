<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Allow 'admin' and 'partner' to view any users
        return in_array($user->type, ['admin', 'parceiro']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Allow 'admin' to view any user
        // Allow 'partner' to view only their own profile (or set your logic accordingly)
        return $user->type === 'admin' || ($user->type === 'parceiro' && $user->id === $model->id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Allow only 'admin' to create users
        return $user->type === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Allow 'admin' to update any user
        // Allow 'partner' to update only their own profile
        return $user->type === 'admin' || ($user->type === 'parceiro' && $user->id === $model->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Allow only 'admin' to delete users
        return $user->type === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        // Allow only 'admin' to restore users
        return $user->type === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Allow only 'admin' to permanently delete users
        return $user->type === 'admin';
    }
}
