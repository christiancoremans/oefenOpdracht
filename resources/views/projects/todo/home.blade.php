{{--
    EXAM STUDY NOTE — TodoApp Home (public landing page)
    =====================================================

    This is the PUBLIC home page for the TodoApp project.
    Anyone can see it — no login needed.

    Layout used: <x-layouts::project-shell>
    The layout gives us the dual-header (project switcher + project header).
    We pass project data to it via props (:currentProject, :projectName, etc.)

    Key patterns for the exam:
    • @auth / @else / @endauth   → show different content based on login state
    • route('project.dashboard', $currentProject)  → named route with parameter
    • route('login')             → named route (registered by Fortify)
--}}
<x-layouts::project-shell
    :title="$projectName"
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-4xl mx-auto px-4 py-16 text-center">

        {{-- Hero --}}
        <div class="mb-10">
            <span class="inline-block bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300
                          text-sm font-semibold px-4 py-1 rounded-full mb-4">
                Practice Project
            </span>
            <h2 class="text-4xl font-bold text-zinc-900 dark:text-white mb-4">
                TodoApp
            </h2>
            <p class="text-lg text-zinc-500 dark:text-zinc-400 max-w-xl mx-auto">
                A classic todo list application. Practice building CRUD operations,
                form handling, and database interactions in Laravel.
            </p>
        </div>

        {{-- Auth-aware call-to-action --}}
        @auth
            {{-- Logged in: go straight to the dashboard --}}
            <a
                href="{{ route('project.dashboard', $currentProject) }}"
                class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-lg text-lg font-semibold
                       hover:bg-indigo-700 transition-colors"
            >
                Go to TodoApp Dashboard →
            </a>
        @else
            {{-- Not logged in: prompt to log in or register --}}
            <div class="flex items-center justify-center gap-4 flex-wrap">
                <a
                    href="{{ route('login') }}"
                    class="inline-block border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300
                           px-8 py-3 rounded-lg text-lg font-semibold hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors"
                >
                    Log in to start
                </a>
                <a
                    href="{{ route('register') }}"
                    class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-lg text-lg font-semibold
                           hover:bg-indigo-700 transition-colors"
                >
                    Create account
                </a>
            </div>
            <p class="mt-4 text-sm text-zinc-400">
                Login is shared across all practice projects.
            </p>
        @endauth

        {{-- What you'll practice --}}
        <div class="mt-16 grid sm:grid-cols-3 gap-6 text-left">
            @foreach([
                ['icon' => '📋', 'title' => 'List todos',    'desc' => 'Display all items from the database using Eloquent'],
                ['icon' => '✏️',  'title' => 'Create & edit', 'desc' => 'Forms with validation, CSRF, and old() input'],
                ['icon' => '🗑️', 'title' => 'Delete items',  'desc' => 'DELETE forms, route–model binding, redirects'],
            ] as $feature)
                <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700
                             rounded-xl p-6 shadow-sm">
                    <div class="text-3xl mb-3">{{ $feature['icon'] }}</div>
                    <h3 class="font-semibold text-zinc-900 dark:text-white mb-1">{{ $feature['title'] }}</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $feature['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts::project-shell>
