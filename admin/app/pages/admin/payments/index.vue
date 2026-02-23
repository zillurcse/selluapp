<template>
  <div class="p-8 bg-[#f8fafc] dark:bg-slate-950 min-h-full">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500 mb-2 font-bold uppercase tracking-widest">
          <Shield class="w-3.5 h-3.5" />
          <NuxtLink to="/admin" class="hover:text-indigo-500 transition-colors">Super Admin</NuxtLink>
          <ChevronRight class="w-3 h-3" />
          <span class="text-slate-600 dark:text-slate-300">Payments</span>
        </div>
        <h1 class="text-3xl font-[1000] text-slate-900 dark:text-white tracking-tight">Payment Records</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Track all incoming payments across the platform.</p>
      </div>
      <button @click="openCreateModal"
        class="flex items-center gap-2 px-5 py-2.5 bg-slate-900 dark:bg-indigo-600 text-white rounded-xl text-sm font-bold hover:scale-105 transition-all shadow-lg shadow-slate-900/20">
        <Plus class="w-4 h-4" /> Record Payment
      </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
      <div v-for="s in statCards" :key="s.label" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-5">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">{{ s.label }}</p>
        <div v-if="!statsLoading" class="text-2xl font-[1000] text-slate-900 dark:text-white">{{ s.value }}</div>
        <div v-else class="h-7 w-24 bg-slate-100 dark:bg-slate-800 rounded animate-pulse mt-1"></div>
        <div class="flex items-center gap-1.5 mt-2">
          <div class="w-2 h-2 rounded-full flex-shrink-0" :class="s.dot"></div>
          <span class="text-[10px] font-bold text-slate-400">{{ s.sub }}</span>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-3 mb-6">
      <div class="relative flex-1 min-w-[200px] max-w-sm">
        <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
        <input v-model="searchQuery" @input="debouncedSearch" type="text" placeholder="Search by ref, name…"
          class="w-full pl-10 pr-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
      </div>
      <select v-model="statusFilter" @change="fetchPayments"
        class="px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all">
        <option value="">All Statuses</option>
        <option value="completed">Completed</option>
        <option value="pending">Pending</option>
        <option value="failed">Failed</option>
        <option value="refunded">Refunded</option>
      </select>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm">
      <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 hidden md:grid grid-cols-12 gap-4">
        <div class="col-span-3 text-xs font-black text-slate-400 uppercase tracking-widest">Vendor</div>
        <div class="col-span-2 text-xs font-black text-slate-400 uppercase tracking-widest">Ref</div>
        <div class="col-span-2 text-xs font-black text-slate-400 uppercase tracking-widest">Amount</div>
        <div class="col-span-2 text-xs font-black text-slate-400 uppercase tracking-widest">Method</div>
        <div class="col-span-2 text-xs font-black text-slate-400 uppercase tracking-widest">Status</div>
        <div class="col-span-1 text-xs font-black text-slate-400 uppercase tracking-widest text-right">Act.</div>
      </div>

      <div v-if="loading" class="divide-y divide-slate-100 dark:divide-slate-800">
        <div v-for="i in 8" :key="i" class="px-6 py-4 flex items-center gap-4">
          <div class="h-10 flex-1 bg-slate-100 dark:bg-slate-800 rounded-xl animate-pulse"></div>
        </div>
      </div>

      <div v-else-if="payments.length" class="divide-y divide-slate-100 dark:divide-slate-800">
        <div v-for="p in payments" :key="p.id"
          class="px-6 py-4 md:grid md:grid-cols-12 md:gap-4 flex flex-col gap-2 hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-all group">

          <div class="col-span-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white text-xs font-black flex-shrink-0">
              {{ (p.user?.name || 'U').charAt(0).toUpperCase() }}
            </div>
            <div class="min-w-0">
              <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ p.user?.name || '—' }}</p>
              <p class="text-[10px] text-slate-400 truncate">{{ p.user?.email }}</p>
            </div>
          </div>

          <div class="col-span-2 flex items-center">
            <span class="text-xs font-mono font-bold text-slate-500 dark:text-slate-400">{{ p.payment_ref || '—' }}</span>
          </div>

          <div class="col-span-2 flex items-center">
            <span class="text-sm font-[1000] text-slate-900 dark:text-white">৳{{ Number(p.amount).toLocaleString() }}</span>
          </div>

          <div class="col-span-2 flex items-center">
            <span class="text-xs font-semibold text-slate-500 capitalize">{{ p.payment_method || 'Manual' }}</span>
          </div>

          <div class="col-span-2 flex items-center">
            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider" :class="statusClass(p.status)">
              {{ p.status }}
            </span>
          </div>

          <div class="col-span-1 flex items-center justify-end gap-1.5">
            <button @click="openEditModal(p)" class="w-7 h-7 flex items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-400 hover:bg-indigo-600 hover:text-white transition-all opacity-0 group-hover:opacity-100">
              <Pencil class="w-3.5 h-3.5" />
            </button>
            <button @click="confirmDelete(p)" class="w-7 h-7 flex items-center justify-center rounded-lg bg-rose-50 text-rose-400 hover:bg-rose-500 hover:text-white transition-all opacity-0 group-hover:opacity-100">
              <Trash2 class="w-3.5 h-3.5" />
            </button>
          </div>
        </div>
      </div>

      <div v-else class="flex flex-col items-center justify-center py-20">
        <div class="w-14 h-14 bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-4">
          <CreditCard class="w-7 h-7 text-slate-300 dark:text-slate-600" />
        </div>
        <h3 class="text-base font-[1000] text-slate-900 dark:text-white mb-1">No payments yet</h3>
        <p class="text-slate-400 text-sm">Record the first payment to get started.</p>
      </div>

      <!-- Pagination -->
      <div v-if="pagination?.last_page > 1"
        class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
        <p class="text-xs text-slate-400 font-semibold">Showing {{ pagination.from }}–{{ pagination.to }} of {{ pagination.total }}</p>
        <div class="flex gap-2">
          <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1"
            class="px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 disabled:opacity-40 hover:bg-slate-200 transition-all">Prev</button>
          <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page"
            class="px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 disabled:opacity-40 hover:bg-slate-200 transition-all">Next</button>
        </div>
      </div>
    </div>

    <!-- Create / Edit Modal -->
    <Teleport to="body">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModal"></div>
        <div class="relative w-full max-w-xl bg-white dark:bg-slate-900 rounded-3xl shadow-2xl overflow-hidden max-h-[92vh] flex flex-col">

          <!-- Modal Header -->
          <div class="flex items-center justify-between px-8 py-6 border-b border-slate-100 dark:border-slate-800 flex-shrink-0">
            <div>
              <h2 class="text-xl font-[1000] text-slate-900 dark:text-white">{{ editingItem ? 'Edit Payment' : 'Record Payment' }}</h2>
              <p class="text-xs text-slate-400 mt-0.5">{{ editingItem ? 'Update payment details' : 'Link a payment to a vendor subscription' }}</p>
            </div>
            <button @click="closeModal" class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-slate-400 hover:bg-slate-200 transition-all">
              <X class="w-4 h-4" />
            </button>
          </div>

          <!-- Modal Body -->
          <div class="p-8 space-y-6 overflow-y-auto">

            <!-- ① Vendor Picker -->
            <div>
              <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-3">Select Vendor *</label>
              <div class="relative mb-2">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                <input v-model="vendorSearch" type="text" placeholder="Search vendor by name or email…"
                  class="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
              </div>
              <div class="max-h-40 overflow-y-auto rounded-xl border border-slate-100 dark:border-slate-800 divide-y divide-slate-50 dark:divide-slate-800/60">
                <div v-if="vendorsLoading" class="p-4 flex items-center justify-center">
                  <Loader2 class="w-5 h-5 animate-spin text-slate-400" />
                </div>
                <div v-else-if="filteredVendors.length === 0" class="p-4 text-center text-sm text-slate-400">No vendors found.</div>
                <button v-for="v in filteredVendors" :key="v.id" @click="selectVendor(v)" type="button"
                  class="w-full flex items-center gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all text-left"
                  :class="form.user_id === v.id ? 'bg-indigo-50 dark:bg-indigo-500/10' : ''">
                  <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white text-xs font-black flex-shrink-0">
                    {{ v.name.charAt(0).toUpperCase() }}
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ v.name }}</p>
                    <p class="text-[10px] text-slate-400 truncate">{{ v.email }}</p>
                  </div>
                  <Check v-if="form.user_id === v.id" class="w-4 h-4 text-indigo-600 flex-shrink-0" />
                </button>
              </div>
              <!-- Selected chip -->
              <div v-if="selectedVendor" class="mt-2 flex items-center gap-2 px-3 py-2 bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 rounded-xl">
                <div class="w-6 h-6 rounded-lg bg-indigo-600 flex items-center justify-center text-white text-[10px] font-black">{{ selectedVendor.name.charAt(0).toUpperCase() }}</div>
                <p class="text-xs font-bold text-indigo-700 dark:text-indigo-400">{{ selectedVendor.name }}</p>
                <span class="ml-auto text-[10px] text-indigo-400">{{ selectedVendor.email }}</span>
              </div>
            </div>

            <!-- ② Subscription Picker -->
            <div v-if="form.user_id">
              <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-3">Link to Subscription (optional)</label>
              <div v-if="subsLoading" class="h-12 bg-slate-50 dark:bg-slate-800 rounded-xl animate-pulse"></div>
              <div v-else-if="vendorSubs.length === 0" class="px-4 py-3 bg-slate-50 dark:bg-slate-800 rounded-xl text-xs text-slate-400 italic">No subscriptions found for this vendor.</div>
              <div v-else class="space-y-2">
                <label v-for="sub in vendorSubs" :key="sub.id"
                  class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all"
                  :class="subCardClass(sub.id)">
                  <input type="radio" :value="sub.id" v-model="form.subscription_id" @change="onSubChange(sub)" class="sr-only" />
                  <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-900 dark:text-white">{{ sub.package?.name || 'No Plan' }} — <span class="capitalize text-slate-400">{{ sub.billing_cycle }}</span></p>
                    <p class="text-[10px] text-slate-400">Inv: {{ sub.invoice_number || '—' }} · Status: {{ sub.status }}</p>
                  </div>
                  <p class="text-sm font-[1000] text-slate-900 dark:text-white flex-shrink-0">৳{{ Number(sub.amount).toLocaleString() }}</p>
                  <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all"
                    :class="form.subscription_id === sub.id ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300 dark:border-slate-600'">
                    <div v-if="form.subscription_id === sub.id" class="w-2 h-2 rounded-full bg-white"></div>
                  </div>
                </label>
              </div>
            </div>

            <!-- ③ Amount -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Amount (৳) *</label>
                <div class="relative">
                  <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">৳</span>
                  <input v-model.number="form.amount" type="number" min="0"
                    class="w-full pl-8 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
                </div>
              </div>
              <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Status *</label>
                <select v-model="form.status" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all">
                  <option value="pending">Pending</option>
                  <option value="completed">Completed</option>
                  <option value="failed">Failed</option>
                  <option value="refunded">Refunded</option>
                </select>
              </div>
            </div>

            <!-- ④ Method (button group) -->
            <div>
              <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-3">Payment Method</label>
              <div class="grid grid-cols-4 gap-2">
                <button v-for="m in paymentMethods" :key="m.value" type="button"
                  @click="form.payment_method = m.value"
                  class="flex flex-col items-center gap-1.5 py-3 rounded-xl border text-[10px] font-black uppercase tracking-wider transition-all"
                  :class="form.payment_method === m.value
                    ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'
                    : 'border-slate-100 dark:border-slate-800 text-slate-400 hover:border-indigo-200'">
                  <span class="text-base">{{ m.emoji }}</span>
                  {{ m.label }}
                </button>
              </div>
            </div>

            <!-- ⑤ Paid At & Notes -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Paid At</label>
                <input v-model="form.paid_at" type="datetime-local"
                  class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
              </div>
              <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Notes</label>
                <input v-model="form.notes" type="text" placeholder="Optional…"
                  class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
              </div>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="px-8 py-5 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3 flex-shrink-0">
            <button @click="closeModal" class="px-5 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-100 transition-all">Cancel</button>
            <button @click="saveItem" :disabled="saving || !form.user_id"
              class="flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
              <Loader2 v-if="saving" class="w-4 h-4 animate-spin" />
              <Check v-else class="w-4 h-4" />
              {{ saving ? 'Saving…' : (editingItem ? 'Update Payment' : 'Record Payment') }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Delete Modal -->
    <Teleport to="body">
      <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="deleteTarget = null"></div>
        <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl p-8 max-w-sm w-full">
          <div class="w-14 h-14 bg-rose-50 dark:bg-rose-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <Trash2 class="w-7 h-7 text-rose-500" />
          </div>
          <h3 class="text-lg font-[1000] text-center text-slate-900 dark:text-white mb-2">Delete Payment?</h3>
          <p class="text-sm text-slate-400 text-center mb-6">Payment <strong class="text-slate-700 dark:text-slate-200">{{ deleteTarget?.payment_ref }}</strong> will be removed permanently.</p>
          <div class="flex gap-3">
            <button @click="deleteTarget = null" class="flex-1 py-2.5 text-sm font-bold bg-slate-100 dark:bg-slate-800 rounded-xl hover:bg-slate-200 transition-all">Cancel</button>
            <button @click="deleteItem" :disabled="saving" class="flex-1 py-2.5 text-sm font-bold text-white bg-rose-500 rounded-xl hover:bg-rose-600 disabled:opacity-50 transition-all">
              {{ saving ? 'Deleting…' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>

<script setup>
import { Shield, ChevronRight, Plus, Search, Pencil, Trash2, CreditCard, X, Loader2, Check } from 'lucide-vue-next'
import { toast } from 'vue-sonner'

definePageMeta({
  middleware: 'auth',
  layout: 'default',
  permissions: 'admin.payments.view'
})

const { getAll, getHeaders } = useCrud()
const config = useRuntimeConfig()
const baseURL = config.public.apiBase

// ── Table state ───────────────────────────────────────────────────────
const payments    = ref([])
const pagination  = ref(null)
const loading     = ref(true)
const statsLoading= ref(true)
const saving      = ref(false)
const searchQuery = ref('')
const statusFilter= ref('')
const currentPage = ref(1)
const showModal   = ref(false)
const editingItem = ref(null)
const deleteTarget= ref(null)
const rawStats    = ref({})

// ── Vendor picker ─────────────────────────────────────────────────────
const allVendors     = ref([])
const vendorSearch   = ref('')
const vendorsLoading = ref(false)
const selectedVendor = ref(null)

const filteredVendors = computed(() => {
  if (!vendorSearch.value) return allVendors.value
  const q = vendorSearch.value.toLowerCase()
  return allVendors.value.filter(v =>
    v.name.toLowerCase().includes(q) || v.email.toLowerCase().includes(q)
  )
})

// ── Subscription picker ───────────────────────────────────────────────
const vendorSubs  = ref([])
const subsLoading = ref(false)

const subCardClass = (subId) =>
  form.value.subscription_id === subId
    ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 dark:border-indigo-500/50'
    : 'border-slate-100 dark:border-slate-800 hover:border-indigo-200 dark:hover:border-indigo-500/20'

// ── Payment methods ───────────────────────────────────────────────────
const paymentMethods = [
  { value: 'manual', label: 'Manual', emoji: '🧾' },
  { value: 'bkash',  label: 'bKash',  emoji: '📱' },
  { value: 'nagad',  label: 'Nagad',  emoji: '💳' },
  { value: 'card',   label: 'Card',   emoji: '💰' },
]

// ── Form ──────────────────────────────────────────────────────────────
const defaultForm = () => ({
  user_id: null, subscription_id: null, amount: 0,
  status: 'pending', payment_method: 'manual', notes: '', paid_at: ''
})
const form = ref(defaultForm())

// ── Stats ─────────────────────────────────────────────────────────────
const statCards = computed(() => [
  { label: 'Total Collected', value: '৳' + Number(rawStats.value.completed || 0).toLocaleString(), sub: 'Completed payments', dot: 'bg-emerald-500' },
  { label: 'This Month',      value: '৳' + Number(rawStats.value.this_month || 0).toLocaleString(), sub: 'Current billing cycle', dot: 'bg-indigo-500' },
  { label: 'Pending',         value: rawStats.value.pending || 0, sub: 'Awaiting settlement', dot: 'bg-amber-500' },
  { label: 'Failed',          value: rawStats.value.failed  || 0, sub: 'Transactions failed', dot: 'bg-rose-500' },
])

const statusClass = (s) => ({
  completed: 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400',
  pending:   'bg-amber-100  dark:bg-amber-500/20  text-amber-700  dark:text-amber-400',
  failed:    'bg-rose-100   dark:bg-rose-500/20   text-rose-600   dark:text-rose-400',
  refunded:  'bg-slate-100  dark:bg-slate-800     text-slate-500  dark:text-slate-400',
}[s] || 'bg-slate-100 text-slate-500')

// ── Fetch ─────────────────────────────────────────────────────────────
const fetchStats = async () => {
  statsLoading.value = true
  try { rawStats.value = await getAll('/admin/finance/payments/stats') } catch (e) {}
  finally { statsLoading.value = false }
}

const fetchPayments = async () => {
  loading.value = true
  try {
    const q = { page: currentPage.value }
    if (searchQuery.value) q.search = searchQuery.value
    if (statusFilter.value) q.status = statusFilter.value
    const res = await getAll('/admin/finance/payments', q)
    payments.value   = res.data || []
    pagination.value = res
  } catch (e) { toast.error('Failed to load payments') }
  finally { loading.value = false }
}

const fetchVendors = async () => {
  vendorsLoading.value = true
  try {
    const res = await getAll('/admin/vendors', { per_page: 200 })
    allVendors.value = res.data || []
  } catch (e) { console.error(e) }
  finally { vendorsLoading.value = false }
}

const fetchVendorSubs = async (userId) => {
  subsLoading.value = true
  vendorSubs.value  = []
  form.value.subscription_id = null
  try {
    const res = await getAll('/admin/finance/subscriptions', { user_id: userId, per_page: 50 })
    vendorSubs.value = res.data || []
  } catch (e) { console.error(e) }
  finally { subsLoading.value = false }
}

// ── Handlers ──────────────────────────────────────────────────────────
const selectVendor = (v) => {
  selectedVendor.value = v
  form.value.user_id   = v.id
  vendorSearch.value   = ''
  fetchVendorSubs(v.id)
}

const onSubChange = (sub) => { form.value.amount = sub.amount }

let timer = null
const debouncedSearch = () => { clearTimeout(timer); timer = setTimeout(() => { currentPage.value = 1; fetchPayments() }, 400) }
const changePage = (p) => { currentPage.value = p; fetchPayments() }

// ── Modal ─────────────────────────────────────────────────────────────
const openCreateModal = () => {
  editingItem.value    = null
  selectedVendor.value = null
  vendorSearch.value   = ''
  vendorSubs.value     = []
  form.value = defaultForm()
  showModal.value = true
}

const openEditModal = (item) => {
  editingItem.value    = item
  selectedVendor.value = item.user ? { id: item.user.id, name: item.user.name, email: item.user.email } : null
  vendorSearch.value   = ''
  form.value = {
    user_id:         item.user_id,
    subscription_id: item.subscription_id,
    amount:          item.amount,
    status:          item.status,
    payment_method:  item.payment_method || 'manual',
    notes:           item.notes || '',
    paid_at:         item.paid_at || '',
  }
  if (item.user_id) fetchVendorSubs(item.user_id)
  showModal.value = true
}

const closeModal = () => {
  showModal.value      = false
  editingItem.value    = null
  selectedVendor.value = null
  vendorSubs.value     = []
  form.value = defaultForm()
}

const saveItem = async () => {
  if (!form.value.user_id) { toast.error('Please select a vendor'); return }
  if (!form.value.amount)  { toast.error('Amount is required');      return }
  saving.value = true
  try {
    if (editingItem.value) {
      await $fetch(`${baseURL}/admin/finance/payments/${editingItem.value.id}`, { method: 'PUT', body: form.value, headers: getHeaders() })
      toast.success('Payment updated')
    } else {
      await $fetch(`${baseURL}/admin/finance/payments`, { method: 'POST', body: form.value, headers: getHeaders() })
      toast.success('Payment recorded')
    }
    closeModal()
    await Promise.all([fetchPayments(), fetchStats()])
  } catch (e) { toast.error(e.data?.message || 'Failed to save') }
  finally { saving.value = false }
}

const confirmDelete = (item) => { deleteTarget.value = item }
const deleteItem = async () => {
  saving.value = true
  try {
    await $fetch(`${baseURL}/admin/finance/payments/${deleteTarget.value.id}`, { method: 'DELETE', headers: getHeaders() })
    toast.success('Payment deleted')
    deleteTarget.value = null
    await Promise.all([fetchPayments(), fetchStats()])
  } catch (e) { toast.error('Failed to delete') }
  finally { saving.value = false }
}

onMounted(() => { fetchPayments(); fetchStats(); fetchVendors() })
</script>

