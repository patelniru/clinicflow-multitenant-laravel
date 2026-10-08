<?php
namespace Tests\Feature;
use App\Enums\UserRole;use App\Models\Company;use App\Models\User;use Illuminate\Foundation\Testing\RefreshDatabase;use Tests\TestCase;
class SuperAdminPermissionTest extends TestCase {use RefreshDatabase;public function test_super_admin_has_any_permission():void{$admin=User::factory()->create(['role'=>UserRole::SUPER_ADMIN,'company_id'=>null]);$this->assertTrue($admin->hasPermission('appointments.create'));}}
