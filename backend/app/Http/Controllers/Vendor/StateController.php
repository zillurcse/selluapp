<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class StateController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:settings.view', only: ['index', 'show']),
            new Middleware('permission:settings.manage', only: ['store', 'update', 'destroy']),
        ];
    }

    /**
     * Display a listing of the states.
     */
    public function index(Request $request)
    {
        $query = State::with('country');

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->has('country_id')) {
            $query->where('country_id', $request->input('country_id'));
        }

        if ($request->has('status')) {
            $query->where('status', (bool) $request->input('status'));
        }

        $perPage = $request->input('per_page', 10);
        $states = $query->latest()->paginate($perPage);

        return response()->json($states);
    }

    /**
     * Store a newly created state in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'status' => 'boolean',
        ]);

        $state = State::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'State created successfully',
            'data' => $state
        ], 201);
    }

    /**
     * Display the specified state.
     */
    public function show(State $state)
    {
        return response()->json([
            'status' => 'success',
            'data' => $state->load('country')
        ]);
    }

    /**
     * Update the specified state in storage.
     */
    public function update(Request $request, State $state)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'country_id' => 'sometimes|required|exists:countries,id',
            'status' => 'sometimes|boolean',
        ]);

        $state->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'State updated successfully',
            'data' => $state
        ]);
    }

    /**
     * Remove the specified state from storage.
     */
    public function destroy(State $state)
    {
        $state->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'State deleted successfully'
        ]);
    }

    /**
     * Fetch countries for the state creation/management UI.
     */
    public function get_countries()
    {
        $countries = Country::where('is_active', true)->get();
        return response()->json([
            'status' => 'success',
            'data' => $countries
        ]);
    }
}
