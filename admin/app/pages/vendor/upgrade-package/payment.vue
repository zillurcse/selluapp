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
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Sidebar: Method Selection -->
        <div class="lg:col-span-4 space-y-4">
          <h2 class="text-xs font-black text-slate-400 uppercase tracking-widest px-4">Select Method</h2>
          
          <div v-if="methodsLoading" class="space-y-3">
             <div v-for="i in 3" :key="i" class="h-16 bg-white border border-slate-100 rounded-2xl animate-pulse"></div>
          </div>
          <div v-else class="space-y-3">
            <!-- Card Option (Always show as primary or if in methods) -->
            <button 
              @click="selectedMethod = 'stripe'"
              :class="['w-full p-4 rounded-3xl border-2 transition-all flex items-center gap-4 text-left group', selectedMethod === 'stripe' ? 'border-indigo-600 bg-white shadow-xl shadow-indigo-500/10' : 'border-transparent bg-white/50 hover:bg-white hover:border-slate-200']"
            >
              <div :class="['w-12 h-12 rounded-2xl flex items-center justify-center transition-colors', selectedMethod === 'stripe' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200']">
                <CreditCard class="w-6 h-6" />
              </div>
              <div>
                <p class="font-bold text-slate-900">Credit / Debit Card</p>
                <p class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Fast & Secure</p>
              </div>
            </button>

            <!-- Dynamic Methods -->
            <button 
              v-for="method in paymentMethods.filter(m => m.slug !== 'stripe' && m.slug !== 'cod')"
              :key="method.slug"
              @click="selectedMethod = method.slug"
              :class="['w-full p-4 rounded-3xl border-2 transition-all flex items-center gap-4 text-left group', selectedMethod === method.slug ? 'border-indigo-600 bg-white shadow-xl shadow-indigo-500/10' : 'border-transparent bg-white/50 hover:bg-white hover:border-slate-200']"
            >
              <div :class="['w-12 h-12 rounded-2xl flex items-center justify-center transition-colors', selectedMethod === method.slug ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-400 group-hover:bg-slate-200']">
                <component :is="getIcon(method.icon)" class="w-6 h-6" />
              </div>
              <div>
                <p class="font-bold text-slate-900">{{ method.name }}</p>
                <p class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Manual Verification</p>
              </div>
            </button>
          </div>
        </div>

        <!-- Payment Details Content -->
        <div class="lg:col-span-8 space-y-6">
          <div class="bg-white p-8 rounded-[32px] border border-slate-100 shadow-xl shadow-slate-200/50">
            <!-- Stripe Card Form -->
            <div v-if="selectedMethod === 'stripe'" class="space-y-8">
              <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-slate-900">Card Information</h2>
                <div class="flex gap-2 text-slate-400">
                  <CreditCard class="w-6 h-6" />
                  <ShieldCheck class="w-6 h-6 text-emerald-500" />
                </div>
              </div>

              <div class="space-y-6">
                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2">Name on Card</label>
                  <input 
                    type="text" 
                    v-model="paymentForm.name"
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium"
                    placeholder="John Doe"
                  />
                </div>

                <div>
                  <label class="block text-sm font-bold text-slate-700 mb-2">Card Number</label>
                  <div class="relative">
                    <input 
                      type="text" 
                      v-model="paymentForm.cardNumber"
                      class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium placeholder-slate-400 tracking-widest"
                      placeholder="0000 0000 0000 0000"
                      maxlength="19"
                    />
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Expiry Date</label>
                    <input 
                      type="text" 
                      v-model="paymentForm.expiry"
                      class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium placeholder-slate-400"
                      placeholder="MM/YY"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">CVC</label>
                    <input 
                      type="text" 
                      v-model="paymentForm.cvc"
                      class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium placeholder-slate-400"
                      placeholder="123"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Manual Payment Form -->
            <div v-else class="space-y-8">
              <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-slate-900">{{ currentMethod?.name }} Payment</h2>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center">
                  <component :is="getIcon(currentMethod?.icon)" class="w-5 h-5 text-indigo-600" />
                </div>
              </div>

              <!-- Payment Instruction Box -->
              <div v-if="currentMethod?.description" class="p-6 bg-indigo-50/50 border border-indigo-100 rounded-2xl flex gap-4">
                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center flex-shrink-0 shadow-sm">
                  <Info class="w-5 h-5 text-indigo-500" />
                </div>
                <div>
                   <p class="text-xs font-black text-indigo-600 uppercase tracking-widest mb-1">Instruction</p>
                   <p class="text-sm font-medium text-slate-700 leading-relaxed">{{ currentMethod.description }}</p>
                </div>
              </div>

              <div class="space-y-6 pt-4">
                <div class="p-5 bg-slate-50 border border-slate-200 rounded-2xl">
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Amount to Pay</p>
                    <p class="text-3xl font-black text-slate-900">${{ displayPrice }}</p>
                </div>

                <div v-if="currentMethod?.config_fields?.length > 0" class="space-y-4">
                  <!-- Info-only Fields (Admin Details) -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div 
                      v-for="field in currentMethod.config_fields.filter(f => f.value)" 
                      :key="field.name" 
                      class="p-4 bg-white border border-slate-100 rounded-2xl shadow-sm group/field relative"
                    >
                      <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">{{ field.label }}</p>
                      <p class="text-sm font-black text-slate-900">{{ field.value || 'N/A' }}</p>
                      <button @click="copyToClipboard(field.value)" class="absolute top-4 right-4 p-2 bg-indigo-50 text-indigo-600 rounded-xl opacity-0 group-hover/field:opacity-100 transition-opacity">
                         <Copy class="w-3 h-3" />
                      </button>
                    </div>
                  </div>

                  <!-- Input Fields (Vendor Verification Details) -->
                  <div class="space-y-4">
                    <div v-for="field in currentMethod.config_fields.filter(f => !f.value)" :key="field.name">
                      <label class="block text-sm font-bold text-slate-700 mb-2">{{ field.label }}</label>
                      <input 
                        v-if="field.type !== 'textarea'"
                        :type="field.type"
                        v-model="manualData[field.name]"
                        :placeholder="field.placeholder"
                        class="w-full px-5 py-4 bg-white border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition-all font-black"
                      />
                      <textarea 
                        v-else
                        v-model="manualData[field.name]"
                        :placeholder="field.placeholder"
                        class="w-full px-5 py-4 bg-white border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition-all font-medium"
                      ></textarea>
                    </div>
                  </div>
                </div>

                <div class="space-y-4 pt-4 border-t border-slate-100 mt-4">
                  <label class="block text-sm font-bold text-slate-700 mb-2">Transaction ID / Reference Number</label>
                  <input 
                    type="text" 
                    v-model="paymentForm.transactionId"
                    class="w-full px-5 py-4 bg-white border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-600 outline-none transition-all font-black text-indigo-600"
                    placeholder="Enter the reference number after payment"
                  />
                  <p class="text-[10px] text-slate-400 font-bold mt-2 uppercase tracking-tight italic">Our team will verify this reference to activate your plan.</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Summary Bar (Mobile/Bottom) -->
          <div class="bg-slate-900 text-white p-8 rounded-[32px] shadow-2xl shadow-slate-900/20 flex flex-col relative overflow-hidden">
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-indigo-500 rounded-full blur-[80px] opacity-40"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
              <div>
                <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
                  <ShoppingBag class="w-5 h-5 text-indigo-400" />
                  Plan: {{ packageData?.name }}
                </h2>
                <div class="flex items-center gap-6">
                   <div class="text-3xl font-black">${{ displayPrice }}</div>
                   <div class="h-8 w-px bg-white/10 hidden md:block"></div>
                   <div class="text-sm font-medium text-slate-400">Billed {{ route.query.billing_cycle === 'yearly' ? 'Yearly' : 'Monthly' }}</div>
                </div>
              </div>
              
              <div class="flex-shrink-0 min-w-[240px]">
                <button 
                  @click="submitPayment"
                  :disabled="isProcessing || !packageData"
                  class="w-full py-4 bg-white text-slate-900 hover:bg-slate-100 active:scale-95 rounded-2xl font-black text-lg transition-all shadow-lg flex items-center justify-center gap-2 disabled:bg-slate-800 disabled:text-slate-500"
                >
                  <span v-if="isProcessing">Processing...</span>
                  <template v-else>
                    Pay & Complete <ArrowRight class="w-5 h-5" />
                  </template>
                </button>
              </div>
            </div>
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
  ArrowRight,
  Wallet,
  Banknote,
  Smartphone,
  Zap,
  Globe,
  Info,
  Send,
  Copy
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

const paymentMethods = ref([])
const selectedMethod = ref('stripe') // Default to stripe/card
const methodsLoading = ref(true)

const paymentForm = ref({
  name: '',
  cardNumber: '',
  expiry: '',
  cvc: '',
  transactionId: '' // For manual payments
})

const manualData = ref({})

const copyToClipboard = (text) => {
  navigator.clipboard.writeText(text)
  toast.success('Copied to clipboard!')
}

const displayPrice = computed(() => {
  if (!packageData.value) return '0.00'
  const monthly = parseFloat(packageData.value.price)
  if (route.query.billing_cycle === 'yearly') {
    return (monthly * 12 * 0.8).toFixed(2)
  }
  return monthly.toFixed(2)
})

const fetchPaymentMethods = async () => {
  methodsLoading.value = true
  try {
    const res = await getAll('/vendor/payment-methods')
    paymentMethods.value = res || []
    // Add default stripe/card if not present to maintain existing flow
    if (!paymentMethods.value.find(m => m.slug === 'stripe')) {
       // Only if we want to force credit card as an option even if not in DB
    }
  } catch (e) {
    console.error(e)
  } finally {
    methodsLoading.value = false
  }
}

onMounted(async () => {
  fetchPaymentMethods()
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
  const isCard = selectedMethod.value === 'stripe'
  
  if (isCard) {
    if (!paymentForm.value.name || !paymentForm.value.cardNumber || !paymentForm.value.expiry || !paymentForm.value.cvc) {
      toast.error('Please fill out all card details.')
      return
    }
  } else {
    if (!paymentForm.value.transactionId) {
      toast.error('Please enter the Transaction ID / Reference.')
      return
    }
  }

  isProcessing.value = true
  
  try {
    const res = await $fetch(`${config.public.apiBase}/vendor/packages/${route.query.package_id}/purchase`, {
      method: 'POST',
      body: { 
        billing_cycle: route.query.billing_cycle || 'monthly',
        payment_method: selectedMethod.value,
        payment_ref: isCard ? null : [
          ...Object.entries(manualData.value).map(([label, val]) => `${label}: ${val}`),
          paymentForm.value.transactionId ? `TRX: ${paymentForm.value.transactionId}` : null
        ].filter(Boolean).join(', ')
      },
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${tokenStore.getToken || tokenStore.token}`
      }
    });
    
    toast.success(res.message || 'Payment submitted securely! Admins will approve your plan soon.')
    
    const authStore = useAuthStore()
    await authStore.fetchUser()
    
    router.push('/vendor')
  } catch (err) {
    console.error(err)
    toast.error(err.data?.message || 'Payment failed! Please try again.')
  } finally {
    isProcessing.value = false
  }
}

const getIcon = (iconName) => {
  const icons = { CreditCard, Wallet, Banknote, Zap, Globe, Smartphone, Send }
  return icons[iconName] || Globe
}

const currentMethod = computed(() => {
  return paymentMethods.value.find(m => m.slug === selectedMethod.value)
})
</script>
