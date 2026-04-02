<?php

namespace App\Http\Controllers\TechBazaar;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — CatalogController (public, no auth)
|--------------------------------------------------------------------------
| with(['category', 'user'])
|   → Eager-load the category and seller alongside each product.
|   → WITHOUT this, each $product->category in the view triggers
|     a separate DB query. This is the N+1 problem.
|   → with() solves it: 1 query for products, 1 for categories. Done.
|
| ->withQueryString()
|   → Makes pagination links preserve the existing ?search=...&category=... params.
|   → Without it, clicking page 2 would lose your search filter.
|
| $request->filled('search')
|   → Returns true only if 'search' is present AND not empty.
|   → Better than $request->has() which returns true even for empty strings.
|--------------------------------------------------------------------------
*/

class CatalogController extends Controller
{
    private function projectData(): array
    {
        return [
            'currentProject'     => 'techbazaar',
            'projectName'        => config('projects.techbazaar.name'),
            'projectDescription' => config('projects.techbazaar.description'),
        ];
    }

    public function index(Request $request)
    {
        $query = Product::with(['category', 'user'])->where('stock', '>', 0);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        return view('projects.techbazaar.products.index', array_merge($this->projectData(), [
            'products'   => $query->latest()->paginate(12)->withQueryString(),
            'categories' => Category::orderBy('name')->get(),
        ]));
    }

    public function show(Product $product)
    {
        // Eager-load nested relationships: reviews → each review's user
        $product->load(['category', 'user', 'reviews.user']);

        $canReview = false;

        if (auth()->check()) {
            // The buyer can review only if:
            // 1. They placed a COMPLETED order that contains this product
            // 2. They have NOT already reviewed this product
            $purchased = Order::where('user_id', auth()->id())
                ->where('status', Order::STATUS_COMPLETED)
                ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
                ->exists();

            $reviewed = Review::where('user_id', auth()->id())
                ->where('product_id', $product->id)
                ->exists();

            $canReview = $purchased && ! $reviewed;
        }

        return view('projects.techbazaar.products.show', array_merge($this->projectData(), [
            'product'   => $product,
            'canReview' => $canReview,
        ]));
    }
}
