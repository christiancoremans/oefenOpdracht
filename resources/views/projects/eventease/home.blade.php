{{-- resources/views/projects/eventease/home.blade.php --}}
{{--
EXAM STUDY NOTE — EventEase home (event index)
==============================================
Lists all upcoming events. Public — anyone can browse.
$events is a collection of Event models, ordered by date ascending
(from the scopeUpcoming() scope: orderBy('date')).

Each card shows:
  • Title, location, date, price
  • Remaining capacity (with "SOLD OUT" badge if full)
  • Link to the event detail page
--}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-5xl mx-auto px-4 py-8">

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                Upcoming Events
            </h1>
            @auth
                @if(auth()->user()->isEeOrganizer() || auth()->user()->isEeAdmin())
                    <a href="{{ route('eventease.organizer.events.create') }}"
                       class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700 transition">
                        + New Event
                    </a>
                @endif
            @endauth
        </div>

        @if($events->isEmpty())
            <p class="text-gray-500 dark:text-gray-400 text-center py-16">
                No upcoming events at the moment. Check back soon!
            </p>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($events as $event)
                    @php $remaining = $event->remainingCapacity(); @endphp
                    <a href="{{ route('eventease.events.show', $event) }}"
                       class="block bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-gray-100 dark:border-zinc-700 p-5 hover:shadow-md transition group">

                        <div class="flex items-start justify-between mb-2">
                            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition">
                                {{ $event->title }}
                            </h2>
                            @if($remaining === 0)
                                <span class="ml-2 shrink-0 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 text-xs font-semibold px-2 py-0.5 rounded-full">
                                    SOLD OUT
                                </span>
                            @else
                                <span class="ml-2 shrink-0 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 text-xs font-semibold px-2 py-0.5 rounded-full">
                                    {{ $remaining }} left
                                </span>
                            @endif
                        </div>

                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">
                            📍 {{ $event->location }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                            🗓 {{ $event->date->format('D d M Y, H:i') }}
                        </p>

                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-emerald-700 dark:text-emerald-400">
                                @if($event->price == 0) Free @else € {{ number_format($event->price, 2) }} @endif
                            </span>
                            <span class="text-xs text-gray-400">
                                {{ $event->capacity }} seats total
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts::project-shell>
