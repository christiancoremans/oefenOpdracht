<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
<div class="max-w-2xl mx-auto py-10 px-4">

    <a href="{{ route('music.workshops.show', $workshop) }}" class="text-sm text-purple-600 hover:underline mb-6 inline-block">
        ← Back to workshop
    </a>

    <h1 class="text-2xl font-bold text-purple-700 mb-6">Edit: {{ $workshop->title }}</h1>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-300 text-red-800 rounded-lg">
            <strong>Please fix the following errors:</strong>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('music.workshops.update', $workshop) }}" method="POST" enctype="multipart/form-data"
          class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-5">
        @csrf
        @method('PUT')

        {{-- Title --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Workshop Title *</label>
            <input type="text" name="title" value="{{ old('title', $workshop->title) }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400 @error('title') border-red-400 @enderror">
            @error('title')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
            <textarea name="description" rows="4"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400 @error('description') border-red-400 @enderror">{{ old('description', $workshop->description) }}</textarea>
            @error('description')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Room + Capacity --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Room *</label>
                <input type="text" name="room" value="{{ old('room', $workshop->room) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400 @error('room') border-red-400 @enderror">
                @error('room')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Capacity *</label>
                <input type="number" name="capacity" value="{{ old('capacity', $workshop->capacity) }}" min="1"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400 @error('capacity') border-red-400 @enderror">
                @error('capacity')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Start + End time --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Time *</label>
                <input type="datetime-local" name="start_time"
                       value="{{ old('start_time', $workshop->start_time->format('Y-m-d\TH:i')) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400 @error('start_time') border-red-400 @enderror">
                @error('start_time')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Time *</label>
                <input type="datetime-local" name="end_time"
                       value="{{ old('end_time', $workshop->end_time->format('Y-m-d\TH:i')) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400 @error('end_time') border-red-400 @enderror">
                @error('end_time')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Instructors --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Instructors</label>
            <p class="text-xs text-gray-500 mb-2">Hold Ctrl (Windows) or Cmd (Mac) to select multiple.</p>
            @php $selectedIds = old('instructor_ids', $workshop->instructors->pluck('id')->toArray()); @endphp
            <select name="instructor_ids[]" multiple
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-400 h-32 @error('instructor_ids') border-red-400 @enderror">
                @foreach($instructors as $instructor)
                    <option value="{{ $instructor->id }}"
                        {{ in_array($instructor->id, $selectedIds) ? 'selected' : '' }}>
                        {{ $instructor->name }} — {{ $instructor->specialization }}
                    </option>
                @endforeach
            </select>
            @error('instructor_ids')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Picture --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Workshop Image</label>
            @if($workshop->picture)
                <div class="mb-2">
                    <img src="{{ Storage::url($workshop->picture) }}" alt="Current image"
                         class="h-24 w-auto rounded-lg object-cover border">
                    <p class="text-xs text-gray-500 mt-1">Upload a new image to replace the current one.</p>
                </div>
            @endif
            <input type="file" name="picture" accept="image/*"
                   class="w-full text-sm text-gray-600 @error('picture') text-red-500 @enderror">
            @error('picture')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-2">
            <button type="submit"
                    class="w-full bg-purple-600 text-white py-2.5 rounded-lg font-semibold hover:bg-purple-700 transition">
                Save Changes
            </button>
        </div>
    </form>
</div>
</x-layouts::project-shell>
