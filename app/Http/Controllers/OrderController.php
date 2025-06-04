<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class OrderController extends Controller
{

    public function __construct(protected OrderService $orderService)
    {
    }

    public function store(StoreOrderRequest $request)
    {
        $result = $this->orderService->createOrder($request->validated());

        return response()->json($result['data'], $result['status']);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        $orders = Order::with('orderItems.product')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json([
            'orders' => $orders->items(),
            'pagination' => [
                'total' => $orders->total(),
                'per_page' => $orders->perPage(),
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
            ],
        ], Response::HTTP_OK);
    }

    public function show($id, Request $request)
    {
        $user = $request->user();

        $order = Order::with('orderItems.product')
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return response()->json($order, Response::HTTP_OK);
    }


}
