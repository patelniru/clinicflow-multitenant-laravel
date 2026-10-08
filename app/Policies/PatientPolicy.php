<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(
            UserRole::SUPER_ADMIN,
            UserRole::MANAGER,
            UserRole::CONSULTANT
        );
    }

    public function view(User $user, Patient $patient): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->role === UserRole::MANAGER) {
            return $user->company_id === $patient->company_id;
        }

        if ($user->role === UserRole::CONSULTANT) {
            return $user->company_id === $patient->company_id
                && $patient->appointments()
                    ->where('consultant_id', $user->consultantProfile?->id)
                    ->exists();
        }

        if ($user->role === UserRole::PATIENT) {
            return $patient->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(
            UserRole::SUPER_ADMIN,
            UserRole::MANAGER
        );
    }

    public function update(User $user, Patient $patient): bool
    {
        return $user->isSuperAdmin()
            || (
                $user->role === UserRole::MANAGER
                && $user->company_id === $patient->company_id
            );
    }

    public function delete(User $user, Patient $patient): bool
    {
        return $this->update($user, $patient);
    }
}