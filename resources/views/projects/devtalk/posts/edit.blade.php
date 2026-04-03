{{-- resources/views/projects/devtalk/posts/edit.blade.php --}}
{{--
EXAM STUDY NOTE — Post (reply) edit form
=========================================
Very simple: one textarea + @method('PUT').
Redirects back to the thread after save (see PostController@update).
--}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-2xl mx-auto px-4 py-10">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('devtalk.threads.show', $post->thread_id) }}"
               class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-sm">← Back to thread</a>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Edit Reply</h1>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
            <form action="{{ route('devtalk.posts.update', $post) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reply body</label>
                    <textarea name="body" required rows="6"
                              class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-violet-400">{{ old('body', $post->body) }}</textarea>
                    @error('body')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                            class="bg-violet-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-violet-700 transition">
                        Save
                    </button>
                    <a href="{{ route('devtalk.threads.show', $post->thread_id) }}"
                       class="border border-gray-300 dark:border-gray-600 px-6 py-2 rounded-lg text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts::project-shell>
