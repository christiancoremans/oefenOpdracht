<?php

namespace App\Http\Controllers\TechBazaar;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — CartController (session-based shopping cart)
|--------------------------------------------------------------------------
| WHY session-based and not a DB table?
|   → Simplicity. A DB cart table needs: migrations, model, seeder.
|   → Session cart is fine for an exam scope without payment integration.
|   → Downside: cart is lost when the session expires or user logs out.
|
| session() helper:
|   session()->get('key', $default)  → Read; returns $default if missing
|   session()->put('key', $value)    → Write
|   session()->forget('key')         → Delete one key
|   session()->flush()               → Delete ALL session data (use carefully)
|
| Cart structure stored in session:
|   [
|     product_id => [
|       'name'     => string,
|       'price'    => decimal string,
|       'quantity' => int,
|       'image'    => string|null,
|     ],
|     ...
|   ]
|
| EXAM NOTE: We namespace the cart key as 'techbazaar_cart' so it doesn't
|   clash with other practice projects that might also use a cart.
|--------------------------------------------------------------------------
*/

class CartController extends Controller
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
        $cart  = session()->get(self::CART_KEY, []);
        $total = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);

        return view('projects.techbazaar.cart.index', array_merge($this->projectData(), [
            'cart'  => $cart,
            'total' => $total,
        ]));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'integer|min:1|max:99',
        ]);

        $product  = Product::findOrFail($request->product_id);
        $quantity = $request->input('quantity', 1);

        // Guard: cannot add more than available stock
        abort_if($product->stock < 1, 422, 'Product is out of stock.');

        $cart = session()->get(self::CART_KEY, []);

        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $quantity;
        } else {
            $cart[$product->id] = [
                'name'     => $product->name,
                'price'    => $product->price,
                'quantity' => $quantity,
                'image'    => $product->image,
            ];
        }

        session()->put(self::CART_KEY, $cart);

        return redirect()->route('techbazaar.cart.index')
                         ->with('success', "\"{$product->name}\" added to cart.");
    }

    public function remove(int $product)
    {
        $cart = session()->get(self::CART_KEY, []);
        unset($cart[$product]);
        session()->put(self::CART_KEY, $cart);

        return redirect()->route('techbazaar.cart.index')
                         ->with('success', 'Item removed from cart.');
    }
}
