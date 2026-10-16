<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Calendar links
    |--------------------------------------------------------------------------
    |
    | Settings used when generating "Add to Google Calendar" links and
    | .ics downloads for appointments.
    |
    | calendar_timezone: timezone the clinic's appointment times are stored
    | in (e.g. America/Vancouver). Google Calendar links and .ics files
    | use this so events land at the right time.
    |
    | calendar_include_patient_name: when true, patient names are included
    | in Google Calendar links and .ics files. DEFAULTS TO FALSE for privacy —
    | patient names are only sent to Google if you deliberately opt in.
    | Appointment notes are never exported either way.
    |
    | calendar_location: optional clinic address included in calendar
    | events. Leave empty to omit.
    |
    */

    'calendar_timezone' => env('CLINICFLOW_TIMEZONE', config('app.timezone', 'UTC')),

    'calendar_include_patient_name' => env('CLINICFLOW_CALENDAR_PATIENT_NAMES', false),

    'calendar_location' => env('CLINICFLOW_CALENDAR_LOCATION', ''),

];
