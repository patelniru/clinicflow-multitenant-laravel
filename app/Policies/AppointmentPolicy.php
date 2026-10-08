<?php
namespace App\Policies;
use App\Enums\UserRole;
use App\Models\Appointment;
use App\Models\User;
class AppointmentPolicy {
 public function viewAny(User $user): bool { return $user->hasAnyRole(UserRole::SUPER_ADMIN,UserRole::MANAGER,UserRole::CONSULTANT); }
 public function view(User $user, Appointment $appointment): bool { if($user->isSuperAdmin()) return true; if($user->role===UserRole::MANAGER) return $user->company_id===$appointment->company_id; if($user->role===UserRole::CONSULTANT) return $user->consultantProfile?->id===$appointment->consultant_id; return $user->role===UserRole::PATIENT && $user->patientProfile?->id===$appointment->patient_id; }
 public function create(User $user): bool { return $user->hasAnyRole(UserRole::SUPER_ADMIN,UserRole::MANAGER); }
 public function update(User $user, Appointment $appointment): bool { return $user->isSuperAdmin() || ($user->role===UserRole::MANAGER && $user->company_id===$appointment->company_id) || ($user->role===UserRole::CONSULTANT && $user->consultantProfile?->id===$appointment->consultant_id); }
}
