<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $employeeId = $this->route('employee')?->id ?? $this->route('employee');
        return [
            'department_id' => 'nullable|integer|exists:hrm_departments,id',
            'designation_id' => 'nullable|integer|exists:hrm_designations,id',
            'employee_id' => 'sometimes|required|string|max:50|unique:hrm_employees,employee_id,' . $employeeId,
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:hrm_employees,email,' . $employeeId,
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|max:2048',
            'joining_date' => 'nullable|date',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:active,inactive,on_leave',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:50',
        ];
    }
}
