<template>
  <div class="vendor-page bg-white min-h-screen relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-blue-50/50 rounded-full blur-[120px] -z-10 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-indigo-50/30 rounded-full blur-[100px] -z-10 pointer-events-none"></div>

    <template v-if="vendorData">
      <!-- ── VENDOR HERO SECTION ── -->
      <section class="relative h-[40vh] md:h-[50vh] min-h-[300px] overflow-hidden">
        <!-- Banner Image -->
        <div class="absolute inset-0">
          <img 
            :src="vendorData.vendor.banner_url || 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?q=80&w=2070&auto=format&fit=crop'" 
            class="w-full h-full object-cover"
            alt="Vendor Banner"
          />
          <div class="absolute inset-0 bg-gradient-to-t from-gray-900/80 via-gray-900/20 to-transparent"></div>
        </div>

        <div class="absolute bottom-0 left-0 w-full p-8 md:p-12 z-10">
          <div class="container mx-auto px-4 md:px-0">
            <div class="flex flex-col md:flex-row items-end gap-6 md:gap-10">
              <!-- Logo -->
              <div class="w-24 h-24 md:w-32 md:h-32 rounded-3xl bg-white p-2 shadow-2xl overflow-hidden border-4 border-white/20 backdrop-blur-md animate-fade-in">
                <img 
                  :src="vendorData.vendor.logo_url || 'https://ui-avatars.com/api/?name=' + vendorData.vendor.store_name" 
                  class="w-full h-full object-contain rounded-2xl"
                  alt="Vendor Logo"
                />
              </div>
              
              <!-- Info -->
              <div class="flex-1 pb-2 md:pb-4 animate-fade-in" style="animation-delay: 0.1s">
                <div class="flex flex-wrap items-center gap-4 mb-4">
                   <span class="px-3 py-1 bg-white/20 backdrop-blur-md border border-white/30 rounded-full text-[10px] font-bold uppercase tracking-widest text-white">Verified Vendor</span>
                   <div class="flex items-center gap-1 text-amber-400">
                     <svg v-for="i in 5" :key="i" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" class="text-amber-400"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                     <span class="text-xs font-bold text-white/80 ml-1">4.9 (120+ Reviews)</span>
                   </div>
                </div>
                <h1 class="text-3xl md:text-5xl lg:text-6xl font-extrabold text-white tracking-tight mb-4 font-heading drop-shadow-lg">
                  {{ vendorData.vendor.store_name }}
                </h1>
                <p class="text-white/80 text-sm md:text-base max-w-2xl font-medium leading-relaxed">
                  {{ vendorData.vendor.description || 'Welcome to our store. We provide high-quality products with exceptional service.' }}
                </p>
              </div>

              <!-- Action button -->
              <div class="pb-2 md:pb-4 flex gap-4 animate-fade-in" style="animation-delay: 0.2s">
                 <button class="px-8 py-3 bg-white text-gray-900 rounded-full font-bold text-xs uppercase tracking-widest hover:bg-gray-100 transition-all flex items-center gap-2">
                   Follow Store
                 </button>
                 <button class="w-12 h-12 flex items-center justify-center bg-white/10 hover:bg-white/20 backdrop-blur-md rounded-full text-white transition-all">
                   <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/></svg>
                 </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ── CONTENT SECTION ── -->
      <div class="container mx-auto px-4 md:px-0 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16">
          <!-- Sidebar: Filter by Vendor Categories -->
          <aside class="lg:col-span-3 space-y-10 animate-fade-in">
             <div class="p-8 rounded-[2.5rem] bg-gray-50/50 border border-gray-100">
               <h3 class="text-lg font-bold text-gray-900 mb-8 font-heading">Shop by Category</h3>
               <div class="space-y-4">
                 <button 
                  v-for="cat in vendorData.categories" 
                  :key="cat.id"
                  class="w-full flex items-center justify-between p-4 bg-white rounded-2xl border border-gray-100 hover:border-indigo-600 hover:shadow-xl hover:shadow-indigo-50/50 transition-all text-left group"
                 >
                   <span class="text-sm font-bold text-gray-600 group-hover:text-indigo-600 transition-colors">{{ cat.name }}</span>
                   <span class="w-6 h-6 rounded-lg bg-gray-50 flex items-center justify-center text-[10px] text-gray-400 group-hover:bg-indigo-600 group-hover:text-white transition-all">→</span>
                 </button>

                 <!-- If no categories -->
                 <div v-if="vendorData.categories.length === 0" class="text-center py-6">
                    <p class="text-xs text-gray-400 font-medium italic">No categories available</p>
                 </div>
               </div>
             </div>

             <!-- Vendor Stats/Info -->
             <div class="p-8 rounded-[2.5rem] bg-gray-900 overflow-hidden relative group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-600/30 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                <h3 class="text-sm font-bold text-white/40 uppercase tracking-widest mb-8 relative z-10">Store Information</h3>
                <div class="space-y-6 relative z-10">
                  <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-xl">📍</div>
                    <div>
                      <div class="text-[10px] font-bold text-white/30 uppercase tracking-widest mb-1">Address</div>
                      <div class="text-sm text-white/80 font-medium">{{ vendorData.vendor.address || 'Global Store' }}</div>
                    </div>
                  </div>
                  <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-xl">📞</div>
                    <div>
                      <div class="text-[10px] font-bold text-white/30 uppercase tracking-widest mb-1">Contact</div>
                      <div class="text-sm text-white/80 font-medium">{{ vendorData.vendor.phone || vendorData.vendor.email || 'N/A' }}</div>
                    </div>
                  </div>
                  <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-xl">📅</div>
                    <div>
                      <div class="text-[10px] font-bold text-white/30 uppercase tracking-widest mb-1">Member Since</div>
                      <div class="text-sm text-white/80 font-medium">February 2026</div>
                    </div>
                  </div>
                </div>

                <div class="mt-10 pt-10 border-t border-white/5 flex justify-center gap-6">
                  <a v-if="vendorData.vendor.facebook" :href="vendorData.vendor.facebook" target="_blank" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-white/60 hover:bg-white hover:text-gray-900 transition-all">FB</a>
                  <a v-if="vendorData.vendor.instagram" :href="vendorData.vendor.instagram" target="_blank" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-white/60 hover:bg-white hover:text-gray-900 transition-all">IG</a>
                  <a v-if="vendorData.vendor.twitter" :href="vendorData.vendor.twitter" target="_blank" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-white/60 hover:bg-white hover:text-gray-900 transition-all">TW</a>
                </div>
             </div>
          </aside>

          <!-- Main Feed: Product Grid -->
          <main class="lg:col-span-9 space-y-12 animate-fade-in" style="animation-delay: 0.1s">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-6 border-b border-gray-100 pb-8">
               <h2 class="text-2xl font-extrabold text-gray-900 font-heading">
                 Store Collection <span class="text-gray-400 font-medium ml-2">({{ vendorData.products?.total || 0 }})</span>
               </h2>
               <div class="flex gap-4">
                 <button class="px-6 py-2 bg-gray-100 rounded-full text-xs font-bold text-gray-900 hover:bg-gray-200 transition-all">Newest</button>
                 <button class="px-6 py-2 bg-white rounded-full text-xs font-medium text-gray-500 hover:text-gray-900 transition-all">Popular</button>
               </div>
            </div>

            <!-- Products Grid -->
            <div v-if="products.length > 0" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8 md:gap-10">
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
            <div v-else class="text-center py-24 bg-gray-50 rounded-[3rem] border border-gray-100">
               <div class="text-6xl mb-6">📦</div>
               <h3 class="text-xl font-bold text-gray-900 mb-2 font-heading">No products found</h3>
               <p class="text-gray-500 max-w-xs mx-auto">This vendor hasn't posted any products yet. Check back soon!</p>
            </div>

            <!-- Pagination -->
            <div v-if="pagination && pagination.last_page > 1" class="pt-10 flex justify-center items-center gap-4">
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
    </template>

    <!-- Loading State -->
    <div v-else class="min-h-screen flex items-center justify-center">
       <div class="flex flex-col items-center gap-6">
         <div class="w-16 h-16 border-4 border-indigo-600/20 border-t-indigo-600 rounded-full animate-spin"></div>
         <p class="text-sm font-bold text-gray-400 uppercase tracking-widest animate-pulse">Loading Store...</p>
       </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useStorefrontStore } from '~/stores/useStorefrontStore'
import lampImg from '~/assets/img/lamp.png'

const route = useRoute()
const storefrontStore = useStorefrontStore()
const { addToCart } = useCart()

const slug = computed(() => route.params.slug)
const vendorData = ref(null)

const filters = reactive({
  page: 1
})

const fetchVendorData = async () => {
  try {
    const res = await storefrontStore.fetchVendor(slug.value)
    vendorData.value = res
  } catch (err) {
    console.error('Failed to fetch vendor data:', err)
  }
}

onMounted(() => {
  fetchVendorData()
})

const products = computed(() => {
  if (!vendorData.value || !vendorData.value.products) return []
  return vendorData.value.products.data.map(p => ({
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
  if (!vendorData.value || !vendorData.value.products) return null
  return {
    current_page: vendorData.value.products.current_page,
    last_page: vendorData.value.products.last_page,
    total: vendorData.value.products.total
  }
})

const changePage = async (page) => {
  filters.page = page
  // We might want to call fetchVendor with page param later if we add it to the backend
  window.scrollTo({ top: 300, behavior: 'smooth' })
}

useHead(() => ({
  title: vendorData.value ? `${vendorData.value.vendor.store_name} | EMU Store` : 'Vendor Store | EMU',
  meta: [{ name: 'description', content: vendorData.value?.vendor.description || '' }],
}))
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
</style>
