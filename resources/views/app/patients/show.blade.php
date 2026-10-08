<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">
                    {{ $patient->first_name }} {{ $patient->last_name }}
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Patient Details
                </p>
            </div>

            <div class="flex flex-wrap gap-2">

                @can('view', $patient)
                    <a
                        href="{{ route('app.patients.documents.index', $patient) }}"
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                    >
                        Documents
                    </a>
                @endcan

                @can('update', $patient)
                    <a
                        href="{{ route('app.patients.edit', $patient) }}"
                        class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800"
                    >
                        Edit Patient
                    </a>
                @endcan

            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded p-6 space-y-4">

                @if(session('success'))
                    <div class="p-4 bg-green-100 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="font-medium">
                        {{ $patient->email ?: 'Not provided' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Phone</p>
                    <p class="font-medium">
                        {{ $patient->phone ?: 'Not provided' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Date of Birth</p>
                    <p class="font-medium">
                        {{ $patient->date_of_birth?->format('Y-m-d') ?: 'Not provided' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <p class="font-medium">
                        {{ ucfirst($patient->status) }}
                    </p>
                </div>

                <div class="pt-4 border-t">
                    <a
                        href="{{ route('app.patients.index') }}"
                        class="text-indigo-600 hover:text-indigo-800"
                    >
                        ← Back to Patients
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>