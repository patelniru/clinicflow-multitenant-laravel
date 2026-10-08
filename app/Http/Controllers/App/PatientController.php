<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Patient::class);
        $patients = Patient::query()->when($request->search, function ($q, $search) {
            $q->where(fn($q) => $q->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
        })->orderBy('last_name')->paginate(15)->withQueryString();
        return view('app.patients.index', compact('patients'));
    }
    public function create()
    {
        $this->authorize('create', Patient::class);
        return view('app.patients.create');
    }
    public function store(Request $request)
    {
        $this->authorize('create', Patient::class);
        $data = $this->validated($request);
        $data['company_id'] = auth()->user()->company_id;
        Patient::create($data);
        return redirect()->route('app.patients.index')->with('success', 'Patient created successfully.');
    }
    public function show(Patient $patient)
    {
        $this->authorize('view', $patient);
        return view('app.patients.show', compact('patient'));
    }
    public function edit(Patient $patient)
    {
        $this->authorize('update', $patient);
        return view('app.patients.edit', compact('patient'));
    }
    public function update(Request $request, Patient $patient)
    {
        $this->authorize('update', $patient);
        $patient->update($this->validated($request));
        return redirect()->route('app.patients.show', $patient)->with('success', 'Patient updated successfully.');
    }
    public function destroy(Patient $patient)
    {
        $this->authorize('delete', $patient);
        $patient->delete();
        return redirect()->route('app.patients.index')->with('success', 'Patient deleted successfully.');
    }
    private function validated(Request $request): array
    {
        return $request->validate(['first_name' => ['required', 'string', 'max:100'], 'last_name' => ['required', 'string', 'max:100'], 'email' => ['nullable', 'email', 'max:255'], 'phone' => ['nullable', 'string', 'max:30'], 'date_of_birth' => ['nullable', 'date', 'before:today'], 'status' => ['required', Rule::in(['active', 'inactive'])]]);
    }
}
