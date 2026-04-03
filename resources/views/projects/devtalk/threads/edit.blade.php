{{-- resources/views/projects/devtalk/threads/edit.blade.php --}}
{{--
EXAM STUDY NOTE — Thread edit form
====================================
@method('PUT') — HTML forms only support GET/POST. Laravel's method
spoofing adds a hidden _method field so the router treats this as PUT.
The route devtalk.threads.update expects a PUT request.
--}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-2xl mx-auto px-4 py-10">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('devtalk.threads.show', $thread) }}"
               class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-sm">← Back</a>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Edit Thread</h1>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">
            <form action="{{ route('devtalk.threads.update', $thread) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Category --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                    <select name="category_id" required
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-violet-400">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                    {{ old('category_id', $thread->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Title --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title</label>
                    <input type="text" name="title"
                           value="{{ old('title', $thread->title) }}"
                           required maxlength="255"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-violet-400">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Body --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Body</label>
                    <textarea name="body" required rows="8"
                              class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-violet-400">{{ old('body', $thread->body) }}</textarea>
                    @error('body')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Lock toggle (mods/admins only) --}}
                @if(auth()->user()->isDtModerator() || auth()->user()->isDtAdmin())
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_locked" id="is_locked" value="1"
                               {{ old('is_locked', $thread->is_locked) ? 'checked' : '' }}
                               class="rounded border-gray-300 dark:border-gray-600 text-violet-600 focus:ring-violet-400">
                        <label for="is_locked" class="text-sm text-gray-700 dark:text-gray-300">
                            Lock this thread (no new replies)
                        </label>
                    </div>
                @endif

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="bg-violet-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-violet-700 transition">
                        Save Changes
                    </button>
                    <a href="{{ route('devtalk.threads.show', $thread) }}"
                       class="border border-gray-300 dark:border-gray-600 px-6 py-2 rounded-lg text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts::project-shell>
