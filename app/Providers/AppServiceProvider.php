<?php
namespace App\Providers;
use App\Models\Company; use App\Models\User; use Illuminate\Support\Facades\Gate; use Illuminate\Support\ServiceProvider;
class AppServiceProvider extends ServiceProvider { public function register(): void {} public function boot(): void { Gate::define('platformView',fn(User $user)=>$user->isSuperAdmin()); } }
