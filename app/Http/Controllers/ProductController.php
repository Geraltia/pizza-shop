<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Symfony\Component\HttpFoundation\Response;

final class ProductController extends Controller
{
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->only('name', 'description', 'price'));

        return response()->json(['product' => $product], Response::HTTP_CREATED);
    }
}
