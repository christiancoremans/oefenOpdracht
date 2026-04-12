<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
<div class="max-w-5xl mx-auto py-10 px-4">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-purple-700">🎵 MusicHub Workshops</h1>
            <p class="text-gray-500 mt-1">Browse and reserve a spot in one of our upcoming workshops.</p>
        </div>

        @auth
            @if(auth()->user()->isMusicAdmin())
                <a href="{{ route('music.workshops.create') }}"
                   class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                    + New Workshop
                </a>
            @endif
        @endauth
    </div>

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

    {{-- Workshop cards --}}
    @if($workshops->isEmpty())
        <div class="text-center py-20 text-gray-500">
            <p class="text-xl">No workshops scheduled yet.</p>
            @auth
                @if(auth()->user()->isMusicAdmin())
                    <a href="{{ route('music.workshops.create') }}" class="mt-4 inline-block text-purple-600 underline">Create the first one</a>
                @endif
            @endauth
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($workshops as $workshop)
                <a href="{{ route('music.workshops.show', $workshop) }}"
                   class="block bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition overflow-hidden group">

                    {{-- Picture --}}
                    @if($workshop->picture)
                        <img src="{{ Storage::url($workshop->picture) }}"
                             alt="{{ $workshop->title }}"
                             class="w-full h-40 object-cover group-hover:opacity-95 transition">
                    @else
                        <div class="w-full h-40 bg-purple-100 flex items-center justify-center text-purple-400 text-5xl">
                            🎵
                        </div>
                    @endif

                    <div class="p-4">
                        {{-- Title + Full badge --}}
                        <div class="flex items-start justify-between gap-2">
                            <h2 class="font-semibold text-gray-800 text-lg leading-tight">{{ $workshop->title }}</h2>
                            @if($workshop->isFull())
                                <span class="shrink-0 text-xs bg-red-100 text-red-600 font-medium px-2 py-0.5 rounded-full">Full</span>
                            @else
                                <span class="shrink-0 text-xs bg-green-100 text-green-700 font-medium px-2 py-0.5 rounded-full">
                                    {{ $workshop->capacity - $workshop->reservations_count }} left
                                </span>
                            @endif
                        </div>

                        {{-- Room --}}
                        <p class="text-sm text-gray-500 mt-1">📍 {{ $workshop->room }}</p>

                        {{-- Date/Time --}}
                        <p class="text-sm text-gray-500">
                            📅 {{ $workshop->start_time->format('d M Y, H:i') }}
                        </p>

                        {{-- Instructors --}}
                        @if($workshop->instructors->isNotEmpty())
                            <p class="text-xs text-purple-600 mt-2">
                                🎤 {{ $workshop->instructors->pluck('name')->join(', ') }}
                            </p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
</x-layouts::project-shell>
