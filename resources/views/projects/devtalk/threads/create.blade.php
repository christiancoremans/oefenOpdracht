{{-- resources/views/projects/devtalk/threads/create.blade.php --}}
{{--
EXAM STUDY NOTE — Thread create form
======================================
Fields: category_id (select), title, body.
$categories is a Collection from Category::all() ordered by name.
old() helper: repopulates on validation failure so the user doesn't
lose their draft.
--}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-2xl mx-auto px-4 py-10">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('devtalk.home') }}" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-sm">← Back</a>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Start a New Thread</h1>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm">

                @if($errors->any())
                    <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 px-4 py-3 rounded-lg text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            <form action="{{ route('devtalk.threads.store') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Category --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                    <select name="category_id" required
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-violet-400">
                        <option value="">— Select a category —</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                    <input type="text" name="title" value="{{ old('title') }}" required
                           maxlength="255"
                           placeholder="What's your question or topic?"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-violet-400">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Body --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Body</label>
                    <textarea name="body" required rows="8"
                              placeholder="Describe your question or discussion topic in detail…"
                              class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-800 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-violet-400">{{ old('body') }}</textarea>
                    @error('body')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="bg-violet-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-violet-700 transition">
                        Post Thread
                    </button>
                    <a href="{{ route('devtalk.home') }}"
                       class="border border-gray-300 dark:border-gray-600 px-6 py-2 rounded-lg text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts::project-shell>
