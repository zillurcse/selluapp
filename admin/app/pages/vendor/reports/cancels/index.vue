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

    <!-- Header Section -->
    <div class="bg-[#1a1c1e] rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl">
      <div class="relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-8">
        <div>
          <h1 class="text-4xl font-black mb-4 tracking-tight">Order Cancellations</h1>
          <p class="text-gray-400 font-medium max-w-md">Analyze why orders are being cancelled and identify patterns to improve retention.</p>
        </div>
        <div class="flex bg-white/10 backdrop-blur-md rounded-2xl p-1.5 border border-white/10">
          <button class="px-6 py-2 text-sm font-bold rounded-xl bg-white text-gray-900 shadow-xl">Last 7 Days</button>
          <button class="px-6 py-2 text-sm font-bold rounded-xl text-gray-300 hover:text-white transition-all">Last 30 Days</button>
        </div>
      </div>
      <!-- Background Decorative Elements -->
      <div class="absolute top-0 right-0 w-64 h-64 bg-rose-500/10 rounded-full blur-3xl -mr-32 -mt-32"></div>
    </div>

    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-rose-600"></div>
    </div>

    <template v-else>
      <!-- KPI Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
          <div class="relative z-10">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Total Cancels</div>
            <div class="text-4xl font-black text-gray-900 mb-4 tracking-tighter">{{ cancelsData?.kpi?.total_cancels || 0 }}</div>
            <div class="flex items-center text-sm font-bold text-emerald-500 bg-emerald-50 px-3 py-1 rounded-full w-fit">
              <ArrowDownRight class="w-4 h-4 mr-1" /> -10%
            </div>
          </div>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
            <XCircle class="w-32 h-32 text-gray-900" />
          </div>
        </div>

        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
          <div class="relative z-10">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Lost Revenue</div>
            <div class="text-4xl font-black text-gray-900 mb-4 tracking-tighter">৳{{ cancelsData?.kpi?.total_lost_revenue?.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) || '0.00' }}</div>
            <div class="text-sm font-bold text-gray-400">Potential income</div>
          </div>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
            <Banknote class="w-32 h-32 text-gray-900" />
          </div>
        </div>

        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
          <div class="relative z-10">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Cancel Rate</div>
            <div class="text-4xl font-black text-rose-600 mb-4 tracking-tighter">{{ cancelsData?.kpi?.cancel_rate?.toFixed(2) || '0.00' }}%</div>
            <div class="text-sm font-bold text-gray-400">Total orders ratio</div>
          </div>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
            <BarChart3 class="w-32 h-32 text-gray-900" />
          </div>
        </div>

        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
          <div class="relative z-10">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Auto Cancels</div>
            <div class="text-4xl font-black text-gray-900 mb-4 tracking-tighter">0</div>
            <div class="text-sm font-bold text-gray-400">System generated</div>
          </div>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
            <Zap class="w-32 h-32 text-gray-900" />
          </div>
        </div>
      </div>

      <!-- Cancellation Reasons -->
      <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm mb-8">
        <h3 class="text-2xl font-black text-gray-900 tracking-tight mb-8">Cancellation Reasons</h3>
        <div class="space-y-6">
          <div v-for="(reason, i) in ['Customer Changed Mind', 'Item Out of Stock', 'Delivery Time Too Long', 'Incorrect Pricing']" :key="reason" class="flex items-center justify-between group">
            <div class="flex-1 max-w-xl">
              <div class="flex justify-between mb-2">
                <span class="font-bold text-gray-700">{{ reason }}</span>
                <span class="font-black text-gray-900">{{ 40 - (i * 10) }}%</span>
              </div>
              <div class="h-2 bg-gray-50 rounded-full overflow-hidden">
                <div class="h-full bg-rose-500 transition-all duration-1000" :style="{ width: (40 - (i * 10)) + '%' }"></div>
              </div>
            </div>
            <div class="hidden md:block w-32 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">
              {{ 20 - (i * 5) }} Orders
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Cancellations Table -->
      <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-10 border-b border-gray-50">
          <h3 class="text-2xl font-black text-gray-900 tracking-tight">Recent Cancellations</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="bg-gray-50/50">
              <tr>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Order ID</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Date</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Reason</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Amount</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="(cancel, index) in cancelsData?.recent_cancels || []" :key="cancel.id" class="hover:bg-gray-50/50 transition-colors">
                <td class="px-10 py-6 font-bold text-gray-900">{{ cancel.invoice_number }}</td>
                <td class="px-10 py-6 text-sm font-medium text-gray-500">{{ new Date(cancel.created_at).toLocaleDateString() }}</td>
                <td class="px-10 py-6 font-bold text-gray-700">Client Request</td>
                <td class="px-10 py-6 font-black text-gray-400 line-through tracking-tighter">৳{{ Number(cancel.total_amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</td>
              </tr>
              <tr v-if="!cancelsData?.recent_cancels?.length">
                <td colspan="4" class="px-10 py-6 text-center text-gray-500 font-medium">No recent cancellations found.</td>
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
  XCircle,
  Banknote,
  BarChart3,
  Zap,
  ArrowDownRight
} from 'lucide-vue-next'

const { data: cancelsData, pending } = useFetch('/api/vendor/reports/cancels', {
  headers: {
    Authorization: `Bearer ${useCookie('auth_token').value}`
  }
})

definePageMeta({
  middleware: 'auth'
})
</script>
