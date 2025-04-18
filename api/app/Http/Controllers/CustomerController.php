<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(): JsonResponse
    {
        Log::info('index');
        $customers = Customer::all();
        return response()->json(['success' => true, 'data' => $customers]);
    }

    /**
     * Store a newly created customer.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'phone' => 'required|string|max:12',
        ]);
        Log::info($validated);
        $customer = Customer::create($validated);
        return response()->json(['success' => true, 'data' => $customer], 201);
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer): JsonResponse
    {
        return response()->json(['success' => true, 'data' => ['customer' => $customer]]);
    }

    /**
     * Update the specified customer.
     */
    public function update(Request $request, Customer $customer): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'phone' => 'required|string|max:12',
        ]);

        $customer->update($validated);
        return response()->json(['success' => true, 'data' => $customer]);
    }

    /**
     * Remove the specified customer.
     */
    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();
        return response()->json(['success' => true], 204);
    }
}
