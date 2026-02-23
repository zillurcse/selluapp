<?php

namespace App\Http\Controllers\Vendor\Hrm;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class HrmDashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('package.feature:hrm'),
            new Middleware('permission:hrm.dashboard.view'),
        ];
    }

    public function stats(Request $request): JsonResponse
    {
        $vendorId = $request->user()->id;
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        $totalEmployees = Employee::where('vendor_id', $vendorId)->count();
        $activeEmployees = Employee::where('vendor_id', $vendorId)->where('status', 'active')->count();
        $onLeave = Employee::where('vendor_id', $vendorId)->where('status', 'on_leave')->count();
        $pendingLeaves = Leave::where('vendor_id', $vendorId)->where('status', 'pending')->count();
        $newHires = Employee::where('vendor_id', $vendorId)
            ->where('joining_date', '>=', Carbon::now()->subDays(30))
            ->count();

        $todayPresent = Attendance::where('vendor_id', $vendorId)
            ->whereDate('date', $today)
            ->where('status', 'present')
            ->count();

        $attendanceRate = $activeEmployees > 0
            ? round(($todayPresent / $activeEmployees) * 100)
            : 0;

        return response()->json([
            'total_employees' => $totalEmployees,
            'on_leave' => $onLeave,
            'attendance_rate' => $attendanceRate . '%',
            'new_hires' => $newHires,
            'pending_leaves' => $pendingLeaves,
            'active_employees' => $activeEmployees,
        ]);
    }

    public function recentActivities(Request $request): JsonResponse
    {
        $vendorId = $request->user()->id;

        $newEmployees = Employee::where('vendor_id', $vendorId)
            ->with(['department', 'designation'])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(fn($e) => [
                'type' => 'new_employee',
                'text' => "{$e->name} was added",
                'sub' => $e->department ? "to {$e->department->name}" : "to team",
                'time' => $e->created_at->diffForHumans(),
            ]);

        $recentLeaves = Leave::where('vendor_id', $vendorId)
            ->with('employee')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(fn($l) => [
                'type' => 'leave_request',
                'text' => "{$l->employee->name} requested {$l->leave_type} leave",
                'sub' => "From {$l->start_date->format('M d')} to {$l->end_date->format('M d')}",
                'time' => $l->created_at->diffForHumans(),
            ]);

        $activities = $newEmployees->merge($recentLeaves)
            ->sortByDesc('time')
            ->values()
            ->take(5);

        return response()->json($activities);
    }
}
