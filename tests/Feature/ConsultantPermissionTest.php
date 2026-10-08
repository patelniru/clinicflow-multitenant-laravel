<?php
namespace Tests\Feature;
use App\Enums\UserRole;use App\Models\Company;use App\Models\Permission;use App\Models\User;use Illuminate\Foundation\Testing\RefreshDatabase;use Tests\TestCase;
class ConsultantPermissionTest extends TestCase {use RefreshDatabase;public function test_consultant_requires_assigned_permission():void{$company=Company::factory()->create();$consultant=User::factory()->create(['company_id'=>$company->id,'role'=>UserRole::CONSULTANT]);$permission=Permission::factory()->create(['slug'=>'appointments.create']);$this->assertFalse($consultant->hasPermission('appointments.create'));$consultant->permissions()->attach($permission->id,['company_id'=>$company->id]);$this->assertTrue($consultant->hasPermission('appointments.create'));}}
