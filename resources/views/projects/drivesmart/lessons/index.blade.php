{{-- resources/views/projects/drivesmart/lessons/index.blade.php --}}
{{--
EXAM STUDY NOTE — Student: My Lessons + Progress Reports
=========================================================
$lessons         → all lessons for this student (upcoming + history), ordered by date
$progressReports → progress reports written for this student

Status colours:
  planned   → sky (default/upcoming)
  completed → emerald (success)
  cancelled → gray (neutral — student cancelled)
  sick      → amber (warning — reported sick)

Cancel / sick buttons only show on MODIFIABLE lessons (planned + future).
Using $lesson->isModifiable() from the Lesson model — business logic lives
on the MODEL, not scattered in the view.
--}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-4xl mx-auto px-4 py-8">

        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">My Lessons</h1>

        @if(session('success'))
            <div class="mb-4 bg-sky-50 dark:bg-sky-900/20 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-700 rounded-lg px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-700 rounded-lg px-4 py-3 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- ── Lessons ──────────────────────────────────────────────────── --}}
        @forelse($lessons as $lesson)
            @php
                $statusColors = [
                    'planned'   => 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400',
                    'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                    'cancelled' => 'bg-gray-100 text-gray-600 dark:bg-zinc-700 dark:text-zinc-400',
                    'sick'      => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                ];
                $color = $statusColors[$lesson->status] ?? $statusColors['planned'];
            @endphp
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-gray-100 dark:border-zinc-700 p-5 mb-4 flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                            🗓 {{ $lesson->scheduled_at->format('l d M Y, H:i') }}
                        </span>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $color }}">
                            {{ ucfirst($lesson->status) }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Instructor: {{ $lesson->instructor->name }}
                    </p>
                    @if($lesson->notes)
                        <p class="text-sm text-gray-500 dark:text-gray-400 italic mt-1">
                            "{{ $lesson->notes }}"
                        </p>
                    @endif
                </div>

                @if($lesson->isModifiable())
                    <div class="flex gap-2 shrink-0">
                        <form method="POST" action="{{ route('drivesmart.lessons.update', $lesson) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="cancel">
                            <button type="submit"
                                    class="border border-red-300 text-red-600 dark:text-red-400 px-3 py-1.5 rounded-lg text-xs hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                Cancel
                            </button>
                        </form>
                        <form method="POST" action="{{ route('drivesmart.lessons.update', $lesson) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="sick">
                            <button type="submit"
                                    class="border border-amber-300 text-amber-600 dark:text-amber-400 px-3 py-1.5 rounded-lg text-xs hover:bg-amber-50 dark:hover:bg-amber-900/20 transition">
                                🤒 Sick
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <p class="text-gray-500 dark:text-gray-400 text-center py-12">
                No lessons scheduled yet. Contact your instructor.
            </p>
        @endforelse

        {{-- ── Progress Reports ─────────────────────────────────────────────── --}}
        @if($progressReports->isNotEmpty())
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-100 mt-10 mb-4">My Progress Reports</h2>

            @foreach($progressReports as $report)
                @php
                    $skillColors = [
                        'beginner'     => 'bg-gray-100 text-gray-600 dark:bg-zinc-700 dark:text-zinc-400',
                        'intermediate' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-400',
                        'advanced'     => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                    ];
                @endphp
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-gray-100 dark:border-zinc-700 p-5 mb-4">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $skillColors[$report->skill_level] }}">
                            {{ ucfirst($report->skill_level) }}
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $report->lessons_completed }} lessons completed
                        </span>
                        <span class="text-xs text-gray-400 ml-auto">
                            by {{ $report->instructor->name }} · {{ $report->created_at->format('d M Y') }}
                        </span>
                    </div>
                    @if($report->notes)
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $report->notes }}</p>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
</x-layouts::project-shell>
