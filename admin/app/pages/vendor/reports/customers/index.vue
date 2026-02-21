<template>
  <div class="max-w-7xl mx-auto p-10 space-y-8">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div class="flex items-center text-sm text-gray-500 bg-white px-4 py-2 rounded-full border border-gray-100 shadow-sm w-fit">
        <NuxtLink to="/vendor/reports" class="hover:text-blue-600 transition-colors">Reports</NuxtLink>
        <ChevronRight class="w-4 h-4 mx-2" />
        <span class="font-bold text-gray-900">Customer Report</span>
      </div>
      
      <div class="flex items-center gap-3">
        <button class="flex items-center gap-2 bg-white border border-gray-200 px-4 py-2 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
          <Download class="w-4 h-4" /> Export Customers
        </button>
      </div>
    </div>

    <!-- Header Section -->
    <div class="bg-[#1a1c1e] rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl">
      <div class="relative z-10">
        <h1 class="text-4xl font-black mb-4 tracking-tight">Customer Insights</h1>
        <p class="text-gray-400 font-medium max-w-md">Understand your audience, retention rates, and lifetime value of your customers.</p>
      </div>
    </div>

    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
    </div>

    <template v-else>
      <!-- KPI Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
          <div class="relative z-10">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Total Customers</div>
            <div class="text-4xl font-black text-gray-900 mb-4 tracking-tighter">{{ customersData?.kpi?.total_customers || 0 }}</div>
            <div class="flex items-center text-sm font-bold text-emerald-500 bg-emerald-50 px-3 py-1 rounded-full w-fit">
              <ArrowUpRight class="w-4 h-4 mr-1" /> +12.4%
            </div>
          </div>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
            <Users class="w-32 h-32 text-gray-900" />
          </div>
        </div>

        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
          <div class="relative z-10">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Active Customers</div>
            <div class="text-4xl font-black text-blue-600 mb-4 tracking-tighter">{{ customersData?.kpi?.active_customers || 0 }}</div>
            <div class="text-sm font-bold text-gray-400">Placed orders</div>
          </div>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
            <Star class="w-32 h-32 text-gray-900" />
          </div>
        </div>

        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
          <div class="relative z-10">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Avg. LTV</div>
            <div class="text-4xl font-black text-gray-900 mb-4 tracking-tighter">৳{{ customersData?.kpi?.average_lifetime_value?.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 }) || '0' }}</div>
            <div class="flex items-center text-sm font-bold text-emerald-500 bg-emerald-50 px-3 py-1 rounded-full w-fit">
              <ArrowUpRight class="w-4 h-4 mr-1" /> per active user
            </div>
          </div>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
            <Heart class="w-32 h-32 text-gray-900" />
          </div>
        </div>
      </div>

      <!-- Top Customers & Segments -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
         <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm">
            <h3 class="text-2xl font-black text-gray-900 tracking-tight mb-8">Customer Segments</h3>
            <div class="space-y-6">
               <div v-for="(segment, i) in ['Loyal Customers', 'At Risk', 'New / Prospects', 'Inactive']" :key="segment" class="flex items-center gap-6 p-6 rounded-3xl hover:bg-gray-50 transition-all border border-transparent hover:border-gray-100">
                  <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-black text-white" :class="i === 0 ? 'bg-emerald-500' : (i === 1 ? 'bg-amber-500' : (i === 2 ? 'bg-blue-500' : 'bg-rose-500'))">
                     {{ segment.charAt(0) }}
                  </div>
                  <div class="flex-1">
                     <div class="flex justify-between items-end mb-2">
                        <span class="font-bold text-gray-700">{{ segment }}</span>
                        <span class="font-black text-gray-900">{{ 45 - (i * 10) }}%</span>
                     </div>
                     <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full" :class="i === 0 ? 'bg-emerald-500' : (i === 1 ? 'bg-amber-500' : (i === 2 ? 'bg-blue-500' : 'bg-rose-500'))" :style="{ width: (45 - (i * 10)) + '%' }"></div>
                     </div>
                  </div>
               </div>
            </div>
         </div>

         <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm">
            <h3 class="text-2xl font-black text-gray-900 tracking-tight mb-8">VIP Customers (Top Spenders)</h3>
            <div class="space-y-6">
               <div v-for="(customer, i) in customersData?.top_customers || []" :key="customer.id" class="flex items-center justify-between group">
                  <div class="flex items-center gap-4">
                     <div class="w-12 h-12 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center font-black text-gray-400">{{ customer.name?.substring(0, 2).toUpperCase() || 'C' + (i+1) }}</div>
                     <div>
                        <div class="font-bold text-gray-900">{{ customer.name }}</div>
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ customer.orders_count }} Orders</div>
                     </div>
                  </div>
                  <div class="text-right">
                     <div class="font-black text-gray-900">৳{{ Number(customer.orders_sum_total_amount || 0).toLocaleString(undefined, { maximumFractionDigits: 0 }) }}</div>
                     <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Total Spent</div>
                  </div>
               </div>
               <div v-if="!customersData?.top_customers?.length" class="text-center text-gray-500 font-medium py-4">No top customers found.</div>
               <button v-else class="w-full mt-4 py-4 text-sm font-bold text-gray-500 hover:text-blue-600 transition-colors bg-gray-50 rounded-2xl">View All Customers</button>
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
  Users,
  Star,
  Heart,
  ArrowUpRight
} from 'lucide-vue-next'

const { data: customersData, pending } = useFetch('/api/vendor/reports/customers', {
  headers: {
    Authorization: `Bearer ${useCookie('auth_token').value}`
  }
})

definePageMeta({
  middleware: 'auth'
})
</script>
