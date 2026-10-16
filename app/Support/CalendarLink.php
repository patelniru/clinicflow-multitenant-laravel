<?php

namespace App\Support;

use App\Models\Appointment;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

/**
 * Generates "Add to Google Calendar" links and .ics files
 * for ClinicFlow appointments.
 *
 * Privacy: by default, patient names and appointment notes are NOT
 * included in links sent to Google. Enable patient names explicitly
 * with CLINICFLOW_CALENDAR_PATIENT_NAMES=true if your clinic wants
 * them in the event title. Appointment notes are never exported.
 */
class CalendarLink
{
    /**
     * Timezone used when building calendar links.
     * Defaults to the app timezone; override with CLINICFLOW_TIMEZONE in .env.
     */
    public static function timezone(): string
    {
        return (string) config('clinicflow.calendar_timezone', config('app.timezone', 'UTC'));
    }

    /**
     * One-click "Add to Google Calendar" template URL.
     * Opens the Google Calendar event editor with the appointment
     * details pre-filled. Returns null when the appointment's times
     * are missing or invalid (nothing is invented).
     *
     * URL format follows the community-documented Google Calendar
     * template link: https://github.com/InteractionDesignFoundation/add-event-to-calendar-docs/blob/main/services/google.md
     */
    public static function google(Appointment $appointment): ?string
    {
        $range = self::range($appointment);

        if ($range === null) {
            return null;
        }

        [$start, $end] = $range;

        $params = [
            'action' => 'TEMPLATE',
            'text' => self::title($appointment),
            'dates' => $start->format('Ymd\THis') . '/' . $end->format('Ymd\THis'),
            'ctz' => self::timezone(),
            'details' => self::details($appointment),
        ];

        if ($location = self::location()) {
            $params['location'] = $location;
        }

        return 'https://calendar.google.com/calendar/render?' . http_build_query($params);
    }

    /**
     * Full .ics file content (RFC 5545) for a single appointment.
     * Times are converted to UTC so the event lands at the correct
     * instant in any calendar app (Google, Outlook, Apple Calendar).
     * Returns null when the appointment's times are missing or invalid.
     */
    public static function ics(Appointment $appointment): ?string
    {
        $range = self::range($appointment);

        if ($range === null) {
            return null;
        }

        [$start, $end] = $range;

        $host = parse_url((string) config('app.url', 'http://localhost'), PHP_URL_HOST) ?: 'localhost';

        $status = self::statusValue($appointment);
        $eventStatus = $status === 'cancelled' ? 'CANCELLED' : 'CONFIRMED';

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//ClinicFlow//Appointments//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:clinicflow-appointment-' . $appointment->id . '@' . $host,
            'DTSTAMP:' . Carbon::now('UTC')->format('Ymd\THis\Z'),
            'DTSTART:' . $start->copy()->setTimezone('UTC')->format('Ymd\THis\Z'),
            'DTEND:' . $end->copy()->setTimezone('UTC')->format('Ymd\THis\Z'),
            'SUMMARY:' . self::escape(self::title($appointment)),
            'DESCRIPTION:' . self::escape(self::details($appointment)),
            'STATUS:' . $eventStatus,
        ];

        if ($location = self::location()) {
            $lines[] = 'LOCATION:' . self::escape($location);
        }

        $lines[] = 'END:VEVENT';
        $lines[] = 'END:VCALENDAR';

        // RFC 5545: fold lines longer than 74 octets (UTF-8 safe).
        $folded = [];
        foreach ($lines as $line) {
            $folded = array_merge($folded, self::fold($line));
        }

        return implode("\r\n", $folded) . "\r\n";
    }

    private static function title(Appointment $appointment): string
    {
        if (self::includePatientName() && ($name = self::patientName($appointment))) {
            return 'Clinic appointment: ' . $name;
        }

        return 'Clinic appointment';
    }

    /**
     * Event details sent to external calendars.
     * Appointment notes are intentionally never included.
     */
    private static function details(Appointment $appointment): string
    {
        $lines = [];

        if ($name = self::consultantName($appointment)) {
            $lines[] = 'Consultant: ' . $name;
        }

        if (self::includePatientName() && ($name = self::patientName($appointment))) {
            $lines[] = 'Patient: ' . $name;
        }

        if ($status = self::statusValue($appointment)) {
            $lines[] = 'Status: ' . ucwords(str_replace('_', ' ', $status));
        }

        $lines[] = 'Booked via ClinicFlow.';

        return implode("\n", $lines);
    }

    private static function patientName(Appointment $appointment): ?string
    {
        $patient = $appointment->patient;

        if ($patient === null) {
            return null;
        }

        $name = trim(($patient->first_name ?? '') . ' ' . ($patient->last_name ?? ''));

        return $name !== '' ? $name : null;
    }

    private static function consultantName(Appointment $appointment): ?string
    {
        $name = $appointment->consultant?->user?->name;

        return is_string($name) && $name !== '' ? $name : null;
    }

    private static function includePatientName(): bool
    {
        return (bool) config('clinicflow.calendar_include_patient_name', false);
    }

    private static function location(): ?string
    {
        $location = trim((string) config('clinicflow.calendar_location', ''));

        return $location !== '' ? $location : null;
    }

    private static function statusValue(Appointment $appointment): string
    {
        $status = $appointment->status;

        if ($status instanceof \BackedEnum) {
            return (string) $status->value;
        }

        return (string) $status;
    }

    /**
     * Start/end datetimes for the appointment, built from
     * appointment_date + start_time / end_time in the clinic timezone.
     *
     * Returns null unless the appointment has a valid date, a valid
     * start time, and a valid end time that is after the start —
     * no times are invented or defaulted.
     *
     * @return array{0: Carbon, 1: Carbon}|null
     */
    private static function range(Appointment $appointment): ?array
    {
        $dateValue = $appointment->appointment_date;

        try {
            $date = $dateValue instanceof CarbonInterface
                ? Carbon::parse($dateValue->toDateString(), self::timezone())
                : Carbon::parse((string) $dateValue, self::timezone());
        } catch (\Throwable $e) {
            return null;
        }

        $start = self::time($date, (string) ($appointment->start_time ?? ''));
        $end = self::time($date, (string) ($appointment->end_time ?? ''));

        if ($start === null || $end === null) {
            return null;
        }

        if ($end->getTimestamp() <= $start->getTimestamp()) {
            return null;
        }

        return [$start, $end];
    }

    /**
     * Parse an "HH:MM" or "HH:MM:SS" time string. Full match only —
     * values like "10:30garbage" are rejected.
     */
    private static function time(Carbon $date, string $value): ?Carbon
    {
        $value = trim($value);

        if (! preg_match('/^(\d{1,2}):(\d{2})(?::\d{2})?$/', $value, $m)) {
            return null;
        }

        $hours = (int) $m[1];
        $minutes = (int) $m[2];

        if ($hours > 23 || $minutes > 59) {
            return null;
        }

        return $date->copy()->setTime($hours, $minutes);
    }

    /**
     * Escape text for use in an .ics property value (RFC 5545).
     */
    private static function escape(string $value): string
    {
        return str_replace(
            ['\\', ';', ',', "\r\n", "\n"],
            ['\\\\', '\\;', '\\,', '\\n', '\\n'],
            $value
        );
    }

    /**
     * Fold an .ics content line at 74 octets without splitting
     * multibyte (UTF-8) characters. Continuation lines start with
     * a single space per RFC 5545.
     *
     * @return list<string>
     */
    private static function fold(string $line): array
    {
        if (strlen($line) <= 74) {
            return [$line];
        }

        $lines = [];

        while (strlen($line) > 74) {
            $cut = 74;

            // Back up until position $cut is a UTF-8 character start
            // (or plain ASCII), so we never split a multibyte char
            // (2-, 3- or 4-byte) in half.
            while ($cut > 1 && (ord($line[$cut]) & 0xC0) === 0x80) {
                $cut--;
            }

            $lines[] = substr($line, 0, $cut);
            $line = ' ' . substr($line, $cut);
        }

        $lines[] = $line;

        return $lines;
    }
}
