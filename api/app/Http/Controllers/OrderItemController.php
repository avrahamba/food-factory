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
            'note' => 'sometimes|string|max:32767',
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
            'note' => 'sometimes|string|max:32767',
            'cooked' => 'sometimes|boolean',
            'in_car' => 'sometimes|boolean'
        ]);

        $orderItem = OrderItem::findOrFail($orderItemId);
        $orderItem->update($validated);
        return response()->json(['success' => true, 'data' => $orderItem]);
    }

    /**
     * Update multiple order items that belong to the same product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $orderId
     * @return \Illuminate\Http\Response
     */
    public function updateByProduct(Request $request, $orderId): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|numeric|min:0',
            'field' => 'sometimes|string|max:255',
            'value' => 'sometimes|boolean',
        ]);

        switch ($validated['field']) {
            case 'cooked':
                $orderItems = OrderItem::where('order_id', $orderId)
                    ->where('product_id', $validated['product_id'])
                    ->update(['cooked' => $validated['value']]);
                break;
            case 'inCar':
                $orderItems = OrderItem::where('order_id', $orderId)
                    ->where('product_id', $validated['product_id'])
                    ->update(['in_car' => $validated['value']]);
                break;
            default:
                return response()->json(['success' => false, 'message' => 'Invalid field'], 400);
        }

        return response()->json(['success' => true, 'data' => $orderItems]);
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
