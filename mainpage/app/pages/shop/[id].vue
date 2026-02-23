<template>
  <div class="product-detail-page bg-white min-h-screen overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-indigo-50/50 rounded-full blur-[120px] -z-10 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-rose-50/30 rounded-full blur-[100px] -z-10 pointer-events-none"></div>

    <div v-if="product" class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20 relative z-10">
      
      <!-- Modern Breadcrumbs -->
      <nav class="flex items-center gap-3 text-xs md:text-sm font-medium text-gray-400 mb-8 md:mb-12 animate-fade-in" style="animation-delay: 0.1s">
        <NuxtLink to="/" class="hover:text-gray-900 transition-colors">Home</NuxtLink>
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300"><path d="m9 18 6-6-6-6"/></svg>
        <NuxtLink to="/shop" class="hover:text-gray-900 transition-colors">Shop</NuxtLink>
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300"><path d="m9 18 6-6-6-6"/></svg>
        <span class="text-gray-900 font-bold truncate max-w-[150px] md:max-w-none">{{ product.name }}</span>
      </nav>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20 items-start">
        
        <!-- Image Gallery (Left) -->
        <div class="lg:col-span-7 space-y-6 lg:sticky lg:top-24 animate-fade-in" style="animation-delay: 0.2s">
          <div class="relative group rounded-[2rem] overflow-hidden bg-gray-50 border border-gray-100 shadow-sm transition-all duration-500 hover:shadow-2xl hover:-translate-y-1">
            <div class="absolute top-4 right-4 z-20">
              <button class="w-10 h-10 rounded-full bg-white/80 backdrop-blur-md flex items-center justify-center text-gray-900 shadow-sm hover:bg-white hover:scale-110 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              </button>
            </div>
            <img :src="product.gallery[activeImg]" :alt="product.name" class="w-full h-auto aspect-[4/5] object-cover transition-transform duration-700 group-hover:scale-105" />
          </div>
          
          <div v-if="product.gallery && product.gallery.length > 1" class="grid grid-cols-4 gap-4">
            <div 
              v-for="(img, idx) in product.gallery" 
              :key="idx" 
              class="relative rounded-2xl overflow-hidden cursor-pointer group border-2 transition-all duration-300"
              :class="activeImg === idx ? 'border-gray-900' : 'border-transparent opacity-60 hover:opacity-100'"
              @click="activeImg = idx"
            >
              <img :src="img" alt="Thumbnail" class="w-full h-full aspect-square object-cover" />
            </div>
          </div>
        </div>

        <!-- Product Details (Right) -->
        <div class="lg:col-span-5 space-y-8 animate-fade-in" style="animation-delay: 0.3s">
          <div>
            <div class="flex items-center gap-3 mb-4">
              <span class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 text-[10px] md:text-xs font-bold uppercase tracking-wider">Premium Selection</span>
              <span class="px-3 py-1 rounded-full bg-amber-50 text-amber-600 text-[10px] md:text-xs font-bold uppercase tracking-wider">In Stock</span>
            </div>
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold tracking-tight text-gray-900 leading-[1.1] mb-4">
              {{ product.name }}
            </h1>
            
            <div class="flex items-center gap-6 mb-6">
              <div class="text-2xl md:text-3xl font-extrabold text-gray-900">${{ product.price }}</div>
              <div class="flex items-center gap-1.5">
                <div class="flex text-amber-400">
                  <svg v-for="i in 5" :key="i" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                </div>
                <span class="text-sm font-semibold text-gray-400 underline underline-offset-4 cursor-pointer hover:text-gray-900">4.9 (12 reviews)</span>
              </div>
            </div>

            <p class="text-gray-500 text-base md:text-lg leading-relaxed">
              {{ product.short_description || 'Experience the perfect blend of form and function. This piece is meticulously crafted from premium materials to ensure durability while maintaining a sleek, minimalist aesthetic. Perfect for any modern space.' }}
            </p>

            <!-- Vendor Badge/Link -->
            <div v-if="product.vendor_profile" class="mt-8 pt-8 border-t border-gray-100 flex items-center justify-between">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gray-50 p-1.5 border border-gray-100">
                  <img :src="product.vendor_profile.logo_url || 'https://ui-avatars.com/api/?name=' + product.vendor_profile.store_name" class="w-full h-full object-contain rounded-lg" />
                </div>
                <div>
                  <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Sold by</div>
                  <div class="font-bold text-gray-900">{{ product.vendor_profile.store_name }}</div>
                </div>
              </div>
              <NuxtLink :to="`/shop/vendor/${product.vendor_profile.store_slug}`" class="px-5 py-2 bg-gray-900 text-white rounded-full text-[10px] font-bold uppercase tracking-widest hover:bg-indigo-600 transition-all">
                Visit Store
              </NuxtLink>
            </div>
          </div>

          <!-- Selectors -->
          <div class="space-y-6 pt-6 border-t border-gray-100">
            <!-- Colors -->
            <div>
              <h3 class="text-sm font-bold uppercase tracking-widest text-gray-900 mb-4">Color Collection</h3>
              <div class="flex flex-wrap gap-4">
                <button 
                  v-for="color in colors" 
                  :key="color.hex" 
                  class="group relative w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300"
                  :style="{ background: color.hex }"
                  :title="color.name"
                  @click="activeColor = color.hex"
                >
                  <div 
                    class="absolute -inset-1.5 rounded-full border-2 transition-all duration-300"
                    :class="activeColor === color.hex ? 'border-gray-900 scale-100' : 'border-transparent scale-50 opacity-0 group-hover:scale-100 group-hover:opacity-100 group-hover:border-gray-200'"
                  ></div>
                  <svg v-if="activeColor === color.hex" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" :class="activeColor === '#ffffff' ? 'text-black' : 'text-white'"><path d="M20 6 9 17l-5-5"/></svg>
                </button>
              </div>
            </div>

            <!-- Quantity & Actions -->
            <div class="flex flex-col sm:flex-row gap-4">
              <div class="inline-flex items-center justify-between p-1.5 rounded-full border-2 border-gray-100 bg-gray-50/50 backdrop-blur-sm min-w-[140px]">
                <button 
                  @click="quantity = Math.max(1, quantity - 1)"
                  class="w-10 h-10 rounded-full flex items-center justify-center text-xl font-medium hover:bg-white hover:shadow-sm text-gray-400 hover:text-gray-900 transition-all"
                >−</button>
                <span class="font-bold text-gray-900 text-lg w-8 text-center">{{ quantity }}</span>
                <button 
                  @click="quantity++"
                  class="w-10 h-10 rounded-full flex items-center justify-center text-xl font-medium hover:bg-white hover:shadow-sm text-gray-400 hover:text-gray-900 transition-all"
                >+</button>
              </div>

              <button 
                class="flex-1 bg-gray-900 text-white rounded-full py-4 px-8 font-bold text-sm tracking-wide shadow-xl shadow-gray-200 hover:shadow-gray-300 hover:-translate-y-1 active:translate-y-0 transition-all duration-300 flex items-center justify-center gap-3"
                @click="handleAddToCart"
              >
                <span>Add to Cart — ${{ (parseFloat(product.price) * quantity).toFixed(2) }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
              </button>

              <button class="w-[60px] h-[60px] rounded-full border-2 border-gray-100 flex items-center justify-center text-gray-400 hover:text-rose-500 hover:border-rose-100 hover:bg-rose-50 transition-all duration-300 group">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform group-hover:scale-110"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
              </button>
            </div>
          </div>

          <!-- Trust Badges -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-8 border-t border-gray-100">
            <div class="flex items-center gap-4 group">
              <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">🚚</div>
              <div>
                <p class="font-bold text-gray-900 text-sm">Free Global Shipping</p>
                <p class="text-xs text-gray-400">On all orders above $200</p>
              </div>
            </div>
            <div class="flex items-center gap-4 group">
              <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">🔄</div>
              <div>
                <p class="font-bold text-gray-900 text-sm">30-Day Happiness</p>
                <p class="text-xs text-gray-400">Easy returns and exchanges</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Detail Tabs -->
      <section class="mt-20 md:mt-32 pt-16 border-t border-gray-100 animate-fade-in" style="animation-delay: 0.4s">
        <div class="max-w-4xl mx-auto">
          <div class="flex items-center justify-center gap-8 md:gap-16 mb-12 border-b border-gray-50 pb-px">
            <button 
              v-for="tab in ['Description', 'Specifications', 'Reviews']" 
              :key="tab"
              @click="activeTab = tab"
              class="relative py-4 text-sm md:text-base font-bold uppercase tracking-widest transition-all duration-300"
              :class="activeTab === tab ? 'text-gray-900' : 'text-gray-300 hover:text-gray-900'"
            >
              {{ tab }}
              <div 
                class="absolute bottom-0 left-0 w-full h-0.5 bg-gray-900 transition-all duration-500"
                :class="activeTab === tab ? 'opacity-100' : 'opacity-0 translate-y-2'"
              ></div>
            </button>
          </div>

          <div class="prose prose-lg max-w-none text-gray-500 leading-relaxed overflow-hidden">
            <transition name="tab-fade" mode="out-in">
              <div :key="activeTab">
                <div v-if="activeTab === 'Description'" class="space-y-6">
                  <p>This product is a testament to minimalist design. Every curve and line has been thoughtfully considered to create a piece that is both beautiful and functional. Made from sustainable materials, it's a guilt-free addition to your home.</p>
                  <p>Our artisans spend over 20 hours hand-crafting each individual unit, ensuring that no two pieces are exactly alike. This uniqueness is what makes our collection truly special.</p>
                </div>
                
                <div v-if="activeTab === 'Specifications'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div v-for="(val, label) in specifications" :key="label" class="flex justify-between py-3 border-b border-gray-50">
                    <span class="font-bold text-gray-900">{{ label }}</span>
                    <span>{{ val }}</span>
                  </div>
                </div>

                <div v-if="activeTab === 'Reviews'" class="text-center py-10">
                  <div class="text-5xl mb-4">⭐</div>
                  <h3 class="text-xl font-bold text-gray-900 mb-2">98% Satisfied Customers</h3>
                  <p>Join the thousands of happy customers who have transformed their space.</p>
                  <button class="mt-6 px-8 py-3 rounded-full border-2 border-gray-900 text-gray-900 font-bold text-sm hover:bg-gray-900 hover:text-white transition-all">Write a Review</button>
                </div>
              </div>
            </transition>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
import lampImg from '~/assets/img/lamp.png'
import vaseImg from '~/assets/img/vase.png'

const route = useRoute()
const { addToCart } = useCart()

const config = useRuntimeConfig()
const apiBase = config.public.apiBase

const quantity = ref(1)
const activeImg = ref(0)
const activeTab = ref('Description')
const activeColor = ref('#000000')

const domain = useRequestURL().hostname;

const { data: rawProduct } = await useFetch(`${apiBase}/storefront/products/${route.params.id}`, {
  headers: { 'X-Tenant-Domain': domain }
})

const product = computed(() => {
  if (!rawProduct.value) return null
  const p = rawProduct.value
  return {
    ...p,
    name: p.name,
    price: p.sale_price,
    image: p.image_url || lampImg,
    category: p.categories?.[0]?.name || 'Uncategorized',
    description: p.description,
    short_description: p.short_description,
    vendor_profile: p.vendor?.vendor_profile ? {
      store_name: p.vendor.vendor_profile.store_name,
      store_slug: p.vendor.vendor_profile.store_slug,
      logo_url: p.vendor.vendor_profile.logo_url
    } : null,
    gallery: p.gallery_urls && p.gallery_urls.length > 0 ? p.gallery_urls : [p.image_url || lampImg]
  }
})

const specifications = computed(() => {
  if (!rawProduct.value || !rawProduct.value.specifications) return defaultSpecs
  const specs = {}
  try {
    const rawSpecs = rawProduct.value.specifications
    if (Array.isArray(rawSpecs)) {
      rawSpecs.forEach(section => {
        if (section.items) {
          section.items.forEach(item => {
            specs[item.label] = item.value
          })
        }
      })
    }
  } catch (e) {
    return defaultSpecs
  }
  return Object.keys(specs).length > 0 ? specs : defaultSpecs
})

const defaultSpecs = {
  'Material': 'Premium Concrete & Brushed Steel',
  'Dimensions': '30cm x 15cm x 15cm',
  'Weight': '1.2kg',
  'Power': '12W LED (Built-in)',
  'Guarantee': '2 Year Limited Warranty',
  'Origin': 'Ethically Sourced'
}

const colors = [
  { name: 'Pitch Black', hex: '#000000' },
  { name: 'Classic White', hex: '#ffffff' },
  { name: 'Deep Slate', hex: '#4b5563' },
  { name: 'Soft Sand', hex: '#f3f4f6' }
]

const handleAddToCart = () => {
  if (!product.value) return
  addToCart(product.value, quantity.value)
}
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  opacity: 0;
  animation: fadeIn 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
}

/* Tab Transitions */
.tab-fade-enter-active,
.tab-fade-leave-active {
  transition: all 0.4s ease;
}
.tab-fade-enter-from {
  opacity: 0;
  transform: translateX(10px);
}
.tab-fade-leave-to {
  opacity: 0;
  transform: translateX(-10px);
}

.product-detail-page {
  font-family: var(--font-sans);
}

h1, h2, h3 {
  font-family: var(--font-heading);
}

/* Scrollbar tweaks */
::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
  background: #cbd5e1;
}
</style>

