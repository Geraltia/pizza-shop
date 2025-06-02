<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

final class OrderService
{
    public function createOrder(array $data): array
    {
        $user = Auth::user();

        $cartItems = CartItem::where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return [
                'status' => Response::HTTP_BAD_REQUEST,
                'data' => ['message' => 'Корзина пуста'],
            ];
        }

        return DB::transaction(static function () use ($user, $data, $cartItems) {
            $order = Order::create([
                'user_id' => $user->id,
                'phone' => $data['phone'],
                'email' => $data['email'],
                'address' => $data['address'],
                'delivery_time' => $data['delivery_time'],
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                ]);
            }

            CartItem::where('user_id', $user->id)->delete();

            return [
                'status' => Response::HTTP_CREATED,
                'data' => [
                    'message' => 'Заказ успешно оформлен',
                    'order_id' => $order->id,
                ],
            ];
        });
    }
}
