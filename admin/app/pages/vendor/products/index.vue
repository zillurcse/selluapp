<template>
  <div class="p-6 lg:p-8 bg-slate-50 dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div class="flex items-center gap-4">
        <button @click="$router.back()" class="p-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm group">
          <ChevronLeft class="w-5 h-5 text-slate-500 dark:text-slate-400 group-hover:-translate-x-0.5 transition-transform" />
        </button>
        <div>
          <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Products</h1>
          <p class="text-sm text-slate-500 dark:text-slate-400">Manage and track your product inventory.</p>
        </div>
      </div>
      <button @click="handleAddProduct" class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-all shadow-sm active:scale-[0.98]">
        <Plus class="w-4 h-4" />
        Add Product
      </button>
    </div>

    <!-- Status Tabs -->
    <div class="flex flex-wrap items-center gap-2 mb-6">
      <NuxtLink 
        v-for="tab in tabs" 
        :key="tab.value"
        :to="tab.value === 'all' ? '/vendor/products' : `/vendor/products/${tab.value}`"
        :class="[
          'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border',
          activeStatus === tab.value 
            ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 border-indigo-200 dark:border-indigo-500/30' 
            : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800'
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
      @filter="handleFilter"
      @clear="clearFilters"
    >
      <template #actions>
        <button @click="bulkDelete" :disabled="selectedItems.length === 0" :class="[
          'h-10 px-4 text-sm font-semibold rounded-lg transition-all flex items-center gap-2 shadow-sm border',
          selectedItems.length > 0 
            ? 'bg-red-50 text-red-600 border-red-100 hover:bg-red-600 hover:text-white' 
            : 'bg-slate-50 text-slate-300 border-slate-100 cursor-not-allowed'
        ]">
          <Trash2 class="w-4 h-4" />
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
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden transition-colors duration-300">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
              <th class="py-4 px-6 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                <div class="flex items-center gap-3">
                  <input type="checkbox" @change="toggleAll" :checked="isAllSelected" class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                </div>
              </th>
              <th class="py-4 px-4 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Product</th>
              <th class="py-4 px-4 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Category</th>
              <th class="py-4 px-4 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Brand</th>
              <th class="py-4 px-4 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Price</th>
              <th class="py-4 px-4 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
              <th class="py-4 px-6 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800/50">
            <!-- Empty State -->
            <tr v-if="products.length === 0">
              <td colspan="7" class="py-24 px-6 text-center">
                <div class="flex flex-col items-center justify-center gap-4 max-w-sm mx-auto">
                  <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-400">
                    <Box class="w-8 h-8" />
                  </div>
                  <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">No products found</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                      Try adjusting your filters or add your first product to get started.
                    </p>
                  </div>
                  <button @click="fetchProducts" class="mt-2 flex items-center gap-2 px-4 py-2 bg-slate-900 dark:bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition-colors">
                    <RefreshCw class="w-4 h-4" />
                    Reload Page
                  </button>
                </div>
              </td>
            </tr>
            <!-- Products Rows -->
            <tr v-else v-for="(product, index) in products" :key="product.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
              <td class="px-6 py-4">
                <input type="checkbox" v-model="selectedItems" :value="product.id" class="w-4 h-4 rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
              </td>
              <td class="px-4 py-4">
                <div class="flex items-center">
                  <div class="h-10 w-10 rounded-lg bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden flex-shrink-0 shadow-sm">
                    <img v-if="product.image" :src="product.image" class="h-full w-full object-cover">
                    <div v-else class="h-full w-full flex items-center justify-center text-slate-400 bg-slate-50 dark:bg-slate-800">
                      <Image class="w-4 h-4 opacity-40" />
                    </div>
                  </div>
                  <div class="ml-3">
                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ product.name }}</div>
                    <div class="text-[10px] font-medium text-slate-400 dark:text-slate-500 tracking-wide">SKU: {{ product.sku || 'N/A' }}</div>
                  </div>
                </div>
              </td>
              <td class="px-4 py-4">
                <div class="flex flex-wrap gap-1">
                  <span v-for="cat in product.categories" :key="cat.id" class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded text-[10px] font-semibold uppercase tracking-tight">
                    {{ cat.name }}
                  </span>
                  <span v-if="!product.categories?.length" class="text-xs text-slate-400 font-medium italic">Uncategorized</span>
                </div>
              </td>
              <td class="px-4 py-4">
                <span class="text-xs font-medium text-slate-600 dark:text-slate-400">{{ product.brand?.name || '—' }}</span>
              </td>
              <td class="px-4 py-4">
                <div class="text-sm font-semibold text-slate-900 dark:text-white">৳{{ product.sale_price }}</div>
                <div v-if="product.purchase_price" class="text-[10px] text-slate-400 line-through opacity-60">৳{{ product.purchase_price }}</div>
              </td>
              <td class="px-4 py-4">
                <span :class="[
                  'px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide inline-flex items-center gap-1.5 border',
                  product.status === 'published' ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border-emerald-100 dark:border-emerald-500/20' : 
                  product.status === 'draft' ? 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700' :
                  'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 border-amber-100 dark:border-amber-500/20'
                ]">
                  <span class="w-1.5 h-1.5 rounded-full" :class="[
                    product.status === 'published' ? 'bg-emerald-500' : 
                    product.status === 'draft' ? 'bg-slate-400' :
                    'bg-amber-500'
                  ]"></span>
                  {{ product.status }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <div class="flex justify-end gap-1.5 opacity-0 group-hover:opacity-100 transition-all duration-300">
                  <NuxtLink :to="`/vendor/products/edit/${product.id}`" class="p-1.5 bg-white dark:bg-slate-800 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 border border-slate-200 dark:border-slate-700 rounded-lg transition-all shadow-sm">
                    <Pencil class="w-4 h-4" />
                  </NuxtLink>
                  <button @click="openDeleteModal(product.id)" class="p-1.5 bg-white dark:bg-slate-800 text-slate-400 hover:text-red-600 dark:hover:text-red-400 border border-slate-200 dark:border-slate-700 rounded-lg transition-all shadow-sm">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    
    <!-- Modern Pagination Component -->
    <div v-if="totalPages > 1" class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-8 px-2">
      <div class="text-sm text-slate-500 dark:text-slate-400">
        Showing <span class="font-semibold text-slate-900 dark:text-white">{{ pagination.from }}</span> to 
        <span class="font-semibold text-slate-900 dark:text-white">{{ pagination.to }}</span> of 
        <span class="font-semibold text-slate-900 dark:text-white">{{ pagination.total }}</span> results
      </div>
      <div class="flex items-center gap-1.5">
        <button 
          @click="changePage(pagination.current_page - 1)" 
          :disabled="pagination.current_page === 1"
          class="p-2 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-500 hover:bg-white hover:text-indigo-600 disabled:opacity-40 disabled:cursor-not-allowed transition-all bg-white dark:bg-slate-900 shadow-sm"
        >
          <ChevronLeft class="w-4 h-4" />
        </button>
        <div class="flex items-center gap-1">
          <button 
            v-for="page in visiblePages" 
            :key="page"
            @click="page !== '...' && changePage(page)"
            :class="[
              'w-9 h-9 flex items-center justify-center rounded-lg text-sm font-semibold transition-all shadow-sm',
              page === pagination.current_page 
                ? 'bg-indigo-600 text-white border-indigo-600 shadow-indigo-200' 
                : page === '...' 
                  ? 'text-slate-400 cursor-default bg-transparent shadow-none' 
                  : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800'
            ]"
          >
            {{ page }}
          </button>
        </div>
        <button 
          @click="changePage(pagination.current_page + 1)" 
          :disabled="pagination.current_page === pagination.last_page"
          class="p-2 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-500 hover:bg-white hover:text-indigo-600 disabled:opacity-40 disabled:cursor-not-allowed transition-all bg-white dark:bg-slate-900 shadow-sm"
        >
          <ChevronRight class="w-4 h-4" />
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  ChevronLeft, 
  ChevronRight,
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
import { toast } from 'vue-sonner'
import { useAuthStore } from '~/stores/useAuthStore'

definePageMeta({
  middleware: 'auth',
  permissions: 'products.view'
})
const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const { deleteItem, getAll } = useCrud()
const products = ref([])
const categories = ref([])
const brands = ref([])
const searchQuery = ref('')
const selectedCategory = ref('')
const selectedBrand = ref('')
const selectedItems = ref([])
// Get status from route or default to null
const activeStatus = computed(() => route.params.status || 'all')

// Modal State
const isDeleteModalOpen = ref(false)
const itemToDelete = ref(null)
const isDeleting = ref(false)
const deleteMode = ref('single') // 'single' or 'bulk'

const tabs = [
  { label: 'All Products', value: 'all' },
  { label: 'Published', value: 'published' },
  { label: 'Draft', value: 'draft' },
  { label: 'Pending', value: 'pending' }
]

const handleAddProduct = () => {
  const user = authStore.user
  const packageDetails = user?.vendor_profile?.package || user?.owner?.vendorProfile?.package
  const limit = packageDetails?.product_limit

  if (limit !== undefined && limit !== -1 && pagination.value.total >= limit) {
    toast.error(`Product limit reached (${pagination.value.total}/${limit}). Please upgrade your plan.`, {
      description: 'You cannot add more products with your current subscription.'
    })
  } else {
    router.push('/vendor/products/create')
  }
}

const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
  per_page: 10,
  from: 0,
  to: 0
})

const totalPages = computed(() => pagination.value.last_page)

const changePage = (page) => {
  if (page < 1 || page > pagination.value.last_page) return
  pagination.value.current_page = page
  fetchProducts()
}

const visiblePages = computed(() => {
  const current = pagination.value.current_page
  const last = pagination.value.last_page
  
  if (last <= 7) return Array.from({ length: last }, (_, i) => i + 1)
  if (current <= 4) return [1, 2, 3, 4, 5, '...', last]
  if (current >= last - 3) return [1, '...', last - 4, last - 3, last - 2, last - 1, last]
  return [1, '...', current - 1, current, current + 1, '...', last]
})

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

const handleFilter = () => {
  pagination.value.current_page = 1
  fetchProducts()
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = ''
  selectedBrand.value = ''
  pagination.value.current_page = 1
  fetchProducts()
}

const fetchProducts = async () => {
  const queryParams = new URLSearchParams()
  if (searchQuery.value) queryParams.append('search', searchQuery.value)
  if (selectedCategory.value) queryParams.append('category_id', selectedCategory.value)
  if (selectedBrand.value) queryParams.append('brand_id', selectedBrand.value)
  if (activeStatus.value && activeStatus.value !== 'all') queryParams.append('status', activeStatus.value)
  
  queryParams.append('per_page', pagination.value.per_page)
  queryParams.append('page', pagination.value.current_page)

  const [productsRes] = await Promise.all([
    getAll(`/vendor/products?${queryParams.toString()}`),
  ])

  if (productsRes) {
    if (productsRes.data && productsRes.current_page !== undefined) {
      products.value = productsRes.data
      pagination.value = {
        current_page: productsRes.current_page,
        last_page: productsRes.last_page,
        total: productsRes.total,
        per_page: productsRes.per_page,
        from: productsRes.from || 0,
        to: productsRes.to || 0
      }
    } else {
      products.value = productsRes.data || productsRes
      pagination.value.total = products.value.length
      pagination.value.from = products.value.length > 0 ? 1 : 0
      pagination.value.to = products.value.length
      pagination.value.last_page = 1
    }
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
      // Since API might not support bulk delete yet, we loop. 
      // Ideally this should be a single API call like POST /vendor/products/bulk-delete { ids: [] }
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

// Watch for route changes to update view and fetch data
watch(() => route.params.status, () => {
  pagination.value.current_page = 1
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

