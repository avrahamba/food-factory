<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(): JsonResponse
    {
        $orders = Order::all();
        return response()->json(['success' => true, 'data' => $orders]);
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'customer_id' => 'required|numeric|min:0',
            'date' => 'required|date',
        ]);

        $order = Order::create($validated);
        return response()->json(['success' => true, 'data' => $order], 201);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): JsonResponse
    {
        $orderItems = $order->orderItems();
        $calcPrice = $order->calcPrice();
        return response()->json(['success' => true, 'data' => ['order' => $order, 'orderItems' => $orderItems, 'calcPrice' => $calcPrice]]);
    }

    /**
     * Update the specified order.
     */
    public function update(Request $request, Order $order): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price_for_customer' => 'sometimes|numeric|min:0',
            'unit_type' => 'sometimes|string|max:20'
        ]);

        $order->update($validated);
        return response()->json(['success' => true, 'data' => $order]);
    }

    /**
     * Remove the specified order.
     */
    public function destroy(Order $order): JsonResponse
    {
        $order->delete();
        return response()->json(['success' => true], 204);
    }
}
