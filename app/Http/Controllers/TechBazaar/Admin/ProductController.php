<?php

namespace App\Http\Controllers\TechBazaar\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Admin ProductController
|--------------------------------------------------------------------------
| Admin sees ALL products from ALL sellers.
|
| with('user', 'category')
|   → Eager-load the seller's name and the category alongside each product.
|
| ->delete() + Storage::disk('public')->delete($product->image)
|   → Admin can force-delete any product, including cleaning up the image.
|   → Always clean up file storage when deleting records that have files.
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
        return view('projects.techbazaar.admin.products.index', array_merge($this->projectData(), [
            'products' => Product::with(['user', 'category'])->latest()->paginate(20),
        ]));
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('techbazaar.admin.products.index')
                         ->with('success', "\"{$product->name}\" deleted.");
    }
}
