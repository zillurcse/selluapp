<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Leave Management</h1>
        <p class="text-gray-500 dark:text-slate-400 mt-1">Review, approve, or reject employee leave requests.</p>
      </div>
      <button @click="openDrawer()" class="inline-flex items-center px-4 py-2 bg-gray-900 rounded-xl font-semibold text-sm text-white hover:bg-gray-800 shadow-sm">
        <Plus class="w-4 h-4 mr-2" />New Leave Request
      </button>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm mb-6 flex flex-wrap items-center gap-4">
      <select v-model="filters.status" @change="load" class="px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="approved">Approved</option>
        <option value="rejected">Rejected</option>
        <option value="cancelled">Cancelled</option>
      </select>
      <select v-model="filters.leave_type" @change="load" class="px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm">
        <option value="">All Types</option>
        <option value="sick">Sick</option>
        <option value="casual">Casual</option>
        <option value="annual">Annual</option>
        <option value="maternity">Maternity</option>
        <option value="paternity">Paternity</option>
        <option value="other">Other</option>
      </select>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
      <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50/50 dark:bg-slate-800/50">
          <tr>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Employee</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Leave Type</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Duration</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Days</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 dark:divide-slate-800">
          <tr v-if="loading" v-for="i in 4" :key="i" class="animate-pulse">
            <td class="px-6 py-4"><div class="h-3 w-32 bg-gray-200 rounded"></div></td>
            <td class="px-6 py-4"><div class="h-3 w-20 bg-gray-200 rounded"></div></td>
            <td class="px-6 py-4"><div class="h-3 w-32 bg-gray-200 rounded"></div></td>
            <td class="px-6 py-4"><div class="h-3 w-8 bg-gray-200 rounded"></div></td>
            <td class="px-6 py-4"><div class="h-4 w-16 bg-gray-200 rounded-full"></div></td>
            <td class="px-6 py-4"></td>
          </tr>
          <tr v-else-if="!leaves.length">
            <td colspan="6" class="px-6 py-12 text-center">
              <FileClock class="w-10 h-10 mx-auto mb-2 text-gray-300" />
              <p class="text-sm text-gray-400">No leave requests found.</p>
            </td>
          </tr>
          <tr v-else v-for="leave in leaves" :key="leave.id" class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors group">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-orange-50 flex items-center justify-center text-orange-600 font-bold uppercase text-xs">{{ leave.employee?.name?.charAt(0) }}</div>
                <div>
                  <div class="text-sm font-semibold text-gray-900 dark:text-slate-100">{{ leave.employee?.name }}</div>
                  <div class="text-xs text-gray-500 dark:text-slate-400">#{{ leave.employee?.employee_id }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-700 dark:text-slate-300 capitalize">{{ leave.leave_type }}</td>
            <td class="px-6 py-4 text-sm text-gray-700 dark:text-slate-300">{{ formatDate(leave.start_date) }} – {{ formatDate(leave.end_date) }}</td>
            <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-slate-100">{{ leave.total_days }}d</td>
            <td class="px-6 py-4"><span :class="getLeaveStatusClass(leave.status)">{{ leave.status }}</span></td>
            <td class="px-6 py-4 text-right">
              <div v-if="leave.status === 'pending'" class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button @click="doAction(leave, 'approve')" class="px-3 py-1.5 bg-green-50 text-green-700 rounded-lg text-xs font-bold hover:bg-green-100 transition-all">Approve</button>
                <button @click="openRejectModal(leave)" class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-bold hover:bg-red-100 transition-all">Reject</button>
              </div>
              <button v-else-if="leave.status === 'approved'" @click="doAction(leave, 'cancel')" class="opacity-0 group-hover:opacity-100 transition-opacity px-3 py-1.5 bg-gray-50 text-gray-600 rounded-lg text-xs font-bold hover:bg-gray-100">Cancel</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- New Leave Drawer -->
    <div v-if="drawerOpen" class="fixed inset-0 z-[60] overflow-hidden">
      <div class="absolute inset-0 bg-black/20 backdrop-blur-sm" @click="drawerOpen = false"></div>
      <div class="absolute inset-y-0 right-0 max-w-md w-full flex">
        <div class="relative w-full bg-white dark:bg-slate-900 shadow-2xl flex flex-col h-full animate-slide-left">
          <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-gray-50/50 dark:bg-slate-800/50">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">New Leave Request</h2>
            <button @click="drawerOpen = false" class="p-2 hover:bg-gray-200 rounded-full transition-colors text-gray-400"><X class="w-5 h-5" /></button>
          </div>
          <div class="flex-1 p-6 space-y-5">
            <div v-if="formError" class="bg-red-50 border border-red-100 text-red-600 text-sm px-4 py-3 rounded-xl">{{ formError }}</div>
            <div>
              <label class="block text-sm font-bold text-gray-900 mb-2">Employee *</label>
              <select v-model="form.employee_id" class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm">
                <option value="">Select Employee</option>
                <option v-for="emp in employees" :key="emp.id" :value="emp.id">{{ emp.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-bold text-gray-900 mb-2">Leave Type *</label>
              <select v-model="form.leave_type" class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm">
                <option value="">Select Type</option>
                <option value="sick">Sick Leave</option>
                <option value="casual">Casual Leave</option>
                <option value="annual">Annual Leave</option>
                <option value="maternity">Maternity Leave</option>
                <option value="paternity">Paternity Leave</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Start Date *</label>
                <input v-model="form.start_date" type="date" class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm" />
              </div>
              <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">End Date *</label>
                <input v-model="form.end_date" type="date" class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm" />
              </div>
            </div>
            <div>
              <label class="block text-sm font-bold text-gray-900 mb-2">Reason</label>
              <textarea v-model="form.reason" rows="3" placeholder="Reason for leave..." class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm resize-none"></textarea>
            </div>
          </div>
          <div class="p-10 border-t border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/50 flex items-center gap-3">
            <button @click="drawerOpen = false" class="flex-1 px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl font-semibold text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-all">Cancel</button>
            <button @click="save" :disabled="saving" class="flex-1 px-4 py-2.5 bg-gray-900 rounded-xl font-semibold text-sm text-white hover:bg-gray-800 transition-all shadow-lg disabled:opacity-60">
              {{ saving ? 'Submitting...' : 'Submit Request' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Reject Modal -->
    <div v-if="rejectTarget" class="fixed inset-0 z-[70] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" @click="rejectTarget = null"></div>
      <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl p-6 w-full max-w-sm">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Reject Leave Request</h3>
        <textarea v-model="rejectionReason" rows="3" placeholder="Reason for rejection..." class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm resize-none mb-4"></textarea>
        <div class="flex gap-3">
          <button @click="rejectTarget = null" class="flex-1 px-4 py-2.5 bg-gray-100 rounded-xl font-semibold text-sm text-gray-700">Cancel</button>
          <button @click="doAction(rejectTarget, 'reject')" :disabled="actioning" class="flex-1 px-4 py-2.5 bg-red-600 rounded-xl font-semibold text-sm text-white disabled:opacity-60">
            {{ actioning ? 'Rejecting...' : 'Reject' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Plus, X, FileClock } from 'lucide-vue-next'

definePageMeta({
  middleware: 'auth',
  permissions: 'hrm.leaves.view'
})

const { fetchLeaves, fetchEmployees, createLeave, leaveAction } = useHrm()

const leaves = ref([])
const employees = ref([])
const loading = ref(true)
const drawerOpen = ref(false)
const saving = ref(false)
const actioning = ref(false)
const rejectTarget = ref(null)
const rejectionReason = ref('')
const formError = ref('')
const filters = reactive({ status: '', leave_type: '' })
const form = reactive({ employee_id: '', leave_type: '', start_date: '', end_date: '', reason: '' })

const openDrawer = () => { Object.keys(form).forEach(k => form[k] = ''); formError.value = ''; drawerOpen.value = true }
const openRejectModal = (leave) => { rejectTarget.value = leave; rejectionReason.value = '' }

const load = async () => {
  loading.value = true
  try {
    const params = {}
    if (filters.status) params.status = filters.status
    if (filters.leave_type) params.leave_type = filters.leave_type
    const data = await fetchLeaves(params)
    leaves.value = data.data ?? data
  } catch (e) { console.error(e) } finally { loading.value = false }
}

const save = async () => {
  saving.value = true
  formError.value = ''
  try {
    await createLeave(form)
    drawerOpen.value = false
    await load()
  } catch (e) {
    formError.value = e?.data?.message || 'An error occurred.'
  } finally { saving.value = false }
}

const doAction = async (leave, action) => {
  actioning.value = true
  try {
    await leaveAction(leave.id, action, action === 'reject' ? rejectionReason.value : undefined)
    rejectTarget.value = null
    await load()
  } catch (e) { console.error(e) } finally { actioning.value = false }
}

const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) : '—'

const getLeaveStatusClass = (status) => {
  const base = 'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider'
  if (status === 'approved') return `${base} bg-green-100 text-green-700`
  if (status === 'rejected') return `${base} bg-red-100 text-red-600`
  if (status === 'pending') return `${base} bg-yellow-100 text-yellow-700`
  return `${base} bg-gray-100 text-gray-500`
}

onMounted(async () => {
  await Promise.all([
    load(),
    fetchEmployees().then(r => employees.value = r.data ?? r),
  ])
})
</script>

<style scoped>
.animate-slide-left { animation: slideLeft 0.3s ease-out; }
@keyframes slideLeft { from { transform: translateX(100%); } to { transform: translateX(0); } }
</style>
