<template>
  <div class="max-w-7xl mx-auto p-10 space-y-8">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div class="flex items-center text-sm text-gray-500 bg-white px-4 py-2 rounded-full border border-gray-100 shadow-sm w-fit">
        <NuxtLink to="/vendor/reports" class="hover:text-rose-600 transition-colors">Reports</NuxtLink>
        <ChevronRight class="w-4 h-4 mx-2" />
        <span class="font-bold text-gray-900">Tax Report</span>
      </div>
      
      <div class="flex items-center gap-3">
        <button class="flex items-center gap-2 bg-white border border-gray-200 px-4 py-2 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
          <Download class="w-4 h-4" /> Download Statement
        </button>
      </div>
    </div>

    <!-- Header Section -->
    <div class="bg-[#1a1c1e] rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl text-center md:text-left">
      <div class="relative z-10">
        <h1 class="text-4xl font-black mb-4 tracking-tight">Tax Compliance</h1>
        <p class="text-gray-400 font-medium max-w-md">Consolidated view of your VAT/Tax liabilities across sales and procurement.</p>
      </div>
    </div>

    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-rose-600"></div>
    </div>

    <template v-else>
      <!-- Tax Breakdown Summary -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
         <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-10">
               <h3 class="text-2xl font-black text-gray-900 tracking-tight">Sales Tax (VAT)</h3>
               <div class="text-sm font-black text-emerald-500">Collected Tax</div>
            </div>
            <div class="flex items-end gap-2 mb-4">
               <div class="text-5xl font-black text-gray-900 tracking-tighter">৳{{ taxData?.kpi?.total_tax_collected?.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 }) || '0' }}</div>
               <div class="text-xs font-bold text-gray-400 mb-2 font-mono uppercase tracking-widest pb-1">Liability</div>
            </div>
            <p class="text-sm text-gray-500 font-medium leading-relaxed mb-8">Generated from taxable sales this period.</p>
            <div class="h-1 bg-gray-50 rounded-full overflow-hidden">
               <div class="h-full bg-indigo-600 w-3/4"></div>
            </div>
         </div>

         <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-10">
               <h3 class="text-2xl font-black text-gray-900 tracking-tight">Tax Categories</h3>
               <div class="text-sm font-black text-rose-500">Breakdown</div>
            </div>
             <div class="flex flex-col gap-4">
                 <div v-for="category in taxData?.tax_by_category || []" :key="category.type" class="flex justify-between items-center bg-gray-50 p-4 rounded-xl">
                      <div class="font-bold text-gray-700 capitalize">{{ category.type }} Tax</div>
                      <div class="font-black text-gray-900">৳{{ Number(category.total_tax || 0).toLocaleString() }}</div>
                 </div>
                 <div v-if="!taxData?.tax_by_category?.length" class="text-gray-500 text-sm py-4">No specific tax categories tracked yet. (Showing unified total in sales tax box).</div>
             </div>
         </div>
      </div>

      <!-- Monthly Log -->
      <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden mt-8">
        <div class="p-10 border-b border-gray-50">
          <h3 class="text-2xl font-black text-gray-900 tracking-tight">Monthly Tax Log</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="bg-gray-50/50">
              <tr>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Month</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Orders Count</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Gross Sales</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Total Tax Collected</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="log in taxData?.monthly_tax_log || []" :key="log.month" class="hover:bg-gray-50/50 transition-colors">
                <td class="px-10 py-6 font-bold text-gray-900">{{ log.month_name }} {{ log.year }}</td>
                <td class="px-10 py-6 font-medium text-gray-500">{{ log.orders_count }}</td>
                <td class="px-10 py-6 font-medium text-gray-500">৳{{ Number(log.total_sales).toLocaleString() }}</td>
                <td class="px-10 py-6 font-black text-gray-900">৳{{ Number(log.total_tax).toLocaleString() }}</td>
              </tr>
              <tr v-if="!taxData?.monthly_tax_log?.length">
                <td colspan="4" class="px-10 py-6 text-center text-gray-500 font-medium">No monthly tax records found.</td>
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
  Download
} from 'lucide-vue-next'

const config = useRuntimeConfig()
const { data: taxData, pending } = useFetch('/vendor/reports/tax', {
  baseURL: config.public.apiBase,
  headers: {
    Authorization: `Bearer ${useCookie('auth_token').value}`
  }
})

definePageMeta({
  middleware: 'auth'
})
</script>
