{{-- resources/views/projects/eventease/organizer/events/index.blade.php --}}
{{--
EXAM STUDY NOTE — Organizer: My Events list
============================================
Admins see ALL events. Organizers see only their own events.
This logic lives in OrganizerEventController@index.

Each row shows: title, date, location, capacity info, actions.
--}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-5xl mx-auto px-4 py-8">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">My Events</h1>
            <a href="{{ route('eventease.organizer.events.create') }}"
               class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700 transition">
                + New Event
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700 rounded-lg px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @forelse($events as $event)
            @php $remaining = $event->remainingCapacity(); @endphp
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-gray-100 dark:border-zinc-700 p-5 mb-4 flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-base font-semibold text-gray-800 dark:text-gray-100">
                        {{ $event->title }}
                    </p>
                    <div class="flex flex-wrap gap-4 text-sm text-gray-500 dark:text-gray-400 mt-1">
                        <span>🗓 {{ $event->date->format('d M Y, H:i') }}</span>
                        <span>📍 {{ $event->location }}</span>
                        <span>🎟 {{ $remaining }} / {{ $event->capacity }} seats left</span>
                        <span>💶 @if($event->price == 0) Free @else € {{ number_format($event->price, 2) }} @endif</span>
                        @if(auth()->user()->isEeAdmin())
                            <span class="text-xs text-gray-400">by {{ $event->organizer->name }}</span>
                        @endif
                    </div>
                </div>

                <div class="flex gap-2 shrink-0">
                    <a href="{{ route('eventease.organizer.events.edit', $event) }}"
                       class="border border-emerald-600 text-emerald-700 dark:text-emerald-400 px-3 py-1.5 rounded-lg text-sm hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition">
                        ✏️ Edit
                    </a>

                    <form method="POST" action="{{ route('eventease.organizer.events.destroy', $event) }}"
                          onsubmit="return confirm('Delete this event? All reservations will also be deleted.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="border border-red-300 text-red-600 dark:text-red-400 px-3 py-1.5 rounded-lg text-sm hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                            🗑 Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-gray-500 dark:text-gray-400 text-center py-16">
                You haven't created any events yet.
            </p>
        @endforelse
    </div>
</x-layouts::project-shell>
