<template>
  <aside
    :class="[
      'w-80 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-r border-slate-200/50 dark:border-slate-800/50 flex flex-col h-screen fixed left-0 top-0 z-50 transition-all duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)] shadow-[10px_0_40px_-15px_rgba(0,0,0,0.03)] dark:shadow-none',
      sidebar.isOpen.value ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
    ]"
  >
    <!-- Logo Area -->
    <div class="h-28 flex items-center px-10 mb-2 relative overflow-hidden group">
      <div class="absolute -left-4 top-0 w-32 h-32 bg-indigo-500/10 dark:bg-indigo-500/5 blur-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-1000"></div>
      <div class="flex items-center gap-4 relative z-10 cursor-pointer" @click="$router.push(homeRoute)">
        <div class="w-12 h-12 text-white rounded-[18px] flex items-center justify-center shadow-xl transform transition-all duration-700 group-hover:rotate-[360deg] group-hover:scale-110"
          :class="isSuperAdmin ? 'bg-violet-600 shadow-violet-900/30' : 'bg-slate-900 dark:bg-indigo-600 shadow-slate-950/20 dark:shadow-indigo-900/30'">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7"><path d="M7 20l10-16l-5 16l-5-16"></path></svg>
        </div>
        <div>
          <span class="font-[1000] text-2xl tracking-[-0.04em] text-slate-900 dark:text-white leading-none">Sells Emu</span>
          <div class="flex items-center gap-1.5 mt-1 px-2 py-0.5 rounded-full w-fit border"
            :class="isSuperAdmin
              ? 'border-violet-500/20 bg-violet-500/5'
              : 'border-emerald-500/20 bg-emerald-500/5'">
            <span class="w-1.5 h-1.5 rounded-full animate-pulse"
              :class="isSuperAdmin ? 'bg-violet-500' : 'bg-emerald-500'"></span>
            <span class="text-[9px] font-black uppercase tracking-[0.15em]"
              :class="isSuperAdmin ? 'text-violet-600 dark:text-violet-400' : 'text-emerald-600 dark:text-emerald-400'">
              {{ vendorPackageName || roleBadge }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- Navigation Area -->
    <div class="flex-1 overflow-y-auto custom-scrollbar px-6 pb-10">

      <!-- SUPER ADMIN NAVIGATION -->
      <template v-if="isSuperAdmin">
        <!-- Dashboard & Users -->
        <div class="space-y-1 mb-6">
          <div class="nav-section-label">
            <div class="w-4 h-[2px] bg-gradient-to-r from-violet-500 to-indigo-500 rounded-full"></div>
            <span class="text-[10px] font-black text-violet-500 dark:text-violet-400 uppercase tracking-[0.25em]">Super Admin</span>
          </div>
          
          <NuxtLink to="/admin" class="nav-link group" :class="{ 'nav-link-active': $route.path === '/admin' }">
            <div class="nav-link-icon-bg !bg-violet-50 dark:!bg-violet-500/10 !border-violet-100 dark:!border-violet-500/20 group-hover:!bg-violet-600 group-hover:border-transparent">
              <Shield class="w-5 h-5 !text-violet-600 dark:!text-violet-400 group-hover:!text-white" />
            </div>
            <span class="nav-link-text">Dashboard</span>
            <div v-if="$route.path === '/admin'" class="nav-link-active-indicator"></div>
          </NuxtLink>

          <NuxtLink to="/admin/users" class="nav-link group" active-class="nav-link-active">
            <div class="nav-link-icon-bg group-hover:bg-slate-950 dark:group-hover:bg-indigo-500/20 group-hover:border-transparent">
              <Users class="w-5 h-5 nav-link-icon" />
            </div>
            <span class="nav-link-text">Users</span>
          </NuxtLink>

          <NuxtLink to="/admin/plans" class="nav-link group" active-class="nav-link-active">
            <div class="nav-link-icon-bg group-hover:bg-slate-950 dark:group-hover:bg-indigo-500/20 group-hover:border-transparent">
              <PackagePlus class="w-5 h-5 nav-link-icon" />
            </div>
            <span class="nav-link-text">Plans</span>
          </NuxtLink>

          <NuxtLink to="/admin/vendors" class="nav-link group" active-class="nav-link-active">
            <div class="nav-link-icon-bg group-hover:bg-slate-950 dark:group-hover:bg-indigo-500/20 group-hover:border-transparent">
              <Building2 class="w-5 h-5 nav-link-icon" />
            </div>
            <span class="nav-link-text">Vendors</span>
          </NuxtLink>
        </div>

        <!-- Finance -->
        <div class="space-y-1 mb-6">
          <div class="nav-section-label">
            <div class="w-4 h-[2px] bg-slate-200 dark:bg-slate-800 rounded-full"></div>
            <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.25em]">Finance</span>
          </div>
          
          <NuxtLink to="/admin/payments" class="nav-link group" active-class="nav-link-active">
            <div class="nav-link-icon-bg group-hover:bg-slate-950 dark:group-hover:bg-indigo-500/20 group-hover:border-transparent">
              <CreditCard class="w-5 h-5 nav-link-icon" />
            </div>
            <span class="nav-link-text">Payments</span>
          </NuxtLink>

          <NuxtLink to="/admin/subscriptions" class="nav-link group" active-class="nav-link-active">
            <div class="nav-link-icon-bg group-hover:bg-slate-950 dark:group-hover:bg-indigo-500/20 group-hover:border-transparent">
              <Zap class="w-5 h-5 nav-link-icon" />
            </div>
            <span class="nav-link-text">Subscriptions</span>
          </NuxtLink>

          <NuxtLink to="/admin/transactions" class="nav-link group" active-class="nav-link-active">
            <div class="nav-link-icon-bg group-hover:bg-slate-950 dark:group-hover:bg-indigo-500/20 group-hover:border-transparent">
              <ArrowUpDown class="w-5 h-5 nav-link-icon" />
            </div>
            <span class="nav-link-text">Transactions</span>
          </NuxtLink>
        </div>

        <!-- System Settings -->
        <div class="space-y-1 mb-6">
          <div class="nav-section-label">
            <div class="w-4 h-[2px] bg-slate-200 dark:bg-slate-800 rounded-full"></div>
            <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.25em]">System</span>
          </div>

          <div class="space-y-1 mb-1">
            <button @click="toggleAdminSettingsMenu" class="nav-dropdown-trigger group" :class="{ 'nav-dropdown-active': isAdminSettingsOpen || isAdminSettingsActive }">
              <div class="flex items-center">
                <div class="nav-link-icon-bg group-hover:bg-slate-950 dark:group-hover:bg-indigo-500/20 group-hover:border-transparent">
                  <Settings class="w-5 h-5 nav-link-icon" />
                </div>
                <span class="nav-link-text">Settings</span>
              </div>
              <ChevronDown class="w-4 h-4 transition-transform duration-500" :class="{ 'rotate-180': isAdminSettingsOpen }" />
            </button>
            <div class="grid transition-all duration-500 ease-in-out overflow-hidden" :class="isAdminSettingsOpen ? 'grid-rows-[1fr] opacity-100 mt-2' : 'grid-rows-[0fr] opacity-0 pointer-events-none'">
              <div class="min-h-0 space-y-1 pl-10 pr-2">
                <NuxtLink to="/admin/settings/general" class="sub-nav-link group/sub" active-class="sub-nav-link-active">
                  <div class="indicator w-1.5 h-1.5 rounded-full bg-slate-200 dark:bg-slate-700 transition-all group-hover/sub:bg-indigo-500"></div>
                  <span class="flex-1">General</span>
                </NuxtLink>
                <NuxtLink to="/admin/settings/appearance" class="sub-nav-link group/sub" active-class="sub-nav-link-active">
                  <div class="indicator w-1.5 h-1.5 rounded-full bg-slate-200 dark:bg-slate-700 transition-all group-hover/sub:bg-indigo-500"></div>
                  <span class="flex-1">Appearance</span>
                </NuxtLink>
                <NuxtLink to="/admin/settings/payments" class="sub-nav-link group/sub" active-class="sub-nav-link-active">
                  <div class="indicator w-1.5 h-1.5 rounded-full bg-slate-200 dark:bg-slate-700 transition-all group-hover/sub:bg-indigo-500"></div>
                  <span class="flex-1">Payments</span>
                </NuxtLink>
                <NuxtLink to="/admin/settings/mail" class="sub-nav-link group/sub" active-class="sub-nav-link-active">
                  <div class="indicator w-1.5 h-1.5 rounded-full bg-slate-200 dark:bg-slate-700 transition-all group-hover/sub:bg-indigo-500"></div>
                  <span class="flex-1">Mail Config</span>
                </NuxtLink>
                <NuxtLink to="/admin/settings/advanced" class="sub-nav-link group/sub" active-class="sub-nav-link-active">
                  <div class="indicator w-1.5 h-1.5 rounded-full bg-slate-200 dark:bg-slate-700 transition-all group-hover/sub:bg-indigo-500"></div>
                  <span class="flex-1">Advanced</span>
                </NuxtLink>
              </div>
            </div>
          </div>
        </div>
      </template>

      <!-- VENDOR NAVIGATION -->
      <template v-if="isVendor">
        <div class="nav-section-label mt-8">
           <div class="w-4 h-[2px] bg-indigo-500 rounded-full"></div>
           <span class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.25em]">Vendor Panel</span>
        </div>

        <!-- Dashboard -->
        <div class="space-y-1 mb-6">
          <NuxtLink to="/vendor" class="nav-link group" :class="{ 'nav-link-active': $route.path === '/vendor' }">
            <div class="nav-link-icon-bg group-hover:bg-slate-950 dark:group-hover:bg-indigo-500/20 group-hover:border-transparent">
              <LayoutDashboard class="w-5 h-5 nav-link-icon" />
            </div>
            <span class="nav-link-text">Overview</span>
            <div v-if="$route.path === '/vendor'" class="nav-link-active-indicator"></div>
          </NuxtLink>
        </div>

        <!-- Assets Management -->
        <div class="space-y-1 mb-6">
          <div class="nav-section-label">
            <div class="w-4 h-[2px] bg-slate-200 dark:bg-slate-800 rounded-full"></div>
            <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.25em]">Assets</span>
          </div>

          <!-- Attributes Accordion -->
          <div class="space-y-1 mb-1">
            <button @click="toggleAttributesMenu" class="nav-dropdown-trigger group" :class="{ 'nav-dropdown-active': isAttributesOpen || isAttributesActive }">
              <div class="flex items-center">
                <div class="nav-link-icon-bg group-hover:bg-slate-950 dark:group-hover:bg-indigo-500/20 group-hover:border-transparent">
                  <Grid class="w-5 h-5 nav-link-icon" />
                </div>
                <span class="nav-link-text">Attributes</span>
              </div>
              <ChevronDown class="w-4 h-4 transition-transform duration-500" :class="{ 'rotate-180': isAttributesOpen }" />
            </button>
            <div class="grid transition-all duration-500 ease-in-out overflow-hidden" :class="isAttributesOpen ? 'grid-rows-[1fr] opacity-100 mt-2' : 'grid-rows-[0fr] opacity-0 pointer-events-none'">
              <div class="min-h-0 space-y-1 pl-10 pr-2">
                <NuxtLink v-for="link in visibleAttributeLinks" :key="link.to" :to="link.to" class="sub-nav-link group/sub" active-class="sub-nav-link-active">
                  <div class="w-1.5 h-1.5 rounded-full bg-slate-200 dark:bg-slate-700 indicator transition-all group-hover/sub:bg-indigo-500"></div>
                  <span class="flex-1">{{ link.label }}</span>
                </NuxtLink>
              </div>
            </div>
          </div>

          <!-- Products Accordion -->
          <div class="space-y-1 mb-1">
            <button @click="toggleProductsMenu" class="nav-dropdown-trigger group" :class="{ 'nav-dropdown-active': isProductsOpen || isProductsActive }">
              <div class="flex items-center">
                <div class="nav-link-icon-bg group-hover:bg-slate-950 dark:group-hover:bg-indigo-500/20 group-hover:border-transparent">
                  <ShoppingBag class="w-5 h-5 nav-link-icon" />
                </div>
                <span class="nav-link-text">Products</span>
              </div>
              <ChevronDown class="w-4 h-4 transition-transform duration-500" :class="{ 'rotate-180': isProductsOpen }" />
            </button>
            <div class="grid transition-all duration-500 ease-in-out overflow-hidden" :class="isProductsOpen ? 'grid-rows-[1fr] opacity-100 mt-2' : 'grid-rows-[0fr] opacity-0 pointer-events-none'">
              <div class="min-h-0 space-y-1 pl-10 pr-2 relative">
                <div class="absolute left-[20px] top-0 bottom-4 w-[1px] bg-slate-100 dark:bg-slate-800/60 rounded-full"></div>
                <NuxtLink v-for="link in productLinks" :key="link.to" :to="link.to" @click="handleProductLinkClick(link, $event)" class="sub-nav-link group/sub" active-class="sub-nav-link-active">
                  <div class="w-1.5 h-1.5 rounded-full bg-slate-200 dark:bg-slate-700 indicator transition-all group-hover/sub:bg-indigo-500"></div>
                  <span class="flex-1">{{ link.label }}</span>
                  <component :is="link.icon" class="w-3 h-3 opacity-0 group-hover/sub:opacity-100 transition-opacity" />
                </NuxtLink>
              </div>
            </div>
          </div>
        </div>

        <!-- Operations -->
        <div class="space-y-1 mb-6">
          <div class="nav-section-label">
            <div class="w-4 h-[2px] bg-slate-200 dark:bg-slate-800 rounded-full"></div>
            <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.25em]">Operations</span>
          </div>
          <template v-for="item in operationsItems" :key="item.to">
            <NuxtLink :to="item.to" class="nav-link group" active-class="nav-link-active">
              <div class="nav-link-icon-bg group-hover:bg-slate-950 dark:group-hover:bg-indigo-500/20 group-hover:border-transparent">
                <component :is="item.icon" class="w-5 h-5 nav-link-icon transition-transform duration-300" />
              </div>
              <span class="nav-link-text">{{ item.label }}</span>
              <div v-if="item.isPro && !hasFeature(item.feature)" class="pro-badge">Pro</div>
              <div v-if="isActive(item.to)" class="nav-link-active-indicator"></div>
            </NuxtLink>
          </template>
        </div>

        <!-- Analytics & Tools -->
        <div class="space-y-1 mb-6">
          <div class="nav-section-label">
            <div class="w-4 h-[2px] bg-slate-200 dark:bg-slate-800 rounded-full"></div>
            <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.25em]">Tools</span>
          </div>
          <template v-for="item in toolItems" :key="item.to">
            <NuxtLink :to="item.to" class="nav-link group" active-class="nav-link-active">
              <div class="nav-link-icon-bg group-hover:bg-slate-950 dark:group-hover:bg-indigo-500/20 group-hover:border-transparent">
                <component :is="item.icon" class="w-5 h-5 nav-link-icon" />
              </div>
              <span class="nav-link-text">{{ item.label }}</span>
              <div v-if="item.isPro && !hasFeature(item.feature)" class="pro-badge">Pro</div>
              <div v-if="isActive(item.to)" class="nav-link-active-indicator"></div>
            </NuxtLink>
          </template>
        </div>

        <!-- Settings & Admin -->
        <div class="space-y-1 mb-6">
          <div class="nav-section-label">
            <div class="w-4 h-[2px] bg-slate-200 dark:bg-slate-800 rounded-full"></div>
            <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.25em]">Settings</span>
          </div>
          <template v-for="item in settingsItems" :key="item.to">
            <NuxtLink :to="item.to" class="nav-link group" active-class="nav-link-active">
              <div class="nav-link-icon-bg group-hover:bg-slate-950 dark:group-hover:bg-indigo-500/20 group-hover:border-transparent">
                <component :is="item.icon" class="w-5 h-5 nav-link-icon" />
              </div>
              <span class="nav-link-text">{{ item.label }}</span>
              <div v-if="item.isPro" class="pro-badge">Pro</div>
              <div v-if="isActive(item.to)" class="nav-link-active-indicator"></div>
            </NuxtLink>
          </template>
        </div>

        <!-- Support -->
        <div class="space-y-1">
          <div class="nav-section-label">
            <div class="w-4 h-[2px] bg-slate-200 dark:bg-slate-800 rounded-full"></div>
            <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.25em]">Support</span>
          </div>
          <NuxtLink to="/vendor/upgrade-package" class="nav-link support-link-upgrade group" active-class="nav-link-active">
            <div class="nav-link-icon-bg bg-emerald-50 dark:bg-emerald-500/10 group-hover:bg-emerald-500/20">
              <Sparkles class="w-5 h-5 text-emerald-500" />
            </div>
            <span class="nav-link-text">Upgrade Plan</span>
          </NuxtLink>
          <NuxtLink to="/vendor/help-center" class="nav-link group" active-class="nav-link-active">
            <div class="nav-link-icon-bg group-hover:bg-slate-950 dark:group-hover:bg-indigo-500/20 group-hover:border-transparent">
              <Headphones class="w-5 h-5 nav-link-icon" />
            </div>
            <span class="nav-link-text">Help Center</span>
          </NuxtLink>
        </div>
      </template>

    </div>

    <!-- Bottom Controls -->
    <div class="p-8 border-t border-slate-100 dark:border-slate-800/60 transition-colors">
      <!-- User Info -->
      <div class="flex items-center gap-3 mb-4 px-1">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-black text-sm shadow-md flex-shrink-0"
          :class="isSuperAdmin ? 'bg-violet-600' : 'bg-gradient-to-br from-indigo-500 to-violet-500'">
          {{ (auth.user?.name || 'U').charAt(0).toUpperCase() }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-xs font-black text-slate-900 dark:text-white truncate">{{ auth.user?.name || 'User' }}</p>
          <p class="text-[10px] text-slate-400 truncate">{{ currentRoleName }}</p>
        </div>
      </div>

      <div class="flex items-center justify-between gap-3 p-1.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200/50 dark:border-slate-700/60 rounded-[20px] shadow-sm">
        <button @click="toggleColorMode" class="bottom-control-btn group" title="Toggle Theme">
          <component :is="isDark ? Sun : Moon" class="w-4 h-4 transition-transform group-active:scale-90" />
        </button>
        <div class="h-6 w-[1px] bg-slate-200 dark:bg-slate-700/60"></div>
        <button class="bottom-control-btn group" title="Language">
          <Globe class="w-4 h-4 transition-transform group-hover:rotate-12" />
        </button>
        <div class="h-6 w-[1px] bg-slate-200 dark:bg-slate-700/60"></div>
        <button @click="auth.logout()" class="bottom-control-btn group text-rose-400 hover:text-white hover:!bg-rose-500 hover:!border-rose-500" title="Logout">
          <LogOut class="w-4 h-4" />
        </button>
      </div>
    </div>
  </aside>
</template>

<script setup>
import {
  LayoutDashboard,
  Grid,
  ShoppingBag,
  LayoutTemplate,
  Users,
  UserPlus,
  ClipboardList,
  Store,
  Globe,
  Tag,
  Gift,
  Briefcase,
  BarChart3,
  UserCheck,
  SearchCheck,
  Headphones,
  ChevronDown,
  Sun,
  Moon,
  Plus,
  Box,
  FileText,
  Layers,
  ArrowUpDown,
  Award,
  Scale,
  Sparkles,
  Shield,
  PackagePlus,
  Building2,
  LogOut,
  Monitor,
  Truck,
  CreditCard,
  Zap,
  Settings,
} from 'lucide-vue-next'

import { toast } from 'vue-sonner'

const { can, hasRole, hasFeature } = usePermissions()

const route = useRoute()
const router = useRouter()
const sidebar = useSidebar()
const colorMode = useColorMode()
const auth = useAuthStore()
const { getAll } = useCrud()

const handleProductLinkClick = async (link, event) => {
  if (link.to === '/vendor/products/create') {
    const user = auth.user
    const packageDetails = user?.vendor_profile?.package || user?.owner?.vendorProfile?.package || user?.vendorProfile?.package
    const limit = packageDetails?.product_limit
    
    if (limit !== undefined && limit !== -1) {
       event.preventDefault()
       try {
         const productsRes = await getAll('/vendor/products?per_page=1')
         const total = productsRes?.total ?? productsRes?.data?.length ?? 0
         
         if (total >= limit) {
           toast.error(`Product limit reached (${total}/${limit}). Please upgrade your plan.`, {
             description: 'You cannot add more products with your current subscription.'
           })
           return 
         }
         router.push(link.to)
       } catch (e) {
         console.error(e)
         router.push(link.to)
       }
    }
  }
}

// ─── Role helpers ─────────────────────────────────────────────────────────────
const isSuperAdmin = computed(() => auth.user?.roles?.some(r => r.name === 'super-admin') ?? false)
const isVendor      = computed(() => (auth.user?.roles?.some(r => r.name === 'vendor') || !!auth.user?.vendor_profile || !!auth.user?.vendor_id) ?? false)
const isVendorOwner = computed(() => !!auth.user?.vendor_profile && !auth.user?.vendor_id)
const isVendorStaff = computed(() => !!auth.user?.vendor_id)
const isAdmin       = computed(() => auth.user?.roles?.some(r => r.name === 'admin') ?? false)

const roleBadge = computed(() => {
  if (isSuperAdmin.value) return 'Super Admin'
  if (isVendorOwner.value) return 'Vendor'
  if (isVendorStaff.value) return auth.user?.roles?.[0]?.name || 'Staff'
  if (isAdmin.value) return 'Admin'
  return 'User'
})

const currentRoleName = computed(() => {
  if (isVendorOwner.value && auth.user?.vendor_profile?.package) {
    return `${auth.user.vendor_profile.package.name} Plan`
  }
  const roles = auth.user?.roles
  if (!roles?.length) {
    return isVendorOwner.value ? 'Vendor' : (isVendorStaff.value ? 'Staff' : 'No role')
  }
  return roles.map((r) => r.name).join(', ')
})

const homeRoute = computed(() => {
  if (isSuperAdmin.value || isAdmin.value) return '/admin'
  return '/vendor'
})

const vendorPackageName = computed(() => {
  const pkg = auth.user?.vendor_profile?.package || auth.user?.owner?.vendor_profile?.package
  return pkg?.name || null
})


// ─── Accordion state ──────────────────────────────────────────────────────────
const isProductsOpen      = ref(false)
const isAttributesOpen    = ref(false)
const isAdminSettingsOpen = ref(false)

const isAttributesActive    = computed(() => route.path.includes('/vendor/attributes'))
const isProductsActive      = computed(() => route.path.includes('/vendor/products'))
const isAdminSettingsActive = computed(() => route.path.includes('/admin/settings'))

const isDark = computed(() => colorMode.value === 'dark')

const toggleColorMode = () => {
  colorMode.preference = colorMode.value === 'dark' ? 'light' : 'dark'
}
const isActive = (path) => route.path === path

onMounted(() => {
  if (route.path.includes('/vendor/attributes')) isAttributesOpen.value = true
  if (route.path.includes('/vendor/products'))   isProductsOpen.value   = true
  if (route.path.includes('/admin/settings'))    isAdminSettingsOpen.value = true
})

watch(() => route.path, () => {
  sidebar.close()
  // also auto-open on navigate
  if (route.path.includes('/vendor/attributes')) isAttributesOpen.value = true
  if (route.path.includes('/vendor/products'))   isProductsOpen.value   = true
  if (route.path.includes('/admin/settings'))    isAdminSettingsOpen.value = true
})

const toggleProductsMenu      = () => { isProductsOpen.value   = !isProductsOpen.value }
const toggleAttributesMenu    = () => { isAttributesOpen.value = !isAttributesOpen.value }
const toggleAdminSettingsMenu = () => { isAdminSettingsOpen.value = !isAdminSettingsOpen.value }

// ─── Filter Helpers ──────────────────────────────────────────────────────────
const filterItems = (items) => {
  return items.filter(item => {
    // 1. Check feature first if it exists (SaaS package logic)
    if (item.feature && !hasFeature(item.feature)) return false
    
    // 2. Check individual permissions
    if (!item.permission) return true
    return can(item.permission)
  })
}

// ─── Nav item definitions ─────────────────────────────────────────────────────
const attributeLinks = [
  { to: '/vendor/attributes/categories', label: 'Categories', permission: 'attributes.view', feature: 'Products' },
  { to: '/vendor/attributes/brands',     label: 'Brands',     permission: 'brands.view',     feature: 'Products' },
  { to: '/vendor/attributes/units',      label: 'Units',      permission: 'units.view',      feature: 'Products' },
  { to: '/vendor/attributes/sorting',    label: 'Sorting',    permission: 'attributes.sort', feature: 'Products' },
]

const visibleAttributeLinks = computed(() => filterItems(attributeLinks))

const rawProductLinks = [
  { to: '/vendor/products/create',   label: 'Add Product',  icon: Plus,     permission: 'products.create', feature: 'Products' },
  { to: '/vendor/products',          label: 'All Products', icon: Box,      permission: 'products.view',   feature: 'Products' },
  { to: '/vendor/products/published',label: 'Published',    icon: FileText, permission: 'products.view',   feature: 'Products' },
  { to: '/vendor/products/pending',  label: 'Pending',      icon: FileText, permission: 'products.view',   feature: 'Products' },
  { to: '/vendor/products/draft',    label: 'Drafts',       icon: FileText, permission: 'products.view',   feature: 'Products' },
]

const productLinks = computed(() => filterItems(rawProductLinks))

const rawOperationsItems = [
  { to: '/vendor/orders',        label: 'Orders',          icon: ClipboardList, permission: 'orders.view',   feature: 'Orders' },
  { to: '/vendor/customers',     label: 'Customers',       icon: Users,         permission: 'customers.view', feature: 'Customers' },
  { to: '/vendor/pos',           label: 'Point of Sale',   icon: Monitor,       permission: 'pos.view',       feature: 'pos',          isPro: true },
  { to: '/vendor/coupon-code',   label: 'Coupon Code',     icon: Tag,           permission: 'coupons.view',   feature: 'Products' },
  { to: '/vendor/promotions',    label: 'Promotions',      icon: Gift,          permission: 'promotions.view',feature: 'Promotions',   isPro: true },
  { to: '/vendor/fraud-check',   label: 'AI Fraud Check',  icon: SearchCheck,   permission: 'fraud_check.view',feature: 'Fraud Check', isPro: true },
  { to: '/vendor/landing-page',  label: 'Landing Page',    icon: LayoutTemplate,permission: 'landing_pages.view',feature: 'Landing Pages', isPro: true },
]

const operationsItems = computed(() => filterItems(rawOperationsItems))

const rawToolItems = [
  { to: '/vendor/hrm',             label: 'HRM Suite',         icon: Briefcase, permission: 'hrm.dashboard.view', feature: 'hrm', isPro: true },
  { to: '/vendor/reports',         label: 'Analytics',         icon: BarChart3, permission: 'reports.view',       feature: 'Reports' },
  { to: '/vendor/managed-shop',    label: 'Manage Shop',       icon: Store,     permission: 'settings.view' },
  { to: '/vendor/managed-website', label: 'Website Settings',  icon: Globe,     permission: 'settings.view' },
]

const toolItems = computed(() => filterItems(rawToolItems))

const rawSettingsItems = [
  { to: '/vendor/staff',            label: 'Manage Staff',    icon: UserPlus,  permission: 'staff.view' },
  { to: '/vendor/role-permissions', label: 'Permissions',     icon: UserCheck, isPro: true, permission: 'roles.view' },
]

const settingsItems = computed(() => filterItems(rawSettingsItems))
</script>

<style scoped>
.nav-section-label {
  @apply flex items-center gap-2 px-4 mb-3;
}

/* Navigation Utility Classes */
.nav-link {
  @apply flex items-center px-4 py-3 text-sm font-bold text-slate-500 dark:text-slate-400 rounded-[18px] transition-all duration-300 relative;
}
.nav-link:hover {
  @apply bg-slate-900 dark:bg-indigo-600 text-white shadow-xl shadow-slate-900/10 dark:shadow-indigo-600/20 translate-x-1;
}
.nav-link-active {
  @apply bg-slate-900 dark:bg-indigo-600 text-white shadow-xl shadow-slate-900/10 dark:shadow-indigo-600/20 translate-x-1;
}

.nav-link-icon-bg {
  @apply w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-800 transition-all duration-300 mr-4;
}
.nav-link:hover .nav-link-icon-bg, .nav-link-active .nav-link-icon-bg {
  @apply bg-white/10 border-transparent scale-90;
}

.nav-link-icon {
  @apply text-slate-400 dark:text-slate-500 transition-colors duration-300;
}
.nav-link:hover .nav-link-icon, .nav-link-active .nav-link-icon {
  @apply text-white;
}

.nav-link-text {
  @apply flex-1 transition-all duration-300;
}

/* Accordion Trigger Styles */
.nav-dropdown-trigger {
  @apply w-full flex items-center justify-between px-4 py-3 text-sm font-bold text-slate-500 dark:text-slate-400 rounded-[18px] transition-all duration-300;
}
.nav-dropdown-trigger:hover {
  @apply bg-slate-50 dark:bg-slate-800/80 text-slate-900 dark:text-white;
}
.nav-dropdown-active {
  @apply bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm ring-1 ring-slate-100 dark:ring-slate-800/60;
}

/* Sub Navigation Links */
.sub-nav-link {
  @apply flex items-center gap-3 py-3 px-4 text-[11px] font-[1000] text-slate-400 dark:text-slate-500 uppercase tracking-widest rounded-xl transition-all duration-300 border border-transparent hover:bg-white dark:hover:bg-slate-900 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-slate-100 dark:hover:border-slate-800;
}
.sub-nav-link-active {
  @apply bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 border-slate-100 dark:border-slate-800 shadow-sm;
}
.sub-nav-link-active .indicator {
  @apply bg-indigo-600 dark:bg-indigo-400 scale-150 shadow-[0_0_8px_rgba(99,102,241,0.5)];
}

/* Pro Badge Styles */
.pro-badge {
  @apply bg-gradient-to-br from-amber-400 to-orange-500 text-white text-[8px] font-black px-2 py-0.5 rounded-lg uppercase tracking-tighter shadow-sm ring-1 ring-white/20;
}

/* Active Indicator */
.nav-link-active-indicator {
  @apply absolute -left-2 top-1/2 -translate-y-1/2 w-1.5 h-6 bg-white rounded-full transition-all;
}

/* Support and Help Styles */
.support-link-upgrade {
  @apply hover:bg-emerald-600 dark:hover:bg-emerald-500;
}

/* Bottom Controls */
.bottom-control-btn {
  @apply p-2.5 text-slate-400 dark:text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-white dark:hover:bg-slate-800 rounded-xl transition-all shadow-none hover:shadow-sm border border-transparent hover:border-slate-100 dark:hover:border-slate-700/60;
}

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb {
  @apply bg-slate-200 dark:bg-slate-800/80 rounded-full transition-colors;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  @apply bg-slate-300 dark:bg-slate-700;
}

@supports not (backdrop-filter: blur(20px)) {
  aside { @apply bg-white dark:bg-slate-900; }
}
</style>
