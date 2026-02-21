<?php

namespace App\Http\Controllers\Vendor\Hrm;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Http\Requests\StoreDesignationRequest;
use App\Http\Requests\UpdateDesignationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DesignationController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:hrm.designations.view', only: ['index', 'show']),
            new Middleware('permission:hrm.designations.create', only: ['store']),
            new Middleware('permission:hrm.designations.edit', only: ['update']),
            new Middleware('permission:hrm.designations.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $designations = Designation::where('vendor_id', $request->user()->id)
            ->with('department:id,name')
            ->withCount('employees')
            ->orderBy('title')
            ->get();

        return response()->json($designations);
    }

    public function store(StoreDesignationRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['vendor_id'] = $request->user()->id;
        $data['status'] = $data['status'] ?? 'active';

        $designation = Designation::create($data);

        return response()->json([
            'message' => 'Designation created successfully',
            'designation' => $designation->load('department:id,name')->loadCount('employees'),
        ], 201);
    }

    public function show(Request $request, Designation $designation): JsonResponse
    {
        if ($designation->vendor_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($designation->load('department:id,name')->loadCount('employees'));
    }

    public function update(UpdateDesignationRequest $request, Designation $designation): JsonResponse
    {
        if ($designation->vendor_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $designation->update($request->validated());

        return response()->json([
            'message' => 'Designation updated successfully',
            'designation' => $designation->fresh()->load('department:id,name')->loadCount('employees'),
        ]);
    }

    public function destroy(Request $request, Designation $designation): JsonResponse
    {
        if ($designation->vendor_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $designation->delete();

        return response()->json(['message' => 'Designation deleted successfully']);
    }
}
