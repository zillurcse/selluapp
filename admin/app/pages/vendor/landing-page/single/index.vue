<template>
  <div class="flex flex-col h-full bg-gray-50/50 dark:bg-slate-950 min-h-screen transition-colors duration-300 p-10">
    <!-- Header -->
    <div class="flex items-center justify-between px-8 py-6 bg-white dark:bg-slate-900 border-b border-gray-100 dark:border-slate-800 transition-colors duration-300">
      <div class="flex items-center">
        <button @click="$router.back()" class="p-2 bg-black text-white rounded-full hover:bg-gray-800 transition-colors mr-6 shadow-sm">
          <ArrowLeft class="w-5 h-5" />
        </button>
        <div>
          <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
            {{ store.isEdit ? 'Edit Your Landing Page' : 'Build Your Landing Page' }}
          </h1>
          <p class="text-gray-500 dark:text-slate-400 mt-1">
            {{ store.isEdit ? 'Update your landing page details below.' : 'Select a template, fill in product details, and create your landing page easily.' }}
          </p>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto w-full p-8 space-y-12">
      
      <!-- Template Selection Section -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Template 1: Modern -->
        <div 
          @click="selectedTemplate = 'modern'"
          class="relative group cursor-pointer transition-all duration-300"
        >
          <div 
            class="aspect-[4/3] rounded-2xl overflow-hidden border-4 transition-all duration-300 shadow-md"
            :class="selectedTemplate === 'modern' ? 'border-blue-500 ring-4 ring-blue-100 dark:ring-blue-900/40 scale-[1.02]' : 'border-white dark:border-slate-800 hover:border-gray-200 dark:hover:border-slate-700'"
          >
            <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&q=80&w=800" alt="Modern Landing Page" class="w-full h-full object-cover grayscale-[0.2] group-hover:grayscale-0 transition-all duration-500" />
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-4">
              <span class="text-white font-semibold flex items-center"><Eye class="w-4 h-4 mr-2" /> Preview Template</span>
            </div>
            
            <!-- Selected Overlay -->
            <div v-if="selectedTemplate === 'modern'" class="absolute inset-0 bg-blue-500/10 flex items-center justify-center">
              <div class="bg-blue-500 text-white rounded-full p-2 shadow-lg">
                <Check class="w-6 h-6" />
              </div>
            </div>
          </div>
          <div class="mt-4 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 dark:text-white text-lg">The Modern Landing Page</h3>
            <span v-if="selectedTemplate === 'modern'" class="text-blue-600 text-sm font-bold flex items-center">
              <CheckCircle2 class="w-4 h-4 mr-1" /> Selected
            </span>
            <span v-else class="text-xs font-semibold px-2 py-1 bg-green-100 text-green-700 rounded-md">FREE</span>
          </div>
        </div>

        <!-- Template 2: Premium 1 -->
        <div 
          @click="selectedTemplate = 'premium_1'"
          class="relative group cursor-pointer transition-all duration-300"
        >
          <div class="absolute -top-3 left-4 z-10">
            <span class="bg-yellow-400 text-white text-[10px] font-black px-2.5 py-1 rounded shadow-sm tracking-widest uppercase">PREMIUM</span>
          </div>
          <div 
            class="aspect-[4/3] rounded-2xl overflow-hidden border-4 transition-all duration-300 shadow-md"
            :class="selectedTemplate === 'premium_1' ? 'border-blue-500 ring-4 ring-blue-100 dark:ring-blue-900/40 scale-[1.02]' : 'border-white dark:border-slate-800 hover:border-gray-200 dark:hover:border-slate-700'"
          >
            <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&q=80&w=800" alt="Premium Template 1" class="w-full h-full object-cover grayscale-[0.2] group-hover:grayscale-0 transition-all duration-500" />
            
            <div v-if="selectedTemplate === 'premium_1'" class="absolute inset-0 bg-blue-500/10 flex items-center justify-center">
              <div class="bg-blue-500 text-white rounded-full p-2 shadow-lg">
                <Check class="w-6 h-6" />
              </div>
            </div>
          </div>
          <div class="mt-4">
            <h3 class="font-bold text-gray-900 dark:text-white text-lg">Vibrant Pink Theme</h3>
          </div>
        </div>

        <!-- Template 3: Premium 2 -->
        <div 
          @click="selectedTemplate = 'premium_2'"
          class="relative group cursor-pointer transition-all duration-300"
        >
          <div class="absolute -top-3 left-4 z-10">
            <span class="bg-yellow-400 text-white text-[10px] font-black px-2.5 py-1 rounded shadow-sm tracking-widest uppercase">PREMIUM</span>
          </div>
          <div 
            class="aspect-[4/3] rounded-2xl overflow-hidden border-4 transition-all duration-300 shadow-md"
            :class="selectedTemplate === 'premium_2' ? 'border-blue-500 ring-4 ring-blue-100 dark:ring-blue-900/40 scale-[1.02]' : 'border-white dark:border-slate-800 hover:border-gray-200 dark:hover:border-slate-700'"
          >
            <img src="https://images.unsplash.com/photo-1491553895911-0055eca6402d?auto=format&fit=crop&q=80&w=800" alt="Premium Template 2" class="w-full h-full object-cover grayscale-[0.2] group-hover:grayscale-0 transition-all duration-500" />
            
            <div v-if="selectedTemplate === 'premium_2'" class="absolute inset-0 bg-blue-500/10 flex items-center justify-center">
              <div class="bg-blue-500 text-white rounded-full p-2 shadow-lg">
                <Check class="w-6 h-6" />
              </div>
            </div>
          </div>
          <div class="mt-4">
            <h3 class="font-bold text-gray-900 dark:text-white text-lg">Ocean Breeze Theme</h3>
          </div>
        </div>

        <!-- Template 4: White Modern -->
        <div 
          @click="selectedTemplate = 'white_modern'"
          class="relative group cursor-pointer transition-all duration-300"
        >
          <div class="absolute -top-3 left-4 z-10">
            <span class="bg-yellow-400 text-white text-[10px] font-black px-2.5 py-1 rounded shadow-sm tracking-widest uppercase">PREMIUM</span>
          </div>
          <div 
            class="aspect-[4/3] rounded-2xl overflow-hidden border-4 transition-all duration-300 shadow-md"
            :class="selectedTemplate === 'white_modern' ? 'border-blue-500 ring-4 ring-blue-100 dark:ring-blue-900/40 scale-[1.02]' : 'border-white dark:border-slate-800 hover:border-gray-200 dark:hover:border-slate-700'"
          >
            <img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&q=80&w=800" alt="White Modern Theme" class="w-full h-full object-cover grayscale-[0.2] group-hover:grayscale-0 transition-all duration-500" />
            
            <div v-if="selectedTemplate === 'white_modern'" class="absolute inset-0 bg-blue-500/10 flex items-center justify-center">
              <div class="bg-blue-500 text-white rounded-full p-2 shadow-lg">
                <Check class="w-6 h-6" />
              </div>
            </div>
          </div>
          <div class="mt-4">
            <h3 class="font-bold text-gray-900 dark:text-white text-lg">White Modern Theme</h3>
          </div>
        </div>
      </div>

      <!-- Instruction Title -->
      <div class="text-center py-4 border-y border-gray-100 dark:border-slate-800">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center justify-center uppercase tracking-wide">
          <PartyPopper class="w-6 h-6 mr-3 text-blue-500" />
          Please choose a template and fill in the details
        </h2>
      </div>

      <!-- Form Section -->
      <div class="bg-white dark:bg-slate-900 rounded-3xl p-10 shadow-xl border border-gray-100 dark:border-slate-800 max-w-5xl mx-auto space-y-8 transition-colors duration-300">
        </div>

        <!-- Product Selection Section -->
        <div class="space-y-6">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider ml-1">Select Product</label>
            
            <!-- Search Bar -->
            <div class="relative w-full md:w-96">
              <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
              <input 
                v-model="searchQuery"
                type="text" 
                placeholder="Search products..." 
                class="w-full h-12 pl-12 pr-4 bg-gray-50 dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-2xl focus:border-blue-500 transition-all outline-none font-medium dark:text-slate-200"
                @input="handleSearch"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div v-for="product in products" :key="product.id" 
              @click="formData.product_id = product.id"
              class="relative flex flex-col p-5 rounded-2xl border-2 transition-all cursor-pointer group hover:shadow-lg"
              :class="formData.product_id === product.id ? 'border-blue-500 bg-blue-50/30 dark:bg-blue-900/10' : 'border-gray-100 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-gray-200 dark:hover:border-slate-700'"
            >
              <div class="w-full aspect-square rounded-xl bg-gray-50 dark:bg-slate-800 border border-gray-100 dark:border-slate-700 mb-4 flex items-center justify-center overflow-hidden">
                <img v-if="product.image" :src="product.image" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                <Package v-else class="w-10 h-10 text-gray-200" />
              </div>
              
              <div class="flex-1">
                <div class="font-bold text-gray-900 dark:text-slate-100 leading-tight mb-1">{{ product.name }}</div>
                <div class="text-sm font-black text-blue-600 dark:text-blue-400">৳{{ product.sale_price }}</div>
              </div>

              <div v-if="formData.product_id === product.id" class="absolute top-3 right-3 bg-blue-500 text-white rounded-full p-1 shadow-md">
                <Check class="w-4 h-4" />
              </div>
            </div>
          </div>

          <!-- Pagination & Loading -->
          <div class="flex flex-col items-center justify-center pt-8 space-y-4">
            <div v-if="loading" class="flex items-center space-x-2 text-gray-500">
              <Loader2 class="w-5 h-5 animate-spin" />
              <span class="text-sm font-medium">Loading products...</span>
            </div>
            
            <button 
              v-if="hasMore && !loading" 
              @click="loadMore"
              class="px-8 py-3 bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400 rounded-xl text-sm font-bold hover:bg-gray-200 dark:hover:bg-slate-700 transition-all active:scale-95 shadow-sm"
            >
              Load More Products
            </button>

            <div v-if="!hasMore && products.length > 0" class="text-xs text-gray-400">
              No more products found
            </div>

            <div v-if="!loading && products.length === 0" class="flex flex-col items-center py-10">
              <PackageSearch class="w-16 h-16 text-gray-200 mb-4" />
              <p class="text-gray-500 font-medium">No products found for "{{ searchQuery }}"</p>
            </div>
          </div>
        </div>

        <!-- Page Title & Additional Settings -->
        <div class="pt-6 border-t border-gray-100 dark:border-slate-800 space-y-8">
          <div class="max-w-xl">
            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider ml-1 mb-3">Landing Page Title</label>
            <input 
              v-model="formData.title"
              type="text" 
              placeholder="e.g. Premium Wireless Headphones Landing Page" 
              class="w-full h-14 px-6 bg-gray-50 dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-2xl focus:border-blue-500 focus:bg-white dark:focus:bg-slate-700 transition-all outline-none font-medium dark:text-slate-200"
            />
          </div>

          <!-- Advanced Content Customization -->
          <div class="pt-8 space-y-8 border-t border-gray-100 dark:border-slate-800">
            <h3 class="text-lg font-black text-gray-900 dark:text-white flex items-center gap-2 uppercase tracking-tight">
              <span class="w-8 h-8 rounded-lg bg-blue-500 text-white flex items-center justify-center text-sm">2</span>
              Content Customization
            </h3>

            <!-- Hero Subtitle -->
            <div class="max-w-xl">
              <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider ml-1 mb-3">Hero Subtitle (Optional)</label>
              <input 
                v-model="formData.settings.hero_subtitle"
                type="text" 
                placeholder="e.g. The Future of Sound" 
                class="w-full h-12 px-5 bg-gray-50 dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-xl focus:border-blue-500 transition-all outline-none font-medium dark:text-slate-200"
              />
            </div>

            <!-- Features -->
            <div class="space-y-4">
              <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider ml-1">Key Features (Max 3)</label>
              <div v-for="(feature, index) in formData.settings.features" :key="index" class="p-6 bg-gray-50 dark:bg-slate-800/50 rounded-2xl border-2 border-dashed border-gray-200 dark:border-slate-700 space-y-4">
                <div class="flex justify-between items-center">
                  <span class="text-xs font-black text-blue-500 uppercase tracking-widest">Feature #{{ index + 1 }}</span>
                  <button @click="removeFeature(index)" class="text-red-500 hover:text-red-600 transition-colors">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <input v-model="feature.title" placeholder="Feature Title" class="w-full h-11 px-4 bg-white dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-xl focus:border-blue-500 transition-all outline-none font-medium text-sm" />
                  <input v-model="feature.desc" placeholder="Brief Description" class="w-full h-11 px-4 bg-white dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-xl focus:border-blue-500 transition-all outline-none font-medium text-sm" />
                </div>
              </div>
              <button 
                v-if="formData.settings.features.length < 3"
                @click="addFeature"
                class="w-full py-4 border-2 border-dashed border-gray-200 dark:border-slate-700 rounded-2xl text-gray-400 hover:border-blue-500 hover:text-blue-500 transition-all flex items-center justify-center gap-2 text-sm font-bold"
              >
                <Plus class="w-4 h-4" /> Add Feature
              </button>
            </div>

            <!-- Floating Points -->
            <div class="space-y-4">
              <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider ml-1">Floating Info Points (On Image)</label>
              <div v-for="(point, index) in formData.settings.floating_points" :key="index" class="p-6 bg-gray-50 dark:bg-slate-800/50 rounded-2xl border-2 border-dashed border-gray-200 dark:border-slate-700 space-y-4">
                <div class="flex justify-between items-center">
                  <span class="text-xs font-black text-amber-500 uppercase tracking-widest">Point #{{ index + 1 }}</span>
                  <button @click="removePoint(index)" class="text-red-500 hover:text-red-600 transition-colors">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                  <input v-model="point.label" placeholder="Label (e.g. Weight)" class="w-full h-11 px-4 bg-white dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-xl focus:border-blue-500 transition-all outline-none font-medium text-sm" />
                  <input v-model="point.value" placeholder="Value (e.g. 1.2kg)" class="w-full h-11 px-4 bg-white dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-xl focus:border-blue-500 transition-all outline-none font-medium text-sm" />
                  <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black text-gray-400">X%</span>
                    <input v-model.number="point.x" type="number" class="w-full h-11 px-4 bg-white dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-xl focus:border-blue-500 transition-all outline-none font-medium text-sm" />
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black text-gray-400">Y%</span>
                    <input v-model.number="point.y" type="number" class="w-full h-11 px-4 bg-white dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-xl focus:border-blue-500 transition-all outline-none font-medium text-sm" />
                  </div>
                </div>
              </div>
              <button 
                v-if="formData.settings.floating_points.length < 3"
                @click="addPoint"
                class="w-full py-4 border-2 border-dashed border-gray-200 dark:border-slate-700 rounded-2xl text-gray-400 hover:border-blue-500 hover:text-blue-500 transition-all flex items-center justify-center gap-2 text-sm font-bold"
              >
                <Plus class="w-4 h-4" /> Add Info Point
              </button>
            </div>

            <!-- Narrative & CTA -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <div class="space-y-4">
                <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider ml-1">Narrative Section</label>
                <input v-model="formData.settings.narrative_title" placeholder="Narrative Title (e.g. Immersive Experience)" class="w-full h-12 px-5 bg-gray-50 dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-xl focus:border-blue-500 transition-all outline-none font-medium text-sm dark:text-slate-200" />
                <textarea v-model="formData.settings.narrative_desc" rows="3" placeholder="Narrative Description" class="w-full px-5 py-4 bg-gray-50 dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-xl focus:border-blue-500 transition-all outline-none font-medium text-sm dark:text-slate-200"></textarea>
              </div>
              <div class="space-y-4">
                <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider ml-1">Call To Action</label>
                <input v-model="formData.settings.cta_text" placeholder="Button Text (e.g. Add To Collection)" class="w-full h-12 px-5 bg-gray-50 dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-xl focus:border-blue-500 transition-all outline-none font-medium text-sm dark:text-slate-200" />
              </div>
            </div>
          </div>
          
          <div class="flex items-center gap-3 p-6 bg-blue-50/50 dark:bg-blue-900/10 rounded-2xl border-2 border-blue-100 dark:border-blue-900/30 max-w-xl">
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" v-model="formData.is_home" class="sr-only peer">
              <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
              <span class="ml-3 text-sm font-bold text-gray-900 dark:text-slate-200 uppercase tracking-wider">Set as Store Home Page</span>
            </label>
            <div class="ml-auto">
              <div v-if="formData.is_home" class="flex items-center text-xs font-black text-blue-600 dark:text-blue-400 gap-1">
                <CheckCircle2 class="w-3 h-3" /> ACTIVE
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
          {{ store.isEdit ? 'Update Page' : "Let's Create Page" }}
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
  ChevronDown, 
  Check, 
  CheckCircle2, 
  Eye, 
  PartyPopper,
  Loader2,
  Search,
  Package,
  PackageSearch,
  Plus,
  Trash2
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

// Search & Pagination state
const products = ref([])
const loading = ref(false)
const searchQuery = ref('')
const currentPage = ref(1)
const hasMore = ref(true)
const perPage = 8

let searchTimeout = null

const selectedTemplate = ref('modern')
const formData = ref({
  product_id: '',
  title: '',
  landing_page_type: 'single',
  template_name: 'modern',
  status: 'active',
  is_home: false,
  settings: {
    hero_subtitle: '',
    narrative_title: '',
    narrative_desc: '',
    cta_text: '',
    features: [],
    floating_points: []
  }
})

const addFeature = () => {
  if (formData.value.settings.features.length < 3) {
    formData.value.settings.features.push({ title: '', desc: '', icon: '' })
  }
}

const removeFeature = (index) => {
  formData.value.settings.features.splice(index, 1)
}

const addPoint = () => {
  if (formData.value.settings.floating_points.length < 3) {
    formData.value.settings.floating_points.push({ x: 50, y: 50, label: '', value: '' })
  }
}

const removePoint = (index) => {
  formData.value.settings.floating_points.splice(index, 1)
}

// Fetch products
const fetchProducts = async (page = 1, append = false) => {
  loading.value = true
  try {
    const res = await getAll('/vendor/products', {
      search: searchQuery.value,
      per_page: perPage,
      page: page
    })
    
    // Check if it's paginated response
    const newProducts = res.data || res || []
    
    if (append) {
      products.value = [...products.value, ...newProducts]
    } else {
      products.value = newProducts
    }
    
    // If we're editing, we might need to fetch the selected product if it's not in the first 8
    // But since it's a list, the user can search for it if they want to change it.
    // The initial fetchPageData will set formData.product_id which is fine for the form.
    
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
        product_id: res.product_id,
        title: res.title,
        landing_page_type: res.landing_page_type,
        template_name: res.template_name,
        status: res.status,
        is_home: res.is_home === 1 || res.is_home === true,
        settings: {
          hero_subtitle: res.settings?.hero_subtitle || '',
          narrative_title: res.settings?.narrative_title || '',
          narrative_desc: res.settings?.narrative_desc || '',
          cta_text: res.settings?.cta_text || '',
          features: res.settings?.features || [],
          floating_points: res.settings?.floating_points || []
        }
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

const isValid = computed(() => {
  return selectedTemplate.value && formData.value.product_id && formData.value.title.trim()
})

watch(selectedTemplate, (val) => {
  formData.value.template_name = val
})

const submitLandingPage = async () => {
  if (!isValid.value) return
  
  try {
    const payload = {
      ...formData.value,
      template_name: selectedTemplate.value
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

<style scoped>
/* Smooth transitions for all elements */
* {
  transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}
</style>
