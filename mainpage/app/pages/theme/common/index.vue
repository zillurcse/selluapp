<template>
  <div class="common-store-page text-white min-h-screen font-sans selection:bg-emerald-500 selection:text-white overflow-x-hidden">

    <!-- Sticky Navigation -->
    <nav class="fixed top-0 left-0 w-full z-50 transition-all duration-300"
      :class="scrolled ? 'bg-black/80 backdrop-blur-2xl border-b border-white/5 py-4 px-6' : 'py-8 px-6'"
    >
      <div class="max-w-7xl mx-auto flex items-center justify-between">
        <NuxtLink to="/" class="group flex items-center gap-3">
          <div class="w-11 h-11 bg-white rounded-2xl flex items-center justify-center group-hover:rotate-[15deg] transition-all duration-500 shadow-xl shadow-white/10 flex-shrink-0">
            <img v-if="vendor?.logo_url" :src="vendor.logo_url" class="w-7 h-7 object-contain" />
            <span v-else class="text-black font-black text-xl">{{ vendor?.store_name?.[0] || 'S' }}</span>
          </div>
          <div class="flex flex-col">
            <span class="text-white font-black tracking-tighter text-xl leading-none capitalize">{{ vendor?.store_name || 'Store' }}</span>
            <span class="text-emerald-400 text-[10px] font-black uppercase tracking-[0.3em]">Official Store</span>
          </div>
        </NuxtLink>

        <div class="flex items-center gap-4">
          <!-- Cart -->
          <button class="relative p-2 rounded-xl hover:bg-white/10 transition-all" @click="toggleCart">
            <div v-if="cartCount > 0" class="absolute -top-1 -right-1 w-5 h-5 bg-emerald-500 rounded-full flex items-center justify-center text-[10px] font-black animate-pulse">{{ cartCount }}</div>
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          </button>

          <!-- User -->
          <div class="relative" ref="profileDropdown">
            <template  v-if="authStore.isAuthenticated">
              <button @click="isProfileOpen = !isProfileOpen" class="w-10 h-10 rounded-xl bg-white text-black flex items-center justify-center font-black text-sm hover:scale-105 transition-all shadow-lg">
                {{ userInitials }}
              </button>
              <div v-if="isProfileOpen" class="absolute top-full right-0 mt-3 bg-black/90 backdrop-blur-2xl border border-white/10 rounded-2xl p-2 shadow-2xl z-[100] min-w-[200px]">
                <div class="px-4 py-3 border-b border-white/5 mb-2">
                  <div class="text-sm font-bold text-white truncate">{{ authStore.user?.name }}</div>
                  <div class="text-xs text-white/40 truncate">{{ authStore.user?.email }}</div>
                </div>
                <NuxtLink to="/account" @click="isProfileOpen = false" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold text-white/60 hover:text-white hover:bg-white/5 transition-all">🏠 Dashboard</NuxtLink>
                <NuxtLink to="/account" @click="isProfileOpen = false" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold text-white/60 hover:text-white hover:bg-white/5 transition-all">📦 My Orders</NuxtLink>
                <div class="border-t border-white/5 my-1"></div>
                <button @click="handleLogout" class="w-full text-left flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold text-rose-400 hover:bg-rose-500/5 transition-all">🚪 Sign out</button>
              </div>
            </template>
            <template v-else>
              <NuxtLink to="/login" class="px-5 py-2.5 rounded-xl bg-white text-black font-black text-xs uppercase tracking-widest hover:bg-emerald-500 hover:text-white transition-all shadow-lg">Login</NuxtLink>
            </template>
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Banner -->
    <header class="relative pt-36 pb-20 px-6 lg:px-20 min-h-[70vh] flex flex-col items-start justify-center overflow-hidden">
      <!-- Ambient bg -->
      <div class="absolute inset-0 -z-10">
        <div class="absolute top-1/2 left-1/4 -translate-x-1/2 -translate-y-1/2 w-[100vw] h-[100vw] bg-emerald-600/5 rounded-full blur-[180px]"></div>
        <div class="absolute top-0 right-0 w-[50vw] h-[50vw] bg-teal-500/5 rounded-full blur-[120px]"></div>
      </div>

      <div class="max-w-4xl space-y-8 animate-slide-up">
        <span class="px-5 py-2 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-[0.4em] inline-block">
          {{ settings.hero_subtitle || 'Official Store' }}
        </span>
        <h1 class="text-5xl md:text-7xl font-black tracking-tight leading-[1] font-heading">
          {{ data.landingPage?.title || vendor?.store_name || 'Welcome' }}<br/>
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-400 to-emerald-200">
            {{ settings.hero_desc ? '' : 'Shop Everything.' }}
          </span>
        </h1>
        <p v-if="settings.hero_desc" class="text-white/50 text-lg max-w-2xl leading-relaxed font-light">{{ settings.hero_desc }}</p>
        <div class="flex flex-wrap gap-4 pt-2">
          <button class="px-8 py-4 rounded-2xl bg-white text-black font-black uppercase tracking-[0.2em] text-xs hover:scale-105 active:scale-95 transition-all shadow-2xl shadow-white/5">
            {{ settings.cta_text || 'Shop Now' }}
          </button>
          <NuxtLink to="/" class="px-8 py-4 rounded-2xl bg-white/5 border border-white/10 text-white font-black uppercase tracking-[0.2em] text-xs hover:bg-white/10 transition-all">
            Browse All
          </NuxtLink>
        </div>
      </div>
    </header>

    <!-- Key Features -->
    <section v-if="keyFeatures.length > 0" class="py-20 px-6 lg:px-20">
      <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
        <div v-for="(feature, idx) in keyFeatures" :key="idx"
          class="group p-8 rounded-[2.5rem] bg-white/[0.03] border border-white/[0.05] hover:bg-white/[0.06] hover:border-white/10 transition-all duration-700 flex flex-col gap-6 animate-slide-up"
          :style="{ animationDelay: (0.2 + idx * 0.1) + 's' }"
        >
          <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 group-hover:scale-110 transition-all">
            <div v-html="feature.icon" class="w-7 h-7"></div>
          </div>
          <div>
            <h3 class="text-xl font-black mb-2 font-heading">{{ feature.title }}</h3>
            <p class="text-white/40 leading-relaxed text-sm">{{ feature.desc }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Product Grid -->
    <section class="py-20 px-6 lg:px-20">
      <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between mb-10">
          <h2 class="text-3xl font-black font-heading tracking-tight">Featured Products</h2>
          <NuxtLink to="/" class="text-[10px] font-black uppercase tracking-[0.3em] text-white/40 hover:text-white transition-colors flex items-center gap-2 group">
            View All
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="translate-x-0 group-hover:translate-x-1 transition-transform"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </NuxtLink>
        </div>

        <!-- Product cards grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div v-for="(prod, idx) in featuredProducts" :key="prod.id"
            class="group relative rounded-[2rem] overflow-hidden bg-white/[0.02] border border-white/5 p-6 flex flex-col gap-4 hover:bg-white/[0.04] hover:border-white/10 transition-all duration-700 animate-slide-up"
            :style="{ animationDelay: (0.1 * idx) + 's' }"
          >
            <!-- Ambient glow -->
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-emerald-500/5 rounded-full blur-[80px] opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>

            <!-- Image -->
            <div class="relative flex items-center justify-center aspect-square rounded-2xl bg-white/5 overflow-hidden">
              <img v-if="prod.image_url || prod.image"
                :src="prod.image_url || prod.image"
                :alt="prod.name"
                class="w-[85%] h-auto object-contain drop-shadow-lg group-hover:scale-110 group-hover:-translate-y-2 transition-all duration-700"
                loading="lazy"
              />
              <div v-else class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="text-white/20"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
              </div>
            </div>

            <!-- Info -->
            <div class="relative z-10 flex-1">
              <p class="text-[10px] font-bold uppercase tracking-widest text-white/30 mb-1">{{ prod.brand?.name || 'Product' }}</p>
              <h3 class="font-black text-white text-sm leading-tight line-clamp-2 mb-2 font-heading">{{ prod.name }}</h3>
              <div class="flex items-center gap-2">
                <span class="text-lg font-black text-white">৳{{ prod.sale_price }}</span>
                <span v-if="prod.regular_price > prod.sale_price" class="text-sm text-white/30 line-through">৳{{ prod.regular_price }}</span>
              </div>
            </div>

            <!-- Add to cart -->
            <button
              @click="addToCart(prod)"
              class="relative z-10 w-full py-3 rounded-xl bg-white text-black font-black uppercase tracking-widest text-[10px] hover:bg-emerald-500 hover:text-white transition-all active:scale-95 flex items-center justify-center gap-2"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Add to Cart
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Strip -->
    <section class="py-20 px-6 lg:px-20">
      <div class="max-w-7xl mx-auto rounded-[3rem] bg-gradient-to-br from-emerald-900/30 via-teal-900/20 to-transparent border border-emerald-500/10 p-12 lg:p-20 text-center relative overflow-hidden">
        <div class="absolute -right-30 -bottom-30 w-96 h-96 bg-emerald-500/10 blur-[100px] rounded-full"></div>
        <div class="relative z-10 space-y-6">
          <h2 class="text-4xl lg:text-5xl font-black font-heading tracking-tight">{{ settings.cta_text ? `${settings.cta_text} Today.` : 'Shop the Collection.' }}</h2>
          <p class="text-white/40 text-lg max-w-xl mx-auto font-light">{{ vendor?.store_name || 'Our store' }} – bringing you the best products at unbeatable prices.</p>
          <NuxtLink to="/" class="inline-block px-10 py-4 rounded-2xl bg-white text-black font-black uppercase tracking-widest text-sm hover:bg-emerald-500 hover:text-white transition-all shadow-2xl">
            {{ settings.cta_text || 'Shop All Products' }}
          </NuxtLink>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="py-16 px-6 lg:px-20 border-t border-white/5 bg-black">
      <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8">
        <div class="flex flex-col items-center md:items-start gap-2">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-white rounded-xl flex items-center justify-center">
              <span class="text-black font-black text-lg">{{ vendor?.store_name?.[0] || 'S' }}</span>
            </div>
            <span class="text-white font-black tracking-tighter text-xl">{{ vendor?.store_name || 'Store' }}</span>
          </div>
          <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/20">© 2026 {{ vendor?.store_name || 'Store' }}. All Rights Reserved.</p>
        </div>
        <div class="flex gap-8 text-[10px] font-bold uppercase tracking-[0.2em] text-white/30">
          <NuxtLink to="/" class="hover:text-white transition-colors">Shop</NuxtLink>
          <NuxtLink to="/" class="hover:text-white transition-colors">About</NuxtLink>
          <NuxtLink to="/" class="hover:text-white transition-colors">Contact</NuxtLink>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { toast } from 'vue-sonner'

const props = defineProps({
  data: { type: Object, required: true }
})

const { toggleCart, cartCount, addToCart: cartAddToCart } = useCart()
const authStore = useAuthStore()

const vendor = computed(() => props.data.vendor || {})
const settings = computed(() => props.data.landingPage?.settings || {})
const featuredProducts = computed(() => props.data.products || [])

const scrolled = ref(false)
const isProfileOpen = ref(false)
const profileDropdown = ref(null)

const handleScroll = () => { scrolled.value = window.scrollY > 50 }

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  document.addEventListener('click', handleClickOutside)
})
onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
  document.removeEventListener('click', handleClickOutside)
})

const handleClickOutside = (e) => {
  if (profileDropdown.value && !profileDropdown.value.contains(e.target)) isProfileOpen.value = false
}

const userInitials = computed(() => {
  if (!authStore.user?.name) return 'U'
  const n = authStore.user.name.split(' ')
  return n.length >= 2 ? (n[0][0] + n[1][0]).toUpperCase() : authStore.user.name.substring(0, 2).toUpperCase()
})

const handleLogout = async () => {
  await authStore.logout()
  isProfileOpen.value = false
  toast.success('Logged out.')
}

const addToCart = (product) => {
  cartAddToCart({ product_id: product.id, quantity: 1 })
  toast.success(`${product.name} added to cart!`)
}

const keyFeatures = computed(() => {
  if (settings.value.features?.length > 0) {
    return settings.value.features.map((f, idx) => ({
      title: f.title, desc: f.desc,
      icon: idx === 0
        ? '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>'
        : idx === 1
        ? '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>'
        : '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
    }))
  }
  return []
})
</script>

<style scoped>
.font-heading { font-family: 'Outfit', sans-serif; }

.common-store-page {
  font-family: 'Outfit', sans-serif;
  scroll-behavior: smooth;
  background-color: #060808;
  position: relative;
  z-index: 10;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-slide-up {
  opacity: 0;
  animation: slideUp 1.2s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
}
.common-store-page::-webkit-scrollbar { display: none; }
.common-store-page { -ms-overflow-style: none; scrollbar-width: none; }
</style>
