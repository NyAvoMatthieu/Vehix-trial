<?php

namespace App\Policies;

use App\Models\FuelPrice;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Auth\Access\Response;

class FuelPricePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Seuls les admins et validators peuvent voir la liste des prix
        return in_array($user->role, [UserRole::ADMIN, UserRole::VALIDATOR]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FuelPrice $fuelPrice): bool
    {
        // Seuls les admins et validators peuvent voir un prix
        return in_array($user->role, [UserRole::ADMIN, UserRole::VALIDATOR]);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Seuls les admins et validators peuvent créer des prix
        return in_array($user->role, [UserRole::ADMIN, UserRole::VALIDATOR]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FuelPrice $fuelPrice): bool
    {
        // Seuls les admins et validators peuvent modifier des prix
        return in_array($user->role, [UserRole::ADMIN, UserRole::VALIDATOR]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FuelPrice $fuelPrice): bool
    {
        // Seuls les admins peuvent supprimer des prix
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, FuelPrice $fuelPrice): bool
    {
        // Seuls les admins peuvent restaurer des prix
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, FuelPrice $fuelPrice): bool
    {
        // Seuls les admins peuvent supprimer définitivement des prix
        return $user->role === UserRole::ADMIN;
    }

    /**
     * Determine whether the user can toggle active status.
     */
    public function toggleActive(User $user, FuelPrice $fuelPrice): bool
    {
        // Seuls les admins et validators peuvent activer/désactiver des prix
        return in_array($user->role, [UserRole::ADMIN, UserRole::VALIDATOR]);
    }
}