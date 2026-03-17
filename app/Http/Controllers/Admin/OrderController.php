<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Order::with('user')
            ->withCount('items')
            ->orderByDesc('created_at');

        if ($status = $request->input('status')) {
            $orderStatus = OrderStatus::tryFrom($status);
            if ($orderStatus) {
                $query->where('status', $orderStatus);
            }
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        $orders = $query->paginate(20)->through(fn (Order $o) => [
            'id' => $o->id,
            'order_number' => $o->order_number,
            'client_name' => $o->user->name,
            'client_email' => $o->user->email,
            'client_group' => $o->user->group?->name,
            'status' => $o->status->value,
            'total_amount' => $o->total_amount,
            'items_count' => $o->items_count,
            'notes' => $o->notes,
            'created_at' => $o->created_at->toISOString(),
        ]);

        $statuses = collect(OrderStatus::cases())->map(fn (OrderStatus $s) => [
            'value' => $s->value,
            'label' => $s->labelEn(),
        ]);

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'statuses' => $statuses,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    public function show(Order $order): Response
    {
        $order->load(['user.group', 'group', 'items.product']);

        return Inertia::render('Admin/Orders/Show', [
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status->value,
                'total_amount' => $order->total_amount,
                'notes' => $order->notes,
                'created_at' => $order->created_at->toISOString(),
                'client' => [
                    'id' => $order->user->id,
                    'name' => $order->user->name,
                    'email' => $order->user->email,
                    'phone' => $order->user->phone,
                    'group' => $order->group?->name ?? $order->user->group?->name,
                ],
                'items' => $order->items->map(fn ($item) => [
                    'id' => $item->id,
                    'product_name_zh' => $item->product->nameFor('zh-TW'),
                    'product_name_en' => $item->product->nameFor('en'),
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                ]),
            ],
            'statuses' => collect(OrderStatus::cases())->map(fn (OrderStatus $s) => [
                'value' => $s->value,
                'label' => $s->labelEn(),
            ]),
        ]);
    }
}