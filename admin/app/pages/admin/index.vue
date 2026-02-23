<template>
  <div class="p-8 bg-[#f8fafc] dark:bg-slate-950 min-h-full">
    <!-- Header -->
    <div class="mb-10">
      <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500 mb-2 font-black uppercase tracking-[0.2em]">
        <Shield class="w-3.5 h-3.5" />
        Platform Administration
      </div>
      <h1 class="text-4xl font-[1000] text-slate-900 dark:text-white tracking-tight">System Overview</h1>
      <p class="text-slate-500 dark:text-slate-400 text-sm mt-1 font-medium">Monitoring platform-wide vendor performance and growth metrics.</p>
    </div>

    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-10">
      <!-- Total Vendors -->
      <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-7 border border-slate-100 dark:border-slate-800/60 shadow-[0_20px_50px_-12px_rgba(0,0,0,0.03)] hover:shadow-xl hover:-translate-y-1 transition-all group">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-1">Total Vendors</p>
            <h3 v-if="!loading" class="text-3xl font-[1000] text-slate-900 dark:text-white tracking-tighter">{{ stats.total_vendors || 0 }}</h3>
            <div v-else class="h-9 w-20 bg-slate-100 dark:bg-slate-800 rounded-lg animate-pulse mt-1"></div>
          </div>
          <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg transition-transform group-hover:scale-110 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
            <Users class="w-6 h-6" />
          </div>
        </div>
        <div class="mt-4 flex items-center gap-2">
          <span class="text-[10px] font-black uppercase tracking-wider text-emerald-500 flex items-center gap-0.5">
            <TrendingUp class="w-3 h-3" /> 12%
          </span>
          <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider text-nowrap">vs last month</span>
        </div>
      </div>

      <!-- Active Packages -->
      <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-7 border border-slate-100 dark:border-slate-800/60 shadow-[0_20px_50px_-12px_rgba(0,0,0,0.03)] hover:shadow-xl hover:-translate-y-1 transition-all group">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-1">Active Plans</p>
            <h3 v-if="!loading" class="text-3xl font-[1000] text-slate-900 dark:text-white tracking-tighter">{{ stats.vendors_with_packages || 0 }}</h3>
            <div v-else class="h-9 w-20 bg-slate-100 dark:bg-slate-800 rounded-lg animate-pulse mt-1"></div>
          </div>
          <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg transition-transform group-hover:scale-110 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
            <Zap class="w-6 h-6" />
          </div>
        </div>
        <div class="mt-4 flex items-center gap-2 text-[10px] text-slate-400 font-bold uppercase tracking-wider">
           <span class="text-emerald-500">{{ Math.round((stats.vendors_with_packages / stats.total_vendors) * 100) || 0 }}%</span> conversion rate
        </div>
      </div>

      <!-- Platform Revenue (Simulated) -->
      <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-7 border border-slate-100 dark:border-slate-800/60 shadow-[0_20px_50px_-12px_rgba(0,0,0,0.03)] hover:shadow-xl hover:-translate-y-1 transition-all group">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-1">Est. Monthly MRR</p>
            <h3 v-if="!loading" class="text-3xl font-[1000] text-slate-900 dark:text-white tracking-tighter">৳{{ calculateMRR().toLocaleString() }}</h3>
            <div v-else class="h-9 w-24 bg-slate-100 dark:bg-slate-800 rounded-lg animate-pulse mt-1"></div>
          </div>
          <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg transition-transform group-hover:scale-110 bg-violet-50 dark:bg-violet-500/10 text-violet-600 dark:text-violet-400">
            <CreditCard class="w-6 h-6" />
          </div>
        </div>
        <div class="mt-4 flex items-center gap-2">
           <div class="flex -space-x-2">
              <div v-for="i in 3" :key="i" class="w-5 h-5 rounded-full border-2 border-white dark:border-slate-900 bg-slate-200 dark:bg-slate-800 flex items-center justify-center text-[8px] font-bold">U</div>
           </div>
           <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider ml-2">+{{ stats.total_vendors - 3 || 0 }} more</span>
        </div>
      </div>

      <!-- Available Plans -->
      <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-7 border border-slate-100 dark:border-slate-800/60 shadow-[0_20px_50px_-12px_rgba(0,0,0,0.03)] hover:shadow-xl hover:-translate-y-1 transition-all group">
        <div class="flex items-start justify-between">
          <div>
            <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] mb-1">Defined Plans</p>
            <h3 v-if="!loading" class="text-3xl font-[1000] text-slate-900 dark:text-white tracking-tighter">{{ stats.total_packages || 0 }}</h3>
            <div v-else class="h-9 w-20 bg-slate-100 dark:bg-slate-800 rounded-lg animate-pulse mt-1"></div>
          </div>
          <div class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg transition-transform group-hover:scale-110 bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400">
            <Layers class="w-6 h-6" />
          </div>
        </div>
        <div class="mt-4">
           <NuxtLink to="/admin/plans" class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] hover:text-indigo-600 transition-colors flex items-center gap-1.5">
              Manage Items <ArrowRight class="w-3 h-3" />
           </NuxtLink>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
      <!-- Recent Vendors Table -->
      <div class="xl:col-span-2 space-y-4">
        <div class="flex items-center justify-between px-2">
          <h2 class="text-xl font-[1000] text-slate-900 dark:text-white tracking-tight">Recent Onboardings</h2>
          <NuxtLink to="/admin/vendors" class="text-xs font-black text-indigo-500 uppercase tracking-widest hover:text-indigo-600 transition-colors">View All Vendors</NuxtLink>
        </div>
        
        <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-100 dark:border-slate-800/60 shadow-[0_20px_50px_-12px_rgba(0,0,0,0.03)] overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left">
              <thead>
                <tr class="border-b border-slate-50 dark:border-slate-800/60">
                  <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Vendor</th>
                  <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Package Info</th>
                  <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Status</th>
                  <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-50 dark:divide-slate-800/60">
                <tr v-for="vendor in stats.recent_vendors" :key="vendor.id" class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-all">
                  <td class="px-8 py-5">
                    <div class="flex items-center gap-4">
                      <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white text-xs font-black shadow-lg shadow-indigo-500/20 group-hover:scale-110 transition-transform">
                        {{ vendor.name.charAt(0) }}
                      </div>
                      <div>
                        <p class="text-sm font-bold text-slate-900 dark:text-white">{{ vendor.name }}</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">{{ vendor.vendor_profile?.store_name || 'Individual' }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-8 py-5">
                    <div v-if="vendor.vendor_profile?.package" class="flex flex-col">
                      <span class="text-[11px] font-black text-slate-700 dark:text-slate-300">{{ vendor.vendor_profile.package.name }}</span>
                      <span class="text-[10px] text-slate-400 font-bold uppercase">৳{{ vendor.vendor_profile.package.price }}/{{ vendor.vendor_profile.package.duration }}</span>
                    </div>
                    <span v-else class="text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Free Tier</span>
                  </td>
                  <td class="px-8 py-5">
                    <div class="flex items-center gap-2">
                       <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                       <span class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Active</span>
                    </div>
                  </td>
                  <td class="px-8 py-5 text-right">
                    <button class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 hover:bg-slate-900 hover:text-white dark:hover:bg-indigo-600 transition-all flex items-center justify-center ml-auto group/btn">
                       <ExternalLink class="w-4 h-4" />
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Plan Distribution -->
      <div class="space-y-4">
        <h2 class="text-xl font-[1000] text-slate-900 dark:text-white tracking-tight px-2">Plan Popularity</h2>
        <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-100 dark:border-slate-800/60 shadow-[0_20px_50px_-12px_rgba(0,0,0,0.03)] p-8">
           <div class="space-y-6">
              <div v-for="plan in stats.package_distribution" :key="plan.name" class="space-y-2">
                 <div class="flex items-center justify-between">
                    <span class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-widest">{{ plan.name }}</span>
                    <span class="text-xs font-black text-slate-400">{{ plan.count }} Units</span>
                 </div>
                 <div class="h-2 w-full bg-slate-50 dark:bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-indigo-500 rounded-full transition-all duration-1000" :style="{ width: `${(plan.count / stats.total_vendors) * 100}%` }"></div>
                 </div>
                 <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Market Share</span>
                    <span class="text-[10px] font-black text-indigo-500">{{ Math.round((plan.count / stats.total_vendors) * 100) || 0 }}%</span>
                 </div>
              </div>
           </div>

           <!-- Invite Vendor Promo -->
           <div class="mt-10 p-6 bg-gradient-to-br from-indigo-600 to-violet-700 rounded-3xl relative overflow-hidden group">
              <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
              <h4 class="text-white font-[1000] tracking-tight mb-1 relative z-10">Expand Your Network</h4>
              <p class="text-indigo-100/70 text-[10px] font-medium mb-4 relative z-10 leading-relaxed uppercase tracking-widest">Growth leads to global prosperity.</p>
              <button class="w-full py-3 bg-white text-indigo-600 rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-indigo-900/20 active:scale-95 transition-all">
                 Onboard New Vendor
              </button>
           </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  Shield, 
  Users, 
  TrendingUp, 
  Zap, 
  CreditCard, 
  Layers, 
  ArrowRight,
  ExternalLink
} from 'lucide-vue-next'
import { ref, onMounted } from 'vue'

definePageMeta({
  middleware: 'auth',
  permissions: 'admin.dashboard.view'
})

const { getAll } = useCrud()
const stats = ref({
  total_vendors: 0,
  vendors_with_packages: 0,
  total_packages: 0,
  recent_vendors: [],
  package_distribution: []
})
const loading = ref(true)

const fetchStats = async () => {
  loading.value = true
  try {
    const res = await getAll('/admin/dashboard')
    stats.value = res
  } catch (error) {
    console.error('dashboard: fetchStats failed', error)
  } finally {
    loading.value = false
  }
}

const calculateMRR = () => {
   if (!stats.value.package_distribution) return 0
   return stats.value.package_distribution.reduce((acc, plan) => acc + (plan.price * plan.count), 0)
}

onMounted(fetchStats)
</script>


