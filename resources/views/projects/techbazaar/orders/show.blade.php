{{-- resources/views/projects/techbazaar/orders/show.blade.php --}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-3xl mx-auto px-4 py-10">

        <div class="flex items-center gap-3 mb-8">
            <a href="{{ route('techbazaar.orders.index') }}"
               class="text-sm text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300">← My Orders</a>
            <span class="text-zinc-300">/</span>
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Order #{{ $order->id }}</h2>
        </div>

        {{-- Order header --}}
        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl
                    p-6 mb-6 shadow-sm">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-zinc-500 uppercase tracking-wide mb-1">Placed on</p>
                    <p class="font-medium text-zinc-900 dark:text-white">
                        {{ $order->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-zinc-500 uppercase tracking-wide mb-1">Status</p>
                    @php
                        $badge = match($order->status) {
                            'pending'   => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                            'shipped'   => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                            'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                            'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                            default     => 'bg-zinc-100 text-zinc-800',
                        };
                    @endphp
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Order items table --}}
        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl
                    shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-zinc-100 dark:border-zinc-700">
                <h3 class="font-semibold text-zinc-900 dark:text-white">Items</h3>
            </div>
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-700 text-xs uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                    <tr>
                        <th class="px-6 py-3 text-left">Product</th>
                        <th class="px-6 py-3 text-right">Unit Price</th>
                        <th class="px-6 py-3 text-right">Qty</th>
                        <th class="px-6 py-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                    {{--
                        EXAM STUDY NOTE — Price snapshot pattern
                        We store $item->price (snapshot) NOT $item->product->price.
                        This means if the seller changes their price tomorrow, the
                        historical order total is unchanged. Critical for financial data.
                    --}}
                    @foreach($order->items as $item)
                        <tr>
                            <td class="px-6 py-4 text-zinc-900 dark:text-white">
                                @if($item->product)
                                    <a href="{{ route('techbazaar.products.show', $item->product) }}"
                                       class="hover:text-amber-500">{{ $item->product->name }}</a>
                                @else
                                    <span class="text-zinc-400 italic">Product removed</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-zinc-600 dark:text-zinc-300">
                                €{{ number_format($item->price, 2) }}
                            </td>
                            <td class="px-6 py-4 text-right text-zinc-600 dark:text-zinc-300">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-zinc-900 dark:text-white">
                                €{{ number_format($item->price * $item->quantity, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-zinc-50 dark:bg-zinc-700">
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right font-semibold text-zinc-700 dark:text-zinc-300">
                            Grand Total
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-lg text-amber-500">
                            €{{ number_format($order->total, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <a href="{{ route('techbazaar.home') }}"
           class="inline-block text-sm text-amber-500 hover:underline">← Continue shopping</a>
    </div>
</x-layouts::project-shell>
