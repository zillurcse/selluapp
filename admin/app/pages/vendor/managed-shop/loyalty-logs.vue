<template>
  <div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Loyalty Point Logs</h1>
        <p class="text-sm text-gray-500 mt-1">Track all loyalty points awarded and redeemed</p>
      </div>
      <div class="flex items-center gap-3">
        <NuxtLink 
          to="/vendor/managed-shop/loyalty-program"
          class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition"
        >
          <Settings class="w-4 h-4" />
          Program Settings
        </NuxtLink>
      </div>
    </div>

    <!-- Filters & Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600">
            <Gift class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Awarded</p>
            <p class="text-2xl font-black text-gray-900 tabular-nums">{{ backendStats.total_awarded }} Pts</p>
          </div>
        </div>
      </div>
      <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
            <Users class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Active Customers</p>
            <p class="text-2xl font-black text-gray-900 tabular-nums">{{ backendStats.active_customers }}</p>
          </div>
        </div>
      </div>
      <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center text-rose-600">
            <ShoppingBag class="w-6 h-6" />
          </div>
          <div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Redeemed</p>
            <p class="text-2xl font-black text-gray-900 tabular-nums">{{ backendStats.total_redeemed }} Pts</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
      <div class="p-6 border-b border-gray-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <h2 class="font-bold text-gray-900 whitespace-nowrap">Transaction History</h2>
        <div class="flex items-center gap-3 w-full sm:w-auto">
           <div class="relative w-full sm:w-64 group">
             <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-purple-600" />
             <input 
               v-model="search" 
               type="text" 
               placeholder="Search customers or details..." 
               class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-100 rounded-xl text-sm outline-none focus:ring-2 focus:ring-purple-600/10 focus:border-purple-600 transition"
             />
           </div>
           <button @click="fetchLogs" class="p-2.5 hover:bg-gray-50 rounded-xl text-gray-400 transition border border-gray-50" title="Refresh">
             <RefreshCw :class="{'animate-spin': loading}" class="w-4 h-4" />
           </button>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/50">
              <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Customer</th>
              <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Transaction</th>
              <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Points</th>
              <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="loading" v-for="i in 5" :key="i" class="animate-pulse">
              <td class="px-6 py-4"><div class="h-4 bg-gray-100 rounded w-24"></div></td>
              <td class="px-6 py-4"><div class="h-4 bg-gray-100 rounded w-48"></div></td>
              <td class="px-6 py-4 text-right"><div class="h-4 bg-gray-100 rounded w-12 ml-auto"></div></td>
              <td class="px-6 py-4"><div class="h-4 bg-gray-100 rounded w-32"></div></td>
            </tr>
            <tr v-else-if="logs.length === 0" class="text-center py-12">
               <td colspan="4" class="px-6 py-12 text-gray-400 italic">No transactions found matching your criteria.</td>
            </tr>
            <tr v-for="log in logs" :key="log.id" class="hover:bg-gray-50/50 transition">
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-indigo-50 flex items-center justify-center text-[10px] font-bold text-indigo-600 shadow-sm">
                    {{ getInitials(log.customer) }}
                  </div>
                  <div>
                    <p class="text-sm font-bold text-gray-900">{{ log.customer?.first_name }} {{ log.customer?.last_name }}</p>
                    <p class="text-[10px] text-gray-400 font-medium">{{ log.customer?.email }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <p class="text-sm text-gray-600 font-medium">{{ log.description }}</p>
                <p v-if="log.order_id" class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Order #{{ log.order_id }}</p>
              </td>
              <td class="px-6 py-4 text-right">
                <span :class="log.points >= 0 ? 'text-emerald-600' : 'text-rose-600'" class="text-sm font-black italic">
                  {{ log.points >= 0 ? '+' : '' }}{{ log.points }}
                </span>
                <span class="text-[10px] text-gray-400 font-bold ml-1 uppercase">Pts</span>
              </td>
              <td class="px-6 py-4">
                 <p class="text-sm text-gray-900 font-medium">{{ formatDate(log.created_at) }}</p>
                 <p class="text-[10px] text-gray-400 tabular-nums">{{ formatTime(log.created_at) }}</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Placeholder -->
      <div v-if="meta.last_page > 1" class="p-6 bg-gray-50/30 border-t border-gray-50 flex items-center justify-between">
         <p class="text-xs text-gray-500 font-medium italic">Showing {{ meta.from }} to {{ meta.to }} of {{ meta.total }} records</p>
         <div class="flex items-center gap-2">
            <button 
              @click="page--" 
              :disabled="page === 1"
              class="px-4 py-2 border border-gray-200 rounded-xl text-xs font-bold hover:bg-white transition disabled:opacity-50 shadow-sm"
            >Prev</button>
            <button 
              @click="page++" 
              :disabled="page === meta.last_page"
              class="px-4 py-2 border border-gray-200 rounded-xl text-xs font-bold hover:bg-white transition disabled:opacity-50 shadow-sm"
            >Next</button>
         </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  Gift, 
  Settings, 
  Users, 
  ShoppingBag, 
  RefreshCw,
  Search,
  BarChart2
} from 'lucide-vue-next'
import { debounce } from 'lodash-es'

const logs = ref([])
const loading = ref(false)
const page = ref(1)
const search = ref('')
const meta = ref({})
const backendStats = ref({
  total_awarded: 0,
  total_redeemed: 0,
  active_customers: 0
})

const fetchLogs = async () => {
    loading.value = true
    try {
        const { data } = await useApiFetch('/vendor/loyalty-logs', {
            params: { 
                page: page.value,
                search: search.value,
                limit: 15
            }
        })
        
        if (data.value) {
            logs.value = data.value.logs?.data || []
            backendStats.value = data.value.stats || {
                total_awarded: 0,
                total_redeemed: 0,
                active_customers: 0
            }
            meta.value = {
                total: data.value.logs?.total,
                from: data.value.logs?.from,
                to: data.value.logs?.to,
                last_page: data.value.logs?.last_page
            }
        }
    } catch (e) {
        console.error('Failed to fetch loyalty logs:', e)
    } finally {
        loading.value = false
    }
}

// Debounced search to avoid excessive API calls
const debouncedSearch = debounce(() => {
    page.value = 1
    fetchLogs()
}, 500)

watch(search, debouncedSearch)
watch(page, fetchLogs)

const getInitials = (customer) => {
    if (!customer) return '??'
    const fn = (customer.first_name || '').charAt(0)
    const ln = (customer.last_name || '').charAt(0)
    return (fn + ln).toUpperCase() || customer.email?.charAt(0).toUpperCase() || '?'
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    })
}

const formatTime = (dateString) => {
    return new Date(dateString).toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
    })
}

onMounted(fetchLogs)
</script>
