<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">
                    Consultant Permissions
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    {{ $consultant->user->name }}
                    ·
                    {{ $consultant->user->email }}
                </p>
            </div>

            <a
                href="{{ route('app.consultants.index') }}"
                class="text-indigo-600 hover:text-indigo-800"
            >
                ← Back to Consultants
            </a>
        </div>
    </x-slot>

    <div
        class="py-8"
        x-data="permissionManager()"
    >
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">

                <div class="mb-6 p-4 bg-indigo-50 border border-indigo-100 rounded-lg">
                    <p class="font-medium text-indigo-900">
                        Permission assignment
                    </p>

                    <p class="mt-1 text-sm text-indigo-800">
                        Select only the permissions required by this consultant.
                        Managers retain full access within their own company.
                    </p>
                </div>

                <form
                    method="POST"
                    action="{{ route('app.consultants.permissions.update', $consultant) }}"
                >
                    @csrf
                    @method('PUT')

                    <!-- Global Permission Controls -->
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between pb-4 border-b">

                        <p class="text-sm text-gray-600">
                            <span
                                class="font-semibold text-indigo-600"
                                x-text="selectedCount"
                            ></span>
                            permissions selected
                        </p>

                        <div class="flex flex-wrap gap-2">
                            <button
                                type="button"
                                @click="selectAll()"
                                class="px-3 py-2 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700"
                            >
                                Select All Modules
                            </button>

                            <button
                                type="button"
                                @click="clearAll()"
                                class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                            >
                                Clear All
                            </button>

                            <button
                                type="button"
                                @click="expandAll()"
                                class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                            >
                                Expand All
                            </button>

                            <button
                                type="button"
                                @click="collapseAll()"
                                class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200"
                            >
                                Collapse All
                            </button>
                        </div>
                    </div>

                    <!-- Accordion Modules -->
                    <div class="mt-6 space-y-4">
                        @foreach($permissions as $module => $items)

                            <section
                                class="border rounded-lg overflow-hidden"
                                x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }"
                                data-permission-module="{{ $module }}"
                            >
                                <!-- Accordion Header -->
                                <div class="bg-gray-50 px-5 py-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                                    <button
                                        type="button"
                                        @click="open = !open"
                                        class="flex items-center gap-3 text-left"
                                    >
                                        <svg
                                            class="h-5 w-5 text-indigo-600 transition-transform duration-200"
                                            :class="{ 'rotate-90': open }"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 5l7 7-7 7"
                                            />
                                        </svg>

                                        <div>
                                            <h3 class="font-semibold text-gray-900 capitalize">
                                                {{ str_replace('_', ' ', $module) }} Module
                                            </h3>

                                            <p class="text-sm text-gray-500">
                                                <span x-text="moduleSelectedCount('{{ $module }}')"></span>
                                                of {{ $items->count() }} selected
                                            </p>
                                        </div>
                                    </button>

                                    <div class="flex gap-2">
                                        <button
                                            type="button"
                                            @click.stop="selectModule('{{ $module }}')"
                                            class="px-3 py-1.5 text-sm bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200"
                                        >
                                            Select All
                                        </button>

                                        <button
                                            type="button"
                                            @click.stop="clearModule('{{ $module }}')"
                                            class="px-3 py-1.5 text-sm bg-gray-200 text-gray-700 rounded hover:bg-gray-300"
                                        >
                                            Clear
                                        </button>
                                    </div>
                                </div>

                                <!-- Accordion Content -->
                                <div
                                    x-show="open"
                                    x-collapse
                                    class="p-5 grid grid-cols-1 md:grid-cols-2 gap-3"
                                >
                                    @foreach($items as $permission)
                                        <label
                                            class="flex items-start gap-3 p-3 border rounded-md cursor-pointer hover:bg-indigo-50"
                                        >
                                            <input
                                                type="checkbox"
                                                name="permissions[]"
                                                value="{{ $permission->id }}"
                                                data-module="{{ $module }}"
                                                x-model="selected"
                                                class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            >

                                            <span>
                                                <span class="block font-medium text-gray-800">
                                                    {{ $permission->name }}
                                                </span>

                                                <span class="block mt-1 text-xs text-gray-500">
                                                    {{ $permission->slug }}
                                                </span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </section>

                        @endforeach
                    </div>

                    <!-- Save -->
                    <div class="mt-8 flex items-center justify-end gap-3">
                        <a
                            href="{{ route('app.consultants.index') }}"
                            class="px-4 py-2 text-gray-700 rounded hover:bg-gray-100"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="px-5 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                        >
                            Save Permissions
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function permissionManager() {
            return {
                selected: @json(array_map('strval', $selected)),

                get selectedCount() {
                    return this.selected.length;
                },

                allCheckboxes() {
                    return Array.from(
                        document.querySelectorAll('input[name="permissions[]"]')
                    );
                },

                moduleIds(module) {
                    return this.allCheckboxes()
                        .filter((checkbox) => checkbox.dataset.module === module)
                        .map((checkbox) => String(checkbox.value));
                },

                moduleSelectedCount(module) {
                    const modulePermissionIds = this.moduleIds(module);

                    return this.selected.filter((id) =>
                        modulePermissionIds.includes(String(id))
                    ).length;
                },

                selectModule(module) {
                    this.selected = [
                        ...new Set([
                            ...this.selected,
                            ...this.moduleIds(module),
                        ]),
                    ];
                },

                clearModule(module) {
                    const modulePermissionIds = this.moduleIds(module);

                    this.selected = this.selected.filter(
                        (id) => !modulePermissionIds.includes(String(id))
                    );
                },

                selectAll() {
                    this.selected = this.allCheckboxes().map(
                        (checkbox) => String(checkbox.value)
                    );
                },

                clearAll() {
                    this.selected = [];
                },

                expandAll() {
                    document
                        .querySelectorAll('[data-permission-module]')
                        .forEach((moduleElement) => {
                            const alpineData = Alpine.$data(moduleElement);

                            if (alpineData) {
                                alpineData.open = true;
                            }
                        });
                },

                collapseAll() {
                    document
                        .querySelectorAll('[data-permission-module]')
                        .forEach((moduleElement) => {
                            const alpineData = Alpine.$data(moduleElement);

                            if (alpineData) {
                                alpineData.open = false;
                            }
                        });
                },
            }
        }
    </script>
</x-app-layout>