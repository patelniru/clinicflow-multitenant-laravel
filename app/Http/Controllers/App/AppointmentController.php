<?php

namespace App\Http\Controllers\App;

use App\Actions\Appointments\CreateAppointmentAction;
use App\Contracts\AppointmentRepositoryInterface;
use App\Enums\AppointmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ConsultantProfile;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AppointmentController extends Controller
{
    public function __construct(private AppointmentRepositoryInterface $appointments) {}
    public function index(Request $request)
    {
        $this->authorize('viewAny', Appointment::class);
        $appointments = $this->appointments->paginate($request->only('status', 'date'));
        return view('app.appointments.index', compact('appointments'));
    }
    public function create()
    {
        $this->authorize('create', Appointment::class);
        $patients = Patient::query()->orderBy('last_name')->get();
        $consultants = ConsultantProfile::query()->with('user')->where('is_active', true)->orderBy('id')->get();
        return view('app.appointments.create', compact('patients', 'consultants'));
    }
    public function store(Request $request, CreateAppointmentAction $action)
    {
        $this->authorize('create', Appointment::class);
        $data = $this->validated($request);
        $data['company_id'] = $request->user()->company_id;
        $data['status'] = AppointmentStatus::SCHEDULED->value;
        $action->execute($data, $request->user()->id);
        return redirect()->route('app.appointments.index')->with('success', 'Appointment created successfully.');
    }
    public function show(Appointment $appointment)
    {
        $this->authorize('view', $appointment);
        return view('app.appointments.show', compact('appointment'));
    }
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $this->authorize('update', $appointment);
        $data = $request->validate(['status' => ['required', Rule::in(array_map(fn($case) => $case->value, AppointmentStatus::cases()))]]);
        $appointment->update(['status' => $data['status']]);
        return redirect()->route('app.appointments.show', $appointment)->with('success', 'Appointment status updated.');
    }
    private function validated(Request $request): array
    {
        return $request->validate(['patient_id' => ['required', 'integer', 'exists:patients,id'], 'consultant_id' => ['required', 'integer', 'exists:consultant_profiles,id'], 'appointment_date' => ['required', 'date', 'after_or_equal:today'], 'start_time' => ['required', 'date_format:H:i'], 'end_time' => ['required', 'date_format:H:i', 'after:start_time'], 'notes' => ['nullable', 'string', 'max:2000']]);
    }
}
