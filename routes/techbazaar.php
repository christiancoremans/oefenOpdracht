<?php

use App\Http\Controllers\TechBazaar\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\TechBazaar\Admin\ProductController as AdminProduct;
use App\Http\Controllers\TechBazaar\Admin\UserController as AdminUser;
use App\Http\Controllers\TechBazaar\CartController;
use App\Http\Controllers\TechBazaar\CatalogController;
use App\Http\Controllers\TechBazaar\OrderController;
use App\Http\Controllers\TechBazaar\ReviewController;
use App\Http\Controllers\TechBazaar\Seller\ProductController as SellerProduct;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Route file structure
|--------------------------------------------------------------------------
| Route::prefix('project/techbazaar')
|   → All URLs in this file start with /project/techbazaar/...
|
| Route::name('techbazaar.')
|   → All named routes in this group are prefixed: techbazaar.home,
|     techbazaar.cart.index, techbazaar.orders.store, etc.
|
| Nesting middleware groups:
|   1. Outer: auth         → must be logged in
|   2. Inner: role:buyer   → must be logged in AND have role=buyer
|   This nesting is clean and readable in exams.
|
| Route::resource('products', Controller::class)
|   → Generates 7 routes in one line:
|       GET    /products           → index
|       GET    /products/create    → create
|       POST   /products           → store
|       GET    /products/{id}      → show
|       GET    /products/{id}/edit → edit
|       PUT    /products/{id}      → update
|       DELETE /products/{id}      → destroy
|   → ->except(['show']) removes the show route from the set
|
| route-model binding on orders:
|   Route::get('/orders/{order}') → Laravel automatically fetches
|   the Order with id=$order and injects it into the controller method.
|   If not found → 404 automatically.
|--------------------------------------------------------------------------
*/

Route::prefix('project/techbazaar')->name('techbazaar.')->group(function () {

    // ── Public (no login required) ──────────────────────────────────────────
    Route::get('/',                  [CatalogController::class, 'index'])->name('home');
    Route::get('/products',          [CatalogController::class, 'index'])->name('products.index');
    Route::get('/products/{product}',[CatalogController::class, 'show'])->name('products.show');

    // ── All authenticated users ─────────────────────────────────────────────
    Route::middleware('auth')->group(function () {

        // Role-based dashboard hub
        Route::get('/dashboard', fn () => view('projects.techbazaar.dashboard', [
            'currentProject'     => 'techbazaar',
            'projectName'        => config('projects.techbazaar.name'),
            'projectDescription' => config('projects.techbazaar.description'),
        ]))->name('dashboard');

        // ── Buyer (and admin) routes ────────────────────────────────────────
        Route::middleware('role:buyer,admin')->group(function () {

            // Cart — session-based, no model needed
            Route::get('/cart',              [CartController::class, 'index'] )->name('cart.index');
            Route::post('/cart',             [CartController::class, 'add']   )->name('cart.add');
            Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');

            // Orders
            Route::get('/orders',             [OrderController::class, 'index'])->name('orders.index');
            Route::post('/orders',            [OrderController::class, 'store'])->name('orders.store');
            Route::get('/orders/{order}',     [OrderController::class, 'show'] )->name('orders.show');

            // Reviews (post a review after purchase)
            Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
        });

        // ── Seller (and admin) routes ───────────────────────────────────────
        Route::middleware('role:seller,admin')
             ->prefix('seller')->name('seller.')
             ->group(function () {
                 // Full CRUD minus show (catalog show is the public one)
                 Route::resource('products', SellerProduct::class)->except(['show']);
             });

        // ── Admin-only routes ───────────────────────────────────────────────
        Route::middleware('role:admin')
             ->prefix('admin')->name('admin.')
             ->group(function () {
                 Route::get('/',           [AdminDashboard::class, 'index'])->name('dashboard');
                 Route::resource('users',    AdminUser::class)->only(['index', 'update', 'destroy']);
                 Route::resource('products', AdminProduct::class)->only(['index', 'destroy']);
             });
    });
});
