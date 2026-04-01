{{--
    EXAM STUDY NOTE — BlogApp Dashboard (protected)
    ================================================
    This is the protected area for the BlogApp.
    Build your PostController and Post model to bring this to life.
--}}
<x-layouts::project-shell
    :title="$projectName . ' — Dashboard'"
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-4xl mx-auto px-4 py-10">

        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">My Blog Posts</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                    Logged in as <strong>{{ auth()->user()->name }}</strong>
                </p>
            </div>
            {{-- EXAM NOTE: link to create form --}}
            {{-- <a href="{{ route('posts.create') }}" class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm">
                + New Post
            </a> --}}
        </div>

        {{-- ============================================================
             EXAM STUDY NOTE — Pagination
             ============================================================
             When you have many records, use ->paginate(10) in your query.
             Then call {{ $posts->links() }} to render the pagination UI.
             ============================================================ --}}

        {{-- Replace with:  $posts = auth()->user()->posts()->latest()->paginate(10); --}}
        @forelse([] as $post)
            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700
                         rounded-xl p-5 mb-4 shadow-sm">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="font-semibold text-zinc-900 dark:text-white">
                            {{-- <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a> --}}
                            {{ $post->title }}
                        </h3>
                        <p class="text-sm text-zinc-500 mt-1">
                            {{ $post->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <div class="flex gap-2">
                        {{-- <a href="{{ route('posts.edit', $post) }}" class="text-sm text-emerald-600">Edit</a> --}}
                        {{-- <form method="POST" action="{{ route('posts.destroy', $post) }}">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-sm text-red-500">Delete</button>
                        </form> --}}
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-zinc-800 border border-dashed border-zinc-300 dark:border-zinc-600
                         rounded-xl p-12 text-center">
                <p class="text-zinc-400 dark:text-zinc-500 text-lg mb-2">No posts yet</p>
                <p class="text-zinc-400 dark:text-zinc-500 text-sm">
                    Build the PostController and Post model to add real data here.
                </p>
            </div>
        @endforelse

        {{-- ============================================================
             EXAM STUDY NOTE — Relationship example
             ============================================================
             Post belongs to a User. User has many Posts.

             In Post model:
               public function user(): BelongsTo {
                   return $this->belongsTo(User::class);
               }

             In User model:
               public function posts(): HasMany {
                   return $this->hasMany(Post::class);
               }

             In migration:
               $table->foreignId('user_id')->constrained()->cascadeOnDelete();
             ============================================================ --}}

        {{-- Write post form --}}
        <div class="mt-8 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700
                     rounded-xl p-6 shadow-sm">
            <h3 class="font-semibold text-zinc-800 dark:text-zinc-200 mb-4">Write a new post</h3>
            {{-- Uncomment after creating your PostController --}}
            {{-- <form method="POST" action="{{ route('posts.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Title</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="w-full border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2
                                  bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white
                                  focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                    @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Content</label>
                    <textarea name="content" rows="5"
                              class="w-full border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2
                                     bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white
                                     focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('content') }}</textarea>
                    @error('content') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit"
                        class="bg-emerald-600 text-white px-6 py-2 rounded-lg hover:bg-emerald-700">
                    Publish
                </button>
            </form> --}}
            <p class="text-sm text-zinc-400">
                👆 Uncomment the form above after creating your PostController and routes.
            </p>
        </div>
    </div>
</x-layouts::project-shell>
