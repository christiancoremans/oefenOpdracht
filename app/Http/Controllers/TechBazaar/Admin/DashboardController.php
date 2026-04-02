<?php

namespace App\Http\Controllers\TechBazaar\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| EXAM STUDY NOTE — Admin DashboardController (statistics)
|--------------------------------------------------------------------------
| ::count()
|   → SELECT COUNT(*) — extremely fast, no row data loaded.
|
| ::sum('column')
|   → SELECT SUM(column) — DB does the math, not PHP.
|   → ALWAYS use DB aggregates for stats instead of loading all rows.
|   → BAD:  Order::all()->sum('total')  → loads every order row into RAM
|   → GOOD: Order::sum('total')         → single SQL query
|
| ->where('status', Order::STATUS_COMPLETED)
|   → Only count revenue from orders that were actually completed.
|   → Pending/cancelled orders should not count as revenue.
|--------------------------------------------------------------------------
*/

class DashboardController extends Controller
{
    public function index()
    {
        return view('projects.techbazaar.admin.dashboard', [
            'currentProject'     => 'techbazaar',
            'projectName'        => config('projects.techbazaar.name'),
            'projectDescription' => config('projects.techbazaar.description'),
            'totalUsers'         => User::count(),
            'totalProducts'      => Product::count(),
            'totalOrders'        => Order::count(),
            'totalRevenue'       => Order::where('status', Order::STATUS_COMPLETED)->sum('total'),
            'recentOrders'       => Order::with('user')->latest()->take(5)->get(),
        ]);
    }
}
