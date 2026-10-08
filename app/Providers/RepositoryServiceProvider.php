<?php
namespace App\Providers;
use App\Contracts\AppointmentRepositoryInterface;
use App\Repositories\EloquentAppointmentRepository;
use Illuminate\Support\ServiceProvider;
class RepositoryServiceProvider extends ServiceProvider { public function register(): void { $this->app->bind(AppointmentRepositoryInterface::class,EloquentAppointmentRepository::class); } }
