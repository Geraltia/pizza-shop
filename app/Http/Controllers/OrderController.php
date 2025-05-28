<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreOrderRequest;
use Symfony\Component\HttpFoundation\Response;


class OrderController extends Controller
{
    public function store(StoreOrderRequest $request)
    {
        $user = Auth::user();

        $validated = $request->validated();

        $cartItems = CartItem::where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'message' => 'Корзина пуста'
            ], Response::HTTP_BAD_REQUEST);
        }

        $order = Order::create([
            'user_id' => $user->id,
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'delivery_time' => $validated['delivery_time'],
        ]);

        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
            ]);
        }

        CartItem::where('user_id', $user->id)->delete();

        return response()->json([
            'message' => 'Заказ успешно оформлен',
            'order_id' => $order->id
        ], Response::HTTP_CREATED);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $orders = Order::with('orderItems.product')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'orders' => $orders
        ], Response::HTTP_OK);
    }

}
