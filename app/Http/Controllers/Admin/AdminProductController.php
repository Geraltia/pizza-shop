<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class AdminProductController extends Controller
{

    public function products(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $cacheKey = "products_page_{$page}_perpage_{$perPage}";

        $products = Cache::remember($cacheKey, 60 * 5, function () use ($perPage) {
            return Product::paginate($perPage)->toArray();
        });

        return response()->json($products, Response::HTTP_OK);
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
}
