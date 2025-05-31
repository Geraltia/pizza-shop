<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use Symfony\Component\HttpFoundation\Response;


class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
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
            ]
        ], Response::HTTP_OK);
    }



}
