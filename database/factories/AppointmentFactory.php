<?php
namespace Database\Factories;
use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Company;
use App\Models\ConsultantProfile;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
class AppointmentFactory extends Factory { protected $model=Appointment::class; public function definition(): array { return ['company_id'=>Company::factory(),'patient_id'=>Patient::factory(),'consultant_id'=>ConsultantProfile::factory(),'appointment_date'=>fake()->dateTimeBetween('+1 day','+1 month')->format('Y-m-d'),'start_time'=>'10:00','end_time'=>'10:30','status'=>AppointmentStatus::SCHEDULED,'notes'=>fake()->sentence(),'created_by'=>User::factory()]; } }
