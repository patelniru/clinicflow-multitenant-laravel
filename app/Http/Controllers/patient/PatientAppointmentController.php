<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class PatientAppointmentController extends Controller
{
    public function index(Request $request)
    {
        $patient = $request->user()->patientProfile;
        abort_unless($patient, 403);
        $appointments = Appointment::query()->where('patient_id', $patient->id)->with('consultant.user')->orderByDesc('appointment_date')->paginate(15);
        return view('patient.appointments.index', compact('appointments'));
    }
    public function show(Request $request, Appointment $appointment)
    {
        $this->authorize('view', $appointment);
        return view('patient.appointments.show', compact('appointment'));
    }
}
