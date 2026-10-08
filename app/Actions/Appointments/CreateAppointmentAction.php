<?php

namespace App\Actions\Appointments;

use App\Contracts\AppointmentRepositoryInterface;
use App\Models\Appointment;
use App\Services\AppointmentAvailabilityService;
use Illuminate\Validation\ValidationException;

class CreateAppointmentAction
{
    public function __construct(
        private AppointmentRepositoryInterface $appointments,
        private AppointmentAvailabilityService $availability
    ) {
    }

    public function execute(array $data, int $actorId): Appointment
    {
        $isAvailable = $this->availability->isAvailable(
            companyId: $data['company_id'],
            consultantId: $data['consultant_id'],
            date: $data['appointment_date'],
            start: $data['start_time'],
            end: $data['end_time']
        );

        if (! $isAvailable) {
            throw ValidationException::withMessages([
                'start_time' => [
                    'The consultant already has an appointment during this time.',
                ],
            ]);
        }

        $data['created_by'] = $actorId;

        return $this->appointments->create($data);
    }
}