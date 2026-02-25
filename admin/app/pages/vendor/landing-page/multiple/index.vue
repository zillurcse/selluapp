<template>
  <div class="flex flex-col h-full bg-gray-50/50 dark:bg-slate-950 min-h-screen transition-colors duration-300 p-10">
    <!-- Header -->
    <div class="flex items-center justify-between px-8 py-6 bg-white dark:bg-slate-900 border-b border-gray-100 dark:border-slate-800 transition-colors duration-300">
      <div class="flex items-center">
        <button @click="$router.back()" class="p-2 bg-black text-white rounded-full hover:bg-gray-800 transition-colors mr-6 shadow-sm">
          <ArrowLeft class="w-5 h-5" />
        </button>
        <div>
          <div class="flex items-center">
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
              {{ store.isEdit ? 'Edit Multiple Products Page' : 'Multiple Products Landing Page' }}
            </h1>
            <span class="ml-4 bg-orange-500 text-white text-[10px] font-black px-2.5 py-0.5 rounded-full tracking-widest uppercase shadow-sm">PRO</span>
          </div>
          <p class="text-gray-500 dark:text-slate-400 mt-1">
            {{ store.isEdit ? 'Update your multi-product landing page details.' : 'Showcase 3-4 products together on a single landing page to maximize bundle sales.' }}
          </p>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto w-full p-8 space-y-12">
      
      <!-- Template Selection Section -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Template 1: Bundle Grid -->
        <div 
          @click="selectedTemplate = 'bundle_grid'"
          class="relative group cursor-pointer transition-all duration-300"
        >
          <div 
            class="aspect-video rounded-2xl overflow-hidden border-4 transition-all duration-300 shadow-md"
            :class="selectedTemplate === 'bundle_grid' ? 'border-orange-500 ring-4 ring-orange-100 dark:ring-orange-900/40 scale-[1.02]' : 'border-white dark:border-slate-800 hover:border-gray-200 dark:hover:border-slate-700'"
          >
            <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&q=80&w=800" alt="Bundle Grid" class="w-full h-full object-cover grayscale-[0.2] group-hover:grayscale-0 transition-all duration-500" />
            <div v-if="selectedTemplate === 'bundle_grid'" class="absolute inset-0 bg-orange-500/10 flex items-center justify-center">
              <div class="bg-orange-500 text-white rounded-full p-2 shadow-lg">
                <Check class="w-6 h-6" />
              </div>
            </div>
          </div>
          <div class="mt-4">
            <h3 class="font-bold text-gray-900 dark:text-white text-lg">Bundle Grid Theme</h3>
            <p class="text-sm text-gray-500 dark:text-slate-400">Perfect for high-contrast product grids.</p>
          </div>
        </div>

        <!-- Template 2: Showcase Slider -->
        <div 
          @click="selectedTemplate = 'showcase_slider'"
          class="relative group cursor-pointer transition-all duration-300"
        >
          <div 
            class="aspect-video rounded-2xl overflow-hidden border-4 transition-all duration-300 shadow-md"
            :class="selectedTemplate === 'showcase_slider' ? 'border-orange-500 ring-4 ring-orange-100 dark:ring-orange-900/40 scale-[1.02]' : 'border-white dark:border-slate-800 hover:border-gray-200 dark:hover:border-slate-700'"
          >
            <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=800" alt="Showcase Slider" class="w-full h-full object-cover grayscale-[0.2] group-hover:grayscale-0 transition-all duration-500" />
            <div v-if="selectedTemplate === 'showcase_slider'" class="absolute inset-0 bg-orange-500/10 flex items-center justify-center">
              <div class="bg-orange-500 text-white rounded-full p-2 shadow-lg">
                <Check class="w-6 h-6" />
              </div>
            </div>
          </div>
          <div class="mt-4">
            <h3 class="font-bold text-gray-900 dark:text-white text-lg">Showcase Slider Theme</h3>
            <p class="text-sm text-gray-500 dark:text-slate-400">Elegant slider for premium collections.</p>
          </div>
        </div>

        <!-- Template 3: Vertical Stack -->
        <div 
          @click="selectedTemplate = 'vertical_stack'"
          class="relative group cursor-pointer transition-all duration-300"
        >
          <div 
            class="aspect-video rounded-2xl overflow-hidden border-4 transition-all duration-300 shadow-md"
            :class="selectedTemplate === 'vertical_stack' ? 'border-orange-500 ring-4 ring-orange-100 dark:ring-orange-900/40 scale-[1.02]' : 'border-white dark:border-slate-800 hover:border-gray-200 dark:hover:border-slate-700'"
          >
            <img src="https://images.unsplash.com/photo-1481437156560-3205f6a55735?auto=format&fit=crop&q=80&w=800" alt="Vertical Stack" class="w-full h-full object-cover grayscale-[0.2] group-hover:grayscale-0 transition-all duration-500" />
            <div v-if="selectedTemplate === 'vertical_stack'" class="absolute inset-0 bg-orange-500/10 flex items-center justify-center">
              <div class="bg-orange-500 text-white rounded-full p-2 shadow-lg">
                <Check class="w-6 h-6" />
              </div>
            </div>
          </div>
          <div class="mt-4 text-center md:text-left">
            <h3 class="font-bold text-gray-900 dark:text-white text-lg">Vertical Stack Theme</h3>
            <p class="text-sm text-gray-500 dark:text-slate-400">Focused content sections for each product.</p>
          </div>
        </div>
      </div>

      <!-- Form Section -->
      <div class="bg-white dark:bg-slate-900 rounded-3xl p-10 shadow-xl border border-gray-100 dark:border-slate-800 max-w-5xl mx-auto space-y-10 transition-colors duration-300">
        <div class="space-y-8">
          
          <!-- Page Title -->
          <div class="space-y-3">
            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider ml-1">Landing Page Title</label>
            <input 
              v-model="formData.title"
              type="text" 
              placeholder="e.g., Summer Collection 2024" 
              class="w-full h-14 px-6 bg-gray-50 dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-2xl focus:border-orange-500 focus:bg-white dark:focus:bg-slate-700 transition-all outline-none font-medium dark:text-slate-200"
            />
          </div>

          <!-- Product Selection -->
          <div class="space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
              <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider ml-1">Select Products (Max 4)</label>
              
              <!-- Search Bar -->
              <div class="relative w-full md:w-80">
                <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                <input 
                  v-model="searchQuery"
                  type="text" 
                  placeholder="Search products..." 
                  class="w-full h-11 pl-11 pr-4 bg-gray-50 dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-xl focus:border-orange-500 transition-all outline-none text-sm dark:text-slate-200"
                  @input="handleSearch"
                />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div v-for="product in products" :key="product.id" 
                @click="toggleProduct(product.id)"
                class="flex items-center p-4 rounded-2xl border-2 transition-all cursor-pointer"
                :class="formData.selectedProducts.includes(product.id) ? 'border-orange-500 bg-orange-50/50 dark:bg-orange-900/10' : 'border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800 hover:border-gray-200 dark:hover:border-slate-700'"
              >
                <div class="w-12 h-12 rounded-lg bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-700 flex items-center justify-center mr-4">
                  <Package class="w-6 h-6 text-gray-400" />
                </div>
                <div class="flex-1">
                  <div class="font-bold text-gray-900 dark:text-slate-100 text-sm">{{ product.name }}</div>
                  <div class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">৳{{ product.sale_price }}</div>
                </div>
                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-colors"
                  :class="formData.selectedProducts.includes(product.id) ? 'bg-orange-500 border-orange-500' : 'border-gray-300'"
                >
                  <Check v-if="formData.selectedProducts.includes(product.id)" class="w-4 h-4 text-white" />
                </div>
              </div>
            </div>

            <!-- Loading & Load More -->
            <div class="flex flex-col items-center justify-center pt-4 space-y-4">
              <div v-if="loading" class="flex items-center space-x-2 text-gray-500">
                <Loader2 class="w-5 h-5 animate-spin" />
                <span class="text-sm font-medium">Loading products...</span>
              </div>
              
              <button 
                v-if="hasMore && !loading" 
                @click="loadMore"
                class="px-6 py-2 bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400 rounded-full text-sm font-bold hover:bg-gray-200 dark:hover:bg-slate-700 transition-all active:scale-95"
              >
                Load More Products
              </button>

              <div v-if="!hasMore && products.length > 0" class="text-xs text-gray-400">
                No more products to load
              </div>

              <div v-if="!loading && products.length === 0" class="flex flex-col items-center py-6">
                <PackageSearch class="w-12 h-12 text-gray-200 mb-2" />
                <p class="text-gray-500 text-sm">No products found matching your search</p>
              </div>
            </div>

            <div class="flex items-center justify-between pt-2">
              <p class="text-xs text-gray-400 ml-1">Selected: {{ formData.selectedProducts.length }} / 4 products</p>
            </div>
          </div>

        </div>

        <!-- Submit Button -->
        <button 
          @click="submitLandingPage"
          class="w-full h-16 bg-black dark:bg-slate-800 text-white rounded-2xl font-black text-xl hover:bg-gray-800 dark:hover:bg-slate-700 active:scale-[0.98] transition-all shadow-xl flex items-center justify-center group"
          :disabled="!isValid || store.isLoading"
          :class="{'opacity-50 cursor-not-allowed': !isValid || store.isLoading}"
        >
          {{ store.isEdit ? 'Update Multi-Product Page' : 'Create Multi-Product Page' }}
          <ArrowRight v-if="!store.isLoading" class="w-6 h-6 ml-3 group-hover:translate-x-1 transition-transform" />
          <Loader2 v-else class="w-6 h-6 ml-3 animate-spin" />
        </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { 
  ArrowLeft,
  ArrowRight,
  Check, 
  Package,
  Search,
  Loader2,
  PackageSearch
} from 'lucide-vue-next'

definePageMeta({
  layout: 'default',
  middleware: 'auth',
  permissions: 'landing_pages.create'
})

const { getAll, createItem, getById, updateItem } = useCrud()
const route = useRoute()
const utilityStore = useUtilityStore()
utilityStore.pageBackLink = '/vendor/landing-page/all'

// Set edit mode based on query param
onMounted(() => {
  if (route.query.id) {
    utilityStore.isEdit = true
    fetchPageData(route.query.id)
  } else {
    utilityStore.isEdit = false
  }
})

const store = utilityStore

// Search and Pagination state
const products = ref([])
const loading = ref(false)
const searchQuery = ref('')
const currentPage = ref(1)
const hasMore = ref(true)
const perPage = 8

let searchTimeout = null

const selectedTemplate = ref('bundle_grid')
const formData = ref({
  title: '',
  selectedProducts: [],
  landing_page_type: 'multiple',
  template_name: 'bundle_grid',
  status: 'active',
  settings: {}
})

// Fetch products
const fetchProducts = async (page = 1, append = false) => {
  loading.value = true
  try {
    const res = await getAll('/vendor/products', {
      search: searchQuery.value,
      per_page: perPage,
      page: page
    })
    
    // Check if it's paginated response (standard Laravel format)
    const newProducts = res.data || res || []
    
    if (append) {
      products.value = [...products.value, ...newProducts]
    } else {
      products.value = newProducts
    }
    
    // Check if there are more products to load
    hasMore.value = newProducts.length === perPage
  } catch (error) {
    console.error('Failed to fetch products:', error)
  } finally {
    loading.value = false
  }
}

const handleSearch = () => {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    currentPage.value = 1
    fetchProducts(1, false)
  }, 500)
}

const loadMore = () => {
  if (!loading.value && hasMore.value) {
    currentPage.value++
    fetchProducts(currentPage.value, true)
  }
}

// Fetch single page data (for edit)
const fetchPageData = async (id) => {
  try {
    const res = await getById('/vendor/landing-pages', id)
    if (res) {
      formData.value = {
        title: res.title,
        selectedProducts: res.settings?.product_ids || [res.product_id],
        landing_page_type: res.landing_page_type,
        template_name: res.template_name,
        status: res.status,
        settings: res.settings || {}
      }
      selectedTemplate.value = res.template_name
    }
  } catch (error) {
    console.error('Failed to fetch page data:', error)
  }
}

onMounted(() => {
  fetchProducts()
})

const toggleProduct = (id) => {
  const index = formData.value.selectedProducts.indexOf(id)
  if (index > -1) {
    formData.value.selectedProducts.splice(index, 1)
  } else if (formData.value.selectedProducts.length < 4) {
    formData.value.selectedProducts.push(id)
  }
}

const isValid = computed(() => {
  return selectedTemplate.value && 
         formData.value.title.trim() && 
         formData.value.selectedProducts.length > 0
})

watch(selectedTemplate, (val) => {
  formData.value.template_name = val
})

const submitLandingPage = async () => {
  if (!isValid.value) return
  
  try {
    const payload = {
      ...formData.value,
      template_name: selectedTemplate.value,
      product_id: formData.value.selectedProducts[0],
      settings: {
        ...formData.value.settings,
        product_ids: formData.value.selectedProducts
      }
    }
    
    if (store.isEdit) {
      await updateItem(`/vendor/landing-pages/${route.query.id}`, payload)
      navigateTo('/vendor/landing-page/all')
    } else {
      await createItem('/vendor/landing-pages', payload)
    }
  } catch (error) {
    console.error('Error submitting landing page:', error)
  }
}
</script>
