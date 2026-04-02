{{-- resources/views/projects/techbazaar/home.blade.php --}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    {{-- Hero --}}
    <div class="bg-gradient-to-br from-amber-500 to-orange-600 text-white py-20">
        <div class="max-w-5xl mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-4">Welcome to TechBazaar</h2>
            <p class="text-lg text-amber-100 mb-8 max-w-xl mx-auto">
                The marketplace for tech products. Browse laptops, accessories, and gadgets from
                trusted sellers — or become a seller yourself.
            </p>

            {{-- Search bar --}}
            <form method="GET" action="{{ route('techbazaar.products.index') }}" class="flex gap-2 max-w-lg mx-auto">
                <input
                    type="text"
                    name="search"
                    placeholder="Search products..."
                    value="{{ request('search') }}"
                    class="flex-1 rounded-lg px-4 py-3 text-zinc-900 focus:outline-none focus:ring-2 focus:ring-amber-300"
                />
                <button type="submit"
                        class="bg-zinc-900 text-white px-6 py-3 rounded-lg hover:bg-zinc-800 font-medium">
                    Search
                </button>
            </form>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 py-12">

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mb-6 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700
                         text-green-800 dark:text-green-300 rounded-lg px-4 py-3">
                {{ session('success') }}
            </div>
        @endif

        {{-- Auth call-to-action --}}
        @guest
            <div class="grid sm:grid-cols-3 gap-6 mb-12">
                @foreach([
                    ['icon' => '🛍️', 'title' => 'Browse',  'desc' => 'Search & filter hundreds of tech products'],
                    ['icon' => '🛒', 'title' => 'Buy',     'desc' => 'Add to cart and place orders instantly'],
                    ['icon' => '⭐', 'title' => 'Review',  'desc' => 'Leave reviews after your purchase'],
                ] as $f)
                    <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700
                                 rounded-xl p-6 text-center shadow-sm">
                        <div class="text-4xl mb-3">{{ $f['icon'] }}</div>
                        <h3 class="font-semibold text-zinc-900 dark:text-white mb-1">{{ $f['title'] }}</h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
            <div class="text-center mb-12">
                <a href="{{ route('login') }}"
                   class="bg-amber-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-amber-600 mr-3">
                    Log in
                </a>
                <a href="{{ route('register') }}"
                   class="border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300
                          px-8 py-3 rounded-lg font-semibold hover:bg-zinc-100 dark:hover:bg-zinc-800">
                    Register
                </a>
            </div>
        @endguest

        @auth
            <div class="text-center mb-12">
                <a href="{{ route('techbazaar.home') }}"
                   class="bg-amber-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-amber-600">
                    Browse Products →
                </a>
                <a href="{{ route('techbazaar.dashboard') }}"
                   class="ml-3 border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300
                          px-8 py-3 rounded-lg font-semibold hover:bg-zinc-100 dark:hover:bg-zinc-800">
                    My Dashboard
                </a>
            </div>
        @endauth
    </div>
</x-layouts::project-shell>
