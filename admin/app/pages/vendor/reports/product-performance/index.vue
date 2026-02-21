<template>
  <div class="max-w-7xl mx-auto p-10 space-y-8">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div class="flex items-center text-sm text-gray-500 bg-white px-4 py-2 rounded-full border border-gray-100 shadow-sm w-fit">
        <NuxtLink to="/vendor/reports" class="hover:text-purple-600 transition-colors">Reports</NuxtLink>
        <ChevronRight class="w-4 h-4 mx-2" />
        <span class="font-bold text-gray-900">Product Performance</span>
      </div>
      
      <div class="flex items-center gap-3">
        <button class="flex items-center gap-2 bg-purple-600 px-6 py-2 rounded-xl text-sm font-bold text-white hover:bg-purple-700 transition-all shadow-lg shadow-purple-100">
           Manage Products
        </button>
      </div>
    </div>

    <!-- Header Section -->
    <div class="bg-[#1a1c1e] rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl">
      <div class="relative z-10">
        <h1 class="text-4xl font-black mb-4 tracking-tight">Merchandise Analysis</h1>
        <p class="text-gray-400 font-medium max-w-md">Detailed breakdown of product-level sales, conversion, and stock efficiency.</p>
      </div>
    </div>

    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600"></div>
    </div>

    <template v-else>
      <!-- Product Analytics Overviews -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
         <!-- Star Products -->
         <div class="lg:col-span-2 bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm">
            <h3 class="text-2xl font-black text-gray-900 tracking-tight mb-8">Performance Leaderboard</h3>
            <div class="space-y-6">
               <div v-for="(product, i) in (performanceData?.top_products || []).slice(0, 3)" :key="product.id" class="flex items-center justify-between p-6 bg-gray-50 hover:bg-white border border-transparent hover:border-gray-100 rounded-[2rem] transition-all group">
                  <div class="flex items-center gap-6">
                     <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center font-black text-gray-200 text-xl group-hover:text-purple-600 transition-colors">#0{{i + 1}}</div>
                     <div>
                        <div class="font-black text-gray-900 mb-1 line-clamp-1">{{ product.name }}</div>
                        <div class="flex items-center gap-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                           <span>{{ product.sales }} Sales</span>
                           <span class="text-emerald-500">Active</span> <!-- Placeholder for conversion rate if you add view counts -->
                        </div>
                     </div>
                  </div>
                  <div class="text-right">
                     <div class="font-black text-gray-900 text-lg">৳{{ (product.sales * product.price).toLocaleString() }}</div>
                     <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Net Revenue</div>
                  </div>
               </div>
               <div v-if="!performanceData?.top_products?.length" class="text-center text-gray-500 font-medium py-4">No top products found.</div>
            </div>
         </div>

         <!-- Actionable Stock -->
         <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm relative overflow-hidden flex flex-col justify-between">
            <div>
              <h3 class="text-2xl font-black text-gray-900 tracking-tight mb-8">Quick Stock Check</h3>
              <div class="text-center mb-8">
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Total Managed Items</div>
                <div class="text-6xl font-black text-gray-900 tracking-tighter">{{ performanceData?.top_products?.length || 0 }}</div>
              </div>
            </div>
            <button class="w-full mt-10 py-5 bg-[#1a1c1e] text-white rounded-3xl font-black uppercase tracking-widest text-[10px] hover:bg-gray-800 transition-colors shadow-xl">
               Inventory Management
            </button>
            <div class="absolute -right-16 -bottom-16 opacity-[0.03] pointer-events-none">
               <Package class="w-64 h-64 text-gray-900" />
            </div>
         </div>
      </div>

      <!-- Detailed Matrix -->
      <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden mt-8">
        <div class="p-10 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
          <h3 class="text-2xl font-black text-gray-900 tracking-tight">Full Performance Matrix</h3>
          <div class="relative">
              <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
              <input type="text" placeholder="Search product name..." class="pl-12 pr-6 py-3 bg-gray-50 border-none rounded-2xl text-sm font-medium w-full md:w-64 focus:ring-2 focus:ring-purple-500/20 transition-all">
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="bg-gray-50/50">
              <tr>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Product</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Price</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Stock</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Orders</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Revenue</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="product in performanceData?.top_products || []" :key="product.id" class="hover:bg-gray-50/50 transition-colors">
                <td class="px-10 py-6">
                  <div class="flex items-center gap-4">
                      <div class="w-12 h-12 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden flex-shrink-0">
                        <img v-if="product.product_thumbnail" :src="product.product_thumbnail" class="w-full h-full object-cover">
                      </div>
                      <div>
                          <div class="font-black text-gray-900 line-clamp-1 max-w-[200px]">{{ product.name }}</div>
                          <div class="text-[10px] font-bold text-emerald-500 uppercase tracking-wider">{{ product.status }}</div>
                      </div>
                  </div>
                </td>
                <td class="px-10 py-6 font-bold text-gray-500">৳{{ Number(product.price).toLocaleString() }}</td>
                <td class="px-10 py-6 font-black" :class="product.stock <= 5 ? 'text-rose-600' : 'text-gray-900'">{{ product.stock }}</td>
                <td class="px-10 py-6 font-black text-gray-900">{{ product.sales }}</td>
                <td class="px-10 py-6 font-black text-gray-900">৳{{ (product.price * product.sales).toLocaleString() }}</td>
              </tr>
              <tr v-if="!performanceData?.top_products?.length">
                <td colspan="5" class="px-10 py-6 text-center text-gray-500 font-medium">No products found.</td>
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
  Search,
  Package
} from 'lucide-vue-next'

const { data: performanceData, pending } = useFetch('/api/vendor/reports/product-performance', {
  headers: {
    Authorization: `Bearer ${useCookie('auth_token').value}`
  }
})

definePageMeta({
  middleware: 'auth'
})
</script>
