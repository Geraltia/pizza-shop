<?php

namespace App\Http\Controllers;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;

class ProductController extends Controller
{
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->only('name', 'description', 'price'));

        return response()->json(['product' => $product], Response::HTTP_CREATED);
    }
}
