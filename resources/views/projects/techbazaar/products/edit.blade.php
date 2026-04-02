{{-- resources/views/projects/techbazaar/products/edit.blade.php --}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-2xl mx-auto px-4 py-10">

        <div class="flex items-center gap-3 mb-8">
            <a href="{{ route('techbazaar.seller.products.index') }}"
               class="text-sm text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300">← My Products</a>
            <span class="text-zinc-300">/</span>
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Edit Product</h2>
        </div>

        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl p-8 shadow-sm">
            {{--
                EXAM STUDY NOTE — PUT/PATCH for update
                HTML forms only support GET/POST. Use @method('PUT') to send a PUT
                request. Laravel matches this to the Route::resource 'update' method.
                Use old() with the model value as fallback: old('name', $product->name)
            --}}
            <form method="POST"
                  action="{{ route('techbazaar.seller.products.update', $product) }}"
                  enctype="multipart/form-data"
                  class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                        Category
                    </label>
                    <select name="category_id"
                            class="w-full border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2
                                   bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white
                                   focus:outline-none focus:ring-2 focus:ring-amber-500">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                @selected(old('category_id', $product->category_id) == $cat->id)>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                        Product Name
                    </label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}"
                           class="w-full border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2
                                  bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white
                                  focus:outline-none focus:ring-2 focus:ring-amber-500">
                    @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                        Description
                    </label>
                    <textarea name="description" rows="4"
                              class="w-full border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2
                                     bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white
                                     focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('description', $product->description) }}</textarea>
                    @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                            Price (€)
                        </label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}"
                               step="0.01" min="0"
                               class="w-full border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2
                                      bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-amber-500">
                        @error('price') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                            Stock
                        </label>
                        <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0"
                               class="w-full border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2
                                      bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-amber-500">
                        @error('stock') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                        Replace Image (optional, max 2MB)
                    </label>
                    @if($product->image)
                        <p class="text-xs text-zinc-400 mb-1">Current: {{ $product->image }}</p>
                    @endif
                    <input type="file" name="image" accept="image/*"
                           class="w-full border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2
                                  bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white">
                    @error('image') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="bg-amber-500 text-white px-8 py-2 rounded-lg hover:bg-amber-600 font-medium">
                        Save Changes
                    </button>
                    <a href="{{ route('techbazaar.seller.products.index') }}"
                       class="border border-zinc-300 dark:border-zinc-600 px-6 py-2 rounded-lg
                              text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts::project-shell>
