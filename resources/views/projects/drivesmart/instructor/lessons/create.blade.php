{{-- resources/views/projects/drivesmart/instructor/lessons/create.blade.php --}}
{{--
EXAM STUDY NOTE — Create Lesson form
=====================================
$students → all users with ds_role='student', for the dropdown.

scheduled_at uses type="datetime-local" — the browser renders a date+time picker.
The controller validates 'after:now' so past lesson slots are rejected.

The instructor_id is set automatically in the controller (auth()->id()).
The status defaults to 'planned' — you can't create a lesson that's already done.
--}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-2xl mx-auto px-4 py-8">

        <a href="{{ route('drivesmart.instructor.lessons.index') }}"
           class="text-sm text-sky-600 dark:text-sky-400 hover:underline mb-4 inline-block">
            ← Back to schedule
        </a>

        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Schedule New Lesson</h1>

        @if($errors->any())
            <div class="mb-4 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-700 rounded-lg px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('drivesmart.instructor.lessons.store') }}"
              class="bg-white dark:bg-zinc-800 rounded-xl shadow border border-gray-100 dark:border-zinc-700 p-6 space-y-5">
            @csrf

            {{-- Student --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Student <span class="text-red-500">*</span></label>
                <select name="student_id" required
                        class="w-full border border-gray-300 dark:border-zinc-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-zinc-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-sky-400">
                    <option value="">— Select a student —</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Date & Time --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date & Time <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" required
                       class="w-full border border-gray-300 dark:border-zinc-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-zinc-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-sky-400">
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes (optional)</label>
                <textarea name="notes" rows="3"
                          placeholder="e.g. Motorway practice, parallel parking…"
                          class="w-full border border-gray-300 dark:border-zinc-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-zinc-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-sky-400">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('drivesmart.instructor.lessons.index') }}"
                   class="border border-gray-300 dark:border-zinc-600 px-5 py-2 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-zinc-700 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-sky-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-sky-700 transition">
                    Schedule Lesson
                </button>
            </div>
        </form>
    </div>
</x-layouts::project-shell>
