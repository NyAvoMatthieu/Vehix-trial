<?php

namespace App\Policies;

use App\Models\Proprietaire;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProprietairePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admins et validateurs peuvent voir tous les propriétaires
        // Clients peuvent voir leurs propres propriétaires
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Proprietaire $proprietaire): bool
    {
        // Admins et validateurs peuvent voir tous les propriétaires
        if ($user->isAdmin() || $user->isValidator()) {
            return true;
        }

        // Les clients peuvent voir uniquement leurs propres propriétaires
        return $user->id === $proprietaire->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Tous les utilisateurs authentifiés peuvent créer un propriétaire
        // Mais un utilisateur ne peut avoir qu'un seul propriétaire
        return !$user->proprietaire()->exists();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Proprietaire $proprietaire): bool
    {
        // Admins peuvent modifier tous les propriétaires
        if ($user->isAdmin()) {
            return true;
        }

        // Les utilisateurs peuvent modifier uniquement leur propre propriétaire
        return $user->id === $proprietaire->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Proprietaire $proprietaire): bool
    {
        // Admins peuvent supprimer tous les propriétaires
        if ($user->isAdmin()) {
            return true;
        }

        // Les utilisateurs peuvent supprimer uniquement leur propre propriétaire
        return $user->id === $proprietaire->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Proprietaire $proprietaire): bool
    {
         return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Proprietaire $proprietaire): bool
    {
        return false;
    }
}
