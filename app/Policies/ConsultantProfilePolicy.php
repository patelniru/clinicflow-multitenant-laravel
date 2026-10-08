<?php
namespace App\Policies;
use App\Enums\UserRole;
use App\Models\ConsultantProfile;
use App\Models\User;
class ConsultantProfilePolicy {
 public function viewAny(User $user): bool { return $user->hasAnyRole(UserRole::SUPER_ADMIN,UserRole::MANAGER,UserRole::CONSULTANT); }
 public function create(User $user): bool { return $user->hasAnyRole(UserRole::SUPER_ADMIN,UserRole::MANAGER); }
 public function update(User $user, ConsultantProfile $consultant): bool { return $user->isSuperAdmin() || ($user->role===UserRole::MANAGER && $user->company_id===$consultant->company_id); }
}
