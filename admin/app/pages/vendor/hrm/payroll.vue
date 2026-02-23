<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Payroll</h1>
        <p class="text-gray-500 dark:text-slate-400 mt-1">Process and manage employee salaries and payments.</p>
      </div>
      <div class="flex items-center gap-3">
        <input v-model="selectedMonth" type="month" @change="load" class="px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl text-sm focus:ring-0 shadow-sm dark:text-slate-200" />
        <button @click="generate" :disabled="generating" class="inline-flex items-center px-4 py-2 bg-gray-900 rounded-xl font-semibold text-sm text-white hover:bg-gray-800 shadow-sm disabled:opacity-60">
          <RefreshCw class="w-4 h-4 mr-2" :class="{ 'animate-spin': generating }" />{{ generating ? 'Generating...' : 'Generate Payroll' }}
        </button>
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm">
        <div class="text-xs text-gray-500 dark:text-slate-400 font-medium mb-2">Total Employees</div>
        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ payrollSummary.total_employees ?? 0 }}</div>
      </div>
      <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm">
        <div class="text-xs text-gray-500 dark:text-slate-400 font-medium mb-2">Total Net Salary</div>
        <div class="text-2xl font-bold text-emerald-600">{{ formatCurrency(payrollSummary.total_net_salary) }}</div>
      </div>
      <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm">
        <div class="text-xs text-gray-500 dark:text-slate-400 font-medium mb-2">Paid</div>
        <div class="text-2xl font-bold text-green-600">{{ formatCurrency(payrollSummary.total_paid) }}</div>
      </div>
      <div class="bg-white dark:bg-slate-900 p-5 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm">
        <div class="text-xs text-gray-500 dark:text-slate-400 font-medium mb-2">Pending</div>
        <div class="text-2xl font-bold text-orange-500">{{ formatCurrency(payrollSummary.total_pending) }}</div>
      </div>
    </div>

    <!-- Payroll Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
      <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50/50 dark:bg-slate-800/50">
          <tr>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Employee</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Basic</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Allowances</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Deductions</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Net Salary</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 dark:divide-slate-800">
          <tr v-if="loading" v-for="i in 4" :key="i" class="animate-pulse">
            <td class="px-6 py-4"><div class="h-3 w-32 bg-gray-200 rounded"></div></td>
            <td class="px-6 py-4"><div class="h-3 w-20 bg-gray-200 rounded"></div></td>
            <td class="px-6 py-4"><div class="h-3 w-16 bg-gray-200 rounded"></div></td>
            <td class="px-6 py-4"><div class="h-3 w-16 bg-gray-200 rounded"></div></td>
            <td class="px-6 py-4"><div class="h-3 w-20 bg-gray-200 rounded"></div></td>
            <td class="px-6 py-4"><div class="h-4 w-16 bg-gray-200 rounded-full"></div></td>
            <td class="px-6 py-4"></td>
          </tr>
          <tr v-else-if="!payrolls.length">
            <td colspan="7" class="px-6 py-12 text-center">
              <Banknote class="w-10 h-10 mx-auto mb-2 text-gray-300" />
              <p class="text-sm text-gray-400">No payroll records for this month. Click "Generate Payroll" to start.</p>
            </td>
          </tr>
          <tr v-else v-for="p in payrolls" :key="p.id" class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors group">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600 font-bold uppercase text-xs">{{ p.employee?.name?.charAt(0) }}</div>
                <div>
                  <div class="text-sm font-semibold text-gray-900 dark:text-slate-100">{{ p.employee?.name }}</div>
                  <div class="text-xs text-gray-500 dark:text-slate-400">{{ p.present_days }}/{{ p.working_days }} days</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-700 dark:text-slate-300">{{ formatCurrency(p.basic_salary) }}</td>
            <td class="px-6 py-4 text-sm text-green-600">+{{ formatCurrency(p.allowances + p.bonuses) }}</td>
            <td class="px-6 py-4 text-sm text-red-500">-{{ formatCurrency(p.deductions + p.tax) }}</td>
            <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-slate-100">{{ formatCurrency(p.net_salary) }}</td>
            <td class="px-6 py-4"><span :class="getPayrollStatusClass(p.status)">{{ p.status }}</span></td>
            <td class="px-6 py-4 text-right">
              <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button v-if="p.status !== 'paid'" @click="openEditModal(p)" class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all"><Pencil class="w-4 h-4" /></button>
                <button v-if="p.status !== 'paid'" @click="doPay(p)" :disabled="payingId === p.id" class="px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-bold hover:bg-emerald-100 transition-all disabled:opacity-60">
                  {{ payingId === p.id ? '...' : 'Mark Paid' }}
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Edit Modal -->
    <div v-if="editTarget" class="fixed inset-0 z-[70] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" @click="editTarget = null"></div>
      <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl p-6 w-full max-w-sm">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Adjust Payroll — {{ editTarget?.employee?.name }}</h3>
        <div class="space-y-3">
          <div><label class="block text-sm font-bold text-gray-900 mb-1">Allowances</label><input v-model.number="editForm.allowances" type="number" class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm" /></div>
          <div><label class="block text-sm font-bold text-gray-900 mb-1">Bonuses</label><input v-model.number="editForm.bonuses" type="number" class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm" /></div>
          <div><label class="block text-sm font-bold text-gray-900 mb-1">Deductions</label><input v-model.number="editForm.deductions" type="number" class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm" /></div>
          <div><label class="block text-sm font-bold text-gray-900 mb-1">Notes</label><textarea v-model="editForm.notes" rows="2" class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm resize-none"></textarea></div>
        </div>
        <div class="flex gap-3 mt-5">
          <button @click="editTarget = null" class="flex-1 px-4 py-2.5 bg-gray-100 rounded-xl font-semibold text-sm text-gray-700">Cancel</button>
          <button @click="doEdit" :disabled="updating" class="flex-1 px-4 py-2.5 bg-gray-900 rounded-xl font-semibold text-sm text-white disabled:opacity-60">
            {{ updating ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { RefreshCw, Pencil, Banknote } from 'lucide-vue-next'

definePageMeta({
  middleware: 'auth',
  permissions: 'hrm.payroll.view'
})

const { fetchPayroll, fetchPayrollSummary, generatePayroll, updatePayroll, markPayrollPaid } = useHrm()

const now = new Date()
const selectedMonth = ref(`${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`)
const payrolls = ref([])
const payrollSummary = ref({})
const loading = ref(true)
const generating = ref(false)
const updating = ref(false)
const payingId = ref(null)
const editTarget = ref(null)
const editForm = reactive({ allowances: 0, bonuses: 0, deductions: 0, notes: '' })

const load = async () => {
  loading.value = true
  try {
    const [p, s] = await Promise.all([
      fetchPayroll({ month: selectedMonth.value }),
      fetchPayrollSummary(selectedMonth.value),
    ])
    payrolls.value = p
    payrollSummary.value = s
  } catch (e) { console.error(e) } finally { loading.value = false }
}

const generate = async () => {
  generating.value = true
  try {
    await generatePayroll(selectedMonth.value)
    await load()
  } catch (e) { console.error(e) } finally { generating.value = false }
}

const openEditModal = (p) => {
  editTarget.value = p
  editForm.allowances = p.allowances
  editForm.bonuses = p.bonuses
  editForm.deductions = p.deductions
  editForm.notes = p.notes ?? ''
}

const doEdit = async () => {
  updating.value = true
  try {
    await updatePayroll(editTarget.value.id, editForm)
    editTarget.value = null
    await load()
  } catch (e) { console.error(e) } finally { updating.value = false }
}

const doPay = async (p) => {
  payingId.value = p.id
  try {
    await markPayrollPaid(p.id)
    await load()
  } catch (e) { console.error(e) } finally { payingId.value = null }
}

const formatCurrency = (v) => {
  if (!v && v !== 0) return '—'
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(v)
}

const getPayrollStatusClass = (status) => {
  const base = 'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider'
  if (status === 'paid') return `${base} bg-green-100 text-green-700`
  if (status === 'processing') return `${base} bg-blue-100 text-blue-600`
  return `${base} bg-orange-100 text-orange-600`
}

onMounted(load)
</script>
