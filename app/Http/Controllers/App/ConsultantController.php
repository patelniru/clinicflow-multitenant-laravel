<?php

namespace App\Http\Controllers\App;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\ConsultantProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ConsultantController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', ConsultantProfile::class);
        $consultants = ConsultantProfile::query()->with('user')->orderBy('id', 'desc')->paginate(15);
        return view('app.consultants.index', compact('consultants'));
    }
    public function create()
    {
        $this->authorize('create', ConsultantProfile::class);
        return view('app.consultants.create');
    }
    public function store(Request $request)
    {
        $this->authorize('create', ConsultantProfile::class);
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')], 'password' => ['required', 'string', 'min:8', 'confirmed'], 'specialty' => ['nullable', 'string', 'max:150'], 'is_active' => ['nullable', 'boolean']]);
        $companyId = $request->user()->company_id;
        $user = User::create(['company_id' => $companyId, 'name' => $data['name'], 'email' => $data['email'], 'password' => Hash::make($data['password']), 'role' => UserRole::CONSULTANT, 'is_active' => true]);
        ConsultantProfile::create(['company_id' => $companyId, 'user_id' => $user->id, 'specialty' => $data['specialty'] ?? null, 'is_active' => $request->boolean('is_active', true)]);
        return redirect()->route('app.consultants.index')->with('success', 'Consultant created successfully.');
    }
    public function edit(ConsultantProfile $consultant)
    {
        $this->authorize('update', $consultant);
        return view('app.consultants.edit', compact('consultant'));
    }
    public function update(Request $request, ConsultantProfile $consultant)
    {
        $this->authorize('update', $consultant);
        $data = $request->validate(['name' => ['required', 'string', 'max:120'], 'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($consultant->user_id)], 'specialty' => ['nullable', 'string', 'max:150'], 'is_active' => ['nullable', 'boolean']]);
        $consultant->user->update(['name' => $data['name'], 'email' => $data['email']]);
        $consultant->update(['specialty' => $data['specialty'] ?? null, 'is_active' => $request->boolean('is_active')]);
        return redirect()->route('app.consultants.index')->with('success', 'Consultant updated successfully.');
    }
}
