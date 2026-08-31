<?php

namespace App\Policies;

use App\Models\Marque;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Auth\Access\Response;

class MarquePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Marque $marque): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Marque $marque): bool
    {
        // Seul l'admin peut modifier les marques
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Marque $marque): bool
    {
        // Seul l'admin peut supprimer les marques utilisateur
        return $user->role === UserRole::ADMIN && !$marque->est_systeme;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Marque $marque): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Marque $marque): bool
    {
        return false;
    }
}
