<template>
  <div class="p-10 lg:p-10 bg-[#f8fafc] min-h-screen">
    <div class="max-w-[1000px] mx-auto">
      <!-- Header -->
      <div class="flex items-center justify-between mb-10">
        <div class="flex items-center gap-5">
          <NuxtLink 
            to="/vendor/upgrade-package" 
            class="w-12 h-12 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-slate-600 hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all active:scale-95 shadow-sm group"
          >
            <ArrowLeft class="w-6 h-6 group-hover:-translate-x-1 transition-transform" />
          </NuxtLink>
          <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Purchase History</h1>
            <p class="text-slate-500 font-medium mt-1">Track your subscription upgrades and payments.</p>
          </div>
        </div>
      </div>

      <!-- History List -->
      <div v-if="loading" class="space-y-4">
        <div v-for="i in 3" :key="i" class="h-24 bg-white border border-slate-100 rounded-3xl animate-pulse"></div>
      </div>

      <div v-else-if="history.length === 0" class="bg-white border border-slate-100 rounded-[40px] p-20 text-center">
        <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center mx-auto mb-6">
          <History class="w-10 h-10 text-slate-300" />
        </div>
        <h2 class="text-2xl font-black text-slate-900 mb-2">No History Found</h2>
        <p class="text-slate-500 font-medium mb-8">You haven't requested any package upgrades yet.</p>
        <NuxtLink 
          to="/vendor/upgrade-package"
          class="inline-flex items-center gap-2 px-8 py-4 bg-slate-900 text-white rounded-2xl font-black hover:bg-black transition-all shadow-lg"
        >
          View Plans <ArrowRight class="w-5 h-5" />
        </NuxtLink>
      </div>

      <div v-else class="space-y-4">
        <div 
          v-for="item in history" 
          :key="item.id"
          class="bg-white border border-slate-100 rounded-[32px] p-6 pr-8 flex flex-col md:flex-row md:items-center justify-between gap-6 hover:shadow-xl hover:border-slate-200 transition-all group"
        >
          <div class="flex items-center gap-6">
            <div :class="['w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg', getStatusColor(item.status).bg]">
               <Zap v-if="item.package?.name?.toLowerCase().includes('basic')" class="w-8 h-8 text-white" />
               <Rocket v-else-if="item.package?.name?.toLowerCase().includes('growth')" class="w-8 h-8 text-white" />
               <Crown v-else class="w-8 h-8 text-white" />
            </div>
            <div>
              <h3 class="text-xl font-black text-slate-900">{{ item.package?.name }} Plan</h3>
              <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1">
                <span class="text-sm font-bold text-slate-400 flex items-center gap-1.5">
                  <Calendar class="w-4 h-4" /> {{ formatDate(item.created_at) }}
                </span>
                <span class="text-sm font-bold text-slate-400 flex items-center gap-1.5 uppercase tracking-wider">
                  <CreditCard class="w-4 h-4" /> {{ item.payment_method }}
                </span>
                <span class="text-sm font-bold text-slate-400 flex items-center gap-1.5 uppercase tracking-wider">
                  <RefreshCw class="w-4 h-4" /> {{ item.billing_cycle }}
                </span>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-between md:justify-end gap-10">
            <div class="text-right">
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Amount</p>
              <p class="text-2xl font-black text-slate-900">${{ parseFloat(item.amount).toFixed(2) }}</p>
            </div>
            <div :class="['px-5 py-2 rounded-full text-xs font-black uppercase tracking-widest shadow-sm', getStatusColor(item.status).text, getStatusColor(item.status).innerBg]">
              {{ item.status }}
            </div>
          </div>
          
          <!-- Notes/Ref Toggle -->
          <div v-if="item.notes" class="md:hidden pt-4 border-t border-slate-50">
             <p class="text-xs font-bold text-slate-400 italic">{{ item.notes }}</p>
          </div>
        </div>
      </div>

       <!-- Info Card -->
       <div class="mt-12 bg-indigo-600 rounded-[40px] p-10 text-white relative overflow-hidden shadow-2xl shadow-indigo-200">
          <div class="absolute -right-20 -top-20 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl"></div>
          <div class="relative z-10 flex flex-col md:flex-row items-center gap-8">
            <div class="w-20 h-20 bg-white/20 rounded-3xl backdrop-blur-md flex items-center justify-center flex-shrink-0">
               <Info class="w-10 h-10 text-white" />
            </div>
            <div>
              <h2 class="text-2xl font-black mb-2">Admin Verification</h2>
              <p class="text-indigo-100 font-medium leading-relaxed">
                Manual payments (bKash, Nagad, etc.) are verified by our team within 24 hours. Once verified, your status will change from <span class="text-white font-bold italic">pending</span> to <span class="text-white font-bold italic">active</span>.
              </p>
            </div>
          </div>
       </div>
    </div>
  </div>
</template>

<script setup>
import { 
  ArrowLeft, 
  ArrowRight, 
  History, 
  Zap, 
  Rocket, 
  Crown, 
  Calendar, 
  CreditCard, 
  RefreshCw,
  Info
} from 'lucide-vue-next'

const { getAll } = useCrud()
const history = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const res = await getAll('/vendor/packages/history')
    history.value = res || []
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  })
}

const getStatusColor = (status) => {
  switch (status?.toLowerCase()) {
    case 'active':
      return { bg: 'bg-emerald-500', text: 'text-emerald-600', innerBg: 'bg-emerald-50' }
    case 'pending':
      return { bg: 'bg-amber-500', text: 'text-amber-600', innerBg: 'bg-amber-50' }
    case 'expired':
      return { bg: 'bg-rose-500', text: 'text-rose-600', innerBg: 'bg-rose-50' }
    default:
      return { bg: 'bg-slate-400', text: 'text-slate-600', innerBg: 'bg-slate-50' }
  }
}
</script>
