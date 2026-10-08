<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Company Dashboard</h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold">Welcome, {{ auth()->user()->name }}</h3>
                <p class="mt-2 text-gray-600">Company: {{ auth()->user()->company?->name }}</p>
                <div class="flex gap-3 flex-wrap mt-4"><a href="{{ route('app.patients.index') }}"
                        class="px-4 py-2 bg-indigo-600 text-white rounded">Patients</a><a
                        href="{{ route('app.consultants.index') }}"
                        class="px-4 py-2 bg-indigo-600 text-white rounded">Consultants</a><a
                        href="{{ route('app.appointments.index') }}"
                        class="px-4 py-2 bg-indigo-600 text-white rounded">Appointments</a></div>
            </div>
        </div>
    </div>
</x-app-layout>