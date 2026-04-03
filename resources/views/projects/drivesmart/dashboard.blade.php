{{-- resources/views/projects/drivesmart/dashboard.blade.php --}}
{{--
EXAM STUDY NOTE — DriveSmart dashboard hub
==========================================
Role-based quick-links hub. Shows different actions depending on ds_role.
Each role is shown their relevant starting points.
--}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-4xl mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-2">Dashboard</h1>
        <p class="text-gray-500 dark:text-gray-400 mb-8">Welcome back, {{ auth()->user()->name }}!</p>

        <div class="flex flex-wrap gap-4">

            {{-- Student links --}}
            @if(auth()->user()->isDsStudent() || auth()->user()->isDsAdmin())
                <a href="{{ route('drivesmart.lessons.index') }}"
                   class="bg-sky-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-sky-700 transition">
                    📅 My Lessons
                </a>
            @endif

            {{-- Instructor links --}}
            @if(auth()->user()->isDsInstructor() || auth()->user()->isDsAdmin())
                <a href="{{ route('drivesmart.instructor.lessons.index') }}"
                   class="border border-sky-600 text-sky-700 dark:text-sky-400 px-6 py-3 rounded-lg font-medium hover:bg-sky-50 dark:hover:bg-sky-900/20 transition">
                    📋 My Schedule
                </a>
                <a href="{{ route('drivesmart.instructor.lessons.create') }}"
                   class="border border-sky-600 text-sky-700 dark:text-sky-400 px-6 py-3 rounded-lg font-medium hover:bg-sky-50 dark:hover:bg-sky-900/20 transition">
                    ✏️ New Lesson
                </a>
                <a href="{{ route('drivesmart.instructor.progress.index') }}"
                   class="border border-sky-600 text-sky-700 dark:text-sky-400 px-6 py-3 rounded-lg font-medium hover:bg-sky-50 dark:hover:bg-sky-900/20 transition">
                    📊 Progress Reports
                </a>
            @endif

            {{-- Admin links --}}
            @if(auth()->user()->isDsAdmin())
                <a href="{{ route('drivesmart.admin.dashboard') }}"
                   class="bg-slate-700 text-white px-6 py-3 rounded-lg font-medium hover:bg-slate-800 transition">
                    ⚙️ Admin Dashboard
                </a>
                <a href="{{ route('drivesmart.admin.users.index') }}"
                   class="bg-slate-700 text-white px-6 py-3 rounded-lg font-medium hover:bg-slate-800 transition">
                    👥 Manage Users
                </a>
            @endif
        </div>

        {{-- Role info badge --}}
        <div class="mt-8 inline-flex items-center gap-2 bg-sky-50 dark:bg-sky-900/20 text-sky-700 dark:text-sky-300 px-4 py-2 rounded-full text-sm">
            Your DriveSmart role:
            <span class="font-semibold capitalize">{{ auth()->user()->ds_role ?? 'student' }}</span>
        </div>
    </div>
</x-layouts::project-shell>
