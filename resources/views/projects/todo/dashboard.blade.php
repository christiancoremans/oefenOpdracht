{{--
    EXAM STUDY NOTE — TodoApp Dashboard (protected)
    ================================================

    This page requires authentication.
    The 'auth' middleware in web.php redirects to /login if not logged in.

    In an exam, this is where your main app content lives:
    - List all todos with @forelse
    - Form to create a new todo
    - Edit / Delete buttons per item

    For now this is a STARTER TEMPLATE — add your Todo model and
    controller actions as you build the project out.
--}}
<x-layouts::project-shell
    :title="$projectName . ' — Dashboard'"
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-3xl mx-auto px-4 py-10">

        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">My Todos</h2>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                    Logged in as <strong>{{ auth()->user()->name }}</strong>
                </p>
            </div>
            {{-- EXAM NOTE: link to create form --}}
            {{-- <a href="{{ route('todos.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm">
                + New Todo
            </a> --}}
        </div>

        {{-- ============================================================
             EXAM STUDY NOTE — @forelse pattern
             ============================================================
             Use @forelse instead of @foreach when the collection might be empty.
             @forelse($items as $item) ... @empty ... show empty state ... @endforelse
             ============================================================ --}}

        {{-- Placeholder — replace $todos with your actual Eloquent query --}}
        {{-- Example: $todos = auth()->user()->todos()->latest()->get(); --}}
        @forelse([] as $todo)
            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700
                         rounded-lg p-4 mb-3 flex items-center justify-between shadow-sm">
                <span class="text-zinc-800 dark:text-zinc-200">{{ $todo->title }}</span>
                <div class="flex gap-2">
                    {{-- Edit button --}}
                    {{-- <a href="{{ route('todos.edit', $todo) }}" class="text-sm text-indigo-600">Edit</a> --}}
                    {{-- Delete form --}}
                    {{-- <form method="POST" action="{{ route('todos.destroy', $todo) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600">Delete</button>
                    </form> --}}
                </div>
            </div>
        @empty
            {{-- Empty state --}}
            <div class="bg-white dark:bg-zinc-800 border border-dashed border-zinc-300 dark:border-zinc-600
                         rounded-xl p-12 text-center">
                <p class="text-zinc-400 dark:text-zinc-500 text-lg mb-2">No todos yet</p>
                <p class="text-zinc-400 dark:text-zinc-500 text-sm">
                    Build the TodoController and Todo model to add real data here.
                </p>
            </div>
        @endforelse

        {{-- ============================================================
             EXAM STUDY NOTE — Quick-add form template
             ============================================================
             @csrf           → required on every POST/PUT/DELETE form
             @error('field') → shows validation error for that field
             old('field')    → re-fills the input if validation fails
             ============================================================ --}}
        <div class="mt-8 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700
                     rounded-xl p-6 shadow-sm">
            <h3 class="font-semibold text-zinc-800 dark:text-zinc-200 mb-4">Add a new todo</h3>
            {{-- Uncomment and wire up when you have a TodoController --}}
            {{-- <form method="POST" action="{{ route('todos.store') }}" class="flex gap-3">
                @csrf
                <input
                    type="text"
                    name="title"
                    value="{{ old('title') }}"
                    placeholder="What needs to be done?"
                    class="flex-1 border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2
                           bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white
                           focus:outline-none focus:ring-2 focus:ring-indigo-500"
                />
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <button type="submit"
                        class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700">
                    Add
                </button>
            </form> --}}
            <p class="text-sm text-zinc-400">
                👆 Uncomment the form above after creating your TodoController and routes.
            </p>
        </div>
    </div>
</x-layouts::project-shell>
