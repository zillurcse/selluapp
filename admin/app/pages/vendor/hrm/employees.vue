<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Employees</h1>
        <p class="text-gray-500 dark:text-slate-400 mt-1">Manage your team members and their information.</p>
      </div>
      <div class="flex items-center gap-3">
        <button class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl font-semibold text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700 transition-all shadow-sm">
          <Download class="w-4 h-4 mr-2" />Export
        </button>
        <button @click="openDrawer()" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-gray-800 transition-all shadow-sm">
          <Plus class="w-4 h-4 mr-2" />Add Employee
        </button>
      </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm mb-6 flex flex-wrap items-center gap-4">
      <div class="relative flex-1 min-w-[300px]">
        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
        <input v-model="filters.search" @input="debouncedLoad" type="text" placeholder="Search by name, email or ID..."
          class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm transition-all" />
      </div>
      <select v-model="filters.department_id" @change="loadEmployees"
        class="px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm min-w-[150px]">
        <option value="">All Departments</option>
        <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
      </select>
      <select v-model="filters.status" @change="loadEmployees"
        class="px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm min-w-[150px]">
        <option value="">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
        <option value="on_leave">On Leave</option>
      </select>
    </div>

    <!-- Employee Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
      <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50/50 dark:bg-slate-800/50">
          <tr>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Employee</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">ID &amp; Dept</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Designation</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Joined</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 dark:divide-slate-800">
          <!-- Loading skeletons -->
          <tr v-if="loading" v-for="i in 5" :key="i" class="animate-pulse">
            <td class="px-6 py-4"><div class="flex items-center gap-3"><div class="w-10 h-10 rounded-full bg-gray-200"></div><div class="space-y-2"><div class="h-3 w-24 bg-gray-200 rounded"></div><div class="h-2 w-32 bg-gray-200 rounded"></div></div></div></td>
            <td class="px-6 py-4"><div class="h-3 w-20 bg-gray-200 rounded"></div></td>
            <td class="px-6 py-4"><div class="h-3 w-24 bg-gray-200 rounded"></div></td>
            <td class="px-6 py-4"><div class="h-3 w-20 bg-gray-200 rounded"></div></td>
            <td class="px-6 py-4"><div class="h-5 w-16 bg-gray-200 rounded-full"></div></td>
            <td class="px-6 py-4"></td>
          </tr>
          <!-- Empty state -->
          <tr v-else-if="!employees.length">
            <td colspan="6" class="px-6 py-12 text-center">
              <Users class="w-10 h-10 mx-auto mb-2 text-gray-300" />
              <p class="text-sm text-gray-400">No employees found. Add your first employee!</p>
            </td>
          </tr>
          <!-- Data rows -->
          <tr v-else v-for="employee in employees" :key="employee.id" class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors group">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold uppercase overflow-hidden border border-indigo-100">
                  <img v-if="employee.avatar" :src="employee.avatar" class="w-full h-full object-cover" />
                  <span v-else>{{ employee.name?.charAt(0) }}</span>
                </div>
                <div>
                  <div class="text-sm font-bold text-gray-900 dark:text-slate-100">{{ employee.name }}</div>
                  <div class="text-xs text-gray-500 dark:text-slate-400">{{ employee.email }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4">
              <div class="text-sm font-medium text-gray-900 dark:text-slate-100">#{{ employee.employee_id }}</div>
              <div class="text-xs text-gray-500 dark:text-slate-400">{{ employee.department?.name ?? '—' }}</div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-700 dark:text-slate-300">{{ employee.designation?.title ?? '—' }}</td>
            <td class="px-6 py-4 text-sm text-gray-500 dark:text-slate-400">{{ formatDate(employee.joining_date) }}</td>
            <td class="px-6 py-4">
              <span :class="getStatusClass(employee.status)">{{ employee.status?.replace('_', ' ') }}</span>
            </td>
            <td class="px-6 py-4 text-right">
              <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button @click="openDrawer(employee)" class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all">
                  <Pencil class="w-4 h-4" />
                </button>
                <button @click="confirmDelete(employee)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                  <Trash2 class="w-4 h-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <!-- Pagination -->
      <div class="px-6 py-4 bg-gray-50/50 dark:bg-slate-800/50 flex items-center justify-between">
        <span class="text-xs text-gray-500 dark:text-slate-400 font-medium">Showing {{ pagination.from ?? 0 }}–{{ pagination.to ?? 0 }} of {{ pagination.total ?? 0 }}</span>
        <div class="flex gap-2">
          <button :disabled="!pagination.prev_page_url" @click="changePage(currentPage - 1)"
            class="p-1 px-3 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed">Previous</button>
          <button :disabled="!pagination.next_page_url" @click="changePage(currentPage + 1)"
            class="p-1 px-3 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-xs font-semibold text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700 disabled:opacity-40 disabled:cursor-not-allowed">Next</button>
        </div>
      </div>
    </div>

    <!-- Add/Edit Drawer -->
    <div v-if="drawerOpen" class="fixed inset-0 z-[60] overflow-hidden">
      <div class="absolute inset-0 bg-black/20 backdrop-blur-sm" @click="drawerOpen = false"></div>
      <div class="absolute inset-y-0 right-0 max-w-xl w-full flex">
        <div class="relative w-full bg-white dark:bg-slate-900 shadow-2xl flex flex-col h-full animate-slide-left">
          <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-gray-50/50 dark:bg-slate-800/50">
            <div>
              <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ editingEmployee ? 'Edit Employee' : 'Add New Employee' }}</h2>
              <p class="text-xs text-gray-500 dark:text-slate-400">{{ editingEmployee ? 'Update employee information.' : 'Fill in the details to onboard a new team member.' }}</p>
            </div>
            <button @click="drawerOpen = false" class="p-2 hover:bg-gray-200 rounded-full transition-colors text-gray-400">
              <X class="w-5 h-5" />
            </button>
          </div>

          <div class="flex-1 overflow-y-auto p-6 space-y-5">
            <!-- Error -->
            <div v-if="formError" class="bg-red-50 border border-red-100 text-red-600 text-sm px-4 py-3 rounded-xl">{{ formError }}</div>

            <div class="grid grid-cols-2 gap-4">
              <div class="col-span-2">
                <label class="block text-sm font-bold text-gray-900 mb-2">Full Name *</label>
                <input v-model="form.name" type="text" placeholder="e.g. John Doe" class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm transition-all" />
              </div>
              <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Email Address *</label>
                <input v-model="form.email" type="email" placeholder="john@example.com" class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm transition-all" />
              </div>
              <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Phone</label>
                <input v-model="form.phone" type="text" placeholder="+1234567890" class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm transition-all" />
              </div>
              <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Employee ID *</label>
                <input v-model="form.employee_id" type="text" placeholder="EMP-001" class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm transition-all" />
              </div>
              <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Joining Date</label>
                <input v-model="form.joining_date" type="date" class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm transition-all" />
              </div>
              <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Department</label>
                <select v-model="form.department_id" class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm transition-all">
                  <option value="">Select Department</option>
                  <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Designation</label>
                <select v-model="form.designation_id" class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm transition-all">
                  <option value="">Select Designation</option>
                  <option v-for="desg in designations" :key="desg.id" :value="desg.id">{{ desg.title }}</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Salary</label>
                <input v-model="form.salary" type="number" placeholder="e.g. 50000" class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm transition-all" />
              </div>
              <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Gender</label>
                <select v-model="form.gender" class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm transition-all">
                  <option value="">Select</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Status</label>
                <select v-model="form.status" class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm transition-all">
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                  <option value="on_leave">On Leave</option>
                </select>
              </div>
              <div class="col-span-2">
                <label class="block text-sm font-bold text-gray-900 mb-2">Address</label>
                <textarea v-model="form.address" rows="2" placeholder="Employee address..." class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm transition-all resize-none"></textarea>
              </div>
            </div>
          </div>

          <div class="p-10 border-t border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/50 flex items-center gap-3">
            <button @click="drawerOpen = false" class="flex-1 px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl font-semibold text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-all">Cancel</button>
            <button @click="saveEmployee" :disabled="saving" class="flex-1 px-4 py-2.5 bg-gray-900 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-gray-800 transition-all shadow-lg active:scale-95 disabled:opacity-60">
              {{ saving ? 'Saving...' : (editingEmployee ? 'Update Employee' : 'Onboard Employee') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirm Modal -->
    <div v-if="deleteTarget" class="fixed inset-0 z-[70] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" @click="deleteTarget = null"></div>
      <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl p-6 w-full max-w-sm">
        <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
          <Trash2 class="w-6 h-6 text-red-500" />
        </div>
        <h3 class="text-lg font-bold text-gray-900 dark:text-white text-center mb-2">Delete Employee</h3>
        <p class="text-sm text-gray-500 dark:text-slate-400 text-center mb-6">Are you sure you want to delete <strong>{{ deleteTarget?.name }}</strong>? This action cannot be undone.</p>
        <div class="flex gap-3">
          <button @click="deleteTarget = null" class="flex-1 px-4 py-2.5 bg-gray-100 rounded-xl font-semibold text-sm text-gray-700 hover:bg-gray-200 transition-all">Cancel</button>
          <button @click="deleteEmployee" :disabled="deleting" class="flex-1 px-4 py-2.5 bg-red-600 rounded-xl font-semibold text-sm text-white hover:bg-red-700 transition-all disabled:opacity-60">
            {{ deleting ? 'Deleting...' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Plus, Search, Pencil, Trash2, X, Download, Users } from 'lucide-vue-next'

definePageMeta({
  middleware: 'auth',
  permissions: 'hrm.employees.view'
})

const { fetchEmployees, fetchDepartments, fetchDesignations, createEmployee, updateEmployee, deleteEmployee: apiDeleteEmployee } = useHrm()

const employees = ref([])
const departments = ref([])
const designations = ref([])
const loading = ref(true)
const saving = ref(false)
const deleting = ref(false)
const drawerOpen = ref(false)
const editingEmployee = ref(null)
const deleteTarget = ref(null)
const formError = ref('')
const currentPage = ref(1)
const pagination = ref({})

const filters = reactive({ search: '', department_id: '', status: '' })

const form = reactive({
  name: '', email: '', phone: '', employee_id: '', joining_date: '',
  department_id: '', designation_id: '', salary: '', gender: '', status: 'active', address: '',
})

const resetForm = () => {
  Object.keys(form).forEach(k => form[k] = k === 'status' ? 'active' : '')
  formError.value = ''
}

const openDrawer = (employee = null) => {
  editingEmployee.value = employee
  if (employee) {
    Object.keys(form).forEach(k => form[k] = employee[k] ?? '')
    form.department_id = employee.department_id ?? ''
    form.designation_id = employee.designation_id ?? ''
  } else {
    resetForm()
  }
  drawerOpen.value = true
}

const loadEmployees = async (page = 1) => {
  loading.value = true
  try {
    const params = { page }
    if (filters.search) params.search = filters.search
    if (filters.department_id) params.department_id = filters.department_id
    if (filters.status) params.status = filters.status

    const data = await fetchEmployees(params)
    employees.value = data.data ?? []
    pagination.value = { from: data.from, to: data.to, total: data.total, prev_page_url: data.prev_page_url, next_page_url: data.next_page_url }
    currentPage.value = page
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const changePage = (page) => loadEmployees(page)

let searchTimeout
const debouncedLoad = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => loadEmployees(), 400)
}

const saveEmployee = async () => {
  saving.value = true
  formError.value = ''
  try {
    const fd = new FormData()
    Object.entries(form).forEach(([k, v]) => { if (v !== '' && v !== null) fd.append(k, v) })
    if (editingEmployee.value) {
      await updateEmployee(editingEmployee.value.id, fd)
    } else {
      await createEmployee(fd)
    }
    drawerOpen.value = false
    await loadEmployees()
  } catch (e) {
    formError.value = e?.data?.message || 'An error occurred. Please check your inputs.'
  } finally {
    saving.value = false
  }
}

const confirmDelete = (employee) => { deleteTarget.value = employee }

const deleteEmployee = async () => {
  deleting.value = true
  try {
    await apiDeleteEmployee(deleteTarget.value.id)
    deleteTarget.value = null
    await loadEmployees()
  } catch (e) {
    console.error(e)
  } finally {
    deleting.value = false
  }
}

const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '—'

const getStatusClass = (status) => {
  const base = 'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider'
  if (status === 'active') return `${base} bg-green-100 text-green-700`
  if (status === 'inactive') return `${base} bg-gray-100 text-gray-600`
  if (status === 'on_leave') return `${base} bg-orange-100 text-orange-700`
  return `${base} bg-gray-100 text-gray-500`
}

onMounted(async () => {
  await Promise.all([
    loadEmployees(),
    fetchDepartments().then(d => departments.value = d),
    fetchDesignations().then(d => designations.value = d),
  ])
})

</script>

<style scoped>
.animate-slide-left { animation: slideLeft 0.3s ease-out; }
@keyframes slideLeft { from { transform: translateX(100%); } to { transform: translateX(0); } }
</style>
