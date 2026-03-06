<template>
  <div class="max-w-7xl mx-auto p-10 space-y-8">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div class="flex items-center text-sm text-gray-500 bg-white px-5 py-2.5 rounded-full border border-gray-100 shadow-sm w-fit">
        <NuxtLink to="/vendor/reports" class="hover:text-emerald-600 font-medium transition-colors">Reports</NuxtLink>
        <ChevronRight class="w-4 h-4 mx-2 text-gray-300" />
        <span class="font-bold text-gray-900">Earnings & Payouts</span>
      </div>
      
      <div class="flex items-center gap-3">
        <button class="flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-2.5 rounded-xl text-sm font-bold text-white hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg shadow-emerald-500/25 ring-1 ring-emerald-500/50">
           <Wallet class="w-4 h-4" /> Request Payout
        </button>
      </div>
    </div>

    <!-- Header Section -->
    <div class="bg-gradient-to-br from-gray-900 via-[#1a1c1e] to-gray-800 rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl border border-gray-800">
      <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-5"></div>
      <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
      
      <div class="relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-8">
        <div>
          <div class="flex items-center gap-3 mb-4">
             <div class="p-2 bg-emerald-500/20 rounded-xl border border-emerald-500/30">
                <Banknote class="w-6 h-6 text-emerald-400" />
             </div>
             <h1 class="text-4xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-white to-gray-400">Financial Summary</h1>
          </div>
          <p class="text-gray-400 font-medium max-w-md text-sm leading-relaxed">Track your net earnings, pending balances, and scheduled payouts in one secure place.</p>
        </div>
        <div class="flex items-center gap-6 bg-white/5 backdrop-blur-xl rounded-3xl p-6 border border-white/10 ring-1 ring-white/5 shadow-2xl">
           <div class="flex items-center gap-5">
              <div class="w-14 h-14 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-emerald-500/30">
                 <Wallet class="w-7 h-7" />
              </div>
              <div>
                 <div class="text-[11px] font-black uppercase tracking-widest text-emerald-400/80 mb-1">Available to Payout</div>
                 <div class="text-3xl font-black tracking-tighter">
                    <span class="text-gray-400 text-2xl pr-1">৳</span>{{ pending ? '...' : (earningsData?.available_payout?.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) || '0.00') }}
                 </div>
              </div>
           </div>
        </div>
      </div>
    </div>

    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="flex flex-col items-center gap-4">
         <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-emerald-500"></div>
         <div class="text-sm font-bold text-gray-400 animate-pulse">Loading financial data...</div>
      </div>
    </div>

    <template v-else>
      <!-- KPI Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
         <!-- Net Earnings -->
         <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.03)] relative group overflow-hidden transition-all hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.05)] hover:-translate-y-1">
            <div class="relative z-10">
               <div class="flex items-center justify-between mb-6">
                  <div class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Net Revenue</div>
                  <div class="p-2 bg-blue-50 text-blue-600 rounded-xl">
                     <ArrowUpRight class="w-4 h-4" />
                  </div>
               </div>
               <div class="text-4xl font-black text-gray-900 mb-2 tracking-tighter"><span class="text-gray-400 text-2xl pr-1">৳</span>{{ earningsData?.net_earnings?.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) || '0.00' }}</div>
               <div class="flex items-center text-[11px] font-black px-2.5 py-1 rounded-full w-fit mt-4"
                    :class="(earningsData?.growth_percentage || 0) >= 0 ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50'">
                  {{ (earningsData?.growth_percentage || 0) >= 0 ? '+' : '' }}{{ earningsData?.growth_percentage || 0 }}% vs last month
               </div>
            </div>
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-blue-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700 blur-2xl"></div>
            <Banknote class="absolute -right-4 -bottom-4 w-28 h-28 text-blue-600 opacity-5 group-hover:rotate-12 transition-transform duration-500" />
         </div>

         <!-- Gross Sales -->
         <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.03)] relative group overflow-hidden transition-all hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.05)] hover:-translate-y-1">
            <div class="relative z-10">
               <div class="flex items-center justify-between mb-6">
                  <div class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Gross Sales</div>
                  <div class="p-2 bg-indigo-50 text-indigo-600 rounded-xl">
                     <Clock class="w-4 h-4" />
                  </div>
               </div>
               <div class="text-4xl font-black text-indigo-600 mb-2 tracking-tighter"><span class="text-indigo-300 text-2xl pr-1">৳</span>{{ earningsData?.total_sales?.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) || '0.00' }}</div>
               <div class="text-[11px] font-bold text-gray-400 mt-4 uppercase tracking-widest">Total Sales Value</div>
            </div>
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-indigo-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700 blur-2xl"></div>
            <TrendingUp class="absolute -right-4 -bottom-4 w-28 h-28 text-indigo-600 opacity-5 group-hover:rotate-12 transition-transform duration-500" />
         </div>

         <!-- Platform Fees -->
         <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.03)] relative group overflow-hidden transition-all hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.05)] hover:-translate-y-1">
            <div class="relative z-10">
               <div class="flex items-center justify-between mb-6">
                  <div class="text-[11px] font-black text-gray-400 uppercase tracking-widest">Platform Fees</div>
                  <div class="p-2 bg-rose-50 text-rose-600 rounded-xl">
                     <CheckCircle2 class="w-4 h-4" />
                  </div>
               </div>
               <div class="text-4xl font-black text-gray-900 mb-2 tracking-tighter"><span class="text-gray-400 text-2xl pr-1">৳</span>{{ earningsData?.platform_fee?.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) || '0.00' }}</div>
               <div class="text-[11px] font-bold text-gray-400 mt-4 uppercase tracking-widest">{{ earningsData?.commission_rate || 10 }}% Commission Rate</div>
            </div>
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-rose-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-700 blur-2xl"></div>
            <PieChart class="absolute -right-4 -bottom-4 w-28 h-28 text-rose-600 opacity-5 group-hover:rotate-12 transition-transform duration-500" />
         </div>
      </div>

      <!-- Payout History -->
      <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm overflow-hidden mt-8">
        <div class="px-8 py-6 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gray-50/30">
          <div>
            <h3 class="text-xl font-black text-gray-900 tracking-tight">Financial Ledger</h3>
            <p class="text-xs font-bold text-gray-400 mt-1">Recent sales and payout history</p>
          </div>
          <div class="flex bg-gray-100/50 p-1 rounded-xl w-fit border border-gray-200/50">
              <button 
                  @click="activeTab = 'all'"
                  :class="activeTab === 'all' ? 'shadow-sm bg-white text-gray-900 ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-900 transition-colors'"
                  class="px-5 py-2 text-xs font-bold rounded-lg">All Activity</button>
              <button 
                  @click="activeTab = 'payouts'"
                  :class="activeTab === 'payouts' ? 'shadow-sm bg-white text-gray-900 ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-900 transition-colors'"
                  class="px-5 py-2 text-xs font-bold rounded-lg">Payouts</button>
              <button 
                  @click="activeTab = 'sales'"
                  :class="activeTab === 'sales' ? 'shadow-sm bg-white text-gray-900 ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-900 transition-colors'"
                  class="px-5 py-2 text-xs font-bold rounded-lg">Sales</button>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="border-b border-gray-100">
                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50/30">Transaction Detail</th>
                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50/30">Date</th>
                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50/30">Type</th>
                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50/30 text-right">Amount</th>
                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50/30">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-for="(tx, index) in filteredLedger" :key="index" class="hover:bg-gray-50/80 transition-colors group">
                <td class="px-8 py-5">
                   <div class="flex items-center gap-4">
                      <div class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
                           :class="tx.source === 'order' ? 'bg-indigo-50 text-indigo-600 group-hover:bg-indigo-100' : 'bg-emerald-50 text-emerald-600 group-hover:bg-emerald-100'">
                         <ShoppingCart v-if="tx.source === 'order'" class="w-5 h-5" />
                         <ArrowDownLeft v-else class="w-5 h-5" />
                      </div>
                      <div>
                         <div class="font-bold text-gray-900 text-sm">#TRX-{{ tx.id }}</div>
                         <div class="text-xs font-medium text-gray-400">{{ tx.source === 'order' ? 'Order Revenue' : (tx.type === 'deposit' ? 'Deposit' : 'Withdrawal Request') }}</div>
                      </div>
                   </div>
                </td>
                <td class="px-8 py-5 text-sm font-medium text-gray-500">
                   <div class="font-bold text-gray-700">{{ new Date(tx.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) }}</div>
                   <div class="text-xs text-gray-400">{{ new Date(tx.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) }}</div>
                </td>
                <td class="px-8 py-5">
                   <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md border border-gray-100 bg-white shadow-sm">
                      <div class="w-1.5 h-1.5 rounded-full" :class="tx.source === 'order' ? 'bg-indigo-500' : 'bg-emerald-500'"></div>
                      <span class="text-[11px] font-bold tracking-wide" :class="tx.source === 'order' ? 'text-indigo-700' : 'text-emerald-700'">{{ tx.source === 'order' ? 'Store Sale' : 'Bank Payout' }}</span>
                   </div>
                </td>
                <td class="px-8 py-5 text-right">
                   <div class="font-black text-base tracking-tight" :class="tx.source === 'order' ? 'text-emerald-600' : 'text-gray-900'">
                      {{ tx.source === 'order' || tx.type === 'deposit' ? '+' : '-' }}৳{{ Number(tx.amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
                   </div>
                </td>
                <td class="px-8 py-5">
                  <span class="px-3 py-1 bg-emerald-50 border border-emerald-100/50 text-emerald-600 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm">Completed</span>
                </td>
              </tr>
              <tr v-if="!filteredLedger.length">
                <td colspan="5" class="px-8 py-10 text-center text-sm text-gray-500 font-medium">No recent transactions found.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { 
  ChevronRight, 
  Wallet,
  ArrowUpRight,
  Banknote,
  Clock,
  CheckCircle2,
  TrendingUp,
  PieChart,
  ShoppingCart,
  ArrowDownLeft
} from 'lucide-vue-next'
import { ref, computed, onMounted } from 'vue'

const { getAll } = useCrud()

const earningsData = ref(null)
const pending = ref(true)

onMounted(async () => {
  try {
    earningsData.value = await getAll('/vendor/reports/earnings')
  } catch (err) {
    console.error("Failed to load earnings data:", err)
  } finally {
    pending.value = false
  }
})

const activeTab = ref('all')

const filteredLedger = computed(() => {
  if (!earningsData.value || !earningsData.value.ledger) return []
  if (activeTab.value === 'payouts') {
    return earningsData.value.ledger.filter(tx => tx.source === 'transaction')
  }
  if (activeTab.value === 'sales') {
    return earningsData.value.ledger.filter(tx => tx.source === 'order')
  }
  return earningsData.value.ledger
})

definePageMeta({
  middleware: 'auth'
})
</script>
