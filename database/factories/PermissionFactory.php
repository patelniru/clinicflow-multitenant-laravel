<?php
namespace Database\Factories;use App\Models\Permission;use Illuminate\Database\Eloquent\Factories\Factory;
class PermissionFactory extends Factory {protected $model=Permission::class;public function definition():array{$module=fake()->unique()->randomElement(['patients','appointments','documents','reports']);$action=fake()->randomElement(['view','create','update']);return ['name'=>ucfirst($action).' '.ucfirst($module),'slug'=>$module.'.'.$action,'module'=>$module,'description'=>fake()->sentence()];}}
