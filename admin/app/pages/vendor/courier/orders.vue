<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <!-- Header -->
    <div class="max-w-7xl mx-auto mb-10">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
          <h1 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight">Courier Orders</h1>
          <p class="text-slate-500 dark:text-slate-400 font-semibold mt-2 flex items-center gap-2">
            <Truck class="w-4 h-4 text-indigo-500" />
            Manage and sync statuses for all orders sent to couriers.
          </p>
        </div>
        <div class="flex items-center gap-3">
          <button 
            @click="syncAll" 
            :disabled="syncingAll"
            class="px-8 py-3.5 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition-all active:scale-95 shadow-xl shadow-indigo-500/20 disabled:opacity-50 flex items-center gap-3"
          >
            <RefreshCw :class="['w-5 h-5', syncingAll ? 'animate-spin' : '']" />
            <span>{{ syncingAll ? 'Syncing All...' : 'Sync All Statuses' }}</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Filters & Stats -->
    <div class="max-w-7xl mx-auto mb-8 grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="md:col-span-3">
        <div class="relative group">
          <Search class="absolute left-5 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors" />
          <input 
            v-model="search" 
            @input="debounceSearch"
            type="text" 
            placeholder="Search by Invoice ID..." 
            class="w-full h-16 pl-14 pr-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[28px] outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 font-bold text-slate-700 dark:text-slate-200 shadow-sm transition-all"
          >
        </div>
      </div>
      <div>
        <select 
          v-model="courierFilter" 
          @change="fetchOrders"
          class="w-full h-16 px-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[28px] outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 font-bold text-slate-700 dark:text-slate-200 shadow-sm appearance-none cursor-pointer"
        >
          <option value="">All Couriers</option>
          <option value="Steadfast">Steadfast</option>
          <option value="Pathao">Pathao</option>
        </select>
      </div>
    </div>

    <!-- Table Section -->
    <div class="max-w-7xl mx-auto">
      <div class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-xl shadow-slate-200/50 dark:shadow-none overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50/50 dark:bg-slate-800/50">
                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 dark:border-slate-800">Order Info</th>
                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 dark:border-slate-800">Courier Details</th>
                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 dark:border-slate-800">Current Status</th>
                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 dark:border-slate-800 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
              <tr v-if="orders.length === 0 && !pending" class="text-center">
                <td colspan="4" class="px-8 py-20 text-slate-400 font-bold italic">No courier orders found.</td>
              </tr>
              <tr v-for="order in orders" :key="order.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
                <td class="px-8 py-6">
                  <div class="flex flex-col">
                    <span class="text-sm font-black text-slate-900 dark:text-white">#{{ order.invoice_number }}</span>
                    <span class="text-[11px] font-bold text-slate-400 mt-1 uppercase">{{ order.customer?.name || 'Guest' }}</span>
                  </div>
                </td>
                <td class="px-8 py-6">
                  <div class="flex items-center gap-3">
                    <div :class="['w-10 h-10 rounded-xl flex items-center justify-center border', order.courier_name === 'Steadfast' ? 'bg-green-50 border-green-100 dark:bg-green-900/10 dark:border-green-900/20' : 'bg-orange-50 border-orange-100 dark:bg-orange-900/10 dark:border-orange-900/20']">
                      <component :is="order.courier_name === 'Steadfast' ? LayoutDashboard : Truck" :class="['w-5 h-5', order.courier_name === 'Steadfast' ? 'text-green-500' : 'text-orange-500']" />
                    </div>
                    <div class="flex flex-col">
                      <span class="text-[13px] font-black text-slate-800 dark:text-slate-200">{{ order.courier_name }}</span>
                      <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500">ID: {{ order.courier_order_id }}</span>
                    </div>
                  </div>
                </td>
                <td class="px-8 py-6">
                  <span :class="['inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border', getStatusClass(order.courier_status)]">
                    {{ order.courier_status || 'Unknown' }}
                  </span>
                </td>
                <td class="px-8 py-6 text-right">
                  <button 
                    @click="syncSingle(order)" 
                    :disabled="order.syncing"
                    class="p-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:border-indigo-500 hover:text-indigo-600 transition-all shadow-sm active:scale-90 disabled:opacity-50"
                  >
                    <RefreshCw :class="['w-4 h-4', order.syncing ? 'animate-spin' : '']" />
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="px-8 py-6 border-t border-slate-50 dark:border-slate-800 flex items-center justify-between">
          <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Page {{ currentPage }} of {{ totalPages }}</span>
          <div class="flex items-center gap-2">
            <button 
              @click="prevPage" 
              :disabled="currentPage === 1"
              class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 disabled:opacity-30 hover:bg-slate-100 transition-all font-bold"
            >
              <ChevronLeft class="w-4 h-4" />
            </button>
            <button 
              @click="nextPage" 
              :disabled="currentPage === totalPages"
              class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 disabled:opacity-30 hover:bg-slate-100 transition-all font-bold"
            >
              <ChevronDown class="w-4 h-4 -rotate-90" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  Truck, 
  RefreshCw, 
  Search, 
  ChevronLeft, 
  ChevronDown,
  LayoutDashboard,
  Activity
} from 'lucide-vue-next'
import { ref, onMounted, watch } from 'vue'

const { $toast } = useNuxtApp()
const { getAll } = useCrud()

const orders = ref([])
const pending = ref(true)
const syncingAll = ref(false)
const search = ref('')
const courierFilter = ref('')
const currentPage = ref(1)
const totalPages = ref(1)
const perPage = ref(15)

const fetchOrders = async () => {
  try {
    pending.value = true
    const params = new URLSearchParams({
      page: currentPage.value,
      per_page: perPage.value,
      courier: courierFilter.value,
      search: search.value
    }).toString()
    
    const response = await getAll(`/vendor/couriers/orders?${params}`)
    if (response) {
      orders.value = response.data.map(o => ({ ...o, syncing: false }))
      totalPages.value = response.last_page
      currentPage.value = response.current_page
    }
  } catch (error) {
    console.error(error)
    $toast.error('Failed to load courier orders')
  } finally {
    pending.value = false
  }
}

const syncSingle = async (order) => {
  try {
    order.syncing = true
    const response = await getAll(`/vendor/couriers/update-status/${order.id}`)
    if (response.status === 'success') {
      $toast.success(response.message)
      await fetchOrders()
    }
  } catch (error) {
    console.error(error)
    $toast.error('Failed to sync status')
  } finally {
    order.syncing = false
  }
}

const syncAll = async () => {
  try {
    syncingAll.value = true
    const response = await getAll('/vendor/couriers/sync-all')
    if (response.status === 'success') {
      $toast.success(response.message)
      await fetchOrders()
    }
  } catch (error) {
    console.error(error)
    $toast.error('Failed to sync all statuses')
  } finally {
    syncingAll.value = false
  }
}

const getStatusClass = (status) => {
  if (!status) return 'bg-slate-50 text-slate-400 border-slate-100'
  const s = status.toLowerCase()
  if (s.includes('delivered') || s.includes('success')) return 'bg-emerald-50 text-emerald-600 border-emerald-100'
  if (s.includes('cancel')) return 'bg-rose-50 text-rose-600 border-rose-100'
  if (s.includes('transit') || s.includes('going')) return 'bg-amber-50 text-amber-600 border-amber-100'
  return 'bg-indigo-50 text-indigo-600 border-indigo-100'
}

let timeout = null
const debounceSearch = () => {
  clearTimeout(timeout)
  timeout = setTimeout(() => {
    currentPage.value = 1
    fetchOrders()
  }, 500)
}

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
    fetchOrders()
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
    fetchOrders()
  }
}

onMounted(() => {
  fetchOrders()
})
</script>

<style scoped>
/* Table Row Hover Micro-animation */
.group:hover .p-3 {
  @apply border-indigo-200 shadow-md transform -translate-y-0.5;
}
</style>
