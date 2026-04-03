{{-- resources/views/projects/drivesmart/home.blade.php --}}
{{--
EXAM STUDY NOTE — DriveSmart public landing page
=================================================
Public home: no login required. Shows school info and a login/dashboard prompt.
Unlike EventEase (public event list), DriveSmart has no public data to show —
all meaningful content requires login. The home page acts as a gateway.
--}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-4xl mx-auto px-4 py-16 text-center">

        <div class="text-6xl mb-4">🚗</div>
        <h1 class="text-4xl font-bold text-gray-800 dark:text-gray-100 mb-3">
            Welcome to DriveSmart
        </h1>
        <p class="text-lg text-gray-500 dark:text-gray-400 mb-8 max-w-xl mx-auto">
            Your driving school management platform. Plan lessons, track student progress,
            and monitor instructor schedules — all in one place.
        </p>

        @auth
            <a href="{{ route('drivesmart.dashboard') }}"
               class="inline-block bg-sky-600 text-white px-8 py-3 rounded-lg font-medium hover:bg-sky-700 transition text-lg">
                Go to Dashboard →
            </a>
        @else
            <div class="flex justify-center gap-4">
                <a href="{{ route('login') }}"
                   class="bg-sky-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-sky-700 transition">
                    Log in
                </a>
                <a href="{{ route('register') }}"
                   class="border border-sky-600 text-sky-700 dark:text-sky-400 px-6 py-3 rounded-lg font-medium hover:bg-sky-50 dark:hover:bg-sky-900/20 transition">
                    Register
                </a>
            </div>
        @endauth

        {{-- Feature highlights --}}
        <div class="mt-16 grid sm:grid-cols-3 gap-6 text-left">
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-5 border border-gray-100 dark:border-zinc-700 shadow-sm">
                <div class="text-2xl mb-2">📅</div>
                <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-1">Lesson Planning</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Instructors schedule lessons and assign students with a specific date and time.</p>
            </div>
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-5 border border-gray-100 dark:border-zinc-700 shadow-sm">
                <div class="text-2xl mb-2">📊</div>
                <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-1">Progress Tracking</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Track skill level (beginner → advanced), completed lessons, and instructor notes.</p>
            </div>
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-5 border border-gray-100 dark:border-zinc-700 shadow-sm">
                <div class="text-2xl mb-2">🎓</div>
                <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-1">Student Dashboard</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Students view their upcoming lessons, cancel, or report sick directly.</p>
            </div>
        </div>
    </div>
</x-layouts::project-shell>
