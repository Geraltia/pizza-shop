<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class AdminOrderController extends Controller
{
    public function orders()
    {
        $perPage = request()->get('per_page', 10);
        $orders = Order::with('orderItems.product')->paginate($perPage);

        return response()->json($orders);
    }

    public function orderDetails($id)
    {
        $order = Order::with('orderItems.product')->findOrFail($id);

        return response()->json($order);
    }
}
