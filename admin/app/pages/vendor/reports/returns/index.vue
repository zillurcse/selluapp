<template>
  <div class="max-w-7xl mx-auto p-10 space-y-8">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div class="flex items-center text-sm text-gray-500 bg-white px-4 py-2 rounded-full border border-gray-100 shadow-sm w-fit">
        <NuxtLink to="/vendor/reports" class="hover:text-amber-600 transition-colors">Reports</NuxtLink>
        <ChevronRight class="w-4 h-4 mx-2" />
        <span class="font-bold text-gray-900">Return Report</span>
      </div>
      
      <div class="flex items-center gap-3">
        <button class="flex items-center gap-2 bg-white border border-gray-200 px-4 py-2 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
          <Download class="w-4 h-4" /> Export PDF
        </button>
      </div>
    </div>

    <!-- Header Section -->
    <div class="bg-[#1a1c1e] rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl">
      <div class="relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-8">
        <div>
          <h1 class="text-4xl font-black mb-4 tracking-tight">Return Analysis</h1>
          <p class="text-gray-400 font-medium max-w-md">Understand your return rates, common reasons, and refund impacts.</p>
        </div>
        <div class="flex bg-white/10 backdrop-blur-md rounded-2xl p-1.5 border border-white/10">
          <button class="px-6 py-2 text-sm font-bold rounded-xl bg-white text-gray-900 shadow-xl">Weekly</button>
          <button class="px-6 py-2 text-sm font-bold rounded-xl text-gray-300 hover:text-white transition-all">Monthly</button>
          <button class="px-6 py-2 text-sm font-bold rounded-xl text-gray-300 hover:text-white transition-all">Yearly</button>
        </div>
      </div>
      <!-- Background Decorative Elements -->
      <div class="absolute top-0 right-0 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl -mr-32 -mt-32"></div>
    </div>

    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-amber-600"></div>
    </div>

    <template v-else>
      <!-- KPI Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
          <div class="relative z-10">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Total Returns</div>
            <div class="text-4xl font-black text-gray-900 mb-4 tracking-tighter">{{ returnsData?.kpi?.total_returns || 0 }}</div>
            <div class="flex items-center text-sm font-bold text-rose-500 bg-rose-50 px-3 py-1 rounded-full w-fit">
              <ArrowUpRight class="w-4 h-4 mr-1" /> +2.4%
            </div>
          </div>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
            <RotateCcw class="w-32 h-32 text-gray-900" />
          </div>
        </div>

        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
          <div class="relative z-10">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Refund Amount</div>
            <div class="text-4xl font-black text-gray-900 mb-4 tracking-tighter">৳{{ returnsData?.kpi?.total_refund_amount?.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) || '0.00' }}</div>
            <div class="flex items-center text-sm font-bold text-emerald-500 bg-emerald-50 px-3 py-1 rounded-full w-fit">
              <ArrowDownRight class="w-4 h-4 mr-1" /> -5.2%
            </div>
          </div>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
            <Wallet class="w-32 h-32 text-gray-900" />
          </div>
        </div>

        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
          <div class="relative z-10">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Return Rate</div>
            <div class="text-4xl font-black text-amber-600 mb-4 tracking-tighter">{{ returnsData?.kpi?.return_rate?.toFixed(2) || '0.00' }}%</div>
            <div class="text-sm font-bold text-gray-400">Total orders ratio</div>
          </div>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
            <BarChart3 class="w-32 h-32 text-gray-900" />
          </div>
        </div>
      </div>

      <!-- Return Reasons Breakdown -->
      <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm mb-8">
        <h3 class="text-2xl font-black text-gray-900 tracking-tight mb-8">Common Return Reasons</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div v-for="(reason, i) in ['Damaged Product', 'Wrong Item', 'Size/Fit Issue', 'Not as Described']" :key="reason" class="p-10 bg-gray-50 rounded-[2rem] hover:bg-amber-50 transition-all border border-transparent hover:border-amber-100">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">{{ reason }}</div>
            <div class="text-2xl font-black text-gray-900 mb-2">{{ (25 - (i * 5)) }}%</div>
            <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
              <div class="h-full bg-amber-500" :style="{ width: (25 - (i * 5)) * 4 + '%' }"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Returns Table -->
      <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-10 flex items-center justify-between border-b border-gray-50">
          <h3 class="text-2xl font-black text-gray-900 tracking-tight">Recent Returns</h3>
          <button class="p-3 bg-gray-50 rounded-2xl text-gray-400 hover:text-gray-900 transition-colors">
            <Filter class="w-5 h-5" />
          </button>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="bg-gray-50/50">
              <tr>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Return ID</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Order ID</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Reason</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Amount</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="(ret, index) in returnsData?.recent_returns || []" :key="ret.id" class="hover:bg-gray-50/50 transition-colors group">
                <td class="px-10 py-6 font-bold text-gray-900">#RET-{{ String(ret.id).padStart(3, '0') }}</td>
                <td class="px-10 py-6 font-medium text-gray-500">{{ ret.invoice_number }}</td>
                <td class="px-10 py-6 font-bold text-gray-700">Client Request</td> <!-- Real reason field not in generic order yet -->
                <td class="px-10 py-6 font-black text-gray-900">৳{{ Number(ret.total_amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</td>
                <td class="px-10 py-6">
                  <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-50 text-amber-600">{{ ret.status }}</span>
                </td>
              </tr>
              <tr v-if="!returnsData?.recent_returns?.length">
                <td colspan="5" class="px-10 py-6 text-center text-gray-500 font-medium">No recent returns found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { 
  ChevronRight, 
  Download, 
  RotateCcw,
  BarChart3,
  ArrowUpRight, 
  ArrowDownRight,
  Wallet,
  Filter
} from 'lucide-vue-next'

const config = useRuntimeConfig()
const { data: returnsData, pending } = useFetch('/vendor/reports/returns', {
  baseURL: config.public.apiBase,
  headers: {
    Authorization: `Bearer ${useCookie('auth_token').value}`
  }
})

definePageMeta({
  middleware: 'auth'
})
</script>
