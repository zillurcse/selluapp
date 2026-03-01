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
        <NuxtLink
          to="/vendor/products"
          class="flex items-center gap-2 bg-purple-600 px-6 py-2 rounded-xl text-sm font-bold text-white hover:bg-purple-700 transition-all shadow-lg shadow-purple-100"
        >
          Manage Products
        </NuxtLink>
      </div>
    </div>

    <!-- Header Section -->
    <div class="bg-[#1a1c1e] rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl">
      <div class="relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
          <h1 class="text-4xl font-black mb-4 tracking-tight">Merchandise Analysis</h1>
          <p class="text-gray-400 font-medium max-w-md">Detailed breakdown of product-level sales, conversion, and stock efficiency.</p>
        </div>
        <!-- Search -->
        <div class="relative">
          <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
          <input
            v-model="search"
            type="text"
            placeholder="Search products..."
            class="pl-12 pr-6 py-3 bg-white/10 border border-white/10 rounded-2xl text-sm font-medium text-white placeholder-gray-500 w-64 focus:outline-none focus:ring-2 focus:ring-purple-500/40 transition-all"
          />
        </div>
      </div>
    </div>

    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600"></div>
    </div>

    <template v-else>
      <!-- KPI Summary Row -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
        <div class="bg-white rounded-[2rem] p-7 border border-gray-100 shadow-sm">
          <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Total Products</div>
          <div class="text-3xl font-black text-gray-900">{{ products.length }}</div>
        </div>
        <div class="bg-white rounded-[2rem] p-7 border border-gray-100 shadow-sm">
          <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Total Units Sold</div>
          <div class="text-3xl font-black text-purple-600">{{ totalUnitsSold.toLocaleString() }}</div>
        </div>
        <div class="bg-white rounded-[2rem] p-7 border border-gray-100 shadow-sm">
          <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Total Revenue</div>
          <div class="text-3xl font-black text-gray-900">৳{{ totalRevenue.toLocaleString() }}</div>
        </div>
        <div class="bg-white rounded-[2rem] p-7 border border-gray-100 shadow-sm">
          <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Low Stock Items</div>
          <div class="text-3xl font-black" :class="lowStockCount > 0 ? 'text-rose-500' : 'text-emerald-500'">{{ lowStockCount }}</div>
        </div>
      </div>

      <!-- Performance Leaderboard + Stock card -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Top 3 Leaderboard -->
        <div class="lg:col-span-2 bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm">
          <h3 class="text-2xl font-black text-gray-900 tracking-tight mb-8">Performance Leaderboard</h3>
          <div class="space-y-5">
            <div
              v-for="(product, i) in products.slice(0, 3)"
              :key="product.id"
              class="flex items-center justify-between p-6 bg-gray-50 hover:bg-white border border-transparent hover:border-gray-100 rounded-[2rem] transition-all group"
            >
              <div class="flex items-center gap-5">
                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center font-black text-gray-200 text-xl group-hover:text-purple-600 transition-colors border border-gray-100">
                  #0{{ i + 1 }}
                </div>
                <div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                  <img v-if="product.thumbnail" :src="product.thumbnail" class="w-full h-full object-cover" />
                  <Package v-else class="w-full h-full p-2.5 text-gray-300" />
                </div>
                <div>
                  <div class="font-black text-gray-900 mb-1 line-clamp-1 max-w-[200px]">{{ product.name }}</div>
                  <div class="flex items-center gap-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                    <span>{{ product.total_sold }} Units Sold</span>
                    <span
                      class="px-2 py-0.5 rounded-full"
                      :class="product.stock_qty <= 0 ? 'bg-rose-100 text-rose-600' : product.stock_qty <= 5 ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600'"
                    >
                      {{ product.stock_qty <= 0 ? 'Out of Stock' : product.stock_qty <= 5 ? 'Low Stock' : 'In Stock' }}
                    </span>
                  </div>
                </div>
              </div>
              <div class="text-right">
                <div class="font-black text-gray-900 text-lg">৳{{ Number(product.total_revenue).toLocaleString() }}</div>
                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Revenue</div>
              </div>
            </div>
            <div v-if="!products.length" class="text-center text-gray-400 font-medium py-6">No products found.</div>
          </div>
        </div>

        <!-- Quick Stock Check -->
        <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm relative overflow-hidden flex flex-col justify-between">
          <div>
            <h3 class="text-2xl font-black text-gray-900 tracking-tight mb-8">Quick Stock Check</h3>
            <div class="space-y-4">
              <div class="flex items-center justify-between p-4 bg-emerald-50 rounded-2xl">
                <span class="text-sm font-black text-emerald-700 uppercase tracking-wider">In Stock</span>
                <span class="font-black text-emerald-700 text-xl">{{ inStockCount }}</span>
              </div>
              <div class="flex items-center justify-between p-4 bg-amber-50 rounded-2xl">
                <span class="text-sm font-black text-amber-700 uppercase tracking-wider">Low Stock</span>
                <span class="font-black text-amber-700 text-xl">{{ lowStockCount }}</span>
              </div>
              <div class="flex items-center justify-between p-4 bg-rose-50 rounded-2xl">
                <span class="text-sm font-black text-rose-700 uppercase tracking-wider">Out of Stock</span>
                <span class="font-black text-rose-700 text-xl">{{ outOfStockCount }}</span>
              </div>
            </div>
          </div>
          <NuxtLink
            to="/vendor/reports/stock"
            class="block w-full mt-8 py-4 bg-[#1a1c1e] text-white rounded-3xl font-black uppercase tracking-widest text-[10px] hover:bg-gray-800 transition-colors shadow-xl text-center"
          >
            Stock Report →
          </NuxtLink>
          <div class="absolute -right-16 -bottom-16 opacity-[0.03] pointer-events-none">
            <Package class="w-64 h-64 text-gray-900" />
          </div>
        </div>
      </div>

      <!-- Full Performance Matrix -->
      <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-10 border-b border-gray-50">
          <h3 class="text-2xl font-black text-gray-900 tracking-tight">Full Performance Matrix</h3>
          <p class="text-sm text-gray-400 mt-1 font-medium">Top 10 products ranked by units sold</p>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="bg-gray-50/50">
              <tr>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Product</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Price</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Stock</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Units Sold</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Revenue</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr
                v-for="product in filteredProducts"
                :key="product.id"
                class="hover:bg-gray-50/50 transition-colors"
              >
                <td class="px-10 py-6">
                  <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gray-100 border border-gray-200 overflow-hidden flex-shrink-0">
                      <img v-if="product.thumbnail" :src="product.thumbnail" class="w-full h-full object-cover" />
                      <Package v-else class="w-full h-full p-2.5 text-gray-300" />
                    </div>
                    <div class="font-black text-gray-900 line-clamp-1 max-w-[200px]">{{ product.name }}</div>
                  </div>
                </td>
                <td class="px-10 py-6 font-bold text-gray-500">৳{{ Number(product.sale_price).toLocaleString() }}</td>
                <td
                  class="px-10 py-6 font-black"
                  :class="product.stock_qty <= 0 ? 'text-rose-600' : product.stock_qty <= 5 ? 'text-amber-600' : 'text-gray-900'"
                >{{ product.stock_qty }}</td>
                <td class="px-10 py-6 font-black text-purple-600">{{ Number(product.total_sold).toLocaleString() }}</td>
                <td class="px-10 py-6 font-black text-gray-900">৳{{ Number(product.total_revenue).toLocaleString() }}</td>
              </tr>
              <tr v-if="!filteredProducts.length">
                <td colspan="5" class="px-10 py-8 text-center text-gray-400 font-medium">No products match your search.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { ChevronRight, Search, Package } from 'lucide-vue-next'

definePageMeta({ middleware: 'auth' })

const config = useRuntimeConfig()
const token = useCookie('auth_token')
const search = ref('')

const { data: rawData, pending } = useFetch('/vendor/reports/product-performance', {
  baseURL: config.public.apiBase,
  headers: { Authorization: `Bearer ${token.value}` },
})

// API returns an array directly
const products = computed(() => rawData.value || [])

const filteredProducts = computed(() => {
  if (!search.value.trim()) return products.value
  const q = search.value.toLowerCase()
  return products.value.filter(p => p.name?.toLowerCase().includes(q))
})

const totalUnitsSold = computed(() =>
  products.value.reduce((sum, p) => sum + Number(p.total_sold || 0), 0)
)
const totalRevenue = computed(() =>
  products.value.reduce((sum, p) => sum + Number(p.total_revenue || 0), 0).toLocaleString()
)
const lowStockCount = computed(() =>
  products.value.filter(p => p.stock_qty > 0 && p.stock_qty <= 5).length
)
const outOfStockCount = computed(() =>
  products.value.filter(p => p.stock_qty <= 0).length
)
const inStockCount = computed(() =>
  products.value.filter(p => p.stock_qty > 5).length
)
</script>
