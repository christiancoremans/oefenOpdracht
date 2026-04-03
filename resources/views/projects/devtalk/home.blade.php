{{-- resources/views/projects/devtalk/home.blade.php --}}
{{--
EXAM STUDY NOTE — DevTalk home (thread index)
==============================================
Layout:
  - Left (8/12): paginated thread list with search bar
  - Right (4/12): category filter sidebar

Search: GET ?search=... is handled by ThreadController@index → scopeSearch().
The input keeps its value via request('search') so the field stays filled.

$threads has ->withCount('posts') so each thread has posts_count available.
$thread->user gives the author (eager-loaded). Same for $thread->category.

Locked threads show a 🔒 badge — users can still read but not reply.
--}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-6">

            {{-- ── Main column ──────────────────────────────────── --}}
            <div class="flex-1 min-w-0">

                {{-- Header + New Thread button --}}
                <div class="flex items-center justify-between mb-4">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                        Latest Threads
                    </h1>
                    @auth
                        <a href="{{ route('devtalk.threads.create') }}"
                           class="bg-violet-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-violet-700 transition">
                            + New Thread
                        </a>
                    @endauth
                </div>

                {{-- Search bar --}}
                <form method="GET" action="{{ route('devtalk.home') }}" class="mb-5">
                    <div class="flex gap-2">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search threads…"
                            class="flex-1 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-sm bg-white dark:bg-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-violet-400"
                        />
                        <button type="submit"
                                class="bg-violet-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-violet-700 transition">
                            Search
                        </button>
                        @if(request('search'))
                            <a href="{{ route('devtalk.home') }}"
                               class="border border-gray-300 dark:border-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>

                {{-- Thread list --}}
                <div class="space-y-3">
                    @forelse ($threads as $thread)
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm hover:shadow-md transition">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <span class="text-xs font-medium bg-violet-100 dark:bg-violet-900 text-violet-700 dark:text-violet-300 px-2 py-0.5 rounded-full">
                                            {{ $thread->category->name ?? '—' }}
                                        </span>
                                        @if($thread->is_locked)
                                            <span class="text-xs text-amber-600 dark:text-amber-400 font-medium">🔒 Locked</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('devtalk.threads.show', $thread) }}"
                                       class="text-lg font-semibold text-gray-900 dark:text-gray-100 hover:text-violet-700 dark:hover:text-violet-400 transition line-clamp-2">
                                        {{ $thread->title }}
                                    </a>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        by <span class="font-medium">{{ $thread->user->name ?? 'Unknown' }}</span>
                                        · {{ $thread->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="shrink-0 text-right text-sm text-gray-500 dark:text-gray-400">
                                    <div class="font-semibold text-gray-700 dark:text-gray-200">{{ $thread->posts_count }}</div>
                                    <div>replies</div>
                                    <div class="mt-1 text-xs">{{ number_format($thread->views) }} views</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 py-8 text-center">No threads found.</p>
                    @endforelse
                </div>

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $threads->withQueryString()->links() }}
                </div>
            </div>

            {{-- ── Sidebar ───────────────────────────────────────── --}}
            <aside class="w-full lg:w-64 shrink-0">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100 mb-3">Browse by Category</h2>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('devtalk.home') }}"
                               class="block px-3 py-1.5 rounded-lg text-sm transition
                                      {{ !request('category') ? 'bg-violet-100 dark:bg-violet-900 text-violet-700 dark:text-violet-300 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                All Categories
                            </a>
                        </li>
                        @foreach ($categories as $category)
                            <li>
                                <a href="{{ route('devtalk.home', ['category' => $category->slug]) }}"
                                   class="block px-3 py-1.5 rounded-lg text-sm transition
                                          {{ request('category') === $category->slug ? 'bg-violet-100 dark:bg-violet-900 text-violet-700 dark:text-violet-300 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                    {{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>

        </div>
    </div>
</x-layouts::project-shell>
