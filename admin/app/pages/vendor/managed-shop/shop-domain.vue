<template>
  <div class="p-8 bg-[#F5F7FA] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <!-- Header -->
    <div class="max-w-6xl mx-auto flex flex-col sm:flex-row sm:items-center gap-4 mb-8">
      <NuxtLink 
        to="/vendor/managed-shop" 
        class="w-10 h-10 bg-white dark:bg-slate-900 rounded-full flex items-center justify-center border border-slate-200 dark:border-slate-800 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-slate-700 dark:text-slate-300"
      >
        <ChevronLeft class="w-5 h-5" />
      </NuxtLink>
      <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Domain Settings</h1>
    </div>

    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
    </div>
    <div v-else class="max-w-6xl mx-auto space-y-6">
      <!-- Status Cards Section -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Main Domain Card -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 sm:p-6 flex items-center justify-between transition-colors duration-300">
          <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center">
              <Layers class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
            </div>
            <div>
              <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Main Domain</p>
              <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-white leading-tight">{{ form.customDomain || 'No Domain Set' }}</h2>
            </div>
          </div>
          <span v-if="form.customDomain" class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-100 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 text-[11px] font-medium rounded-full uppercase tracking-wider">Active</span>
        </div>

        <!-- Sub Domain Card -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 sm:p-6 flex items-center justify-between transition-colors duration-300">
          <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
              <Plus class="w-5 h-5 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
              <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Sub Domain</p>
              <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-white leading-tight">
                {{ form.subDomain ? `${form.subDomain}.${mainAppDomain}` : `${fallbackSubdomain}.${mainAppDomain}` }}
              </h2>
            </div>
          </div>
          <span v-if="form.subDomain" class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-100 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 text-[11px] font-medium rounded-full uppercase tracking-wider">Active</span>
        </div>
      </div>

      <!-- Domain Configuration Forms -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Main Domain Form -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sm:p-8 space-y-5 transition-colors duration-300 flex flex-col justify-between">
          <div>
            <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-white mb-5">Main Domain</h2>
            <div class="space-y-4">
              <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Custom Domain <span class="text-red-500">*</span></label>
                <input 
                  v-model="form.customDomain"
                  type="text" 
                  placeholder="example.com" 
                  class="w-full h-11 px-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400"
                >
                <div class="flex items-start gap-2 text-slate-500 mt-2">
                  <AlertTriangle class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" />
                  <span class="text-[13px] leading-snug">Enter a matching domain like <span class="font-medium text-slate-700 dark:text-slate-300">yourdomain.com</span></span>
                </div>
              </div>
            </div>
          </div>
          <div class="pt-4 mt-auto">
            <button @click="saveSettings" :disabled="saving" class="px-6 py-2.5 bg-slate-900 dark:bg-slate-800 text-white font-medium text-sm rounded-lg hover:bg-slate-800 dark:hover:bg-slate-700 transition-colors shadow-sm disabled:opacity-50 flex items-center justify-center gap-2 w-max">
              <span v-if="saving" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
              <span>{{ saving ? 'Saving...' : 'Save Changes' }}</span>
            </button>
          </div>
        </div>

        <!-- Sub Domain Form -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sm:p-8 space-y-5 transition-colors duration-300 flex flex-col justify-between">
          <div>
            <div class="flex items-start gap-2 text-slate-500 mb-5">
              <AlertTriangle class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" />
              <span class="text-[13px] leading-snug">You can change this subdomain <strong class="font-semibold text-slate-700 dark:text-slate-300">only once</strong>.</span>
            </div>
            <div class="space-y-4">
              <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Sub Domain <span class="text-red-500">*</span></label>
                <div class="flex rounded-lg shadow-sm">
                  <input 
                    v-model="form.subDomain"
                    type="text" 
                    :placeholder="fallbackSubdomain" 
                    class="flex-grow h-11 px-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 border-r-0 rounded-l-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400"
                  >
                  <div class="h-11 px-4 bg-slate-100 dark:bg-slate-800 border-y border-r border-slate-200 dark:border-slate-700 rounded-r-lg flex items-center justify-center text-slate-500 dark:text-slate-400 text-sm font-medium">
                    .{{ mainAppDomain }}
                  </div>
                </div>
                <button class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 text-[13px] font-medium transition-colors pt-1 focus:outline-none focus:underline mt-1 inline-block">
                  Check Availability
                </button>
              </div>
            </div>
          </div>
          <div class="pt-4 mt-auto">
            <button @click="saveSettings" :disabled="saving" class="px-6 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 font-medium text-sm rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors shadow-sm disabled:opacity-50 flex items-center justify-center gap-2 w-max">
              <span v-if="saving" class="w-4 h-4 border-2 border-slate-500 border-t-slate-800 rounded-full animate-spin"></span>
              <span>{{ saving ? 'Saving...' : 'Save Changes' }}</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Domain Connection Instructions Card -->
      <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-300">
        <div class="bg-[#18181B] dark:bg-slate-950 px-6 py-5 text-white">
          <h2 class="text-lg font-semibold flex items-center gap-2">
            <Plus class="w-5 h-5" />
            Connect Your domain
          </h2>
          <p class="text-slate-300 dark:text-slate-400 text-sm mt-1 mb-0">To use your own domain (e.g. myshop.com), please follow the steps below.</p>
        </div>
        
        <div class="p-6 space-y-6">
          <ul class="space-y-2.5 text-[14px] text-slate-600 dark:text-slate-300">
            <li class="flex items-start">
              <span class="mr-1">1.</span>
              <span>Login to your domain provider (Cloudflare, GoDaddy, Namecheap, Hostinger, etc.).</span>
            </li>
            <li class="flex items-start">
              <span class="mr-1">2.</span>
              <span>Go to <strong class="font-semibold text-slate-800 dark:text-slate-200">DNS Settings</strong> section.</span>
            </li>
            <li class="flex items-start">
              <span class="mr-1">3.</span>
              <span>Add a new <strong class="font-semibold text-slate-800 dark:text-slate-200">record</strong>:</span>
            </li>
          </ul>

          <!-- DNS Table -->
          <div class="border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden bg-white dark:bg-slate-900 transition-colors duration-300">
            <table class="w-full text-left border-collapse">
              <thead class="bg-slate-50 dark:bg-slate-800/50">
                <tr class="border-b border-slate-200 dark:border-slate-700">
                  <th class="px-5 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300">Type</th>
                  <th class="px-5 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300">Host/Name</th>
                  <th class="px-5 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300">Value/target</th>
                  <th class="px-5 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300">TTL</th>
                  <th class="px-5 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 text-center">Copy</th>
                </tr>
              </thead>
              <tbody>
                <tr class="bg-white dark:bg-slate-900">
                  <td class="px-5 py-4 text-sm font-semibold text-slate-800 dark:text-white">CNAME</td>
                  <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">@</td>
                  <td class="px-5 py-4 text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 cursor-pointer">{{ form.subDomain ? `${form.subDomain}.${mainAppDomain}` : `${fallbackSubdomain}.${mainAppDomain}` }}</td>
                  <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-400">Auto</td>
                  <td class="px-5 py-4 text-center">
                    <button @click="copyToClipboard(form.subDomain ? `${form.subDomain}.${mainAppDomain}` : `${fallbackSubdomain}.${mainAppDomain}`)" class="px-4 py-1.5 bg-[#6366F1] text-white text-xs font-medium rounded-full hover:bg-[#4F46E5] transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#6366F1] dark:focus:ring-offset-slate-900">
                      Copy
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="flex items-start gap-2 text-[14px] text-slate-500 dark:text-slate-400 mt-2">
            <Zap class="w-4 h-4 text-orange-400 flex-shrink-0 mt-0.5 fill-orange-400" />
            <p class="leading-relaxed">
              <span class="text-orange-400 italic">Tip:</span> If you want your domain <span class="italic font-medium text-slate-600 dark:text-slate-300">{{ form.customDomain || 'myshop.com' }}</span> (without www) to work, also add a redirect from <strong class="font-semibold text-slate-700 dark:text-slate-200">root domain</strong> to <strong class="font-semibold text-slate-700 dark:text-slate-200">www.{{ form.customDomain || 'myshop.com' }}</strong> inside your domain panel.
            </p>
          </div>

          <div class="pt-2">
            <button class="flex items-center gap-2.5 px-5 py-2.5 bg-[#DC2626] text-white text-sm font-medium rounded-lg hover:bg-[#B91C1C] transition-colors shadow-sm">
              <Play class="w-4 h-4 fill-white" />
              Watch Video
            </button>
          </div>
        </div>
      </div>

      <!-- Hosting Promo Banner -->
      <div class="relative w-full rounded-xl overflow-hidden bg-slate-900 border border-slate-800 shadow-sm mt-2">
        <!-- Clean gradient background -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-900 via-[#18181B] to-slate-800"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_100%_0%,rgba(99,102,241,0.15),transparent_50%)]"></div>
        
        <!-- Content -->
        <div class="relative p-6 sm:p-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
          <div class="max-w-md">
            <div class="flex items-center gap-2 mb-2">
              <span class="px-2 py-0.5 bg-indigo-500/10 text-indigo-400 text-[10px] font-semibold uppercase tracking-wider rounded border border-indigo-500/20">Partner Offer</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-white tracking-tight mb-2">Need Domain & Hosting?</h2>
            <p class="text-slate-400 text-sm leading-relaxed">
              Power your store with reliable hosting & domain services from our trusted astronomy host partner.
            </p>
          </div>
          
          <div class="flex-shrink-0">
            <button class="flex items-center justify-center gap-2 w-full sm:w-auto px-6 py-3 bg-[#6366F1] text-white font-medium text-sm rounded-lg hover:bg-[#4F46E5] transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-[#6366F1] focus:ring-offset-slate-900 shadow-sm">
              Buy Hosting & Domain
              <ArrowUpRight class="w-4 h-4" />
            </button>
          </div>
        </div>
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
const auth = useAuthStore()

const mainAppDomain = ref('selluee.test')

const fallbackSubdomain = computed(() => {
  const profile = auth.user?.vendor_profile
  if (profile?.store_slug) return profile.store_slug
  if (profile?.store_name) return profile.store_name.toLowerCase().replace(/[^a-z0-9]/g, '')
  if (auth.user?.name) return auth.user.name.toLowerCase().replace(/[^a-z0-9]/g, '')
  return 'yourstore'
})

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
    const errData = error.response?.data
    if (errData?.errors) {
      const firstError = Object.values(errData.errors)[0][0]
      $toast.error(firstError)
    } else {
      $toast.error(errData?.message || 'Failed to save domain settings')
    }
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
