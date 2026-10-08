<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\ConsultantProfile;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(PermissionSeeder::class);
        $north = Company::create(['name' => 'NorthStar Clinic', 'slug' => 'northstar-clinic', 'email' => 'info@northstar.test']);
        $maple = Company::create(['name' => 'Maple Health Centre', 'slug' => 'maple-health']);
        User::factory()->create(['name' => 'Platform Admin', 'email' => 'admin@clinicflow.test', 'password' => 'password', 'role' => UserRole::SUPER_ADMIN, 'company_id' => null]);
        User::factory()->create(['name' => 'NorthStar Manager', 'email' => 'manager@northstar.test', 'password' => 'password', 'role' => UserRole::MANAGER, 'company_id' => $north->id]);
        $consultant = User::factory()->create(['name' => 'Taylor Chen', 'email' => 'consultant@northstar.test', 'password' => 'password', 'role' => UserRole::CONSULTANT, 'company_id' => $north->id]);
        ConsultantProfile::create(['company_id' => $north->id, 'user_id' => $consultant->id, 'specialty' => 'General Consultation']);
        $patientUser = User::factory()->create(['name' => 'Alex Morgan', 'email' => 'patient@northstar.test', 'password' => 'password', 'role' => UserRole::PATIENT, 'company_id' => $north->id]);
        Patient::create(['company_id' => $north->id, 'user_id' => $patientUser->id, 'first_name' => 'Alex', 'last_name' => 'Morgan', 'email' => 'patient@northstar.test']);
        User::factory()->create(['name' => 'Maple Manager', 'email' => 'manager@maple.test', 'password' => 'password', 'role' => UserRole::MANAGER, 'company_id' => $maple->id]);
    }
}
