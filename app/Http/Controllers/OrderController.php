<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $orders = $request->user()
            ->orders()
            ->with('group')
            ->withCount('items')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Order $o) => [
                'id' => $o->id,
                'order_number' => $o->order_number,
                'status' => $o->status->value,
                'total_amount' => $o->total_amount,
                'created_at' => $o->created_at->toISOString(),
                'items_count' => $o->items_count,
                'group_name' => $o->group?->name,
            ]);

        return Inertia::render('Orders', [
            'orders' => $orders,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $order = DB::transaction(function () use ($validated, $request) {
            $totalAmount = 0;
            $orderItems = [];

            foreach ($validated['items'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    abort(422, "Insufficient stock for product #{$product->id}");
                }

                $product->decrement('stock', $item['quantity']);

                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                $orderItems[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                ];
            }

            $user = $request->user();

            $order = Order::create([
                'user_id' => $user->id,
                'group_id' => $user->group_id, // snapshot of user's group at order time
                'status' => OrderStatus::Pending,
                'total_amount' => $totalAmount,
                'notes' => $validated['notes'] ?? null,
            ]);

            $order->items()->createMany($orderItems);

            return $order;
        });

        return redirect()->route('checkout')->with('flash', [
            'order_number' => $order->order_number,
        ]);
    }
}
