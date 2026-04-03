{{-- resources/views/projects/drivesmart/instructor/lessons/index.blade.php --}}
{{--
EXAM STUDY NOTE — Instructor: My Schedule
==========================================
$lessons → all lessons for this instructor (all statuses, ordered by scheduled_at).
Admins see ALL lessons across all instructors.

For planned lessons, instructor can update status and notes via a small inline form.
For planned future lessons, a delete button is also shown.
--}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-5xl mx-auto px-4 py-8">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                {{ auth()->user()->isDsAdmin() ? 'All Lessons' : 'My Schedule' }}
            </h1>
            <a href="{{ route('drivesmart.instructor.lessons.create') }}"
               class="bg-sky-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-sky-700 transition">
                + New Lesson
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-sky-50 dark:bg-sky-900/20 text-sky-700 dark:text-sky-300 border border-sky-200 dark:border-sky-700 rounded-lg px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

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
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-gray-100 dark:border-zinc-700 p-5 mb-4">

                <div class="flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-semibold text-gray-800 dark:text-gray-100 text-sm">
                                🗓 {{ $lesson->scheduled_at->format('l d M Y, H:i') }}
                            </span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $color }}">
                                {{ ucfirst($lesson->status) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Student: <span class="font-medium">{{ $lesson->student->name }}</span>
                            @if(auth()->user()->isDsAdmin())
                                · Instructor: <span class="font-medium">{{ $lesson->instructor->name }}</span>
                            @endif
                        </p>
                        @if($lesson->notes)
                            <p class="text-sm text-gray-500 dark:text-gray-400 italic mt-1">"{{ $lesson->notes }}"</p>
                        @endif
                    </div>

                    {{-- Delete (planned only) --}}
                    @if($lesson->status === 'planned')
                        <form method="POST" action="{{ route('drivesmart.instructor.lessons.destroy', $lesson) }}"
                              onsubmit="return confirm('Delete this lesson?')" class="ml-4 shrink-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-red-400 hover:text-red-600 text-xs px-2 py-1 rounded transition">
                                🗑
                            </button>
                        </form>
                    @endif
                </div>

                {{-- Inline status update form --}}
                <form method="POST" action="{{ route('drivesmart.instructor.lessons.update', $lesson) }}"
                      class="mt-3 flex flex-wrap items-end gap-3 border-t border-gray-100 dark:border-zinc-700 pt-3">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Status</label>
                        <select name="status"
                                class="border border-gray-300 dark:border-zinc-600 rounded-lg px-2 py-1.5 text-xs bg-white dark:bg-zinc-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-sky-400">
                            @foreach(['planned','completed','cancelled','sick'] as $s)
                                <option value="{{ $s }}" {{ $lesson->status === $s ? 'selected' : '' }}>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex-1 min-w-40">
                        <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Notes</label>
                        <input type="text" name="notes" value="{{ $lesson->notes }}"
                               placeholder="Optional instructor note…"
                               class="w-full border border-gray-300 dark:border-zinc-600 rounded-lg px-2 py-1.5 text-xs bg-white dark:bg-zinc-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-sky-400">
                    </div>

                    <button type="submit"
                            class="bg-sky-600 text-white px-3 py-1.5 rounded-lg text-xs hover:bg-sky-700 transition">
                        Update
                    </button>
                </form>
            </div>
        @empty
            <p class="text-gray-500 dark:text-gray-400 text-center py-12">
                No lessons yet.
                <a href="{{ route('drivesmart.instructor.lessons.create') }}" class="text-sky-600 dark:text-sky-400 hover:underline">Schedule the first one →</a>
            </p>
        @endforelse
    </div>
</x-layouts::project-shell>
