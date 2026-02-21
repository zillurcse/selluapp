export function useHrm() {
    const config = useRuntimeConfig()
    const tokenStore = useTokenStore()

    const getHeaders = () => ({
        Accept: 'application/json',
        Authorization: `Bearer ${tokenStore.token}`,
    })

    const apiBase = () => `${config.public.apiBase}/vendor/hrm`

    // ─── Dashboard ─────────────────────────────────────────────────────────────
    const fetchStats = () =>
        $fetch(`${apiBase()}/stats`, { headers: getHeaders() })

    const fetchRecentActivities = () =>
        $fetch(`${apiBase()}/recent-activities`, { headers: getHeaders() })

    // ─── Departments ────────────────────────────────────────────────────────────
    const fetchDepartments = () =>
        $fetch(`${apiBase()}/departments`, { headers: getHeaders() })

    const createDepartment = (data: any) =>
        $fetch(`${apiBase()}/departments`, {
            method: 'POST',
            headers: getHeaders(),
            body: data,
        })

    const updateDepartment = (id: number, data: any) =>
        $fetch(`${apiBase()}/departments/${id}`, {
            method: 'PUT',
            headers: getHeaders(),
            body: data,
        })

    const deleteDepartment = (id: number) =>
        $fetch(`${apiBase()}/departments/${id}`, {
            method: 'DELETE',
            headers: getHeaders(),
        })

    // ─── Designations ───────────────────────────────────────────────────────────
    const fetchDesignations = () =>
        $fetch(`${apiBase()}/designations`, { headers: getHeaders() })

    const createDesignation = (data: any) =>
        $fetch(`${apiBase()}/designations`, {
            method: 'POST',
            headers: getHeaders(),
            body: data,
        })

    const updateDesignation = (id: number, data: any) =>
        $fetch(`${apiBase()}/designations/${id}`, {
            method: 'PUT',
            headers: getHeaders(),
            body: data,
        })

    const deleteDesignation = (id: number) =>
        $fetch(`${apiBase()}/designations/${id}`, {
            method: 'DELETE',
            headers: getHeaders(),
        })

    // ─── Employees ──────────────────────────────────────────────────────────────
    const fetchEmployees = (params: Record<string, any> = {}) => {
        const query = new URLSearchParams(params).toString()
        return $fetch(`${apiBase()}/employees${query ? '?' + query : ''}`, {
            headers: getHeaders(),
        })
    }

    const createEmployee = (data: FormData) =>
        $fetch(`${apiBase()}/employees`, {
            method: 'POST',
            headers: { Authorization: `Bearer ${tokenStore.token}` },
            body: data,
        })

    const updateEmployee = (id: number, data: FormData) =>
        $fetch(`${apiBase()}/employees/${id}?_method=PUT`, {
            method: 'POST',
            headers: { Authorization: `Bearer ${tokenStore.token}` },
            body: data,
        })

    const deleteEmployee = (id: number) =>
        $fetch(`${apiBase()}/employees/${id}`, {
            method: 'DELETE',
            headers: getHeaders(),
        })

    // ─── Attendance ─────────────────────────────────────────────────────────────
    const fetchAttendance = (params: Record<string, any> = {}) => {
        const query = new URLSearchParams(params).toString()
        return $fetch(`${apiBase()}/attendance${query ? '?' + query : ''}`, {
            headers: getHeaders(),
        })
    }

    const fetchAttendanceSummary = (date?: string) =>
        $fetch(`${apiBase()}/attendance/summary${date ? '?date=' + date : ''}`, {
            headers: getHeaders(),
        })

    const saveAttendance = (data: any) =>
        $fetch(`${apiBase()}/attendance`, {
            method: 'POST',
            headers: getHeaders(),
            body: data,
        })

    const saveBulkAttendance = (data: any) =>
        $fetch(`${apiBase()}/attendance/bulk`, {
            method: 'POST',
            headers: getHeaders(),
            body: data,
        })

    // ─── Leaves ─────────────────────────────────────────────────────────────────
    const fetchLeaves = (params: Record<string, any> = {}) => {
        const query = new URLSearchParams(params).toString()
        return $fetch(`${apiBase()}/leaves${query ? '?' + query : ''}`, {
            headers: getHeaders(),
        })
    }

    const createLeave = (data: any) =>
        $fetch(`${apiBase()}/leaves`, {
            method: 'POST',
            headers: getHeaders(),
            body: data,
        })

    const leaveAction = (id: number, action: string, rejectionReason?: string) =>
        $fetch(`${apiBase()}/leaves/${id}/action`, {
            method: 'POST',
            headers: getHeaders(),
            body: { action, rejection_reason: rejectionReason },
        })

    const deleteLeave = (id: number) =>
        $fetch(`${apiBase()}/leaves/${id}`, {
            method: 'DELETE',
            headers: getHeaders(),
        })

    // ─── Payroll ────────────────────────────────────────────────────────────────
    const fetchPayroll = (params: Record<string, any> = {}) => {
        const query = new URLSearchParams(params).toString()
        return $fetch(`${apiBase()}/payroll${query ? '?' + query : ''}`, {
            headers: getHeaders(),
        })
    }

    const fetchPayrollSummary = (month?: string) =>
        $fetch(`${apiBase()}/payroll/summary${month ? '?month=' + month : ''}`, {
            headers: getHeaders(),
        })

    const generatePayroll = (month: string) =>
        $fetch(`${apiBase()}/payroll/generate`, {
            method: 'POST',
            headers: getHeaders(),
            body: { month },
        })

    const updatePayroll = (id: number, data: any) =>
        $fetch(`${apiBase()}/payroll/${id}`, {
            method: 'PUT',
            headers: getHeaders(),
            body: data,
        })

    const markPayrollPaid = (id: number, paymentDate?: string) =>
        $fetch(`${apiBase()}/payroll/${id}/mark-paid`, {
            method: 'POST',
            headers: getHeaders(),
            body: { payment_date: paymentDate },
        })

    return {
        fetchStats,
        fetchRecentActivities,
        fetchDepartments,
        createDepartment,
        updateDepartment,
        deleteDepartment,
        fetchDesignations,
        createDesignation,
        updateDesignation,
        deleteDesignation,
        fetchEmployees,
        createEmployee,
        updateEmployee,
        deleteEmployee,
        fetchAttendance,
        fetchAttendanceSummary,
        saveAttendance,
        saveBulkAttendance,
        fetchLeaves,
        createLeave,
        leaveAction,
        deleteLeave,
        fetchPayroll,
        fetchPayrollSummary,
        generatePayroll,
        updatePayroll,
        markPayrollPaid,
    }
}
