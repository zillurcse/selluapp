<?php

namespace App\Http\Controllers\Vendor\Hrm;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class EmployeeController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:hrm.employees.view', only: ['index', 'show']),
            new Middleware('permission:hrm.employees.create', only: ['store']),
            new Middleware('permission:hrm.employees.edit', only: ['update']),
            new Middleware('permission:hrm.employees.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $query = Employee::where('vendor_id', $request->user()->id)
            ->with(['department:id,name', 'designation:id,title']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $employees = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($employees);
    }

    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['vendor_id'] = $request->user()->id;
        $data['status'] = $data['status'] ?? 'active';

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('hrm/avatars', 'public');
        }

        $employee = Employee::create($data);

        return response()->json([
            'message' => 'Employee created successfully',
            'employee' => $employee->load(['department:id,name', 'designation:id,title']),
        ], 201);
    }

    public function show(Request $request, Employee $employee): JsonResponse
    {
        if ($employee->vendor_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($employee->load(['department:id,name', 'designation:id,title']));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): JsonResponse
    {
        if ($employee->vendor_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            if ($employee->avatar) {
                Storage::disk('public')->delete($employee->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('hrm/avatars', 'public');
        }

        $employee->update($data);

        return response()->json([
            'message' => 'Employee updated successfully',
            'employee' => $employee->fresh()->load(['department:id,name', 'designation:id,title']),
        ]);
    }

    public function destroy(Request $request, Employee $employee): JsonResponse
    {
        if ($employee->vendor_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($employee->avatar) {
            Storage::disk('public')->delete($employee->avatar);
        }

        $employee->delete();

        return response()->json(['message' => 'Employee deleted successfully']);
    }
}
