<?php

namespace App\Models\Concerns;

use App\Enums\UserRole;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasPermissions
{
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'user_permissions'
        )
            ->withPivot('company_id')
            ->withTimestamps();
    }

    public function hasPermission(string $permission): bool
    {
        $role = $this->role;

        if ($role instanceof UserRole) {
            $role = $role->value;
        }

        /*
         * Super Admin has all platform permissions.
         * Manager has all permissions only inside their own company.
         */
        if (in_array($role, ['super_admin', 'manager'], true)) {
            return true;
        }

        /*
         * Consultants need permissions assigned by their Manager.
         * Patients do not receive company module permissions.
         */
        return $this->permissions()
            ->where('slug', $permission)
            ->wherePivot('company_id', $this->company_id)
            ->exists();
    }

    public function syncPermissions(array $permissionIds): void
    {
        $permissions = [];

        foreach ($permissionIds as $permissionId) {
            $permissions[$permissionId] = [
                'company_id' => $this->company_id,
            ];
        }

        $this->permissions()->sync($permissions);
    }
}