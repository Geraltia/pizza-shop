<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Http\Requests\StoreProductRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\UpdateProductRequest;

class AdminController extends Controller
{
    public function products(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $products = Product::paginate($perPage);
        return response()->json($products);
    }

    public function addProduct(StoreProductRequest $request)
    {
        $product = Product::create($request->validated());

        return response()->json([
            'product' => $product,
        ], Response::HTTP_CREATED);
    }

    public function updateProduct(UpdateProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);

        $product->update($request->validated());

        return response()->json([
            'product' => $product,
        ], Response::HTTP_OK);
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->noContent();
    }

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
