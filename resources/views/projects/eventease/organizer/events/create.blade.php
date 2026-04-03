{{-- resources/views/projects/eventease/organizer/events/create.blade.php --}}
{{--
EXAM STUDY NOTE — Create event form
=====================================
Standard Laravel form pattern:
  method="POST" + @csrf + validation errors under each field.

date input uses type="datetime-local" — the browser renders a date+time
picker. The controller validates 'after:now' so past dates are rejected.

price uses type="number" with step="0.01" so decimals are allowed.
The controller stores it as decimal(8,2) — exact, no float rounding.
--}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-2xl mx-auto px-4 py-8">

        <a href="{{ route('eventease.organizer.events.index') }}"
           class="text-sm text-emerald-600 dark:text-emerald-400 hover:underline mb-4 inline-block">
            ← Back to my events
        </a>

        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Create New Event</h1>

        @if($errors->any())
            <div class="mb-4 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-700 rounded-lg px-4 py-3 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('eventease.organizer.events.store') }}"
              class="bg-white dark:bg-zinc-800 rounded-xl shadow border border-gray-100 dark:border-zinc-700 p-6 space-y-5">
            @csrf

            {{-- Title --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="w-full border border-gray-300 dark:border-zinc-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-zinc-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            {{-- Location --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location <span class="text-red-500">*</span></label>
                <input type="text" name="location" value="{{ old('location') }}" required
                       class="w-full border border-gray-300 dark:border-zinc-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-zinc-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            {{-- Date & Time --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date & Time <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="date" value="{{ old('date') }}" required
                       class="w-full border border-gray-300 dark:border-zinc-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-zinc-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-400">
            </div>

            {{-- Capacity + Price --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Capacity <span class="text-red-500">*</span></label>
                    <input type="number" name="capacity" value="{{ old('capacity') }}" min="1" required
                           class="w-full border border-gray-300 dark:border-zinc-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-zinc-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Price (€) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" value="{{ old('price', '0') }}" min="0" step="0.01" required
                           class="w-full border border-gray-300 dark:border-zinc-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-zinc-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-400">
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                <textarea name="description" rows="4"
                          class="w-full border border-gray-300 dark:border-zinc-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-zinc-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-400">{{ old('description') }}</textarea>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('eventease.organizer.events.index') }}"
                   class="border border-gray-300 dark:border-zinc-600 px-5 py-2 rounded-lg text-sm hover:bg-gray-50 dark:hover:bg-zinc-700 transition">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-emerald-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-emerald-700 transition">
                    Create Event
                </button>
            </div>
        </form>
    </div>
</x-layouts::project-shell>
