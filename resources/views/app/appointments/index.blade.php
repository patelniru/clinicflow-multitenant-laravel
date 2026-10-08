<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800">Appointments</h2>@if(auth()->user()->role->value ===
            'manager')<a href="{{ route('app.appointments.create') }}"
                class="px-4 py-2 bg-indigo-600 text-white rounded">Create Appointment</a>@endif
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">@if(session('success'))<div
                class="mb-4 p-4 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>@endif<div
                class="bg-white shadow rounded overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="text-left px-4 py-3">Date</th>
                            <th class="text-left px-4 py-3">Patient</th>
                            <th class="text-left px-4 py-3">Consultant</th>
                            <th class="text-left px-4 py-3">Time</th>
                            <th class="text-left px-4 py-3">Status</th>
                            <th class="text-left px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>@forelse($appointments as $appointment)<tr class="border-t">
                            <td class="px-4 py-3">{{ $appointment->appointment_date->format('Y-m-d') }}</td>
                            <td class="px-4 py-3">{{ $appointment->patient->first_name }} {{
                                $appointment->patient->last_name }}</td>
                            <td class="px-4 py-3">{{ $appointment->consultant->user->name }}</td>
                            <td class="px-4 py-3">{{ $appointment->start_time }}–{{ $appointment->end_time }}</td>
                            <td class="px-4 py-3">{{ $appointment->status->value }}</td>
                            <td class="px-4 py-3"><a href="{{ route('app.appointments.show',$appointment) }}"
                                    class="text-indigo-600">View</a></td>
                        </tr>@empty<tr>
                            <td colspan="6" class="px-4 py-6 text-center">No appointments found.</td>
                        </tr>@endforelse</tbody>
                </table>
            </div>
            <div class="mt-4">{{ $appointments->links() }}</div>
        </div>
    </div>
</x-app-layout>