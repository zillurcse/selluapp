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

    <div v-else class="space-y-8">
      <!-- KPI Cards -->
      <StockKpiCards :kpi="stockData?.kpi" />

      <!-- Inventory Matrix Table -->
      <StockTable 
        :products="stockData?.products" 
        v-model:search="search"
        v-model:page="page"
        :filter="filter"
        :sort-by="sortBy"
        :sort-order="sortOrder"
        @update:page="fetchStockData"
        @update:search="handleSearch"
        @update:filter="handleFilterUpdate"
        @update:sort="handleSortUpdate"
      />
    </div>

    <!-- Adjustment Modal -->
    <StockAdjustmentModal 
      v-model="showAdjustmentModal" 
      :products="adjustmentProducts"
      @success="fetchStockData" 
    />
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { ChevronRight } from 'lucide-vue-next'
import { debounce } from '~/utils'
import useCrud from '~/composables/useCrud'
import StockKpiCards from '~/components/vendor/reports/stock/StockKpiCards.vue'
import StockTable from '~/components/vendor/reports/stock/StockTable.vue'
import StockAdjustmentModal from '~/components/vendor/reports/stock/StockAdjustmentModal.vue'

const { getAll } = useCrud()

const stockData = ref(null)
const pending = ref(true)

const showAdjustmentModal = ref(false)
const search = ref('')
const page = ref(1)
const filter = ref('all')
const sortBy = ref('created_at')
const sortOrder = ref('desc')
const adjustmentProducts = ref([])

const fetchStockData = async () => {
    pending.value = true
    try {
        const res = await getAll('/vendor/reports/stock', {
          search: search.value,
          page: page.value,
          filter: filter.value,
          sort_by: sortBy.value,
          sort_order: sortOrder.value,
          per_page: 10
        })
        stockData.value = res
    } catch (e) {
        console.error(e)
    } finally {
        pending.value = false
    }
}

const fetchAdjustmentProducts = async () => {
    try {
        const res = await getAll('/vendor/products')
        // ProductController returns Either a paginator or a collection depending on 'limit'
        const items = res
        adjustmentProducts.value = items.map(p => ({
            id: p.id,
            name: `${p.name} (Stock: ${p.stock_qty})`
        }))
    } catch (e) {
        console.error(e)
    }
}

const handleSearch = debounce(() => {
  page.value = 1
  fetchStockData()
}, 500)

const handleFilterUpdate = (newFilter) => {
  filter.value = newFilter
  page.value = 1
  fetchStockData()
}

const handleSortUpdate = ({ column, order }) => {
  sortBy.value = column
  sortOrder.value = order
  fetchStockData()
}

onMounted(() => {
    fetchStockData()
    fetchAdjustmentProducts()
})

definePageMeta({
  middleware: 'auth'
})
</script>
