{{-- resources/views/projects/techbazaar/orders/index.blade.php --}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-4xl mx-auto px-4 py-10">

        <h2 class="text-2xl font-bold text-zinc-900 dark:text-white mb-8">My Orders</h2>

        @if(session('success'))
            <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700
                        text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @forelse($orders as $order)
            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl
                        p-5 mb-4 flex items-center justify-between shadow-sm">
                <div>
                    <p class="font-semibold text-zinc-900 dark:text-white">Order #{{ $order->id }}</p>
                    <p class="text-sm text-zinc-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    <p class="text-sm text-zinc-500">{{ $order->items->count() }} item(s)</p>
                </div>
                <div class="flex items-center gap-6">
                    <span class="font-bold text-zinc-900 dark:text-white">
                        €{{ number_format($order->total, 2) }}
                    </span>
                    {{--
                        EXAM STUDY NOTE — Status badge with conditional Tailwind
                        @match() is PHP 8 match expression — returns the first matching arm.
                        Great for mapping a value to CSS classes or labels.
                    --}}
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
                    <a href="{{ route('techbazaar.orders.show', $order) }}"
                       class="text-amber-500 hover:text-amber-600 text-sm font-medium">
                        View →
                    </a>
                </div>
            </div>
        @empty
            <div class="text-center py-16 text-zinc-400">
                <p class="text-lg mb-2">No orders yet.</p>
                <a href="{{ route('techbazaar.home') }}"
                   class="text-amber-500 hover:underline">Start shopping →</a>
            </div>
        @endforelse

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    </div>
</x-layouts::project-shell>
