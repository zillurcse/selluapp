<template>
  <nav 
    :class="[
      'fixed top-0 left-0 right-0 z-[900] flex items-center transition-all duration-500',
      isScrolled ? 'bg-white/90 backdrop-blur-xl h-[70px] shadow-sm' : 'bg-transparent h-20'
    ]"
  >
    <div class="container mx-auto flex justify-between items-center h-full px-4 sm:px-6 -mt-1 md:-mt-0">
      <NuxtLink to="/" class="flex items-center group">
        <template v-if="storefrontStore.vendorProfile">
          <!-- Vendor Logo -->
          <img v-if="storefrontStore.vendorProfile.logo_url" :src="storefrontStore.vendorProfile.logo_url" :alt="storefrontStore.vendorProfile.store_name" class="h-8 max-w-[150px] object-contain" />
          <span v-else class="text-xl md:text-2xl font-extrabold tracking-tighter text-gray-900 font-heading">
            {{ storefrontStore.vendorProfile.store_name }}<span class="text-indigo-600">.</span>
          </span>
        </template>
        <template v-else>
          <span class="text-2xl font-extrabold tracking-tighter text-gray-900 font-heading">EMU</span>
          <span class="text-2xl font-extrabold text-indigo-600 font-heading">.</span>
        </template>
      </NuxtLink>
      
      <!-- Desktop Navigation -->
      <div class="hidden md:flex items-center gap-10 h-full">
        <NuxtLink to="/" class="nav-link-tailwind">Home</NuxtLink>
        
        <!-- Shop Dropdown (Hover) -->
        <div class="relative h-full flex items-center group/nav">
          <button class="nav-link-tailwind flex items-center gap-1">
            Shop 
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="transition-transform duration-300 group-hover/nav:rotate-180"><path d="m6 9 6 6 6-6"/></svg>
          </button>
          
          <div class="absolute top-full left-1/2 -translate-x-1/2 translate-y-4 opacity-0 invisible group-hover/nav:opacity-100 group-hover/nav:visible group-hover/nav:translate-y-0 transition-all duration-500 bg-white/95 backdrop-blur-xl border border-gray-100 rounded-[2rem] p-8 flex gap-12 shadow-2xl z-[100] min-w-max max-w-[90vw]">
            <!-- Categories Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-12 gap-y-10">
              <div v-for="cat in storefrontStore.topCategories.slice(0, 8)" :key="cat.slug" class="flex flex-col gap-4 min-w-[160px]">
                <NuxtLink :to="`/shop?category=${cat.slug}`" class="flex items-center gap-3 group/cat-title">
                  <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-xl group-hover/cat-title:bg-indigo-50 group-hover/cat-title:scale-110 transition-all duration-300">
                    {{ cat.icon }}
                  </div>
                  <div class="flex flex-col">
                    <span class="text-[0.7rem] uppercase tracking-[0.15em] font-black text-gray-900 group-hover/cat-title:text-indigo-600 transition-colors">{{ cat.name }}</span>
                    <span class="text-[0.6rem] text-gray-400 font-bold uppercase tracking-tighter">{{ cat.children?.length || 0 }} Collections</span>
                  </div>
                </NuxtLink>
                
                <div class="flex flex-col gap-1.5 pl-1.5 border-l-2 border-transparent hover:border-indigo-100 transition-colors">
                  <NuxtLink 
                    v-for="child in cat.children" 
                    :key="child.slug" 
                    :to="`/shop?category=${child.slug}`" 
                    class="text-[0.88rem] font-semibold text-gray-500 hover:text-indigo-600 hover:translate-x-2 transition-all duration-300 py-0.5"
                  >
                    {{ child.name }}
                  </NuxtLink>
                  <NuxtLink 
                    v-if="!cat.children?.length" 
                    :to="`/shop?category=${cat.slug}`" 
                    class="text-[0.8rem] font-medium text-gray-400 hover:text-indigo-500 transition-colors italic"
                  >
                    View All {{ cat.name }}
                  </NuxtLink>
                </div>
              </div>
            </div>

            <!-- Featured Section -->
            <div class="border-l border-gray-100 pl-12 flex flex-col gap-6">
              <div>
                <h4 class="text-[0.65rem] uppercase tracking-[0.2em] font-bold text-gray-400 mb-4 px-2">Featured</h4>
                <div class="flex flex-col gap-1">
                  <NuxtLink to="/shop?sort=newest" class="dropdown-item-tailwind">
                    <span class="mr-2">✨</span> New Arrivals
                  </NuxtLink>
                  <NuxtLink to="/shop?sort=featured" class="dropdown-item-tailwind">
                    <span class="mr-2">🔥</span> Best Sellers
                  </NuxtLink>
                  <NuxtLink to="/shop?sale=true" class="dropdown-item-tailwind text-rose-500 hover:bg-rose-50">
                    <span class="mr-2">🏷️</span> Seasonal Sale
                  </NuxtLink>
                </div>
              </div>
              
              <div class="relative w-64 aspect-[4/3] rounded-2xl overflow-hidden group/nav-img shadow-lg">
                <img src="~/assets/img/vase.png" alt="Featured" class="w-full h-full object-cover transition-transform duration-1000 group-hover/nav-img:scale-110" />
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex flex-col justify-end p-5">
                  <span class="text-[0.6rem] font-black text-indigo-300 uppercase tracking-widest mb-1">Premium Quality</span>
                  <h5 class="text-white font-extrabold text-sm leading-snug">The Artisanal Collection 2026</h5>
                </div>
                <div class="absolute inset-0 bg-indigo-600/10 opacity-0 group-hover/nav-img:opacity-100 transition-opacity duration-500"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pages Dropdown (Hover) -->
        <div class="relative h-full flex items-center group/nav">
          <button class="nav-link-tailwind flex items-center gap-1">
            Pages 
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="transition-transform duration-300 group-hover/nav:rotate-180"><path d="m6 9 6 6 6-6"/></svg>
          </button>
          
          <div class="absolute top-full left-1/2 -translate-x-1/2 translate-y-4 opacity-0 invisible group-hover/nav:opacity-100 group-hover/nav:visible group-hover/nav:translate-y-0 transition-all duration-500 bg-white/95 backdrop-blur-xl border border-gray-100 rounded-2xl p-2 flex flex-col gap-1 shadow-2xl z-[100] min-w-[220px]">
             <NuxtLink to="/about" class="dropdown-item-tailwind">About Us</NuxtLink>
             <NuxtLink to="/collections" class="dropdown-item-tailwind">Collections</NuxtLink>
             <NuxtLink to="/contact" class="dropdown-item-tailwind">Contact</NuxtLink>
             <NuxtLink to="/faq" class="dropdown-item-tailwind">FAQ</NuxtLink>
             
             <div class="relative group/nested">
               <button class="dropdown-item-tailwind flex items-center justify-between w-full">
                 More 
                 <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
               </button>
               <div class="absolute left-full top-0 ml-1 opacity-0 invisible group-hover/nested:opacity-100 group-hover/nested:visible bg-white/95 backdrop-blur-xl border border-gray-100 rounded-xl p-2 flex flex-col gap-1 shadow-2xl min-w-[200px]">
                 <NuxtLink to="/terms" class="dropdown-item-tailwind">Terms & Conditions</NuxtLink>
                 <NuxtLink to="/privacy" class="dropdown-item-tailwind">Privacy Policy</NuxtLink>
               </div>
             </div>
          </div>
        </div>

        <NuxtLink to="/about" class="nav-link-tailwind">Our Story</NuxtLink>
      </div>

      <!-- Actions -->
      <div class="flex items-center gap-2 md:gap-4">
        <button class="p-2.5 rounded-full hover:bg-gray-100 transition-all active:scale-95 text-gray-900">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        </button>
        
        <button class="relative p-2.5 rounded-full hover:bg-gray-100 transition-all active:scale-95 text-gray-900" @click="toggleCart">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          <span v-if="cartCount > 0" class="absolute top-1.5 right-1.5 bg-gray-900 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center animate-bounce">{{ cartCount }}</span>
        </button>

        <!-- User Account / Login Toggle -->
        <div class="relative h-auto flex items-center" ref="profileDropdown">
          <template v-if="authStore.isAuthenticated">
            <button @click="toggleProfile" class="h-10 w-10 md:h-11 md:w-11 rounded-xl bg-gradient-to-br from-indigo-600 to-blue-500 p-0.5 shadow-lg shadow-indigo-200/50 hover:scale-105 active:scale-95 transition-all flex items-center justify-center">
              <div class="w-full h-full bg-white rounded-[10px] flex items-center justify-center font-black text-xs md:text-sm text-indigo-600">{{ userInitials }}</div>
            </button>
            
            <div 
              :class="[
                'absolute top-full right-0 mt-3 bg-white/95 backdrop-blur-xl border border-gray-100 rounded-2xl p-2 flex flex-col shadow-2xl z-[100] min-w-[240px] transition-all duration-300 transform',
                isProfileOpen ? 'opacity-100 visible translate-y-0' : 'opacity-0 invisible -translate-y-2 pointer-events-none'
              ]"
            >
              <div class="px-4 py-3 border-b border-gray-50 mb-2">
                <div class="text-[0.9rem] font-bold text-gray-900 truncate">{{ authStore.user?.name }}</div>
                <div class="text-[0.75rem] font-medium text-gray-400 truncate">{{ authStore.user?.email }}</div>
              </div>
              <NuxtLink v-for="link in accountLinks" :key="link.to" :to="link.to" @click="isProfileOpen = false" class="dropdown-item-tailwind flex items-center gap-3">
                <span class="text-lg opacity-70 group-hover:opacity-100">{{ link.icon }}</span> {{ link.label }}
              </NuxtLink>
              <div class="border-t border-gray-50 my-1"></div>
              <button @click="handleLogout" class="dropdown-item-tailwind w-full text-left flex items-center gap-3 text-rose-500 hover:bg-rose-50 cursor-pointer border-none bg-transparent">
                <span class="text-lg">🚪</span> Sign out
              </button>
            </div>
          </template>
          <template v-else>
            <NuxtLink to="/login" class="px-4 py-2 rounded-xl bg-gray-900 text-white font-bold text-sm hover:bg-indigo-600 transition-colors shadow-lg shadow-gray-900/20">Login</NuxtLink>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { useAuthStore } from '~/stores/useAuthStore'
import { useStorefrontStore } from '~/stores/useStorefrontStore'
import { toast } from 'vue-sonner'

const { toggleCart, cartCount } = useCart()
const authStore = useAuthStore()
const storefrontStore = useStorefrontStore()
const isScrolled = ref(false)
const isProfileOpen = ref(false)
const profileDropdown = ref<HTMLElement | null>(null)

const userInitials = computed(() => {
  if (!authStore.user?.name) return 'U'
  const names = authStore.user.name.split(' ')
  if (names.length >= 2) {
    return (names[0][0] + names[1][0]).toUpperCase()
  }
  return authStore.user.name.substring(0, 2).toUpperCase()
})

const handleLogout = async () => {
  try {
    await authStore.logout()
    isProfileOpen.value = false
    toast.success('Successfully logged out')
  } catch (error) {
    console.error('Logout error:', error)
    toast.error('Failed to log out')
  }
}

const toggleProfile = (e: Event) => {
  e.preventDefault()
  isProfileOpen.value = !isProfileOpen.value
}

const handleClickOutside = (event: Event) => {
  if (profileDropdown.value && !profileDropdown.value.contains(event.target as Node)) {
    isProfileOpen.value = false
  }
}

const accountLinks = [
  { to: '/account', label: 'Dashboard', icon: '🏠' },
  { to: '/account', label: 'My Orders', icon: '📦' },
  { to: '/account', label: 'Wishlist', icon: '❤️' },
  { to: '/account', label: 'Profile Settings', icon: '👤' },
]

onMounted(async () => {
  // Fetch storefront data if categories are empty
  if (storefrontStore.topCategories.length === 0) {
    await storefrontStore.fetchStorefront()
  }

  window.addEventListener('scroll', () => {
    isScrolled.value = window.scrollY > 50
  })
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.font-heading {
  font-family: var(--font-heading);
}

.nav-link-tailwind {
  @apply text-[0.92rem] font-bold text-gray-400 tracking-tight transition-all duration-300 relative py-1 hover:text-gray-900;
}

.group\/nav:hover .nav-link-tailwind {
  @apply text-gray-900;
}

.nav-link-tailwind::after {
  content: '';
  @apply absolute bottom-0 left-0 w-full h-[2.5px] bg-gray-900 scale-x-0 origin-right transition-transform duration-500;
}

.nav-link-tailwind:hover::after,
.group\/nav:hover .nav-link-tailwind::after {
  @apply scale-x-100 origin-left;
}

.dropdown-item-tailwind {
  @apply text-[0.88rem] font-semibold text-gray-500 px-4 py-2.5 rounded-xl transition-all duration-300 hover:bg-gray-50 hover:text-gray-900 hover:translate-x-1 flex items-center;
}

@keyframes bounce {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-4px); }
}
.animate-bounce {
  animation: bounce 0.6s infinite ease-in-out;
}
</style>
