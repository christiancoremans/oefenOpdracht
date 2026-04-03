{{-- resources/views/projects/techbazaar/products/show.blade.php --}}
@php
    /* @var \App\Models\Product $product */
    /* @var bool $canReview */
@endphp
<x-layouts::project-shell
    :currentProject="$currentProject"
    :projectName="$projectName"
    :projectDescription="$projectDescription"
>
    <div class="max-w-4xl mx-auto px-4 py-10">

        {{-- Flash messages --}}
        @foreach(['success','error'] as $type)
            @if(session($type))
                <div class="mb-6 rounded-lg px-4 py-3 {{ $type === 'success'
                    ? 'bg-green-100 dark:bg-green-900/30 border border-green-300 text-green-800 dark:text-green-300'
                    : 'bg-red-100 dark:bg-red-900/30 border border-red-300 text-red-800 dark:text-red-300' }}">
                    {{ session($type) }}
                </div>
            @endif
        @endforeach

        {{-- Product detail --}}
        <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-2xl overflow-hidden shadow-sm mb-10">
            <div class="md:flex">
                {{-- Image --}}
                <div class="md:w-80 aspect-square bg-zinc-100 dark:bg-zinc-700 shrink-0 flex items-center justify-center text-6xl">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                             class="w-full h-full object-cover">
                    @else
                        📦
                    @endif
                </div>
                {{-- Info --}}
                <div class="p-8 flex-1">
                    <span class="text-xs text-amber-600 dark:text-amber-400 font-semibold uppercase tracking-wide">
                        {{ $product->category->name }}
                    </span>
                    <h2 class="text-2xl font-bold text-zinc-900 dark:text-white mt-1 mb-3">
                        {{ $product->name }}
                    </h2>
                    <p class="text-zinc-600 dark:text-zinc-400 mb-4">{{ $product->description }}</p>
                    <div class="flex items-center gap-4 mb-6">
                        <span class="text-3xl font-bold text-amber-600 dark:text-amber-400">
                            €{{ number_format($product->price, 2) }}
                        </span>
                        <span class="text-sm text-zinc-400">
                            {{ $product->stock > 0 ? "{$product->stock} in stock" : 'Out of stock' }}
                        </span>
                    </div>
                    <p class="text-xs text-zinc-400 mb-6">Sold by: {{ $product->user->name }}</p>

                    {{-- Add to cart form (buyer + admin only) --}}
                    @auth
                        @if(auth()->user()->isBuyer() || auth()->user()->isAdmin())
                            @if($product->stock > 0)
                                {{--
                                    EXAM STUDY NOTE: @csrf is REQUIRED on every POST form.
                                    Without it Laravel returns 419 "Page Expired".
                                    It protects against Cross-Site Request Forgery attacks.
                                --}}
                                <form method="POST" action="{{ route('techbazaar.cart.add') }}"
                                      class="flex gap-3 items-center">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}"
                                           class="w-20 border border-zinc-300 dark:border-zinc-600 rounded-lg px-3 py-2
                                                  bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white text-center">
                                    <button type="submit"
                                            class="bg-amber-500 text-white px-6 py-2 rounded-lg hover:bg-amber-600 font-medium">
                                        Add to Cart 🛒
                                    </button>
                                </form>
                            @else
                                <p class="text-red-500 font-medium">Out of stock</p>
                            @endif
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="text-amber-600 hover:underline">
                            Log in to add to cart
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        {{-- Reviews --}}
        <div class="mb-6">
            <h3 class="text-xl font-bold text-zinc-900 dark:text-white mb-4">
                Reviews ({{ $product->reviews->count() }})
            </h3>

            @forelse($product->reviews as $review)
                <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700
                             rounded-xl p-5 mb-3 shadow-sm">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $review->user->name }}</span>
                        <span class="text-amber-500">
                            @for($i = 1; $i <= 5; $i++)
                                {{ $i <= $review->rating ? '★' : '☆' }}
                            @endfor
                        </span>
                    </div>
                    @if($review->comment)
                        <p class="text-zinc-600 dark:text-zinc-400 text-sm">{{ $review->comment }}</p>
                    @endif
                </div>
            @empty
                <p class="text-zinc-400">No reviews yet.</p>
            @endforelse
        </div>

        {{-- Leave a review (only if $canReview is true — set by CatalogController) --}}
        @if($canReview)
            @php
                // Find a completed order containing this product for the review form
                $reviewOrder = \App\Models\Order::where('user_id', auth()->id())
                    ->where('status', \App\Models\Order::STATUS_COMPLETED)
                    ->whereHas('items', fn($q) => $q->where('product_id', $product->id))
                    ->first();
            @endphp
            <div class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700
                         rounded-xl p-6 shadow-sm">
                <h3 class="font-bold text-zinc-800 dark:text-zinc-200 mb-4">Leave a Review</h3>
                <form method="POST" action="{{ route('techbazaar.reviews.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="order_id" value="{{ $reviewOrder->id }}">

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                            Rating (1-5)
                        </label>
                        <input type="number" name="rating" min="1" max="5" value="{{ old('rating', 5) }}"
                               class="w-24 border border-zinc-300 dark:border-zinc-600 rounded-lg px-3 py-2
                                      bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white">
                        @error('rating')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                            Comment (optional)
                        </label>
                        <textarea name="comment" rows="3"
                                  class="w-full border border-zinc-300 dark:border-zinc-600 rounded-lg px-4 py-2
                                         bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white
                                         focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('comment') }}</textarea>
                        @error('comment')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="bg-amber-500 text-white px-6 py-2 rounded-lg hover:bg-amber-600">
                        Submit Review
                    </button>
                </form>
            </div>
        @endif
    </div>
</x-layouts::project-shell>
