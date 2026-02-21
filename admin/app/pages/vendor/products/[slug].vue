<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 transition-colors duration-300">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div class="flex items-center gap-4">
        <button @click="$router.back()" class="p-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm active:scale-95 group">
          <ChevronLeft class="w-5 h-5 text-slate-600 dark:text-slate-300 group-hover:-translate-x-0.5 transition-transform" />
        </button>
        <div>
          <h1 class="text-2xl font-black text-slate-900 dark:text-white leading-tight tracking-tight">Products</h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 font-semibold opacity-80">Manage and track your product inventory efficiently.</p>
        </div>
      </div>
      <NuxtLink to="/vendor/products/create" class="flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl transition-all shadow-lg shadow-indigo-600/20 active:scale-95 group">
        <Plus class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" />
        Add Product
      </NuxtLink>
    </div>

    <!-- Status Tabs -->
    <div class="flex flex-wrap items-center gap-2 mb-8">
      <NuxtLink 
        v-for="tab in tabs" 
        :key="tab.value"
        :to="tab.value === 'all' ? '/vendor/products' : `/vendor/products/${tab.value}`"
        :class="[
          'px-5 py-2.5 rounded-xl font-bold text-sm transition-all duration-300 border',
          activeStatus === tab.value 
            ? 'bg-indigo-600 text-white border-indigo-600 shadow-lg shadow-indigo-600/20' 
            : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800'
        ]"
      >
        {{ tab.label }}
      </NuxtLink>
    </div>

    <!-- Filter Section Area -->
    <AppProductFilter 
      v-model:search="searchQuery"
      v-model:category="selectedCategory"
      v-model:brand="selectedBrand"
      :categories="categories"
      :brands="brands"
      @filter="fetchProducts"
      @clear="clearFilters"
    >
      <template #actions>
        <button @click="bulkDelete" :disabled="selectedItems.length === 0" :class="[
          'h-12 px-8 font-black rounded-2xl transition-all flex items-center gap-2 group whitespace-nowrap active:scale-95 shadow-lg',
          selectedItems.length > 0 ? 'bg-red-50 text-red-600 border border-red-100 hover:bg-red-600 hover:text-white shadow-red-600/5' : 'bg-slate-50 text-slate-300 border border-slate-100 cursor-not-allowed shadow-none'
        ]">
          <Trash2 class="w-4 h-4" :class="{ 'group-hover:animate-bounce': selectedItems.length > 0 }" />
          Bulk Delete
        </button>
      </template>
    </AppProductFilter>

    <!-- Confirmation Modal -->
    <AppConfirmationModal
      :isOpen="isDeleteModalOpen"
      :title="deleteModalTitle"
      :message="deleteModalMessage"
      confirmText="Yes, Delete"
      :isLoading="isDeleting"
      @close="closeDeleteModal"
      @confirm="confirmDelete"
    />

    <!-- Products Table Container -->
    <div class="bg-white dark:bg-slate-900 rounded-[24px] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.06)] dark:shadow-none border border-slate-200/60 dark:border-slate-800 group overflow-hidden transition-colors duration-300">
      <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-900 dark:bg-slate-800 border-b border-slate-800 dark:border-slate-700">
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-slate-400">
                <div class="flex items-center gap-4">
                  <span class="text-white">#</span>
                  <input type="checkbox" @change="toggleAll" :checked="isAllSelected" class="w-4 h-4 rounded border-slate-700 dark:border-slate-600 bg-transparent dark:bg-slate-700 text-indigo-600 focus:ring-indigo-500/50 cursor-pointer">
                </div>
              </th>
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-[#f8fafc] dark:text-slate-200">Product</th>
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-[#f8fafc] dark:text-slate-200">Category</th>
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-[#f8fafc] dark:text-slate-200">Brand</th>
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-[#f8fafc] dark:text-slate-200">Price</th>
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-[#f8fafc] dark:text-slate-200">Status</th>
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-[#f8fafc] dark:text-slate-200 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <!-- Empty State -->
            <tr v-if="products.length === 0">
              <td colspan="7" class="py-32 px-8 text-center">
                <div class="flex flex-col items-center justify-center gap-8 max-w-sm mx-auto animate-in fade-in zoom-in duration-700">
                  <div class="w-28 h-28 bg-slate-50 dark:bg-slate-800 rounded-[40px] flex items-center justify-center border-4 border-white dark:border-slate-700 shadow-2xl rotate-3 transition-transform hover:rotate-0 duration-500">
                    <div class="w-16 h-16 bg-white dark:bg-slate-900 rounded-full flex items-center justify-center border border-slate-100 dark:border-slate-700 shadow-inner">
                      <Box class="w-8 h-8 text-slate-300 dark:text-slate-500" />
                    </div>
                  </div>
                  <div>
                    <h3 class="text-2xl font-black text-slate-800 dark:text-slate-200 mb-3 tracking-tight">No products found</h3>
                    <p class="text-slate-500 dark:text-slate-400 font-semibold leading-relaxed px-4">
                      Try adjusting your filters or add your first product to get started.
                    </p>
                  </div>
                  <button @click="fetchProducts" class="flex items-center gap-3 px-10 py-4 bg-slate-900 hover:bg-slate-800 text-white font-black rounded-[20px] transition-all shadow-2xl shadow-slate-900/20 active:scale-95 group">
                    <RefreshCw class="w-5 h-5 group-hover:rotate-180 transition-transform duration-700" />
                    Reload Page
                  </button>
                </div>
              </td>
            </tr>
            <!-- Products Rows -->
            <tr v-else v-for="(product, index) in products" :key="product.id" class="border-b border-slate-50 dark:border-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group/row">
              <td class="px-8 py-5">
                <div class="flex items-center gap-4">
                  <span class="text-xs font-bold text-slate-400 dark:text-slate-500">#{{ index + 1 }}</span>
                  <input type="checkbox" v-model="selectedItems" :value="product.id" class="w-4 h-4 rounded border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-indigo-600 focus:ring-indigo-500/50 cursor-pointer">
                </div>
              </td>
              <td class="px-8 py-5">
                <div class="flex items-center">
                  <div class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden flex-shrink-0 shadow-sm">
                    <img v-if="product.image" :src="product.image" class="h-full w-full object-cover">
                    <div v-else class="h-full w-full flex items-center justify-center text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-800">
                      <Image class="w-5 h-5 opacity-40" />
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-black text-slate-900 dark:text-slate-100">{{ product.name }}</div>
                    <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mt-0.5">SKU: {{ product.sku || 'N/A' }}</div>
                  </div>
                </div>
              </td>
              <td class="px-8 py-5">
                <div class="flex flex-wrap gap-1.5">
                  <span v-for="cat in product.categories" :key="cat.id" class="px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-lg text-[10px] font-black uppercase tracking-wider">
                    {{ cat.name }}
                  </span>
                  <span v-if="!product.categories?.length || product.categories?.length === 0" class="text-xs text-slate-400 dark:text-slate-500 font-medium italic">Uncategorized</span>
                </div>
              </td>
              <td class="px-8 py-5">
                <span class="text-xs font-bold text-slate-600 dark:text-slate-400">{{ product.brand?.name || '—' }}</span>
              </td>
              <td class="px-8 py-5">
                <div class="text-sm font-black text-slate-900 dark:text-white">৳{{ product.sale_price }}</div>
                <div v-if="product.purchase_price" class="text-[10px] font-bold text-slate-400 line-through opacity-60 mt-0.5">৳{{ product.purchase_price }}</div>
              </td>
              <td class="px-8 py-5">
                <span :class="[
                  'px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-1.5',
                  product.status === 'published' ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20' : 
                  product.status === 'draft' ? 'bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-100 dark:border-slate-700' :
                  'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-500/20'
                ]">
                  <span class="w-1.5 h-1.5 rounded-full" :class="[
                    product.status === 'published' ? 'bg-emerald-600 dark:bg-emerald-400' : 
                    product.status === 'draft' ? 'bg-slate-500 dark:bg-slate-400' :
                    'bg-amber-600 dark:bg-amber-400'
                  ]"></span>
                  {{ product.status }}
                </span>
              </td>
              <td class="px-8 py-5 text-right">
                <div class="flex justify-end gap-2 opacity-0 group-hover/row:opacity-100 transition-all duration-300 translate-x-4 group-hover/row:translate-x-0">
                  <NuxtLink :to="`/vendor/products/edit/${product.id}`" class="p-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-600 dark:hover:bg-indigo-500 hover:text-white rounded-lg transition-all shadow-sm border border-indigo-100 dark:border-indigo-500/20">
                    <Pencil class="w-4 h-4" />
                  </NuxtLink>
                  <button @click="openDeleteModal(product.id)" class="p-2 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-600 dark:hover:bg-red-500 hover:text-white rounded-lg transition-all shadow-sm border border-red-100 dark:border-red-500/20">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  ChevronLeft, 
  ChevronDown, 
  Search, 
  X, 
  Trash2, 
  RefreshCw,
  Box,
  Plus,
  Image,
  Pencil,
  ExternalLink
} from 'lucide-vue-next'
import AppProductFilter from '~/components/AppProductFilter.vue'
import AppConfirmationModal from '~/components/AppConfirmationModal.vue'

definePageMeta({
  middleware: 'auth',
  validate: async (route) => {
    // Check if slug is one of the allowed statuses
    return ['published', 'draft', 'pending'].includes(route.params.slug)
  }
})

const route = useRoute()
const { deleteItem, getAll } = useCrud()
const products = ref([])
const categories = ref([])
const brands = ref([])
const searchQuery = ref('')
const selectedCategory = ref('')
const selectedBrand = ref('')
const selectedItems = ref([])
const activeStatus = ref(route.params.slug)

// Modal State
const isDeleteModalOpen = ref(false)
const itemToDelete = ref(null)
const isDeleting = ref(false)
const deleteMode = ref('single')

const tabs = [
  { label: 'All Products', value: 'all' },
  { label: 'Published', value: 'published' },
  { label: 'Draft', value: 'draft' },
  { label: 'Pending', value: 'pending' }
]

const deleteModalMessage = computed(() => {
  return deleteMode.value === 'bulk'
    ? `Are you sure you want to delete ${selectedItems.value.length} selected products? This action cannot be undone.`
    : 'Are you sure you want to delete this product? This action cannot be undone.'
})

const deleteModalTitle = computed(() => {
  return deleteMode.value === 'bulk' ? 'Delete Products' : 'Delete Product'
})

const isAllSelected = computed(() => {
  return products.value.length > 0 && selectedItems.value.length === products.value.length
})

const toggleAll = () => {
  if (isAllSelected.value) {
    selectedItems.value = []
  } else {
    selectedItems.value = products.value.map(p => p.id)
  }
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = ''
  selectedBrand.value = ''
  fetchProducts()
}

const fetchProducts = async () => {
  const queryParams = new URLSearchParams()
  if (searchQuery.value) queryParams.append('search', searchQuery.value)
  if (selectedCategory.value) queryParams.append('category_id', selectedCategory.value)
  if (selectedBrand.value) queryParams.append('brand_id', selectedBrand.value)
  if (activeStatus.value) queryParams.append('status', activeStatus.value)
  
  const [productsRes] = await Promise.all([
    getAll(`/vendor/products?${queryParams.toString()}`),
  ])

  if (productsRes) {
    products.value = productsRes
  }
}

const fetchAttributes = async () => {
  const [catRes, brandRes] = await Promise.all([
    getAll('/vendor/attributes/categories'),
    getAll('/vendor/attributes/brands')
  ])
  
  if (catRes) categories.value = catRes.data || catRes // Handle wrapper if exists
  if (brandRes) brands.value = brandRes.data || brandRes
}

const openDeleteModal = (id = null, mode = 'single') => {
  deleteMode.value = mode
  if (mode === 'single') {
    itemToDelete.value = id
  }
  isDeleteModalOpen.value = true
}

const closeDeleteModal = () => {
  isDeleteModalOpen.value = false
  itemToDelete.value = null
  deleteMode.value = 'single'
}

const confirmDelete = async () => {
  isDeleting.value = true
  try {
    if (deleteMode.value === 'single') {
      if (!itemToDelete.value) return
      await deleteItem(itemToDelete.value, '/vendor/products')
    } else {
      // Bulk Delete Logic
      await Promise.all(selectedItems.value.map(id => deleteItem(id, '/vendor/products')))
      selectedItems.value = [] // Clear selection
    }
    await fetchProducts()
    closeDeleteModal()
  } catch (error) {
    console.error('Failed to delete product(s)', error)
  } finally {
    isDeleting.value = false
  }
}

const bulkDelete = () => {
  openDeleteModal(null, 'bulk')
}

watch(() => route.params.slug, (newSlug) => {
  activeStatus.value = newSlug
  fetchProducts()
})

onMounted(() => {
  fetchProducts()
  fetchAttributes()
})
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
  border-radius: 10px;
}

.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 #f1f5f9;
}
</style>