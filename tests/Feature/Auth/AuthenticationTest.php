<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
    }

    public function test_manager_is_redirected_to_company_dashboard_after_login(): void
    {
        $manager = User::factory()->create([
            'role' => UserRole::MANAGER,
        ]);

        $response = $this->post('/login', [
            'email' => $manager->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();

        $response->assertRedirect(route('app.dashboard', absolute: false));
    }

    public function test_consultant_is_redirected_to_company_dashboard_after_login(): void
    {
        $consultant = User::factory()->create([
            'role' => UserRole::CONSULTANT,
        ]);

        $response = $this->post('/login', [
            'email' => $consultant->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();

        $response->assertRedirect(route('app.dashboard', absolute: false));
    }

    public function test_patient_is_redirected_to_patient_dashboard_after_login(): void
    {
        $patient = User::factory()->create([
            'role' => UserRole::PATIENT,
        ]);

        $response = $this->post('/login', [
            'email' => $patient->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();

        $response->assertRedirect(route('patient.dashboard', absolute: false));
    }

    public function test_super_admin_is_redirected_to_admin_dashboard_after_login(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::SUPER_ADMIN,
            'company_id' => null,
        ]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();

        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::PATIENT,
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();

        $response->assertRedirect('/');
    }
}