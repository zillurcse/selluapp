<?php

namespace App\Http\Controllers\Vendor\Hrm;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AttendanceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:hrm.attendance.view', only: ['index', 'summary']),
            new Middleware('permission:hrm.attendance.manage', only: ['store', 'bulkStore']),
        ];
    }

    public function index(Request $request): JsonResponse
    {
        $query = Attendance::where('vendor_id', $request->user()->id)
            ->with('employee:id,name,employee_id,avatar,department_id');

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        } else {
            $query->whereDate('date', Carbon::today());
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendance = $query->orderBy('created_at', 'desc')->get();

        return response()->json($attendance);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'employee_id' => 'required|integer|exists:hrm_employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'status' => 'required|in:present,absent,late,half_day,holiday',
            'notes' => 'nullable|string',
        ]);

        $vendorId = $request->user()->id;

        // Verify employee belongs to this vendor
        $employee = Employee::where('id', $request->employee_id)
            ->where('vendor_id', $vendorId)
            ->firstOrFail();

        $workingHours = null;
        if ($request->check_in && $request->check_out) {
            $checkIn = Carbon::createFromFormat('H:i', $request->check_in);
            $checkOut = Carbon::createFromFormat('H:i', $request->check_out);
            $workingHours = round($checkIn->diffInMinutes($checkOut) / 60, 2);
        }

        $attendance = Attendance::updateOrCreate(
            ['employee_id' => $employee->id, 'date' => $request->date],
            [
                'vendor_id' => $vendorId,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'working_hours' => $workingHours,
                'status' => $request->status,
                'notes' => $request->notes,
            ]
        );

        return response()->json([
            'message' => 'Attendance recorded successfully',
            'attendance' => $attendance->load('employee:id,name,employee_id'),
        ], 201);
    }

    public function bulkStore(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'records' => 'required|array',
            'records.*.employee_id' => 'required|integer|exists:hrm_employees,id',
            'records.*.status' => 'required|in:present,absent,late,half_day,holiday',
            'records.*.check_in' => 'nullable|date_format:H:i',
            'records.*.check_out' => 'nullable|date_format:H:i',
        ]);

        $vendorId = $request->user()->id;
        $date = $request->date;
        $saved = [];

        foreach ($request->records as $record) {
            $employee = Employee::where('id', $record['employee_id'])
                ->where('vendor_id', $vendorId)->first();

            if (!$employee) continue;

            $workingHours = null;
            if (!empty($record['check_in']) && !empty($record['check_out'])) {
                $checkIn = Carbon::createFromFormat('H:i', $record['check_in']);
                $checkOut = Carbon::createFromFormat('H:i', $record['check_out']);
                $workingHours = round($checkIn->diffInMinutes($checkOut) / 60, 2);
            }

            $saved[] = Attendance::updateOrCreate(
                ['employee_id' => $record['employee_id'], 'date' => $date],
                [
                    'vendor_id' => $vendorId,
                    'check_in' => $record['check_in'] ?? null,
                    'check_out' => $record['check_out'] ?? null,
                    'working_hours' => $workingHours,
                    'status' => $record['status'],
                    'notes' => $record['notes'] ?? null,
                ]
            );
        }

        return response()->json([
            'message' => count($saved) . ' attendance records saved',
            'count' => count($saved),
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        $vendorId = $request->user()->id;
        $date = $request->date ?? Carbon::today()->format('Y-m-d');

        $employees = Employee::where('vendor_id', $vendorId)
            ->where('status', '!=', 'inactive')
            ->with(['attendances' => fn($q) => $q->whereDate('date', $date)])
            ->get();

        $summary = [
            'date' => $date,
            'total' => $employees->count(),
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'on_leave' => 0,
            'not_marked' => 0,
        ];

        foreach ($employees as $employee) {
            $att = $employee->attendances->first();
            if (!$att) {
                $summary['not_marked']++;
            } elseif ($att->status === 'present') {
                $summary['present']++;
            } elseif ($att->status === 'absent') {
                $summary['absent']++;
            } elseif ($att->status === 'late') {
                $summary['late']++;
            } elseif ($att->status === 'half_day') {
                $summary['on_leave']++;
            }
        }

        return response()->json($summary);
    }
}
