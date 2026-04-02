{{-- resources/views/projects/techbazaar/dashboard.blade.php --}}
{{--
    EXAM STUDY NOTE — Role-based dashboard
    @switch(auth()->user()->role) → equivalent to PHP switch, shows different
    content per role without a separate controller or view per role.
--}}
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-5xl mx-auto px-4 py-10">

        <div class="mb-8">
            <h2 class="text-2xl font-bold text-zinc-900 dark:text-white">
                Welcome back, {{ auth()->user()->name }}
            </h2>
            <p class="text-zinc-500 dark:text-zinc-400 mt-1">
                You are logged in as a
                <span class="font-semibold capitalize">{{ auth()->user()->role }}</span>.
            </p>
        </div>

        @switch(auth()->user()->role)

            @case('admin')
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <a href="{{ route('techbazaar.admin.dashboard') }}"
                       class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800
                               rounded-xl p-6 hover:shadow-md transition-shadow">
                        <div class="text-2xl mb-2">🔧</div>
                        <h3 class="font-bold text-red-700 dark:text-red-400">Admin Panel</h3>
                        <p class="text-sm text-red-600 dark:text-red-500 mt-1">Statistics, user & product management</p>
                    </a>
                    <a href="{{ route('techbazaar.seller.products.index') }}"
                       class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800
                               rounded-xl p-6 hover:shadow-md transition-shadow">
                        <div class="text-2xl mb-2">📦</div>
                        <h3 class="font-bold text-amber-700 dark:text-amber-400">Manage Products</h3>
                        <p class="text-sm text-amber-600 dark:text-amber-500 mt-1">Add, edit and remove products</p>
                    </a>
                    <a href="{{ route('techbazaar.orders.index') }}"
                       class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800
                               rounded-xl p-6 hover:shadow-md transition-shadow">
                        <div class="text-2xl mb-2">📋</div>
                        <h3 class="font-bold text-blue-700 dark:text-blue-400">View Orders</h3>
                        <p class="text-sm text-blue-600 dark:text-blue-500 mt-1">All buyer orders</p>
                    </a>
                </div>
                @break

            @case('seller')
                <div class="grid sm:grid-cols-2 gap-4">
                    <a href="{{ route('techbazaar.seller.products.index') }}"
                       class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800
                               rounded-xl p-6 hover:shadow-md transition-shadow">
                        <div class="text-2xl mb-2">📦</div>
                        <h3 class="font-bold text-amber-700 dark:text-amber-400">My Products</h3>
                        <p class="text-sm text-amber-600 dark:text-amber-500 mt-1">Create, edit and delete your listings</p>
                    </a>
                    <a href="{{ route('techbazaar.seller.products.create') }}"
                       class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800
                               rounded-xl p-6 hover:shadow-md transition-shadow">
                        <div class="text-2xl mb-2">➕</div>
                        <h3 class="font-bold text-green-700 dark:text-green-400">Add New Product</h3>
                        <p class="text-sm text-green-600 dark:text-green-500 mt-1">List a new item for sale</p>
                    </a>
                </div>
                @break

            @default {{-- buyer --}}
                <div class="grid sm:grid-cols-3 gap-4">
                    <a href="{{ route('techbazaar.home') }}"
                       class="bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700
                               rounded-xl p-6 hover:shadow-md transition-shadow">
                        <div class="text-2xl mb-2">🔍</div>
                        <h3 class="font-bold text-zinc-700 dark:text-zinc-300">Browse Products</h3>
                        <p class="text-sm text-zinc-500 mt-1">Search the catalog</p>
                    </a>
                    <a href="{{ route('techbazaar.cart.index') }}"
                       class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800
                               rounded-xl p-6 hover:shadow-md transition-shadow">
                        <div class="text-2xl mb-2">🛒</div>
                        <h3 class="font-bold text-amber-700 dark:text-amber-400">My Cart</h3>
                        <p class="text-sm text-amber-600 dark:text-amber-500 mt-1">View and checkout</p>
                    </a>
                    <a href="{{ route('techbazaar.orders.index') }}"
                       class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800
                               rounded-xl p-6 hover:shadow-md transition-shadow">
                        <div class="text-2xl mb-2">📋</div>
                        <h3 class="font-bold text-blue-700 dark:text-blue-400">My Orders</h3>
                        <p class="text-sm text-blue-600 dark:text-blue-500 mt-1">Track your purchases</p>
                    </a>
                </div>

        @endswitch
    </div>
</x-layouts::project-shell>
