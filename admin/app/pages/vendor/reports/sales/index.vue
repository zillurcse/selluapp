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
          <button class="px-6 py-2 text-sm font-bold rounded-xl bg-white text-gray-900 shadow-xl">Weekly</button>
          <button class="px-6 py-2 text-sm font-bold rounded-xl text-gray-300 hover:text-white transition-all">Monthly</button>
          <button class="px-6 py-2 text-sm font-bold rounded-xl text-gray-300 hover:text-white transition-all">Yearly</button>
        </div>
      </div>
      <!-- Background Decorative Elements -->
      <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl -mr-32 -mt-32"></div>
      <div class="absolute bottom-0 left-0 w-48 h-48 bg-purple-500/10 rounded-full blur-3xl -ml-24 -mb-24"></div>
    </div>

    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <template v-else>
      <!-- KPI Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
          <div class="relative z-10">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Total Revenue</div>
            <div class="text-4xl font-black text-gray-900 mb-4 tracking-tighter">৳{{ salesData?.kpi?.total_revenue?.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) || '0.00' }}</div>
            <div class="flex items-center text-sm font-bold text-emerald-500 bg-emerald-50 px-3 py-1 rounded-full w-fit">
              <ArrowUpRight class="w-4 h-4 mr-1" /> +15.4%
            </div>
          </div>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
            <Banknote class="w-32 h-32 text-gray-900" />
          </div>
        </div>

        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
          <div class="relative z-10">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Total Orders</div>
            <div class="text-4xl font-black text-gray-900 mb-4 tracking-tighter">{{ salesData?.kpi?.total_orders || 0 }}</div>
            <div class="flex items-center text-sm font-bold text-emerald-500 bg-emerald-50 px-3 py-1 rounded-full w-fit">
              <ArrowUpRight class="w-4 h-4 mr-1" /> +8.2%
            </div>
          </div>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
            <ShoppingBag class="w-32 h-32 text-gray-900" />
          </div>
        </div>

        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
          <div class="relative z-10">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Avg. Order Value</div>
            <div class="text-4xl font-black text-indigo-600 mb-4 tracking-tighter">৳{{ salesData?.kpi?.avg_order_value?.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) || '0.00' }}</div>
            <div class="flex items-center text-sm font-bold text-rose-500 bg-rose-50 px-3 py-1 rounded-full w-fit">
              <ArrowDownRight class="w-4 h-4 mr-1" /> -2.1%
            </div>
          </div>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
            <Zap class="w-32 h-32 text-gray-900" />
          </div>
        </div>
      </div>

      <!-- Chart & Top Products -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm">
          <div class="flex items-center justify-between mb-10">
            <h3 class="text-2xl font-black text-gray-900 tracking-tight">Revenue Trend</h3>
            <div class="flex items-center gap-4 text-xs font-bold uppercase tracking-widest text-gray-400">
              <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-indigo-600 rounded-full"></div> Current
              </div>
              <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-gray-200 rounded-full"></div> Previous
              </div>
            </div>
          </div>
          
          <!-- Chart Placeholder -->
          <div class="h-80 flex items-end justify-between gap-4 py-4 relative">
            <!-- Horizontal Grid Lines -->
            <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
              <div v-for="i in 6" :key="i" class="border-t border-gray-50 w-full h-0"></div>
            </div>
            
            <div v-for="i in 12" :key="i" class="flex-1 group relative flex flex-col items-center gap-3">
               <div class="w-full bg-indigo-50 rounded-t-xl transition-all duration-500 group-hover:bg-indigo-600" :style="{ height: Math.random() * 100 + '%' }"></div>
               <div class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">M{{i}}</div>
               
               <!-- Tooltip -->
               <div class="absolute -top-12 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-20">
                 ৳{{ (Math.random() * 50000).toFixed(0) }}
               </div>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm">
          <h3 class="text-2xl font-black text-gray-900 tracking-tight mb-8">Top Products</h3>
          <div class="space-y-6">
            <div v-for="i in 4" :key="i" class="flex items-center justify-between p-4 rounded-3xl hover:bg-gray-50 transition-all border border-transparent hover:border-gray-100 group">
              <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center font-black text-gray-400 group-hover:bg-white transition-colors shadow-sm">
                  P{{i}}
                </div>
                <div>
                  <div class="font-bold text-gray-900 mb-1">Premium Item 0{{i}}</div>
                  <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ 120 - (i * 15) }} Sales</div>
                </div>
              </div>
              <div class="font-black text-indigo-600">৳{{ (5000 / i).toFixed(0) }}</div>
            </div>
            <button class="w-full py-4 text-sm font-bold text-gray-500 hover:text-indigo-600 transition-colors bg-gray-50 rounded-2xl hover:bg-indigo-50">
              View All Products
            </button>
          </div>
        </div>
      </div>

      <!-- Recent Sales Table -->
      <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-10 flex items-center justify-between border-b border-gray-50">
          <h3 class="text-2xl font-black text-gray-900 tracking-tight">Recent Transactions</h3>
          <div class="flex items-center gap-4">
            <div class="relative">
              <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
              <input type="text" placeholder="Search orders..." class="pl-12 pr-6 py-3 bg-gray-50 border-none rounded-2xl text-sm font-medium focus:ring-2 focus:ring-indigo-100 w-64 transition-all">
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
              <tr v-for="order in salesData?.recent_transactions || []" :key="order.id" class="hover:bg-gray-50/50 transition-colors group">
                <td class="px-10 py-6 font-bold text-gray-900">{{ order.invoice_number }}</td>
                <td class="px-10 py-6">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-[10px] font-black text-indigo-600">
                      {{ order.customer?.first_name?.charAt(0) || 'C' }}
                    </div>
                    <span class="font-bold text-gray-700">{{ order.customer?.first_name }} {{ order.customer?.last_name }}</span>
                  </div>
                </td>
                <td class="px-10 py-6 text-sm font-medium text-gray-500">{{ new Date(order.created_at).toLocaleDateString() }}</td>
                <td class="px-10 py-6 font-black text-gray-900">৳{{ Number(order.total_amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</td>
                <td class="px-10 py-6">
                  <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-50 text-emerald-600">{{ order.status }}</span>
                </td>
              </tr>
              <tr v-if="!salesData?.recent_transactions?.length">
                <td colspan="5" class="px-10 py-6 text-center text-gray-500 font-medium">No recent transactions found.</td>
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
  ChevronLeft,
  Download, 
  Share2, 
  ArrowUpRight, 
  ArrowDownRight,
  Banknote,
  ShoppingBag,
  Zap,
  Search,
  Filter
} from 'lucide-vue-next'

const { data: salesData, pending } = useFetch('/api/vendor/reports/sales', {
  headers: {
    Authorization: `Bearer ${useCookie('auth_token').value}`
  }
})

definePageMeta({
  middleware: 'auth'
})
</script>
