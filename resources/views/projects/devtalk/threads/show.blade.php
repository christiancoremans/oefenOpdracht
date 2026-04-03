{{-- resources/views/projects/devtalk/threads/show.blade.php --}}
{{--
EXAM STUDY NOTE — Thread show page
===================================
Data received:
  $thread   — with posts (paginated), each post has user + votes eager loaded
  $userVotes — array: ['post_id' => vote_value] for the current user.
               Built in ThreadController@show via:
               $userVotes = auth()->check()
                   ? Vote::where('user_id', auth()->id())
                         ->whereIn('post_id', $posts->pluck('id'))
                         ->pluck('value', 'post_id')->toArray()
                   : [];

Vote forms:
  POST devtalk.votes.store with post_id + value (1 or -1)
  The current user's active vote gets a highlighted border so they see
  which direction they already clicked. Clicking again removes the vote.

Report form:
  POST devtalk.reports.store with post_id + reason (hidden modal per post)
  Using Alpine.js x-data / x-show for inline toggle without a library.

Locked thread:
  If $thread->is_locked is true AND user is not mod/admin → hide reply form.
--}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-4xl mx-auto px-4 py-8">

        {{-- ── Breadcrumb ── --}}
        <nav class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            <a href="{{ route('devtalk.home') }}" class="hover:underline">Threads</a>
            <span class="mx-1">›</span>
            <span>{{ $thread->category->name ?? 'Uncategorized' }}</span>
        </nav>

        {{-- ── Thread header ── --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 mb-6 shadow-sm">
            <div class="flex items-start justify-between gap-3 flex-wrap">
                <div>
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        <span class="text-xs bg-violet-100 dark:bg-violet-900 text-violet-700 dark:text-violet-300 px-2 py-0.5 rounded-full font-medium">
                            {{ $thread->category->name ?? '—' }}
                        </span>
                        @if($thread->is_locked)
                            <span class="text-xs bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300 px-2 py-0.5 rounded-full font-medium">
                                🔒 Locked
                            </span>
                        @endif
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $thread->title }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        by <span class="font-medium">{{ $thread->user->name ?? 'Unknown' }}</span>
                        · {{ $thread->created_at->diffForHumans() }}
                        · {{ number_format($thread->views) }} views
                    </p>
                </div>
                @if(auth()->check() && (auth()->id() === $thread->user_id || auth()->user()->isDtModerator() || auth()->user()->isDtAdmin()))
                    <div class="flex gap-2 shrink-0">
                        <a href="{{ route('devtalk.threads.edit', $thread) }}"
                           class="text-sm text-violet-600 dark:text-violet-400 hover:underline">Edit</a>
                        <form action="{{ route('devtalk.threads.destroy', $thread) }}" method="POST"
                              onsubmit="return confirm('Delete this thread?')">
                            @csrf @method('DELETE')
                            <button class="text-sm text-red-600 dark:text-red-400 hover:underline">Delete</button>
                        </form>
                    </div>
                @endif
            </div>
            <div class="prose dark:prose-invert max-w-none mt-4 text-gray-700 dark:text-gray-300">
                {!! nl2br(e($thread->body)) !!}
            </div>
        </div>

        {{-- ── Flash message ── --}}
        @if(session('success'))
            <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- ── Replies ── --}}
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-3">
            {{ $posts->total() }} {{ Str::plural('Reply', $posts->total()) }}
        </h2>

        <div class="space-y-4 mb-6">
            @foreach ($posts as $post)
                <div id="post-{{ $post->id }}"
                     class="bg-white dark:bg-gray-800 border rounded-xl p-5 shadow-sm
                            {{ $post->is_flagged ? 'border-amber-400 dark:border-amber-600' : 'border-gray-200 dark:border-gray-700' }}">
                    <div class="flex gap-4">

                        {{-- Vote column --}}
                        <div class="flex flex-col items-center gap-1 shrink-0 w-12">
                            @auth
                                <form action="{{ route('devtalk.votes.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                                    <input type="hidden" name="value" value="1">
                                    <button type="submit"
                                            class="w-8 h-8 rounded-full border-2 flex items-center justify-center text-sm transition
                                                   {{ ($userVotes[$post->id] ?? null) === 1
                                                      ? 'border-green-500 bg-green-100 dark:bg-green-900 text-green-700'
                                                      : 'border-gray-300 dark:border-gray-600 hover:border-green-400 text-gray-400 hover:text-green-600' }}">
                                        ▲
                                    </button>
                                </form>
                            @endauth
                            <span class="text-sm font-bold {{ $post->voteScore() >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $post->voteScore() }}
                            </span>
                            @auth
                                <form action="{{ route('devtalk.votes.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                                    <input type="hidden" name="value" value="-1">
                                    <button type="submit"
                                            class="w-8 h-8 rounded-full border-2 flex items-center justify-center text-sm transition
                                                   {{ ($userVotes[$post->id] ?? null) === -1
                                                      ? 'border-red-500 bg-red-100 dark:bg-red-900 text-red-700'
                                                      : 'border-gray-300 dark:border-gray-600 hover:border-red-400 text-gray-400 hover:text-red-600' }}">
                                        ▼
                                    </button>
                                </form>
                            @endauth
                        </div>

                        {{-- Post body + meta --}}
                        <div class="flex-1 min-w-0">
                            <div class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $post->user->name ?? 'Unknown' }}</span>
                                · {{ $post->created_at->diffForHumans() }}
                                @if($post->is_flagged)
                                    <span class="ml-2 text-xs bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300 px-2 py-0.5 rounded-full">
                                        🚩 Flagged
                                    </span>
                                @endif
                            </div>
                            <div class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">
                                {!! nl2br(e($post->body)) !!}
                            </div>

                            {{-- Post actions --}}
                            @auth
                                <div class="flex flex-wrap gap-3 mt-3 text-xs">
                                    @if(auth()->id() === $post->user_id || auth()->user()->isDtModerator() || auth()->user()->isDtAdmin())
                                        <a href="{{ route('devtalk.posts.edit', $post) }}"
                                           class="text-violet-600 dark:text-violet-400 hover:underline">Edit</a>
                                        <form action="{{ route('devtalk.posts.destroy', $post) }}" method="POST"
                                              onsubmit="return confirm('Delete this reply?')">
                                            @csrf @method('DELETE')
                                            <button class="text-red-600 dark:text-red-400 hover:underline">Delete</button>
                                        </form>
                                    @endif

                                    {{-- Report toggle --}}
                                    @if(auth()->id() !== $post->user_id)
                                        <div x-data="{ open: false }">
                                            <button @click="open = !open"
                                                    class="text-gray-400 hover:text-amber-600 dark:hover:text-amber-400 transition">
                                                🚩 Report
                                            </button>
                                            <div x-show="open" x-cloak class="mt-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-3">
                                                <form action="{{ route('devtalk.reports.store') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                                                    <textarea name="reason" required placeholder="Describe the issue…"
                                                              class="w-full border border-gray-300 dark:border-gray-600 rounded-lg p-2 text-sm bg-white dark:bg-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-amber-400"
                                                              rows="2"></textarea>
                                                    <button type="submit"
                                                            class="mt-2 bg-amber-500 text-white px-3 py-1 rounded text-xs hover:bg-amber-600 transition">
                                                        Submit Report
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Posts pagination --}}
        {{ $posts->links() }}

        {{-- ── Reply form ── --}}
        @auth
            @if(!$thread->is_locked || auth()->user()->isDtModerator() || auth()->user()->isDtAdmin())
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 mt-6 shadow-sm">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-3">Post a Reply</h3>
                    <form action="{{ route('devtalk.posts.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="thread_id" value="{{ $thread->id }}">
                        <textarea name="body" required rows="4"
                                  placeholder="Write your reply…"
                                  class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 text-sm bg-white dark:bg-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-violet-400 mb-3">{{ old('body') }}</textarea>
                        @error('body')
                            <p class="text-red-500 text-xs mb-2">{{ $message }}</p>
                        @enderror
                        <button type="submit"
                                class="bg-violet-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-violet-700 transition">
                            Post Reply
                        </button>
                    </form>
                </div>
            @else
                <div class="text-center text-gray-500 dark:text-gray-400 py-6">
                    🔒 This thread is locked. No new replies allowed.
                </div>
            @endif
        @else
            <div class="text-center text-gray-500 dark:text-gray-400 py-6">
                <a href="{{ route('login') }}" class="text-violet-600 dark:text-violet-400 hover:underline">Log in</a> to reply.
            </div>
        @endauth

    </div>
</x-layouts::project-shell>
