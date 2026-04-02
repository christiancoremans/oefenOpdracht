<?php

namespace App\Http\Controllers\TechBazaar\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Seller ProductController (CRUD scoped to the seller)
|--------------------------------------------------------------------------
| CRITICAL SECURITY RULE:
|   ALWAYS scope Eloquent queries to the authenticated user.
|   NEVER do Product::find($id) alone in a seller controller.
|   Instead: auth()->user()->products()->findOrFail($id)
|
|   Why? Without scoping, seller A could edit/delete seller B's products
|   by guessing the product id in the URL.
|   This is called "Insecure Direct Object Reference" (IDOR) — OWASP Top 10.
|
| $request->hasFile('image')
|   → Checks if a file was actually uploaded.
|   → $request->file('image')->store('products', 'public')
|     saves to storage/app/public/products/filename.jpg
|     and returns the relative path.
|   → Storage::url($path) gives the public URL.
|   → Run: php artisan storage:link (once) to make public/storage/ accessible.
|
| ->validated()
|   → Returns ONLY the fields that passed validation rules.
|   → Same as $request->only(['name','price',...]) but auto-synced with rules.
|   → Never use $request->all() directly in create()/update() — it's unsafe.
|--------------------------------------------------------------------------
*/

class ProductController extends Controller
{
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
        // Scoped to the logged-in seller's own products only
        $products = auth()->user()->products()->with('category')->latest()->paginate(15);

        return view('projects.techbazaar.products.seller-index', array_merge($this->projectData(), [
            'products' => $products,
        ]));
    }

    public function create()
    {
        return view('projects.techbazaar.products.create', array_merge($this->projectData(), [
            'categories' => Category::orderBy('name')->get(),
        ]));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|max:2048', // max 2 MB
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        auth()->user()->products()->create(array_merge($validated, ['image' => $imagePath]));

        return redirect()->route('techbazaar.seller.products.index')
                         ->with('success', 'Product created successfully!');
    }

    public function edit(Product $product)
    {
        // Scope check — only the owner (or admin) can edit
        abort_if($product->user_id !== auth()->id() && ! auth()->user()->isAdmin(), 403);

        return view('projects.techbazaar.products.edit', array_merge($this->projectData(), [
            'product'    => $product,
            'categories' => Category::orderBy('name')->get(),
        ]));
    }

    public function update(Request $request, Product $product)
    {
        abort_if($product->user_id !== auth()->id() && ! auth()->user()->isAdmin(), 403);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image from storage before saving the new one
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('techbazaar.seller.products.index')
                         ->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        abort_if($product->user_id !== auth()->id() && ! auth()->user()->isAdmin(), 403);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('techbazaar.seller.products.index')
                         ->with('success', 'Product deleted.');
    }
}
