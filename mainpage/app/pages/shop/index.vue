<template>
  <div class="shop-page bg-white min-h-screen relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-blue-50/50 rounded-full blur-[120px] -z-10 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-indigo-50/30 rounded-full blur-[100px] -z-10 pointer-events-none"></div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20 relative z-10">
      <header class="mb-12 md:mb-20 text-center animate-fade-in">
        <span class="inline-block px-4 py-1.5 rounded-full bg-gray-100 text-gray-900 text-[10px] font-bold uppercase tracking-widest mb-6">Explore the Collection</span>
        <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight text-gray-900 mb-6 font-heading">
          Our Collection
        </h1>
        <p class="text-gray-500 text-lg md:text-xl max-w-2xl mx-auto leading-relaxed">
          Meticulously designed pieces that blend minimalist aesthetics with exceptional craftsmanship for any modern home.
        </p>
      </header>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16">
        <!-- Sidebar Filters -->
        <aside class="lg:col-span-3 space-y-10 lg:sticky lg:top-24 h-fit animate-fade-in" style="animation-delay: 0.1s">
          <div class="space-y-8 p-8 rounded-[2rem] bg-gray-50/50 border border-gray-100 backdrop-blur-sm shadow-sm">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-bold text-gray-900 font-heading">Filters</h3>
              <button 
                @click="clearFilters"
                class="text-xs font-bold text-indigo-600 hover:text-indigo-700 uppercase tracking-wider transition-colors"
              >
                Clear All
              </button>
            </div>

            <div class="space-y-6">
              <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Search</h4>
              <div class="relative">
                <input 
                  type="text" 
                  v-model="filters.search"
                  placeholder="Search products..." 
                  class="w-full pl-10 pr-4 py-3 bg-white border border-gray-100 rounded-xl text-sm font-bold focus:ring-2 focus:ring-gray-100 outline-none transition-all shadow-sm" 
                />
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </div>
              </div>
            </div>

            <div class="space-y-6">
              <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Categories</h4>
              <div class="space-y-4 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                <div v-for="cat in availableCategories" :key="cat.id" class="space-y-2">
                  <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative w-5 h-5 flex items-center justify-center">
                      <input 
                        type="checkbox" 
                        :value="cat.slug" 
                        :checked="filters.categories.includes(cat.slug)"
                        @change="toggleCategory(cat.slug)"
                        class="peer hidden" 
                      />
                      <div class="w-full h-full border-2 border-gray-200 rounded-md transition-all peer-checked:bg-indigo-600 peer-checked:border-indigo-600 group-hover:border-gray-300"></div>
                      <svg class="absolute w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                    </div>
                    <span class="text-sm font-bold text-gray-700 group-hover:text-gray-900 transition-colors">{{ cat.name }}</span>
                  </label>
                  
                  <!-- Subcategories -->
                  <div v-if="cat.children && cat.children.length > 0" class="pl-8 space-y-2">
                    <label v-for="child in cat.children" :key="child.id" class="flex items-center gap-3 cursor-pointer group">
                      <div class="relative w-4 h-4 flex items-center justify-center">
                        <input 
                          type="checkbox" 
                          :value="child.slug" 
                          :checked="filters.categories.includes(child.slug)"
                          @change="toggleCategory(child.slug)"
                          class="peer hidden" 
                        />
                        <div class="w-full h-full border-2 border-gray-200 rounded transition-all peer-checked:bg-indigo-500 peer-checked:border-indigo-500 group-hover:border-gray-300"></div>
                        <svg class="absolute w-2.5 h-2.5 text-white opacity-0 peer-checked:opacity-100 transition-opacity" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                      </div>
                      <span class="text-xs font-medium text-gray-500 group-hover:text-indigo-600 transition-colors">{{ child.name }}</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <div class="space-y-6">
              <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Brands</h4>
              <div class="space-y-4 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                <label v-for="brand in availableBrands" :key="brand.id" class="flex items-center gap-3 cursor-pointer group">
                  <div class="relative w-5 h-5 flex items-center justify-center">
                    <input 
                      type="checkbox" 
                      :value="brand.slug" 
                      :checked="filters.brands.includes(brand.slug)"
                      @change="toggleBrand(brand.slug)"
                      class="peer hidden" 
                    />
                    <div class="w-full h-full border-2 border-gray-200 rounded-md transition-all peer-checked:bg-gray-900 peer-checked:border-gray-900 group-hover:border-gray-300"></div>
                    <svg class="absolute w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                  </div>
                  <span class="text-sm font-semibold text-gray-500 group-hover:text-gray-900 transition-colors">{{ brand.name }}</span>
                </label>
              </div>
            </div>

            <div class="space-y-6">
              <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Units</h4>
              <div class="space-y-4 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                <label v-for="unit in availableUnits" :key="unit.id" class="flex items-center gap-3 cursor-pointer group">
                  <div class="relative w-5 h-5 flex items-center justify-center">
                    <input 
                      type="checkbox" 
                      :value="unit.slug" 
                      :checked="filters.units.includes(unit.slug)"
                      @change="toggleUnit(unit.slug)"
                      class="peer hidden" 
                    />
                    <div class="w-full h-full border-2 border-gray-200 rounded-md transition-all peer-checked:bg-gray-900 peer-checked:border-gray-900 group-hover:border-gray-300"></div>
                    <svg class="absolute w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                  </div>
                  <span class="text-sm font-semibold text-gray-500 group-hover:text-gray-900 transition-colors">{{ unit.name }}</span>
                </label>
              </div>
            </div>

            <div class="space-y-6">
              <h4 class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Price Range</h4>
              <div class="flex items-center gap-3">
                <div class="relative flex-1">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                  <input 
                    type="number" 
                    v-model.number="filters.min_price"
                    placeholder="Min" 
                    class="w-full pl-7 pr-4 py-2 bg-white border border-gray-100 rounded-xl text-sm font-bold focus:ring-2 focus:ring-gray-100 outline-none transition-all" 
                  />
                </div>
                <div class="w-2 h-[2px] bg-gray-200"></div>
                <div class="relative flex-1">
                  <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                  <input 
                    type="number" 
                    v-model.number="filters.max_price"
                    placeholder="Max" 
                    class="w-full pl-7 pr-4 py-2 bg-white border border-gray-100 rounded-xl text-sm font-bold focus:ring-2 focus:ring-gray-100 outline-none transition-all" 
                  />
                </div>
              </div>
            </div>

            <div class="pt-4">
              <button 
                @click="applyPriceFilter"
                class="w-full py-3 bg-gray-900 text-white rounded-full font-bold text-xs uppercase tracking-widest shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all"
              >
                Apply Filters
              </button>
            </div>
          </div>

          <!-- Featured Offer -->
          <div class="relative p-8 rounded-[2rem] bg-indigo-600 overflow-hidden group shadow-2xl shadow-indigo-100">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-2xl transition-transform group-hover:scale-150 duration-700"></div>
            <div class="relative z-10 space-y-4">
              <span class="text-[10px] font-bold text-white/60 uppercase tracking-widest">Limited Offer</span>
              <h4 class="text-xl font-bold text-white leading-tight font-heading">Summer Collection Sale is here!</h4>
              <p class="text-white/70 text-xs leading-relaxed">Get up to 40% off on all lighting products this week.</p>
              <button class="text-xs font-bold text-white border-b-2 border-white pb-1 group-hover:gap-2 flex items-center gap-1 transition-all">Shop Sale →</button>
            </div>
          </div>
        </aside>

        <!-- Product Grid -->
        <main class="lg:col-span-9 animate-fade-in" style="animation-delay: 0.2s">
          <div class="flex flex-col sm:flex-row justify-between items-center mb-10 gap-6 border-b border-gray-50 pb-8">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest">
              Showing <span class="text-gray-900">{{ pagination?.total || 0 }}</span> items found
            </div>
            <div class="flex items-center gap-4">
              <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Sort By:</span>
              <div class="relative">
                <select 
                  v-model="filters.sort"
                  class="appearance-none bg-gray-50 border border-gray-100 rounded-full px-8 py-2.5 text-xs font-bold text-gray-900 outline-none focus:ring-2 focus:ring-gray-100 transition-all cursor-pointer"
                >
                  <option value="featured">Featured Products</option>
                  <option value="price_asc">Price: Low to High</option>
                  <option value="price_desc">Price: High to Low</option>
                  <option value="newest">Newest First</option>
                </select>
                <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><path d="m6 9 6 6 6-6"/></svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Loading State -->
          <div v-if="pending" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8 md:gap-10">
            <div v-for="i in 6" :key="i" class="space-y-4 animate-pulse">
              <div class="aspect-[4/5] bg-gray-100 rounded-[2rem]"></div>
              <div class="h-4 bg-gray-100 rounded w-1/3"></div>
              <div class="h-6 bg-gray-100 rounded w-3/4"></div>
              <div class="h-6 bg-gray-100 rounded w-1/4"></div>
            </div>
          </div>

          <!-- Products Grid -->
          <div v-else-if="products.length > 0" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8 md:gap-10">
            <ProductCard 
              v-for="(product, idx) in products" 
              :key="product.id" 
              :product="product"
              :style="{ animationDelay: `${0.1 + (idx * 0.05)}s` }"
              class="animate-fade-in"
              @add-to-cart="addToCart"
            />
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-20 px-4 rounded-[2rem] bg-gray-50 border border-gray-100">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2 font-heading">No products found</h3>
            <p class="text-gray-500">Try adjusting your filters to find what you're looking for.</p>
            <button 
              @click="clearFilters"
              class="mt-8 px-8 py-3 bg-gray-900 text-white rounded-full font-bold text-xs uppercase tracking-widest hover:bg-indigo-600 transition-all"
            >
              Clear All Filters
            </button>
          </div>

          <!-- Pagination -->
          <div v-if="pagination && pagination.last_page > 1" class="mt-20 flex justify-center items-center gap-4">
            <button 
              @click="changePage(filters.page - 1)"
              :disabled="filters.page === 1"
              class="w-12 h-12 rounded-full border border-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-900 transition-all disabled:opacity-30 disabled:pointer-events-none"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            </button>
            <div class="flex gap-2">
              <button 
                v-for="page in pagination.last_page" 
                :key="page"
                @click="changePage(page)"
                class="w-12 h-12 rounded-full font-bold text-sm transition-all" 
                :class="filters.page === page ? 'bg-gray-900 text-white shadow-lg' : 'text-gray-400 hover:text-gray-900 hover:bg-gray-50'"
              >
                {{ page }}
              </button>
            </div>
            <button 
              @click="changePage(filters.page + 1)"
              :disabled="filters.page === pagination.last_page"
              class="w-12 h-12 rounded-full border border-gray-100 flex items-center justify-center text-gray-400 hover:text-gray-900 transition-all disabled:opacity-30 disabled:pointer-events-none"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </button>
          </div>
        </main>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import lampImg from '~/assets/img/lamp.png'

const route = useRoute()
const router = useRouter()

const config = useRuntimeConfig()
const apiBase = config.public.apiBase

// Filters state
const filters = reactive({
  categories: [],
  brands: [],
  units: [],
  specs: {},
  min_price: null,
  max_price: null,
  sort: 'newest',
  page: 1,
  search: ''
})

// Initialize filters from query params
const initFiltersFromQuery = () => {
  if (route.query.category) {
    filters.categories = [route.query.category]
  } else if (route.query.categories) {
    filters.categories = route.query.categories.split(',')
  }
  
  if (route.query.search) filters.search = route.query.search
  if (route.query.sort) filters.sort = route.query.sort
  if (route.query.min_price) filters.min_price = Number(route.query.min_price)
  if (route.query.max_price) filters.max_price = Number(route.query.max_price)
  if (route.query.page) filters.page = Number(route.query.page)
}

initFiltersFromQuery()

const domain = useRequestURL().hostname;

// Fetch Metadata (Categories, Brands, Units)
const { data: storefrontData } = await useAsyncData('storefront-meta', () => 
  $fetch(`${apiBase}/storefront`, { headers: { 'X-Tenant-Domain': domain } })
)

const availableCategories = computed(() => storefrontData.value?.categories || [])
const availableBrands = computed(() => storefrontData.value?.brands || [])
const availableUnits = computed(() => storefrontData.value?.units || [])

// Fetch Products
const { data: productsData, pending, refresh } = await useAsyncData('products', () => 
  $fetch(`${apiBase}/storefront/products`, {
    headers: { 'X-Tenant-Domain': domain },
    params: {
      ...filters,
      categories: filters.categories.join(','),
      brands: filters.brands.join(','),
      units: filters.units.join(','),
      specs: filters.specs
    }
  }), {
    watch: [filters]
  }
)

const products = computed(() => {
  if (!productsData.value) return []
  return productsData.value.data.map(p => ({
    id: p.id,
    name: p.name,
    slug: p.slug,
    price: p.sale_price,
    image: p.image_url || lampImg,
    category: p.categories?.[0]?.name || 'Uncategorized',
    brand: p.brand?.name || null,
    vendor: p.vendor?.vendor_profile ? {
      name: p.vendor.vendor_profile.store_name,
      slug: p.vendor.vendor_profile.store_slug
    } : null
  }))
})

const pagination = computed(() => {
  if (!productsData.value) return null
  return {
    current_page: productsData.value.current_page,
    last_page: productsData.value.last_page,
    total: productsData.value.total,
    from: productsData.value.from,
    to: productsData.value.to
  }
})

const { addToCart } = useCart()

const clearFilters = () => {
  filters.categories = []
  filters.brands = []
  filters.units = []
  filters.specs = {}
  filters.min_price = null
  filters.max_price = null
  filters.sort = 'newest'
  filters.page = 1
  filters.search = ''
}

const toggleFilter = (type, slug) => {
  const index = filters[type].indexOf(slug)
  if (index > -1) {
    filters[type].splice(index, 1)
  } else {
    filters[type].push(slug)
  }
  filters.page = 1
}

const toggleCategory = (slug) => toggleFilter('categories', slug)
const toggleBrand = (slug) => toggleFilter('brands', slug)
const toggleUnit = (slug) => toggleFilter('units', slug)

const toggleSpec = (key, value) => {
  if (!filters.specs[key]) {
    filters.specs[key] = []
  }
  const index = filters.specs[key].indexOf(value)
  if (index > -1) {
    filters.specs[key].splice(index, 1)
    if (filters.specs[key].length === 0) {
      delete filters.specs[key]
    }
  } else {
    filters.specs[key].push(value)
  }
  filters.page = 1
}

const applyPriceFilter = () => {
  filters.page = 1
}

const changePage = (page) => {
  filters.page = page
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

watch(() => filters.sort, () => {
  filters.page = 1
})

watch(() => filters.search, () => {
  filters.page = 1
})

// Watch for external route changes (like from Navbar)
watch(() => route.query.category, (newVal) => {
  if (newVal) {
    filters.categories = [newVal]
    filters.page = 1
  }
})

// Update query string when filters change (optional but good for UX)
watch(filters, (newFilters) => {
  const query = {}
  if (newFilters.categories.length) query.categories = newFilters.categories.join(',')
  if (newFilters.search) query.search = newFilters.search
  if (newFilters.sort !== 'newest') query.sort = newFilters.sort
  if (newFilters.min_price) query.min_price = newFilters.min_price
  if (newFilters.max_price) query.max_price = newFilters.max_price
  if (newFilters.page > 1) query.page = newFilters.page
  
  router.push({ query, replace: true })
}, { deep: true })
</script>

<style scoped>
.font-heading {
  font-family: var(--font-heading);
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  opacity: 0;
  animation: fadeIn 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
}

/* Custom Scrollbar for Sidebar */
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #f1f5f9;
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #e2e8f0;
}

::-webkit-scrollbar {
  width: 5px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: #f1f5f9;
  border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
  background: #e2e8f0;
}
</style>

