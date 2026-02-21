<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <!-- Header Section -->
    <div class="max-w-7xl mx-auto mb-8">
      <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 font-medium mb-4">
        <NuxtLink to="/vendor/managed-shop" class="hover:text-indigo-600 transition-colors">Manage Shop</NuxtLink>
        <ChevronRight class="w-4 h-4" />
        <span class="text-slate-900 dark:text-white font-bold">Fraud Check</span>
      </div>
      
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="w-full sm:w-auto">
          <div class="flex items-center gap-3 mb-1">
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Fraud Check</h1>
            <span class="px-3 py-1 bg-gradient-to-r from-orange-500 to-rose-600 text-white text-[10px] font-black rounded-full uppercase tracking-wider shadow-lg shadow-rose-200">PRO AI</span>
          </div>
          <p class="text-slate-500 dark:text-slate-400 font-semibold">AI-powered risk analysis to protect your store from automated fraud and suspicious orders.</p>
        </div>
        <div class="flex gap-3 w-full sm:w-auto">
          <button class="w-full sm:w-auto flex items-center justify-center gap-2 px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold rounded-xl transition-all shadow-sm active:scale-95">
            <Download class="w-4 h-4 text-slate-400" />
            Export Report
          </button>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto space-y-8">
      <!-- KPI Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-slate-900 rounded-[24px] p-6 border border-slate-200/60 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
          <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-bl-full opacity-50 group-hover:scale-110 transition-transform"></div>
          <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600">
                <Search class="w-6 h-6" />
              </div>
            </div>
            <div class="text-sm font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Total Scanned</div>
            <div class="text-3xl font-black text-slate-900 dark:text-white">{{ stats.total_scanned.toLocaleString() }}</div>
          </div>
        </div>

        <div class="bg-white rounded-[24px] p-6 border border-slate-200/60 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
          <div class="absolute right-0 top-0 w-24 h-24 bg-rose-50 rounded-bl-full opacity-50 group-hover:scale-110 transition-transform"></div>
          <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-rose-100 rounded-2xl flex items-center justify-center text-rose-600">
                <ShieldAlert class="w-6 h-6" />
              </div>
            </div>
            <div class="text-sm font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">High Risk Blocked</div>
            <div class="text-3xl font-black text-rose-600">{{ stats.high_risk_blocked.toLocaleString() }}</div>
          </div>
        </div>

        <div class="bg-white rounded-[24px] p-6 border border-slate-200/60 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
          <div class="absolute right-0 top-0 w-24 h-24 bg-amber-50 rounded-bl-full opacity-50 group-hover:scale-110 transition-transform"></div>
          <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-600">
                <AlertTriangle class="w-6 h-6" />
              </div>
              <span v-if="stats.suspicious > 0" class="flex items-center gap-1 text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-lg">Action Needed</span>
            </div>
            <div class="text-sm font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Suspicious (Review)</div>
            <div class="text-3xl font-black text-amber-600">{{ stats.suspicious.toLocaleString() }}</div>
          </div>
        </div>

        <div class="bg-white rounded-[24px] p-6 border border-slate-200/60 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
          <div class="absolute right-0 top-0 w-24 h-24 bg-emerald-50 rounded-bl-full opacity-50 group-hover:scale-110 transition-transform"></div>
          <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600">
                <ShieldCheck class="w-6 h-6" />
              </div>
            </div>
            <div class="text-sm font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Safe Orders</div>
            <div class="text-3xl font-black text-emerald-600">{{ stats.safe_orders.toLocaleString() }}</div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- AI Protection Sensitivity -->
        <div class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-200/60 dark:border-slate-800 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.05)] dark:shadow-none p-8">
          <div class="flex flex-col h-full justify-between">
            <div>
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-black text-slate-800 dark:text-slate-100 tracking-tight">AI Protection Level</h3>
                <span class="text-xs font-black text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 px-2.5 py-1 rounded-full uppercase tracking-wider">Dynamic</span>
              </div>
              <p class="text-sm text-slate-500 dark:text-slate-400 font-semibold mb-8">Adjust the strictness of the fraud prevention algorithms.</p>
              
              <div class="space-y-10 py-2">
                <div class="relative h-3 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden border border-slate-200/60 dark:border-slate-700">
                  <div 
                    class="absolute inset-y-0 left-0 bg-gradient-to-r transition-all duration-700 rounded-full shadow-lg"
                    :class="getProtectionColor(stats.ai_protection_level)"
                    :style="{ width: getProtectionProgress(stats.ai_protection_level) }"
                  ></div>
                </div>
                <div class="flex justify-between text-[10px] font-black uppercase tracking-widest">
                  <span 
                    class="cursor-pointer transition-colors"
                    :class="stats.ai_protection_level === 'passive' ? 'text-emerald-500 scale-110' : 'text-slate-400 hover:text-emerald-400'"
                    @click="updateProtectionLevel('passive')"
                  >Passive</span>
                  <span 
                    class="cursor-pointer transition-all px-3 py-1 rounded-full -mt-2.5"
                    :class="stats.ai_protection_level === 'optimal' ? 'bg-indigo-50 text-indigo-600 scale-110' : 'text-slate-400 hover:text-indigo-400'"
                    @click="updateProtectionLevel('optimal')"
                  >Optimal</span>
                  <span 
                    class="cursor-pointer transition-colors"
                    :class="stats.ai_protection_level === 'aggressive' ? 'text-rose-500 scale-110' : 'text-slate-400 hover:text-rose-400'"
                    @click="updateProtectionLevel('aggressive')"
                  >Aggressive</span>
                </div>
              </div>
            </div>
            
            <p class="text-[11px] text-slate-400 dark:text-slate-500 font-bold leading-relaxed bg-slate-50 dark:bg-slate-800/50 p-4 rounded-2xl mt-8">
              <span class="text-slate-600 dark:text-slate-300 transition-colors duration-500">
                Current Setting (<span class="capitalize">{{ stats.ai_protection_level }}</span>):
              </span> 
              <span v-if="stats.ai_protection_level === 'passive'">Minimal filtering. Only obvious blocklist matches are flagged. Recommended for high-trust niches.</span>
              <span v-else-if="stats.ai_protection_level === 'optimal'">Blocks obvious bot traffic and high-risk orders while allowing manual review for ambiguous cases.</span>
              <span v-else-if="stats.ai_protection_level === 'aggressive'">Strict validation on all parameters. High sensitivity to IP and device mismatches.</span>
            </p>
          </div>
        </div>

        <!-- Spider Intelligence Widget -->
        <div class="lg:col-span-2 bg-slate-900 rounded-[32px] p-8 md:p-10 relative overflow-hidden group">
          <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,#4f46e5,transparent)] opacity-30"></div>
          <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-indigo-600/20 rounded-full blur-[80px] group-hover:bg-indigo-500/30 transition-colors duration-1000"></div>

          <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8 h-full">
            <div class="flex-1 space-y-6">
              <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20 shadow-xl">
                  <Bug class="w-6 h-6 text-indigo-400 animate-pulse" />
                </div>
                <div>
                  <h2 class="text-2xl font-black text-white tracking-widest uppercase">Spider Core</h2>
                  <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span class="text-xs font-bold text-emerald-400 uppercase tracking-widest">Active Monitoring</span>
                  </div>
                </div>
              </div>
              <p class="text-indigo-100/70 font-medium leading-relaxed max-w-md">
                Our advanced neural network continuously analyzes over 150 data points per transaction to identify sophisticated bot nets, velocity attacks, and proxy usage.
              </p>
              <NuxtLink to="/vendor/managed-shop/spider-intelligence" class="inline-flex flex-items-center gap-2 text-xs font-black text-white hover:text-indigo-300 uppercase tracking-widest transition-colors mt-2">
                Configure Spider Intel <ArrowRight class="w-4 h-4" />
              </NuxtLink>
            </div>
            
            <div class="grid grid-cols-2 gap-x-8 gap-y-6 bg-white/5 backdrop-blur-sm border border-white/10 rounded-3xl p-6 min-w-[240px]">
              <div>
                <span class="block text-2xl font-black text-white">99.8%</span>
                <span class="text-[10px] uppercase font-black tracking-widest text-indigo-400">Accuracy</span>
              </div>
              <div>
                <span class="block text-2xl font-black text-white">0ms</span>
                <span class="text-[10px] uppercase font-black tracking-widest text-white/50">Latency</span>
              </div>
              <div>
                <span class="block text-2xl font-black text-white">152ms</span>
                <span class="text-[10px] uppercase font-black tracking-widest text-white/50">Avg Score Time</span>
              </div>
              <div>
                <span class="block text-2xl font-black text-white">4.2k</span>
                <span class="text-[10px] uppercase font-black tracking-widest text-indigo-400">Threats Blocked</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Suspicious Orders Table -->
      <div class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-200/60 dark:border-slate-800 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.05)] dark:shadow-none overflow-hidden">
        <div class="p-8 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <h3 class="text-xl font-black text-slate-800 dark:text-white tracking-tight">Orders Requiring Review</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 font-semibold mt-1">These orders were flagged by the AI for manual verification.</p>
          </div>
          <div class="flex gap-2">
            <div class="relative">
              <input 
                v-model="searchQuery"
                type="text" 
                placeholder="Search Invoice..." 
                class="w-full sm:w-64 h-10 pl-10 pr-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500 text-sm text-slate-700 dark:text-slate-200 font-semibold"
              >
              <Search class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
            </div>
            <button class="h-10 px-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors flex items-center gap-2 text-sm shadow-sm group">
              <Filter class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition-colors" />
              Filter
            </button>
          </div>
        </div>

        <div class="overflow-x-auto custom-scrollbar relative min-h-[400px]">
          <div v-if="loading" class="absolute inset-0 bg-white/50 dark:bg-slate-900/50 backdrop-blur-[2px] z-20 flex items-center justify-center">
            <div class="flex flex-col items-center gap-3">
              <div class="w-10 h-10 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
              <span class="text-xs font-black text-indigo-600 uppercase tracking-widest">Analyzing Data...</span>
            </div>
          </div>

          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700">
                <th class="py-5 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">Order ID</th>
                <th class="py-5 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">Customer Details</th>
                <th class="py-5 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">Risk Score</th>
                <th class="py-5 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">Flags</th>
                <th class="py-5 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">Amount</th>
                <th class="py-5 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 text-right">Action</th>
              </tr>
            </thead>
            <tbody>
              <template v-if="fraudChecks.length > 0">
                <tr v-for="check in fraudChecks" :key="check.id" class="border-b border-slate-50 dark:border-slate-800 hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors group/row">
                  <td class="px-8 py-5">
                    <div class="font-bold text-slate-900 dark:text-slate-100">#{{ check.order.invoice_number }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ new Date(check.created_at).toLocaleString() }}</div>
                  </td>
                  <td class="px-8 py-5">
                    <div class="font-bold text-slate-800 dark:text-slate-200">{{ check.order.customer.name }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ check.order.customer.email }}</div>
                  </td>
                  <td class="px-8 py-5">
                    <div class="flex items-center gap-2">
                      <span 
                        class="w-8 h-8 rounded-full flex items-center justify-center font-black text-xs border shadow-sm"
                        :class="{
                          'bg-rose-100 text-rose-600 border-rose-200': check.risk_score >= 60,
                          'bg-amber-100 text-amber-600 border-amber-200': check.risk_score < 60 && check.risk_score >= 20,
                          'bg-emerald-100 text-emerald-600 border-emerald-200': check.risk_score < 20
                        }"
                      >
                        {{ check.risk_score }}
                      </span>
                      <span class="text-xs font-bold uppercase tracking-wider" :class="check.risk_score >= 60 ? 'text-rose-600' : check.risk_score >= 20 ? 'text-amber-600' : 'text-emerald-600'">
                        {{ check.risk_score >= 60 ? 'High Risk' : check.risk_score >= 20 ? 'Med Risk' : 'Low Risk' }}
                      </span>
                    </div>
                  </td>
                  <td class="px-8 py-5">
                    <div class="flex flex-wrap gap-1.5">
                      <span v-for="flag in check.flags" :key="flag" class="text-[10px] px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-bold border border-slate-200 dark:border-slate-700">
                        {{ flag }}
                      </span>
                    </div>
                  </td>
                  <td class="px-8 py-5">
                    <div class="font-black text-slate-900 dark:text-white">৳{{ check.order.total_amount }}</div>
                    <div class="text-xs text-slate-400 dark:text-slate-500 font-medium capitalize">{{ check.order.payment_method }}</div>
                  </td>
                  <td class="px-8 py-5 text-right">
                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover/row:opacity-100 transition-opacity">
                      <button 
                        @click="handleAction(check.id, 'approved')"
                        class="p-2 text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg transition-colors tooltip-target" 
                        title="Approve Order"
                      >
                        <Check class="w-4 h-4" />
                      </button>
                      <button 
                        @click="handleAction(check.id, 'rejected')"
                        class="p-2 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors tooltip-target" 
                        title="Reject Order"
                      >
                        <X class="w-4 h-4" />
                      </button>
                    </div>
                  </td>
                </tr>
              </template>
              <template v-else-if="!loading">
                <tr>
                  <td colspan="6" class="py-20 text-center">
                    <div class="flex flex-col items-center gap-4">
                      <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center border border-slate-100 dark:border-slate-700">
                        <ShieldCheck class="w-8 h-8 text-slate-300" />
                      </div>
                      <div class="max-w-xs">
                        <p class="text-slate-900 dark:text-white font-black">No suspicious orders found</p>
                        <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold mt-1">Excellent! Your store appears to be safe from bot threats at the moment.</p>
                      </div>
                    </div>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div class="p-10 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-sm">
          <span class="text-slate-500 dark:text-slate-400 font-medium">
            Showing {{ pagination.from || 0 }} to {{ pagination.to || 0 }} of {{ pagination.total }} entries
          </span>
          <div class="flex gap-1" v-if="pagination.last_page > 1">
            <button 
              @click="fetchFraudChecks(pagination.current_page - 1)"
              :disabled="pagination.current_page === 1"
              class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors disabled:opacity-50"
            >
              <ChevronLeft class="w-4 h-4" />
            </button>
            <button 
              v-for="page in pagination.last_page" 
              :key="page"
              @click="fetchFraudChecks(page)"
              class="w-8 h-8 flex items-center justify-center rounded-lg transition-all shadow-sm"
              :class="pagination.current_page === page ? 'bg-slate-900 dark:bg-indigo-600 text-white font-bold' : 'border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-medium hover:bg-slate-50 dark:hover:bg-slate-800'"
            >
              {{ page }}
            </button>
            <button 
              @click="fetchFraudChecks(pagination.current_page + 1)"
              :disabled="pagination.current_page === pagination.last_page"
              class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 text-slate-400 dark:text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors disabled:opacity-50"
            >
              <ChevronRight class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { 
  ChevronRight, 
  ChevronLeft,
  Download,
  Search,
  ShieldAlert,
  AlertTriangle,
  ShieldCheck,
  Bug,
  ArrowRight,
  Filter,
  Check,
  X
} from 'lucide-vue-next'

definePageMeta({
  middleware: 'auth'
})

const { getAll, createItem } = useCrud()
const router = useRouter()

const loading = ref(true)
const stats = ref({
  total_scanned: 0,
  high_risk_blocked: 0,
  suspicious: 0,
  safe_orders: 0,
  ai_protection_level: 'optimal'
})

const fraudChecks = ref([])
const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
  from: 0,
  to: 0
})

const searchQuery = ref('')
const perPage = ref(10)

const fetchStats = async () => {
  try {
    const response = await getAll('/vendor/fraud-checks/stats')
    stats.value = response
  } catch (error) {
    console.error('Failed to fetch stats:', error)
  }
}

const fetchFraudChecks = async (page = 1) => {
  loading.value = true
  try {
    const response = await getAll('/vendor/fraud-checks', {
      params: {
        page,
        per_page: perPage.value,
        search: searchQuery.value
      }
    })
    fraudChecks.value = response.data
    pagination.value = {
      current_page: response.current_page,
      last_page: response.last_page,
      total: response.total,
      from: response.from,
      to: response.to
    }
  } catch (error) {
    console.error('Failed to fetch fraud checks:', error)
  } finally {
    loading.value = false
  }
}

const updateProtectionLevel = async (level) => {
  try {
    await createItem('/vendor/fraud-checks/settings', { level })
    stats.value.ai_protection_level = level
    router.push('/vendor/fraud-check')
    // Show success notification here if available
  } catch (error) {
    console.error('Failed to update protection level:', error)
  }
}

const handleAction = async (id, status) => {
  try {
    await createItem(`/vendor/fraud-checks/${id}/action`, { status })
    // Refresh data
    fetchStats()
    fetchFraudChecks(pagination.value.current_page)
    router.push('/vendor/fraud-check')
  } catch (error) {
    console.error(`Failed to ${status} order:`, error)
  }
}

const debounceTimeout = ref(null)
watch(searchQuery, () => {
  clearTimeout(debounceTimeout.value)
  debounceTimeout.value = setTimeout(() => {
    fetchFraudChecks(1)
  }, 500)
})

onMounted(() => {
  fetchStats()
  fetchFraudChecks()
})

const getProtectionProgress = (level) => {
  if (level === 'passive') return '33%'
  if (level === 'optimal') return '66%'
  if (level === 'aggressive') return '100%'
  return '0%'
}

const getProtectionColor = (level) => {
  if (level === 'passive') return 'from-emerald-500 to-emerald-400'
  if (level === 'optimal') return 'from-emerald-500 via-indigo-500 to-indigo-400'
  if (level === 'aggressive') return 'from-emerald-500 via-indigo-500 to-rose-500'
  return 'from-slate-400 to-slate-300'
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  height: 8px;
  width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
  border: 2px solid #f1f5f9;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* For Firefox */
.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 #f1f5f9;
}
</style>