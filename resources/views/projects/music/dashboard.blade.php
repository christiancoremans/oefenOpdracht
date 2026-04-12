<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
<div class="max-w-4xl mx-auto py-10 px-4">

    <h1 class="text-3xl font-bold text-purple-700 mb-2">🎵 MusicHub Dashboard</h1>
    <p class="text-gray-500 mb-8">Welcome back, {{ auth()->user()->name }}!</p>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-300 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- Role badge --}}
    <div class="mb-6">
        @if(auth()->user()->isMusicAdmin())
            <span class="inline-block bg-purple-600 text-white text-sm font-semibold px-3 py-1 rounded-full">
                Admin
            </span>
        @else
            <span class="inline-block bg-purple-100 text-purple-700 text-sm font-semibold px-3 py-1 rounded-full">
                Member
            </span>
        @endif
    </div>

    {{-- Quick action cards --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">

        <a href="{{ route('music.home') }}"
           class="block p-6 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition text-center">
            <div class="text-4xl mb-3">🎼</div>
            <h2 class="font-semibold text-gray-800">Browse Workshops</h2>
            <p class="text-sm text-gray-500 mt-1">See all upcoming music workshops</p>
        </a>

        @if(auth()->user()->isMusicAdmin())
            <a href="{{ route('music.workshops.create') }}"
               class="block p-6 bg-purple-50 border border-purple-200 rounded-xl shadow-sm hover:shadow-md transition text-center">
                <div class="text-4xl mb-3">➕</div>
                <h2 class="font-semibold text-purple-800">New Workshop</h2>
                <p class="text-sm text-purple-500 mt-1">Create a new workshop session</p>
            </a>
        @endif

        {{-- My reservations card --}}
        <div class="p-6 bg-white border border-gray-200 rounded-xl shadow-sm text-center">
            <div class="text-4xl mb-3">📋</div>
            <h2 class="font-semibold text-gray-800">My Reservations</h2>
            <p class="text-sm text-gray-500 mt-1">
                {{ auth()->user()->musicReservations()->count() }} active reservation(s)
            </p>
        </div>

    </div>

    {{-- My reservations list --}}
    @php $reservations = auth()->user()->musicReservations()->with('workshop')->latest()->get(); @endphp

    @if($reservations->isNotEmpty())
        <div class="mt-10">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Your Reservations</h2>
            <div class="space-y-3">
                @foreach($reservations as $res)
                    <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-800">{{ $res->workshop->title }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $res->workshop->start_time->format('d M Y, H:i') }} · {{ $res->workshop->room }}
                            </p>
                        </div>
                        <a href="{{ route('music.workshops.show', $res->workshop) }}"
                           class="text-purple-600 text-sm hover:underline">View</a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
</x-layouts::project-shell>
