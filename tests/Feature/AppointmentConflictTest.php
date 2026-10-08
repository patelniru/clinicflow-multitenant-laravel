<?php

namespace Tests\Feature;

use App\Actions\Appointments\CreateAppointmentAction;
use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Company;
use App\Models\ConsultantProfile;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AppointmentConflictTest extends TestCase
{
    use RefreshDatabase;

    public function test_overlapping_appointment_is_rejected(): void
    {
        $company = Company::factory()->create();

        $consultantUser = User::factory()->create([
            'company_id' => $company->id,
        ]);

        $consultant = ConsultantProfile::factory()->create([
            'company_id' => $company->id,
            'user_id' => $consultantUser->id,
        ]);

        $patient = Patient::factory()->create([
            'company_id' => $company->id,
        ]);

        Appointment::factory()->create([
            'company_id' => $company->id,
            'consultant_id' => $consultant->id,
            'patient_id' => $patient->id,
            'appointment_date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '10:30',
            'status' => AppointmentStatus::SCHEDULED,
            'created_by' => $consultantUser->id,
        ]);

        $this->expectException(ValidationException::class);

        app(CreateAppointmentAction::class)->execute(
            [
                'company_id' => $company->id,
                'consultant_id' => $consultant->id,
                'patient_id' => $patient->id,
                'appointment_date' => now()->addDay()->toDateString(),
                'start_time' => '10:15',
                'end_time' => '10:45',
                'status' => AppointmentStatus::SCHEDULED->value,
                'notes' => 'Fictional test appointment.',
            ],
            $consultantUser->id
        );
    }
}