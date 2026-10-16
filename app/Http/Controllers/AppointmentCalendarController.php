<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Support\CalendarLink;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class AppointmentCalendarController extends Controller
{
    /**
     * Month-grid calendar of appointments.
     *
     * - Manager: all appointments in their company
     * - Consultant: only their own appointments
     * - Patient: only their own appointments
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $role = $user?->role?->value ?? 'patient';

        $current = $this->currentMonth($request->input('month'));

        // 6-week grid (42 days) starting on Sunday, so the month fits fully.
        $gridStart = $current->copy()->startOfWeek(0); // 0 = Sunday
        $gridEnd = $gridStart->copy()->addDays(41);

        $query = Appointment::query()
            ->with(['patient', 'consultant.user'])
            ->whereBetween('appointment_date', [$gridStart->toDateString(), $gridEnd->toDateString()])
            ->orderBy('appointment_date')
            ->orderBy('start_time');

        // Tenant isolation: a manager/consultant must belong to a company.
        // (Fail closed — never show another tenant's appointments.)
        if (in_array($role, ['manager', 'consultant'], true) && ! $user?->company_id) {
            abort(403, 'No company is associated with your account.');
        }

        if ($user?->company_id) {
            $query->where('company_id', $user->company_id);
        }

        // Role scoping.
        if ($role === 'consultant') {
            $query->where('consultant_id', $user?->consultantProfile?->id ?? 0);
        } elseif ($role === 'patient') {
            $query->where('patient_id', $this->patientIdFor($user) ?? 0);
        }

        $appointments = $query->get();

        $byDay = $appointments->groupBy(
            fn (Appointment $appointment) => $appointment->appointment_date instanceof Carbon
                ? $appointment->appointment_date->toDateString()
                : (string) $appointment->appointment_date
        );

        $days = [];
        for ($i = 0; $i < 42; $i++) {
            $day = $gridStart->copy()->addDays($i);
            $days[] = [
                'date' => $day,
                'appointments' => $byDay->get($day->toDateString(), new Collection()),
            ];
        }

        // Selected day (from ?day=Y-m-d), defaulting to today when visible.
        $selectedDay = null;
        foreach ($days as $day) {
            if ($day['date']->toDateString() === (string) $request->input('day')) {
                $selectedDay = $day;
                break;
            }
        }

        if ($selectedDay === null) {
            foreach ($days as $day) {
                if ($day['date']->isToday()) {
                    $selectedDay = $day;
                    break;
                }
            }
        }

        if ($selectedDay === null) {
            foreach ($days as $day) {
                if ($day['date']->isSameMonth($current) && $day['date']->day === 1) {
                    $selectedDay = $day;
                    break;
                }
            }
        }

        $selectedDay ??= $days[0];

        $view = $role === 'patient'
            ? 'patient.appointments.calendar'
            : 'appointments.calendar';

        return view($view, [
            'days' => $days,
            'today' => Carbon::now(CalendarLink::timezone())->toDateString(),
            'selected' => $selectedDay['date']->toDateString(),
            'selectedAppointments' => $selectedDay['appointments'],
            'current' => $current,
            'prevMonth' => $current->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $current->copy()->addMonth()->format('Y-m'),
            'statusColors' => [
                'scheduled' => 'blue',
                'confirmed' => 'indigo',
                'completed' => 'green',
                'cancelled' => 'red',
                'no_show' => 'amber',
                'no-show' => 'amber',
            ],
        ]);
    }

    /**
     * Download a single appointment as an .ics calendar file
     * (works with Google Calendar, Outlook, Apple Calendar).
     */
    public function ics(Request $request, Appointment $appointment)
    {
        $user = $request->user();
        $role = $user?->role?->value ?? 'patient';

        if (in_array($role, ['manager', 'consultant'], true) && ! $user?->company_id) {
            abort(403, 'No company is associated with your account.');
        }

        if ($user?->company_id && (int) $appointment->company_id !== (int) $user->company_id) {
            abort(403);
        }

        if ($role === 'consultant'
            && (int) $appointment->consultant_id !== (int) ($user?->consultantProfile?->id ?? 0)) {
            abort(403);
        }

        if ($role === 'patient'
            && (int) $appointment->patient_id !== (int) ($this->patientIdFor($user) ?? 0)) {
            abort(403);
        }

        $ics = CalendarLink::ics($appointment);

        if ($ics === null) {
            abort(422, 'This appointment has no valid start time, so it cannot be exported.');
        }

        $filename = 'appointment-' . $appointment->id . '.ics';

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function currentMonth(?string $month): Carbon
    {
        if ($month !== null && $month !== '') {
            try {
                // "!" resets day/time fields, so the month starts on the 1st at 00:00.
                return Carbon::createFromFormat('!Y-m', $month, CalendarLink::timezone());
            } catch (\Throwable $e) {
                // fall through to current month
            }
        }

        return Carbon::now(CalendarLink::timezone())->startOfMonth();
    }

    private function patientIdFor($user): ?int
    {
        try {
            if ($user?->patientProfile) {
                return (int) $user->patientProfile->id;
            }
        } catch (\Throwable $e) {
            // relation may not exist; fall back to lookup below
        }

        if ($user?->id) {
            return Patient::query()->where('user_id', $user->id)->value('id');
        }

        return null;
    }
}
