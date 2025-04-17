<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\OrderItemRequest;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderItemController extends Controller
{
    /**
     * Display a listing of orderItems.
     */
    public function index(Order $order): JsonResponse
    {
        $orderItems = $order->orderItems();
        return response()->json(['success' => true, 'data' => $orderItems]);
    }

    /**
     * Store a newly created orderItem.
     */
    public function store(Request $request, $orderId): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|numeric|min:0',
            'count' => 'required|numeric|min:0',
        ]);
        $validated['order_id'] = $orderId;
        $orderItem = OrderItem::create($validated);
        return response()->json(['success' => true, 'data' => $orderItem], 201);
    }

    /**
     * Display the specified orderItem.
     */
    public function show(OrderItem $orderItem): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $orderItem]);
    }

    /**
     * Update the specified orderItem.
     */
    public function update(Request $request, $orderId, $orderItemId): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'sometimes|numeric|min:0',
            'count' => 'sometimes|numeric|min:0',
        ]);

        Log::info($validated);
        Log::info($orderItemId);
        $orderItem = OrderItem::findOrFail($orderItemId);
        $orderItem->update($validated);
        return response()->json(['success' => true, 'data' => $orderItem]);
    }

    /**
     * Remove the specified orderItem.
     */
    public function destroy(OrderItem $orderItem): JsonResponse
    {
        $orderItem->delete();
        return response()->json(['success' => true], 204);
    }
}
