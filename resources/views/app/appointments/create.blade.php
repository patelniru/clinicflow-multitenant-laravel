<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Create Appointment</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded p-6">
                <form method="POST" action="{{ route('app.appointments.store') }}">@csrf <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="block mb-1">Patient</label><select name="patient_id"
                                class="w-full border rounded px-3 py-2" required>@foreach($patients as $patient)<option
                                    value="{{ $patient->id }}">{{ $patient->first_name }} {{ $patient->last_name }}
                                </option>@endforeach</select></div>
                        <div><label class="block mb-1">Consultant</label><select name="consultant_id"
                                class="w-full border rounded px-3 py-2" required>@foreach($consultants as $consultant)
                                <option value="{{ $consultant->id }}">{{ $consultant->user->name }}{{
                                    $consultant->specialty ? ' — '.$consultant->specialty : '' }}</option>
                                @endforeach</select></div>
                        <div><label class="block mb-1">Date</label><input type="date" name="appointment_date"
                                min="{{ now()->format('Y-m-d') }}" class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div><label class="block mb-1">Start Time</label><input type="time" name="start_time"
                                class="w-full border rounded px-3 py-2" required></div>
                        <div><label class="block mb-1">End Time</label><input type="time" name="end_time"
                                class="w-full border rounded px-3 py-2" required></div>
                        <div class="md:col-span-2"><label class="block mb-1">Notes</label><textarea name="notes"
                                class="w-full border rounded px-3 py-2" rows="4"></textarea></div>
                    </div>@if($errors->any())<div class="mt-4 text-red-600">@foreach($errors->all() as $error)<p>{{
                            $error }}</p>@endforeach</div>@endif<button
                        class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded">Create Appointment</button></form>
            </div>
        </div>
    </div>
</x-app-layout>