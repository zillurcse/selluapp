<template>
  <div class="flex flex-col h-full bg-gray-50/50 dark:bg-slate-950 min-h-screen transition-colors duration-300 p-10">
    <!-- Header -->
    <div class="flex items-center justify-between px-8 py-6 bg-white dark:bg-slate-900 border-b border-gray-100 dark:border-slate-800 transition-colors duration-300">
      <div class="flex items-center">
        <button @click="$router.back()" class="p-2 bg-black text-white rounded-full hover:bg-gray-800 transition-colors mr-6 shadow-sm">
          <ArrowLeft class="w-5 h-5" />
        </button>
        <div>
          <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Build Your Landing Page</h1>
          <p class="text-gray-500 dark:text-slate-400 mt-1">Select a template, fill in product details, and create your landing page easily.</p>
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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
          
          <!-- Product Selection -->
          <div class="space-y-3">
            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider ml-1">Select Product</label>
            <div class="relative">
              <select v-model="formData.product_id" class="w-full h-14 pl-5 pr-12 bg-gray-50 dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-2xl focus:border-blue-500 focus:bg-white dark:focus:bg-slate-700 transition-all appearance-none cursor-pointer outline-none font-medium dark:text-slate-200">
                <option value="">-- Select Product --</option>
                <option v-for="product in products" :key="product.id" :value="product.id">{{ product.name }}</option>
              </select>
              <ChevronDown class="absolute right-5 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none" />
            </div>
          </div>

          <!-- Page Title -->
          <div class="space-y-3">
            <label class="block text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider ml-1">Page Title</label>
            <input 
              v-model="formData.title"
              type="text" 
              placeholder="Enter Page Title" 
              class="w-full h-14 px-6 bg-gray-50 dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-2xl focus:border-blue-500 focus:bg-white dark:focus:bg-slate-700 transition-all outline-none font-medium dark:text-slate-200"
            />
          </div>

        </div>

        <!-- Submit Button -->
        <button 
          @click="submitLandingPage"
          class="w-full h-16 bg-black dark:bg-slate-800 text-white rounded-2xl font-black text-xl hover:bg-gray-800 dark:hover:bg-slate-700 active:scale-[0.98] transition-all shadow-xl flex items-center justify-center group"
          :disabled="!isValid"
          :class="{'opacity-50 cursor-not-allowed': !isValid}"
        >
          Let's Create Page
          <ArrowRight class="w-6 h-6 ml-3 group-hover:translate-x-1 transition-transform" />
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
  PartyPopper 
} from 'lucide-vue-next'

definePageMeta({
  layout: 'default',
  middleware: 'auth',
  permissions: 'landing_pages.create'
})

const { getAll, createItem } = useCrud()
const utilityStore = useUtilityStore()
utilityStore.pageBackLink = '/vendor/landing-page/all'

const selectedTemplate = ref('modern')
const formData = ref({
  product_id: '',
  title: '',
  landing_page_type: 'single',
  template_name: 'modern',
  status: 'active',
  settings: {}
})

const products = ref([])

// Fetch products
const fetchProducts = async () => {
  try {
    const res = await getAll('/vendor/products')
    products.value = res || []
  } catch (error) {
    console.error('Failed to fetch products:', error)
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
    await createItem('/vendor/landing-pages', payload)
  } catch (error) {
    console.error('Error creating landing page:', error)
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
