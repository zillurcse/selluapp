<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <div class="max-w-4xl mx-auto mb-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 font-medium mb-4">
          <NuxtLink to="/vendor/managed-shop" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Manage Shop</NuxtLink>
          <ChevronRight class="w-4 h-4" />
          <span class="text-slate-900 dark:text-white font-bold">Chat & Plugins</span>
        </div>
        <button @click="saveSettings" :disabled="saving || pending" class="px-8 py-3 bg-[#0F172A] dark:bg-indigo-600 text-white font-black rounded-xl hover:bg-slate-800 dark:hover:bg-indigo-700 transition-all active:scale-95 shadow-xl shadow-slate-900/10 dark:shadow-indigo-900/20 disabled:opacity-50 flex items-center gap-2">
          <Save v-if="!saving" class="w-5 h-5" />
          <span>{{ saving ? 'Saving...' : 'Save Changes' }}</span>
        </button>
      </div>
      
      <div>
        <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Chat & Plugins</h1>
        <p class="text-slate-500 dark:text-slate-400 font-semibold mt-1">Connect with your customers through popular chat platforms.</p>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="w-8 h-8 border-4 border-[#0F172A] dark:border-white border-t-transparent rounded-full animate-spin"></div>
    </div>
    <!-- Plugins Grid -->
    <div v-else class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">
      <div 
        v-for="plugin in chatPlugins" 
        :key="plugin.type"
        class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm p-8 hover:shadow-xl hover:shadow-indigo-500/5 transition-all duration-500 group transition-colors duration-300"
      >
        <div class="space-y-6">
          <div class="flex items-start justify-between">
            <div :class="['w-16 h-16 rounded-2xl flex items-center justify-center', plugin.bg]">
              <component :is="plugin.icon" class="w-8 h-8 text-white" />
            </div>
            <button 
              @click="plugin.active = !plugin.active"
              :class="['w-12 h-6 rounded-full transition-all duration-300 relative', plugin.active ? 'bg-indigo-600' : 'bg-slate-200 dark:bg-slate-700']"
            >
              <div :class="['w-4 h-4 bg-white rounded-full absolute top-1 transition-all duration-300', plugin.active ? 'left-7' : 'left-1']"></div>
            </button>
          </div>

          <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">{{ plugin.name }}</h2>
            <p class="text-sm font-bold text-slate-400 dark:text-slate-500 mt-1">{{ plugin.desc }}</p>
          </div>

          <div v-if="plugin.active" class="space-y-4 pt-4 border-t border-slate-50 dark:border-slate-800">
            <div class="space-y-2">
              <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest ml-1">{{ plugin.fieldLabel }}</label>
              <input 
                v-model="plugin.value"
                type="text" 
                :placeholder="plugin.placeholder" 
                class="w-full h-12 px-5 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200 text-sm"
              >
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
  Save, 
  MessageSquare, 
  MessageCircle, 
  Facebook, 
  Zap 
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

const chatPlugins = ref([
  { 
    type: 'whatsapp', 
    name: 'WhatsApp Chat', 
    desc: 'Let customers chat with you on WhatsApp.', 
    icon: MessageCircle, 
    bg: 'bg-emerald-500', 
    active: false,
    fieldLabel: 'WhatsApp Number',
    placeholder: '+8801XXXXXXXXX',
    value: ''
  },
  { 
    type: 'messenger', 
    name: 'Messenger', 
    desc: 'Facebook Page Messenger live chat.', 
    icon: Facebook, 
    bg: 'bg-blue-600', 
    active: false,
    fieldLabel: 'Page ID / URL',
    placeholder: 'yourpageid',
    value: ''
  },
  { 
    type: 'tawk', 
    name: 'Tawk.to', 
    desc: 'Free live chat software for websites.', 
    icon: MessageSquare, 
    bg: 'bg-orange-500', 
    active: false,
    fieldLabel: 'Property ID',
    placeholder: '60xxxxxxxxxxxxxxxx',
    value: ''
  },
  { 
    type: 'crisp', 
    name: 'Crisp Chat', 
    desc: 'Customer messaging platform.', 
    icon: Zap, 
    bg: 'bg-blue-400', 
    active: false,
    fieldLabel: 'Website ID',
    placeholder: 'xxxx-xxxx-xxxx',
    value: ''
  },
])

const loadSettings = async () => {
  try {
    pending.value = true
    const response = await getAll('/vendor/settings?group=chat_plugins')
    if (response.data) {
      const data = response.data
      chatPlugins.value.forEach(plugin => {
        if (data[plugin.type]) {
          const remoteData = typeof data[plugin.type] === 'string' ? JSON.parse(data[plugin.type]) : data[plugin.type]
          plugin.active = remoteData.active ?? plugin.active
          plugin.value = remoteData.value ?? plugin.value
        }
      })
    }
  } catch (error) {
    if (error.response?.status !== 404) {
      $toast.error('Failed to load chat plugins')
    }
  } finally {
    pending.value = false
  }
}

const saveSettings = async () => {
  try {
    saving.value = true
    const settingsPayload = {}
    chatPlugins.value.forEach(plugin => {
      settingsPayload[plugin.type] = {
        active: plugin.active,
        value: plugin.value
      }
    })

    await createItem('/vendor/settings', {
      group: 'chat_plugins',
      settings: settingsPayload
    })
    router.push('/vendor/managed-shop')
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
