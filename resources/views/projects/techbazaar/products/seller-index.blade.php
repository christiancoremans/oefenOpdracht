{{-- resources/views/projects/techbazaar/products/seller-index.blade.php --}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-5xl mx-auto px-4 py-10">

        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">My Products</h2>
            <a href="{{ route('techbazaar.seller.products.create') }}"
               class="bg-amber-500 text-white px-5 py-2 rounded-lg hover:bg-amber-600 font-medium text-sm">
                + Add Product
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700
                        text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @forelse($products as $product)
            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl
                        p-5 mb-4 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-4">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                             class="w-16 h-16 object-cover rounded-lg">
                    @else
                        <div class="w-16 h-16 bg-zinc-100 dark:bg-zinc-700 rounded-lg flex items-center
                                    justify-center text-zinc-400 text-xs text-center">No image</div>
                    @endif
                    <div>
                        <p class="font-semibold text-zinc-900 dark:text-white">{{ $product->name }}</p>
                        <p class="text-sm text-zinc-500">{{ $product->category->name }}</p>
                        <p class="text-sm text-zinc-500">€{{ number_format($product->price, 2) }} · Stock: {{ $product->stock }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('techbazaar.seller.products.edit', $product) }}"
                       class="border border-zinc-300 dark:border-zinc-600 text-zinc-700 dark:text-zinc-300
                              px-4 py-1.5 rounded-lg text-sm hover:bg-zinc-50 dark:hover:bg-zinc-700">
                        Edit
                    </a>
                    {{--
                        EXAM STUDY NOTE — DELETE via form
                        HTML forms only support GET and POST. To send DELETE you use
                        a hidden input @method('DELETE') + @csrf inside a POST form.
                        Laravel reads the _method field and routes it accordingly.
                    --}}
                    <form method="POST"
                          action="{{ route('techbazaar.seller.products.destroy', $product) }}"
                          onsubmit="return confirm('Delete {{ $product->name }}?')">
                        @csrf
                        @method('DELETE')
                        <button class="border border-red-300 text-red-500 px-4 py-1.5 rounded-lg text-sm
                                       hover:bg-red-50 dark:hover:bg-red-900/20">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-16 text-zinc-400">
                <p class="text-lg mb-2">No products yet.</p>
                <a href="{{ route('techbazaar.seller.products.create') }}"
                   class="text-amber-500 hover:underline">Add your first product →</a>
            </div>
        @endforelse

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>
</x-layouts::project-shell>
