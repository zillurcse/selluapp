<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 transition-colors duration-300">
    <!-- Breadcrumb & Date Filter -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
        <Home class="w-4 h-4 mr-2" />
        <span>Dashboard</span>
        <ChevronRight class="w-4 h-4 mx-2" />
        <span>Profile</span>
        <ChevronRight class="w-4 h-4 mx-2" />
        <span class="font-semibold text-gray-800 dark:text-gray-200">{{ profile?.vendor_profile?.store_name || 'My Store' }}</span>
      </div>
      
      <div class="flex bg-white dark:bg-slate-900 rounded-md shadow-sm border border-gray-200 dark:border-slate-700 p-1">
        <button class="px-4 py-1.5 text-sm font-medium rounded bg-gray-900 dark:bg-indigo-600 text-white shadow-sm transition-all active:scale-95">Last 7 Days</button>
        <button class="px-4 py-1.5 text-sm font-medium rounded text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-all">Last 30 Days</button>
      </div>
    </div>

    <!-- Loading Skeleton or Content -->
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-8">
        <div v-for="i in 4" :key="i" class="h-32 bg-white dark:bg-slate-900 rounded-xl animate-pulse border border-gray-100 dark:border-slate-800"></div>
    </div>

    <div v-else class="space-y-8 mt-8">
      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 tracking-tight">
        <!-- Total Sales -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm p-7 border border-gray-100 dark:border-slate-800/60 hover:border-indigo-500/30 transition-all duration-500 group">
          <div class="flex flex-col h-full justify-between">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-slate-500 dark:text-slate-400 text-xs font-black uppercase tracking-widest mb-1.5">Total Sales</p>
                <h3 class="text-3xl font-[1000] text-slate-900 dark:text-white leading-none">৳{{ formatNumber(stats?.total_sales || 0) }}</h3>
              </div>
              <div class="p-3 bg-indigo-50 dark:bg-indigo-500/10 rounded-xl group-hover:scale-110 transition-transform">
                <LayoutDashboard class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
              </div>
            </div>
            <div class="mt-8 flex items-center text-xs">
              <span class="text-emerald-600 dark:text-emerald-400 font-black flex items-center bg-emerald-50 dark:bg-emerald-500/10 px-2 py-1 rounded-lg mr-2">
                +{{ stats?.trends?.sales || 0 }}% <ArrowUp class="w-3 h-3 ml-0.5" />
              </span>
              <span class="text-slate-400 dark:text-slate-500 font-bold">vs last week</span>
            </div>
          </div>
        </div>

        <!-- Total Expenses -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm p-7 border border-gray-100 dark:border-slate-800/60 hover:border-rose-500/30 transition-all duration-500 group">
          <div class="flex flex-col h-full justify-between">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-slate-500 dark:text-slate-400 text-xs font-black uppercase tracking-widest mb-1.5">Expenses</p>
                <h3 class="text-3xl font-[1000] text-slate-900 dark:text-white leading-none">৳{{ formatNumber(stats?.expenses || 0) }}</h3>
              </div>
              <div class="p-3 bg-rose-50 dark:bg-rose-500/10 rounded-xl group-hover:scale-110 transition-transform">
                <ArrowUpRight class="w-5 h-5 text-rose-600 dark:text-rose-400" />
              </div>
            </div>
            <div class="mt-8 flex items-center text-xs">
              <span class="text-rose-600 dark:text-rose-400 font-black flex items-center bg-rose-50 dark:bg-rose-500/10 px-2 py-1 rounded-lg mr-2">
                +{{ stats?.trends?.expenses || 0 }}% <ArrowUp class="w-3 h-3 ml-0.5" />
              </span>
              <span class="text-slate-400 dark:text-slate-500 font-bold">vs last week</span>
            </div>
          </div>
        </div>

        <!-- Net Profit -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm p-7 border border-gray-100 dark:border-slate-800/60 hover:border-emerald-500/30 transition-all duration-500 group">
          <div class="flex flex-col h-full justify-between">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-slate-500 dark:text-slate-400 text-xs font-black uppercase tracking-widest mb-1.5">Net Profit</p>
                <h3 class="text-3xl font-[1000] text-slate-900 dark:text-white leading-none">৳{{ formatNumber(stats?.net_profit || 0) }}</h3>
              </div>
              <div class="p-3 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl group-hover:scale-110 transition-transform">
                <ArrowUp class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
              </div>
            </div>
            <div class="mt-8 flex items-center text-xs">
              <span class="text-emerald-600 dark:text-emerald-400 font-black flex items-center bg-emerald-50 dark:bg-emerald-500/10 px-2 py-1 rounded-lg mr-2">
                +{{ stats?.trends?.profit || 0 }}% <ArrowUp class="w-3 h-3 ml-0.5" />
              </span>
              <span class="text-slate-400 dark:text-slate-500 font-bold">vs last week</span>
            </div>
          </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm p-7 border border-gray-100 dark:border-slate-800/60 hover:border-amber-500/30 transition-all duration-500 group">
          <div class="flex flex-col h-full justify-between">
            <div class="flex justify-between items-start">
              <div>
                <p class="text-slate-500 dark:text-slate-400 text-xs font-black uppercase tracking-widest mb-1.5">Total Orders</p>
                <h3 class="text-3xl font-[1000] text-slate-900 dark:text-white leading-none">{{ stats?.total_orders || 0 }}</h3>
              </div>
              <div class="p-3 bg-amber-50 dark:bg-amber-500/10 rounded-xl group-hover:scale-110 transition-transform">
                <ShoppingBag class="w-5 h-5 text-amber-600 dark:text-amber-400" />
              </div>
            </div>
            <div class="mt-8 flex items-center text-xs">
              <span class="text-slate-900 dark:text-white font-black bg-slate-900 dark:bg-slate-800 px-2 py-1 rounded-lg mr-2 uppercase tracking-tighter">
                Live Status
              </span>
              <span class="text-slate-400 dark:text-slate-500 font-bold">Real-time sync</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-5">
        <!-- Sales Overview -->
        <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-[32px] shadow-sm p-10 border border-slate-200/50 dark:border-slate-800/50">
          <div class="flex items-center justify-between mb-10">
            <div>
              <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Income Overview</h3>
              <p class="text-xs text-slate-500 dark:text-slate-500 font-bold uppercase tracking-widest mt-1">Monthly performance</p>
            </div>
            <select class="bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-xs font-black px-4 py-2 outline-none cursor-pointer">
              <option>2024</option>
              <option>2023</option>
            </select>
          </div>
          
          <div class="h-72 flex items-end justify-between px-2 py-5 relative border-b border-slate-100 dark:border-slate-800">
            <div v-for="(v, i) in (stats?.monthly_sales || [0,0,0,0,0,0,0,0,0,0,0,0])" :key="i" class="flex flex-col items-center gap-4 group/bar w-full">
              <div class="relative w-2.5 bg-slate-100 dark:bg-slate-800 rounded-full h-48 overflow-hidden">
                <div 
                  class="absolute bottom-0 left-0 w-full bg-indigo-500 group-hover/bar:bg-indigo-400 transition-all duration-700 ease-out rounded-full"
                  :style="{ height: `${Math.max((v / (Math.max(...(stats?.monthly_sales || [1])) * 1.2)) * 100, 2)}%` }"
                ></div>
                <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-black px-2 py-1 rounded-md opacity-0 group-hover/bar:opacity-100 transition-opacity whitespace-nowrap z-20">
                  {{ formatNumber(v) }}
                </div>
              </div>
            </div>
          </div>
          <div class="flex justify-between items-center px-2 mt-4">
             <span v-for="m in ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']" :key="m" class="text-[10px] font-black text-slate-400 dark:text-slate-600 w-full text-center">{{ m }}</span>
          </div>
        </div>

        <!-- Expense Analytics -->
        <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-[32px] shadow-sm p-10 border border-slate-200/50 dark:border-slate-800/50">
          <div class="flex items-center justify-between mb-10">
            <div>
              <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Expense Analytics</h3>
              <p class="text-xs text-slate-500 dark:text-slate-500 font-bold uppercase tracking-widest mt-1">Categorized spending</p>
            </div>
            <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-500/10 rounded-xl flex items-center justify-center">
              <ArrowUpRight class="w-5 h-5 text-indigo-500" />
            </div>
          </div>
          
          <div class="space-y-6">
            <div v-for="ex in (stats?.expense_breakdown || [])" :key="ex.label" class="space-y-2">
              <div class="flex justify-between items-center text-xs font-bold uppercase tracking-widest">
                <span class="text-slate-500">{{ ex.label }}</span>
                <span class="text-slate-900 dark:text-white">{{ ex.val }}%</span>
              </div>
              <div class="h-2.5 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                <div 
                  class="h-full rounded-full transition-all duration-1000"
                  :class="ex.color"
                  :style="{ width: `${ex.val}%` }"
                ></div>
              </div>
            </div>
          </div>
          
          <div class="mt-12 pt-8 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between px-2">
            <div class="flex -space-x-3">
               <div v-for="i in 4" :key="i" class="w-10 h-10 rounded-full border-4 border-white dark:border-slate-900 bg-slate-200 dark:bg-slate-800 flex items-center justify-center text-[10px] font-black uppercase">
                 {{ ['A', 'D', 'M', 'S'][i-1] }}
               </div>
            </div>
            <p class="text-xs font-bold text-indigo-600 dark:text-indigo-400 cursor-pointer hover:underline">View Detailed Report &rarr;</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  Home, 
  ChevronRight, 
  ArrowUp,
  LayoutDashboard,
  ArrowUpRight,
  ShoppingBag,
} from 'lucide-vue-next'

definePageMeta({
  middleware: 'auth'
})

const { getAll } = useCrud()

const stats = ref(null)
const profile = ref(null)
const loading = ref(true)

const fetchDashboardData = async () => {
  loading.value = true
  try {
    const [statsRes, profileRes] = await Promise.all([
      getAll('/vendor/reports/overview'),
      getAll('/vendor/profile')
    ])
    stats.value = statsRes
    profile.value = profileRes
  } catch (error) {
    console.error('Error fetching dashboard data:', error)
  } finally {
    loading.value = false
  }
}

const formatNumber = (num) => {
  return new Intl.NumberFormat('en-IN').format(num)
}

onMounted(fetchDashboardData)
</script>
