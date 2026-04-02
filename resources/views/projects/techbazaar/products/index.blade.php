{{-- resources/views/projects/techbazaar/products/index.blade.php --}}
{{--
    EXAM STUDY NOTE — Paginated product catalog with filter
    $products->links() renders the pagination buttons.
    $products->withQueryString() preserves ?search=&category= on page clicks.
--}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-6xl mx-auto px-4 py-10">

        {{-- Filter bar --}}
        <form method="GET" action="{{ route('techbazaar.products.index') }}"
              class="flex flex-col sm:flex-row gap-3 mb-8">
            <input
                type="text" name="search"
                value="{{ request('search') }}"
                placeholder="Search products..."
                class="flex-1 border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2
                       bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white
                       focus:outline-none focus:ring-2 focus:ring-amber-500"
            />
            <select name="category"
                    class="border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2
                           bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white
                           focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="">All categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit"
                    class="bg-amber-500 text-white px-6 py-2 rounded-lg hover:bg-amber-600 font-medium">
                Filter
            </button>
            @if(request()->hasAny(['search','category']))
                <a href="{{ route('techbazaar.products.index') }}"
                   class="border border-zinc-300 dark:border-zinc-600 px-4 py-2 rounded-lg
                          text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                    Clear
                </a>
            @endif
        </form>

        {{-- Product grid --}}
        @forelse($products as $product)
            @if($loop->first)
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
            @endif

            <a href="{{ route('techbazaar.products.show', $product) }}"
               class="group bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700
                       rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">

                {{-- Product image placeholder (or real image) --}}
                <div class="aspect-video bg-zinc-100 dark:bg-zinc-700 overflow-hidden">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-4xl text-zinc-300">
                            📦
                        </div>
                    @endif
                </div>

                <div class="p-4">
                    <span class="text-xs text-amber-600 dark:text-amber-400 font-medium">
                        {{ $product->category->name }}
                    </span>
                    <h3 class="font-semibold text-zinc-900 dark:text-white mt-1 truncate">
                        {{ $product->name }}
                    </h3>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-lg font-bold text-amber-600 dark:text-amber-400">
                            €{{ number_format($product->price, 2) }}
                        </span>
                        <span class="text-xs text-zinc-400">
                            Stock: {{ $product->stock }}
                        </span>
                    </div>
                </div>
            </a>

            @if($loop->last)
                </div>
            @endif
        @empty
            <div class="bg-white dark:bg-zinc-800 border border-dashed border-zinc-300 dark:border-zinc-600
                         rounded-xl p-16 text-center">
                <p class="text-2xl mb-3">🔍</p>
                <p class="text-zinc-500 dark:text-zinc-400">No products found.</p>
                @if(request('search'))
                    <p class="text-sm text-zinc-400 mt-1">Try a different search term.</p>
                @endif
            </div>
        @endforelse

        {{-- Pagination --}}
        {{ $products->links() }}
    </div>
</x-layouts::project-shell>
