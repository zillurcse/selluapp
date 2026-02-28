<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div class="flex items-center gap-4">
        <button @click="$router.back()" class="p-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm active:scale-95 group">
          <ChevronLeft class="w-5 h-5 text-slate-600 dark:text-slate-300 group-hover:-translate-x-0.5 transition-transform" />
        </button>
        <div>
          <h1 class="text-2xl font-black text-slate-900 dark:text-white leading-tight tracking-tight">Restock Inventory</h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 font-semibold opacity-80">Add stock and track purchase history professionally.</p>
        </div>
      </div>
      
      <div class="flex p-1 bg-slate-100 dark:bg-slate-800 rounded-2xl">
        <button v-for="tab in ['restock', 'history']" :key="tab" @click="activeTab = tab" :class="[
          'px-6 py-2.5 text-xs font-black uppercase tracking-widest rounded-xl transition-all',
          activeTab === tab ? 'bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
        ]">
          {{ tab }}
        </button>
      </div>
    </div>

    <!-- Restock Tab -->
    <div v-if="activeTab === 'restock'" class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
      <!-- Form Column -->
      <div class="lg:col-span-2 space-y-6">
        <div class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-200/60 dark:border-slate-800 p-8 shadow-sm">
          <h2 class="text-lg font-black text-slate-800 dark:text-white mb-6 flex items-center gap-2">
            <Package class="w-5 h-5 text-indigo-500" />
            Stock Entry
          </h2>
          
          <div class="space-y-6">
            <!-- Product Search -->
            <div class="relative">
              <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">Search Product or Scan Barcode</label>
              <div class="relative group">
                <input 
                  type="text" 
                  v-model="productSearch" 
                  @input="searchProducts"
                  placeholder="Type product name or scan..." 
                  class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl pl-12 pr-5 py-4 text-sm font-bold focus:ring-2 focus:ring-indigo-500/20 transition-all placeholder:text-slate-400" 
                />
                <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-300 group-focus-within:text-indigo-500 transition-colors" />
                
                <!-- Results Dropdown -->
                <div v-if="searchResults.length > 0" class="absolute left-0 right-0 top-full mt-2 z-50 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[24px] shadow-2xl p-2 max-h-[300px] overflow-y-auto">
                  <button v-for="res in searchResults" :key="res.id" @click="selectProduct(res)" class="w-full flex items-center gap-4 p-3 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl transition-all text-left group">
                    <img :src="res.thumbnail_url || '/placeholder.png'" class="w-12 h-12 rounded-lg object-cover shadow-sm" />
                    <div class="flex-1">
                      <div class="font-bold text-slate-900 dark:text-white text-sm group-hover:text-indigo-600 transition-colors">{{ res.name }}</div>
                      <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Stock: {{ res.stock_qty }} {{ res.unit?.name }}</div>
                    </div>
                  </button>
                </div>
              </div>
            </div>

            <!-- Selected Product Preview -->
            <div v-if="selectedProduct" class="bg-indigo-50/50 dark:bg-indigo-500/5 rounded-2xl p-6 border border-indigo-100/50 dark:border-indigo-500/10 flex items-center gap-6 animate-in zoom-in-95 duration-300">
               <img :src="selectedProduct.thumbnail_url" class="w-20 h-20 rounded-2xl object-cover shadow-md border-4 border-white dark:border-slate-800" />
               <div class="flex-1">
                 <h3 class="font-black text-slate-900 dark:text-white">{{ selectedProduct.name }}</h3>
                 <p class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest mt-1">Current Stock: {{ selectedProduct.stock_qty }}</p>
               </div>
               <button @click="selectedProduct = null" class="p-2 text-slate-400 hover:text-rose-500 transition-colors">
                 <X class="w-5 h-5" />
               </button>
            </div>

            <!-- Variant Selection if exists -->
            <div v-if="selectedProduct?.variants?.length > 0">
               <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">Choose Variant</label>
               <div class="grid grid-cols-2 gap-3">
                 <button v-for="v in selectedProduct.variants" :key="v.id" @click="selectedVariant = v" :class="[
                   'flex items-center gap-3 p-4 rounded-2xl border transition-all text-left group',
                   selectedVariant?.id === v.id ? 'bg-indigo-600 border-indigo-600 text-white shadow-lg' : 'bg-slate-50 dark:bg-slate-800 border-transparent hover:border-slate-200 dark:hover:border-slate-700'
                 ]">
                    <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center font-black">V</div>
                    <div>
                      <div class="text-xs font-black uppercase tracking-widest">{{ v.sku }}</div>
                      <div class="text-[10px] opacity-70">Stock: {{ v.stock_qty }}</div>
                    </div>
                 </button>
               </div>
            </div>

            <!-- Restock Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">Quantity to Add</label>
                <input v-model="form.quantity" type="number" step="0.01" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm font-black focus:ring-2 focus:ring-indigo-500/20 transition-all" />
              </div>
              <div>
                <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">Unit Cost (Optional)</label>
                <div class="relative">
                  <input v-model="form.purchase_price" type="number" step="0.01" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl pl-10 pr-5 py-4 text-sm font-black focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                  <span class="absolute left-4 top-1/2 -translate-y-1/2 font-black text-slate-400">$</span>
                </div>
              </div>
            </div>

            <!-- Supplier -->
            <div>
              <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">Supplier</label>
              <select v-model="form.supplier_id" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-indigo-500/20 transition-all appearance-none">
                <option value="">Select a supplier</option>
                <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.name }}</option>
              </select>
            </div>

            <!-- Notes -->
            <div>
                <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">Notes</label>
                <textarea v-model="form.note" rows="3" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm font-bold focus:ring-2 focus:ring-indigo-500/20 transition-all placeholder:font-medium" placeholder="E.g. Monthly restock, Damage correction..."></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Column -->
      <div class="space-y-6">
        <div class="bg-slate-900 rounded-[32px] p-8 text-white shadow-2xl">
          <h2 class="text-xl font-black mb-6">Summary</h2>
          <div class="space-y-4 mb-8">
            <div class="flex justify-between items-center text-sm">
              <span class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Product</span>
              <span class="font-black text-right">{{ selectedProduct?.name || '-' }}</span>
            </div>
            <div v-if="selectedVariant" class="flex justify-between items-center text-sm border-t border-white/5 pt-4">
              <span class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Variant</span>
              <span class="font-black">{{ selectedVariant.sku }}</span>
            </div>
            <div class="flex justify-between items-center text-sm border-t border-white/5 pt-4">
              <span class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Add Qty</span>
              <span class="font-black text-emerald-400">+ {{ form.quantity || 0 }}</span>
            </div>
            <div class="flex justify-between items-center text-sm border-t border-white/5 pt-4">
              <span class="text-slate-400 font-bold uppercase tracking-widest text-[10px]">Projected Stock</span>
              <span class="font-black">{{ projectedStock }}</span>
            </div>
          </div>
          
          <button @click="processRestock" :disabled="!isReady || loading" class="w-full py-4.5 bg-indigo-500 hover:bg-indigo-400 text-white font-black rounded-2xl transition-all shadow-lg active:scale-95 disabled:opacity-30 disabled:grayscale">
            {{ loading ? 'Processing...' : 'Complete Restock' }}
          </button>
        </div>

        <div class="bg-indigo-600 rounded-[32px] p-8 text-white">
            <div class="flex items-center gap-4 mb-4">
                <div class="p-3 bg-white/10 rounded-2xl">
                    <Truck class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-[10px] font-black uppercase tracking-wider opacity-60">Manage</div>
                    <div class="text-lg font-black">Suppliers</div>
                </div>
            </div>
            <p class="text-sm opacity-70 font-medium mb-6">Manage your product source directories to maintain a professional inventory ledger.</p>
            <NuxtLink to="/vendor/products/suppliers" class="block w-full text-center py-3 bg-white text-indigo-600 font-black rounded-xl hover:bg-indigo-50 transition-colors">
                Go to Suppliers
            </NuxtLink>
        </div>
      </div>
    </div>

    <!-- History Tab -->
    <div v-else class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-200/60 dark:border-slate-800 overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-500 shadow-sm">
        <div class="flex justify-between items-center p-8 border-b border-slate-100 dark:border-slate-800">
            <h2 class="text-lg font-black text-slate-800 dark:text-white">Stock Movement History</h2>
            <button @click="fetchLogs" class="p-2 text-slate-400 hover:text-indigo-600 transition-colors">
                <RefreshCw class="w-5 h-5" :class="{ 'animate-spin': loadingLogs }" />
            </button>
        </div>

        <div v-if="loadingLogs" class="flex justify-center py-20">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
        </div>

        <div v-else-if="logs.length === 0" class="text-center py-20">
            <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">No movements recorded yet.</p>
        </div>

        <div v-else class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50 dark:bg-slate-800/50">
                    <tr>
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Date & Item</th>
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Type</th>
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Qty</th>
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Before/After</th>
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Supplier/Note</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800/40">
                    <tr v-for="log in logs" :key="log.id" class="hover:bg-slate-50/30 dark:hover:bg-slate-800/20 transition-colors">
                        <td class="px-8 py-4">
                            <div class="text-[10px] text-slate-400 font-bold mb-1">{{ formatDate(log.created_at) }}</div>
                            <div class="font-bold text-slate-900 dark:text-white text-sm">{{ log.product?.name }}</div>
                            <div v-if="log.variant" class="text-[9px] text-indigo-500 font-black uppercase tracking-tighter">SKU: {{ log.variant.sku }}</div>
                        </td>
                        <td class="px-8 py-4">
                            <span :class="[
                                'px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-tighter',
                                log.type === 'restock' ? 'bg-emerald-50 text-emerald-600' : 
                                log.type === 'sale' ? 'bg-indigo-50 text-indigo-600' : 'bg-slate-50 text-slate-600'
                            ]">
                                {{ log.type }}
                            </span>
                        </td>
                        <td class="px-8 py-4 text-center">
                            <span class="font-black" :class="log.quantity > 0 ? 'text-emerald-500' : 'text-rose-500'">
                                {{ log.quantity > 0 ? '+' : '' }}{{ log.quantity }}
                            </span>
                        </td>
                        <td class="px-8 py-4 text-center">
                            <div class="text-xs font-bold text-slate-400 line-through">{{ log.old_stock }}</div>
                            <div class="text-sm font-black text-slate-900 dark:text-white">{{ log.new_stock }}</div>
                        </td>
                        <td class="px-8 py-4 max-w-[200px]">
                            <div class="text-[10px] font-black text-slate-500 truncate" v-if="log.supplier">{{ log.supplier.name }}</div>
                            <div class="text-[10px] text-slate-400 italic font-medium line-clamp-2">{{ log.note || '-' }}</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'vendor', middleware: 'auth' })
import { ref, onMounted, computed, watch } from 'vue'
import { toast } from 'vue-sonner'
import { ChevronLeft, Package, Search, X, Truck, RefreshCw } from 'lucide-vue-next'

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

<style scoped>
.flex-2 { flex: 2; }
</style>
