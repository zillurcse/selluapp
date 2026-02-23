<template>
  <div class="p-8 bg-[#F5F7FA] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <!-- Header -->
    <div class="max-w-6xl mx-auto flex items-center gap-4 mb-8">
      <NuxtLink 
        to="/vendor/managed-shop" 
        class="w-11 h-11 bg-white dark:bg-slate-900 rounded-full flex items-center justify-center border border-slate-100 dark:border-slate-800 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-all text-[#1E293B] dark:text-white"
      >
        <ChevronLeft class="w-6 h-6" />
      </NuxtLink>
      <h1 class="text-2xl font-black text-[#1E293B] dark:text-white tracking-tight">Domain Settings</h1>
    </div>

    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
    </div>
    <div v-else class="max-w-6xl mx-auto space-y-6">
      <!-- Status Cards Section -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Main Domain Card -->
        <div class="bg-white dark:bg-slate-900 rounded-[24px] border border-slate-100 dark:border-slate-800 shadow-sm p-6 flex items-center justify-between transition-colors duration-300">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-2xl flex items-center justify-center">
              <Layers class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
            </div>
            <div>
              <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none mb-1.5">Main Domain</p>
              <h2 class="text-lg font-black text-slate-900 dark:text-white leading-tight">{{ form.customDomain || 'No Domain Set' }}</h2>
            </div>
          </div>
          <span v-if="form.customDomain" class="px-3 py-1 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-[10px] font-black rounded-full uppercase">Active</span>
        </div>

        <!-- Sub Domain Card -->
        <div class="bg-white dark:bg-slate-900 rounded-[24px] border border-slate-100 dark:border-slate-800 shadow-sm p-6 flex items-center justify-between transition-colors duration-300">
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center">
              <Plus class="w-6 h-6 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
              <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest leading-none mb-1.5">Sub Domain</p>
              <h2 class="text-lg font-black text-slate-900 dark:text-white leading-tight">
                {{ form.subDomain ? `${form.subDomain}.${mainAppDomain}` : `yourstore.${mainAppDomain}` }}
              </h2>
            </div>
          </div>
          <span v-if="form.subDomain" class="px-3 py-1 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 text-[10px] font-black rounded-full uppercase">Active</span>
        </div>
      </div>

      <!-- Domain Configuration Forms -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Main Domain Form -->
        <div class="bg-[#F8FAFC] dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 p-8 space-y-6 transition-colors duration-300">
          <h2 class="text-xl font-black text-slate-900 dark:text-white">Main Domain</h2>
          <div class="space-y-4">
            <div class="space-y-2">
              <label class="text-xs font-black text-slate-900 dark:text-slate-300">Custom Domain <span class="text-red-500">*</span></label>
              <input 
                v-model="form.customDomain"
                type="text" 
                placeholder="example.com" 
                class="w-full h-14 px-6 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200"
              >
              <div class="flex items-center gap-2 text-[#D97706] mt-2">
                <AlertTriangle class="w-4 h-4" />
                <span class="text-xs font-bold">Enter a domain like yourdomain.com</span>
              </div>
            </div>
            <button @click="saveSettings" :disabled="saving" class="px-8 py-4 bg-[#1E293B] dark:bg-slate-800 text-white font-black rounded-xl hover:bg-slate-800 dark:hover:bg-slate-700 transition-all active:scale-95 shadow-lg shadow-slate-900/10 disabled:opacity-50 flex items-center gap-2">
              <span v-if="saving">Saving...</span>
              <span v-else>Save Changes</span>
            </button>
          </div>
        </div>

        <!-- Sub Domain Form -->
        <div class="bg-[#F8FAFC] dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 p-8 space-y-6 transition-colors duration-300">
          <div class="flex items-center gap-2 text-[#D97706]">
            <AlertTriangle class="w-4 h-4" />
            <span class="text-xs font-bold tracking-tight">You can change subdomain only once</span>
          </div>
          <div class="space-y-4">
            <div class="space-y-2">
              <label class="text-xs font-black text-slate-900 dark:text-slate-300">Sub Domain <span class="text-red-500">*</span></label>
              <div class="flex">
                <input 
                  v-model="form.subDomain"
                  type="text" 
                  placeholder="yourstore" 
                  class="flex-grow h-14 px-6 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 border-r-0 rounded-l-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200"
                >
                <div class="h-14 px-6 bg-[#0F172A] border border-[#0F172A] rounded-r-2xl flex items-center justify-center text-white font-bold">
                  .{{ mainAppDomain }}
                </div>
              </div>
              <button class="text-indigo-600 text-xs font-black border-b-2 border-indigo-600/20 hover:border-indigo-600 transition-all pt-1">
                Check Availibility
              </button>
            </div>
            <button @click="saveSettings" :disabled="saving" class="px-8 py-4 bg-[#94A3B8] dark:bg-slate-700 text-white font-black rounded-xl hover:bg-slate-500 dark:hover:bg-slate-600 transition-all disabled:opacity-50 flex items-center gap-2">
              <span v-if="saving">Saving...</span>
              <span v-else>Save Changes</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Domain Connection Instructions Card -->
      <div v-if="form.customDomain" class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-300">
        <div class="bg-[#0F172A] dark:bg-slate-950 p-6 text-white">
          <h2 class="text-xl font-black flex items-center gap-3">
            <Plus class="w-6 h-6" />
            Connect Your domain
          </h2>
          <p class="text-slate-400 dark:text-slate-500 text-sm font-medium mt-1">To use your own domain (e.g. {{ form.customDomain }}), please follow the steps below.</p>
        </div>
        
        <div class="p-8 space-y-8">
          <div class="space-y-4">
            <ul class="space-y-3 text-sm font-semibold text-slate-600 dark:text-slate-400">
              <li class="flex items-start gap-2">
                <span class="text-slate-400">1.</span>
                <span>Login to your domain provider (Cloudflare, GoDaddy, Namecheap, Hostinger, etc.).</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-slate-400">2.</span>
                <span>Go to <strong class="text-slate-900 dark:text-slate-100">DNS Settings</strong> section.</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="text-slate-400">3.</span>
                <span>Add a new <strong class="text-slate-900 dark:text-slate-100">record</strong>:</span>
              </li>
            </ul>

            <!-- DNS Table -->
            <div class="border border-slate-100 dark:border-slate-800 rounded-[20px] overflow-hidden bg-[#F8FAFC] dark:bg-slate-900 transition-colors duration-300">
              <table class="w-full text-left border-collapse">
                <thead>
                  <tr class="border-b border-slate-100 dark:border-slate-800">
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Type</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Host/Name</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Value/target</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">TTL</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Copy</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="bg-white dark:bg-slate-900 transition-colors duration-300">
                    <td class="px-6 py-5 text-sm font-black text-slate-900 dark:text-white uppercase">CNAME</td>
                    <td class="px-6 py-5 text-sm font-semibold text-slate-600 dark:text-slate-400">@</td>
                    <td class="px-6 py-5 text-sm font-bold text-indigo-600 dark:text-indigo-400">{{ form.subDomain ? `${form.subDomain}.${mainAppDomain}` : `yourstore.${mainAppDomain}` }}</td>
                    <td class="px-6 py-5 text-sm font-semibold text-slate-500 dark:text-slate-500">Auto</td>
                    <td class="px-6 py-5 text-right">
                      <button @click="copyToClipboard(form.subDomain ? `${form.subDomain}.${mainAppDomain}` : `yourstore.${mainAppDomain}`)" class="p-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-600/10">
                        <Copy class="w-4 h-4" />
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="flex items-start gap-3 p-4 bg-orange-50 dark:bg-orange-900/10 border border-orange-100 dark:border-orange-900/20 rounded-2xl">
              <Zap class="w-5 h-5 text-orange-500 flex-shrink-0" />
              <p class="text-xs font-semibold text-slate-600 dark:text-slate-400 leading-relaxed">
                <span class="text-orange-600 dark:text-orange-400 font-bold italic">Tip:</span> If you want your domain <span class="italic text-slate-900 dark:text-slate-100 font-bold">{{ form.customDomain }}</span> (without www) to work, also add a redirect from <strong class="text-slate-900 dark:text-slate-100">root domain</strong> to <strong class="text-slate-900 dark:text-slate-100">www.{{ form.customDomain }}</strong> inside your domain panel.
              </p>
            </div>

            <button class="flex items-center gap-3 px-6 py-3.5 bg-[#DC2626] text-white font-black rounded-[14px] hover:bg-red-700 transition-all shadow-lg shadow-red-600/10">
              <Play class="w-4 h-4 fill-white" />
              Watch Video
            </button>
          </div>
        </div>
      </div>

      <!-- Hosting Promo Banner -->
      <div class="relative w-full h-[220px] rounded-[32px] overflow-hidden group">
        <!-- Static background representing the space theme -->
        <div class="absolute inset-0 bg-gradient-to-r from-black via-black/80 to-[#282E6A]"></div>
        <div class="absolute inset-0 opacity-40 mix-blend-overlay">
          <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_50%,#4F46E5_0%,transparent_50%)]"></div>
        </div>
        
        <!-- Content -->
        <div class="relative h-full px-12 flex flex-col justify-center gap-1">
          <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Astronomy Host</p>
          <h2 class="text-4xl font-black text-white tracking-tight">Need Domain & Hosting?</h2>
          <p class="text-slate-300 font-medium max-w-[420px] mt-2 leading-relaxed">
            Power your store with reliable hosting & domain services from our trusted partner.
          </p>
          
          <div class="mt-8 flex items-end justify-between">
            <button class="flex items-center gap-3 px-8 py-4 bg-[#5046E5] text-white font-black rounded-xl hover:bg-[#4338CA] transition-all shadow-xl shadow-[#5046E5]/20 group-hover:scale-105">
              Buy Hosting & Domain
              <ArrowUpRight class="w-5 h-5" />
            </button>
            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest opacity-60">Take Your Domain & Hosting</span>
          </div>
        </div>

        <!-- Planet Element Decoration (Simplified CSS version) -->
        <div class="absolute right-20 top-1/2 -translate-y-1/2 w-48 h-48 rounded-full bg-gradient-to-br from-[#E11D48] to-[#9F1239] blur-[2px] shadow-2xl opacity-20 group-hover:opacity-30 transition-opacity"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  middleware: 'auth',
  permissions: 'settings.view'
})
import { 
  ChevronLeft, 
  Layers, 
  Plus, 
  AlertTriangle, 
  Copy, 
  Play, 
  Zap,
  ArrowUpRight
} from 'lucide-vue-next'
import { reactive, ref, onMounted } from 'vue'

const router = useRouter()
const { getAll, createItem } = useCrud()
const { $toast } = useNuxtApp()

const mainAppDomain = ref('selluee.test')

const form = reactive({
  customDomain: '',
  subDomain: ''
})

const pending = ref(true)
const saving = ref(false)

const loadSettings = async () => {
  try {
    pending.value = true
    const response = await getAll('/vendor/settings?group=shop_domain')
    if (response.data) {
      Object.assign(form, response.data)
    }
  } catch (error) {
    if (error.response?.status !== 404) {
      $toast.error('Failed to load domains')
    }
  } finally {
    pending.value = false
  }
}

const saveSettings = async () => {
  try {
    saving.value = true
    await createItem('/vendor/settings', {
      group: 'shop_domain',
      settings: form
    })
    router.push('/vendor/managed-shop')
  } catch (error) {
    console.error(error)
    $toast.error('Failed to save domain settings')
  } finally {
    saving.value = false
  }
}

const copyToClipboard = async (text) => {
  try {
    await navigator.clipboard.writeText(text)
    $toast.success('Copied to clipboard!')
  } catch (err) {
    console.error(err)
  }
}

onMounted(() => {
  loadSettings()
})
</script>
