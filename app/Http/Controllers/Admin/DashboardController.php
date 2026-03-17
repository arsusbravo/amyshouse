<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_products' => Product::count(),
                'active_products' => Product::active()->count(),
                'total_clients' => User::where('is_admin', false)->count(),
                'active_clients' => User::where('is_admin', false)->where('is_active', true)->count(),
                'pending_orders' => Order::where('status', OrderStatus::Pending)->count(),
                'total_orders' => Order::count(),
            ],
            'recent_orders' => Order::with('user')
                ->withCount('items')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get()
                ->map(fn (Order $o) => [
                    'id' => $o->id,
                    'order_number' => $o->order_number,
                    'client_name' => $o->user->name,
                    'client_group' => $o->user->group,
                    'status' => $o->status->value,
                    'total_amount' => $o->total_amount,
                    'items_count' => $o->items_count,
                    'created_at' => $o->created_at->toISOString(),
                ]),
        ]);
    }
}
