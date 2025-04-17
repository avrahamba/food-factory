<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(): JsonResponse
    {
        $products = Product::all();
        return response()->json(['success' => true, 'data' => $products]);
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price_for_customer' => 'required|numeric|min:0',
            'unit_type' => 'required|string|max:20',
            'unit_type_customer' => 'required|string|max:20',
            'ratio' => 'required|numeric|min:0'
        ]);

        $product = Product::create($validated);
        return response()->json(['success' => true, 'data' => $product], 201);
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $product]);
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price_for_customer' => 'sometimes|numeric|min:0',
            'unit_type' => 'sometimes|string|max:20',
            'unit_type_customer' => 'required|string|max:20',
            'ratio' => 'required|numeric|min:0'
        ]);

        $product->update($validated);
        return response()->json(['success' => true, 'data' => $product]);
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return response()->json(['success' => true], 204);
    }
}
