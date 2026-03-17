<?php

namespace App\Http\Controllers\Admin\Api;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:' . implode(',', array_column(OrderStatus::cases(), 'value')),
        ]);

        $newStatus = OrderStatus::from($validated['status']);

        // If cancelling, restore stock
        if ($newStatus === OrderStatus::Cancelled && $order->status !== OrderStatus::Cancelled) {
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }
        }

        $order->update(['status' => $newStatus]);

        return back()->with('success', "Order status updated to {$newStatus->labelEn()}.");
    }
}