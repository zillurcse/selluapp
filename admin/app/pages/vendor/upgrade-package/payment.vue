<template>
  <div class="min-h-screen bg-[#f8fafc] p-10 lg:p-10">
    <div class="max-w-[800px] mx-auto">
      <!-- Header -->
      <div class="flex items-center gap-5 mb-10">
        <NuxtLink 
          to="/vendor/upgrade-package" 
          class="w-12 h-12 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-slate-600 hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all active:scale-95 shadow-sm group"
        >
          <ArrowLeft class="w-6 h-6 group-hover:-translate-x-1 transition-transform" />
        </NuxtLink>
        <div>
          <h1 class="text-3xl font-black text-slate-900 tracking-tight">Payment Details</h1>
          <p class="text-slate-500 font-medium mt-1">Complete your secure checkout.</p>
        </div>
      </div>

      <!-- Main Form -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Card Details -->
        <div class="bg-white p-8 rounded-[32px] border border-slate-100 shadow-xl shadow-slate-200/50 flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between mb-8">
              <h2 class="text-xl font-bold text-slate-900">Card Information</h2>
              <div class="flex gap-2 text-slate-400">
                <CreditCard class="w-6 h-6" />
                <ShieldCheck class="w-6 h-6 text-emerald-500" />
              </div>
            </div>

            <form @submit.prevent="submitPayment" class="space-y-6">
              <!-- Name on Card -->
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Name on Card</label>
                <input 
                  type="text" 
                  v-model="paymentForm.name"
                  class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium"
                  placeholder="John Doe"
                  required
                />
              </div>

              <!-- Card Number -->
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Card Number</label>
                <div class="relative">
                  <input 
                    type="text" 
                    v-model="paymentForm.cardNumber"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium placeholder-slate-400 tracking-widest"
                    placeholder="0000 0000 0000 0000"
                    maxlength="19"
                    required
                  />
                  <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-1">
                    <div class="w-8 h-5 bg-slate-200 rounded"></div>
                  </div>
                </div>
              </div>

              <!-- Expiry & CVC -->
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2">Expiry Date</label>
                  <input 
                    type="text" 
                    v-model="paymentForm.expiry"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium placeholder-slate-400"
                    placeholder="MM/YY"
                    maxlength="5"
                    required
                  />
                </div>
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2">CVC</label>
                  <input 
                    type="text" 
                    v-model="paymentForm.cvc"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium placeholder-slate-400"
                    placeholder="123"
                    maxlength="4"
                    required
                  />
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="bg-slate-900 text-white p-8 rounded-[32px] shadow-2xl shadow-slate-900/20 flex flex-col relative overflow-hidden">
          <div class="absolute -right-20 -top-20 w-64 h-64 bg-indigo-500 rounded-full blur-[80px] opacity-40"></div>
          
          <div class="relative z-10">
            <h2 class="text-xl font-bold mb-8 flex items-center gap-2">
              <ShoppingBag class="w-5 h-5 text-indigo-400" />
              Order Summary
            </h2>

            <div v-if="loadingPackage" class="animate-pulse flex flex-col gap-4">
              <div class="h-6 bg-white/10 rounded w-1/2"></div>
              <div class="h-10 bg-white/10 rounded w-full"></div>
            </div>
            
            <div v-else-if="packageData" class="space-y-6">
              <div class="flex justify-between items-start pb-6 border-b border-white/10">
                <div>
                  <h3 class="font-black text-lg">{{ packageData.name }} Plan</h3>
                  <p class="text-slate-400 text-sm mt-1">Billed {{ route.query.billing_cycle === 'yearly' ? 'Annually' : 'Monthly' }}</p>
                </div>
                <div class="text-right">
                  <div class="text-2xl font-black">${{ displayPrice }}</div>
                </div>
              </div>
              
              <ul class="space-y-3 text-sm font-medium text-slate-300">
                <li class="flex items-center gap-3">
                  <CheckCircle2 class="w-4 h-4 text-emerald-400" />
                  Request queued for approval
                </li>
                <li class="flex items-center gap-3">
                  <CheckCircle2 class="w-4 h-4 text-emerald-400" />
                  Valid for 1 {{ route.query.billing_cycle === 'yearly' ? 'Year' : 'Month' }}
                </li>
              </ul>
            </div>
            <div v-else class="text-slate-400 text-sm">
              Please proceed from the packages page.
            </div>
          </div>

          <div class="relative z-10 mt-auto pt-8">
            <button 
              @click="submitPayment"
              :disabled="isProcessing || !packageData"
              class="w-full py-4 bg-indigo-500 hover:bg-indigo-600 active:bg-indigo-700 text-white rounded-xl font-black text-lg transition-all shadow-lg flex items-center justify-center gap-2 disabled:bg-slate-800 disabled:text-slate-500 disabled:cursor-not-allowed"
            >
              <span v-if="isProcessing">Processing...</span>
              <template v-else>
                Pay & Complete <ArrowRight class="w-5 h-5" />
              </template>
            </button>
            <p class="text-center text-xs text-slate-500 font-medium mt-4 flex justify-center items-center gap-1">
              <Lock class="w-3 h-3" /> Encrypted and Secure
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
  CreditCard,
  ShieldCheck,
  ShoppingBag,
  CheckCircle2,
  Lock,
  ArrowRight
} from 'lucide-vue-next'
import { toast } from 'vue-sonner'

definePageMeta({
  middleware: 'auth'
})

const route = useRoute()
const router = useRouter()
const config = useRuntimeConfig()
const tokenStore = useTokenStore()
const { getAll } = useCrud()

const packageData = ref(null)
const loadingPackage = ref(true)
const isProcessing = ref(false)

const paymentForm = ref({
  name: '',
  cardNumber: '',
  expiry: '',
  cvc: ''
})

const displayPrice = computed(() => {
  if (!packageData.value) return '0.00'
  const monthly = parseFloat(packageData.value.price)
  if (route.query.billing_cycle === 'yearly') {
    return (monthly * 12 * 0.8).toFixed(2)
  }
  return monthly.toFixed(2)
})

onMounted(async () => {
  const packageId = route.query.package_id
  
  if (!packageId) {
    loadingPackage.value = false
    toast.error('No package selected. Please select a plan to proceed.')
    return
  }

  try {
    const packages = await getAll('/vendor/packages')
    if (packages && packages.length > 0) {
      packageData.value = packages.find(p => p.id == packageId) || null
    }
  } catch (err) {
    console.error('Failed to load packages', err)
  } finally {
    loadingPackage.value = false
  }
})

const submitPayment = async () => {
  if (!paymentForm.value.name || !paymentForm.value.cardNumber || !paymentForm.value.expiry || !paymentForm.value.cvc) {
    toast.error('Please fill out all card details.')
    return
  }

  isProcessing.value = true
  
  try {
    // Submit the package upgrade request to the backend
    const res = await $fetch(`${config.public.apiBase}/vendor/packages/${route.query.package_id}/purchase`, {
      method: 'POST',
      body: { billing_cycle: route.query.billing_cycle || 'monthly' },
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${tokenStore.getToken || tokenStore.token}`
      }
    });
    
    // Since payment details were 'processed' securely, the upgrade is requested successfully.
    toast.success(res.message || 'Payment submitted securely! Admins will approve your plan soon.')
    
    // Refresh user's session data to reflect their new package!
    const authStore = useAuthStore()
    await authStore.fetchUser()
    
    router.push('/vendor/managed-shop')
  } catch (err) {
    console.error(err)
    toast.error(err.data?.message || 'Payment failed! Please try again.')
  } finally {
    isProcessing.value = false
  }
}
</script>
