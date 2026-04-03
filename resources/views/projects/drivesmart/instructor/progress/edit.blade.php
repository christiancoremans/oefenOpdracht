{{-- resources/views/projects/drivesmart/instructor/progress/edit.blade.php --}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-2xl mx-auto px-4 py-8">

        <a href="{{ route('drivesmart.instructor.progress.index') }}"
           class="text-sm text-sky-600 dark:text-sky-400 hover:underline mb-4 inline-block">
            ← Back to reports
        </a>

        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Edit Progress Report</h1>

        @if($errors->any())
            <div class="mb-4 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-700 rounded-lg px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('drivesmart.instructor.progress.update', $report) }}"
              class="bg-white dark:bg-zinc-800 rounded-xl shadow border border-gray-100 dark:border-zinc-700 p-6 space-y-5">
            @csrf
            @method('PUT')

            {{-- Student --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Student <span class="text-red-500">*</span></label>
                <select name="student_id" required
                        class="w-full border border-gray-300 dark:border-zinc-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-zinc-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-sky-400">
                    <option value="">— Select a student —</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ old('student_id', $report->student_id) == $student->id ? 'selected' : '' }}>
                            {{ $student->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Lessons Completed + Skill Level --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lessons Completed <span class="text-red-500">*</span></label>
                    <input type="number" name="lessons_completed" value="{{ old('lessons_completed', $report->lessons_completed) }}" min="0" required
                           class="w-full border border-gray-300 dark:border-zinc-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-zinc-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-sky-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Skill Level <span class="text-red-500">*</span></label>
                    <select name="skill_level" required
                            class="w-full border border-gray-300 dark:border-zinc-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-zinc-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-sky-400">
                        @foreach(['beginner','intermediate','advanced'] as $level)
                            <option value="{{ $level }}" {{ old('skill_level', $report->skill_level) === $level ? 'selected' : '' }}>
                                {{ ucfirst($level) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                <textarea name="notes" rows="4"
                          class="w-full border border-gray-300 dark:border-zinc-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-zinc-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-sky-400">{{ old('notes', $report->notes) }}</textarea>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('drivesmart.instructor.progress.index') }}"
                   class="border border-gray-300 dark:border-zinc-600 px-5 py-2 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-zinc-700 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-sky-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-sky-700 transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-layouts::project-shell>
