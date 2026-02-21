<template>
  <div class="p-8 bg-[#f8fafc] dark:bg-slate-950 min-h-full">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500 mb-2 font-bold uppercase tracking-widest">
          <Shield class="w-3.5 h-3.5" />
          <span>Super Admin</span>
          <ChevronRight class="w-3 h-3" />
          <span class="text-slate-600 dark:text-slate-300">Dashboard</span>
        </div>
        <h1 class="text-3xl font-[1000] text-slate-900 dark:text-white tracking-tight">Super Admin Control Center</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Manage packages, vendors, and platform settings from one place.</p>
      </div>
      <NuxtLink to="/admin/vendors/create"
        class="flex items-center gap-2 px-5 py-2.5 bg-slate-900 dark:bg-indigo-600 text-white rounded-xl text-sm font-bold hover:scale-105 transition-all shadow-lg shadow-slate-900/20 dark:shadow-indigo-600/30">
        <Plus class="w-4 h-4" />
        Add Vendor
      </NuxtLink>
    </div>

    <!-- Stats Cards -->
    <div v-if="statsLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
      <div v-for="i in 4" :key="i" class="h-28 bg-white dark:bg-slate-900 rounded-2xl animate-pulse border border-slate-100 dark:border-slate-800"></div>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-6 hover:border-indigo-400/40 transition-all group">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1.5">Total Vendors</p>
            <h3 class="text-3xl font-[1000] text-slate-900 dark:text-white">{{ stats?.total_vendors || 0 }}</h3>
          </div>
          <div class="p-3 bg-indigo-50 dark:bg-indigo-500/10 rounded-xl group-hover:scale-110 transition-transform">
            <Store class="w-5 h-5 text-indigo-500" />
          </div>
        </div>
        <p class="text-xs text-slate-400 mt-3 font-semibold">Registered vendor accounts</p>
      </div>

      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-6 hover:border-emerald-400/40 transition-all group">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1.5">Active Plans</p>
            <h3 class="text-3xl font-[1000] text-slate-900 dark:text-white">{{ stats?.vendors_with_packages || 0 }}</h3>
          </div>
          <div class="p-3 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl group-hover:scale-110 transition-transform">
            <CheckCircle class="w-5 h-5 text-emerald-500" />
          </div>
        </div>
        <p class="text-xs text-slate-400 mt-3 font-semibold">Vendors on a paid package</p>
      </div>

      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-6 hover:border-violet-400/40 transition-all group">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1.5">Packages</p>
            <h3 class="text-3xl font-[1000] text-slate-900 dark:text-white">{{ stats?.total_packages || 0 }}</h3>
          </div>
          <div class="p-3 bg-violet-50 dark:bg-violet-500/10 rounded-xl group-hover:scale-110 transition-transform">
            <Package class="w-5 h-5 text-violet-500" />
          </div>
        </div>
        <p class="text-xs text-slate-400 mt-3 font-semibold">Active subscription plans</p>
      </div>

      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-6 hover:border-amber-400/40 transition-all group">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1.5">No Package</p>
            <h3 class="text-3xl font-[1000] text-slate-900 dark:text-white">{{ (stats?.total_vendors || 0) - (stats?.vendors_with_packages || 0) }}</h3>
          </div>
          <div class="p-3 bg-amber-50 dark:bg-amber-500/10 rounded-xl group-hover:scale-110 transition-transform">
            <AlertCircle class="w-5 h-5 text-amber-500" />
          </div>
        </div>
        <p class="text-xs text-slate-400 mt-3 font-semibold">Vendors without a package</p>
      </div>
    </div>

    <!-- Main Grid: Recent Vendors + Package Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Recent Vendors -->
      <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-base font-[1000] text-slate-900 dark:text-white">Recent Vendors</h2>
          <NuxtLink to="/admin/vendors" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">View All →</NuxtLink>
        </div>
        <div v-if="statsLoading" class="space-y-3">
          <div v-for="i in 5" :key="i" class="h-14 bg-slate-50 dark:bg-slate-800 rounded-xl animate-pulse"></div>
        </div>
        <div v-else class="space-y-2">
          <div v-for="vendor in stats?.recent_vendors" :key="vendor.id"
            class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all group cursor-pointer"
            @click="$router.push('/admin/vendors/' + vendor.id)">
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-violet-500 rounded-xl flex items-center justify-center text-white font-black text-sm shadow-md shadow-indigo-500/20 flex-shrink-0">
              {{ (vendor.name || 'V').charAt(0).toUpperCase() }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ vendor.name }}</p>
              <p class="text-xs text-slate-400 truncate">{{ vendor.email }}</p>
            </div>
            <div class="text-right flex-shrink-0">
              <span v-if="vendor.vendor_profile?.package"
                class="inline-block px-2.5 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-black rounded-full uppercase tracking-wider">
                {{ vendor.vendor_profile.package.name }}
              </span>
              <span v-else class="inline-block px-2.5 py-1 bg-slate-100 dark:bg-slate-800 text-slate-500 text-[10px] font-black rounded-full uppercase tracking-wider">
                No Plan
              </span>
            </div>
          </div>
          <div v-if="!stats?.recent_vendors?.length" class="text-center py-8 text-slate-400 text-sm">
            No vendors yet.
          </div>
        </div>
      </div>

      <!-- Package Distribution -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-6">
        <div class="flex items-center justify-between mb-6">
          <h2 class="text-base font-[1000] text-slate-900 dark:text-white">Package Distribution</h2>
          <NuxtLink to="/admin/packages" class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:underline">Edit →</NuxtLink>
        </div>
        <div v-if="statsLoading" class="space-y-4">
          <div v-for="i in 3" :key="i" class="h-10 bg-slate-50 dark:bg-slate-800 rounded-xl animate-pulse"></div>
        </div>
        <div v-else class="space-y-4">
          <div v-for="(pkg, i) in stats?.package_distribution" :key="i">
            <div class="flex justify-between items-center text-xs font-bold mb-1.5">
              <span class="text-slate-600 dark:text-slate-300">{{ pkg.name }}</span>
              <span class="text-slate-400">{{ pkg.count }} vendors</span>
            </div>
            <div class="h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
              <div class="h-full rounded-full transition-all duration-700"
                :class="pkgColors[i % pkgColors.length]"
                :style="{ width: totalVendors > 0 ? `${(pkg.count / totalVendors) * 100}%` : '0%' }">
              </div>
            </div>
          </div>
          <div v-if="!stats?.package_distribution?.length" class="text-center py-8 text-slate-400 text-sm">
            No packages yet. <NuxtLink to="/admin/packages/create" class="text-indigo-600 dark:text-indigo-400 font-bold hover:underline">Create one →</NuxtLink>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
      <NuxtLink v-for="action in quickActions" :key="action.to" :to="action.to"
        class="flex flex-col items-center gap-3 p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 hover:border-indigo-400/30 hover:-translate-y-1 transition-all group text-center">
        <div class="p-3 rounded-xl transition-all group-hover:scale-110" :class="action.bg">
          <component :is="action.icon" class="w-5 h-5" :class="action.color" />
        </div>
        <span class="text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-wider">{{ action.label }}</span>
      </NuxtLink>
    </div>
  </div>
</template>

<script setup>
import {
  Shield, ChevronRight, Plus, Store, CheckCircle, Package, AlertCircle,
  PackagePlus, Users, BarChart3, Settings
} from 'lucide-vue-next'

definePageMeta({
  middleware: 'auth',
  layout: 'default',
  roles: ['super-admin', 'admin']
})

const { getAll } = useCrud()
const stats = ref(null)
const statsLoading = ref(true)

const pkgColors = [
  'bg-indigo-500', 'bg-violet-500', 'bg-emerald-500', 'bg-amber-500', 'bg-rose-500'
]

const totalVendors = computed(() => stats.value?.total_vendors || 1)

const quickActions = [
  { to: '/admin/packages/create', label: 'New Package', icon: PackagePlus, bg: 'bg-indigo-50 dark:bg-indigo-500/10', color: 'text-indigo-600 dark:text-indigo-400' },
  { to: '/admin/vendors/create', label: 'Add Vendor', icon: Users, bg: 'bg-violet-50 dark:bg-violet-500/10', color: 'text-violet-600 dark:text-violet-400' },
  { to: '/admin/packages', label: 'Manage Plans', icon: Package, bg: 'bg-emerald-50 dark:bg-emerald-500/10', color: 'text-emerald-600 dark:text-emerald-400' },
  { to: '/admin/vendors', label: 'All Vendors', icon: Store, bg: 'bg-amber-50 dark:bg-amber-500/10', color: 'text-amber-600 dark:text-amber-400' },
]

const fetchStats = async () => {
  statsLoading.value = true
  try {
    stats.value = await getAll('/admin/dashboard')
  } catch (e) {
    console.error(e)
  } finally {
    statsLoading.value = false
  }
}

onMounted(fetchStats)
</script>
