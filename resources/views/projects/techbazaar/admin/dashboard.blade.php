{{-- resources/views/projects/techbazaar/admin/dashboard.blade.php --}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-5xl mx-auto px-4 py-10">

        <h2 class="text-2xl font-bold text-zinc-900 dark:text-white mb-8">Admin Dashboard</h2>

        {{-- Stats grid --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">

            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700
                        rounded-2xl p-6 shadow-sm text-center">
                <p class="text-3xl font-bold text-amber-500">{{ $totalUsers }}</p>
                <p class="text-sm text-zinc-500 mt-1">Users</p>
            </div>

            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700
                        rounded-2xl p-6 shadow-sm text-center">
                <p class="text-3xl font-bold text-amber-500">{{ $totalProducts }}</p>
                <p class="text-sm text-zinc-500 mt-1">Products</p>
            </div>

            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700
                        rounded-2xl p-6 shadow-sm text-center">
                <p class="text-3xl font-bold text-amber-500">{{ $totalOrders }}</p>
                <p class="text-sm text-zinc-500 mt-1">Orders</p>
            </div>

            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700
                        rounded-2xl p-6 shadow-sm text-center">
                <p class="text-3xl font-bold text-amber-500">€{{ number_format($totalRevenue, 2) }}</p>
                <p class="text-sm text-zinc-500 mt-1">Revenue</p>
            </div>
        </div>

        {{-- Admin nav shortcuts --}}
        <div class="grid grid-cols-2 gap-4 mb-10">
            <a href="{{ route('techbazaar.admin.users.index') }}"
               class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700
                      rounded-xl p-5 shadow-sm hover:border-amber-400 transition group">
                <p class="font-semibold text-zinc-900 dark:text-white group-hover:text-amber-500">
                    Manage Users →
                </p>
                <p class="text-sm text-zinc-500 mt-1">View, edit roles, delete accounts</p>
            </a>
            <a href="{{ route('techbazaar.admin.products.index') }}"
               class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700
                      rounded-xl p-5 shadow-sm hover:border-amber-400 transition group">
                <p class="font-semibold text-zinc-900 dark:text-white group-hover:text-amber-500">
                    Manage Products →
                </p>
                <p class="text-sm text-zinc-500 mt-1">Review and remove listings</p>
            </a>
        </div>

        {{-- Recent orders --}}
        <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Recent Orders</h3>
        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl
                    shadow-sm overflow-hidden">
            @forelse($recentOrders as $order)
                <div class="flex items-center justify-between px-6 py-4
                            border-b border-zinc-100 dark:border-zinc-700 last:border-none">
                    <div>
                        <p class="font-medium text-zinc-900 dark:text-white">Order #{{ $order->id }}</p>
                        <p class="text-sm text-zinc-500">{{ $order->user->name ?? 'Unknown user' }}
                            · {{ $order->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="font-semibold text-zinc-900 dark:text-white">
                            €{{ number_format($order->total, 2) }}
                        </span>
                        @php
                            $badge = match($order->status) {
                                'pending'   => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                'shipped'   => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                'completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                default     => 'bg-zinc-100 text-zinc-800',
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="px-6 py-8 text-center text-zinc-400">No orders yet.</p>
            @endforelse
        </div>
    </div>
</x-layouts::project-shell>
