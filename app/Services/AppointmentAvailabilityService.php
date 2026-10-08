<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;

class AppointmentAvailabilityService
{
    public function isAvailable(
        int $companyId,
        int $consultantId,
        string $date,
        string $start,
        string $end,
        ?int $ignoreAppointmentId = null
    ): bool {
        $hasConflict = Appointment::query()
            ->withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('consultant_id', $consultantId)
            ->whereDate('appointment_date', $date)
            ->whereNotIn('status', [
                AppointmentStatus::CANCELLED->value,
            ])
            ->when(
                $ignoreAppointmentId,
                fn ($query) => $query->where('id', '!=', $ignoreAppointmentId)
            )
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->exists();

        return ! $hasConflict;
    }
}