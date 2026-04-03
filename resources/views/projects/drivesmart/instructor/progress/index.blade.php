{{-- resources/views/projects/drivesmart/instructor/progress/index.blade.php --}}
{{--
EXAM STUDY NOTE — Progress Reports list (instructor view)
==========================================================
$reports → reports written by this instructor (or all reports for admin).

Skill level colour-coded: beginner=gray, intermediate=sky, advanced=emerald.
--}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-5xl mx-auto px-4 py-8">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                {{ auth()->user()->isDsAdmin() ? 'All Progress Reports' : 'My Progress Reports' }}
            </h1>
            <a href="{{ route('drivesmart.instructor.progress.create') }}"
               class="bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-sky-700 transition">
                + New Report
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-sky-50 dark:bg-sky-900/20 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-700 rounded-lg px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @forelse($reports as $report)
            @php
                $skillColors = [
                    'beginner'     => 'bg-gray-100 text-gray-600 dark:bg-zinc-700 dark:text-zinc-400',
                    'intermediate' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400',
                    'advanced'     => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                ];
            @endphp
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-gray-100 dark:border-zinc-700 p-5 mb-4">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-semibold text-gray-800 dark:text-gray-100 text-sm">
                                🎓 {{ $report->student->name }}
                            </span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $skillColors[$report->skill_level] }}">
                                {{ ucfirst($report->skill_level) }}
                            </span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                · {{ $report->lessons_completed }} lessons completed
                            </span>
                        </div>
                        @if($report->notes)
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $report->notes }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-2">
                            @if(auth()->user()->isDsAdmin())
                                By {{ $report->instructor->name }} ·
                            @endif
                            {{ $report->created_at->format('d M Y') }}
                        </p>
                    </div>

                    <a href="{{ route('drivesmart.instructor.progress.edit', $report) }}"
                       class="ml-4 shrink-0 border border-sky-300 text-sky-600 dark:text-sky-400 px-3 py-1.5 rounded-lg text-xs hover:bg-sky-50 dark:hover:bg-sky-900/20 transition">
                        ✏️ Edit
                    </a>
                </div>
            </div>
        @empty
            <p class="text-gray-500 dark:text-gray-400 text-center py-12">
                No progress reports written yet.
                <a href="{{ route('drivesmart.instructor.progress.create') }}" class="text-sky-600 dark:text-sky-400 hover:underline">Write the first one →</a>
            </p>
        @endforelse
    </div>
</x-layouts::project-shell>
