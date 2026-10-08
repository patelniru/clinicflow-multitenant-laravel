<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Patient Portal</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">Welcome, {{ auth()->user()->name }}.<br /><a
                    href="{{ route('patient.appointments.index') }}"
                    class="inline-block mt-4 px-4 py-2 bg-indigo-600 text-white rounded">My Appointments</a></div>
        </div>
    </div>
</x-app-layout>