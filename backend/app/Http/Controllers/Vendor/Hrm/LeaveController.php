<?php

namespace App\Http\Controllers\Vendor\Hrm;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LeaveController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:hrm.leaves.view', only: ['index']),
            new Middleware('permission:hrm.leaves.manage', only: ['store', 'action', 'destroy']),
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $query = Leave::where('vendor_id', $request->user()->id)
            ->with('employee:id,name,employee_id,avatar');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->leave_type);
        }

        $leaves = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($leaves);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'employee_id' => 'required|integer|exists:hrm_employees,id',
            'leave_type' => 'required|string|in:sick,casual,annual,maternity,paternity,other',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        $vendorId = $request->user()->id;

        $employee = Employee::where('id', $request->employee_id)
            ->where('vendor_id', $vendorId)->firstOrFail();

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        $leave = Leave::create([
            'vendor_id' => $vendorId,
            'employee_id' => $employee->id,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $totalDays,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Leave request created successfully',
            'leave' => $leave->load('employee:id,name,employee_id'),
        ], 201);
    }

    public function action(Request $request, Leave $leave): JsonResponse
    {
        if ($leave->vendor_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'action' => 'required|in:approve,reject,cancel',
            'rejection_reason' => 'required_if:action,reject|nullable|string',
        ]);

        $updateData = [];

        switch ($request->action) {
            case 'approve':
                $updateData['status'] = 'approved';
                $updateData['approved_by'] = $request->user()->id;
                $updateData['approved_at'] = now();
                // Update employee status to on_leave if dates are current
                $today = Carbon::today();
                if ($leave->start_date <= $today && $leave->end_date >= $today) {
                    $leave->employee->update(['status' => 'on_leave']);
                }
                break;
            case 'reject':
                $updateData['status'] = 'rejected';
                $updateData['rejection_reason'] = $request->rejection_reason;
                break;
            case 'cancel':
                $updateData['status'] = 'cancelled';
                break;
        }

        $leave->update($updateData);

        return response()->json([
            'message' => 'Leave ' . $request->action . 'd successfully',
            'leave' => $leave->fresh()->load('employee:id,name,employee_id'),
        ]);
    }

    public function destroy(Request $request, Leave $leave): JsonResponse
    {
        if ($leave->vendor_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $leave->delete();

        return response()->json(['message' => 'Leave request deleted successfully']);
    }
}
