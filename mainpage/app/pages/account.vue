<template>
  <div class="min-h-screen bg-gray-50/50">
    <!-- Profile Header -->
    <header class="bg-white border-b border-gray-100 sticky top-0 z-30 backdrop-blur-md bg-white/80">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 md:py-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div class="flex items-center gap-4">
            <!-- Dynamic Avatar with Gradient -->
            <div class="relative group">
              <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-gradient-to-br from-indigo-600 via-violet-600 to-purple-600 flex items-center justify-center text-2xl font-black text-white shrink-0 shadow-xl shadow-indigo-200/50 transition-transform duration-300 group-hover:scale-105">
                {{ user.initials || 'U' }}
              </div>
              <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 border-4 border-white rounded-full shadow-sm"></div>
            </div>
            
            <div class="space-y-0.5">
              <h1 class="text-xl md:text-2xl font-black text-gray-900 tracking-tight leading-tight">
                {{ user.name || 'Account Dashboard' }}
              </h1>
              <div class="flex items-center gap-2">
                <p class="text-sm font-medium text-gray-500">{{ user.email }}</p>
                <div v-if="user.loyalty_points > 0" class="flex items-center gap-1 px-2 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-black uppercase tracking-wider rounded-md border border-amber-100">
                  <span class="animate-pulse">✨</span> Premium
                </div>
              </div>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-gray-50 rounded-xl border border-gray-100">
              <span class="text-lg">💎</span>
              <span class="text-sm font-bold text-gray-700">{{ user.loyalty_points || 0 }} <span class="text-gray-400 font-medium">Credits</span></span>
            </div>
            <NuxtLink to="/" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 rounded-2xl text-sm font-bold text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-all active:scale-95 shadow-sm">
              <span class="text-xs">←</span> Continue Shopping
            </NuxtLink>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
      <div class="flex flex-col lg:flex-row gap-8">
        <!-- Glassmorphism Sidebar -->
        <aside class="lg:w-72 shrink-0">
          <div class="sticky top-28 space-y-4">
            <nav class="bg-white/70 backdrop-blur-xl rounded-[2rem] border border-white p-3 shadow-2xl shadow-gray-200/50">
              <div class="space-y-1">
                <NuxtLink
                  v-for="item in navItems"
                  :key="item.id"
                  :to="item.path"
                  class="w-full flex items-center gap-3.5 px-5 py-4 rounded-2xl text-sm font-bold transition-all duration-300 text-left border-none cursor-pointer group no-underline"
                  :class="isRouteActive(item.path)
                    ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200 ring-4 ring-indigo-50'
                    : 'text-gray-500 bg-transparent hover:bg-gray-50 hover:text-gray-900'"
                >
                  <span class="text-xl transition-transform duration-300 group-hover:scale-110" :class="{ 'filter grayscale brightness-200': isRouteActive(item.path) }">
                    {{ item.icon }}
                  </span>
                  <span class="flex-1">{{ item.label }}</span>
                  <span v-if="item.badge" class="px-2.5 py-1 text-[10px] font-black rounded-lg transition-colors border"
                    :class="isRouteActive(item.path) 
                      ? 'bg-white/20 border-white/20 text-white' 
                      : 'bg-indigo-50 border-indigo-100 text-indigo-600'">
                    {{ item.badge }}
                  </span>
                </NuxtLink>
              </div>

              <!-- Logout at bottom of sidebar -->
              <div class="mt-4 pt-4 border-t border-gray-100 px-2">
                <button 
                  @click="logout" 
                  class="w-full flex items-center gap-3.5 px-5 py-3.5 rounded-2xl text-sm font-bold text-red-500 hover:bg-red-50 transition-all border-none bg-transparent cursor-pointer text-left group"
                >
                  <span class="text-xl transition-transform group-hover:-translate-x-1">🚪</span>
                  <span>Sign out</span>
                </button>
              </div>
            </nav>

            <!-- Support Card -->
            <div class="bg-gradient-to-br from-indigo-600 to-violet-700 rounded-[2rem] p-6 text-white shadow-xl shadow-indigo-200 relative overflow-hidden group">
              <div class="relative z-10 space-y-3">
                <h3 class="font-bold">Need Help?</h3>
                <p class="text-xs text-indigo-100 leading-relaxed font-medium">Our support team is available 24/7 to assist you with any questions.</p>
                <button class="w-full py-2.5 bg-white/20 hover:bg-white/30 backdrop-blur-md rounded-xl text-xs font-bold transition-all border-none text-white cursor-pointer">
                  Contact Support
                </button>
              </div>
              <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
            </div>
          </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 min-w-0">
          <NuxtPage />
        </main>
      </div>
    </div>
  </div>
</template>

<script setup>
const {
  user,
  allOrders,
  wishlistCount,
  fetchCustomerData,
  logout
} = useAccount()

const route = useRoute()

// Navigation Definition
const navItems = computed(() => [
  { id: 'overview',   icon: '⚡', label: 'Dashboard',   path: '/account' },
  { id: 'orders',     icon: '📦', label: 'My Orders',   path: '/account/orders', badge: allOrders.value.length > 0 ? allOrders.value.length.toString() : null },
  { id: 'wishlist',   icon: '💖', label: 'Wishlist',    path: '/account/wishlist', badge: wishlistCount.value > 0 ? wishlistCount.value.toString() : null },
  { id: 'addresses',  icon: '📍', label: 'Addresses',   path: '/account/addresses' },
  { id: 'profile',    icon: '👤', label: 'My Profile', path: '/account/profile' },
  { id: 'settings',   icon: '⚙️', label: 'Settings',   path: '/account/settings' },
])

const isRouteActive = (path) => {
  if (path === '/account') return route.path === '/account'
  return route.path.startsWith(path)
}

onMounted(() => {
  fetchCustomerData()
})
</script>

<style scoped>
/* Hide scrollbar for Chrome, Safari and Opera */
aside::-webkit-scrollbar {
  display: none;
}

/* Hide scrollbar for IE, Edge and Firefox */
aside {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}
</style>
