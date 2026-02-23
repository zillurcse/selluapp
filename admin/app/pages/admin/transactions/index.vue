<template>
  <div class="p-8 bg-[#f8fafc] dark:bg-slate-950 min-h-full">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500 mb-2 font-bold uppercase tracking-widest">
          <Shield class="w-3.5 h-3.5" />
          <NuxtLink to="/admin" class="hover:text-indigo-500 transition-colors">Super Admin</NuxtLink>
          <ChevronRight class="w-3 h-3" />
          <span class="text-slate-600 dark:text-slate-300">Transactions</span>
        </div>
        <h1 class="text-3xl font-[1000] text-slate-900 dark:text-white tracking-tight">Transactions</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Full ledger of all platform financial activity.</p>
      </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
      <div v-for="s in statCards" :key="s.label" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-5">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">{{ s.label }}</p>
        <div v-if="!statsLoading" class="text-2xl font-[1000] text-slate-900 dark:text-white">{{ s.value }}</div>
        <div v-else class="h-7 w-24 bg-slate-100 dark:bg-slate-800 rounded animate-pulse mt-1"></div>
        <div class="flex items-center gap-1.5 mt-2">
          <div class="w-2 h-2 rounded-full" :class="s.dot"></div>
          <span class="text-[10px] font-bold text-slate-400">{{ s.sub }}</span>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-3 mb-6">
      <div class="relative flex-1 min-w-[200px] max-w-sm">
        <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
        <input v-model="searchQuery" @input="debouncedSearch" type="text" placeholder="Search by reference, description…"
          class="w-full pl-10 pr-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
      </div>
      <select v-model="typeFilter" @change="fetchTransactions"
        class="px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all">
        <option value="">All Types</option>
        <option value="credit">Credit</option>
        <option value="debit">Debit</option>
        <option value="refund">Refund</option>
        <option value="fee">Fee</option>
      </select>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm">
      <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 hidden md:grid grid-cols-12 gap-4">
        <div class="col-span-3 text-xs font-black text-slate-400 uppercase tracking-widest">User</div>
        <div class="col-span-2 text-xs font-black text-slate-400 uppercase tracking-widest">Reference</div>
        <div class="col-span-2 text-xs font-black text-slate-400 uppercase tracking-widest">Type</div>
        <div class="col-span-2 text-xs font-black text-slate-400 uppercase tracking-widest">Amount</div>
        <div class="col-span-2 text-xs font-black text-slate-400 uppercase tracking-widest">Description</div>
        <div class="col-span-1 text-xs font-black text-slate-400 uppercase tracking-widest">Date</div>
      </div>

      <div v-if="loading" class="divide-y divide-slate-100 dark:divide-slate-800">
        <div v-for="i in 10" :key="i" class="px-6 py-4">
          <div class="h-10 bg-slate-100 dark:bg-slate-800 rounded-xl animate-pulse"></div>
        </div>
      </div>

      <div v-else-if="transactions.length" class="divide-y divide-slate-100 dark:divide-slate-800">
        <div v-for="t in transactions" :key="t.id"
          class="px-6 py-4 md:grid md:grid-cols-12 md:gap-4 flex flex-col gap-2 hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-all">

          <div class="col-span-3 flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-xs font-black flex-shrink-0"
              :class="t.type === 'credit' ? 'bg-emerald-500' : t.type === 'refund' ? 'bg-amber-500' : 'bg-rose-500'">
              {{ t.type === 'credit' ? '+' : t.type === 'refund' ? '↩' : '−' }}
            </div>
            <div class="min-w-0">
              <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ t.user?.name || 'System' }}</p>
              <p class="text-[10px] text-slate-400 truncate">{{ t.user?.email }}</p>
            </div>
          </div>

          <div class="col-span-2 flex items-center">
            <span class="text-xs font-mono text-slate-500 dark:text-slate-400">{{ t.reference || '—' }}</span>
          </div>

          <div class="col-span-2 flex items-center">
            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider" :class="typeClass(t.type)">
              {{ t.type }}
            </span>
          </div>

          <div class="col-span-2 flex items-center">
            <span class="text-sm font-[1000]"
              :class="t.type === 'credit' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
              {{ t.type === 'credit' ? '+' : '−' }}৳{{ Number(t.amount).toLocaleString() }}
            </span>
          </div>

          <div class="col-span-2 flex items-center">
            <p class="text-xs text-slate-400 truncate">{{ t.description || '—' }}</p>
          </div>

          <div class="col-span-1 flex items-center">
            <p class="text-[10px] text-slate-400 font-semibold">{{ formatDate(t.created_at) }}</p>
          </div>
        </div>
      </div>

      <div v-else class="flex flex-col items-center justify-center py-20">
        <div class="w-14 h-14 bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-4">
          <ArrowUpDown class="w-7 h-7 text-slate-300 dark:text-slate-600" />
        </div>
        <h3 class="text-base font-[1000] text-slate-900 dark:text-white mb-1">No transactions yet</h3>
        <p class="text-slate-400 text-sm">Transactions will appear here after payments are processed.</p>
      </div>

      <div v-if="pagination?.last_page > 1" class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
        <p class="text-xs text-slate-400 font-semibold">Showing {{ pagination.from }}–{{ pagination.to }} of {{ pagination.total }}</p>
        <div class="flex gap-2">
          <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 disabled:opacity-40 hover:bg-slate-200 transition-all">Prev</button>
          <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 disabled:opacity-40 hover:bg-slate-200 transition-all">Next</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Shield, ChevronRight, Search, ArrowUpDown } from 'lucide-vue-next'
import { toast } from 'vue-sonner'

definePageMeta({
  middleware: 'auth',
  layout: 'default',
  permissions: 'admin.transactions.view'
})

const { getAll } = useCrud()

const transactions = ref([])
const pagination = ref(null)
const loading = ref(true)
const statsLoading = ref(true)
const searchQuery = ref('')
const typeFilter = ref('')
const currentPage = ref(1)
const rawStats = ref({})

const statCards = computed(() => [
  { label: 'Total Credits', value: '৳' + Number(rawStats.value.total_credits || 0).toLocaleString(), sub: 'All-time inflow', dot: 'bg-emerald-500' },
  { label: 'Total Debits', value: '৳' + Number(rawStats.value.total_debits || 0).toLocaleString(), sub: 'All-time outflow', dot: 'bg-rose-500' },
  { label: 'Refunds', value: '৳' + Number(rawStats.value.total_refunds || 0).toLocaleString(), sub: 'Reversed payments', dot: 'bg-amber-500' },
  { label: 'This Month', value: '৳' + Number(rawStats.value.this_month || 0).toLocaleString(), sub: 'Current cycle', dot: 'bg-indigo-500' },
])

const typeClass = (t) => ({
  credit: 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400',
  debit:  'bg-rose-100 dark:bg-rose-500/20 text-rose-600 dark:text-rose-400',
  refund: 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400',
  fee:    'bg-slate-100 dark:bg-slate-800 text-slate-500',
}[t] || 'bg-slate-100 text-slate-500')

const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) : '—'

const fetchStats = async () => {
  statsLoading.value = true
  try { rawStats.value = await getAll('/admin/finance/transactions/stats') } catch (e) {}
  finally { statsLoading.value = false }
}

const fetchTransactions = async () => {
  loading.value = true
  try {
    const q = { page: currentPage.value }
    if (searchQuery.value) q.search = searchQuery.value
    if (typeFilter.value) q.type = typeFilter.value
    const res = await getAll('/admin/finance/transactions', q)
    transactions.value = res.data || []
    pagination.value = res
  } catch (e) { toast.error('Failed to load transactions') }
  finally { loading.value = false }
}

let timer = null
const debouncedSearch = () => { clearTimeout(timer); timer = setTimeout(() => { currentPage.value = 1; fetchTransactions() }, 400) }
const changePage = (p) => { currentPage.value = p; fetchTransactions() }

onMounted(() => { fetchTransactions(); fetchStats() })
</script>
