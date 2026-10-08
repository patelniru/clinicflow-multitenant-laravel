<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_cannot_view_another_company_patient(): void
    {
        $companyOne = Company::factory()->create();

        $companyTwo = Company::factory()->create();

        $manager = User::factory()->create([
            'company_id' => $companyOne->id,
            'role' => UserRole::MANAGER,
        ]);

        $patient = Patient::factory()->create([
            'company_id' => $companyTwo->id,
        ]);

        $this->assertFalse(
            $manager->can('view', $patient)
        );
    }

    public function test_manager_can_view_patient_in_own_company(): void
    {
        $company = Company::factory()->create();

        $manager = User::factory()->create([
            'company_id' => $company->id,
            'role' => UserRole::MANAGER,
        ]);

        $patient = Patient::factory()->create([
            'company_id' => $company->id,
        ]);

        $this->assertTrue(
            $manager->can('view', $patient)
        );
    }
}