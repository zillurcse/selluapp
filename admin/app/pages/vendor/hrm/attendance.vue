<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Attendance</h1>
        <p class="text-gray-500 dark:text-slate-400 mt-1">Track daily check-ins, absences, and working hours.</p>
      </div>
      <div class="flex items-center gap-3">
        <input v-model="selectedDate" type="date" @change="load" class="px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl text-sm focus:ring-0 shadow-sm dark:text-slate-200" />
        <button @click="openDrawer()" class="inline-flex items-center px-4 py-2 bg-gray-900 rounded-xl font-semibold text-sm text-white hover:bg-gray-800 shadow-sm">
          <Plus class="w-4 h-4 mr-2" />Mark Attendance
        </button>
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
      <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm text-center">
        <div class="text-2xl font-bold text-green-600">{{ summary.present ?? '—' }}</div>
        <div class="text-xs text-gray-500 dark:text-slate-400 mt-1 font-medium">Present</div>
      </div>
      <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm text-center">
        <div class="text-2xl font-bold text-red-500">{{ summary.absent ?? '—' }}</div>
        <div class="text-xs text-gray-500 dark:text-slate-400 mt-1 font-medium">Absent</div>
      </div>
      <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm text-center">
        <div class="text-2xl font-bold text-orange-500">{{ summary.late ?? '—' }}</div>
        <div class="text-xs text-gray-500 dark:text-slate-400 mt-1 font-medium">Late</div>
      </div>
      <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm text-center">
        <div class="text-2xl font-bold text-gray-400">{{ summary.not_marked ?? '—' }}</div>
        <div class="text-xs text-gray-500 dark:text-slate-400 mt-1 font-medium">Not Marked</div>
      </div>
    </div>

    <!-- Attendance Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm overflow-hidden">
      <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50/50 dark:bg-slate-800/50">
          <tr>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Employee</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Date</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Check In</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Check Out</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Hours</th>
            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 dark:divide-slate-800">
          <tr v-if="loading" v-for="i in 4" :key="i" class="animate-pulse">
            <td class="px-6 py-4"><div class="h-3 w-32 bg-gray-200 rounded"></div></td>
            <td class="px-6 py-4"><div class="h-3 w-24 bg-gray-200 rounded"></div></td>
            <td class="px-6 py-4"><div class="h-3 w-16 bg-gray-200 rounded"></div></td>
            <td class="px-6 py-4"><div class="h-3 w-16 bg-gray-200 rounded"></div></td>
            <td class="px-6 py-4"><div class="h-3 w-12 bg-gray-200 rounded"></div></td>
            <td class="px-6 py-4"><div class="h-4 w-16 bg-gray-200 rounded-full"></div></td>
          </tr>
          <tr v-else-if="!records.length">
            <td colspan="6" class="px-6 py-12 text-center">
              <CalendarCheck2 class="w-10 h-10 mx-auto mb-2 text-gray-300" />
              <p class="text-sm text-gray-400">No attendance records for this date.</p>
            </td>
          </tr>
          <tr v-else v-for="rec in records" :key="rec.id" class="hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold uppercase text-xs">{{ rec.employee?.name?.charAt(0) }}</div>
                <div>
                  <div class="text-sm font-semibold text-gray-900 dark:text-slate-100">{{ rec.employee?.name }}</div>
                  <div class="text-xs text-gray-500 dark:text-slate-400">{{ rec.employee?.employee_id }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-700 dark:text-slate-300">{{ formatDate(rec.date) }}</td>
            <td class="px-6 py-4 text-sm text-gray-700 dark:text-slate-300">{{ rec.check_in ?? '—' }}</td>
            <td class="px-6 py-4 text-sm text-gray-700 dark:text-slate-300">{{ rec.check_out ?? '—' }}</td>
            <td class="px-6 py-4 text-sm text-gray-700 dark:text-slate-300">{{ rec.working_hours ? rec.working_hours + 'h' : '—' }}</td>
            <td class="px-6 py-4"><span :class="getStatusClass(rec.status)">{{ rec.status?.replace('_', ' ') }}</span></td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Mark Attendance Drawer -->
    <div v-if="drawerOpen" class="fixed inset-0 z-[60] overflow-hidden">
      <div class="absolute inset-0 bg-black/20 backdrop-blur-sm" @click="drawerOpen = false"></div>
      <div class="absolute inset-y-0 right-0 max-w-md w-full flex">
        <div class="relative w-full bg-white dark:bg-slate-900 shadow-2xl flex flex-col h-full animate-slide-left">
          <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-gray-50/50 dark:bg-slate-800/50">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Mark Attendance</h2>
            <button @click="drawerOpen = false" class="p-2 hover:bg-gray-200 rounded-full transition-colors text-gray-400"><X class="w-5 h-5" /></button>
          </div>
          <div class="flex-1 p-6 space-y-5">
            <div v-if="formError" class="bg-red-50 border border-red-100 text-red-600 text-sm px-4 py-3 rounded-xl">{{ formError }}</div>
            <div>
              <label class="block text-sm font-bold text-gray-900 mb-2">Employee *</label>
              <select v-model="form.employee_id" class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm transition-all">
                <option value="">Select Employee</option>
                <option v-for="emp in employees" :key="emp.id" :value="emp.id">{{ emp.name }} ({{ emp.employee_id }})</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-bold text-gray-900 mb-2">Date *</label>
              <input v-model="form.date" type="date" class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm transition-all" />
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Check In</label>
                <input v-model="form.check_in" type="time" class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm transition-all" />
              </div>
              <div>
                <label class="block text-sm font-bold text-gray-900 mb-2">Check Out</label>
                <input v-model="form.check_out" type="time" class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm transition-all" />
              </div>
            </div>
            <div>
              <label class="block text-sm font-bold text-gray-900 mb-2">Status *</label>
              <select v-model="form.status" class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm transition-all">
                <option value="present">Present</option>
                <option value="absent">Absent</option>
                <option value="late">Late</option>
                <option value="half_day">Half Day</option>
                <option value="holiday">Holiday</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-bold text-gray-900 mb-2">Notes</label>
              <textarea v-model="form.notes" rows="2" placeholder="Optional notes..." class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm transition-all resize-none"></textarea>
            </div>
          </div>
          <div class="p-10 border-t border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/50 flex items-center gap-3">
            <button @click="drawerOpen = false" class="flex-1 px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl font-semibold text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-all">Cancel</button>
            <button @click="save" :disabled="saving" class="flex-1 px-4 py-2.5 bg-gray-900 rounded-xl font-semibold text-sm text-white hover:bg-gray-800 transition-all shadow-lg disabled:opacity-60">
              {{ saving ? 'Saving...' : 'Record Attendance' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Plus, X, CalendarCheck2 } from 'lucide-vue-next'

const { fetchAttendance, fetchAttendanceSummary, saveAttendance, fetchEmployees } = useHrm()

const today = new Date().toISOString().split('T')[0]
const selectedDate = ref(today)
const records = ref([])
const summary = ref({})
const employees = ref([])
const loading = ref(true)
const drawerOpen = ref(false)
const saving = ref(false)
const formError = ref('')

const form = reactive({ employee_id: '', date: today, check_in: '', check_out: '', status: 'present', notes: '' })

const openDrawer = () => {
  form.date = selectedDate.value
  formError.value = ''
  drawerOpen.value = true
}

const load = async () => {
  loading.value = true
  try {
    const [r, s] = await Promise.all([
      fetchAttendance({ date: selectedDate.value }),
      fetchAttendanceSummary(selectedDate.value),
    ])
    records.value = r
    summary.value = s
  } catch (e) { console.error(e) } finally { loading.value = false }
}

const save = async () => {
  saving.value = true
  formError.value = ''
  try {
    await saveAttendance(form)
    drawerOpen.value = false
    await load()
  } catch (e) {
    formError.value = e?.data?.message || 'An error occurred.'
  } finally { saving.value = false }
}

const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—'

const getStatusClass = (status) => {
  const base = 'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider'
  if (status === 'present') return `${base} bg-green-100 text-green-700`
  if (status === 'absent') return `${base} bg-red-100 text-red-600`
  if (status === 'late') return `${base} bg-orange-100 text-orange-700`
  if (status === 'half_day') return `${base} bg-yellow-100 text-yellow-700`
  return `${base} bg-gray-100 text-gray-500`
}

onMounted(async () => {
  await Promise.all([
    load(),
    fetchEmployees({ status: 'active' }).then(r => employees.value = r.data ?? r),
  ])
})
</script>

<style scoped>
.animate-slide-left { animation: slideLeft 0.3s ease-out; }
@keyframes slideLeft { from { transform: translateX(100%); } to { transform: translateX(0); } }
</style>
