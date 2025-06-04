<?php

namespace App\Services;

use App\Models\Product;
use App\Models\CartItem;
use App\ProductType;
use Illuminate\Support\Facades\Auth;
use LogicException;

class CartService
{
    public function addToCart(int $productId, int $quantity): CartItem
    {
        $user = Auth::user();
        $product = Product::findOrFail($productId);

        $cartItems = CartItem::where('user_id', $user->id)->get();

        $pizzaCount = 0;
        $drinkCount = 0;

        foreach ($cartItems as $item) {
            if ($item->product->type === ProductType::Pizza) {
                $pizzaCount += $item->quantity;
            } elseif ($item->product->type === ProductType::Drink) {
                $drinkCount += $item->quantity;
            }
        }

        if ($product->type === ProductType::Pizza && $pizzaCount + $quantity > 10) {
            throw new LogicException('Можно добавить не более 10 пицц.');
        }

        if ($product->type === ProductType::Drink && $drinkCount + $quantity > 20) {
            throw new LogicException('Можно добавить не более 20 напитков.');
        }

        $cartItem = CartItem::firstOrNew([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $cartItem->quantity += $quantity;
        $cartItem->save();

        return $cartItem;
    }
}
