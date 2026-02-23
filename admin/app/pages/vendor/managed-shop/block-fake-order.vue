<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <div class="max-w-4xl mx-auto mb-8">
      <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 font-medium mb-4">
        <NuxtLink to="/vendor/managed-shop" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Manage Shop</NuxtLink>
        <ChevronRight class="w-4 h-4" />
        <span class="text-slate-900 dark:text-white font-bold">Block Fake Order</span>
      </div>
      
      <div class="flex items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 mb-1">
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Block Fake Order</h1>
            <span class="px-3 py-1 bg-orange-500 text-white text-[10px] font-black rounded-full uppercase tracking-wider">PRO</span>
          </div>
          <p class="text-slate-500 dark:text-slate-400 font-semibold">Identify and automatically block suspicious or fake orders.</p>
        </div>
        <button @click="saveSettings" :disabled="saving || pending" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white font-black rounded-2xl shadow-lg shadow-indigo-200 dark:shadow-indigo-900/20 transition-all active:scale-95 flex items-center gap-2 disabled:opacity-50">
          <Save v-if="!saving" class="w-5 h-5" />
          <span>{{ saving ? 'Saving...' : 'Save Rules' }}</span>
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="w-8 h-8 border-4 border-indigo-600 dark:border-indigo-400 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <div v-else class="max-w-4xl mx-auto space-y-6">
      <!-- Blocking Rules -->
      <div class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm p-8 space-y-8 transition-colors duration-300">
        <h2 class="text-xl font-black text-slate-900 dark:text-white border-b border-slate-50 dark:border-slate-800 pb-6">Automatic Blocking Rules</h2>
        
        <div class="space-y-6">
          <div v-for="rule in rules" :key="rule.type" class="flex items-center justify-between p-6 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-700/50">
            <div class="flex items-center gap-4">
              <div class="w-10 h-10 bg-white dark:bg-slate-800 rounded-xl flex items-center justify-center shadow-sm text-slate-400 dark:text-slate-500">
                <component :is="rule.icon" class="w-5 h-5" />
              </div>
              <div>
                <h3 class="font-black text-slate-800 dark:text-slate-200">{{ rule.name }}</h3>
                <p class="text-xs font-bold text-slate-400 dark:text-slate-500">{{ rule.desc }}</p>
              </div>
            </div>
            <button 
              @click="rule.enabled = !rule.enabled"
              :class="['w-14 h-8 rounded-full transition-all duration-300 relative', rule.enabled ? 'bg-indigo-600' : 'bg-slate-200 dark:bg-slate-700']"
            >
              <div :class="['w-6 h-6 bg-white rounded-full absolute top-1 transition-all duration-300', rule.enabled ? 'left-7' : 'left-1']"></div>
            </button>
          </div>
        </div>
      </div>

      <!-- Blacklist -->
      <div class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm p-8 space-y-6 transition-colors duration-300">
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-black text-slate-900 dark:text-white">Manual Blacklist</h2>
        </div>
        
        <div class="space-y-4">
          <form @submit.prevent="addBlacklistEntry" class="flex gap-4">
            <input v-model="newEntry" type="text" placeholder="Enter IP address or phone number to block" class="flex-grow h-12 px-6 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200 text-sm">
            <button type="submit" :disabled="!newEntry.trim()" class="px-6 bg-slate-900 dark:bg-indigo-600 text-white font-black rounded-xl hover:bg-slate-800 dark:hover:bg-indigo-700 transition-all active:scale-95 disabled:opacity-50 flex items-center gap-2 text-sm">
              <Plus class="w-4 h-4" />
              Block
            </button>
          </form>

          <div v-if="blacklist.length === 0" class="bg-slate-50 dark:bg-slate-800/50 p-8 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700 text-center">
            <p class="text-slate-400 dark:text-slate-500 font-bold italic">No blacklisted phones or IPs yet.</p>
          </div>

          <div v-else class="space-y-3">
            <div v-for="(entry, index) in blacklist" :key="index" class="flex items-center justify-between p-4 bg-red-50/50 dark:bg-red-900/10 rounded-xl border border-red-100 dark:border-red-900/20">
              <div class="flex items-center gap-3">
                <ShieldAlert class="w-4 h-4 text-red-500" />
                <span class="font-bold text-slate-700 dark:text-slate-200 text-sm">{{ entry }}</span>
              </div>
              <button @click="removeBlacklistEntry(index)" class="text-slate-400 dark:text-slate-500 hover:text-red-500 p-2 rounded-lg hover:bg-white dark:hover:bg-slate-800 transition-colors">
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  ChevronRight, 
  ShieldAlert, 
  Phone, 
  Globe, 
  Clock, 
  Scale,
  Save,
  Plus,
  Trash2
} from 'lucide-vue-next'
import { ref, onMounted } from 'vue'

definePageMeta({
  middleware: 'auth',
  permissions: 'settings.view'
})

const { $toast } = useNuxtApp()
const { getAll, createItem } = useCrud()
const router = useRouter()

const pending = ref(true)
const saving = ref(false)

const rules = ref([
  { type: 'ip_rate_limiting', name: 'IP Rate Limiting', desc: 'Block users making too many orders from one IP.', icon: Globe, enabled: true },
  { type: 'phone_verification', name: 'Phone Verification', desc: 'Require OTP for suspicious phone numbers.', icon: Phone, enabled: true },
  { type: 'behavior_analysis', name: 'Behavior Analysis', desc: 'Block rapidly repeated checkout attempts.', icon: Clock, enabled: false },
  { type: 'order_threshold', name: 'Order Threshold', desc: 'Flag guest orders over a certain amount.', icon: Scale, enabled: false },
])

const blacklist = ref([])
const newEntry = ref('')

const addBlacklistEntry = () => {
  const entry = newEntry.value.trim()
  if (entry) {
    if (!blacklist.value.includes(entry)) {
      blacklist.value.push(entry)
    } else {
      $toast.error('This entry is already blacklisted')
    }
    newEntry.value = ''
  }
}

const removeBlacklistEntry = (index) => {
  blacklist.value.splice(index, 1)
}

const loadSettings = async () => {
  try {
    pending.value = true
    const response = await getAll('/vendor/settings?group=fraud_check')
    if (response.data) {
      const data = response.data
      
      // Load rules
      rules.value.forEach(rule => {
        if (data[rule.type] !== undefined) {
          const ruleData = typeof data[rule.type] === 'string' ? JSON.parse(data[rule.type]) : data[rule.type]
          rule.enabled = ruleData.enabled ?? rule.enabled
        }
      })

      // Load blacklist
      if (data.blacklist) {
        blacklist.value = typeof data.blacklist === 'string' ? JSON.parse(data.blacklist) : data.blacklist
      }
    }
  } catch (error) {
    if (error.response?.status !== 404) {
      $toast.error('Failed to load fraud check settings')
    }
  } finally {
    pending.value = false
  }
}

const saveSettings = async () => {
  try {
    saving.value = true
    const settingsPayload = {
      blacklist: blacklist.value
    }
    
    rules.value.forEach(rule => {
      settingsPayload[rule.type] = {
        enabled: rule.enabled
      }
    })

    await createItem('/vendor/settings', {
      group: 'fraud_check',
      settings: settingsPayload
    })
    router.push('/vendor/managed-shop/block-fake-order')
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
