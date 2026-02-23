<?php

namespace App\Http\Controllers\Vendor\Hrm;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PayrollController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('package.feature:hrm'),
            new Middleware('permission:hrm.payroll.view', only: ['index', 'summary']),
            new Middleware('permission:hrm.payroll.manage', only: ['generate', 'update', 'markPaid']),
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $query = Payroll::where('vendor_id', $request->user()->id)
            ->with('employee:id,name,employee_id,avatar,department_id,designation_id');

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        } else {
            $query->where('month', Carbon::now()->format('Y-m'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payrolls = $query->orderBy('created_at', 'desc')->get();

        return response()->json($payrolls);
    }

    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
        ]);

        $vendorId = $request->user()->id;
        $month = $request->month;

        $employees = Employee::where('vendor_id', $vendorId)
            ->where('status', '!=', 'inactive')
            ->get();

        $generated = 0;

        foreach ($employees as $employee) {
            // Skip if payroll already exists
            if (Payroll::where('employee_id', $employee->id)->where('month', $month)->exists()) {
                continue;
            }

            // Calculate days from attendance
            $startDate = Carbon::parse("{$month}-01");
            $endDate = $startDate->copy()->endOfMonth();
            $workingDays = $startDate->diffInWeekdays($endDate) + 1;

            $presentDays = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->whereIn('status', ['present', 'late'])
                ->count();

            $leaveDays = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->where('status', 'half_day')
                ->count();

            $basicSalary = $employee->salary;
            $perDaySalary = $workingDays > 0 ? $basicSalary / $workingDays : 0;
            $earnedSalary = $perDaySalary * $presentDays;
            $tax = round($earnedSalary * 0.05, 2); // 5% tax example
            $netSalary = round($earnedSalary - $tax, 2);

            Payroll::create([
                'vendor_id' => $vendorId,
                'employee_id' => $employee->id,
                'month' => $month,
                'basic_salary' => $basicSalary,
                'allowances' => 0,
                'bonuses' => 0,
                'deductions' => 0,
                'tax' => $tax,
                'net_salary' => $netSalary,
                'working_days' => $workingDays,
                'present_days' => $presentDays,
                'leave_days' => $leaveDays,
                'status' => 'pending',
            ]);

            $generated++;
        }

        return response()->json([
            'message' => "{$generated} payroll records generated for {$month}",
            'generated' => $generated,
        ]);
    }

    public function update(Request $request, Payroll $payroll): JsonResponse
    {
        if ($payroll->vendor_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'allowances' => 'nullable|numeric|min:0',
            'bonuses' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:pending,processing,paid',
            'notes' => 'nullable|string',
            'payment_date' => 'nullable|date',
        ]);

        $data = $request->only([
            'allowances', 'bonuses', 'deductions', 'status', 'notes', 'payment_date'
        ]);

        // Recalculate net salary
        $payroll->fill($data);
        $payroll->net_salary = round(
            $payroll->basic_salary + $payroll->allowances + $payroll->bonuses
            - $payroll->deductions - $payroll->tax,
            2
        );
        $payroll->save();

        return response()->json([
            'message' => 'Payroll updated successfully',
            'payroll' => $payroll->fresh()->load('employee:id,name,employee_id'),
        ]);
    }

    public function markPaid(Request $request, Payroll $payroll): JsonResponse
    {
        if ($payroll->vendor_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $payroll->update([
            'status' => 'paid',
            'payment_date' => $request->payment_date ?? now()->format('Y-m-d'),
        ]);

        return response()->json([
            'message' => 'Payroll marked as paid',
            'payroll' => $payroll->fresh()->load('employee:id,name,employee_id'),
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        $vendorId = $request->user()->id;
        $month = $request->month ?? Carbon::now()->format('Y-m');

        $payrolls = Payroll::where('vendor_id', $vendorId)
            ->where('month', $month)
            ->get();

        return response()->json([
            'month' => $month,
            'total_employees' => $payrolls->count(),
            'total_net_salary' => $payrolls->sum('net_salary'),
            'total_paid' => $payrolls->where('status', 'paid')->sum('net_salary'),
            'total_pending' => $payrolls->where('status', 'pending')->sum('net_salary'),
            'paid_count' => $payrolls->where('status', 'paid')->count(),
            'pending_count' => $payrolls->where('status', 'pending')->count(),
        ]);
    }
}
