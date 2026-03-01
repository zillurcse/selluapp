<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CountryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:settings.view', only: ['index', 'show']),
            new Middleware('permission:settings.manage', only: ['store', 'update', 'destroy']),
        ];
    }

    /**
     * Display a listing of the countries.
     */
    public function index(Request $request)
    {
        $query = Country::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%')
                  ->orWhere('iso_code', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->has('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $perPage = $request->input('per_page', 10);
        $countries = $query->latest()->paginate($perPage);

        return response()->json($countries);
    }

    /**
     * Store a newly created country in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'iso_code' => 'required|string|max:3|unique:countries,iso_code',
            'is_active' => 'boolean',
        ]);

        $country = Country::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Country created successfully',
            'data' => $country
        ], 201);
    }

    /**
     * Display the specified country.
     */
    public function show(Country $country)
    {
        return response()->json([
            'status' => 'success',
            'data' => $country
        ]);
    }

    /**
     * Update the specified country in storage.
     */
    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'iso_code' => 'sometimes|required|string|max:3|unique:countries,iso_code,' . $country->id,
            'is_active' => 'sometimes|boolean',
        ]);

        $country->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Country updated successfully',
            'data' => $country
        ]);
    }

    /**
     * Remove the specified country from storage.
     */
    public function destroy(Country $country)
    {
        $country->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Country deleted successfully'
        ]);
    }
}
