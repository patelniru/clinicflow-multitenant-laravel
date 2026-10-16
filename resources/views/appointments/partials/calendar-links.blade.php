@php
    /**
     * "Add to Google Calendar" + .ics download buttons for one appointment.
     *
     * Usage:
     *   @include('appointments.partials.calendar-links', ['appointment' => $appointment])
     *
     * Optional: pass 'icsRoute' to override the .ics route name.
     * Defaults: patient.appointments.ics on patient pages,
     *           app.appointments.ics (or appointments.ics) everywhere else.
     *
     * Both buttons are hidden automatically when the appointment has no
     * valid date/start/end times (nothing is invented).
     *
     * @var \App\Models\Appointment $appointment
     */
    $googleUrl = \App\Support\CalendarLink::google($appointment);
    $icsRouteName = $icsRoute
        ?? (request()->routeIs('patient.*')
            ? (Route::has('patient.appointments.ics') ? 'patient.appointments.ics' : null)
            : (Route::has('app.appointments.ics') ? 'app.appointments.ics'
                : (Route::has('appointments.ics') ? 'appointments.ics' : null)));
@endphp
@if ($googleUrl)
    <div class="flex flex-wrap items-center gap-2">
        <a
            href="{{ $googleUrl }}"
            target="_blank"
            rel="noopener"
            title="Open Google Calendar with this appointment pre-filled"
            class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
        >
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <path d="M16 2v4M8 2v4M3 10h18"/>
                <path d="M8 15h8M8 18h5" stroke-linecap="round"/>
            </svg>
            Add to Google Calendar
        </a>

        @if ($icsRouteName && Route::has($icsRouteName))
            <a
                href="{{ route($icsRouteName, $appointment) }}"
                title="Download .ics file (Google, Outlook, Apple Calendar)"
                class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
            >
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M12 3v12m0 0 4-4m-4 4-4-4" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" stroke-linecap="round"/>
                </svg>
                Download .ics
            </a>
        @endif
    </div>
@endif
