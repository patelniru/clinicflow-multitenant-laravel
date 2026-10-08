<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">My Appointments</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="text-left px-4 py-3">Date</th>
                            <th class="text-left px-4 py-3">Consultant</th>
                            <th class="text-left px-4 py-3">Time</th>
                            <th class="text-left px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>@forelse($appointments as $appointment)<tr class="border-t">
                            <td class="px-4 py-3">{{ $appointment->appointment_date->format('Y-m-d') }}</td>
                            <td class="px-4 py-3">{{ $appointment->consultant->user->name }}</td>
                            <td class="px-4 py-3">{{ $appointment->start_time }}–{{ $appointment->end_time }}</td>
                            <td class="px-4 py-3">{{ $appointment->status->value }}</td>
                        </tr>@empty<tr>
                            <td colspan="4" class="px-4 py-6 text-center">No appointments found.</td>
                        </tr>@endforelse</tbody>
                </table>
            </div>
            <div class="mt-4">{{ $appointments->links() }}</div>
        </div>
    </div>
</x-app-layout>