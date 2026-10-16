<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Appointment Calendar</h2>
    </x-slot>

    @php
        $today = $today ?? now()->toDateString();
        $calendarRoute = request()->route()->getName();
        $badgeColors = [
            'scheduled' => 'bg-blue-100 text-blue-800',
            'confirmed' => 'bg-indigo-100 text-indigo-800',
            'checked_in' => 'bg-purple-100 text-purple-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'no_show' => 'bg-amber-100 text-amber-800',
        ];
        $dotColors = [
            'scheduled' => 'bg-blue-500',
            'confirmed' => 'bg-indigo-500',
            'checked_in' => 'bg-purple-500',
            'completed' => 'bg-green-500',
            'cancelled' => 'bg-red-500',
            'no_show' => 'bg-amber-500',
        ];
        $selectedDate = $selected ?? $today;
    @endphp
    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- Page header --}}
            <div class="sm:flex sm:items-center sm:justify-between">
                <p class="mt-1 text-sm text-gray-500">Click a day to see your appointments.</p>
                @if (Route::has('patient.appointments.index'))
                    <div class="mt-4 sm:mt-0">
                        <a href="{{ route('patient.appointments.index') }}"
                           class="rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                            List view
                        </a>
                    </div>
                @endif
            </div>

            {{-- Month navigation --}}
            <div class="mt-6 flex items-center justify-between rounded-lg border border-gray-200 bg-white px-4 py-3 shadow-sm">
                <a href="{{ route($calendarRoute, ['month' => $prevMonth]) }}"
                   class="inline-flex items-center gap-1 rounded-md border border-gray-300 bg-white px-2.5 py-1.5 text-sm text-gray-600 hover:bg-gray-50"
                   aria-label="Previous month">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M15 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="hidden sm:inline">{{ \Carbon\Carbon::parse($prevMonth . '-01')->format('M Y') }}</span>
                </a>

                <div class="flex items-center gap-3">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $current->format('F Y') }}</h2>
                    <a href="{{ route($calendarRoute) }}"
                       class="rounded-md border border-gray-300 bg-white px-2.5 py-1 text-xs font-medium text-gray-600 hover:bg-gray-50">
                        Today
                    </a>
                </div>

                <a href="{{ route($calendarRoute, ['month' => $nextMonth]) }}"
                   class="inline-flex items-center gap-1 rounded-md border border-gray-300 bg-white px-2.5 py-1.5 text-sm text-gray-600 hover:bg-gray-50"
                   aria-label="Next month">
                    <span class="hidden sm:inline">{{ \Carbon\Carbon::parse($nextMonth . '-01')->format('M Y') }}</span>
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M9 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>

            {{-- Status legend --}}
            <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500">
                @foreach ($dotColors as $legendStatus => $legendColor)
                    <span class="inline-flex items-center gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-full {{ $legendColor }}"></span>
                        {{ ucfirst(str_replace('_', ' ', $legendStatus)) }}
                    </span>
                @endforeach
            </div>

            {{-- Month grid --}}
            <div class="mt-4 overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="grid grid-cols-7 border-b border-gray-200 bg-gray-50">
                    @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $weekday)
                        <div class="px-2 py-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $weekday }}</div>
                    @endforeach
                </div>
                <div class="grid grid-cols-7">
                    @foreach ($days as $day)
                        @php
                            $dateString = $day['date']->toDateString();
                            $isCurrentMonth = $day['date']->isSameMonth($current);
                            $isToday = $dateString === $today;
                            $isSelected = $dateString === $selectedDate;
                            $daysAppointments = $day['appointments'];
                        @endphp
                        <a href="{{ route($calendarRoute, ['month' => $day['date']->format('Y-m'), 'day' => $dateString]) }}"
                           class="min-h-[96px] border-b border-r border-gray-100 p-1.5 text-left align-top transition hover:bg-blue-50 {{ $isCurrentMonth ? 'bg-white' : 'bg-gray-50/60' }} {{ $isSelected ? 'bg-blue-50 ring-2 ring-inset ring-blue-500' : '' }}">
                            <div class="flex items-center justify-between">
                                <span class="inline-flex h-6 w-6 items-center justify-center rounded-full text-xs font-semibold {{ $isToday ? 'bg-blue-600 text-white' : ($isCurrentMonth ? 'text-gray-700' : 'text-gray-400') }}">
                                    {{ $day['date']->format('j') }}
                                </span>
                                @if ($daysAppointments->isNotEmpty())
                                    <span class="rounded-full bg-gray-100 px-1.5 py-0.5 text-[10px] font-medium text-gray-600">
                                        {{ $daysAppointments->count() }}
                                    </span>
                                @endif
                            </div>
                            <div class="mt-1 space-y-1">
                                @foreach ($daysAppointments->take(3) as $appointment)
                                    @php
                                        $status = $appointment->status instanceof \BackedEnum ? $appointment->status->value : (string) $appointment->status;
                                        $dotColor = $dotColors[$status] ?? 'bg-gray-400';
                                        $consultantName = $appointment->consultant?->user?->name;
                                    @endphp
                                    <div class="flex items-center gap-1.5 overflow-hidden">
                                        <span class="h-1.5 w-1.5 shrink-0 rounded-full {{ $dotColor }}"></span>
                                        <span class="truncate text-[11px] leading-4 {{ $status === 'cancelled' ? 'text-gray-400 line-through' : 'text-gray-700' }}">
                                            {{ \Illuminate\Support\Str::substr((string) $appointment->start_time, 0, 5) }}
                                            {{ $consultantName ?? 'Appointment' }}
                                        </span>
                                    </div>
                                @endforeach
                                @if ($daysAppointments->count() > 3)
                                    <div class="text-[11px] font-medium text-blue-600">+{{ $daysAppointments->count() - 3 }} more</div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Selected day panel --}}
            <div class="mt-6 rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-200 px-4 py-3">
                    <h3 class="text-sm font-semibold text-gray-900">
                        {{ \Carbon\Carbon::parse($selectedDate)->isoFormat('dddd, MMMM D, YYYY') }}
                    </h3>
                    <span class="text-xs text-gray-500">{{ $selectedAppointments->count() }} appointment{{ $selectedAppointments->count() === 1 ? '' : 's' }}</span>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($selectedAppointments as $appointment)
                        @php
                            $status = $appointment->status instanceof \BackedEnum ? $appointment->status->value : (string) $appointment->status;
                            $badgeColor = $badgeColors[$status] ?? 'bg-gray-100 text-gray-800';
                            $startTime = \Illuminate\Support\Str::substr((string) $appointment->start_time, 0, 5);
                            $endTime = \Illuminate\Support\Str::substr((string) ($appointment->end_time ?? ''), 0, 5);
                            $consultantName = $appointment->consultant?->user?->name;
                        @endphp
                        <div class="flex flex-col gap-3 px-4 py-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ $startTime }}{{ $endTime && $endTime !== $startTime ? ' – ' . $endTime : '' }}
                                    </span>
                                    <span class="rounded-full px-2 py-0.5 text-xs font-medium capitalize {{ $badgeColor }}">
                                        {{ str_replace('_', ' ', $status) }}
                                    </span>
                                </div>
                                @if ($consultantName)
                                    <div class="mt-1 text-sm text-gray-700">Consultant: {{ $consultantName }}</div>
                                @endif
                            </div>
                            <div class="shrink-0">
                                @include('appointments.partials.calendar-links', ['appointment' => $appointment])
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-6 text-sm text-gray-500">No appointments on this day.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
