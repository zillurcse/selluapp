<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen relative overflow-hidden transition-colors duration-300">
    <div class="max-w-4xl mx-auto mb-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 font-medium mb-4">
          <NuxtLink to="/vendor/managed-shop" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Manage Shop</NuxtLink>
          <ChevronRight class="w-4 h-4" />
          <span class="text-slate-900 dark:text-white font-bold">Payment Gateway</span>
        </div>
      </div>
      
      <div>
        <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Payment Gateway</h1>
        <p class="text-slate-500 dark:text-slate-400 font-semibold mt-1">Configure your payment methods to start accepting orders.</p>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="w-8 h-8 border-4 border-[#0F172A] dark:border-white border-t-transparent rounded-full animate-spin"></div>
    </div>
    <!-- Payment Methods Grid -->
    <div v-else class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">
      <div 
        v-for="(gateway, index) in gateways" 
        :key="gateway.type"
        class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm p-8 hover:shadow-xl hover:shadow-indigo-500/5 transition-all duration-500 group relative overflow-hidden flex flex-col justify-between"
      >
        <!-- Background Pattern -->
        <div class="absolute -right-4 -top-4 w-32 h-32 bg-slate-50 dark:bg-slate-800/50 rounded-full group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/20 transition-colors"></div>

        <div class="relative space-y-6 flex-grow">
          <div class="flex items-start justify-between">
            <div :class="['w-16 h-16 rounded-2xl flex items-center justify-center shadow-inner', gateway.bg]">
              <component :is="gateway.icon" class="w-8 h-8 text-white" />
            </div>
            <button 
              @click="gateway.active = !gateway.active"
              :class="['w-12 h-6 rounded-full transition-all duration-300 relative', gateway.active ? 'bg-emerald-500' : 'bg-slate-200 dark:bg-slate-700']"
            >
              <div :class="['w-4 h-4 bg-white rounded-full absolute top-1 transition-all duration-300', gateway.active ? 'left-7' : 'left-1']"></div>
            </button>
          </div>

          <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">{{ gateway.name }}</h2>
            <p class="text-sm font-bold text-slate-400 dark:text-slate-500 mt-1">{{ gateway.desc }}</p>
          </div>
        </div>

        <div class="relative mt-6" v-if="gateway.type !== 'cod' && gateway.active">
          <div class="pt-4 border-t border-slate-50 dark:border-slate-800">
            <button @click="openConfig(index)" class="w-full py-4 bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-black rounded-2xl transition-all flex items-center justify-center gap-2">
              <Settings2 class="w-4 h-4 text-slate-400" />
              Configure API Keys
            </button>
          </div>
        </div>
      </div>

      <!-- Add New Mock -->
      <div class="bg-slate-50 dark:bg-slate-900 rounded-[32px] border-2 border-dashed border-slate-200 dark:border-slate-700 p-8 flex flex-col items-center justify-center text-center gap-4 hover:border-indigo-200 hover:bg-white dark:hover:bg-slate-800 transition-all cursor-pointer group">
        <div class="w-16 h-16 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
          <Plus class="w-8 h-8 text-slate-300 group-hover:text-indigo-500" />
        </div>
        <div>
          <h3 class="font-black text-slate-400 group-hover:text-slate-800 dark:group-hover:text-white">Add More Gateways</h3>
          <p class="text-xs font-bold text-slate-300 group-hover:text-slate-400 dark:group-hover:text-slate-500">Request SSLCommerz, bKash, or AmarPay</p>
        </div>
      </div>
    </div>

    <!-- Floating Save Button -->
    <div class="fixed bottom-8 right-8 z-40">
      <button @click="saveSettings" :disabled="saving || pending" class="px-8 py-4 bg-slate-900 dark:bg-indigo-600 hover:bg-slate-800 dark:hover:bg-indigo-700 text-white font-black rounded-2xl shadow-2xl flex items-center gap-3 transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
        <Save v-if="!saving" class="w-6 h-6" />
        <span v-if="saving">Saving Changes...</span>
        <span v-else>Update Payment Gateways</span>
      </button>
    </div>

    <!-- Side Drawer -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="translate-x-0"
      leave-to-class="translate-x-full"
    >
      <div v-if="isDrawerOpen" class="fixed inset-y-0 right-0 w-[480px] bg-white dark:bg-slate-900 shadow-2xl z-50 flex flex-col border-l border-slate-100 dark:border-slate-800">
        <!-- Drawer Header -->
        <div class="p-8 flex items-center justify-between pb-4">
          <h2 class="text-xl font-black text-slate-900 dark:text-white">Configure {{ gateways[editingIndex]?.name }}</h2>
          <button 
            @click="isDrawerOpen = false"
            class="w-10 h-10 bg-black dark:bg-slate-800 rounded-xl flex items-center justify-center text-white hover:bg-slate-800 dark:hover:bg-slate-700 transition-all"
          >
            <X class="w-6 h-6" />
          </button>
        </div>

        <!-- Drawer Body -->
        <div class="flex-grow overflow-y-auto px-8 py-4 space-y-6" v-if="gateways[editingIndex]">
          <div v-if="gateways[editingIndex].type === 'stripe'" class="space-y-4">
            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Publishable Key</label>
              <input v-model="gateways[editingIndex].config.publicKey" type="text" placeholder="pk_test_..." class="w-full h-14 px-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200">
            </div>
            <div class="space-y-2 relative">
              <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Secret Key</label>
              <input v-model="gateways[editingIndex].config.secretKey" :type="showSecret ? 'text' : 'password'" placeholder="sk_test_..." class="w-full h-14 px-5 pr-12 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200">
              <button @click="showSecret = !showSecret" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                <EyeOff v-if="showSecret" class="w-4 h-4" />
                <Eye v-else class="w-4 h-4" />
              </button>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Webhook Secret</label>
              <input v-model="gateways[editingIndex].config.webhookSecret" type="password" placeholder="whsec_..." class="w-full h-14 px-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200">
            </div>
          </div>

          <div v-if="gateways[editingIndex].type === 'wallet'" class="space-y-4">
            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Wallet Type</label>
              <select v-model="gateways[editingIndex].config.walletType" class="w-full h-14 px-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200 appearance-none">
                <option value="bkash">bKash</option>
                <option value="nagad">Nagad</option>
                <option value="rocket">Rocket</option>
                <option value="upay">Upay</option>
              </select>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Account Type</label>
              <select v-model="gateways[editingIndex].config.accountType" class="w-full h-14 px-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200 appearance-none">
                <option value="Personal">Personal</option>
                <option value="Agent">Agent</option>
                <option value="Merchant">Merchant</option>
              </select>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Mobile Number</label>
              <input v-model="gateways[editingIndex].config.number" type="text" placeholder="01XXXXXXXXX" class="w-full h-14 px-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200">
            </div>
          </div>

          <div v-if="gateways[editingIndex].type === 'bank'" class="space-y-4">
            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Bank Name</label>
              <input v-model="gateways[editingIndex].config.bankName" type="text" placeholder="e.g. Dutch Bangla Bank" class="w-full h-14 px-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Account Name</label>
              <input v-model="gateways[editingIndex].config.accountName" type="text" placeholder="e.g. John Doe" class="w-full h-14 px-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Account Number</label>
              <input v-model="gateways[editingIndex].config.accountNumber" type="text" placeholder="e.g. 1012XXXXXXXX" class="w-full h-14 px-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Branch Name</label>
              <input v-model="gateways[editingIndex].config.branchName" type="text" placeholder="e.g. Banani Branch" class="w-full h-14 px-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Routing Number (Optional)</label>
              <input v-model="gateways[editingIndex].config.routingNumber" type="text" placeholder="Routing Number" class="w-full h-14 px-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200">
            </div>
          </div>
        </div>

        <!-- Drawer Footer -->
        <div class="p-8 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3 z-10 relative">
          <button 
            @click="isDrawerOpen = false"
            class="px-8 py-4 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-200 font-black rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-all active:scale-95"
          >
            Done
          </button>
        </div>
      </div>
    </Transition>

    <!-- Overlay -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="isDrawerOpen" @click="isDrawerOpen = false" class="fixed inset-0 bg-slate-900/10 backdrop-blur-sm z-40"></div>
    </Transition>
  </div>
</template>

<script setup>
import { 
  ChevronRight, 
  CreditCard, 
  Settings2, 
  Plus, 
  Wallet, 
  Banknote, 
  Zap,
  Save,
  X,
  Eye,
  EyeOff
} from 'lucide-vue-next'
import { ref, onMounted } from 'vue'

const { $toast } = useNuxtApp()
const { getAll, createItem } = useCrud()
const router = useRouter()

const pending = ref(true)
const saving = ref(false)
const isDrawerOpen = ref(false)
const editingIndex = ref(-1)
const showSecret = ref(false)

const gateways = ref([
  { type: 'cod', name: 'Cash on Delivery', desc: 'Accept payments at your doorstep.', icon: Banknote, bg: 'bg-emerald-600', active: true, config: {} },
  { type: 'stripe', name: 'Stripe', desc: 'Secure worldwide card payments.', icon: CreditCard, bg: 'bg-indigo-600', active: false, config: { publicKey: '', secretKey: '', webhookSecret: '' } },
  { type: 'wallet', name: 'Digital Wallet', desc: 'Mobile wallet systems (bKash/Nagad).', icon: Wallet, bg: 'bg-rose-500', active: true, config: { walletType: 'bkash', number: '', accountType: 'Personal' } },
  { type: 'bank', name: 'Instant Bank Transfer', desc: 'Real-time bank verification.', icon: Zap, bg: 'bg-blue-600', active: false, config: { bankName: '', accountName: '', accountNumber: '', branchName: '', routingNumber: '' } },
])

const openConfig = (index) => {
  editingIndex.value = index
  isDrawerOpen.value = true
}

const loadSettings = async () => {
  try {
    pending.value = true
    const response = await getAll('/vendor/settings?group=payment_gateways')
    if (response.data) {
      const data = response.data
      gateways.value.forEach(gateway => {
        if (data[gateway.type]) {
          const remoteData = typeof data[gateway.type] === 'string' ? JSON.parse(data[gateway.type]) : data[gateway.type]
          gateway.active = remoteData.active ?? gateway.active
          gateway.config = remoteData.config || gateway.config
        }
      })
    }
  } catch (error) {
    if (error.response?.status !== 404) {
      $toast.error('Failed to load payment gateways')
    }
  } finally {
    pending.value = false
  }
}

const saveSettings = async () => {
  try {
    saving.value = true
    const settingsPayload = {}
    gateways.value.forEach(gateway => {
      settingsPayload[gateway.type] = {
        active: gateway.active,
        config: gateway.config
      }
    })

    await createItem('/vendor/settings', {
      group: 'payment_gateways',
      settings: settingsPayload
    })
    
    router.push('/vendor/managed-shop/payment-gateway')
  } catch (error) {
    console.error(error)
    $toast.error('Failed to save settings')
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  loadSettings()
})
</script>

<style scoped>
/* Custom scrollbar for drawer body */
.overflow-y-auto::-webkit-scrollbar {
  width: 4px;
}
.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}
.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
.dark .overflow-y-auto::-webkit-scrollbar-thumb {
  background: #1e293b;
}
.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #cbd5e1;
}
</style>
