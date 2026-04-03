@props([
    'title'              => null,
    'currentProject'     => null,
    'projectName'        => null,
    'projectDescription' => null,
])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-zinc-50 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100">

    {{-- =================================================================
         HEADER 1 — PROJECT SWITCHER  (the top bar)
         =================================================================
         EXAM STUDY NOTE:
         This top bar shows ALL practice projects so you can jump between
         them at any time. It reads from config/projects.php.

         Key concepts used here:
         • config('projects')          → reads the projects array
         • route('project.home', slug) → generates /project/{slug}
         • $currentProject             → passed from the controller / view
         • Blade: @foreach / @endforeach to loop over the array
         • Conditional CSS class using ternary:  condition ? 'a' : 'b'
         ================================================================= --}}
    <header class="bg-zinc-900 dark:bg-zinc-950 text-white sticky top-0 z-50 border-b border-zinc-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center h-11 gap-4">
            <span class="text-xs font-semibold text-zinc-400 uppercase tracking-widest shrink-0 hidden sm:inline">
                Switch project:
            </span>
            <nav class="flex items-center gap-1 overflow-x-auto">
                @foreach(config('projects') as $slug => $info)
                    <a
                        href="{{ route('project.home', $slug) }}"
                        class="px-3 py-1 text-sm rounded-md font-medium whitespace-nowrap transition-colors
                               {{ $currentProject === $slug
                                    ? 'bg-indigo-600 text-white'
                                    : 'text-zinc-300 hover:bg-zinc-700 hover:text-white' }}"
                    >
                        {{ $info['name'] }}
                    </a>
                @endforeach
            </nav>

            {{-- Right side: link back to main homepage --}}
            <div class="ml-auto">
                <a href="{{ route('home') }}"
                   class="text-xs text-zinc-400 hover:text-zinc-200 transition-colors">
                    ← Home
                </a>
            </div>
        </div>
    </header>

    {{-- =================================================================
         HEADER 2 — CURRENT PROJECT HEADER
         =================================================================
         EXAM STUDY NOTE:
         This is the main navbar for your specific project/app.
         It shows:
           • Left:  project name (links back to project home)
           • Right: auth links — Dashboard + Logout OR Login + Register

         Key concepts used here:
         • @auth / @else / @endauth  → Blade auth directives
           - @auth  = only show this if the user IS logged in
           - @else  = show this if NOT logged in
         • auth()->user()->name       → get the logged-in user's name
         • route('login')             → named route to the login page
         • route('register')          → named route to the register page
         • route('logout')            → POST route for logging out
         • @csrf                      → always add to POST forms (security)
         • Route::has('register')     → check if the route exists
         ================================================================= --}}
    <header class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">

            {{-- Project name / logo --}}
            <div class="flex items-center gap-3">
                <a
                    href="{{ $currentProject ? route('project.home', $currentProject) : route('home') }}"
                    class="text-xl font-bold text-zinc-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors"
                >
                    {{ $projectName ?? config('app.name') }}
                </a>
                @if($projectDescription)
                    <span class="hidden md:inline text-sm text-zinc-400 dark:text-zinc-500">
                        — {{ $projectDescription }}
                    </span>
                @endif
            </div>

            {{-- Auth navigation --}}
            <nav class="flex items-center gap-2">
                @auth
                    {{-- User is LOGGED IN --}}
                    <span class="hidden sm:inline text-sm text-zinc-500 dark:text-zinc-400 mr-1">
                        {{ auth()->user()->name }}
                    </span>
                    @if($currentProject)
                        <a
                            href="{{ route('project.dashboard', $currentProject) }}"
                            class="text-sm px-3 py-2 rounded-lg text-zinc-600 dark:text-zinc-300
                                   hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors"
                        >
                            Dashboard
                        </a>
                    @endif

                    {{-- EXAM NOTE: logout is always a POST (never GET) for security --}}
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button
                            type="submit"
                            class="text-sm bg-zinc-200 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200
                                   px-4 py-2 rounded-lg hover:bg-zinc-300 dark:hover:bg-zinc-600 transition-colors"
                        >
                            Log out
                        </button>
                    </form>
                @else
                    {{-- User is NOT logged in --}}
                    <a
                        href="{{ route('login') }}"
                        class="text-sm px-3 py-2 rounded-lg text-zinc-600 dark:text-zinc-300
                               hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors"
                    >
                        Log in
                    </a>
                    @if (Route::has('register'))
                        <a
                            href="{{ route('register') }}"
                            class="text-sm bg-indigo-600 text-white px-4 py-2 rounded-lg
                                   hover:bg-indigo-700 transition-colors"
                        >
                            Register
                        </a>
                    @endif
                @endauth
            </nav>
        </div>
    </header>

    {{-- =================================================================
         HEADER 3 — PER-PROJECT NAV BAR
         =================================================================
         EXAM STUDY NOTE:
         This third bar only renders when a specific project needs it.
         Controlled by $currentProject — the same variable passed from
         every controller's projectData() array.

         Why a separate bar instead of putting links in header 2?
         → Header 2 is shared auth/identity UI (login, logout, name).
         → Project navigation (Products, Cart, Orders) lives here so
           they stay separate and easy to maintain per project.

         Key concepts:
         • @if($currentProject === 'techbazaar') → only show for that project
         • request()->routeIs('name') → highlights the active nav link
         • @auth / role checks inside Blade to show/hide role-specific links
         ================================================================= --}}
    @if($currentProject === 'techbazaar')
    <header class="bg-amber-500 text-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center h-11 gap-1">

            {{-- Always visible --}}
            <a href="{{ route('techbazaar.products.index') }}"
               class="px-3 py-1 text-sm rounded-md font-medium whitespace-nowrap transition-colors
                      {{ request()->routeIs('techbazaar.home', 'techbazaar.products.index', 'techbazaar.products.show')
                           ? 'bg-amber-700 text-white'
                           : 'hover:bg-amber-600' }}">
                🛍 Products
            </a>

            @auth
                {{-- Buyer + Admin --}}
                @if(auth()->user()->role === 'buyer' || auth()->user()->role === 'admin')
                    <a href="{{ route('techbazaar.cart.index') }}"
                       class="px-3 py-1 text-sm rounded-md font-medium whitespace-nowrap transition-colors
                              {{ request()->routeIs('techbazaar.cart.*')
                                   ? 'bg-amber-700 text-white'
                                   : 'hover:bg-amber-600' }}">
                        🛒 Cart
                    </a>
                    <a href="{{ route('techbazaar.orders.index') }}"
                       class="px-3 py-1 text-sm rounded-md font-medium whitespace-nowrap transition-colors
                              {{ request()->routeIs('techbazaar.orders.*')
                                   ? 'bg-amber-700 text-white'
                                   : 'hover:bg-amber-600' }}">
                        📦 My Orders
                    </a>
                @endif

                {{-- Seller + Admin --}}
                @if(auth()->user()->role === 'seller' || auth()->user()->role === 'admin')
                    <a href="{{ route('techbazaar.seller.products.index') }}"
                       class="px-3 py-1 text-sm rounded-md font-medium whitespace-nowrap transition-colors
                              {{ request()->routeIs('techbazaar.seller.*')
                                   ? 'bg-amber-700 text-white'
                                   : 'hover:bg-amber-600' }}">
                        📝 My Listings
                    </a>
                @endif

                {{-- Admin only --}}
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('techbazaar.admin.dashboard') }}"
                       class="px-3 py-1 text-sm rounded-md font-medium whitespace-nowrap transition-colors
                              {{ request()->routeIs('techbazaar.admin.*')
                                   ? 'bg-amber-700 text-white'
                                   : 'hover:bg-amber-600' }}">
                        ⚙️ Admin
                    </a>
                @endif
            @endauth
        </div>
    </header>
    @endif

    @if($currentProject === 'devtalk')
    <header class="bg-violet-600 text-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center h-11 gap-1">

            <a href="{{ route('devtalk.home') }}"
               class="px-3 py-1 text-sm rounded-md font-medium whitespace-nowrap transition-colors
                      {{ request()->routeIs('devtalk.home', 'devtalk.threads.*')
                           ? 'bg-violet-800 text-white'
                           : 'hover:bg-violet-700' }}">
                💬 Threads
            </a>

            @auth
                <a href="{{ route('devtalk.threads.create') }}"
                   class="px-3 py-1 text-sm rounded-md font-medium whitespace-nowrap transition-colors hover:bg-violet-700">
                    ✏️ New Thread
                </a>

                @if(auth()->user()->isDtModerator() || auth()->user()->isDtAdmin())
                    <a href="{{ route('devtalk.moderator.reports.index') }}"
                       class="px-3 py-1 text-sm rounded-md font-medium whitespace-nowrap transition-colors
                              {{ request()->routeIs('devtalk.moderator.*')
                                   ? 'bg-violet-800 text-white'
                                   : 'hover:bg-violet-700' }}">
                        🚩 Reports
                    </a>
                @endif

                @if(auth()->user()->isDtAdmin())
                    <a href="{{ route('devtalk.admin.dashboard') }}"
                       class="px-3 py-1 text-sm rounded-md font-medium whitespace-nowrap transition-colors
                              {{ request()->routeIs('devtalk.admin.*')
                                   ? 'bg-violet-800 text-white'
                                   : 'hover:bg-violet-700' }}">
                        ⚙️ Admin
                    </a>
                    <a href="{{ route('devtalk.admin.users.index') }}"
                       class="px-3 py-1 text-sm rounded-md font-medium whitespace-nowrap transition-colors
                              {{ request()->routeIs('devtalk.admin.users.*')
                                   ? 'bg-violet-800 text-white'
                                   : 'hover:bg-violet-700' }}">
                        👥 Users
                    </a>
                @endif
            @endauth
        </div>
    </header>
    @endif

    {{-- =================================================================
         MAIN CONTENT
         Each project's page content goes here via {{ $slot }}
         ================================================================= --}}
    <main>
        {{ $slot }}
    </main>

    @fluxScripts
</body>
</html>
