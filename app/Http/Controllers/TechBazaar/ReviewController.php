<?php

namespace App\Http\Controllers\TechBazaar;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — ReviewController
|--------------------------------------------------------------------------
| Business rule: a buyer may only review a product they actually purchased
| in a COMPLETED order, and only ONCE per product.
|
| Validation rule 'min:1|max:5'
|   → The rating must be between 1 and 5 inclusive.
|
| 'unique:reviews,product_id,NULL,id,user_id,' . auth()->id()
|   is an alternative to the manual duplicate check, but the manual check
|   below is clearer and easier to understand in an exam context.
|
| redirect()->back()
|   → Sends the user back to the previous page (the product detail page).
|   → ->with('success', ...) flashes a message to the session.
|   → In the view: @if(session('success')) ... @endif
|--------------------------------------------------------------------------
*/

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_id'   => 'required|exists:orders,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string|max:1000',
        ]);

        // Guard 1: the order must belong to this user
        $order = Order::findOrFail($request->order_id);
        abort_if($order->user_id !== auth()->id(), 403);

        // Guard 2: the order must be completed
        abort_if($order->status !== Order::STATUS_COMPLETED, 422,
            'You can only review products from completed orders.');

        // Guard 3: the product must actually be in that order
        abort_if(
            ! $order->items()->where('product_id', $request->product_id)->exists(),
            422, 'That product is not in this order.'
        );

        // Guard 4: prevent a second review for the same product
        abort_if(
            Review::where('user_id', auth()->id())
                  ->where('product_id', $request->product_id)
                  ->exists(),
            422, 'You have already reviewed this product.'
        );

        Review::create([
            'user_id'    => auth()->id(),
            'product_id' => $request->product_id,
            'order_id'   => $request->order_id,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Review submitted. Thank you!');
    }
}
