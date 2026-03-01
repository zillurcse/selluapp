<template>
  <div class="max-w-7xl mx-auto p-10 space-y-8">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div class="flex items-center text-sm text-gray-500 bg-white px-4 py-2 rounded-full border border-gray-100 shadow-sm w-fit">
        <NuxtLink to="/vendor/reports" class="hover:text-indigo-600 transition-colors">Reports</NuxtLink>
        <ChevronRight class="w-4 h-4 mx-2" />
        <span class="font-bold text-gray-900">Sales Analytics</span>
      </div>
    </div>

    <!-- Header Section -->
    <div class="bg-[#1a1c1e] rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl">
      <div class="relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-8">
        <div>
          <h1 class="text-4xl font-black mb-4 tracking-tight">Sales Analytics</h1>
          <p class="text-gray-400 font-medium max-w-md">Deep dive into your revenue trends, top products, and order performance.</p>
        </div>
        <!-- Period Switcher -->
        <div class="flex bg-white/10 backdrop-blur-md rounded-2xl p-1.5 border border-white/10">
          <button
            v-for="p in periods" :key="p.value"
            @click="activePeriod = p.value"
            class="px-6 py-2 text-sm font-bold rounded-xl transition-all"
            :class="activePeriod === p.value ? 'bg-white text-gray-900 shadow-xl' : 'text-gray-300 hover:text-white'"
          >
            {{ p.label }}
          </button>
        </div>
      </div>
    </div>

    <div v-if="pending || kpiPending" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <template v-else>
      <!-- KPI Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm">
          <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Total Revenue</div>
          <div class="text-3xl font-black text-gray-900 mb-2">৳{{ Number(kpiData?.kpi?.total_revenue || 0).toLocaleString() }}</div>
          <div class="text-xs font-bold text-gray-400">All-time online + POS</div>
        </div>

        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm">
          <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Total Orders</div>
          <div class="text-3xl font-black text-indigo-600 mb-2">{{ Number(kpiData?.kpi?.total_orders || 0).toLocaleString() }}</div>
          <div class="text-xs font-bold text-gray-400">Online + POS combined</div>
        </div>

        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm">
          <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Avg Order Value</div>
          <div class="text-3xl font-black text-gray-900 mb-2">৳{{ Number(kpiData?.kpi?.avg_order_value || 0).toFixed(2) }}</div>
          <div class="text-xs font-bold text-gray-400">Revenue ÷ Total Orders</div>
        </div>
      </div>

      <!-- Revenue Trend Chart -->
      <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm">
        <div class="flex items-center justify-between mb-10">
          <h3 class="text-2xl font-black text-gray-900 tracking-tight">
            Revenue Trend
            <span class="text-sm font-bold text-gray-400 ml-2">({{ periods.find(p => p.value === activePeriod)?.label }})</span>
          </h3>
          <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">৳ Bangladeshi Taka</div>
        </div>

        <div v-if="!chartPoints.length" class="h-64 flex items-center justify-center text-gray-400 font-medium">
          No revenue data for this period.
        </div>

        <div v-else class="h-72 flex items-end justify-between gap-2 relative">
          <!-- Horizontal Grid Lines -->
          <div class="absolute inset-0 flex flex-col justify-between pointer-events-none pb-8">
            <div v-for="i in 5" :key="i" class="border-t border-gray-100 w-full"></div>
          </div>

          <div
            v-for="(point, index) in chartPoints"
            :key="index"
            class="flex-1 group relative flex flex-col items-center gap-2"
          >
            <!-- Bar -->
            <div
              class="w-full rounded-t-xl transition-all duration-500 group-hover:opacity-80 cursor-pointer relative"
              :style="{ height: barHeight(point.amount) }"
              :class="point.amount > 0 ? 'bg-indigo-500' : 'bg-gray-100'"
            >
              <!-- Tooltip -->
              <div class="absolute -top-14 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-20 pointer-events-none">
                ৳{{ Number(point.amount).toLocaleString() }}
              </div>
            </div>
            <!-- Label -->
            <div class="text-[10px] font-black text-gray-400 uppercase tracking-tighter text-center">
              {{ formatLabel(point.label) }}
            </div>
          </div>
        </div>
      </div>

      <!-- Top Products from Sales Report -->
      <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm">
        <h3 class="text-2xl font-black text-gray-900 tracking-tight mb-8">Top Products (by Units Sold)</h3>
        <div class="space-y-4">
          <div
            v-for="(product, i) in kpiData?.top_products || []"
            :key="product.id"
            class="flex items-center justify-between p-5 bg-gray-50 hover:bg-indigo-50 rounded-2xl transition-all group"
          >
            <div class="flex items-center gap-4">
              <div class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center font-black text-gray-300 text-sm group-hover:text-indigo-500 transition-colors">
                #{{ i + 1 }}
              </div>
              <div class="w-10 h-10 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                <img v-if="product.thumbnail" :src="product.thumbnail" class="w-full h-full object-cover" />
                <Package v-else class="w-full h-full p-2 text-gray-300" />
              </div>
              <div>
                <div class="font-black text-gray-900 line-clamp-1 max-w-xs">{{ product.name }}</div>
                <div class="text-xs font-bold text-gray-400">{{ product.total_sold }} units sold</div>
              </div>
            </div>
            <div class="text-right">
              <div class="font-black text-gray-900">৳{{ Number(product.total_revenue).toLocaleString() }}</div>
              <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-0.5">Revenue</div>
            </div>
          </div>
          <div v-if="!kpiData?.top_products?.length" class="text-center text-gray-400 py-8 font-medium">
            No sales recorded yet.
          </div>
        </div>
      </div>

      <!-- Recent Transactions -->
      <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-10 border-b border-gray-50">
          <h3 class="text-2xl font-black text-gray-900 tracking-tight">Recent Transactions</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="bg-gray-50/50">
              <tr>
                <th class="px-10 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Order ID</th>
                <th class="px-10 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Customer</th>
                <th class="px-10 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Status</th>
                <th class="px-10 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Amount</th>
                <th class="px-10 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Date</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr
                v-for="order in kpiData?.recent_transactions || []"
                :key="order.id"
                class="hover:bg-gray-50/50 transition-colors"
              >
                <td class="px-10 py-5 font-black text-gray-900">#{{ order.id }}</td>
                <td class="px-10 py-5 font-bold text-gray-600">{{ order.customer?.name || 'Guest' }}</td>
                <td class="px-10 py-5">
                  <span
                    class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider"
                    :class="statusClass(order.status)"
                  >{{ order.status }}</span>
                </td>
                <td class="px-10 py-5 font-black text-gray-900">৳{{ Number(order.total_amount).toLocaleString() }}</td>
                <td class="px-10 py-5 font-bold text-gray-400">{{ new Date(order.created_at).toLocaleDateString() }}</td>
              </tr>
              <tr v-if="!kpiData?.recent_transactions?.length">
                <td colspan="5" class="px-10 py-8 text-center text-gray-400 font-medium">No recent transactions.</td>
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
import { ChevronRight, Package } from 'lucide-vue-next'

definePageMeta({ middleware: 'auth' })

const config = useRuntimeConfig()
const token = useCookie('auth_token')

const periods = [
  { label: 'Weekly', value: 'weekly' },
  { label: 'Monthly', value: 'monthly' },
  { label: 'Yearly', value: 'yearly' },
]
const activePeriod = ref('monthly')

// ── Chart data (period-aware) ─────────────────────────────────────────────────
const { data: kpiData, pending, refresh } = useFetch('/vendor/reports/sales', {
  baseURL: config.public.apiBase,
  headers: { Authorization: `Bearer ${token.value}` },
  query: computed(() => ({ period: activePeriod.value })),
  watch: [activePeriod],
})

// ── KPI summary (all-time, no period dependency, reuse kpiData) ──────────────
const kpiPending = computed(() => pending.value)

// ── Chart points from kpiData.chart ─────────────────────────────────────────
const chartPoints = computed(() => kpiData.value?.chart || [])

// Max revenue for bar scaling
const maxRevenue = computed(() => {
  const vals = chartPoints.value.map(p => Number(p.amount) || 0)
  return Math.max(...vals, 1)
})

function barHeight(amount) {
  const pct = Math.max((Number(amount) / maxRevenue.value) * 100, amount > 0 ? 3 : 0)
  return `calc(${pct}% - 2rem)`
}

function formatLabel(label) {
  if (activePeriod.value === 'monthly') {
    const months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']
    return months[(Number(label) - 1)] || label
  }
  if (activePeriod.value === 'weekly') {
    return new Date(label).toLocaleDateString(undefined, { weekday: 'short', month: 'short', day: 'numeric' })
  }
  return label // yearly: just the year number
}

function statusClass(status) {
  const map = {
    pending:    'bg-amber-100 text-amber-700',
    processing: 'bg-blue-100 text-blue-700',
    completed:  'bg-emerald-100 text-emerald-700',
    cancelled:  'bg-rose-100 text-rose-700',
    returned:   'bg-purple-100 text-purple-700',
  }
  return map[status] ?? 'bg-gray-100 text-gray-600'
}
</script>
