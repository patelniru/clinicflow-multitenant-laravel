<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">My Documents</h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="text-left px-4 py-3">File</th>
                            <th class="text-left px-4 py-3">Date</th>
                            <th class="text-left px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>@forelse($documents as $document)<tr class="border-t">
                            <td class="px-4 py-3">{{ $document->original_name }}</td>
                            <td class="px-4 py-3">{{ $document->created_at }}</td>
                            <td class="px-4 py-3"><a class="text-indigo-600"
                                    href="{{ route('patient.documents.download',$document) }}">Download</a></td>
                        </tr>@empty<tr>
                            <td colspan="3" class="px-4 py-6 text-center">No documents available.</td>
                        </tr>@endforelse</tbody>
                </table>
            </div>
            <div class="mt-4">{{ $documents->links() }}</div>
        </div>
    </div>
</x-app-layout>