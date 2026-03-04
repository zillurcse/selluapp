<template>
  <div class="p-6 md:p-8 min-h-screen">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
          <div class="flex items-center gap-2">
            <button @click="$router.back()" class="p-2 -ml-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-300 rounded-lg transition-colors">
              <ChevronLeft class="w-5 h-5" />
            </button>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Restock Inventory</h1>
          </div>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 ml-9">Add stock to your products and track historical inventory changes.</p>
        </div>
        
        <div class="flex p-1 bg-gray-100/80 dark:bg-gray-800/80 rounded-lg shadow-inner">
          <button v-for="tab in ['restock', 'history']" :key="tab" @click="activeTab = tab" :class="[
            'px-5 py-2 text-sm font-medium capitalize rounded-md transition-all duration-200',
            activeTab === tab ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm ring-1 ring-gray-900/5' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'
          ]">
            {{ tab }}
          </button>
        </div>
      </div>

      <!-- Restock Tab -->
      <div v-if="activeTab === 'restock'" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form Column -->
        <div class="lg:col-span-2">
          <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="p-6 sm:p-8">
              <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Stock Entry Details</h2>
              
              <div class="space-y-6">
                <!-- Search -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Search Product or Scan Barcode</label>
                  <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                      <Search class="h-5 w-5 text-gray-400" aria-hidden="true" />
                    </div>
                    <input 
                      type="text" 
                      v-model="productSearch" 
                      @input="searchProducts"
                      placeholder="Search by product name, SKU, or barcode..." 
                      class="block w-full rounded-xl border-0 py-3.5 pl-11 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 text-base dark:bg-gray-800 dark:text-white dark:ring-gray-700 transition-shadow" 
                    />
                    <!-- Dropdown -->
                    <ul v-if="searchResults.length > 0" class="absolute z-10 mt-1.5 max-h-60 w-full overflow-auto rounded-xl bg-white dark:bg-gray-800 py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm border border-gray-100 dark:border-gray-700">
                      <li v-for="res in searchResults" :key="res.id" @click="selectProduct(res)" class="relative cursor-pointer select-none py-2.5 pl-3 pr-9 hover:bg-gray-50 dark:hover:bg-gray-700/50 text-gray-900 dark:text-white flex items-center gap-3 transition-colors">
                        <img :src="res.thumbnail_url || '/placeholder.png'" class="h-10 w-10 flex-shrink-0 rounded-md object-cover border border-gray-200 dark:border-gray-700" />
                        <div class="flex flex-col">
                          <span class="block truncate font-medium">{{ res.name }}</span>
                          <span class="block truncate text-xs text-gray-500 dark:text-gray-400 mt-0.5">Stock: {{ res.stock_qty }} {{ res.unit?.name }} &bull; SKU: {{ res.sku || 'N/A' }}</span>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>

                <!-- Selected Product Preview -->
                <div v-if="selectedProduct" class="rounded-xl border border-indigo-100 dark:border-indigo-900/50 bg-indigo-50/50 dark:bg-indigo-900/10 p-4 transition-all">
                   <div class="flex items-start justify-between">
                     <div class="flex items-center gap-4">
                       <img :src="selectedProduct.thumbnail_url" class="h-16 w-16 rounded-lg object-cover border border-white dark:border-gray-800 shadow-sm bg-white" />
                       <div>
                         <h3 class="font-semibold text-gray-900 dark:text-white text-base">{{ selectedProduct.name }}</h3>
                         <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Current Stock: <span class="font-medium text-gray-900 dark:text-gray-300">{{ selectedProduct.stock_qty }}</span></p>
                       </div>
                     </div>
                     <button @click="selectedProduct = null" class="text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 p-1.5 rounded-lg transition-colors">
                       <X class="h-5 w-5" />
                     </button>
                   </div>
                </div>

                <!-- Variant Selection -->
                <div v-if="selectedProduct?.variants?.length > 0">
                   <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Variant</label>
                   <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                     <div v-for="v in selectedProduct.variants" :key="v.id" @click="selectedVariant = v" :class="[
                       'cursor-pointer rounded-xl border p-4 transition-all duration-200',
                       selectedVariant?.id === v.id ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-900/20 ring-1 ring-indigo-600 shadow-sm' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800/50 hover:border-gray-300 dark:hover:border-gray-600'
                     ]">
                        <div class="flex items-start justify-between">
                          <div class="flex flex-col">
                              <span class="text-sm font-medium text-gray-900 dark:text-white" v-if="v.attributes && v.attributes.length > 0">
                                  {{ v.attributes.map(a => a.attribute?.name + ': ' + a.value).join(', ') }}
                              </span>
                              <span class="text-sm font-medium text-gray-900 dark:text-white" v-else>Variant {{ v.sku }}</span>
                              <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">SKU: {{ v.sku || 'N/A' }}</span>
                          </div>
                          <span class="inline-flex items-center rounded-md bg-gray-50 dark:bg-gray-800 px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-400 ring-1 ring-inset ring-gray-500/10">Stock: {{ v.stock_qty }}</span>
                        </div>
                     </div>
                   </div>
                </div>

                <!-- Restock Details (Qty, Cost, Supplier) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Quantity to Add <span class="text-red-500">*</span></label>
                    <input v-model="form.quantity" type="number" step="0.01" min="0.01" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 text-base dark:bg-gray-800 dark:text-white dark:ring-gray-700" />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Unit Purchase Cost <span class="text-gray-400 font-normal">(Optional)</span></label>
                    <div class="relative">
                      <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                        <span class="text-gray-500 text-base">$</span>
                      </div>
                      <input v-model="form.purchase_price" type="number" step="0.01" class="block w-full rounded-xl border-0 py-3 pl-9 pr-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 text-base dark:bg-gray-800 dark:text-white dark:ring-gray-700" />
                    </div>
                  </div>
                </div>

                <!-- Supplier -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Supplier <span class="text-gray-400 font-normal">(Optional)</span></label>
                  <select v-model="form.supplier_id" class="block w-full rounded-xl border-0 py-3 px-4 pr-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 text-base dark:bg-gray-800 dark:text-white dark:ring-gray-700">
                    <option value="">Select a supplier...</option>
                    <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.name }}</option>
                  </select>
                </div>

                <!-- Notes -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Note <span class="text-gray-400 font-normal">(Optional)</span></label>
                    <textarea v-model="form.note" rows="3" class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 text-base dark:bg-gray-800 dark:text-white dark:ring-gray-700" placeholder="E.g. Restock from new batch, damaged stock replacement..."></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar / Action Column -->
        <div class="space-y-6">
          <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 sm:p-8">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-6">Stock Summary</h2>
            <dl class="space-y-4 text-sm text-gray-600 dark:text-gray-400">
              <div class="flex justify-between items-center pb-4 border-b border-gray-200 dark:border-gray-700/50">
                <dt>Selected Product</dt>
                <dd class="font-medium text-gray-900 dark:text-white text-right max-w-[150px] truncate" :title="selectedProduct?.name">{{ selectedProduct?.name || '-' }}</dd>
              </div>
              <div v-if="selectedVariant" class="flex justify-between items-center pb-4 border-b border-gray-200 dark:border-gray-700/50">
                <dt>Variant SKU</dt>
                <dd class="font-medium text-gray-900 dark:text-white">{{ selectedVariant.sku || 'Yes' }}</dd>
              </div>
              <div class="flex justify-between items-center pb-4 border-b border-gray-200 dark:border-gray-700/50">
                <dt>Quantity to Add</dt>
                <dd class="font-medium text-green-600 dark:text-green-500">+ {{ form.quantity || 0 }}</dd>
              </div>
              <div class="flex justify-between items-center pt-2">
                <dt class="font-medium text-gray-900 dark:text-white text-base">Projected Stock</dt>
                <dd class="font-bold text-gray-900 dark:text-white text-lg">{{ projectedStock }}</dd>
              </div>
            </dl>
            
            <button @click="processRestock" :disabled="!isReady || loading" class="mt-8 w-full flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
              <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              {{ loading ? 'Processing...' : 'Confirm Restock' }}
            </button>
          </div>

          <div class="rounded-2xl border border-indigo-100 dark:border-indigo-900/40 bg-indigo-50/50 dark:bg-indigo-900/10 p-6 flex flex-col gap-4">
              <div class="flex items-center gap-3">
                <div class="p-2.5 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-lg">
                    <Truck class="w-5 h-5" />
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Supplier Directory</h3>
                </div>
              </div>
              <p class="text-sm text-gray-500 dark:text-gray-400">Manage your product source directories to maintain a professional inventory ledger.</p>
              <NuxtLink to="/vendor/products/suppliers" class="mt-2 text-sm font-medium text-indigo-600 hover:text-indigo-700 dark:hover:text-indigo-400 inline-flex items-center gap-1 group">
                  Go to Suppliers <ChevronRight class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" />
              </NuxtLink>
          </div>
        </div>
      </div>

      <!-- History Tab -->
      <div v-else class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden animate-in fade-in duration-300">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/20">
          <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Stock Movement History</h3>
          <button @click="fetchLogs" class="p-1.5 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
            <RefreshCw class="w-4 h-4" :class="{ 'animate-spin': loadingLogs }" />
          </button>
        </div>
        
        <div v-if="loadingLogs" class="py-16 flex justify-center">
          <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </div>

        <div v-else-if="logs.length === 0" class="py-16 text-center px-6">
          <div class="mx-auto w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-3">
              <Package class="h-6 w-6 text-gray-400" />
          </div>
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white">No stock movements found</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Inventory adjustments and restock logs will appear here.</p>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-800/50">
              <tr>
                <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product Info</th>
                <th scope="col" class="px-3 py-3.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action Type</th>
                <th scope="col" class="px-3 py-3.5 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Qty change</th>
                <th scope="col" class="px-3 py-3.5 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Balance</th>
                <th scope="col" class="py-3.5 pl-3 pr-6 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Remarks</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800 bg-white dark:bg-gray-900">
              <tr v-for="log in logs" :key="log.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                <td class="whitespace-nowrap py-4 pl-6 pr-3 text-sm text-gray-500 dark:text-gray-400">
                  {{ formatDate(log.created_at) }}
                </td>
                <td class="px-3 py-4 text-sm max-w-[240px]">
                  <div class="font-medium text-gray-900 dark:text-white truncate" :title="log.product?.name">{{ log.product?.name }}</div>
                  <div v-if="log.variant" class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">SKU: {{ log.variant.sku }}</div>
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm">
                  <span :class="[
                      'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                      log.type === 'restock' ? 'bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20' : 
                      log.type === 'sale' ? 'bg-blue-50 text-blue-700 ring-blue-700/10 dark:bg-blue-900/20 dark:text-blue-400 dark:ring-blue-900/30' : 
                      'bg-gray-50 text-gray-600 ring-gray-500/20 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20'
                  ]">
                      <span class="capitalize">{{ log.type }}</span>
                  </span>
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-right font-medium" :class="log.quantity > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                  {{ log.quantity > 0 ? '+' : '' }}{{ log.quantity }}
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-right text-gray-500 dark:text-gray-400">
                  <span class="line-through text-gray-400 dark:text-gray-500 text-xs mr-2">{{ log.old_stock }}</span>
                  <span class="font-medium text-gray-900 dark:text-white">{{ log.new_stock }}</span>
                </td>
                <td class="py-4 pl-3 pr-6 text-sm text-gray-500 dark:text-gray-400 max-w-[200px]">
                   <div v-if="log.supplier" class="font-medium text-gray-900 dark:text-white truncate" :title="log.supplier.name">{{ log.supplier.name }}</div>
                   <div class="truncate text-xs mt-0.5" :title="log.note">{{ log.note || '-' }}</div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'vendor', middleware: 'auth' })
import { ref, onMounted, computed, watch } from 'vue'
import { toast } from 'vue-sonner'
import { ChevronLeft, ChevronRight, Package, Search, X, Truck, RefreshCw } from 'lucide-vue-next'

const { getAll, createItem } = useCrud()

const activeTab = ref('restock')
const productSearch = ref('')
const searchResults = ref([])
const selectedProduct = ref(null)
const selectedVariant = ref(null)
const suppliers = ref([])
const logs = ref([])

const loading = ref(false)
const loadingLogs = ref(false)

const form = ref({
    product_id: '',
    variant_id: '',
    supplier_id: '',
    quantity: 1,
    purchase_price: '',
    note: ''
})

const isReady = computed(() => {
    if (!selectedProduct.value) return false
    if (selectedProduct.value.variants?.length > 0 && !selectedVariant.value) return false
    return form.value.quantity > 0
})

const projectedStock = computed(() => {
    let current = 0
    if (selectedVariant.value) current = parseFloat(selectedVariant.value.stock_qty)
    else if (selectedProduct.value) current = parseFloat(selectedProduct.value.stock_qty)
    
    return current + (parseFloat(form.value.quantity) || 0)
})

const fetchSuppliers = async () => {
    try {
        const res = await getAll('/vendor/suppliers')
        suppliers.value = res.data
    } catch (e) {
        console.error(e)
    }
}

const fetchLogs = async () => {
    loadingLogs.value = true
    try {
        const res = await getAll('/vendor/stock/logs')
        logs.value = res.data.data
    } catch (e) {
        console.error(e)
    } finally {
        loadingLogs.value = false
    }
}

const searchProducts = async () => {
    if (productSearch.value.length < 2) {
        searchResults.value = []
        return
    }
    try {
        const res = await getAll('/vendor/products', { search: productSearch.value, per_page: 5 })
        searchResults.value = res.data
    } catch (e) {
        console.error(e)
    }
}

const selectProduct = (p) => {
    selectedProduct.value = p
    selectedVariant.value = null
    form.value.product_id = p.id
    form.value.variant_id = ''
    productSearch.value = ''
    searchResults.value = []
}

watch(selectedVariant, (newV) => {
    form.value.variant_id = newV ? newV.id : ''
})

const processRestock = async () => {
    loading.value = true
    try {
        await createItem('/vendor/stock/restock', form.value, null, false)
        toast.success('Inventory updated successfully!')
        
        // Reset only partial form
        form.value.quantity = 1
        form.value.note = ''
        
        // Refresh product data if selected
        searchProducts() 
        activeTab.value = 'history'
        fetchLogs()
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit'
    })
}

onMounted(() => {
    fetchSuppliers()
    fetchLogs()
})

watch(activeTab, (newTab) => {
    if (newTab === 'history') fetchLogs()
})
</script>
