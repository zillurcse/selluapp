<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    /**
     * Admin: List all payment methods
     * Vendor: List only active payment methods
     */
    public function index(Request $request)
    {
        $query = PaymentMethod::query();
        
        if ($request->user() && $request->user()->hasRole('super-admin')) {
            // Admin sees everything
        } else {
            // Vendors/Customers only see active ones
            $query->where('is_active', true);
        }

        return response()->json($query->get());
    }

    /**
     * Admin: Create a new payment method
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:payment_methods,slug',
            'icon' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'config_fields' => 'nullable|array'
        ]);

        $paymentMethod = PaymentMethod::create($validated);

        return response()->json([
            'message' => 'Payment method created',
            'data' => $paymentMethod
        ], 201);
    }

    /**
     * Admin: Update a payment method
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|unique:payment_methods,slug,' . $paymentMethod->id,
            'icon' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'config_fields' => 'nullable|array'
        ]);

        $paymentMethod->update($validated);

        return response()->json([
            'message' => 'Payment method updated',
            'data' => $paymentMethod
        ]);
    }

    /**
     * Admin: Delete a payment method
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();
        return response()->json(['message' => 'Payment method deleted']);
    }
}
