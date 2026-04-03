{{-- resources/views/projects/eventease/events/show.blade.php --}}
{{--
EXAM STUDY NOTE — Event detail page
====================================
$event          → the event model (with remainingCapacity() available)
$remainingCapacity → integer result of $event->remainingCapacity()
$userReservation   → the logged-in user's Reservation row for this event,
                     or null if not booked / not logged in

This page handles three states:
1. User is not logged in           → show Login button
2. User is logged in, no booking  → show booking form
3. User has an existing booking   → show status + cancel/rebook button
--}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-3xl mx-auto px-4 py-8">

        {{-- Back link --}}
        <a href="{{ route('eventease.home') }}"
           class="text-sm text-emerald-600 dark:text-emerald-400 hover:underline mb-4 inline-block">
            ← Back to events
        </a>

        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow border border-gray-100 dark:border-zinc-700 p-6">

            <div class="flex items-start justify-between mb-4">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                    {{ $event->title }}
                </h1>
                @if($remainingCapacity === 0)
                    <span class="bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 text-xs font-semibold px-3 py-1 rounded-full">
                        SOLD OUT
                    </span>
                @else
                    <span class="bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 text-xs font-semibold px-3 py-1 rounded-full">
                        {{ $remainingCapacity }} / {{ $event->capacity }} seats left
                    </span>
                @endif
            </div>

            <div class="grid sm:grid-cols-2 gap-3 text-sm text-gray-600 dark:text-gray-400 mb-4">
                <div>📍 <strong>Location:</strong> {{ $event->location }}</div>
                <div>🗓 <strong>Date:</strong> {{ $event->date->format('l d F Y, H:i') }}</div>
                <div>💶 <strong>Price:</strong> @if($event->price == 0) Free @else € {{ number_format($event->price, 2) }} @endif</div>
                <div>👤 <strong>Organizer:</strong> {{ $event->organizer->name }}</div>
            </div>

            @if($event->description)
                <p class="text-gray-700 dark:text-gray-300 mb-6 border-t border-gray-100 dark:border-zinc-700 pt-4">
                    {{ $event->description }}
                </p>
            @endif

            {{-- ── Reservation panel ─────────────────────────────────── --}}
            @auth
                @if($userReservation)
                    {{-- User already has a reservation --}}
                    <div class="bg-gray-50 dark:bg-zinc-900 rounded-lg p-4 border border-gray-200 dark:border-zinc-600">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Your reservation:
                            <span class="font-semibold {{ $userReservation->status === 'confirmed' ? 'text-emerald-600' : 'text-red-500' }}">
                                {{ ucfirst($userReservation->status) }}
                            </span>
                            ({{ $userReservation->seats }} seat{{ $userReservation->seats > 1 ? 's' : '' }})
                        </p>

                        @if($errors->any())
                            <p class="text-red-600 text-sm mb-2">{{ $errors->first() }}</p>
                        @endif

                        <form method="POST" action="{{ route('eventease.reservations.update', $userReservation) }}">
                            @csrf
                            @method('PATCH')

                            @if($userReservation->status === 'confirmed')
                                <input type="hidden" name="action" value="cancel">
                                <button type="submit"
                                        class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition">
                                    Cancel reservation
                                </button>
                            @else
                                <input type="hidden" name="action" value="rebook">
                                <button type="submit"
                                        class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-emerald-700 transition"
                                        {{ $remainingCapacity === 0 ? 'disabled' : '' }}>
                                    Rebook
                                </button>
                                @if($remainingCapacity === 0)
                                    <span class="ml-2 text-sm text-red-500">Event is full</span>
                                @endif
                            @endif
                        </form>
                    </div>
                @elseif($remainingCapacity > 0)
                    {{-- User has no reservation — show booking form --}}
                    <div class="bg-gray-50 dark:bg-zinc-900 rounded-lg p-4 border border-gray-200 dark:border-zinc-600">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Book tickets</h3>

                        @if($errors->any())
                            <p class="text-red-600 text-sm mb-2">{{ $errors->first() }}</p>
                        @endif

                        <form method="POST" action="{{ route('eventease.reservations.store') }}" class="flex items-end gap-3">
                            @csrf
                            <input type="hidden" name="event_id" value="{{ $event->id }}">

                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Seats</label>
                                <input type="number" name="seats" value="{{ old('seats', 1) }}"
                                       min="1" max="{{ min(10, $remainingCapacity) }}"
                                       class="border border-gray-300 dark:border-zinc-600 rounded-lg px-3 py-2 text-sm w-20 bg-white dark:bg-zinc-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                            </div>

                            <button type="submit"
                                    class="bg-emerald-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700 transition">
                                Book now
                            </button>
                        </form>
                    </div>
                @else
                    <p class="text-sm text-red-500">This event is sold out.</p>
                @endif
            @else
                <div class="bg-gray-50 dark:bg-zinc-900 rounded-lg p-4 border border-gray-200 dark:border-zinc-600 text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                        Log in to book tickets for this event.
                    </p>
                    <a href="{{ route('login') }}"
                       class="bg-emerald-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700 transition">
                        Log in
                    </a>
                </div>
            @endauth
        </div>
    </div>
</x-layouts::project-shell>
