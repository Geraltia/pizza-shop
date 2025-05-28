<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\AddToCartRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\CartItem;
use LogicException;
class CartController extends Controller
{
    public function addToCart(AddToCartRequest $request)
    {
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
            throw new LogicException('Можно добавить не более 10 пицц.');
        }

        if ($product->type === 'drink' && $drinkCount + $quantity > 20) {
            throw new LogicException('Можно добавить не более 20 напитков.');
        }

        $cartItem = CartItem::firstOrNew([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $cartItem->quantity += $quantity;
        $cartItem->save();

        return response()->noContent(Response::HTTP_OK);

    }
}
