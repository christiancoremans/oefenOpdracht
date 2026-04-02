<?php

namespace App\Http\Controllers\TechBazaar;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — OrderController
|--------------------------------------------------------------------------
| DB::transaction(function () { ... })
|   → All DB operations inside the closure run as ONE atomic unit.
|   → If ANY statement fails (exception thrown), ALL changes are rolled back.
|   → ALWAYS wrap multi-step DB writes (create order + create items +
|     decrement stock) in a transaction so data can't get into a half-saved state.
|
| $product->decrement('stock', $qty)
|   → Shorthand for:  UPDATE products SET stock = stock - $qty WHERE id = ?
|   → Atomic on the DB level — safer than read-then-write in PHP.
|
| Route-model binding on show($order)
|   → Laravel auto-fetches the Order with the id from the URL.
|   → $this->authorize('view', $order) would check a Policy (not implemented
|     here for brevity), but in a real exam you'd add:
|     abort_if($order->user_id !== auth()->id(), 403);
|--------------------------------------------------------------------------
*/

class OrderController extends Controller
{
    private const CART_KEY = 'techbazaar_cart';

    private function projectData(): array
    {
        return [
            'currentProject'     => 'techbazaar',
            'projectName'        => config('projects.techbazaar.name'),
            'projectDescription' => config('projects.techbazaar.description'),
        ];
    }

    public function index()
    {
        $orders = auth()->user()->orders()->latest()->with('items.product')->paginate(10);

        return view('projects.techbazaar.orders.index', array_merge($this->projectData(), [
            'orders' => $orders,
        ]));
    }

    public function store(Request $request)
    {
        $cart = session()->get(self::CART_KEY, []);

        if (empty($cart)) {
            return redirect()->route('techbazaar.cart.index')
                             ->with('error', 'Your cart is empty.');
        }

        DB::transaction(function () use ($cart) {
            // 1. Create the order shell (total updated at the end)
            $order = Order::create([
                'user_id' => auth()->id(),
                'status'  => Order::STATUS_PENDING,
                'total'   => 0,
            ]);

            $total = 0;

            foreach ($cart as $productId => $item) {
                $product = Product::lockForUpdate()->findOrFail($productId);

                // Guard: stock might have changed since cart was filled
                abort_if($product->stock < $item['quantity'], 422,
                    "Not enough stock for \"{$product->name}\".");

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $productId,
                    'quantity'   => $item['quantity'],
                    'price'      => $product->price, // snapshot current price
                ]);

                $product->decrement('stock', $item['quantity']);
                $total += $product->price * $item['quantity'];
            }

            $order->update(['total' => $total]);
        });

        session()->forget(self::CART_KEY);

        return redirect()->route('techbazaar.orders.index')
                         ->with('success', 'Order placed successfully!');
    }

    public function show(Order $order)
    {
        // Security: a buyer may only see their OWN orders
        abort_if($order->user_id !== auth()->id() && ! auth()->user()->isAdmin(), 403);

        $order->load('items.product');

        return view('projects.techbazaar.orders.show', array_merge($this->projectData(), [
            'order' => $order,
        ]));
    }
}
