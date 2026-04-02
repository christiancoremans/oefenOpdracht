{{-- resources/views/projects/techbazaar/admin/products/index.blade.php --}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-5xl mx-auto px-4 py-10">

        <div class="flex items-center gap-3 mb-8">
            <a href="{{ route('techbazaar.admin.dashboard') }}"
               class="text-sm text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300">← Admin Dashboard</a>
            <span class="text-zinc-300">/</span>
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">All Products</h2>
        </div>

        @if(session('success'))
            <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700
                        text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl
                    shadow-sm overflow-hidden">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700 text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                    <tr>
                        <th class="px-6 py-3 text-left">Product</th>
                        <th class="px-6 py-3 text-left">Seller</th>
                        <th class="px-6 py-3 text-left">Category</th>
                        <th class="px-6 py-3 text-right">Price</th>
                        <th class="px-6 py-3 text-right">Stock</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                    @forelse($products as $product)
                        <tr>
                            <td class="px-6 py-4">
                                <a href="{{ route('techbazaar.products.show', $product) }}"
                                   class="font-medium text-zinc-900 dark:text-white hover:text-amber-500">
                                    {{ $product->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-zinc-500">
                                {{ $product->user->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-zinc-500">
                                {{ $product->category->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-right text-zinc-900 dark:text-white font-medium">
                                €{{ number_format($product->price, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right text-zinc-500">
                                {{ $product->stock }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                {{--
                                    EXAM STUDY NOTE — Admin delete overrides seller ownership
                                    The Admin\ProductController::destroy() does NOT check
                                    $product->user_id === auth()->id(). Admin can delete any product.
                                    Access to this route is protected by role:admin middleware.
                                --}}
                                <form method="POST"
                                      action="{{ route('techbazaar.admin.products.destroy', $product) }}"
                                      onsubmit="return confirm('Delete {{ $product->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="border border-red-300 text-red-500 px-3 py-1
                                                   rounded text-sm hover:bg-red-50 dark:hover:bg-red-900/20">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-zinc-400">
                                No products in the system.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </div>
</x-layouts::project-shell>
