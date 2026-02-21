<template>
  <div class="max-w-7xl mx-auto p-10 space-y-8">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div class="flex items-center text-sm text-gray-500 bg-white px-4 py-2 rounded-full border border-gray-100 shadow-sm w-fit">
        <NuxtLink to="/vendor/reports" class="hover:text-indigo-600 transition-colors">Reports</NuxtLink>
        <ChevronRight class="w-4 h-4 mx-2" />
        <span class="font-bold text-gray-900">Stock & Inventory</span>
      </div>
      
      <div class="flex items-center gap-3">
        <button @click="showAdjustmentModal = true" class="flex items-center gap-2 bg-indigo-600 px-6 py-2 rounded-xl text-sm font-bold text-white hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">
           Adjustment Inventory
        </button>
      </div>
    </div>

    <!-- Header Section -->
    <div class="bg-[#1a1c1e] rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl">
      <div class="relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-8">
        <div>
          <h1 class="text-4xl font-black mb-4 tracking-tight">Inventory Health</h1>
          <p class="text-gray-400 font-medium max-w-md">Real-time tracking of stock levels, turnover rates, and valuation.</p>
        </div>
        <div class="flex bg-white/10 backdrop-blur-md rounded-2xl p-1.5 border border-white/10">
          <button class="px-6 py-2 text-sm font-bold rounded-xl bg-white text-gray-900 shadow-xl">Overview</button>
          <button class="px-6 py-2 text-sm font-bold rounded-xl text-gray-300 hover:text-white transition-all">Movements</button>
        </div>
      </div>
    </div>

    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <template v-else>
      <!-- KPI Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative group overflow-hidden">
          <div class="relative z-10">
            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Inventory Value</div>
            <div class="text-3xl font-black text-gray-900 mb-2">৳{{ stockData?.kpi?.inventory_value?.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 }) || '0' }}</div>
            <div class="text-xs font-bold text-emerald-500 flex items-center">
              <ArrowUpRight class="w-3 h-3 mr-1" /> +5% this month
            </div>
          </div>
          <Boxes class="absolute -right-4 -bottom-4 w-24 h-24 text-gray-900 opacity-[0.03] group-hover:scale-110 transition-all" />
        </div>

        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative group overflow-hidden">
          <div class="relative z-10">
            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Total Items</div>
            <div class="text-3xl font-black text-indigo-600 mb-2">{{ stockData?.kpi?.total_items || 0 }}</div>
            <div class="text-xs font-bold text-gray-400">Total in-stock units</div>
          </div>
          <BoxSelect class="absolute -right-4 -bottom-4 w-24 h-24 text-gray-900 opacity-[0.03] group-hover:scale-110 transition-all" />
        </div>

        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative group overflow-hidden border-amber-100 bg-amber-50/10">
          <div class="relative z-10">
            <div class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-4">Low Stock Alerts</div>
            <div class="text-3xl font-black text-amber-600 mb-2 text-glow">{{ stockData?.kpi?.low_stock_items || 0 }}</div>
            <div class="text-xs font-bold text-amber-500 underline cursor-pointer">View Items</div>
          </div>
          <AlertTriangle class="absolute -right-4 -bottom-4 w-24 h-24 text-amber-600 opacity-[0.05] group-hover:scale-110 transition-all" />
        </div>

        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative group overflow-hidden border-rose-100 bg-rose-50/10">
          <div class="relative z-10">
            <div class="text-[10px] font-black text-rose-600 uppercase tracking-widest mb-4">Out of Stock</div>
            <div class="text-3xl font-black text-rose-600 mb-2">{{ stockData?.kpi?.out_of_stock_items || 0 }}</div>
            <div class="text-xs font-bold text-rose-500">Urgent restock needed</div>
          </div>
          <XCircle class="absolute -right-4 -bottom-4 w-24 h-24 text-rose-600 opacity-[0.05] group-hover:scale-110 transition-all" />
        </div>
      </div>

      <!-- Inventory Matrix Table -->
      <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-10 border-b border-gray-50 flex items-center justify-between">
          <h3 class="text-2xl font-black text-gray-900 tracking-tight">Stock Analysis Matrix</h3>
          <div class="flex items-center text-xs font-black text-gray-400 gap-4">
             FILTERS:
             <button class="px-4 py-2 bg-indigo-600 text-white rounded-xl">All Items</button>
             <button class="px-4 py-2 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all">Low Stock</button>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead class="bg-gray-50/50">
              <tr>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Product</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Current Stock</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Incoming</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Status</th>
                <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Valuation</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="item in stockData?.products || []" :key="item.id" class="hover:bg-gray-50/50 transition-colors">
                <td class="px-10 py-6">
                   <div class="flex items-center gap-3">
                      <img :src="item.thumbnail" :alt="item.name" class="w-10 h-10 rounded-lg object-cover bg-gray-100" v-if="item.thumbnail" />
                      <div v-else class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400">
                        <Boxes class="w-5 h-5" />
                      </div>
                      <div>
                         <div class="font-bold text-gray-900">{{ item.name }}</div>
                         <div class="text-[10px] font-black text-gray-400 uppercase">ID: {{ item.id }}</div>
                      </div>
                   </div>
                </td>
                <td class="px-10 py-6">
                   <div class="flex items-center gap-2">
                      <span class="font-black" :class="item.stock_qty <= 5 ? 'text-amber-500' : (item.stock_qty === 0 ? 'text-rose-500' : 'text-gray-900')">{{ item.stock_qty }} units</span>
                      <div v-if="item.stock_qty <= 5 && item.stock_qty > 0" class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
                   </div>
                </td>
                <td class="px-10 py-6 font-medium text-indigo-500">-</td> <!-- Placeholder for incoming stock -->
                <td class="px-10 py-6">
                   <div class="text-xs font-bold px-3 py-1 rounded-full w-fit" :class="item.stock_qty === 0 ? 'bg-rose-50 text-rose-600' : (item.stock_qty <= 5 ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600')">
                    {{ item.stock_qty === 0 ? 'Out of Stock' : (item.stock_qty <= 5 ? 'Low Stock' : 'In Stock') }}
                   </div>
                </td>
                <td class="px-10 py-6 font-black text-gray-900">৳{{ Number(item.sale_price * item.stock_qty).toLocaleString(undefined, { maximumFractionDigits: 0 }) }}</td>
              </tr>
              <tr v-if="!stockData?.products?.length">
                <td colspan="5" class="px-10 py-6 text-center text-gray-500 font-medium">No inventory data found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>

    <!-- Adjustment Modal -->
    <Teleport to="body">
      <div v-if="showAdjustmentModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden border border-gray-100 transform transition-all">
          <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
            <div>
               <h3 class="text-2xl font-black text-gray-900 tracking-tight">Adjust Inventory</h3>
               <p class="text-xs font-bold text-gray-400 mt-1">Manual stock modification</p>
            </div>
            <button @click="showAdjustmentModal = false" class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-gray-400 hover:text-rose-600 hover:bg-rose-50 hover:rotate-90 transition-all shadow-sm">
              <X class="w-5 h-5" />
            </button>
          </div>
          
          <div class="p-8 space-y-6">
            <div class="space-y-2">
              <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Product</label>
              <select v-model="selectedProduct" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-bold text-gray-900 outline-none appearance-none">
                <option value="" disabled>Select a product to adjust</option>
                <option v-for="item in stockData?.products || []" :key="item.id" :value="item.id">
                   {{ item.name }} (Current: {{ item.stock_qty }})
                </option>
              </select>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-2">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Type</label>
                <select v-model="adjustType" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-bold text-gray-900 outline-none appearance-none">
                  <option value="add">Add (+)</option>
                  <option value="subtract">Subtract (-)</option>
                </select>
              </div>
              <div class="space-y-2">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Quantity</label>
                <input type="number" v-model="adjustQuantity" min="1" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-bold text-gray-900 outline-none" placeholder="1" />
              </div>
            </div>
          </div>
          
          <div class="p-8 border-t border-gray-50 bg-gray-50/50 flex justify-end gap-3">
            <button @click="showAdjustmentModal = false" class="px-8 py-3 rounded-xl text-sm font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm">
              Cancel
            </button>
            <button @click="adjustStock" :disabled="isAdjusting || !selectedProduct" class="flex items-center justify-center min-w-[140px] gap-2 px-8 py-3 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-xl shadow-indigo-600/20">
              <div v-if="isAdjusting" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
              <span v-else>Confirm</span>
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { 
  ChevronRight, 
  ArrowUpRight,
  Boxes,
  BoxSelect,
  AlertTriangle,
  XCircle,
  X
} from 'lucide-vue-next'

const { data: stockData, pending, refresh } = useFetch('/api/vendor/reports/stock', {
  headers: {
    Authorization: `Bearer ${useCookie('auth_token').value}`
  }
})

// Modal State
const showAdjustmentModal = ref(false)
const selectedProduct = ref('')
const adjustType = ref('add')
const adjustQuantity = ref(1)
const isAdjusting = ref(false)

const adjustStock = async () => {
  if (!selectedProduct.value || adjustQuantity.value < 1) return

  isAdjusting.value = true
  try {
    await $fetch('/api/vendor/reports/stock/adjust', {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${useCookie('auth_token').value}`
      },
      body: {
        product_id: selectedProduct.value,
        type: adjustType.value,
        quantity: Math.abs(adjustQuantity.value)
      }
    })
    
    useNuxtApp().$toast.success('Stock adjusted successfully')
    showAdjustmentModal.value = false
    
    // Reset form
    adjustQuantity.value = 1
    selectedProduct.value = ''
    adjustType.value = 'add'
    
    // Refresh Data
    await refresh()
  } catch (error) {
    useNuxtApp().$toast.error(error.data?.message || 'Failed to adjust stock')
  } finally {
    isAdjusting.value = false
  }
}

definePageMeta({
  middleware: 'auth'
})
</script>
