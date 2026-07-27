<template>
  <nav
    :class="[
      'fixed top-0 left-0 right-0 z-[900] flex flex-col transition-all duration-300',
      isScrolled ? 'bg-white/95 backdrop-blur-md border-b border-[var(--border)] shadow-sm' : 'bg-white/80 backdrop-blur-md border-b border-transparent'
    ]"
  >
    <!-- Announcement Bar -->
    <div
      v-if="storefrontStore.generalSettings?.showAnnouncement"
      :style="{
        backgroundColor: storefrontStore.generalSettings?.announcementBgColor || '#111111',
        color: storefrontStore.generalSettings?.announcementTextColor || '#ffffff'
      }"
      class="w-full py-2 px-4 text-center text-[11px] sm:text-xs font-semibold tracking-wide transition-all duration-300 overflow-hidden"
      :class="[isScrolled ? 'h-0 py-0 opacity-0' : 'h-auto opacity-100']"
    >
      <div class="container mx-auto">
        {{ storefrontStore.generalSettings?.announcementText }}
      </div>
    </div>

    <div class="w-full flex items-center h-[64px] md:h-[72px]">
      <div class="container mx-auto flex justify-between items-center h-full gap-4">
        <NuxtLink to="/" class="flex items-center shrink-0">
          <template v-if="storefrontStore.vendorProfile">
            <img
              v-if="storefrontStore.vendorProfile.logo_url"
              :src="storefrontStore.vendorProfile.logo_url"
              :alt="storefrontStore.vendorProfile.store_name"
              class="h-9 md:h-10 max-w-[140px] object-contain"
            />
            <span
              v-else
              class="text-lg md:text-xl font-semibold tracking-tight text-gray-900"
              style="font-family: var(--font-heading)"
            >
              {{ storefrontStore.vendorProfile.store_name }}
            </span>
          </template>
          <template v-else>
            <span class="text-xl font-semibold tracking-tight text-gray-900" style="font-family: var(--font-heading)">Store</span>
          </template>
        </NuxtLink>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center gap-8 h-full">
          <NuxtLink to="/" class="nav-link">Home</NuxtLink>

          <div class="relative h-full flex items-center group/nav">
            <button class="nav-link flex items-center gap-1">
              Shop
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="transition-transform duration-200 group-hover/nav:rotate-180"><path d="m6 9 6 6 6-6"/></svg>
            </button>

            <div class="absolute left-1/2 -translate-x-1/2 top-full pt-2 opacity-0 invisible group-hover/nav:opacity-100 group-hover/nav:visible transition-all duration-200 z-[100]">
              <div class="bg-white border border-[var(--border)] rounded-2xl p-6 flex gap-10 shadow-lg min-w-max max-w-[90vw]">
                <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-10 gap-y-6">
                  <div v-for="cat in storefrontStore.topCategories.slice(0, 8)" :key="cat.slug" class="flex flex-col gap-2 min-w-[140px]">
                    <NuxtLink :to="`/shop?category=${cat.slug}`" class="flex items-center gap-2.5 group/cat">
                      <span class="w-9 h-9 rounded-xl bg-[var(--muted)] flex items-center justify-center text-lg">{{ cat.icon }}</span>
                      <span class="text-sm font-semibold text-gray-900 group-hover/cat:opacity-60 transition-opacity">{{ cat.name }}</span>
                    </NuxtLink>
                    <div class="flex flex-col gap-1 pl-1">
                      <NuxtLink
                        v-for="child in cat.children"
                        :key="child.slug"
                        :to="`/shop?category=${child.slug}`"
                        class="text-sm text-[var(--muted-foreground)] hover:text-gray-900 transition-colors py-0.5"
                      >
                        {{ child.name }}
                      </NuxtLink>
                      <NuxtLink
                        v-if="!cat.children?.length"
                        :to="`/shop?category=${cat.slug}`"
                        class="text-sm text-[var(--muted-foreground)] hover:text-gray-900 transition-colors"
                      >
                        View all
                      </NuxtLink>
                    </div>
                  </div>
                </div>

                <div class="border-l border-[var(--border)] pl-8 flex flex-col gap-2 min-w-[180px]">
                  <span class="text-[11px] font-semibold uppercase tracking-wider text-[var(--muted-foreground)] mb-1">Featured</span>
                  <NuxtLink to="/shop?sort=newest" class="dropdown-item">New Arrivals</NuxtLink>
                  <NuxtLink to="/shop?sort=best_selling" class="dropdown-item">Best Sellers</NuxtLink>
                  <NuxtLink to="/shop?sale=true" class="dropdown-item text-[var(--sale)]">On Sale</NuxtLink>
                </div>
              </div>
            </div>
          </div>

          <div v-if="storefrontStore.customPages?.length" class="relative h-full flex items-center group/nav">
            <button class="nav-link flex items-center gap-1">
              Pages
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="transition-transform duration-200 group-hover/nav:rotate-180"><path d="m6 9 6 6 6-6"/></svg>
            </button>
            <div class="absolute top-full left-1/2 -translate-x-1/2 pt-2 opacity-0 invisible group-hover/nav:opacity-100 group-hover/nav:visible transition-all duration-200 z-[100]">
              <div class="bg-white border border-[var(--border)] rounded-2xl p-2 flex flex-col shadow-lg min-w-[200px]">
                <NuxtLink v-for="page in storefrontStore.customPages" :key="page.id" :to="`/pages/${page.slug}`" class="dropdown-item">
                  {{ page.title }}
                </NuxtLink>
              </div>
            </div>
          </div>

          <NuxtLink to="/about" class="nav-link">Our Story</NuxtLink>
        </div>

        <div class="hidden lg:flex flex-1 max-w-md mx-4">
          <TheGlobalSearch />
        </div>

        <div class="flex items-center gap-1 sm:gap-2">
          <button class="lg:hidden p-2.5 rounded-xl hover:bg-[var(--muted)] transition-colors text-gray-900" @click="isMobileSearchOpen = !isMobileSearchOpen" aria-label="Search">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          </button>

          <button @click="toggleWishlistDrawer" class="relative p-2.5 rounded-xl hover:bg-[var(--muted)] transition-colors text-gray-900" title="Wishlist" aria-label="Wishlist">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :class="{ 'fill-[var(--sale)] text-[var(--sale)]': wishlistCount > 0 }"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
            <span v-if="wishlistCount > 0" class="absolute top-1.5 right-1.5 bg-[var(--sale)] text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center">{{ wishlistCount }}</span>
          </button>

          <button class="relative p-2.5 rounded-xl hover:bg-[var(--muted)] transition-colors text-gray-900" @click="toggleCart" aria-label="Cart">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            <span v-if="cartCount > 0" class="absolute top-1.5 right-1.5 bg-gray-900 text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center">{{ cartCount }}</span>
          </button>

          <div class="relative" ref="profileDropdown">
            <template v-if="authStore.isAuthenticated">
              <button @click="toggleProfile" class="h-9 w-9 md:h-10 md:w-10 rounded-xl bg-[var(--muted)] flex items-center justify-center font-semibold text-xs text-gray-800 hover:bg-gray-200 transition-colors">
                {{ userInitials }}
              </button>
              <div
                :class="[
                  'absolute top-full right-0 mt-2 bg-white border border-[var(--border)] rounded-2xl p-2 flex flex-col shadow-lg z-[100] min-w-[220px] transition-all duration-200',
                  isProfileOpen ? 'opacity-100 visible translate-y-0' : 'opacity-0 invisible -translate-y-1 pointer-events-none'
                ]"
              >
                <div class="px-3 py-2.5 border-b border-[var(--border)] mb-1">
                  <div class="text-sm font-semibold text-gray-900 truncate">{{ authStore.user?.name }}</div>
                  <div class="text-xs text-[var(--muted-foreground)] truncate">{{ authStore.user?.email }}</div>
                </div>
                <NuxtLink v-for="link in accountLinks" :key="link.to" :to="link.to" @click="isProfileOpen = false" class="dropdown-item">
                  {{ link.label }}
                </NuxtLink>
                <div class="border-t border-[var(--border)] my-1"></div>
                <button @click="handleLogout" class="dropdown-item w-full text-left text-[var(--sale)] cursor-pointer">
                  Sign out
                </button>
              </div>
            </template>
            <template v-else>
              <NuxtLink to="/login" class="px-3.5 sm:px-4 py-2 rounded-xl bg-gray-900 text-white font-semibold text-xs sm:text-sm hover:opacity-90 transition-opacity">
                Login
              </NuxtLink>
            </template>
          </div>
        </div>
      </div>
    </div>

    <Transition name="search-drop">
      <div v-if="isMobileSearchOpen" class="lg:hidden border-t border-[var(--border)] bg-white px-4 py-3">
        <TheGlobalSearch @select="isMobileSearchOpen = false" />
      </div>
    </Transition>
  </nav>
</template>

<script setup lang="ts">
import { toast } from 'vue-sonner'

const { toggleCart, cartCount } = useCart()
const { wishlistCount, toggleWishlistDrawer } = useWishlist()
const authStore = useAuthStore()
const storefrontStore = useStorefrontStore()
const isScrolled = ref(false)
const isProfileOpen = ref(false)
const isMobileSearchOpen = ref(false)
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
  { to: '/account', label: 'Dashboard' },
  { to: '/account/orders', label: 'My Orders' },
  { to: '/account/wishlist', label: 'Wishlist' },
  { to: '/account/profile', label: 'Profile Settings' },
]

onMounted(async () => {
  if (storefrontStore.topCategories.length === 0) {
    await storefrontStore.fetchStorefront()
  }
  window.addEventListener('scroll', () => {
    isScrolled.value = window.scrollY > 40
  })
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.nav-link {
  @apply text-sm font-medium text-[var(--muted-foreground)] transition-colors duration-200 hover:text-gray-900;
}

.group\/nav:hover .nav-link {
  @apply text-gray-900;
}

.dropdown-item {
  @apply text-sm font-medium text-gray-600 px-3 py-2 rounded-xl transition-colors hover:bg-[var(--muted)] hover:text-gray-900;
}

.search-drop-enter-active,
.search-drop-leave-active {
  transition: all 0.2s ease;
}
.search-drop-enter-from,
.search-drop-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
</style>
