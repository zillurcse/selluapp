<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CustomerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('package.feature:customers'),
            new Middleware('permission:customers.view', only: ['index', 'show']),
            new Middleware('permission:customers.create', only: ['store']),
            new Middleware('permission:customers.edit', only: ['update']),
            new Middleware('permission:customers.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the customers for the authenticated vendor.
     */
    public function index(Request $request): JsonResponse
    {
        // Get customers scoped to the authenticated vendor
        $customers = Customer::where('vendor_id', $request->user()->id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($customers);
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        
        // Add vendor user ID to the customer data
        $validatedData['vendor_id'] = $request->user()->id;

        // Hash password if provided
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }
        

        $customer = Customer::create($validatedData);
        // create user for customer
        $user = \App\Models\User::create([
            'name' => $customer->first_name . ' ' . $customer->last_name,
            'email' => $customer->email,
            'password' => $customer->password,
        ]);
        $user->assignRole('customer');

        return response()->json([
            'message' => 'Customer created successfully',
            'customer' => $customer
        ], 201);
    }

    /**
     * Display the specified customer.
     */
    public function show(Request $request, Customer $customer): JsonResponse
    {
        // Ensure the customer belongs to the authenticated vendor
        if ($customer->vendor_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($customer);
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        // Ensure the customer belongs to the authenticated vendor
        if ($customer->vendor_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validatedData = $request->validated();

        // Hash password if provided, else remove it from update array
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        $customer->update($validatedData);

        return response()->json([
            'message' => 'Customer updated successfully',
            'customer' => $customer
        ]);
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Request $request, Customer $customer): JsonResponse
    {
        // Ensure the customer belongs to the authenticated vendor
        if ($customer->vendor_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $customer->delete();

        return response()->json([
            'message' => 'Customer deleted successfully'
        ]);
    }
}
