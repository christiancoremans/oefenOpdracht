{{-- resources/views/projects/eventease/reservations/index.blade.php --}}
{{--
EXAM STUDY NOTE — My Tickets page
===================================
Shows the logged-in user's reservations.
$reservations is eager-loaded with 'event' to avoid N+1 queries.

Each reservation shows:
  • Event title (link to event detail)
  • Date, location, seats, status
  • Cancel button (if confirmed)
  • Rebook button (if cancelled + event not full)

Status is colour-coded: confirmed = emerald, cancelled = red/gray
--}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-4xl mx-auto px-4 py-8">

        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">My Tickets</h1>

        @if(session('success'))
            <div class="mb-4 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700 rounded-lg px-4 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-700 rounded-lg px-4 py-3 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        @forelse($reservations as $reservation)
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-gray-100 dark:border-zinc-700 p-5 mb-4 flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <a href="{{ route('eventease.events.show', $reservation->event) }}"
                       class="text-base font-semibold text-gray-800 dark:text-gray-100 hover:text-emerald-600 dark:hover:text-emerald-400 transition">
                        {{ $reservation->event->title }}
                    </a>
                    <div class="flex flex-wrap gap-4 text-sm text-gray-500 dark:text-gray-400 mt-1">
                        <span>🗓 {{ $reservation->event->date->format('d M Y, H:i') }}</span>
                        <span>📍 {{ $reservation->event->location }}</span>
                        <span>🎟 {{ $reservation->seats }} seat{{ $reservation->seats > 1 ? 's' : '' }}</span>
                        <span class="font-medium {{ $reservation->status === 'confirmed' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500' }}">
                            {{ ucfirst($reservation->status) }}
                        </span>
                    </div>
                </div>

                <form method="POST" action="{{ route('eventease.reservations.update', $reservation) }}" class="shrink-0">
                    @csrf
                    @method('PATCH')
                    @if($reservation->status === 'confirmed')
                        <input type="hidden" name="action" value="cancel">
                        <button type="submit"
                                class="bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 px-4 py-2 rounded-lg text-sm hover:bg-red-200 dark:hover:bg-red-900/50 transition">
                            Cancel
                        </button>
                    @else
                        <input type="hidden" name="action" value="rebook">
                        <button type="submit"
                                class="bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 px-4 py-2 rounded-lg text-sm hover:bg-emerald-200 dark:hover:bg-emerald-900/50 transition"
                                {{ $reservation->event->isFull() ? 'disabled title=Event is full' : '' }}>
                            Rebook
                        </button>
                    @endif
                </form>
            </div>
        @empty
            <p class="text-gray-500 dark:text-gray-400 text-center py-16">
                You haven't booked any events yet.
                <a href="{{ route('eventease.home') }}" class="text-emerald-600 dark:text-emerald-400 hover:underline">Browse events →</a>
            </p>
        @endforelse
    </div>
</x-layouts::project-shell>
