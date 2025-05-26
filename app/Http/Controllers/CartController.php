<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\CartItem;
class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity;

        $cartItems = CartItem::where('user_id', $user->id)->get();

        $pizzaCount = 0;
        $drinkCount = 0;

        foreach ($cartItems as $item) {
            if ($item->product->type === 'pizza') {
                $pizzaCount += $item->quantity;
            } elseif ($item->product->type === 'drink') {
                $drinkCount += $item->quantity;
            }
        }

        if ($product->type === 'pizza' && $pizzaCount + $quantity > 10) {
            return response()->json(['error' => 'Можно добавить не более 10 пицц.'], 400);
        }

        if ($product->type === 'drink' && $drinkCount + $quantity > 20) {
            return response()->json(['error' => 'Можно добавить не более 20 напитков.'], 400);
        }

        $cartItem = CartItem::firstOrNew([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $cartItem->quantity += $quantity;
        $cartItem->save();

        return response()->json(['message' => 'Товар добавлен в корзину.']);
    }
}
