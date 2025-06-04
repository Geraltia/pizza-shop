<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\ProductType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Services\CartService;

final class CartController extends Controller
{
    public function __construct(protected CartService $cartService)
    {
    }


    public function addToCart(AddToCartRequest $request)
    {
        $cartItem = $this->cartService->addToCart(
            $request->product_id,
            $request->quantity
        );

        return response()->json($cartItem, Response::HTTP_OK);
    }


    public function decreaseFromCart(Request $request)
    {
        $user = Auth::user();
        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;

        $cartItem = CartItem::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->firstOrFail();

        $cartItem->quantity -= $quantity;

        if ($cartItem->quantity <= 0) {
            $cartItem->delete();
            return response()->json(['message' => 'Товар удалён из корзины.']);
        }

        $cartItem->save();

        return response()->json($cartItem);
    }

    public function removeFromCart(Request $request)
    {
        $user = Auth::user();
        $productId = $request->product_id;

        $cartItem = CartItem::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            $cartItem->delete();
        }

        return response()->json(['message' => 'Товар удалён из корзины.']);
    }

    public function viewCart()
    {
        $user = Auth::user();

        $cartItems = CartItem::with('product')
            ->where('user_id', $user->id)
            ->get();

        return response()->json($cartItems);
    }




}
