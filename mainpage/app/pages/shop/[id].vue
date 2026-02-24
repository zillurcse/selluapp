<template>
  <div class="product-detail-page bg-white min-h-screen overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-indigo-50/50 rounded-full blur-[120px] -z-10 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-rose-50/30 rounded-full blur-[100px] -z-10 pointer-events-none"></div>

    <div v-if="product" class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20 relative z-10">
      
      <!-- Premium Breadcrumbs -->
      <nav class="flex items-center gap-2 text-[11px] uppercase tracking-[0.15em] font-bold text-gray-400 mb-10 animate-fade-in" style="animation-delay: 0.1s">
        <NuxtLink to="/" class="hover:text-primary-600 transition-colors">Home</NuxtLink>
        <span class="w-1 h-1 rounded-full bg-gray-300"></span>
        <NuxtLink to="/shop" class="hover:text-primary-600 transition-colors">Shop</NuxtLink>
        <span class="w-1 h-1 rounded-full bg-gray-300"></span>
        <span class="text-gray-900 truncate max-w-[150px] md:max-w-none">{{ product.name }}</span>
      </nav>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-start">
        
        <!-- Premium Gallery System -->
        <div class="space-y-8 lg:sticky lg:top-24 animate-fade-in" style="animation-delay: 0.2s">
          <div class="relative group rounded-[3rem] overflow-hidden bg-[#F9F9F9] border border-gray-100/50 shadow-[0_32px_64px_-16px_rgba(0,0,0,0.1)] transition-all duration-1000">
            <div class="absolute top-8 right-8 z-30 flex flex-col gap-4">
              <button class="w-14 h-14 rounded-full bg-white/70 backdrop-blur-2xl flex items-center justify-center text-gray-900 shadow-2xl hover:bg-black hover:text-white transition-all duration-700 hover:scale-110 active:scale-95 group/btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform duration-500 group-hover/btn:rotate-90"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
              </button>
            </div>
            
            <div class="absolute inset-0 bg-gradient-to-t from-black/5 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-1000 pointer-events-none"></div>
            
            <transition name="image-zoom" mode="out-in">
                <img :key="currentImage" :src="currentImage" :alt="product.name" class="w-full h-auto aspect-[3/4] object-cover transition-transform duration-1000 group-hover:scale-105" />
            </transition>
          </div>
          
          <div v-if="product.gallery && product.gallery.length > 1" class="flex gap-5 overflow-x-auto pb-6 hide-scrollbar snap-x px-2">
            <div 
              v-for="(img, idx) in product.gallery" 
              :key="idx" 
              class="relative min-w-[110px] h-[140px] rounded-[2rem] overflow-hidden cursor-pointer group transition-all duration-700 snap-center shadow-lg"
              @click="handleThumbnailClick(idx)"
            >
              <img :src="img" alt="Thumbnail" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" />
              <div 
                class="absolute inset-0 border-[3px] rounded-[2rem] transition-all duration-500"
                :class="activeImg === idx ? 'border-black opacity-100' : 'border-transparent opacity-0 group-hover:opacity-40 group-hover:border-black'"
              ></div>
              <div v-if="activeImg === idx" class="absolute inset-0 bg-black/5 backdrop-blur-[1px]"></div>
            </div>
          </div>
        </div>

        <!-- Editorial Product Details -->
        <div class="space-y-12 animate-fade-in" style="animation-delay: 0.3s">
          <div class="space-y-6">
            <div class="flex flex-wrap items-center gap-4">
              <span class="px-5 py-2 rounded-full bg-black text-white text-[9px] font-black uppercase tracking-[0.3em] shadow-2xl shadow-black/10">Limited Release</span>
              <div v-if="currentStock > 0" class="flex items-center gap-2 px-4 py-2 rounded-full glass-panel border border-emerald-100/50">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="text-[10px] font-black uppercase tracking-widest text-emerald-700">Available: {{ currentStock }} Units</span>
              </div>
              <span v-else class="px-5 py-2 rounded-full bg-rose-50 text-rose-600 text-[9px] font-black uppercase tracking-[0.3em]">Sold Out</span>
            </div>

            <h1 class="text-3xl md:text-5xl lg:text-6xl font-black tracking-tight text-gray-900 leading-[1.1] md:leading-[1.05]">
              {{ product.name }}
            </h1>
            
            <div class="pt-4 flex flex-col gap-8 md:gap-10">
              <div class="relative">
                <div class="text-5xl md:text-6xl lg:text-7xl font-black text-gray-900 tracking-tighter transition-all duration-700 group hover:tracking-normal cursor-default">
                    ${{ currentPrice }}
                </div>
                <div class="flex items-center gap-3 mt-4">
                     <div class="flex text-amber-400">
                      <svg v-for="i in 5" :key="i" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                    </div>
                    <span class="text-[11px] font-black text-gray-400 uppercase tracking-widest leading-none">Verified 1.2k Purchases</span>
                </div>
              </div>

              <p class="text-gray-400 text-base md:text-lg leading-relaxed max-w-xl font-medium border-l-2 border-gray-100 pl-6">
                {{ product.short_description || 'A masterpiece of contemporary craftsmanship, blending structural integrity with fluid aesthetics for the discerning collector.' }}
              </p>
            </div>
          </div>

          <!-- Sophisticated Attribute Selectors -->
          <div class="space-y-12 border-t border-gray-50 pt-12">
            <div v-if="availableAttributes.length > 0" class="space-y-10">
               <div v-for="attr in availableAttributes" :key="attr.id" class="space-y-6">
                  <div class="flex items-center justify-between">
                      <h3 class="text-[11px] font-black uppercase tracking-[0.3em] text-gray-400">{{ attr.name }}</h3>
                      <button v-if="attr.name.toLowerCase().includes('size')" class="text-[10px] font-black uppercase tracking-widest text-indigo-600 underline underline-offset-4 hover:text-black transition-colors">Size Guide</button>
                  </div>
                  
                  <!-- Premium Color System -->
                  <div v-if="attr.type === 'color'" class="flex flex-wrap gap-5">
                    <button 
                      v-for="val in attr.values" 
                      :key="val.id" 
                      class="group relative w-12 h-12 rounded-full flex items-center justify-center transition-all duration-700 active:scale-90"
                      @click="selectedAttributes[attr.id] = val.id"
                    >
                      <div 
                        class="absolute -inset-2 rounded-full border border-gray-100 transition-all duration-500 scale-75 opacity-0 group-hover:scale-100 group-hover:opacity-100"
                        :class="selectedAttributes[attr.id] === val.id ? 'border-black scale-100 opacity-100' : ''"
                      ></div>
                      <div 
                        class="w-full h-full rounded-full shadow-inner border border-black/5"
                        :style="{ background: val.meta }"
                        :title="val.value"
                      ></div>
                      <div 
                        v-if="selectedAttributes[attr.id] === val.id" 
                        class="absolute inset-0 flex items-center justify-center"
                      >
                         <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" :class="val.meta === '#ffffff' ? 'text-black' : 'text-white'"><path d="M20 6 9 17l-5-5"/></svg>
                      </div>
                    </button>
                  </div>
                  
                  <!-- Editorial Size/Label Buttons -->
                  <div v-else class="flex flex-wrap gap-4">
                    <button 
                      v-for="val in attr.values" 
                      :key="val.id" 
                      @click="selectedAttributes[attr.id] = val.id"
                      class="min-w-[70px] px-6 py-4 rounded-2xl border-2 text-[11px] font-black uppercase tracking-[0.2em] transition-all duration-700 active:scale-95 shadow-sm hover:shadow-lg"
                      :class="selectedAttributes[attr.id] === val.id ? 'border-black bg-black text-white shadow-black/20' : 'border-gray-50 bg-white text-gray-400 hover:border-gray-900 hover:text-black'"
                    >
                      {{ val.value }}
                    </button>
                  </div>
               </div>
            </div>

            <!-- Ultimate Action Section -->
            <div class="space-y-10 pt-8 border-t border-gray-50 animate-fade-in" style="animation-delay: 0.5s">
               <!-- Advanced Counter - Now centered or full width on mobile -->
               <div class="flex justify-center sm:justify-start">
                  <div class="flex items-center p-2 rounded-full bg-gray-50 border border-gray-100 shadow-sm">
                    <button 
                      @click="quantity = Math.max(1, quantity - 1)"
                      class="w-14 h-14 rounded-full flex items-center justify-center bg-white shadow-sm hover:shadow-md hover:bg-black hover:text-white transition-all duration-500 text-gray-400 active:scale-90"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/></svg>
                    </button>
                    <span class="font-black text-gray-900 text-xl w-14 md:w-20 text-center tabular-nums">{{ quantity }}</span>
                    <button 
                      @click="quantity = Math.min(currentStock, quantity + 1)"
                      :disabled="quantity >= currentStock"
                      class="w-14 h-14 rounded-full flex items-center justify-center bg-white shadow-sm hover:shadow-md hover:bg-black hover:text-white transition-all duration-500 text-gray-400 disabled:opacity-20 active:scale-90"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    </button>
                  </div>
               </div>

               <div class="flex flex-col sm:flex-row gap-4 items-stretch">
                  <!-- Add to Cart -->
                  <button 
                    class="group relative flex-1 h-[72px] bg-[#0F172A] text-white rounded-2xl font-black text-[11px] uppercase tracking-[0.3em] overflow-hidden transition-all duration-700 disabled:opacity-40 shadow-xl hover:shadow-2xl hover:-translate-y-1 active:scale-95 active:translate-y-0"
                    @click="handleAddToCart"
                    :disabled="(product.has_variants && !selectedVariant) || currentStock <= 0"
                  >
                    <div class="absolute inset-0 bg-indigo-600 translate-y-full group-hover:translate-y-0 transition-transform duration-700 cubic-bezier(0.19, 1, 0.22, 1)"></div>
                    <span class="relative z-10 flex items-center justify-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                        <span>Add to Cart</span>
                    </span>
                  </button>

                  <!-- Order Now (Express) -->
                  <button 
                    class="group relative flex-1 h-[72px] bg-white text-black border-2 border-black rounded-2xl font-black text-[11px] uppercase tracking-[0.3em] overflow-hidden transition-all duration-700 disabled:opacity-40 shadow-lg hover:shadow-xl hover:-translate-y-1 active:scale-95 active:translate-y-0"
                    @click="handleOrderNow"
                    :disabled="(product.has_variants && !selectedVariant) || currentStock <= 0"
                  >
                    <div class="absolute inset-0 bg-black translate-y-full group-hover:translate-y-0 transition-transform duration-700 cubic-bezier(0.19, 1, 0.22, 1)"></div>
                    <span class="relative z-10 flex items-center justify-center gap-3 group-hover:text-white transition-colors duration-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="fill-current"><path d="m13 2-2 10h3L11 22l2-10h-3l2-10Z"/></svg>
                        <span>Order Now</span>
                    </span>
                  </button>
               </div>

               <!-- Subtle Trust Bar -->
               <div class="flex flex-col md:flex-row items-center justify-center gap-4 md:gap-8 py-5 px-6 md:py-6 md:px-8 rounded-[2rem] bg-gray-50/50 border border-gray-100/50">
                    <div class="flex items-center gap-3">
                         <div class="w-1.5 h-1.5 rounded-full bg-indigo-600"></div>
                         <span class="text-[9px] font-black uppercase tracking-widest text-gray-500">Museum Quality Collection</span>
                    </div>
                    <div class="hidden md:block w-px h-3 bg-gray-200"></div>
                    <div class="flex items-center gap-3">
                         <div class="w-1.5 h-1.5 rounded-full bg-black"></div>
                         <span class="text-[9px] font-black uppercase tracking-widest text-gray-500">Ethically Sourced Materials</span>
                    </div>
               </div>
            </div>
          </div>
        </div>
      </div>

          <!-- Trust Badges -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-8 border-t border-gray-100">
            <div class="flex items-center gap-4 group">
              <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">🚚</div>
              <div>
                <p class="font-bold text-gray-900 text-xs md:text-sm">Free Global Shipping</p>
                <p class="text-[10px] md:text-xs text-gray-400">On all orders above $200</p>
              </div>
            </div>
            <div class="flex items-center gap-4 group">
              <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform duration-300">🔄</div>
              <div>
                <p class="font-bold text-gray-900 text-xs md:text-sm">30-Day Happiness</p>
                <p class="text-[10px] md:text-xs text-gray-400">Easy returns and exchanges</p>
              </div>
            </div>
          </div>

      <!-- Detail Tabs -->
      <section class="mt-20 md:mt-32 pt-16 border-t border-gray-100 animate-fade-in" style="animation-delay: 0.4s">
        <div class="max-w-4xl mx-auto">
          <div class="flex items-center justify-center gap-4 md:gap-16 mb-12 border-b border-gray-50 pb-px overflow-x-auto hide-scrollbar whitespace-nowrap">
            <button 
              v-for="tab in ['Description', 'Specifications', 'Reviews']" 
              :key="tab"
              @click="activeTab = tab"
              class="relative py-4 text-[10px] md:text-sm font-black uppercase tracking-[0.2em] transition-all duration-300"
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
                  <h3 class="text-2xl font-black text-gray-900 mb-2">98% Satisfied Customers</h3>
                  <p class="text-sm font-medium tracking-wide">Join the thousands of happy customers who have transformed their space.</p>
                  <button class="mt-10 group px-12 py-4 rounded-full border-2 border-black text-black font-black text-[11px] uppercase tracking-[0.3em] hover:bg-black hover:text-white transition-all duration-700 flex items-center gap-4 mx-auto">
                    Write a Review
                    <span class="w-1 h-1 rounded-full bg-black group-hover:bg-white transition-colors"></span>
                  </button>
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
const selectedAttributes = ref({})
const isManualImageSelection = ref(false)

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

const availableAttributes = computed(() => {
  if (!rawProduct.value || !rawProduct.value.variants) return []
  
  const attrMap = {}
  
  rawProduct.value.variants.forEach(variant => {
    variant.attributes.forEach(attrVal => {
      const attribute = attrVal.attribute
      if (!attrMap[attribute.id]) {
        attrMap[attribute.id] = {
          id: attribute.id,
          name: attribute.name,
          type: attribute.type,
          values: []
        }
      }
      
      if (!attrMap[attribute.id].values.find(v => v.id === attrVal.id)) {
        attrMap[attribute.id].values.push({
          id: attrVal.id,
          value: attrVal.value,
          meta: attrVal.meta
        })
      }
    })
  })
  
  return Object.values(attrMap)
})

// Initialize selectedAttributes with first values
watch(() => availableAttributes.value, (newAttrs) => {
  if (newAttrs.length > 0 && Object.keys(selectedAttributes.value).length === 0) {
    newAttrs.forEach(attr => {
      selectedAttributes.value[attr.id] = attr.values[0].id
    })
  }
}, { immediate: true })

const selectedVariant = computed(() => {
    if (!rawProduct.value || !rawProduct.value.variants || availableAttributes.value.length === 0) return null
    
    return rawProduct.value.variants.find(variant => {
        return variant.attributes.every(attrVal => {
            return selectedAttributes.value[attrVal.attribute_id] === attrVal.id
        })
    }) || null
})

const currentPrice = computed(() => {
    if (selectedVariant.value) return selectedVariant.value.price
    return product.value?.price
})

const currentStock = computed(() => {
    if (product.value?.has_variants) {
        return selectedVariant.value ? selectedVariant.value.stock_qty : 0
    }
    return rawProduct.value?.stock_qty || 0
})

watch(currentStock, (newStock) => {
    if (quantity.value > newStock) {
        quantity.value = Math.max(1, newStock)
    }
})

watch(selectedVariant, (newVariant) => {
    if (newVariant && newVariant.image_url) {
        isManualImageSelection.value = false
    }
})

const currentImage = computed(() => {
    if (isManualImageSelection.value) {
        return product.value?.gallery[activeImg.value]
    }
    if (selectedVariant.value && selectedVariant.value.image_url) {
        return selectedVariant.value.image_url
    }
    return product.value?.gallery[activeImg.value]
})

const handleThumbnailClick = (idx) => {
    activeImg.value = idx
    isManualImageSelection.value = true
}

const handleAddToCart = (openCart = true) => {
  if (!product.value) return
  
  const itemToAdd = {
    ...product.value,
    price: currentPrice.value,
    image: currentImage.value,
    variant_id: selectedVariant.value?.id || null,
    selected_attributes: selectedVariant.value?.attributes.map(a => ({
        name: a.attribute.name,
        value: a.value
    })) || []
  }
  
  addToCart(itemToAdd, quantity.value, openCart)
}

const handleOrderNow = async () => {
    if (!product.value) return
    
    // First add to cart without opening the cart tab
    handleAddToCart(false)
    
    // Then navigate to checkout
    await navigateTo('/checkout')
}
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes float {
  0% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
  100% { transform: translateY(0px); }
}

@keyframes shine {
  0% { left: -100%; }
  100% { left: 200%; }
}

.animate-shine {
  animation: shine 2s cubic-bezier(0.4, 0, 0.2, 1) infinite;
}

.animate-fade-in {
  opacity: 0;
  animation: fadeIn 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.image-zoom-enter-active,
.image-zoom-leave-active {
  transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}
.image-zoom-enter-from {
  opacity: 0;
  transform: scale(1.1) rotate(1deg);
  filter: blur(10px);
}
.image-zoom-leave-to {
  opacity: 0;
  transform: scale(0.9) rotate(-1deg);
  filter: blur(10px);
}

.hide-scrollbar::-webkit-scrollbar {
  display: none;
}

.product-detail-page {
  font-family: 'Poppins', sans-serif;
  letter-spacing: -0.01em;
}

h1, h2, h3, button, nav {
  font-family: 'Poppins', sans-serif;
}

/* Glassmorphism Classes */
.glass-panel {
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.glass-dark {
  background: rgba(15, 23, 42, 0.8);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

/* Tab Transitions */
.tab-fade-enter-active,
.tab-fade-leave-active {
  transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}
.tab-fade-enter-from {
  opacity: 0;
  transform: translateY(20px);
}
.tab-fade-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}
</style>

