<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Appointment Details</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded p-6 space-y-3">
                <p><strong>Patient:</strong> {{ $appointment->patient->first_name }} {{ $appointment->patient->last_name
                    }}</p>
                <p><strong>Consultant:</strong> {{ $appointment->consultant->user->name }}</p>
                <p><strong>When:</strong> {{ $appointment->appointment_date->format('Y-m-d') }} {{
                    $appointment->start_time }}–{{ $appointment->end_time }}</p>
                <p><strong>Status:</strong> {{ $appointment->status->value }}</p>
                <p><strong>Notes:</strong> {{ $appointment->notes ?: 'None' }}</p>@can('update',$appointment)<form
                    method="POST" action="{{ route('app.appointments.status',$appointment) }}"
                    class="flex gap-2 items-center">@csrf @method('PATCH')<select name="status"
                        class="border rounded px-3 py-2">@foreach(\App\Enums\AppointmentStatus::cases() as $status)
                        <option value="{{ $status->value }}" @selected($appointment->status->value===$status->value)>{{
                            ucfirst(str_replace('_',' ',$status->value)) }}</option>@endforeach</select><button
                        class="px-4 py-2 bg-indigo-600 text-white rounded">Update Status</button></form>@endcan<a
                    href="{{ route('app.appointments.index') }}" class="inline-block text-indigo-600">Back to
                    appointments</a>
            </div>
        </div>
    </div>
</x-app-layout>