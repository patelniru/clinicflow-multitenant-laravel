<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Appointment</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded p-6">
                <p><strong>Consultant:</strong> {{ $appointment->consultant->user->name }}</p>
                <p><strong>Date:</strong> {{ $appointment->appointment_date->format('Y-m-d') }}</p>
                <p><strong>Time:</strong> {{ $appointment->start_time }}–{{ $appointment->end_time }}</p>
                <p><strong>Status:</strong> {{ $appointment->status->value }}</p>
            </div>
        </div>
    </div>
</x-app-layout>