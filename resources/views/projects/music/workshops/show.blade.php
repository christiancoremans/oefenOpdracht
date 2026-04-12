<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
<div class="max-w-4xl mx-auto py-10 px-4">

    {{-- Back link --}}
    <a href="{{ route('music.home') }}" class="text-sm text-purple-600 hover:underline mb-6 inline-block">
        ← Back to all workshops
    </a>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-300 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-300 text-red-800 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- Workshop card --}}
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

        {{-- Picture --}}
        @if($workshop->picture)
            <img src="{{ Storage::url($workshop->picture) }}"
                 alt="{{ $workshop->title }}"
                 class="w-full h-64 object-cover">
        @else
            <div class="w-full h-40 bg-purple-100 flex items-center justify-center text-purple-400 text-6xl">
                🎵
            </div>
        @endif

        <div class="p-8">

            {{-- Title + admin actions --}}
            <div class="flex items-start justify-between gap-4">
                <h1 class="text-3xl font-bold text-gray-900">{{ $workshop->title }}</h1>

                @auth
                    @if(auth()->user()->isMusicAdmin())
                        <div class="flex gap-2 shrink-0">
                            <a href="{{ route('music.workshops.edit', $workshop) }}"
                               class="bg-yellow-400 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-yellow-500 transition">
                                ✏️ Edit
                            </a>
                            <form action="{{ route('music.workshops.destroy', $workshop) }}" method="POST"
                                  onsubmit="return confirm('Delete this workshop permanently?')">
                                @csrf
                                @method('DELETE')
                                <button class="bg-red-500 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-red-600 transition">
                                    🗑️ Delete
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>

            {{-- Meta info --}}
            <div class="mt-4 grid gap-2 sm:grid-cols-2 text-sm text-gray-600">
                <div>📍 <span class="font-medium text-gray-800">{{ $workshop->room }}</span></div>
                <div>👥 <span class="font-medium text-gray-800">Capacity: {{ $workshop->capacity }}</span></div>
                <div>🕐 Start: <span class="font-medium text-gray-800">{{ $workshop->start_time->format('d M Y, H:i') }}</span></div>
                <div>🕔 End: <span class="font-medium text-gray-800">{{ $workshop->end_time->format('d M Y, H:i') }}</span></div>
            </div>

            {{-- Spots left --}}
            <div class="mt-4">
                @if($workshopFull)
                    <span class="inline-block bg-red-100 text-red-700 text-sm font-semibold px-3 py-1 rounded-full">
                        ❌ Fully booked ({{ $workshop->reservations->count() }}/{{ $workshop->capacity }})
                    </span>
                @else
                    <span class="inline-block bg-green-100 text-green-700 text-sm font-semibold px-3 py-1 rounded-full">
                        ✅ {{ $workshop->capacity - $workshop->reservations->count() }} spot(s) left
                    </span>
                @endif
            </div>

            {{-- Description --}}
            <div class="mt-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-2">About this workshop</h2>
                <p class="text-gray-600 whitespace-pre-line">{{ $workshop->description }}</p>
            </div>

            {{-- Instructors --}}
            @if($workshop->instructors->isNotEmpty())
                <div class="mt-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-3">🎤 Instructors</h2>
                    <div class="flex flex-wrap gap-3">
                        @foreach($workshop->instructors as $instructor)
                            <div class="bg-purple-50 border border-purple-200 rounded-lg px-4 py-2">
                                <p class="font-medium text-purple-800">{{ $instructor->name }}</p>
                                <p class="text-xs text-purple-500">{{ $instructor->specialization }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Reserve section --}}
            <div class="mt-8 pt-6 border-t border-gray-100">
                @auth
                    @if($userReservation)
                        <div class="p-4 bg-purple-50 border border-purple-200 text-purple-800 rounded-lg flex items-center justify-between gap-4">
                            <span>🎟️ You have a spot in this workshop!</span>
                            <form action="{{ route('music.reservations.destroy', $userReservation) }}" method="POST"
                                  onsubmit="return confirm('Cancel your reservation?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="bg-red-500 text-white px-4 py-1.5 rounded-lg text-sm hover:bg-red-600 transition">
                                    Cancel Spot
                                </button>
                            </form>
                        </div>
                    @elseif($workshopFull)
                        <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                            This workshop is fully booked. Check other workshops!
                        </div>
                    @elseif(!auth()->user()->isMusicAdmin())
                        <form action="{{ route('music.reservations.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="workshop_id" value="{{ $workshop->id }}">
                            <button type="submit"
                                    class="bg-purple-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-purple-700 transition">
                                🎟️ Reserve My Spot
                            </button>
                        </form>
                    @else
                        <p class="text-sm text-gray-400">Admins manage workshops, not reserve spots.</p>
                    @endif
                @else
                    <p class="text-gray-600">
                        <a href="{{ route('login') }}" class="text-purple-600 font-semibold hover:underline">Log in</a>
                        to reserve your spot.
                    </p>
                @endauth
            </div>

        </div>
    </div>

    {{-- Reservations list (admin only) --}}
    @auth
        @if(auth()->user()->isMusicAdmin() && $workshop->reservations->isNotEmpty())
            <div class="mt-8 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-xl font-bold text-gray-800 mb-4">
                    Attendees ({{ $workshop->reservations->count() }}/{{ $workshop->capacity }})
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="border-b text-gray-500">
                                <th class="py-2 pr-4">Name</th>
                                <th class="py-2 pr-4">Email</th>
                                <th class="py-2 pr-4">Phone</th>
                                <th class="py-2">Experience</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workshop->reservations as $res)
                                <tr class="border-b border-gray-50 hover:bg-gray-50">
                                    <td class="py-2 pr-4 font-medium text-gray-800">{{ $res->full_name }}</td>
                                    <td class="py-2 pr-4 text-gray-600">{{ $res->email }}</td>
                                    <td class="py-2 pr-4 text-gray-600">{{ $res->phone ?? '—' }}</td>
                                    <td class="py-2 text-gray-600">{{ $res->music_experience ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endauth

</div>
</x-layouts::project-shell>
