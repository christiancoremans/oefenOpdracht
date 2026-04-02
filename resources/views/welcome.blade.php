{{--
    EXAM STUDY NOTE â€” Main Homepage (welcome.blade.php)
    ====================================================
    This is the app's entry point. It uses the project-shell layout
    which gives us:
      - Header 1: project switcher (top bar â€” all project tabs)
      - Header 2: app name + login/register

    When no project is active, $currentProject is null so nothing is
    highlighted in the switcher. Passing null is fine â€” the layout handles it.
--}}
<x-layouts::project-shell
    title="Welcome"
    :currentProject="null"
    :projectName="config('app.name')"
    projectDescription="Choose a practice project below"
>
    <div class="max-w-5xl mx-auto px-4 py-16">

        {{-- Page heading --}}
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-zinc-900 dark:text-white mb-4">
                Practice Projects
            </h2>
            <p class="text-lg text-zinc-500 dark:text-zinc-400">
                Select a project from the tabs above, or click a card below to get started.
            </p>
            @auth
                <p class="mt-2 text-sm text-zinc-400">
                    Logged in as <strong>{{ auth()->user()->name }}</strong>
                </p>
            @endauth
        </div>

        {{-- Project cards â€” auto-generated from config/projects.php --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach(config('projects') as $slug => $project)
                <a
                    href="{{ route('project.home', $slug) }}"
                    class="group bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700
                           rounded-2xl p-8 shadow-sm hover:shadow-md hover:border-indigo-400
                           dark:hover:border-indigo-500 transition-all"
                >
                    <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                        {{ $project['name'] }}
                    </h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        {{ $project['description'] }}
                    </p>
                    <div class="mt-6 text-indigo-600 dark:text-indigo-400 text-sm font-medium">
                        Open project â†’
                    </div>
                </a>
            @endforeach

            {{-- "Add project" hint card --}}
            <div class="bg-zinc-50 dark:bg-zinc-800/50 border border-dashed border-zinc-300 dark:border-zinc-600
                         rounded-2xl p-8 flex flex-col items-center justify-center text-center">
                <p class="text-zinc-400 dark:text-zinc-500 text-sm font-medium mb-2">Add a project</p>
                <p class="text-zinc-400 dark:text-zinc-500 text-xs">
                    Edit <code class="bg-zinc-200 dark:bg-zinc-700 px-1 rounded">config/projects.php</code>
                    and create the matching views folder.
                </p>
            </div>
        </div>

        {{-- Quick start guide --}}
        <div class="mt-16 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700
                     rounded-2xl p-8 shadow-sm">
            <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-4">
                How to add a new exam project
            </h3>
            <ol class="space-y-3 text-sm text-zinc-600 dark:text-zinc-400">
                <li class="flex gap-3">
                    <span class="bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300
                                  font-bold text-xs w-6 h-6 rounded-full flex items-center justify-center shrink-0">1</span>
                    Add an entry to <code class="bg-zinc-100 dark:bg-zinc-700 px-1.5 py-0.5 rounded text-xs">config/projects.php</code>
                    with a slug, name, and description.
                </li>
                <li class="flex gap-3">
                    <span class="bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300
                                  font-bold text-xs w-6 h-6 rounded-full flex items-center justify-center shrink-0">2</span>
                    Create <code class="bg-zinc-100 dark:bg-zinc-700 px-1.5 py-0.5 rounded text-xs">resources/views/projects/{slug}/home.blade.php</code>
                    and <code class="bg-zinc-100 dark:bg-zinc-700 px-1.5 py-0.5 rounded text-xs">dashboard.blade.php</code>
                </li>
                <li class="flex gap-3">
                    <span class="bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300
                                  font-bold text-xs w-6 h-6 rounded-full flex items-center justify-center shrink-0">3</span>
                    Done! The tab appears automatically. Build your controllers, models, and migrations inside the project.
                </li>
            </ol>
        </div>
    </div>
</x-layouts::project-shell>
