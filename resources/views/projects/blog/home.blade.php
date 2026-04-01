{{--
    EXAM STUDY NOTE — BlogApp Home (public landing page)
    =====================================================
    Same structure as TodoApp home — just different content.
    This shows how you can reuse the project-shell layout for any project.
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
            <span class="inline-block bg-emerald-100 dark:bg-emerald-900 text-emerald-700 dark:text-emerald-300
                          text-sm font-semibold px-4 py-1 rounded-full mb-4">
                Practice Project
            </span>
            <h2 class="text-4xl font-bold text-zinc-900 dark:text-white mb-4">
                BlogApp
            </h2>
            <p class="text-lg text-zinc-500 dark:text-zinc-400 max-w-xl mx-auto">
                A blog platform. Practice relationships, pagination, policies,
                and more advanced Laravel features.
            </p>
        </div>

        {{-- Auth-aware call-to-action --}}
        @auth
            <a
                href="{{ route('project.dashboard', $currentProject) }}"
                class="inline-block bg-emerald-600 text-white px-8 py-3 rounded-lg text-lg font-semibold
                       hover:bg-emerald-700 transition-colors"
            >
                Go to BlogApp Dashboard →
            </a>
        @else
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
                    class="inline-block bg-emerald-600 text-white px-8 py-3 rounded-lg text-lg font-semibold
                           hover:bg-emerald-700 transition-colors"
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
                ['icon' => '📝', 'title' => 'Posts CRUD',      'desc' => 'Create, read, update, delete blog posts'],
                ['icon' => '💬', 'title' => 'Comments',         'desc' => 'One-to-many relationships with Eloquent'],
                ['icon' => '🔒', 'title' => 'Authorization',    'desc' => 'Policies so only the author can edit/delete'],
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
