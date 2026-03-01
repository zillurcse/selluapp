<template>
  <div class="max-w-7xl mx-auto p-10 space-y-8">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div class="flex items-center text-sm text-gray-500 bg-white px-4 py-2 rounded-full border border-gray-100 shadow-sm w-fit">
        <NuxtLink to="/vendor/reports" class="hover:text-rose-600 transition-colors">Reports</NuxtLink>
        <ChevronRight class="w-4 h-4 mx-2" />
        <span class="font-bold text-gray-900">Cancel Report</span>
      </div>
      <div class="flex items-center gap-3">
        <button class="flex items-center gap-2 bg-white border border-gray-200 px-4 py-2 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
          <Download class="w-4 h-4" /> Export PDF
        </button>
      </div>
    </div>

    <!-- Header -->
    <div class="bg-[#1a1c1e] rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl">
      <div class="relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-8">
        <div>
          <h1 class="text-4xl font-black mb-4 tracking-tight">Order Cancellations</h1>
          <p class="text-gray-400 font-medium max-w-md">Analyze why orders are being cancelled and identify patterns to improve retention.</p>
        </div>
        <div class="flex bg-white/10 backdrop-blur-md rounded-2xl p-1.5 border border-white/10">
          <button
            v-for="p in periods"
            :key="p.value"
            @click="setPeriod(p.value)"
            class="px-6 py-2 text-sm font-bold rounded-xl transition-all"
            :class="period === p.value ? 'bg-white text-gray-900 shadow-xl' : 'text-gray-300 hover:text-white'"
          >
            {{ p.label }}
          </button>
        </div>
      </div>
      <div class="absolute top-0 right-0 w-64 h-64 bg-rose-500/10 rounded-full blur-3xl -mr-32 -mt-32"></div>
      <div class="absolute bottom-0 left-0 w-48 h-48 bg-orange-500/10 rounded-full blur-3xl -ml-24 -mb-24"></div>
    </div>

    <!-- Loading -->
    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-rose-600"></div>
    </div>

    <template v-else>
      <!-- KPI Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Total Cancels -->
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
          <div class="relative z-10">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Total Cancels</div>
            <div class="text-4xl font-black text-gray-900 mb-4 tracking-tighter">
              {{ cancelsData?.kpi?.total_cancels?.toLocaleString() || '0' }}
            </div>
            <div class="flex items-center text-sm font-bold text-rose-500 bg-rose-50 px-3 py-1 rounded-full w-fit">
              <XCircle class="w-4 h-4 mr-1" /> All-time
            </div>
          </div>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
            <XCircle class="w-32 h-32 text-gray-900" />
          </div>
        </div>

        <!-- Lost Revenue -->
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
          <div class="relative z-10">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Lost Revenue</div>
            <div class="text-4xl font-black text-gray-900 mb-4 tracking-tighter">
              ৳{{ fmt(cancelsData?.kpi?.total_lost_revenue) }}
            </div>
            <div class="text-sm font-bold text-gray-400">Potential income</div>
          </div>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
            <Banknote class="w-32 h-32 text-gray-900" />
          </div>
        </div>

        <!-- Cancel Rate -->
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
          <div class="relative z-10">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Cancel Rate</div>
            <div class="text-4xl font-black text-rose-600 mb-4 tracking-tighter">
              {{ cancelsData?.kpi?.cancel_rate?.toFixed(2) ?? '0.00' }}%
            </div>
            <div class="text-sm font-bold text-gray-400">Of total orders</div>
          </div>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
            <BarChart3 class="w-32 h-32 text-gray-900" />
          </div>
        </div>

        <!-- Unpaid Cancels -->
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
          <div class="relative z-10">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Unpaid Cancels</div>
            <div class="text-4xl font-black text-gray-900 mb-4 tracking-tighter">
              {{ cancelsData?.kpi?.unpaid_cancels?.toLocaleString() || '0' }}
            </div>
            <div class="text-sm font-bold text-gray-400">Payment not completed</div>
          </div>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
            <Zap class="w-32 h-32 text-gray-900" />
          </div>
        </div>
      </div>

      <!-- Chart & Breakdown -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cancellations Trend Chart -->
        <div class="lg:col-span-2 bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between mb-10">
            <h3 class="text-2xl font-black text-gray-900 tracking-tight">Cancellation Trend</h3>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-gray-400">
              <div class="w-3 h-3 bg-rose-500 rounded-full"></div> {{ currentPeriodLabel }}
            </div>
          </div>

          <div v-if="chartData.length" class="h-72 flex items-end justify-between gap-2 py-4 relative">
            <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
              <div v-for="i in 5" :key="i" class="border-t border-gray-50 w-full h-0"></div>
            </div>
            <div
              v-for="(bar, idx) in chartData"
              :key="idx"
              class="flex-1 group relative flex flex-col items-center gap-2"
            >
              <div
                class="w-full rounded-t-xl transition-all duration-500 group-hover:bg-rose-500"
                :class="bar.count > 0 ? 'bg-rose-100' : 'bg-gray-50'"
                :style="{ height: barHeight(bar.count) }"
              ></div>
              <div class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ barLabel(bar.label) }}</div>
              <div
                v-if="bar.count > 0"
                class="absolute -top-10 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-20"
              >
                {{ bar.count }} cancel{{ bar.count !== 1 ? 's' : '' }}
              </div>
            </div>
          </div>

          <div v-else class="h-72 flex items-center justify-center text-gray-400 font-medium">
            No cancellation data for this period.
          </div>
        </div>

        <!-- Payment Method Breakdown -->
        <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm">
          <h3 class="text-2xl font-black text-gray-900 tracking-tight mb-8">By Payment Method</h3>

          <div v-if="breakdown.length" class="space-y-5">
            <div
              v-for="(item, idx) in breakdown"
              :key="idx"
              class="p-4 bg-gray-50 rounded-2xl hover:bg-rose-50 transition-all border border-transparent hover:border-rose-100"
            >
              <div class="flex items-center justify-between mb-2">
                <div class="text-sm font-bold text-gray-700 capitalize">{{ item.label }}</div>
                <div class="text-sm font-black text-rose-600">{{ item.count }}</div>
              </div>
              <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                <div
                  class="h-full bg-rose-500 rounded-full transition-all duration-700"
                  :style="{ width: breakdownPct(item.count) }"
                ></div>
              </div>
            </div>
          </div>

          <div v-else class="py-10 text-center text-gray-400 font-medium text-sm">
            No breakdown data available.
          </div>
        </div>
      </div>

      <!-- Recent Cancellations Table -->
      <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-10 flex flex-col sm:flex-row items-start sm:items-center justify-between border-b border-gray-50 gap-4">
          <h3 class="text-2xl font-black text-gray-900 tracking-tight">Recent Cancellations</h3>
          <div class="relative">
            <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search orders..."
              class="pl-12 pr-6 py-3 bg-gray-50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-rose-100 w-56 transition-all"
            />
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="bg-gray-50/50">
              <tr>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Order ID</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Customer</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Date</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Payment</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Amount</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr
                v-for="cancel in filteredCancels"
                :key="cancel.id"
                class="hover:bg-gray-50/50 transition-colors"
              >
                <td class="px-10 py-6 font-bold text-gray-900">{{ cancel.invoice_number || `#${cancel.id}` }}</td>
                <td class="px-10 py-6">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center text-[10px] font-black text-rose-600">
                      {{ cancel.customer?.first_name?.charAt(0) || 'C' }}
                    </div>
                    <span class="font-bold text-gray-700">
                      {{ cancel.customer?.first_name }} {{ cancel.customer?.last_name }}
                    </span>
                  </div>
                </td>
                <td class="px-10 py-6 text-sm font-medium text-gray-500">
                  {{ new Date(cancel.created_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                </td>
                <td class="px-10 py-6">
                  <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-100 text-gray-500 capitalize">
                    {{ cancel.payment_method || '—' }}
                  </span>
                </td>
                <td class="px-10 py-6 font-black text-gray-400 line-through tracking-tighter">
                  ৳{{ fmt(cancel.total_amount) }}
                </td>
              </tr>
              <tr v-if="!filteredCancels.length">
                <td colspan="5" class="px-10 py-12 text-center text-gray-400 font-medium">
                  {{ searchQuery ? 'No cancellations match your search.' : 'No recent cancellations found.' }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div
          v-if="pagination && pagination.last_page > 1"
          class="p-6 flex items-center justify-between border-t border-gray-50"
        >
          <span class="text-sm font-medium text-gray-400">
            Showing {{ pagination.from }}–{{ pagination.to }} of {{ pagination.total }}
          </span>
          <div class="flex items-center gap-2">
            <button
              @click="goToPage(currentPage - 1)"
              :disabled="currentPage <= 1"
              class="px-4 py-2 rounded-xl text-sm font-bold bg-gray-50 text-gray-500 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
            >
              <ChevronLeft class="w-4 h-4" />
            </button>
            <button
              v-for="pg in pageNumbers"
              :key="pg"
              @click="goToPage(pg)"
              class="px-4 py-2 rounded-xl text-sm font-bold transition-all"
              :class="pg === currentPage ? 'bg-rose-600 text-white shadow' : 'bg-gray-50 text-gray-500 hover:bg-gray-100'"
            >
              {{ pg }}
            </button>
            <button
              @click="goToPage(currentPage + 1)"
              :disabled="currentPage >= pagination.last_page"
              class="px-4 py-2 rounded-xl text-sm font-bold bg-gray-50 text-gray-500 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
            >
              <ChevronRight class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import {
  ChevronRight,
  ChevronLeft,
  Download,
  XCircle,
  Banknote,
  BarChart3,
  Zap,
  Search,
} from 'lucide-vue-next'

definePageMeta({ middleware: 'auth' })

const config    = useRuntimeConfig()
const authToken = useCookie('auth_token')

// ── Period ────────────────────────────────────────────────────────────────────
const periods = [
  { label: 'Weekly',  value: 'weekly'  },
  { label: 'Monthly', value: 'monthly' },
  { label: 'Yearly',  value: 'yearly'  },
]
const period      = ref('monthly')
const currentPage = ref(1)

const currentPeriodLabel = computed(() => periods.find(p => p.value === period.value)?.label ?? '')

function setPeriod(val) {
  period.value      = val
  currentPage.value = 1
}

function goToPage(pg) {
  if (pg < 1 || (pagination.value && pg > pagination.value.last_page)) return
  currentPage.value = pg
}

// ── Fetch ─────────────────────────────────────────────────────────────────────
const { data: cancelsData, pending, refresh } = useFetch(
  () => `/vendor/reports/cancels?period=${period.value}&page=${currentPage.value}`,
  {
    baseURL: config.public.apiBase,
    headers: { Authorization: `Bearer ${authToken.value}` },
    watch: false,
  }
)

watch([period, currentPage], () => refresh())

// ── Chart ─────────────────────────────────────────────────────────────────────
const chartData = computed(() => cancelsData.value?.chart ?? [])
const maxCount  = computed(() => Math.max(...chartData.value.map(b => b.count), 1))

function barHeight(count) {
  const pct = (count / maxCount.value) * 100
  return `${Math.max(pct, count > 0 ? 4 : 1)}%`
}

const MONTH_NAMES = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']
const DAYS        = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']

function barLabel(label) {
  if (period.value === 'monthly') return MONTH_NAMES[(+label) - 1] ?? label
  if (period.value === 'weekly')  return DAYS[new Date(label).getDay()]
  return label
}

// ── Breakdown ─────────────────────────────────────────────────────────────────
const breakdown    = computed(() => cancelsData.value?.breakdown ?? [])
const maxBreakdown = computed(() => Math.max(...breakdown.value.map(r => r.count), 1))

function breakdownPct(count) {
  return `${Math.round((count / maxBreakdown.value) * 100)}%`
}

// ── Pagination ────────────────────────────────────────────────────────────────
const pagination = computed(() => {
  const r = cancelsData.value?.recent_cancels
  if (!r) return null
  return { from: r.from, to: r.to, total: r.total, last_page: r.last_page }
})

const pageNumbers = computed(() => {
  if (!pagination.value) return []
  const total = pagination.value.last_page
  const cur   = currentPage.value
  const range = []
  for (let i = Math.max(1, cur - 2); i <= Math.min(total, cur + 2); i++) range.push(i)
  return range
})

// ── Search ────────────────────────────────────────────────────────────────────
const searchQuery = ref('')

const filteredCancels = computed(() => {
  const list = cancelsData.value?.recent_cancels?.data ?? []
  if (!searchQuery.value.trim()) return list
  const q = searchQuery.value.toLowerCase()
  return list.filter(c =>
    (c.invoice_number ?? '').toLowerCase().includes(q) ||
    (c.customer?.first_name ?? '').toLowerCase().includes(q) ||
    (c.customer?.last_name  ?? '').toLowerCase().includes(q) ||
    (c.payment_method       ?? '').toLowerCase().includes(q)
  )
})

// ── Helpers ───────────────────────────────────────────────────────────────────
function fmt(val) {
  return Number(val ?? 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}
</script>
