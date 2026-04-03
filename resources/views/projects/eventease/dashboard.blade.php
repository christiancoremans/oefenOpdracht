{{-- resources/views/projects/eventease/dashboard.blade.php --}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-4xl mx-auto px-4 py-12 text-center">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100 mb-3">
            Welcome to EventEase
        </h1>
        <p class="text-gray-500 dark:text-gray-400 mb-8">
            Discover and book tickets for upcoming events.
        </p>

        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('eventease.home') }}"
               class="bg-emerald-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-emerald-700 transition">
                🎫 Browse Events
            </a>
            <a href="{{ route('eventease.reservations.index') }}"
               class="border border-emerald-600 text-emerald-700 dark:text-emerald-400 px-6 py-3 rounded-lg font-medium hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition">
                🎟 My Tickets
            </a>
            @if(auth()->user()->isEeOrganizer() || auth()->user()->isEeAdmin())
                <a href="{{ route('eventease.organizer.events.create') }}"
                   class="border border-emerald-600 text-emerald-700 dark:text-emerald-400 px-6 py-3 rounded-lg font-medium hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition">
                    ✏️ Create Event
                </a>
            @endif
        </div>
    </div>
</x-layouts::project-shell>
