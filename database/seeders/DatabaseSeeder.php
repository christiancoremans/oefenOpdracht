<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\DevTalk\Category as DevTalkCategory;
use App\Models\DevTalk\Post;
use App\Models\DevTalk\Report;
use App\Models\DevTalk\Thread;
use App\Models\DevTalk\Vote;
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

        // ══════════════════════════════════════════════════════════════════
        // ── DevTalk seeder data ───────────────────────────────────────────
        // EXAM NOTE: DevTalk users use DIFFERENT emails than TechBazaar users
        // to avoid unique-email constraint violations on re-seed.
        // devtalk_role is separate from the TechBazaar `role` column.
        // Users without a TechBazaar role get null (defaults to 'buyer' if
        // the column has a DB default, but here we just leave role null).
        // ══════════════════════════════════════════════════════════════════

        // ── DevTalk users ──────────────────────────────────────────────────
        $dtAdmin = User::create([
            'name'         => 'Forum Admin',
            'email'        => 'dtadmin@test.com',
            'password'     => Hash::make('password'),
            'role'         => 'buyer',   // TechBazaar role (required column)
            'devtalk_role' => 'admin',
        ]);

        $dtMod = User::create([
            'name'         => 'Forum Mod',
            'email'        => 'dtmod@test.com',
            'password'     => Hash::make('password'),
            'role'         => 'buyer',
            'devtalk_role' => 'moderator',
        ]);

        $dtUser1 = User::create([
            'name'         => 'Dev User One',
            'email'        => 'dtuser1@test.com',
            'password'     => Hash::make('password'),
            'role'         => 'buyer',
            'devtalk_role' => 'user',
        ]);

        $dtUser2 = User::create([
            'name'         => 'Dev User Two',
            'email'        => 'dtuser2@test.com',
            'password'     => Hash::make('password'),
            'role'         => 'buyer',
            'devtalk_role' => 'user',
        ]);

        // ── DevTalk categories ─────────────────────────────────────────────
        // EXAM NOTE: DevTalkCategory uses devtalk_categories table (prefixed to
        // avoid collision with TechBazaar's `categories` table).
        $catPhp = DevTalkCategory::create([
            'name'        => 'PHP',
            'slug'        => 'php',
            'description' => 'PHP language questions, tips, and best practices.',
        ]);

        $catJs = DevTalkCategory::create([
            'name'        => 'JavaScript',
            'slug'        => 'javascript',
            'description' => 'Frontend and Node.js JavaScript discussions.',
        ]);

        $catCareer = DevTalkCategory::create([
            'name'        => 'Career',
            'slug'        => 'career',
            'description' => 'Advice on jobs, interviews, and developer careers.',
        ]);

        // ── DevTalk threads ────────────────────────────────────────────────
        $thread1 = Thread::create([
            'user_id'     => $dtUser1->id,
            'category_id' => $catPhp->id,
            'title'       => 'How does Laravel service container work?',
            'body'        => "I've been reading about the IoC container and dependency injection in Laravel, but I'm struggling to understand when to use bind() vs singleton(). Can someone explain with a practical example?",
            'views'       => 42,
        ]);

        $thread2 = Thread::create([
            'user_id'     => $dtUser2->id,
            'category_id' => $catJs->id,
            'title'       => 'Async/await vs Promise.then() — which should I use?',
            'body'        => "I see both patterns used in codebases. Is there a performance difference? Are there cases where Promise.then() is better than async/await?",
            'views'       => 27,
        ]);

        $thread3 = Thread::create([
            'user_id'     => $dtMod->id,
            'category_id' => $catCareer->id,
            'title'       => 'Tips for your first developer job interview?',
            'body'        => "About to have my first technical interview next week. Any advice on algorithm questions, system design, or what to expect from a junior role interview?",
            'views'       => 91,
        ]);

        // ── DevTalk posts (replies) ────────────────────────────────────────
        // EXAM NOTE: Post::create() uses devtalk_posts table. The 'body' and
        // 'thread_id'/'user_id' are the minimal required fields.
        $post1 = Post::create([
            'user_id'   => $dtMod->id,
            'thread_id' => $thread1->id,
            'body'      => "bind() creates a new instance every time it's resolved. singleton() creates it once and reuses the same instance. For stateless services (like a Calculator) use bind; for stateful services (like a Cart) use singleton.",
        ]);

        $post2 = Post::create([
            'user_id'   => $dtAdmin->id,
            'thread_id' => $thread1->id,
            'body'      => "Also worth noting: you can use the make() method anywhere to manually resolve from the container — App::make(MyService::class). This is useful in commands and tests.",
        ]);

        $post3 = Post::create([
            'user_id'   => $dtUser1->id,
            'thread_id' => $thread2->id,
            'body'      => "async/await is syntactic sugar over Promises. No performance difference. Use async/await for readability — especially in try/catch. Promise.then() is useful when you need to chain multiple independent promises with Promise.all().",
        ]);

        $post4 = Post::create([
            'user_id'   => $dtUser2->id,
            'thread_id' => $thread3->id,
            'body'      => "Practice LeetCode easy/medium array and string problems. For junior roles, don't overthink system design — they mostly want to see communication and problem decomposition.",
        ]);

        $post5 = Post::create([
            'user_id'   => $dtAdmin->id,
            'thread_id' => $thread3->id,
            'body'      => "Be honest when you don't know something. Say 'I'm not sure, but here's how I'd approach finding out...' — interviewers value self-awareness over bluffing.",
        ]);

        // ── DevTalk votes ──────────────────────────────────────────────────
        // EXAM NOTE: unique(user_id, post_id) — one vote per user per post.
        // value: 1 = upvote, -1 = downvote.
        Vote::create(['user_id' => $dtUser1->id, 'post_id' => $post1->id, 'value' =>  1]);
        Vote::create(['user_id' => $dtUser2->id, 'post_id' => $post1->id, 'value' =>  1]);
        Vote::create(['user_id' => $dtAdmin->id, 'post_id' => $post1->id, 'value' =>  1]);
        Vote::create(['user_id' => $dtUser1->id, 'post_id' => $post2->id, 'value' =>  1]);
        Vote::create(['user_id' => $dtMod->id,   'post_id' => $post3->id, 'value' =>  1]);
        Vote::create(['user_id' => $dtUser2->id, 'post_id' => $post4->id, 'value' =>  1]);
        Vote::create(['user_id' => $dtUser1->id, 'post_id' => $post5->id, 'value' =>  1]);
        Vote::create(['user_id' => $dtMod->id,   'post_id' => $post5->id, 'value' => -1]);

        // ── DevTalk reports ────────────────────────────────────────────────
        // EXAM NOTE: reporter_id (not user_id) because the column was named
        // reporter_id in the migration. The Report model uses:
        //   belongsTo(User::class, 'reporter_id')
        Report::create([
            'reporter_id' => $dtUser2->id,
            'post_id'     => $post3->id,
            'reason'      => 'Off-topic — this is not related to the original question.',
        ]);
        // Mark the post as flagged
        $post3->update(['is_flagged' => true]);
    }
}
