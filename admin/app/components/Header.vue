<template>
  <header class="h-20 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200/60 dark:border-slate-800/60 flex items-center justify-between px-8 sticky top-0 z-40 transition-all duration-300">
    <!-- Left - Page Context (Breadcrumbs or Title) -->
    <div class="flex items-center gap-3">
      <!-- Mobile Menu Toggle -->
      <button 
        @click="sidebar.toggle()" 
        class="lg:hidden p-2 -ml-2 text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100 focus:outline-none rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
      >
        <Menu class="w-6 h-6" />
      </button>

      <div class="h-8 w-[1px] bg-slate-200 dark:bg-slate-700 hidden sm:block"></div>
      <div class="flex items-center gap-2 text-sm font-bold text-slate-400 dark:text-slate-500">
        <span class="hover:text-slate-600 dark:hover:text-slate-300 cursor-pointer transition-colors">Admin</span>
        <ChevronRight class="w-3 h-3" />
        <span class="text-slate-900 dark:text-slate-100">{{ currentPageTitle }}</span>
      </div>
    </div>

    <!-- Right Actions -->
    <div class="flex items-center gap-3">
       <a :href="config.public.webBase" target="_blank" class="hidden md:flex items-center gap-2 px-5 py-2.5 text-xs font-black text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm active:scale-95 group">
         <ExternalLink class="w-4 h-4 text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white transition-colors" />
         VISIT SHOP
       </a>
       
       <div class="flex items-center p-1.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-2xl gap-1">
         <button class="hidden sm:block p-2 text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-white dark:hover:bg-slate-700 rounded-xl transition-all group">
            <RotateCcw class="w-5 h-5 group-hover:rotate-[-120deg] transition-transform duration-500" />
         </button>

         <NuxtLink to="/vendor/pos" target="_blank" class="hidden sm:block p-2 text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-white dark:hover:bg-slate-700 rounded-xl transition-all">
            <Monitor class="w-5 h-5" />
         </NuxtLink>

         <button class="p-2 text-slate-400 hover:text-slate-900 dark:hover:text-slate-100 hover:bg-white dark:hover:bg-slate-700 rounded-xl transition-all relative">
            <Bell class="w-5 h-5" />
            <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-indigo-500 rounded-full border-2 border-white dark:border-slate-800 animate-pulse"></span>
         </button>
       </div>

       <div class="h-8 w-[1px] bg-slate-200 dark:bg-slate-700 mx-2"></div>

       <!-- User Profile -->
       <div class="relative" ref="dropdownRef">
         <button @click="toggleDropdown" class="flex items-center gap-3 p-1.5 pl-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-2xl hover:bg-white dark:hover:bg-slate-800 transition-all shadow-sm active:scale-95 group">
            <div class="flex flex-col items-end hidden sm:flex">
              <span class="text-[10px] font-black text-slate-900 dark:text-slate-100 uppercase tracking-wider">{{ auth.user?.name || 'User' }}</span>
              <span class="text-[10px] font-bold text-indigo-500 dark:text-indigo-400 mt-0.5 uppercase tracking-[0.1em]">{{ userRoleLabel }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-900 dark:bg-indigo-600 flex items-center justify-center text-white font-black text-sm transition-all group-hover:scale-105 shadow-lg shadow-slate-900/10 dark:shadow-indigo-900/20">
               {{ userInitials }}
            </div>
         </button>

         <!-- Dropdown Menu -->
         <transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="transform scale-95 opacity-0 -translate-y-2"
            enter-to-class="transform scale-100 opacity-100 translate-y-0"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="transform scale-100 opacity-100 translate-y-0"
            leave-to-class="transform scale-95 opacity-0 -translate-y-2"
         >
           <div v-if="isDropdownOpen" class="absolute right-0 mt-3 w-64 bg-white dark:bg-slate-900 rounded-[24px] shadow-[0_20px_50px_-15px_rgba(0,0,0,0.15)] dark:shadow-[0_20px_50px_-15px_rgba(0,0,0,0.5)] border border-slate-200/60 dark:border-slate-700/60 py-2 z-50 overflow-hidden">
              <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800 mb-2">
                 <p class="text-sm font-black text-slate-900 dark:text-slate-100 truncate">{{ auth.user?.name || 'User' }}</p>
                 <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 truncate mt-0.5">{{ auth.user?.email || '' }}</p>
              </div>
              
              <NuxtLink to="/vendor/profile-settings" class="mx-2 flex items-center gap-3 px-4 py-3 text-sm font-bold text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-xl transition-all group">
                 <div class="p-2 bg-slate-100 dark:bg-slate-800 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-900/50 rounded-lg transition-colors">
                   <User class="w-4 h-4 text-slate-400 dark:text-slate-500 group-hover:text-indigo-600 dark:group-hover:text-indigo-400" />
                 </div>
                 Account Settings
              </NuxtLink>
              
              <div class="px-3 my-2">
                <div class="h-[1px] bg-slate-100 dark:bg-slate-800"></div>
              </div>

              <button @click="handleLogout" class="mx-2 w-[calc(100%-16px)] flex items-center gap-3 px-4 py-3 text-sm font-bold text-red-600 dark:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all group">
                 <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg group-hover:bg-red-100 dark:group-hover:bg-red-900/40 transition-colors border border-red-100 dark:border-red-900/30">
                  <LogOut class="w-4 h-4" />
                 </div>
                 Sign Out
              </button>
           </div>
         </transition>
       </div>
    </div>
  </header>
</template>

<script setup>
import { 
  ExternalLink,
  RotateCcw,
  Monitor,
  Bell,
  Menu,
  LogOut,
  User,
  ChevronRight
} from 'lucide-vue-next'
import { ref, computed, onMounted, onUnmounted } from 'vue'

const auth = useAuthStore()
const router = useRouter()
const route = useRoute()
const config = useRuntimeConfig()
const sidebar = useSidebar()
const isDropdownOpen = ref(false)
const dropdownRef = ref(null)

const currentPageTitle = computed(() => {
  const path = route.path
  if (path === '/') return 'Dashboard'
  const parts = path.split('/')
  const lastPart = parts[parts.length - 1]
  return lastPart.charAt(0).toUpperCase() + lastPart.slice(1).replace(/-/g, ' ')
})

const userInitials = computed(() => {
   const name = auth.user?.name || 'U'
   return name.substring(0, 1).toUpperCase()
})

const userRoleLabel = computed(() => {
   const roles = auth.user?.roles
   if (!roles?.length) return 'User'
   const roleNames = roles.map(r => r.name)
   if (roleNames.includes('super-admin')) return 'Super Admin'
   if (roleNames.includes('admin')) return 'Admin'
   if (roleNames.includes('vendor')) return 'Vendor'
   return 'User'
})

const toggleDropdown = () => {
   isDropdownOpen.value = !isDropdownOpen.value
}

const closeDropdown = (e) => {
   if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
      isDropdownOpen.value = false
   }
}

const handleLogout = async () => {
   isDropdownOpen.value = false
   await auth.logout()
}

onMounted(() => {
   document.addEventListener('click', closeDropdown)
})

onUnmounted(() => {
   document.removeEventListener('click', closeDropdown)
})
</script>

