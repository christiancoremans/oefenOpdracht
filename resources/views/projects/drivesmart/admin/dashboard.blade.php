{{-- resources/views/projects/drivesmart/admin/dashboard.blade.php --}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-5xl mx-auto px-4 py-8 space-y-8">

        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Admin Dashboard</h1>
            <a href="{{ route('drivesmart.admin.users.index') }}"
               class="bg-sky-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-sky-700 transition">
                👥 Manage Users
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-700 rounded-lg px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach([
                ['label' => 'Instructors', 'value' => $stats['instructors'], 'color' => 'sky'],
                ['label' => 'Students',    'value' => $stats['students'],    'color' => 'sky'],
                ['label' => 'Planned',     'value' => $stats['planned'],     'color' => 'amber'],
                ['label' => 'Completed',   'value' => $stats['completed'],   'color' => 'emerald'],
            ] as $card)
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow border border-gray-100 dark:border-zinc-700 p-4 text-center">
                <p class="text-3xl font-bold text-{{ $card['color'] }}-600 dark:text-{{ $card['color'] }}-400">
                    {{ $card['value'] }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wide">{{ $card['label'] }}</p>
            </div>
            @endforeach
        </div>

        {{-- Upcoming Lessons --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow border border-gray-100 dark:border-zinc-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-zinc-700">
                <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100">Upcoming Lessons</h2>
            </div>

            @if($upcomingLessons->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400 px-5 py-6 text-center">No upcoming planned lessons.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-zinc-700/50 text-left">
                            <tr>
                                <th class="px-5 py-3 font-medium text-gray-500 dark:text-gray-400">Date &amp; Time</th>
                                <th class="px-5 py-3 font-medium text-gray-500 dark:text-gray-400">Student</th>
                                <th class="px-5 py-3 font-medium text-gray-500 dark:text-gray-400">Instructor</th>
                                <th class="px-5 py-3 font-medium text-gray-500 dark:text-gray-400">Status</th>
                                <th class="px-5 py-3 font-medium text-gray-500 dark:text-gray-400">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-zinc-700">
                            @foreach($upcomingLessons as $lesson)
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700/30 transition">
                                <td class="px-5 py-3 whitespace-nowrap text-gray-700 dark:text-gray-300">
                                    {{ $lesson->scheduled_at->format('D d M Y, H:i') }}
                                </td>
                                <td class="px-5 py-3 text-gray-700 dark:text-gray-300">{{ $lesson->student->name ?? '—' }}</td>
                                <td class="px-5 py-3 text-gray-700 dark:text-gray-300">{{ $lesson->instructor->name ?? '—' }}</td>
                                <td class="px-5 py-3">
                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300">
                                        planned
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ $lesson->notes ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</x-layouts::project-shell>
