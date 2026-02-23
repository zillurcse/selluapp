<?php

namespace App\Http\Controllers\Vendor\Hrm;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DepartmentController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('package.feature:hrm'),
            new Middleware('permission:hrm.departments.view', only: ['index', 'show']),
            new Middleware('permission:hrm.departments.create', only: ['store']),
            new Middleware('permission:hrm.departments.edit', only: ['update']),
            new Middleware('permission:hrm.departments.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $departments = Department::where('vendor_id', $request->user()->id)
            ->withCount('employees')
            ->orderBy('name')
            ->get();

        return response()->json($departments);
    }

    public function store(StoreDepartmentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['vendor_id'] = $request->user()->id;
        $data['status'] = $data['status'] ?? 'active';

        $department = Department::create($data);

        return response()->json([
            'message' => 'Department created successfully',
            'department' => $department->loadCount('employees'),
        ], 201);
    }

    public function show(Request $request, Department $department): JsonResponse
    {
        if ($department->vendor_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($department->loadCount('employees'));
    }

    public function update(UpdateDepartmentRequest $request, Department $department): JsonResponse
    {
        if ($department->vendor_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $department->update($request->validated());

        return response()->json([
            'message' => 'Department updated successfully',
            'department' => $department->fresh()->loadCount('employees'),
        ]);
    }

    public function destroy(Request $request, Department $department): JsonResponse
    {
        if ($department->vendor_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $department->delete();

        return response()->json(['message' => 'Department deleted successfully']);
    }
}
