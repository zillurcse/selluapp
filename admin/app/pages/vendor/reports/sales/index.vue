<template>
  <div class="max-w-7xl mx-auto p-10 space-y-8">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div class="flex items-center text-sm text-gray-500 bg-white px-4 py-2 rounded-full border border-gray-100 shadow-sm w-fit">
        <NuxtLink to="/vendor/reports" class="hover:text-indigo-600 transition-colors">Reports</NuxtLink>
        <ChevronRight class="w-4 h-4 mx-2" />
        <span class="font-bold text-gray-900">Sales Report</span>
      </div>

      <div class="flex items-center gap-3">
        <button class="flex items-center gap-2 bg-white border border-gray-200 px-4 py-2 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
          <Download class="w-4 h-4" /> Export PDF
        </button>
        <button class="flex items-center gap-2 bg-indigo-600 px-6 py-2 rounded-xl text-sm font-bold text-white hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">
          <Share2 class="w-4 h-4" /> Share Report
        </button>
      </div>
    </div>

    <!-- Header Section -->
    <div class="bg-[#1a1c1e] rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl">
      <div class="relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-8">
        <div>
          <h1 class="text-4xl font-black mb-4 tracking-tight">Sales Performance</h1>
          <p class="text-gray-400 font-medium max-w-md">Detailed analysis of your revenue, order volume, and sales trends over time.</p>
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
      <!-- Background Decorative Elements -->
      <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl -mr-32 -mt-32"></div>
      <div class="absolute bottom-0 left-0 w-48 h-48 bg-purple-500/10 rounded-full blur-3xl -ml-24 -mb-24"></div>
    </div>

    <!-- Loading -->
    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <template v-else>
      <!-- KPI Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Total Revenue -->
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
          <div class="relative z-10">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Total Revenue</div>
            <div class="text-4xl font-black text-gray-900 mb-4 tracking-tighter">
              ৳{{ fmt(salesData?.kpi?.total_revenue) }}
            </div>
            <div class="flex items-center text-sm font-bold text-emerald-500 bg-emerald-50 px-3 py-1 rounded-full w-fit">
              <ArrowUpRight class="w-4 h-4 mr-1" /> All-time
            </div>
          </div>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
            <Banknote class="w-32 h-32 text-gray-900" />
          </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
          <div class="relative z-10">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Total Orders</div>
            <div class="text-4xl font-black text-gray-900 mb-4 tracking-tighter">
              {{ salesData?.kpi?.total_orders?.toLocaleString() || '0' }}
            </div>
            <div class="flex items-center text-sm font-bold text-emerald-500 bg-emerald-50 px-3 py-1 rounded-full w-fit">
              <ArrowUpRight class="w-4 h-4 mr-1" /> All-time
            </div>
          </div>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
            <ShoppingBag class="w-32 h-32 text-gray-900" />
          </div>
        </div>

        <!-- Avg. Order Value -->
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
          <div class="relative z-10">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Avg. Order Value</div>
            <div class="text-4xl font-black text-indigo-600 mb-4 tracking-tighter">
              ৳{{ fmt(salesData?.kpi?.avg_order_value) }}
            </div>
            <div class="flex items-center text-sm font-bold text-gray-500 bg-gray-50 px-3 py-1 rounded-full w-fit">
              <Zap class="w-4 h-4 mr-1" /> Per order
            </div>
          </div>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
            <Zap class="w-32 h-32 text-gray-900" />
          </div>
        </div>
      </div>

      <!-- Chart & Top Products -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Revenue Trend Chart -->
        <div class="lg:col-span-2 bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between mb-10">
            <h3 class="text-2xl font-black text-gray-900 tracking-tight">Revenue Trend</h3>
            <div class="flex items-center gap-4 text-xs font-bold uppercase tracking-widest text-gray-400">
              <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-indigo-600 rounded-full"></div>
                {{ currentPeriodLabel }}
              </div>
            </div>
          </div>

          <!-- Bar Chart -->
          <div v-if="chartData.length" class="h-80 flex items-end justify-between gap-2 py-4 relative">
            <!-- Horizontal Grid Lines -->
            <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
              <div v-for="i in 5" :key="i" class="border-t border-gray-50 w-full h-0"></div>
            </div>

            <div
              v-for="(bar, idx) in chartData"
              :key="idx"
              class="flex-1 group relative flex flex-col items-center gap-2"
            >
              <div
                class="w-full rounded-t-xl transition-all duration-500 group-hover:bg-indigo-600"
                :class="bar.amount > 0 ? 'bg-indigo-100' : 'bg-gray-50'"
                :style="{ height: barHeight(bar.amount) }"
              ></div>
              <div class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ barLabel(bar.label) }}</div>

              <!-- Tooltip -->
              <div
                v-if="bar.amount > 0"
                class="absolute -top-12 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-20"
              >
                ৳{{ fmt(bar.amount) }}
              </div>
            </div>
          </div>

          <!-- Empty state -->
          <div v-else class="h-80 flex items-center justify-center text-gray-400 font-medium">
            No sales data for this period.
          </div>
        </div>

        <!-- Top Products -->
        <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm">
          <h3 class="text-2xl font-black text-gray-900 tracking-tight mb-8">Top Products</h3>
          <div class="space-y-4">
            <div
              v-for="(product, idx) in salesData?.top_products || []"
              :key="product.id"
              class="flex items-center justify-between p-4 rounded-3xl hover:bg-gray-50 transition-all border border-transparent hover:border-gray-100 group"
            >
              <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gray-100 rounded-2xl overflow-hidden flex-shrink-0 group-hover:bg-white transition-colors shadow-sm">
                  <img
                    v-if="product.thumbnail"
                    :src="product.thumbnail"
                    :alt="product.name"
                    class="w-full h-full object-cover"
                  />
                  <div v-else class="w-full h-full flex items-center justify-center font-black text-gray-400 text-sm">
                    #{{ idx + 1 }}
                  </div>
                </div>
                <div>
                  <div class="font-bold text-gray-900 mb-1 text-sm line-clamp-2">{{ product.name }}</div>
                  <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ product.total_sold }} sold</div>
                </div>
              </div>
              <div class="font-black text-indigo-600 text-sm ml-2 shrink-0">৳{{ fmt(product.total_revenue) }}</div>
            </div>

            <!-- Empty state -->
            <div v-if="!salesData?.top_products?.length" class="py-8 text-center text-gray-400 font-medium text-sm">
              No product data available.
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Transactions -->
      <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-10 flex flex-col sm:flex-row items-start sm:items-center justify-between border-b border-gray-50 gap-4">
          <h3 class="text-2xl font-black text-gray-900 tracking-tight">Recent Transactions</h3>
          <div class="flex items-center gap-4">
            <div class="relative">
              <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Search orders..."
                class="pl-12 pr-6 py-3 bg-gray-50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-indigo-100 w-64 transition-all"
              />
            </div>
            <button class="p-3 bg-gray-50 rounded-2xl text-gray-400 hover:text-gray-900 transition-colors">
              <Filter class="w-5 h-5" />
            </button>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="bg-gray-50/50">
              <tr>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Order ID</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Customer</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Date</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Amount</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr
                v-for="order in filteredTransactions"
                :key="order.id"
                class="hover:bg-gray-50/50 transition-colors group"
              >
                <td class="px-10 py-6 font-bold text-gray-900">{{ order.invoice_number || `#${order.id}` }}</td>
                <td class="px-10 py-6">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-[10px] font-black text-indigo-600">
                      {{ order.customer?.first_name?.charAt(0) || 'C' }}
                    </div>
                    <span class="font-bold text-gray-700">
                      {{ order.customer?.first_name }} {{ order.customer?.last_name }}
                    </span>
                  </div>
                </td>
                <td class="px-10 py-6 text-sm font-medium text-gray-500">
                  {{ new Date(order.created_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                </td>
                <td class="px-10 py-6 font-black text-gray-900">৳{{ fmt(order.total_amount) }}</td>
                <td class="px-10 py-6">
                  <span
                    class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest"
                    :class="statusClass(order.status)"
                  >
                    {{ order.status }}
                  </span>
                </td>
              </tr>
              <tr v-if="!filteredTransactions.length">
                <td colspan="5" class="px-10 py-12 text-center text-gray-400 font-medium">
                  {{ searchQuery ? 'No orders match your search.' : 'No recent transactions found.' }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import {
  ChevronRight,
  Download,
  Share2,
  ArrowUpRight,
  Banknote,
  ShoppingBag,
  Zap,
  Search,
  Filter
} from 'lucide-vue-next'

definePageMeta({ middleware: 'auth' })

const config = useRuntimeConfig()
const authToken = useCookie('auth_token')

// ── Period ────────────────────────────────────────────────────────────────────
const periods = [
  { label: 'Weekly',  value: 'weekly'  },
  { label: 'Monthly', value: 'monthly' },
  { label: 'Yearly',  value: 'yearly'  },
]
const period = ref('monthly')

const currentPeriodLabel = computed(() => periods.find(p => p.value === period.value)?.label ?? '')

function setPeriod(val) {
  period.value = val
}

// ── Fetch ─────────────────────────────────────────────────────────────────────
const { data: salesData, pending, refresh } = useFetch(
  () => `/vendor/reports/sales?period=${period.value}`,
  {
    baseURL: config.public.apiBase,
    headers: { Authorization: `Bearer ${authToken.value}` },
    watch: false,
  }
)

watch(period, () => refresh())

// ── Chart ─────────────────────────────────────────────────────────────────────
const chartData = computed(() => salesData.value?.chart ?? [])

const maxAmount = computed(() => Math.max(...chartData.value.map(b => b.amount), 1))

function barHeight(amount) {
  const pct = (amount / maxAmount.value) * 100
  return `${Math.max(pct, amount > 0 ? 4 : 1)}%`
}

const MONTH_NAMES = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']
const DAYS = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']

function barLabel(label) {
  if (period.value === 'monthly') {
    const m = parseInt(label)
    return MONTH_NAMES[m - 1] ?? label
  }
  if (period.value === 'weekly') {
    // label is Y-m-d
    const d = new Date(label)
    return DAYS[d.getDay()]
  }
  return label // yearly: show the year number
}

// ── Search ────────────────────────────────────────────────────────────────────
const searchQuery = ref('')

const filteredTransactions = computed(() => {
  const list = salesData.value?.recent_transactions ?? []
  if (!searchQuery.value.trim()) return list
  const q = searchQuery.value.toLowerCase()
  return list.filter(o =>
    (o.invoice_number ?? '').toLowerCase().includes(q) ||
    (o.customer?.first_name ?? '').toLowerCase().includes(q) ||
    (o.customer?.last_name ?? '').toLowerCase().includes(q)
  )
})

// ── Helpers ───────────────────────────────────────────────────────────────────
function fmt(val) {
  return Number(val ?? 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

function statusClass(status) {
  const map = {
    delivered:  'bg-emerald-50 text-emerald-600',
    pending:    'bg-amber-50 text-amber-600',
    processing: 'bg-blue-50 text-blue-600',
    cancelled:  'bg-rose-50 text-rose-600',
    returned:   'bg-orange-50 text-orange-600',
  }
  return map[status?.toLowerCase()] ?? 'bg-gray-100 text-gray-500'
}
</script>
