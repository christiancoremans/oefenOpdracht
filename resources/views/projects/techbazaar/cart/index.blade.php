{{-- resources/views/projects/techbazaar/cart/index.blade.php --}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-3xl mx-auto px-4 py-10">

        <h2 class="text-2xl font-bold text-zinc-900 dark:text-white mb-8">Shopping Cart</h2>

        @if(session('success'))
            <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700
                        text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700
                        text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        @if(empty($cart))
            <div class="text-center py-16 text-zinc-400">
                <p class="text-5xl mb-4">🛒</p>
                <p class="text-lg mb-2">Your cart is empty.</p>
                <a href="{{ route('techbazaar.home') }}"
                   class="text-amber-500 hover:underline">Browse products →</a>
            </div>
        @else
            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl
                        shadow-sm overflow-hidden mb-6">
                <table class="w-full">
                    <thead class="bg-zinc-50 dark:bg-zinc-700 text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                        <tr>
                            <th class="px-6 py-3 text-left">Product</th>
                            <th class="px-6 py-3 text-right">Unit Price</th>
                            <th class="px-6 py-3 text-right">Qty</th>
                            <th class="px-6 py-3 text-right">Subtotal</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                        @foreach($cart as $productId => $item)
                            <tr>
                                <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">
                                    {{ $item['name'] }}
                                </td>
                                <td class="px-6 py-4 text-right text-zinc-600 dark:text-zinc-300">
                                    €{{ number_format($item['price'], 2) }}
                                </td>
                                <td class="px-6 py-4 text-right text-zinc-600 dark:text-zinc-300">
                                    {{ $item['quantity'] }}
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-zinc-900 dark:text-white">
                                    €{{ number_format($item['price'] * $item['quantity'], 2) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    {{--
                                        EXAM STUDY NOTE — DELETE with route param
                                        Route: DELETE /cart/{productId} → CartController@remove
                                        The productId is passed in the route, not body.
                                    --}}
                                    <form method="POST"
                                          action="{{ route('techbazaar.cart.remove', $productId) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-500 hover:text-red-700 text-sm">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between">
                <div class="text-xl font-bold text-zinc-900 dark:text-white">
                    Total: <span class="text-amber-500">€{{ number_format($total, 2) }}</span>
                </div>
                {{--
                    EXAM STUDY NOTE — Checkout form (POST to orders.store)
                    The cart data lives in the session on the server.
                    No cart data needs to be in the form body — the controller
                    reads it from session('techbazaar_cart').
                --}}
                <form method="POST" action="{{ route('techbazaar.orders.store') }}">
                    @csrf
                    <button type="submit"
                            class="bg-amber-500 text-white px-8 py-3 rounded-lg hover:bg-amber-600 font-semibold">
                        Place Order →
                    </button>
                </form>
            </div>
        @endif
    </div>
</x-layouts::project-shell>
