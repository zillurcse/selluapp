<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <!-- Breadcrumbs -->
    <div class="max-w-7xl mx-auto mb-8">
      <div class="flex items-center gap-2 text-sm text-slate-500 font-medium mb-4">
        <NuxtLink to="/vendor/managed-shop" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Manage Shop</NuxtLink>
        <ChevronRight class="w-4 h-4" />
        <span class="text-slate-900 dark:text-white font-bold">Loyalty Program</span>
      </div>
      
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
          <h1 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight">Loyalty Program</h1>
          <p class="text-slate-500 dark:text-slate-400 font-semibold mt-1 italic">Reward your loyal customers and increase repeat purchases.</p>
        </div>
        
        <div class="flex items-center gap-4">
          <NuxtLink to="/vendor/managed-shop/loyalty-logs" class="flex items-center gap-2 px-6 py-3 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl text-sm font-black text-slate-600 dark:text-slate-400 hover:text-purple-600 transition-all shadow-sm group/btn">
            <BarChart2 class="w-4 h-4 group-hover/btn:scale-110 transition-transform" />
            Activity Logs
          </NuxtLink>
          <div class="flex items-center gap-4 bg-white dark:bg-slate-900 p-2 pl-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <span class="text-sm font-bold text-slate-600 dark:text-slate-400 uppercase tracking-widest">Status</span>
            <button 
              @click="form.is_enabled = !form.is_enabled"
              :class="['w-16 h-8 rounded-full transition-all duration-500 relative shadow-inner', form.is_enabled ? 'bg-emerald-500' : 'bg-slate-200 dark:bg-slate-800']"
            >
              <div :class="['w-6 h-6 bg-white rounded-full absolute top-1 transition-all duration-500 shadow-md', form.is_enabled ? 'left-9' : 'left-1']"></div>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="w-10 h-10 border-4 border-purple-600 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <div v-else class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Configuration Card -->
      <div class="lg:col-span-2 space-y-8">
        <div class="bg-white dark:bg-slate-900 rounded-[40px] border border-slate-100 dark:border-slate-800 shadow-sm p-10 relative overflow-hidden group">
          <div class="absolute top-0 right-0 p-12 opacity-[0.03] group-hover:scale-110 transition-transform duration-1000">
             <Gift class="w-64 h-64 text-slate-900 dark:text-white" />
          </div>
          
          <h2 class="text-2xl font-black text-slate-900 dark:text-white mb-8 flex items-center gap-3">
             <Settings class="w-6 h-6 text-purple-600" />
             Rules & Configuration
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <!-- Earning Rate -->
            <div class="space-y-4">
              <label class="block">
                <span class="text-sm font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-2 block">Earning Rate</span>
                <span class="text-xs text-slate-400 font-bold block mb-4 italic">How many points per 100 BDT spent?</span>
              </label>
              <div class="relative group/input">
                <input 
                  v-model="form.point_earning_rate"
                  type="number" 
                  class="w-full h-16 pl-6 pr-16 bg-slate-50 dark:bg-slate-800/50 border-2 border-transparent focus:border-purple-500 focus:bg-white dark:focus:bg-slate-800 rounded-2xl outline-none transition-all font-black text-xl text-slate-900 dark:text-white"
                >
                <span class="absolute right-6 top-1/2 -translate-y-1/2 font-black text-slate-400">Pts</span>
              </div>
            </div>

            <!-- Point Value -->
            <div class="space-y-4">
              <label class="block">
                <span class="text-sm font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-2 block">Point Redemption Value</span>
                <span class="text-xs text-slate-400 font-bold block mb-4 italic">How much is 1 point worth in BDT?</span>
              </label>
              <div class="relative group/input">
                <input 
                  v-model="form.point_value"
                  type="number" 
                  step="0.01"
                  class="w-full h-16 pl-6 pr-16 bg-slate-50 dark:bg-slate-800/50 border-2 border-transparent focus:border-purple-500 focus:bg-white dark:focus:bg-slate-800 rounded-2xl outline-none transition-all font-black text-xl text-slate-900 dark:text-white"
                >
                <span class="absolute right-6 top-1/2 -translate-y-1/2 font-black text-slate-400">BDT</span>
              </div>
            </div>

            <!-- Min Order Total -->
            <div class="space-y-4 md:col-span-2">
              <label class="block">
                <span class="text-sm font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-2 block">Minimum Order Amount</span>
                <span class="text-xs text-slate-400 font-bold block mb-4 italic">Minimum purchase required to start earning points.</span>
              </label>
              <div class="relative group/input">
                <input 
                  v-model="form.min_order_total"
                  type="number" 
                  class="w-full h-16 pl-6 pr-16 bg-slate-50 dark:bg-slate-800/50 border-2 border-transparent focus:border-purple-500 focus:bg-white dark:focus:bg-slate-800 rounded-2xl outline-none transition-all font-black text-xl text-slate-900 dark:text-white"
                >
                <span class="absolute right-6 top-1/2 -translate-y-1/2 font-black text-slate-400">BDT</span>
              </div>
            </div>
          </div>

          <div class="mt-12 flex items-center justify-between p-8 bg-purple-50 dark:bg-purple-900/10 rounded-[32px] border border-purple-100 dark:border-purple-900/20">
             <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center shadow-sm">
                   <Info class="w-6 h-6 text-purple-600" />
                </div>
                <div>
                  <p class="text-sm font-black text-slate-900 dark:text-white tracking-tight">Earning Example:</p>
                  <p class="text-xs text-slate-500 dark:text-slate-400 font-bold">A purchase of ৳1,000 will grant <span class="text-purple-600">{{ (1000 / 100) * form.point_earning_rate }}</span> points (Worth ৳{{ ((1000 / 100) * form.point_earning_rate) * form.point_value }}).</p>
                </div>
             </div>
             <button 
              @click="saveSettings" 
              :disabled="saving" 
              class="px-10 h-14 bg-slate-900 dark:bg-slate-100 text-white dark:text-slate-900 font-black rounded-2xl transition-all shadow-xl hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-3"
             >
               <span v-if="saving">Updating...</span>
               <span v-else>Save Strategy</span>
               <ArrowRight v-if="!saving" class="w-5 h-5" />
             </button>
          </div>
        </div>
      </div>

      <!-- Stats / Summary Column -->
      <div class="space-y-8">
        <div class="bg-slate-900 dark:bg-slate-100 rounded-[40px] p-10 text-white dark:text-slate-900 shadow-2xl relative overflow-hidden group">
           <div class="relative z-10">
              <h3 class="text-sm font-black uppercase tracking-[0.2em] mb-8 opacity-60">Program Health</h3>
              <div class="space-y-8">
                 <div>
                    <div class="text-[10px] font-black uppercase mb-1 opacity-50">Points in Circulation</div>
                    <div class="text-4xl font-black tabular-nums">{{ stats.total_points_circulation?.toLocaleString() || 0 }}</div>
                 </div>
                 <div class="h-[1px] bg-white/10 dark:bg-slate-900/10"></div>
                 <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="text-[10px] font-black uppercase mb-1 opacity-50">Avg. Points / Order</div>
                        <div class="text-xl font-black">{{ Math.round(stats.avg_points_per_order || 0) }} Pts</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-black uppercase mb-1 opacity-50">Active Customers</div>
                        <div class="text-xl font-black text-emerald-400 dark:text-emerald-600">{{ stats.active_customers_count || 0 }}</div>
                    </div>
                 </div>
              </div>
           </div>
           <div class="absolute -right-4 -bottom-4 opacity-[0.05] group-hover:rotate-12 transition-transform duration-1000">
              <Zap class="w-48 h-48" />
           </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-[40px] border border-slate-100 dark:border-slate-800 p-8 shadow-sm">
           <h3 class="text-lg font-black text-slate-900 dark:text-white mb-6">Recent Activity</h3>
           <div class="space-y-6">
              <div v-for="log in stats.recent_logs" :key="log.id" class="flex gap-4">
                 <div class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center shrink-0">
                    <User class="w-5 h-5 text-slate-400" />
                 </div>
                 <div class="overflow-hidden">
                    <p class="text-sm font-black text-slate-800 dark:text-white truncate">
                      {{ log.customer?.first_name || 'Customer' }} {{ log.points >= 0 ? 'earned' : 'redeemed' }} {{ Math.abs(log.points) }} pts
                    </p>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">{{ timeSince(log.created_at) }}</p>
                 </div>
              </div>
              <div v-if="!stats.recent_logs?.length" class="text-center py-4 text-xs text-slate-400 font-bold uppercase tracking-widest italic opacity-50">
                 No recent activity
              </div>
              <button class="w-full py-4 border-2 border-dashed border-slate-100 dark:border-slate-800 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 hover:border-purple-200 hover:text-purple-600 transition-all">
                 View All Journals
              </button>
           </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  middleware: 'auth',
  permissions: 'loyalty.view'
})
import { 
  ChevronRight, 
  Gift, 
  BarChart2,
  Settings, 
  Info, 
  ArrowRight, 
  Zap, 
  User 
} from 'lucide-vue-next'
import { reactive, ref, onMounted } from 'vue'

const { $toast } = useNuxtApp()
const { getAll, createItem } = useCrud()

const stats = ref({
  total_points_circulation: 0,
  avg_points_per_order: 0,
  active_customers_count: 0,
  recent_logs: []
})

const form = reactive({
  is_enabled: false,
  point_earning_rate: 1,
  min_order_total: 0,
  point_value: 0.1
})

const pending = ref(true)
const saving = ref(false)

const loadSettings = async () => {
  try {
    pending.value = true
    const response = await getAll('/vendor/loyalty-settings')
    if (response) {
      Object.assign(form, response.settings)
      Object.assign(stats.value, response.stats)
    }
  } catch (error) {
    console.error('Failed to load loyalty settings:', error)
  } finally {
    pending.value = false
  }
}

const timeSince = (date) => {
  const seconds = Math.floor((new Date() - new Date(date)) / 1000)
  let interval = seconds / 31536000
  if (interval > 1) return Math.floor(interval) + " years ago"
  interval = seconds / 2592000
  if (interval > 1) return Math.floor(interval) + " months ago"
  interval = seconds / 86400
  if (interval > 1) return Math.floor(interval) + " days ago"
  interval = seconds / 3600
  if (interval > 1) return Math.floor(interval) + " hours ago"
  interval = seconds / 60
  if (interval > 1) return Math.floor(interval) + " minutes ago"
  return Math.floor(seconds) + " seconds ago"
}

const saveSettings = async () => {
  try {
    saving.value = true
    await createItem('/vendor/loyalty-settings', form)
    navigateTo('/vendor/managed-shop/loyalty-logs')
  } catch (error) {
    $toast.error('Failed to save settings')
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  loadSettings()
})
</script>
