<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Add Consultant</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded p-6">
                <form method="POST" action="{{ route('app.consultants.store') }}">@csrf
                    @include('app.consultants.form')<button
                        class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded">Create Consultant</button></form>
            </div>
        </div>
    </div>
</x-app-layout>