<template>
  <aside :class="[
    'fixed top-0 left-0 z-50 w-[240px] h-screen flex flex-col transition-transform duration-300 ease-in-out',
    'bg-white dark:bg-[#0d1117]',
    'border-r border-gray-100 dark:border-white/[0.06]',
    sidebar.isOpen.value ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
  ]">

    <!-- Logo Header -->
    <div
      class="flex items-center gap-3 px-4 h-[60px] border-b border-gray-100 dark:border-white/[0.06] cursor-pointer flex-shrink-0 group"
      @click="$router.push(homeRoute)"
    >
      <div :class="[
        'w-8 h-8 rounded-xl flex items-center justify-center text-white flex-shrink-0 shadow-sm',
        isSuperAdmin
          ? 'bg-gradient-to-br from-violet-500 to-purple-600'
          : 'bg-gradient-to-br from-indigo-500 to-blue-600'
      ]">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
          stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M7 20l10-16l-5 16l-5-16"></path>
        </svg>
      </div>
      <div class="flex flex-col gap-0.5 min-w-0">
        <span class="text-[14px] font-bold text-gray-900 dark:text-white tracking-tight leading-none group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Sells Emu</span>
        <span :class="[
          'text-[10px] font-semibold px-1.5 py-0.5 rounded-md inline-block leading-snug w-fit',
          isSuperAdmin
            ? 'bg-violet-100 text-violet-600 dark:bg-violet-500/10 dark:text-violet-400'
            : 'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400'
        ]">{{ vendorPackageName || roleBadge }}</span>
      </div>
    </div>

    <!-- Search Bar -->
    <div class="px-3 pt-3 pb-1 flex-shrink-0">
      <div class="search-wrap">
        <Search :size="13" class="search-icon" />
        <input
          ref="searchInputRef"
          v-model="searchQuery"
          type="text"
          name="sidebar-search"
          autocomplete="off"
          placeholder="Search menu..."
          class="search-input"
          @keydown.escape="clearSearch"
        />
        <button
          v-if="searchQuery"
          @click="clearSearch"
          class="search-clear"
          title="Clear"
        >
          <X :size="11" />
        </button>
      </div>
    </div>

    <!-- Nav -->
    <nav class="sidebar-nav flex-1 overflow-y-auto px-3 py-2 space-y-0.5">

      <!-- ── SEARCH RESULTS ─────────────────────── -->
      <template v-if="searchQuery.trim()">
        <template v-if="searchResults.length > 0">
          <p class="nav-label pt-1">{{ searchResults.length }} result{{ searchResults.length !== 1 ? 's' : '' }}</p>
          <NuxtLink
            v-for="item in searchResults"
            :key="item.to"
            :to="item.to"
            class="nav-item"
            active-class="nav-item-active"
            @click="handleSearchItemClick(item, $event)"
          >
            <span class="nav-icon">
              <component :is="item.icon" :size="14" />
            </span>
            <span class="flex-1 min-w-0">
              <!-- Highlighted label -->
              <span v-html="highlightMatch(item.label, searchQuery)" />
              <span v-if="item.section" class="block text-[10px] text-gray-400 dark:text-gray-600 leading-none mt-0.5 font-normal">{{ item.section }}</span>
            </span>
            <span v-if="item.isPro && !hasFeature(item.feature)" class="pro-badge">Pro</span>
          </NuxtLink>
        </template>

        <!-- Empty State -->
        <div v-else class="search-empty">
          <div class="search-empty-icon">
            <SearchX :size="18" class="text-gray-400 dark:text-gray-600" />
          </div>
          <p class="search-empty-text">No results for <strong>"{{ searchQuery }}"</strong></p>
          <button @click="clearSearch" class="search-empty-btn">Clear search</button>
        </div>
      </template>

      <!-- ── NORMAL NAV ─────────────────────────── -->
      <template v-else>

        <!-- SUPER ADMIN -->
        <template v-if="isSuperAdmin">
          <div class="mb-2">
            <p class="nav-label nav-label-violet">Super Admin</p>
            <NuxtLink to="/admin"       class="nav-item" :class="{ 'nav-item-active': $route.path === '/admin' }"><span class="nav-icon nav-icon-violet"><Shield :size="14" /></span>Dashboard</NuxtLink>
            <NuxtLink to="/admin/users"   class="nav-item" active-class="nav-item-active"><span class="nav-icon"><Users :size="14" /></span>Users</NuxtLink>
            <NuxtLink to="/admin/plans"   class="nav-item" active-class="nav-item-active"><span class="nav-icon"><PackagePlus :size="14" /></span>Plans</NuxtLink>
            <NuxtLink to="/admin/vendors" class="nav-item" active-class="nav-item-active"><span class="nav-icon"><Building2 :size="14" /></span>Vendors</NuxtLink>
          </div>

          <div class="mb-2">
            <p class="nav-label">Finance</p>
            <NuxtLink to="/admin/payments"      class="nav-item" active-class="nav-item-active"><span class="nav-icon"><CreditCard :size="14" /></span>Payments</NuxtLink>
            <NuxtLink to="/admin/subscriptions" class="nav-item" active-class="nav-item-active"><span class="nav-icon"><Zap :size="14" /></span>Subscriptions</NuxtLink>
            <NuxtLink to="/admin/transactions"  class="nav-item" active-class="nav-item-active"><span class="nav-icon"><ArrowUpDown :size="14" /></span>Transactions</NuxtLink>
          </div>

          <div class="mb-2">
            <p class="nav-label">System</p>
            <button @click="toggleAdminSettingsMenu" :class="['nav-item w-full', (isAdminSettingsOpen || isAdminSettingsActive) ? 'nav-item-active' : '']">
              <span class="nav-icon"><Settings :size="14" /></span>
              Settings
              <ChevronDown :size="12" :class="['ml-auto text-gray-400 dark:text-gray-600 transition-transform duration-300 flex-shrink-0', isAdminSettingsOpen ? 'rotate-180' : '']" />
            </button>
            <div class="nav-children" :class="{ 'nav-children-open': isAdminSettingsOpen }">
              <NuxtLink to="/admin/settings/general"    class="nav-child" active-class="nav-child-active">General</NuxtLink>
              <NuxtLink to="/admin/settings/appearance" class="nav-child" active-class="nav-child-active">Appearance</NuxtLink>
              <NuxtLink to="/admin/settings/payments"   class="nav-child" active-class="nav-child-active">Payments</NuxtLink>
              <NuxtLink to="/admin/settings/mail"       class="nav-child" active-class="nav-child-active">Mail Config</NuxtLink>
              <NuxtLink to="/admin/settings/advanced"   class="nav-child" active-class="nav-child-active">Advanced</NuxtLink>
            </div>
          </div>
        </template>

        <!-- VENDOR -->
        <template v-if="isVendor">
          <div class="mb-2">
            <p class="nav-label nav-label-indigo">Vendor Panel</p>
            <NuxtLink to="/vendor" class="nav-item" :class="{ 'nav-item-active': $route.path === '/vendor' }">
              <span class="nav-icon"><LayoutDashboard :size="14" /></span>Overview
            </NuxtLink>
          </div>

          <!-- Assets -->
          <div v-if="visibleAttributeLinks.length > 0 || productLinks.length > 0" class="mb-2">
            <p class="nav-label">Assets</p>

            <div v-if="visibleAttributeLinks.length > 0">
              <button @click="toggleAttributesMenu" :class="['nav-item w-full', (isAttributesOpen || isAttributesActive) ? 'nav-item-active' : '']">
                <span class="nav-icon"><Grid :size="14" /></span>
                Attributes
                <ChevronDown :size="12" :class="['ml-auto text-gray-400 dark:text-gray-600 transition-transform duration-300 flex-shrink-0', isAttributesOpen ? 'rotate-180' : '']" />
              </button>
              <div class="nav-children" :class="{ 'nav-children-open': isAttributesOpen }">
                <NuxtLink v-for="link in visibleAttributeLinks" :key="link.to" :to="link.to" class="nav-child" active-class="nav-child-active">{{ link.label }}</NuxtLink>
              </div>
            </div>

            <div v-if="productLinks.length > 0">
              <button @click="toggleProductsMenu" :class="['nav-item w-full', (isProductsOpen || isProductsActive) ? 'nav-item-active' : '']">
                <span class="nav-icon"><ShoppingBag :size="14" /></span>
                Products
                <ChevronDown :size="12" :class="['ml-auto text-gray-400 dark:text-gray-600 transition-transform duration-300 flex-shrink-0', isProductsOpen ? 'rotate-180' : '']" />
              </button>
              <div class="nav-children" :class="{ 'nav-children-open': isProductsOpen }">
                <NuxtLink v-for="link in productLinks" :key="link.to" :to="link.to" @click="handleProductLinkClick(link, $event)" class="nav-child" active-class="nav-child-active">{{ link.label }}</NuxtLink>
              </div>
            </div>
          </div>

          <!-- Operations -->
          <div v-if="operationsItems.length > 0" class="mb-2">
            <p class="nav-label">Operations</p>
            <template v-for="item in operationsItems" :key="item.to">
              <NuxtLink :to="item.to" class="nav-item" active-class="nav-item-active">
                <span class="nav-icon"><component :is="item.icon" :size="14" /></span>
                {{ item.label }}
                <span v-if="item.isPro && !hasFeature(item.feature)" class="pro-badge">Pro</span>
              </NuxtLink>
            </template>
          </div>

          <!-- Tools -->
          <div v-if="toolItems.length > 0" class="mb-2">
            <p class="nav-label">Tools</p>
            <template v-for="item in toolItems" :key="item.to">
              <NuxtLink :to="item.to" class="nav-item" active-class="nav-item-active">
                <span class="nav-icon"><component :is="item.icon" :size="14" /></span>
                {{ item.label }}
                <span v-if="item.isPro && !hasFeature(item.feature)" class="pro-badge">Pro</span>
              </NuxtLink>
            </template>
          </div>

          <!-- Settings -->
          <div v-if="settingsItems.length > 0" class="mb-2">
            <p class="nav-label">Settings</p>
            <template v-for="item in settingsItems" :key="item.to">
              <NuxtLink :to="item.to" class="nav-item" active-class="nav-item-active">
                <span class="nav-icon"><component :is="item.icon" :size="14" /></span>
                {{ item.label }}
                <span v-if="item.isPro" class="pro-badge">Pro</span>
              </NuxtLink>
            </template>
          </div>

          <!-- Support -->
          <div class="mb-2">
            <p class="nav-label">Support</p>
            <NuxtLink
              v-if="isVendorOwner"
              to="/vendor/upgrade-package"
              class="nav-item upgrade-item"
              active-class="nav-item-active"
            >
              <span class="nav-icon upgrade-icon"><Sparkles :size="14" /></span>
              Upgrade Plan
            </NuxtLink>
            <NuxtLink to="/vendor/help-center" class="nav-item" active-class="nav-item-active">
              <span class="nav-icon"><Headphones :size="14" /></span>Help Center
            </NuxtLink>
          </div>
        </template>

      </template>

    </nav>

    <!-- Footer -->
    <div class="border-t border-gray-100 dark:border-white/[0.06] p-3 flex-shrink-0">
      <!-- User Card -->
      <div class="flex items-center gap-2.5 px-2 py-2 rounded-xl mb-2 hover:bg-gray-50 dark:hover:bg-white/[0.03] transition-colors cursor-default">
        <div :class="[
          'w-8 h-8 rounded-lg flex items-center justify-center text-white text-[13px] font-bold flex-shrink-0 shadow-sm',
          isSuperAdmin
            ? 'bg-gradient-to-br from-violet-500 to-purple-600'
            : 'bg-gradient-to-br from-indigo-500 to-blue-600'
        ]">
          {{ (auth.user?.name || 'U').charAt(0).toUpperCase() }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-[13px] font-semibold text-gray-900 dark:text-white truncate m-0 leading-tight">{{ auth.user?.name || 'User' }}</p>
          <p class="text-[11px] text-gray-400 dark:text-gray-500 truncate m-0 leading-tight mt-0.5">{{ currentRoleName }}</p>
        </div>
      </div>

      <!-- Actions Row -->
      <div class="flex items-center gap-1 p-1 rounded-xl bg-gray-50 dark:bg-white/[0.03] border border-gray-100 dark:border-white/[0.06]">
        <button @click="toggleColorMode" class="footer-ctrl" :title="isDark ? 'Switch to Light' : 'Switch to Dark'">
          <component :is="isDark ? Sun : Moon" :size="14" />
        </button>
        <button class="footer-ctrl" title="Language">
          <Globe :size="14" />
        </button>
        <button @click="auth.logout()" class="footer-ctrl logout-ctrl" title="Logout">
          <LogOut :size="14" />
        </button>
      </div>
    </div>

  </aside>
</template>

<script setup>
import {
  LayoutDashboard, Grid, ShoppingBag, LayoutTemplate, Users, UserPlus,
  ClipboardList, Store, Globe, Tag, Gift, Briefcase, BarChart3,
  UserCheck, SearchCheck, Headphones, ChevronDown, Sun, Moon, Plus,
  Box, FileText, Layers, ArrowUpDown, Award, Scale, Sparkles, Shield,
  PackagePlus, Building2, LogOut, Monitor, Truck, CreditCard, Zap, Settings,
  Search, X, SearchX,
} from 'lucide-vue-next'

import { toast } from 'vue-sonner'

const { can, hasRole, hasFeature } = usePermissions()

const route = useRoute()
const router = useRouter()
const sidebar = useSidebar()
const colorMode = useColorMode()
const auth = useAuthStore()
const { getAll } = useCrud()

// ── Search ───────────────────────────────────────────────────
const searchQuery    = ref('')
const searchInputRef = ref(null)

const clearSearch = () => { searchQuery.value = '' }

const highlightMatch = (text, query) => {
  if (!query || !text) return text ?? ''
  const escaped = query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')
  return text.replace(
    new RegExp(`(${escaped})`, 'gi'),
    '<mark class="search-mark">$1</mark>'
  )
}

// ── All searchable items (flat list) ─────────────────────────
const allSearchableItems = computed(() => {
  const items = []

  if (isSuperAdmin.value) {
    items.push(
      { to: '/admin',              label: 'Dashboard',     icon: Shield,      section: 'Super Admin' },
      { to: '/admin/users',        label: 'Users',         icon: Users,       section: 'Super Admin' },
      { to: '/admin/plans',        label: 'Plans',         icon: PackagePlus, section: 'Super Admin' },
      { to: '/admin/vendors',      label: 'Vendors',       icon: Building2,   section: 'Super Admin' },
      { to: '/admin/payments',     label: 'Payments',      icon: CreditCard,  section: 'Finance' },
      { to: '/admin/subscriptions',label: 'Subscriptions', icon: Zap,         section: 'Finance' },
      { to: '/admin/transactions', label: 'Transactions',  icon: ArrowUpDown, section: 'Finance' },
      { to: '/admin/settings/general',    label: 'General Settings',    icon: Settings, section: 'System' },
      { to: '/admin/settings/appearance', label: 'Appearance Settings', icon: Settings, section: 'System' },
      { to: '/admin/settings/payments',   label: 'Payment Settings',    icon: Settings, section: 'System' },
      { to: '/admin/settings/mail',       label: 'Mail Config',         icon: Settings, section: 'System' },
      { to: '/admin/settings/advanced',   label: 'Advanced Settings',   icon: Settings, section: 'System' },
    )
  }

    if (isVendor.value) {
      items.push({ to: '/vendor', label: 'Overview', icon: LayoutDashboard, section: 'Vendor Panel' })

    visibleAttributeLinks.value.forEach(l =>
      items.push({ to: l.to, label: l.label, icon: Grid, section: 'Attributes' })
    )
    productLinks.value.forEach(l =>
      items.push({ to: l.to, label: l.label, icon: l.icon, section: 'Products' })
    )
    operationsItems.value.forEach(l =>
      items.push({ to: l.to, label: l.label, icon: l.icon, section: 'Operations', isPro: l.isPro, feature: l.feature })
    )
    toolItems.value.forEach(l =>
      items.push({ to: l.to, label: l.label, icon: l.icon, section: 'Tools', isPro: l.isPro, feature: l.feature })
    )
    settingsItems.value.forEach(l =>
      items.push({ to: l.to, label: l.label, icon: l.icon, section: 'Settings', isPro: l.isPro })
    )
    if (isVendorOwner.value) {
      items.push({ to: '/vendor/upgrade-package', label: 'Upgrade Plan', icon: Sparkles, section: 'Support' })
    }
    items.push({ to: '/vendor/help-center', label: 'Help Center', icon: Headphones, section: 'Support' })
  }

  return items
})

const searchResults = computed(() => {
  const q = searchQuery.value.trim().toLowerCase()
  if (!q) return []
  return allSearchableItems.value.filter(item => {
    const labelMatch   = item.label?.toLowerCase().includes(q)   ?? false
    const sectionMatch = item.section?.toLowerCase().includes(q) ?? false
    return labelMatch || sectionMatch
  })
})

const handleSearchItemClick = async (item, event) => {
  clearSearch()
  if (item.to === '/vendor/products/create') {
    await handleProductLinkClick(item, event)
  }
}

// ── Product limit check ──────────────────────────────────────
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

// ── Auth / Role ──────────────────────────────────────────────
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

// ── Accordion state ──────────────────────────────────────────
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

onMounted(() => {
  if (route.path.includes('/vendor/attributes')) isAttributesOpen.value = true
  if (route.path.includes('/vendor/products'))   isProductsOpen.value   = true
  if (route.path.includes('/admin/settings'))    isAdminSettingsOpen.value = true
})

watch(() => route.path, () => {
  sidebar.close()
  clearSearch()
  if (route.path.includes('/vendor/attributes')) isAttributesOpen.value = true
  if (route.path.includes('/vendor/products'))   isProductsOpen.value   = true
  if (route.path.includes('/admin/settings'))    isAdminSettingsOpen.value = true
})

const toggleProductsMenu      = () => { isProductsOpen.value   = !isProductsOpen.value }
const toggleAttributesMenu    = () => { isAttributesOpen.value = !isAttributesOpen.value }
const toggleAdminSettingsMenu = () => { isAdminSettingsOpen.value = !isAdminSettingsOpen.value }

// ── Permission filtering ─────────────────────────────────────
const filterItems = (items) => {
  return items.filter(item => {
    if (item.feature && !hasFeature(item.feature)) return false
    if (!item.permission) return true
    return can(item.permission)
  })
}

const attributeLinks = [
  { to: '/vendor/attributes/categories', label: 'Categories', permission: 'attributes.view', feature: 'Products' },
  { to: '/vendor/attributes/brands',     label: 'Brands',     permission: 'brands.view',     feature: 'Products' },
  { to: '/vendor/attributes/units',      label: 'Units',      permission: 'units.view',      feature: 'Products' },
  { to: '/vendor/attributes/sorting',    label: 'Sorting',    permission: 'attributes.sort', feature: 'Products' },
]
const visibleAttributeLinks = computed(() => filterItems(attributeLinks))

const rawProductLinks = [
  { to: '/vendor/products/create',    label: 'Add Product',  icon: Plus,     permission: 'products.create', feature: 'Products' },
  { to: '/vendor/products',           label: 'All Products', icon: Box,      permission: 'products.view',   feature: 'Products' },
  { to: '/vendor/products/published', label: 'Published',    icon: FileText, permission: 'products.view',   feature: 'Products' },
  { to: '/vendor/products/pending',   label: 'Pending',      icon: FileText, permission: 'products.view',   feature: 'Products' },
  { to: '/vendor/products/draft',     label: 'Drafts',       icon: FileText, permission: 'products.view',   feature: 'Products' },
  { to: '/vendor/products/barcodes',  label: 'Barcodes',     icon: Tag,      permission: 'products.view',   feature: 'Products' },
  { to: '/vendor/products/restock',   label: 'Restock',      icon: Plus,     permission: 'stock.manage',    feature: 'Products' },
  { to: '/vendor/products/suppliers', label: 'Suppliers',    icon: Truck,    permission: 'suppliers.view',  feature: 'Products' },
  { to: '/vendor/warehouse/audit',    label: 'Stock Audit',  icon: Layers,   permission: 'products.view',   feature: 'Products' },
]
const productLinks = computed(() => filterItems(rawProductLinks))

const rawOperationsItems = [
  { to: '/vendor/orders',       label: 'Orders',         icon: ClipboardList,  permission: 'orders.view',        feature: 'Orders' },
  { to: '/vendor/customers',    label: 'Customers',      icon: Users,          permission: 'customers.view',     feature: 'Customers' },
  { to: '/vendor/pos',          label: 'Point of Sale',  icon: Monitor,        permission: 'pos.view',           feature: 'pos',          isPro: true },
  { to: '/vendor/coupon-code',  label: 'Coupon Code',    icon: Tag,            permission: 'coupons.view',       feature: 'Products' },
  { to: '/vendor/promotions',   label: 'Promotions',     icon: Gift,           permission: 'promotions.view',    feature: 'Promotions',   isPro: true },
  { to: '/vendor/fraud-check',  label: 'AI Fraud Check', icon: SearchCheck,    permission: 'fraud_check.view',   feature: 'Fraud Check',  isPro: true },
  { to: '/vendor/ai-automation', label: 'AI Automation', icon: Sparkles,       permission: 'settings.manage' },
  { to: '/vendor/landing-page', label: 'Landing Page',   icon: LayoutTemplate, permission: 'landing_pages.view', feature: 'Landing Pages',isPro: true },
]
const operationsItems = computed(() => filterItems(rawOperationsItems))

const rawToolItems = [
  { to: '/vendor/hrm',             label: 'HRM Suite',        icon: Briefcase, permission: 'hrm.dashboard.view', feature: 'hrm', isPro: true },
  { to: '/vendor/reports',         label: 'Analytics',        icon: BarChart3, permission: 'reports.view',       feature: 'Reports' },
  { to: '/vendor/managed-shop',    label: 'Manage Shop',      icon: Store,     permission: 'settings.view' },
  { to: '/vendor/managed-website', label: 'Website Settings', icon: Globe,     permission: 'settings.view' },
]
const toolItems = computed(() => filterItems(rawToolItems))

const rawSettingsItems = [
  { to: '/vendor/staff',            label: 'Manage Staff', icon: UserPlus,  permission: 'staff.view' },
  { to: '/vendor/role-permissions', label: 'Permissions',  icon: UserCheck, isPro: true, permission: 'roles.view' },
]
const settingsItems = computed(() => filterItems(rawSettingsItems))
</script>

<style scoped>
/* ── Section Label ─────────────────────────────────────── */
.nav-label {
  @apply text-[10px] font-semibold uppercase tracking-[0.1em]
         text-gray-400 dark:text-gray-600
         px-2.5 pt-1 pb-1.5 m-0;
}
.nav-label-indigo { @apply text-indigo-500 dark:text-indigo-400; }
.nav-label-violet { @apply text-violet-500 dark:text-violet-400; }

/* ── Nav Item ──────────────────────────────────────────── */
.nav-item {
  @apply relative flex items-center gap-2.5 w-full px-2.5 py-2 rounded-lg
         text-[13px] font-medium text-gray-600 dark:text-gray-400
         no-underline border-none bg-transparent cursor-pointer text-left
         transition-all duration-150;
}
.nav-item:hover {
  @apply bg-gray-50 dark:bg-white/[0.04] text-gray-900 dark:text-gray-100;
}
.nav-item-active {
  @apply bg-indigo-50 dark:bg-indigo-500/10
         text-indigo-700 dark:text-indigo-400
         font-semibold;
}

/* ── Nav Icon ──────────────────────────────────────────── */
.nav-icon {
  @apply flex items-center justify-center w-[26px] h-[26px] rounded-lg flex-shrink-0
         bg-white dark:bg-white/[0.04]
         border border-gray-200 dark:border-white/[0.08]
         text-gray-500 dark:text-gray-500
         shadow-[0_1px_2px_rgba(0,0,0,0.05)]
         transition-all duration-150;
}
.nav-item:hover .nav-icon {
  @apply bg-white dark:bg-white/[0.07]
         border-gray-300 dark:border-white/[0.12]
         text-gray-700 dark:text-gray-300
         shadow-[0_1px_3px_rgba(0,0,0,0.08)];
}
.nav-item-active .nav-icon {
  @apply bg-indigo-100 dark:bg-indigo-500/20
         border-indigo-200 dark:border-indigo-500/30
         text-indigo-600 dark:text-indigo-400
         shadow-none;
}
.nav-icon-violet {
  @apply bg-violet-100 dark:bg-violet-500/20
         border-violet-200 dark:border-violet-500/30
         text-violet-600 dark:text-violet-400;
}

/* ── Upgrade item ─────────────────────────────────────── */
.upgrade-item { @apply text-emerald-700 dark:text-emerald-400; }
.upgrade-item:hover {
  @apply bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400;
}
.upgrade-icon {
  @apply bg-emerald-50 dark:bg-emerald-500/10
         border-emerald-200 dark:border-emerald-500/20
         text-emerald-600 dark:text-emerald-400;
}

/* ── Pro Badge ─────────────────────────────────────────── */
.pro-badge {
  @apply ml-auto text-[9px] font-bold uppercase tracking-wide
         px-1.5 py-0.5 rounded-md
         bg-amber-100 text-amber-700
         dark:bg-amber-500/10 dark:text-amber-400
         flex-shrink-0;
}

/* ── Accordion ────────────────────────────────────────── */
.nav-children {
  @apply overflow-hidden max-h-0 transition-all duration-200 ease-in-out pl-3 mt-0.5;
}
.nav-children-open { @apply max-h-96; }

/* ── Child Link ───────────────────────────────────────── */
.nav-child {
  @apply flex items-center gap-2 px-2.5 py-[5px] text-[12.5px] font-medium
         text-gray-500 dark:text-gray-500
         no-underline rounded-md my-px
         border-l-2 border-transparent
         transition-all duration-150;
}
.nav-child::before {
  content: '';
  @apply w-[5px] h-[5px] rounded-full bg-gray-300 dark:bg-gray-700
         flex-shrink-0 transition-colors duration-150;
}
.nav-child:hover {
  @apply bg-gray-50 dark:bg-white/[0.04] text-gray-800 dark:text-gray-200;
}
.nav-child:hover::before { @apply bg-indigo-400; }
.nav-child-active {
  @apply bg-indigo-50 dark:bg-indigo-500/10
         text-indigo-600 dark:text-indigo-400
         font-semibold border-indigo-400 dark:border-indigo-500;
}
.nav-child-active::before { @apply bg-indigo-500; }

/* ── Footer Controls ──────────────────────────────────── */
.footer-ctrl {
  @apply flex-1 flex items-center justify-center py-[7px] px-2 rounded-lg border-none
         bg-transparent text-gray-500 dark:text-gray-500 cursor-pointer
         hover:bg-white dark:hover:bg-white/[0.07]
         hover:text-gray-900 dark:hover:text-gray-100
         hover:shadow-[0_1px_3px_rgba(0,0,0,0.08)]
         transition-all duration-150;
}
.logout-ctrl {
  @apply hover:!bg-red-50 hover:!text-red-600
         dark:hover:!bg-red-500/10 dark:hover:!text-red-400;
}

/* ── Sidebar Scrollbar ────────────────────────────────── */
.sidebar-nav {
  scrollbar-width: thin;
  scrollbar-color: #e2e8f0 transparent;
}
.sidebar-nav::-webkit-scrollbar { width: 4px; }
.sidebar-nav::-webkit-scrollbar-track {
  background: transparent;
  margin: 6px 0;
}
.sidebar-nav::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 999px;
  transition: background 0.2s ease;
}
.sidebar-nav::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
:global(.dark) .sidebar-nav { scrollbar-color: rgba(255,255,255,0.08) transparent; }
:global(.dark) .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); }
:global(.dark) .sidebar-nav::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.14); }

/* ── Search Bar ───────────────────────────────────────── */
.search-wrap {
  @apply relative flex items-center;
}
.search-icon {
  @apply absolute left-2.5 text-gray-400 dark:text-gray-600 pointer-events-none flex-shrink-0;
}
.search-input {
  @apply w-full h-8 pl-8 pr-7 text-[12.5px] font-medium
         text-gray-700 dark:text-gray-300
         bg-gray-50 dark:bg-white/[0.04]
         border border-gray-200 dark:border-white/[0.08]
         rounded-lg outline-none
         placeholder:text-gray-400 dark:placeholder:text-gray-600
         transition-all duration-150;
}
.search-input:focus {
  @apply bg-white dark:bg-white/[0.07]
         border-indigo-300 dark:border-indigo-500/40
         shadow-[0_0_0_3px_rgba(99,102,241,0.08)] dark:shadow-[0_0_0_3px_rgba(99,102,241,0.12)];
}
.search-clear {
  @apply absolute right-2 flex items-center justify-center
         w-4 h-4 rounded-full
         bg-gray-300 dark:bg-white/[0.12]
         text-gray-600 dark:text-gray-400
         border-none cursor-pointer
         hover:bg-gray-400 dark:hover:bg-white/[0.2]
         transition-colors duration-150;
}

/* ── Search highlight mark ─────────────────────────────── */
:deep(.search-mark) {
  background: rgba(99, 102, 241, 0.15);
  color: #4f46e5;
  border-radius: 2px;
  padding: 0 1px;
  font-weight: 600;
}
:global(.dark) :deep(.search-mark) {
  background: rgba(99, 102, 241, 0.25);
  color: #818cf8;
}

/* ── Search Empty State ────────────────────────────────── */
.search-empty {
  @apply flex flex-col items-center justify-center py-8 px-3 text-center;
}
.search-empty-icon {
  @apply w-10 h-10 rounded-xl flex items-center justify-center
         bg-gray-100 dark:bg-white/[0.04]
         border border-gray-200 dark:border-white/[0.06]
         mb-3;
}
.search-empty-text {
  @apply text-[12px] text-gray-500 dark:text-gray-500 m-0 mb-3 leading-snug;
}
.search-empty-text strong {
  @apply text-gray-700 dark:text-gray-300 font-semibold;
}
.search-empty-btn {
  @apply text-[11px] font-medium text-indigo-600 dark:text-indigo-400
         bg-indigo-50 dark:bg-indigo-500/10
         border border-indigo-200 dark:border-indigo-500/20
         px-3 py-1.5 rounded-lg cursor-pointer
         hover:bg-indigo-100 dark:hover:bg-indigo-500/20
         transition-colors duration-150;
}
</style>
