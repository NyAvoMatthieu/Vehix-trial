<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VisiteTechnique;

class VisiteTechniquePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, VisiteTechnique $visiteTechnique): bool
    {
        return $user->id === $visiteTechnique->user_id || $user->canValidateVehicules();
    }

    public function create(User $user): bool
    {
        return $user->hasValidatedVehicule();
    }

    public function update(User $user, VisiteTechnique $visiteTechnique): bool
    {
        return $user->id === $visiteTechnique->user_id;
    }

    public function delete(User $user, VisiteTechnique $visiteTechnique): bool
    {
        return $user->id === $visiteTechnique->user_id;
    }
}