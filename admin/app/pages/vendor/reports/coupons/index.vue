<template>
  <div class="max-w-7xl mx-auto p-10 space-y-8">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div class="flex items-center text-sm text-gray-500 bg-white px-4 py-2 rounded-full border border-gray-100 shadow-sm w-fit">
        <NuxtLink to="/vendor/reports" class="hover:text-emerald-600 transition-colors">Reports</NuxtLink>
        <ChevronRight class="w-4 h-4 mx-2" />
        <span class="font-bold text-gray-900">Coupon Report</span>
      </div>
      
      <div class="flex items-center gap-3">
        <button class="flex items-center gap-2 bg-emerald-600 px-6 py-2 rounded-xl text-sm font-bold text-white hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-100">
           Manage Coupons
        </button>
      </div>
    </div>

    <!-- Header Section -->
    <div class="bg-[#1a1c1e] rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl">
      <div class="relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-8">
        <div>
          <h1 class="text-4xl font-black mb-4 tracking-tight">Coupon Performance</h1>
          <p class="text-gray-400 font-medium max-w-md">Track the effectiveness of your promotion campaigns and discount strategies.</p>
        </div>
        <div class="bg-white/10 backdrop-blur-md rounded-3xl p-6 border border-white/10 flex items-center gap-8">
           <div class="text-center">
              <div class="text-[10px] font-black uppercase tracking-widest text-emerald-400 mb-1">Total Redeemed</div>
              <div class="text-2xl font-black">{{ couponsData?.kpi?.total_redemptions || 0 }}</div>
           </div>
           <div class="w-[1px] h-10 bg-white/20"></div>
           <div class="text-center">
              <div class="text-[10px] font-black uppercase tracking-widest text-emerald-400 mb-1">Total Discount</div>
              <div class="text-2xl font-black">৳{{ couponsData?.kpi?.total_discount_given?.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 }) || '0' }}</div>
           </div>
        </div>
      </div>
    </div>

    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-600"></div>
    </div>

    <template v-else>
      <!-- High Impact Coupons -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
         <div v-for="(coupon, index) in (couponsData?.top_coupons || []).slice(0, 2)" :key="coupon.id" class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm relative overflow-hidden group">
            <div class="flex justify-between items-start mb-8 relative z-10">
               <div>
                  <div class="px-4 py-1.5 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-widest mb-4 w-fit">Top Performer</div>
                  <h3 class="text-3xl font-black text-gray-900">{{ coupon.code }}</h3>
               </div>
               <div class="text-right">
                  <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Usage Rate</div>
                  <div class="text-2xl font-black text-indigo-600">
                    {{ coupon.usage_limit ? Math.round((coupon.orders_count / coupon.usage_limit) * 100) + '%' : '∞' }}
                  </div>
               </div>
            </div>
            <div class="grid grid-cols-2 gap-4 relative z-10">
               <div class="p-10 bg-gray-50 rounded-3xl">
                  <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Redemptions</div>
                  <div class="text-2xl font-black text-gray-900">{{ coupon.orders_count }}</div>
               </div>
               <div class="p-10 bg-gray-50 rounded-3xl">
                  <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Total Saved</div>
                  <div class="text-2xl font-black text-gray-900">৳{{ Number(coupon.orders_sum_discount_amount || 0).toLocaleString(undefined, { maximumFractionDigits: 0 }) }}</div>
               </div>
            </div>
            <div class="absolute -right-8 -bottom-8 opacity-[0.03] group-hover:scale-110 transition-transform duration-700">
               <Ticket class="w-64 h-64 text-gray-900" />
            </div>
         </div>
      </div>

      <!-- Active Coupons Table -->
      <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden mt-8">
        <div class="p-10 border-b border-gray-50">
          <h3 class="text-2xl font-black text-gray-900 tracking-tight">Coupon Insights</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="bg-gray-50/50">
              <tr>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Coupon Code</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Type</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Total Used</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Avg. Order</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="coupon in couponsData?.top_coupons || []" :key="coupon.id" class="hover:bg-gray-50/50 transition-colors">
                <td class="px-10 py-6">
                   <div class="flex items-center gap-3">
                      <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600"><Ticket class="w-4 h-4" /></div>
                      <span class="font-black text-gray-900">{{ coupon.code }}</span>
                   </div>
                </td>
                <td class="px-10 py-6 text-sm font-bold text-gray-500 capitalize">{{ coupon.type || 'Fixed' }}</td>
                <td class="px-10 py-6 font-black text-gray-900">{{ coupon.orders_count }}</td>
                <td class="px-10 py-6 font-bold text-indigo-600">
                  {{ coupon.orders_count > 0 ? '৳' + (coupon.orders_sum_total_amount / coupon.orders_count).toLocaleString(undefined, { maximumFractionDigits: 0 }) : '-' }}
                </td>
                <td class="px-10 py-6">
                  <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest" :class="coupon.status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-500'">
                    {{ coupon.status }}
                  </span>
                </td>
              </tr>
              <tr v-if="!couponsData?.top_coupons?.length">
                <td colspan="5" class="px-10 py-6 text-center text-gray-500 font-medium">No coupons found.</td>
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
  Ticket
} from 'lucide-vue-next'

const config = useRuntimeConfig()
const { data: couponsData, pending } = useFetch('/vendor/reports/coupons', {
  baseURL: config.public.apiBase,
  headers: {
    Authorization: `Bearer ${useCookie('auth_token').value}`
  }
})

definePageMeta({
  middleware: 'auth'
})
</script>
