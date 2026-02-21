<template>
  <div class="max-w-7xl mx-auto p-10 space-y-8">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div class="flex items-center text-sm text-gray-500 bg-white px-4 py-2 rounded-full border border-gray-100 shadow-sm w-fit">
        <NuxtLink to="/vendor/reports" class="hover:text-indigo-600 transition-colors">Reports</NuxtLink>
        <ChevronRight class="w-4 h-4 mx-2" />
        <span class="font-bold text-gray-900">Sales Analytics</span>
      </div>
      
      <div class="flex items-center gap-3">
        <button class="flex items-center gap-2 bg-white border border-gray-200 px-4 py-2 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
          <Download class="w-4 h-4" /> Export Data
        </button>
      </div>
    </div>

    <!-- Header Section -->
    <div class="bg-[#1a1c1e] rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl">
      <div class="relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-8">
        <div>
          <h1 class="text-4xl font-black mb-4 tracking-tight">Advanced Analytics</h1>
          <p class="text-gray-400 font-medium max-w-md">Deep dive into conversion rates, traffic sources, and customer behavior.</p>
        </div>
        <div class="flex bg-white/10 backdrop-blur-md rounded-2xl p-1.5 border border-white/10">
          <button class="px-6 py-2 text-sm font-bold rounded-xl bg-white text-gray-900 shadow-xl">Overview</button>
          <button class="px-6 py-2 text-sm font-bold rounded-xl text-gray-300 hover:text-white transition-all">Behavior</button>
          <button class="px-6 py-2 text-sm font-bold rounded-xl text-gray-300 hover:text-white transition-all">Retention</button>
        </div>
      </div>
    </div>

    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <template v-else>
      <!-- Interactive Analytics Flow -->
      <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm mb-8">
        <h3 class="text-2xl font-black text-gray-900 tracking-tight mb-10">Sales Trend (Last 7 Days)</h3>
        
        <!-- Chart Placeholder -->
        <div class="h-80 flex items-end justify-between gap-4 py-4 relative">
          <!-- Horizontal Grid Lines -->
          <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
            <div v-for="i in 6" :key="i" class="border-t border-gray-50 w-full h-0"></div>
          </div>
          
          <div v-for="(day, index) in analyticsData || []" :key="index" class="flex-1 group relative flex flex-col items-center gap-3">
              <!-- Bar height is relative to the max revenue in the set -> assuming max around 10k for now as placeholder layout or use static relative heights based on data -->
              <div class="w-full bg-indigo-50 rounded-t-xl transition-all duration-500 group-hover:bg-indigo-600" :style="{ height: Math.max((Number(day.revenue) / 10000) * 100, 10) + '%' }"></div>
              <div class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ new Date(day.date).toLocaleDateString(undefined, { weekday: 'short' }) }}</div>
              
              <!-- Tooltip -->
              <div class="absolute -top-12 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-20">
                ৳{{ Number(day.revenue).toLocaleString() }}
              </div>
          </div>
          <div v-if="!analyticsData?.length" class="absolute inset-0 flex items-center justify-center text-gray-500 font-medium">
            No sales data available for the last 7 days.
          </div>
        </div>
      </div>

      <!-- KPI Cards (Mocked for now as we didn't add this specific logic to backend in analytics) -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm">
          <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Store Visits</div>
          <div class="text-3xl font-black text-gray-900 mb-2">12.5k</div>
          <div class="text-xs font-bold text-emerald-500 flex items-center">
            <ArrowUpRight class="w-3 h-3 mr-1" /> +18% vs last month
          </div>
        </div>
        
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm">
          <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Conversion Rate</div>
          <div class="text-3xl font-black text-indigo-600 mb-2">3.2%</div>
          <div class="text-xs font-bold text-rose-500 flex items-center">
            <ArrowDownRight class="w-3 h-3 mr-1" /> -0.5% vs last month
          </div>
        </div>

        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm">
          <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Add to Cart</div>
          <div class="text-3xl font-black text-gray-900 mb-2">850</div>
          <div class="text-xs font-bold text-emerald-500 flex items-center">
            <ArrowUpRight class="w-3 h-3 mr-1" /> +12% vs last month
          </div>
        </div>

        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm">
          <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Unique Buyers</div>
          <div class="text-3xl font-black text-gray-900 mb-2">412</div>
          <div class="text-xs font-bold text-emerald-500 flex items-center">
            <ArrowUpRight class="w-3 h-3 mr-1" /> +5% vs last month
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { 
  ChevronRight, 
  Download, 
  ArrowUpRight, 
  ArrowDownRight
} from 'lucide-vue-next'

const { data: analyticsData, pending } = useFetch('/api/vendor/reports/sales-analytics', {
  headers: {
    Authorization: `Bearer ${useCookie('auth_token').value}`
  }
})

definePageMeta({
  middleware: 'auth'
})
</script>
