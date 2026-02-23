<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <div class="max-w-4xl mx-auto mb-8">
      <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 font-medium mb-4">
        <NuxtLink to="/vendor/managed-shop" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Manage Shop</NuxtLink>
        <ChevronRight class="w-4 h-4" />
        <span class="text-slate-900 dark:text-white font-bold">Third Party APIs</span>
      </div>
      
      <div class="flex items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">External Integrations</h1>
          <p class="text-slate-500 dark:text-slate-400 font-semibold mt-1">Configure SMS gateways and other third-party API services.</p>
        </div>
        <button @click="saveSettings" :disabled="saving || pending" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white font-black rounded-2xl shadow-lg shadow-indigo-200 dark:shadow-indigo-900/20 transition-all active:scale-95 flex items-center gap-2 disabled:opacity-50">
          <Save v-if="!saving" class="w-5 h-5" />
          <span>{{ saving ? 'Saving...' : 'Save Changes' }}</span>
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="w-8 h-8 border-4 border-indigo-600 dark:border-indigo-400 border-t-transparent rounded-full animate-spin"></div>
    </div>
    
    <div v-else class="max-w-4xl mx-auto space-y-6">
      <!-- SMS Gateway -->
      <div class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm p-8 space-y-8 transition-colors duration-300">
        <div class="flex items-center gap-3 border-b border-slate-50 dark:border-slate-800 pb-6">
          <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl flex items-center justify-center">
            <MessageSquare class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
          </div>
          <h2 class="text-xl font-black text-slate-900 dark:text-white">SMS Gateway Configuration</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="space-y-2 relative">
            <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider ml-1">Provider</label>
            <select v-model="sms.provider" class="w-full h-14 px-6 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200 appearance-none">
              <option value="Twilio">Twilio</option>
              <option value="Infobip">Infobip</option>
              <option value="SSL Wireless (Local)">SSL Wireless (Local)</option>
              <option value="Mim SMS">Mim SMS</option>
            </select>
            <ChevronDown class="w-5 h-5 text-slate-400 dark:text-slate-500 absolute right-4 top-10 pointer-events-none" />
          </div>
          <div class="space-y-2 relative">
            <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider ml-1">API Key</label>
            <input v-model="sms.apiKey" :type="showSmsKey ? 'text' : 'password'" placeholder="••••••••••••••••" class="w-full h-14 px-6 pr-12 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200">
            <button @click="showSmsKey = !showSmsKey" class="absolute right-4 top-10 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
              <EyeOff v-if="showSmsKey" class="w-4 h-4" />
              <Eye v-else class="w-4 h-4" />
            </button>
          </div>
          <div class="space-y-2">
            <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider ml-1">Sender ID</label>
            <input v-model="sms.senderId" type="text" placeholder="e.g. SHOPNAME" class="w-full h-14 px-6 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200">
          </div>
        </div>
      </div>

      <!-- General API Webhooks -->
      <div class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm p-8 space-y-8 transition-colors duration-300">
        <div class="flex items-center gap-3 border-b border-slate-50 dark:border-slate-800 pb-6">
          <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl flex items-center justify-center">
            <Cpu class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
          </div>
          <h2 class="text-xl font-black text-slate-900 dark:text-white">Custom Webhooks & APIs</h2>
        </div>

        <div class="space-y-6">
          <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Connect your store events to external tools via Webhooks.</p>
          
          <div class="space-y-3" v-if="webhooks.length > 0">
            <div v-for="(webhook, index) in webhooks" :key="index" class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700">
              <span class="font-semibold text-slate-700 dark:text-slate-200">{{ webhook }}</span>
              <button @click="removeWebhook(index)" class="text-red-500 dark:text-red-400 hover:text-red-600 dark:hover:text-red-500 p-2 rounded-lg hover:bg-white dark:hover:bg-slate-700 transition-colors">
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
          </div>

          <form @submit.prevent="addWebhook" class="flex gap-4">
            <input v-model="newWebhook" type="url" placeholder="https://your-api.com/webhook" required class="flex-grow h-14 px-6 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200">
            <button type="submit" class="px-8 bg-slate-900 dark:bg-indigo-600 text-white font-black rounded-2xl hover:bg-slate-800 dark:hover:bg-indigo-700 transition-all active:scale-95 flex items-center gap-2">
              <Plus class="w-4 h-4" />
              Add Webhook
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  ChevronRight, 
  Save, 
  MessageSquare, 
  Cpu,
  ChevronDown,
  Eye,
  EyeOff,
  Trash2,
  Plus
} from 'lucide-vue-next'
import { ref, reactive, onMounted } from 'vue'

definePageMeta({
  middleware: 'auth',
  permissions: 'settings.view'
})

const { $toast } = useNuxtApp()
const { getAll, createItem } = useCrud()
const router = useRouter()

const pending = ref(true)
const saving = ref(false)
const showSmsKey = ref(false)

const sms = reactive({
  provider: 'Twilio',
  apiKey: '',
  senderId: ''
})

const webhooks = ref([])
const newWebhook = ref('')

const addWebhook = () => {
  if (newWebhook.value) {
    if (!webhooks.value.includes(newWebhook.value)) {
      webhooks.value.push(newWebhook.value)
    } else {
      $toast.error('Webhook already exists')
    }
    newWebhook.value = ''
  }
}

const removeWebhook = (index) => {
  webhooks.value.splice(index, 1)
}

const loadSettings = async () => {
  try {
    pending.value = true
    const response = await getAll('/vendor/settings?group=third_party_apis')
    if (response.data) {
      const data = response.data
      if (data.sms) {
        const remoteSms = typeof data.sms === 'string' ? JSON.parse(data.sms) : data.sms
        Object.assign(sms, remoteSms)
      }
      if (data.webhooks) {
        webhooks.value = typeof data.webhooks === 'string' ? JSON.parse(data.webhooks) : data.webhooks
      }
    }
  } catch (error) {
    if (error.response?.status !== 404) {
      $toast.error('Failed to load API settings')
    }
  } finally {
    pending.value = false
  }
}

const saveSettings = async () => {
  try {
    saving.value = true
    await createItem('/vendor/settings', {
      group: 'third_party_apis',
      settings: {
        sms,
        webhooks: webhooks.value
      }
    })
    router.push('/vendor/managed-shop/third-party-apis')
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
