<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <div class="max-w-4xl mx-auto mb-8">
      <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 font-medium mb-4">
        <NuxtLink to="/vendor/managed-shop" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Manage Shop</NuxtLink>
        <ChevronRight class="w-4 h-4" />
        <span class="text-slate-900 dark:text-white font-bold">Email Settings</span>
      </div>
      
      <div class="flex items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Email SMTP Configuration</h1>
          <p class="text-slate-500 dark:text-slate-400 font-semibold mt-1">Configure your SMTP server to send automated emails.</p>
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
      <!-- SMTP Config -->
      <div class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm p-8 space-y-8 transition-colors duration-300">
        <div class="flex items-center gap-3 border-b border-slate-50 dark:border-slate-800 pb-6">
          <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl flex items-center justify-center">
            <Mail class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
          </div>
          <h2 class="text-xl font-black text-slate-900 dark:text-white">SMTP Server Details</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="space-y-2 relative">
            <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider ml-1">SMTP Host</label>
            <input v-model="smtp.host" type="text" autocomplete="off" placeholder="smtp.mailtrap.io" class="w-full h-14 px-6 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200">
          </div>
          
          <div class="space-y-2 relative">
            <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider ml-1">SMTP Port</label>
            <input v-model="smtp.port" type="text" autocomplete="off" placeholder="2525" class="w-full h-14 px-6 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200">
          </div>

          <div class="space-y-2 relative">
            <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider ml-1">Encryption</label>
            <select v-model="smtp.encryption" class="w-full h-14 px-6 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200 appearance-none">
              <option value="tls">TLS</option>
              <option value="ssl">SSL</option>
              <option value="">None</option>
            </select>
            <ChevronDown class="w-5 h-5 text-slate-400 dark:text-slate-500 absolute right-4 top-10 pointer-events-none" />
          </div>

          <div class="space-y-2 relative">
            <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider ml-1">SMTP Username</label>
            <input v-model="smtp.username" type="text" autocomplete="off" placeholder="your-username" class="w-full h-14 px-6 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200">
          </div>

          <div class="space-y-2 relative">
            <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider ml-1">SMTP Password</label>
            <input v-model="smtp.password" :type="showPassword ? 'text' : 'password'" autocomplete="new-password" placeholder="••••••••" class="w-full h-14 px-6 pr-12 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200">
            <button @click="showPassword = !showPassword" class="absolute right-4 top-10 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
              <EyeOff v-if="showPassword" class="w-4 h-4" />
              <Eye v-else class="w-4 h-4" />
            </button>
          </div>

          <div class="space-y-2 relative">
            <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider ml-1">From Email Address</label>
            <input v-model="smtp.from_address" type="email" autocomplete="off" placeholder="support@yourshop.com" class="w-full h-14 px-6 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200">
          </div>

          <div class="space-y-2 relative">
            <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider ml-1">From Name</label>
            <input v-model="smtp.from_name" type="text" autocomplete="off" placeholder="Your Shop Name" class="w-full h-14 px-6 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200">
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
  Mail, 
  ChevronDown,
  Eye,
  EyeOff
} from 'lucide-vue-next'
import { ref, reactive, onMounted } from 'vue'

definePageMeta({
  middleware: 'auth',
  permissions: 'settings.view'
})

const { $toast } = useNuxtApp()
const { getAll, createItem } = useCrud()

const pending = ref(true)
const saving = ref(false)
const showPassword = ref(false)

const smtp = reactive({
  host: '',
  port: '',
  encryption: 'tls',
  username: '',
  password: '',
  from_address: '',
  from_name: ''
})

const loadSettings = async () => {
  try {
    pending.value = true
    const response = await getAll('/vendor/settings?group=smtp_settings')
    if (response.data) {
      Object.assign(smtp, response.data)
    }
  } catch (error) {
    if (error.response?.status !== 404) {
      $toast.error('Failed to load SMTP settings')
    }
  } finally {
    pending.value = false
  }
}

const saveSettings = async () => {
  try {
    saving.value = true
    await createItem('/vendor/settings', {
      group: 'smtp_settings',
      settings: smtp
    })
    navigateTo('/vendor/managed-shop')
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
