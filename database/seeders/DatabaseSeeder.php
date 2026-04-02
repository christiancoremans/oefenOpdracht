<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ──────────────────────────────────────────────────────────
        // EXAM NOTE: Hash::make() is the correct way to hash passwords in seeders.
        // Never store plain-text passwords. bcrypt() is an alias for Hash::make().
        $admin = User::create([
            'name'     => 'Admin',
            'email'    => 'admin@test.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        $seller1 = User::create([
            'name'     => 'Seller One',
            'email'    => 'seller1@test.com',
            'password' => Hash::make('password'),
            'role'     => 'seller',
        ]);

        $seller2 = User::create([
            'name'     => 'Seller Two',
            'email'    => 'seller2@test.com',
            'password' => Hash::make('password'),
            'role'     => 'seller',
        ]);

        $buyer1 = User::create([
            'name'     => 'Buyer One',
            'email'    => 'buyer1@test.com',
            'password' => Hash::make('password'),
            'role'     => 'buyer',
        ]);

        $buyer2 = User::create([
            'name'     => 'Buyer Two',
            'email'    => 'buyer2@test.com',
            'password' => Hash::make('password'),
            'role'     => 'buyer',
        ]);

        // ── Categories ─────────────────────────────────────────────────────
        $laptops     = Category::create(['name' => 'Laptops',      'slug' => 'laptops']);
        $accessories = Category::create(['name' => 'Accessories',  'slug' => 'accessories']);

        // ── Products ───────────────────────────────────────────────────────
        $laptop1 = Product::create([
            'user_id'     => $seller1->id,
            'category_id' => $laptops->id,
            'name'        => 'GameBook Pro 15',
            'description' => 'High-performance gaming laptop with RTX 4060 GPU.',
            'price'       => 1299.99,
            'stock'       => 10,
        ]);

        $laptop2 = Product::create([
            'user_id'     => $seller1->id,
            'category_id' => $laptops->id,
            'name'        => 'UltraSlim X1',
            'description' => 'Ultra-thin business laptop, 14-inch OLED display.',
            'price'       => 899.99,
            'stock'       => 15,
        ]);

        $mouse = Product::create([
            'user_id'     => $seller2->id,
            'category_id' => $accessories->id,
            'name'        => 'ErgoMouse Pro',
            'description' => 'Ergonomic wireless mouse with 90-day battery life.',
            'price'       => 49.99,
            'stock'       => 50,
        ]);

        $keyboard = Product::create([
            'user_id'     => $seller2->id,
            'category_id' => $accessories->id,
            'name'        => 'MechKey TKL',
            'description' => 'Tenkeyless mechanical keyboard, Cherry MX Brown switches.',
            'price'       => 79.99,
            'stock'       => 30,
        ]);

        // ── Orders (completed so buyers can leave reviews) ─────────────────
        // EXAM NOTE: Orders use status constants defined on the model to avoid magic strings.
        $order1 = Order::create([
            'user_id' => $buyer1->id,
            'status'  => Order::STATUS_COMPLETED,
            'total'   => 1299.99 + 49.99,
        ]);
        OrderItem::create(['order_id' => $order1->id, 'product_id' => $laptop1->id, 'quantity' => 1, 'price' => 1299.99]);
        OrderItem::create(['order_id' => $order1->id, 'product_id' => $mouse->id,   'quantity' => 1, 'price' => 49.99]);

        $order2 = Order::create([
            'user_id' => $buyer2->id,
            'status'  => Order::STATUS_COMPLETED,
            'total'   => 79.99 * 2,
        ]);
        OrderItem::create(['order_id' => $order2->id, 'product_id' => $keyboard->id, 'quantity' => 2, 'price' => 79.99]);

        // ── Reviews ────────────────────────────────────────────────────────
        // EXAM NOTE: Reviews have a unique(['user_id','product_id']) constraint.
        // A buyer can only review a product once, even across multiple orders.
        Review::create([
            'user_id'    => $buyer1->id,
            'product_id' => $laptop1->id,
            'order_id'   => $order1->id,
            'rating'     => 5,
            'comment'    => 'Excellent performance! Runs every game at max settings.',
        ]);

        Review::create([
            'user_id'    => $buyer1->id,
            'product_id' => $mouse->id,
            'order_id'   => $order1->id,
            'rating'     => 4,
            'comment'    => 'Very comfortable. Battery lasts forever.',
        ]);

        Review::create([
            'user_id'    => $buyer2->id,
            'product_id' => $keyboard->id,
            'order_id'   => $order2->id,
            'rating'     => 5,
            'comment'    => 'Best mechanical keyboard I have owned.',
        ]);
    }
}
