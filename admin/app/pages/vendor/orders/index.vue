<template>
  <div class="p-6 lg:p-8 bg-slate-50 dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div class="flex items-center gap-4">
        <button @click="$router.back()" class="p-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm group">
          <ChevronLeft class="w-5 h-5 text-slate-500 dark:text-slate-400 group-hover:-translate-x-0.5 transition-transform" />
        </button>
        <div>
          <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">All Orders</h1>
          <p class="text-sm text-slate-500 dark:text-slate-400">Manage and track every customer order efficiently.</p>
        </div>
      </div>
    </div>

    <!-- Status Tabs -->
    <div class="mb-8">
      <div class="flex items-center gap-3 overflow-x-auto tabs-scrollbar pb-3 -mb-3 px-1">
        <button 
          v-for="tab in statusTabs" 
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border flex items-center gap-2.5 shadow-sm whitespace-nowrap',
            activeTab === tab.id 
              ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 border-indigo-100 dark:border-indigo-500/30' 
              : 'bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800'
          ]"
        >
          <component :is="tab.icon" class="w-4 h-4 opacity-70" />
          {{ tab.label }}
          <span 
            :class="[
              'flex items-center justify-center min-w-[20px] h-[20px] px-1 text-[10px] font-bold rounded-full',
              activeTab === tab.id ? 'bg-indigo-600 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500'
            ]"
          >
            {{ tab.count }}
          </span>
        </button>
      </div>
    </div>

    <!-- Filter Section Area -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 mb-8 p-5 sm:p-6">
      <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:flex lg:items-end gap-5 flex-grow">
          <!-- Invoice/Phone -->
          <div class="flex flex-col gap-2 flex-grow min-w-[240px]">
            <label class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.15em] ml-1">Invoice / Phone</label>
            <div class="relative">
              <input 
                v-model="searchQuery"
                @keyup.enter="handleSearch"
                type="text" 
                placeholder="Search invoice or phone..." 
                class="w-full h-11 pl-4 pr-10 bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all text-sm font-medium placeholder:text-slate-400"
              >
            </div>
          </div>

          <!-- Order Type -->
          <div class="flex flex-col gap-2 w-full lg:w-48">
            <label class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.15em] ml-1">Order Type</label>
            <div class="relative">
              <select v-model="selectedType" class="w-full h-11 pl-4 pr-10 bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 rounded-xl appearance-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all text-sm font-medium cursor-pointer">
                <option>All</option>
                <option>Normal</option>
                <option>Pre-order</option>
                <option>POS</option>
              </select>
              <ChevronDown class="absolute right-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
            </div>
          </div>

          <!-- Order Date -->
          <div class="flex flex-col gap-2 w-full lg:w-48">
            <label class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.15em] ml-1">Order Date</label>
            <div class="relative">
              <input 
                v-model="selectedDate"
                type="date" 
                class="w-full h-11 px-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all text-sm font-medium text-slate-600 dark:text-slate-300"
              >
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex items-center gap-3 h-11">
            <button @click="handleSearch" class="h-11 px-6 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-all shadow-md shadow-indigo-200 dark:shadow-none active:scale-[0.98] flex items-center justify-center gap-2.5">
              <Search class="w-4 h-4" />
              Search
            </button>
            <button @click="clearFilters" class="h-11 px-6 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-sm font-bold rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-all flex items-center justify-center gap-2 active:scale-[0.98] shadow-sm">
              <X class="w-4 h-4" />
              Clear
            </button>
          </div>
        </div>

        <!-- Bulk Delete -->
        <div class="flex lg:items-end w-full lg:w-auto mt-2 lg:mt-0">
          <button class="w-full lg:w-auto h-11 px-5 text-[13px] font-bold rounded-xl transition-all flex items-center justify-center gap-2.5 bg-red-50 dark:bg-red-950/30 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-900/30 hover:bg-red-600 hover:text-white dark:hover:bg-red-600 shadow-sm active:scale-[0.98]">
            <Trash2 class="w-4 h-4" />
            <span class="lg:hidden xl:inline">Bulk Delete</span>
            <span class="hidden lg:inline xl:hidden">Delete</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Orders Table Container -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden transition-colors duration-300">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse font-sans">
          <thead>
            <tr class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
              <th class="py-4 px-6 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <div class="flex items-center gap-3">
                  <span class="text-slate-400">#</span>
                  <input type="checkbox" class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                </div>
              </th>
              <th class="py-4 px-4 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Customer</th>
              <th class="py-4 px-4 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Order</th>
              <th class="py-4 px-4 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Spider Intelligence</th>
              <th class="py-4 px-4 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Products</th>
              <th class="py-4 px-4 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Amount</th>
              <th class="py-4 px-4 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Others</th>
              <th class="py-4 px-4 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Courier</th>
              <th class="py-4 px-6 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
            <!-- Loading State -->
            <tr v-if="loading" v-for="i in 5" :key="`skeleton-${i}`">
              <td class="px-6 py-4" colspan="9">
                <div class="h-12 w-full bg-slate-50 dark:bg-slate-800/50 animate-pulse rounded-lg"></div>
              </td>
            </tr>

            <!-- Empty State -->
            <tr v-if="!loading && orders.length === 0">
              <td colspan="9" class="py-24 px-6 text-center">
                <div class="flex flex-col items-center justify-center gap-4 max-w-sm mx-auto">
                  <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-400">
                    <XCircle class="w-8 h-8" />
                  </div>
                  <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">No data found</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Try adjusting your filters or check back later for more data.</p>
                  </div>
                  <button @click="reloadPage" class="mt-2 flex items-center gap-2 px-4 py-2 bg-slate-900 dark:bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-colors">
                    <RefreshCw class="w-4 h-4" />
                    Reload Page
                  </button>
                </div>
              </td>
            </tr>

            <!-- Orders Grid Data -->
            <tr v-else-if="!loading" v-for="(order, index) in orders" :key="order.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <span class="text-xs font-semibold text-slate-400">#{{ index + 1 }}</span>
                  <input type="checkbox" class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                </div>
              </td>

              <td class="px-4 py-4">
                <div class="flex items-center gap-3">
                  <div class="flex-shrink-0 w-9 h-9 rounded-full bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center border border-indigo-100 dark:border-indigo-500/20 text-indigo-700 dark:text-indigo-400 text-xs font-bold relative">
                    {{ order.customer.initials }}
                    <div v-if="order.customer.vip" class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-amber-400 rounded-full border-2 border-white dark:border-slate-900 flex items-center justify-center">
                      <Star class="w-2 h-2 text-white fill-current" />
                    </div>
                  </div>
                  <div>
                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100 line-clamp-1">{{ order.customer.name }}</div>
                    <div class="text-[10px] text-slate-500 font-medium tracking-tight">{{ order.customer.phone }}</div>
                  </div>
                </div>
              </td>

              <td class="px-4 py-4">
                <div class="flex flex-col gap-1">
                  <span class="text-[11px] font-bold text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md min-w-[90px] text-center border border-slate-200 dark:border-slate-700">
                    {{ order.invoice }}
                  </span>
                  <div :class="[
                    'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide inline-flex items-center justify-center border',
                    {
                      'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-500/20': order.status === 'Delivered',
                      'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-500/20': order.status === 'Processing',
                      'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-500/20': order.status === 'Pending',
                      'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400 border-indigo-100 dark:border-indigo-500/20': order.status === 'Courier',
                      'bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400 border-rose-100 dark:border-rose-500/20': order.status === 'Cancelled'
                    }
                  ]">
                    {{ order.status }}
                  </div>
                </div>
              </td>

              <td class="px-4 py-4">
                <div class="flex flex-col gap-1.5 w-[140px]">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                      <ShieldCheck v-if="order.risk.score < 20" class="w-3.5 h-3.5 text-emerald-500" />
                      <AlertTriangle v-else-if="order.risk.score < 60" class="w-3.5 h-3.5 text-amber-500" />
                      <ShieldAlert v-else class="w-3.5 h-3.5 text-rose-500" />
                      <span class="text-xs font-bold" :class="order.risk.score < 20 ? 'text-emerald-600' : order.risk.score < 60 ? 'text-amber-600' : 'text-rose-600'">
                        {{ order.risk.score }}
                      </span>
                    </div>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Risk Score</span>
                  </div>
                  <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                    <div :class="['h-full transition-all duration-1000', order.risk.score < 20 ? 'bg-emerald-500' : order.risk.score < 60 ? 'bg-amber-500' : 'bg-rose-500']" :style="{ width: `${order.risk.score}%` }"></div>
                  </div>
                </div>
              </td>

              <td class="px-4 py-4">
                <div class="flex items-center gap-2">
                  <div class="flex -space-x-2">
                    <img v-for="(img, idx) in order.products.images.slice(0, 3)" :key="idx" :src="img" class="w-8 h-8 rounded-lg border-2 border-white dark:border-slate-800 shadow-sm object-cover bg-slate-100">
                  </div>
                  <div class="flex flex-col">
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ order.products.count }} Items</span>
                    <span class="text-[9px] font-semibold text-slate-400 uppercase tracking-tight">SKU: {{ order.products.skus }}</span>
                  </div>
                </div>
              </td>

              <td class="px-4 py-4">
                <div class="flex flex-col">
                  <span class="text-sm font-bold text-slate-900 dark:text-white">{{ order.amount.total }}</span>
                  <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded-md border border-slate-200 dark:border-slate-700">
                      {{ order.amount.method }}
                    </span>
                    <span v-if="order.amount.discount > 0" class="text-[9px] font-black uppercase tracking-wider px-1.5 py-0.5 bg-rose-50 dark:bg-rose-950 text-rose-600 dark:text-rose-400 rounded-md border border-rose-100 dark:border-rose-900/30">
                      Promotion
                    </span>
                  </div>
                </div>
              </td>

              <td class="px-4 py-4">
                <div class="flex flex-col gap-1 text-[11px] font-medium text-slate-500 dark:text-slate-400">
                  <div class="flex items-center gap-1.5">
                    <Tag class="w-3.5 h-3.5 opacity-60" /> {{ order.type }}
                  </div>
                  <div class="flex items-center gap-1.5 truncate max-w-[100px]">
                    <MapPin class="w-3.5 h-3.5 opacity-60" /> {{ order.zone }}
                  </div>
                </div>
              </td>

              <td class="px-4 py-4">
                <div class="flex items-center gap-2">
                  <div v-if="order.courier.name" class="flex flex-col">
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ order.courier.name }}</span>
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ order.courier.tracking }}</span>
                  </div>
                  <span v-else class="text-xs text-slate-400 italic">Unassigned</span>
                </div>
              </td>

              <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-all duration-300">
                  <button @click="openDrawer(order)" class="p-1.5 bg-white dark:bg-slate-800 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 border border-slate-200 dark:border-slate-700 rounded-lg transition-all shadow-sm">
                    <Eye class="w-4 h-4" />
                  </button>
                  <button @click="openDrawer(order)" class="p-1.5 bg-white dark:bg-slate-800 text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 border border-slate-200 dark:border-slate-700 rounded-lg transition-all shadow-sm">
                    <Edit class="w-4 h-4" />
                  </button>
                  <NuxtLink :to="`/vendor/orders/invoice/${order.id}?type=${order.type}`" target="_blank" class="p-1.5 bg-white dark:bg-slate-800 text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 border border-slate-200 dark:border-slate-700 rounded-lg transition-all shadow-sm">
                    <Printer class="w-4 h-4" />
                  </NuxtLink>
                  <button @click="confirmDelete(order)" class="p-1.5 bg-white dark:bg-slate-800 text-slate-400 hover:text-red-600 dark:hover:text-red-400 border border-slate-200 dark:border-slate-700 rounded-lg transition-all shadow-sm">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Details Drawer -->
    <VendorOrderDetailsDrawer ref="orderDetailsDrawer" @updated="fetchOrders" />

    <!-- Delete Confirmation Modal -->
    <AppConfirmationModal
      :isOpen="isDeleteModalOpen"
      title="Delete Order"
      message="Are you sure you want to completely delete this order? This action cannot be undone."
      icon="Trash2"
      confirmText="Delete Order"
      :isLoading="isDeleting"
      @confirm="handleConfirmDelete"
      @close="isDeleteModalOpen = false"
    />
  </div>
</template>

<script setup>
import { 
  ChevronLeft, 
  ChevronDown, 
  Search, 
  X, 
  Trash2, 
  XCircle, 
  RefreshCw,
  LayoutGrid,
  Clock,
  CheckCircle2,
  Truck,
  Package,
  Star,
  Phone,
  CalendarDays,
  ShieldCheck,
  ShieldAlert,
  AlertTriangle,
  Tag,
  MapPin,
  Eye,
  Edit,
  Printer
} from 'lucide-vue-next'
import { ref, computed, watch } from 'vue'
import { toast } from 'vue-sonner'
import VendorOrderDetailsDrawer from '~/components/vendor/VendorOrderDetailsDrawer.vue'
import AppConfirmationModal from '~/components/AppConfirmationModal.vue'

definePageMeta({
  middleware: 'auth',
  permissions: 'orders.view'
})

const activeTab = ref('latest')
const searchQuery = ref('')
const selectedType = ref('All')
const selectedDate = ref('')
const { getAll, deleteItem } = useCrud()

// Computed query params for API call
const queryParams = computed(() => {
  return {
    status: activeTab.value,
    search: searchQuery.value,
    type: selectedType.value,
    date: selectedDate.value,
  }
})

const listData = ref(null)
const loading = ref(false)

// Fetch Orders Dynamically
const fetchOrders = async () => {
  loading.value = true
  try {
    const response = await getAll('/vendor/orders', queryParams.value)
    listData.value = response

    // Update counts if available
    if (response?.counts) {
      statusTabs.value.forEach(tab => {
        if (response.counts[tab.id] !== undefined) {
          tab.count = response.counts[tab.id]
        }
      })
    }
  } catch (error) {
    console.error('Failed to fetch orders', error)
  } finally {
    loading.value = false
  }
}

// Watch filters to refetch
watch(queryParams, () => {
  fetchOrders()
}, { deep: true, immediate: true })

// Orders Computed List
const orders = computed(() => {
  return listData.value?.data || []
})

const handleSearch = () => {
  fetchOrders()
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedType.value = 'All'
  selectedDate.value = ''
  activeTab.value = 'latest'
  fetchOrders()
}

const statusTabs = ref([
  { id: 'latest', label: 'Latest 15 Orders', count: 0, icon: LayoutGrid },
  { id: 'pending', label: 'Pending Orders', count: 0, icon: Clock },
  { id: 'approved', label: 'Approved Orders', count: 0, icon: CheckCircle2 },
  { id: 'process', label: 'Process Orders', count: 0, icon: Package },
  { id: 'courier', label: 'Courier', count: 0, icon: Truck },
  { id: 'delivered', label: 'Delivered Orders', count: 0, icon: CheckCircle2 },
  { id: 'cancelled', label: 'Cancelled Orders', count: 0, icon: XCircle },
])

const reloadPage = () => {
  window.location.reload()
}

// Drawer Refs & logic
const orderDetailsDrawer = ref(null)

const openDrawer = (order) => {
  orderDetailsDrawer.value?.openDrawer(order.id, order.type)
}

// Delete Logic
const isDeleteModalOpen = ref(false)
const orderToDelete = ref(null)
const isDeleting = ref(false)

const confirmDelete = (order) => {
  orderToDelete.value = order
  isDeleteModalOpen.value = true
}

const handleConfirmDelete = async () => {
  if (!orderToDelete.value) return
  isDeleting.value = true
  try {
    await deleteItem(`${orderToDelete.value.id}?type=${orderToDelete.value.type}`, '/vendor/orders')
    fetchOrders()
  } catch (e) {
    console.error('Failed to delete order', e)
  } finally {
    isDeleting.value = false
    isDeleteModalOpen.value = false
    orderToDelete.value = null
  }
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  height: 8px;
  width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
  border: 2px solid #f1f5f9;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 #f1f5f9;
}

.tabs-scrollbar::-webkit-scrollbar {
  height: 4px;
}

.tabs-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.tabs-scrollbar::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}

.tabs-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #cbd5e1;
}

.tabs-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: #e2e8f0 transparent;
}
</style>
