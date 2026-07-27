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
        <!-- Sub Domain Card (default — always available) -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 sm:p-6 flex items-center justify-between transition-colors duration-300">
          <div class="flex items-center gap-4 min-w-0">
            <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
              <Globe class="w-5 h-5 text-blue-600 dark:text-blue-400" />
            </div>
            <div class="min-w-0">
              <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Default Subdomain</p>
              <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-white leading-tight truncate">{{ subdomainFull }}</h2>
              <a
                v-if="meta.subdomainUrl"
                :href="meta.subdomainUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mt-1 inline-flex items-center gap-1"
              >
                Visit store <ExternalLink class="w-3 h-3" />
              </a>
            </div>
          </div>
          <span class="px-2.5 py-1 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-100 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 text-[11px] font-medium rounded-full uppercase tracking-wider flex-shrink-0">Active</span>
        </div>

        <!-- Custom Domain Card -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-5 sm:p-6 flex items-center justify-between transition-colors duration-300">
          <div class="flex items-center gap-4 min-w-0">
            <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
              <Layers class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
            </div>
            <div class="min-w-0">
              <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Custom Domain</p>
              <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-white leading-tight truncate">
                {{ form.customDomain || 'Not configured' }}
              </h2>
              <a
                v-if="meta.customDomainUrl && meta.customDomainVerified"
                :href="meta.customDomainUrl"
                target="_blank"
                rel="noopener noreferrer"
                class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mt-1 inline-flex items-center gap-1"
              >
                Visit store <ExternalLink class="w-3 h-3" />
              </a>
            </div>
          </div>
          <span
            v-if="form.customDomain"
            :class="customDomainStatusClass"
            class="px-2.5 py-1 text-[11px] font-medium rounded-full uppercase tracking-wider flex-shrink-0 border"
          >
            {{ customDomainStatusLabel }}
          </span>
        </div>
      </div>

      <!-- Domain Configuration Forms -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Sub Domain Form -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sm:p-8 space-y-5 transition-colors duration-300 flex flex-col justify-between">
          <div>
            <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-white mb-2">Subdomain</h2>
            <p class="text-[13px] text-slate-500 dark:text-slate-400 mb-5">
              Your store is live on a free subdomain by default. You can customize it
              <strong v-if="!meta.subDomainLocked" class="text-slate-700 dark:text-slate-300">once</strong>
              <strong v-else class="text-slate-700 dark:text-slate-300">(locked)</strong>.
            </p>
            <div class="space-y-4">
              <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Subdomain</label>
                <div class="flex rounded-lg shadow-sm">
                  <input 
                    v-model="form.subDomain"
                    type="text" 
                    :disabled="meta.subDomainLocked"
                    :placeholder="fallbackSubdomain" 
                    class="flex-grow h-11 px-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 border-r-0 rounded-l-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 disabled:opacity-60 disabled:cursor-not-allowed"
                  >
                  <div class="h-11 px-4 bg-slate-100 dark:bg-slate-800 border-y border-r border-slate-200 dark:border-slate-700 rounded-r-lg flex items-center justify-center text-slate-500 dark:text-slate-400 text-sm font-medium">
                    .{{ mainAppDomain }}
                  </div>
                </div>
                <div class="flex items-center gap-3 pt-1">
                  <button
                    type="button"
                    :disabled="checkingSubdomain || meta.subDomainLocked || !form.subDomain"
                    @click="checkAvailability"
                    class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 text-[13px] font-medium transition-colors focus:outline-none focus:underline disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center gap-1.5"
                  >
                    <span v-if="checkingSubdomain" class="w-3 h-3 border-2 border-indigo-400 border-t-transparent rounded-full animate-spin"></span>
                    Check Availability
                  </button>
                  <span v-if="availabilityMessage" :class="availabilityClass" class="text-[13px] font-medium">
                    {{ availabilityMessage }}
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="pt-4 mt-auto">
            <button
              type="button"
              @click="saveSubdomain"
              :disabled="savingSubdomain || meta.subDomainLocked"
              class="px-6 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 border border-slate-200 dark:border-slate-700 font-medium text-sm rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors shadow-sm disabled:opacity-50 flex items-center justify-center gap-2 w-max"
            >
              <span v-if="savingSubdomain" class="w-4 h-4 border-2 border-slate-500 border-t-slate-800 rounded-full animate-spin"></span>
              <span>{{ savingSubdomain ? 'Saving...' : meta.subDomainLocked ? 'Subdomain Locked' : 'Save Subdomain' }}</span>
            </button>
          </div>
        </div>

        <!-- Custom Domain Form -->
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sm:p-8 space-y-5 transition-colors duration-300 flex flex-col justify-between">
          <div>
            <h2 class="text-base sm:text-lg font-semibold text-slate-900 dark:text-white mb-2">Custom Domain</h2>
            <p class="text-[13px] text-slate-500 dark:text-slate-400 mb-5">
              Connect your own domain (e.g. <span class="font-medium text-slate-700 dark:text-slate-300">myshop.com</span>) to your store.
            </p>
            <div class="space-y-4">
              <div class="space-y-1.5">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Domain</label>
                <input 
                  v-model="form.customDomain"
                  type="text" 
                  placeholder="myshop.com" 
                  class="w-full h-11 px-4 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400"
                >
                <div class="flex items-start gap-2 text-slate-500 mt-2">
                  <AlertTriangle class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" />
                  <span class="text-[13px] leading-snug">Enter your domain without <span class="font-medium">http://</span> or <span class="font-medium">www</span></span>
                </div>
              </div>
            </div>
          </div>
          <div class="pt-4 mt-auto flex flex-wrap gap-2">
            <button
              type="button"
              @click="saveCustomDomain"
              :disabled="savingCustomDomain"
              class="px-6 py-2.5 bg-slate-900 dark:bg-slate-800 text-white font-medium text-sm rounded-lg hover:bg-slate-800 dark:hover:bg-slate-700 transition-colors shadow-sm disabled:opacity-50 flex items-center justify-center gap-2"
            >
              <span v-if="savingCustomDomain" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
              <span>{{ savingCustomDomain ? 'Saving...' : 'Save Custom Domain' }}</span>
            </button>
            <button
              v-if="form.customDomain"
              type="button"
              @click="removeCustomDomain"
              :disabled="removingCustomDomain"
              class="px-4 py-2.5 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-900/50 font-medium text-sm rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors disabled:opacity-50"
            >
              Remove
            </button>
          </div>
        </div>
      </div>

      <!-- Domain Connection Instructions Card -->
      <div
        v-if="form.customDomain"
        class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-300"
      >
        <div class="bg-[#18181B] dark:bg-slate-950 px-6 py-5 text-white">
          <h2 class="text-lg font-semibold flex items-center gap-2">
            <Link2 class="w-5 h-5" />
            Connect Your Custom Domain
          </h2>
          <p class="text-slate-300 dark:text-slate-400 text-sm mt-1 mb-0">
            Add these DNS records at your domain provider, then click Verify Connection.
          </p>
        </div>
        
        <div class="p-6 space-y-6">
          <ul class="space-y-2.5 text-[14px] text-slate-600 dark:text-slate-300">
            <li class="flex items-start">
              <span class="mr-1">1.</span>
              <span>Login to your domain provider (Cloudflare, GoDaddy, Namecheap, Hostinger, etc.).</span>
            </li>
            <li class="flex items-start">
              <span class="mr-1">2.</span>
              <span>Go to <strong class="font-semibold text-slate-800 dark:text-slate-200">DNS Settings</strong>.</span>
            </li>
            <li class="flex items-start">
              <span class="mr-1">3.</span>
              <span>Add the DNS records below:</span>
            </li>
          </ul>

          <!-- DNS Table -->
          <div class="border border-slate-200 dark:border-slate-700 rounded-lg overflow-x-auto bg-white dark:bg-slate-900 transition-colors duration-300">
            <table class="w-full text-left border-collapse min-w-[540px]">
              <thead class="bg-slate-50 dark:bg-slate-800/50">
                <tr class="border-b border-slate-200 dark:border-slate-700">
                  <th class="px-5 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300">Type</th>
                  <th class="px-5 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300">Host/Name</th>
                  <th class="px-5 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300">Value/Target</th>
                  <th class="px-5 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300">TTL</th>
                  <th class="px-5 py-3 text-sm font-semibold text-slate-700 dark:text-slate-300 text-center">Copy</th>
                </tr>
              </thead>
              <tbody>
                <tr class="bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800">
                  <td class="px-5 py-4 text-sm font-semibold text-slate-800 dark:text-white">CNAME</td>
                  <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">www</td>
                  <td class="px-5 py-4 text-sm text-blue-600 dark:text-blue-400">{{ cnameTarget }}</td>
                  <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-400">Auto</td>
                  <td class="px-5 py-4 text-center">
                    <button @click="copyToClipboard(cnameTarget)" class="px-4 py-1.5 bg-[#6366F1] text-white text-xs font-medium rounded-full hover:bg-[#4F46E5] transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#6366F1] dark:focus:ring-offset-slate-900">
                      Copy
                    </button>
                  </td>
                </tr>
                <tr v-if="meta.platformIp" class="bg-white dark:bg-slate-900">
                  <td class="px-5 py-4 text-sm font-semibold text-slate-800 dark:text-white">A</td>
                  <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">@</td>
                  <td class="px-5 py-4 text-sm text-blue-600 dark:text-blue-400">{{ meta.platformIp }}</td>
                  <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-400">Auto</td>
                  <td class="px-5 py-4 text-center">
                    <button @click="copyToClipboard(meta.platformIp)" class="px-4 py-1.5 bg-[#6366F1] text-white text-xs font-medium rounded-full hover:bg-[#4F46E5] transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#6366F1] dark:focus:ring-offset-slate-900">
                      Copy
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="flex items-start gap-2 text-[14px] text-slate-500 dark:text-slate-400">
            <Zap class="w-4 h-4 text-orange-400 flex-shrink-0 mt-0.5 fill-orange-400" />
            <p class="leading-relaxed">
              <span class="text-orange-400 italic">Tip:</span>
              Point <strong class="font-semibold text-slate-700 dark:text-slate-200">www.{{ form.customDomain }}</strong>
              to <strong class="font-semibold text-slate-700 dark:text-slate-200">{{ cnameTarget }}</strong>.
              <span v-if="!meta.platformIp">For the root domain (@), set up a redirect to www in your domain panel.</span>
            </p>
          </div>

          <div class="flex flex-wrap gap-3 pt-1">
            <button
              type="button"
              @click="verifyCustomDomain"
              :disabled="verifyingDomain"
              class="flex items-center gap-2.5 px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm disabled:opacity-50"
            >
              <span v-if="verifyingDomain" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
              <CheckCircle2 v-else class="w-4 h-4" />
              {{ verifyingDomain ? 'Verifying...' : 'Verify Connection' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Info: subdomain always works -->
      <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-900/40 rounded-xl p-5 flex items-start gap-3">
        <Globe class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" />
        <div>
          <p class="text-sm font-semibold text-blue-900 dark:text-blue-200">Your subdomain is always active</p>
          <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">
            Customers can always visit your store at
            <a :href="meta.subdomainUrl || '#'" target="_blank" class="font-medium underline">{{ subdomainFull }}</a>
            even before connecting a custom domain.
          </p>
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
  AlertTriangle, 
  Zap,
  Globe,
  ExternalLink,
  Link2,
  CheckCircle2,
} from 'lucide-vue-next'

const { getAll, createItem } = useCrud()
const { $toast } = useNuxtApp()
const config = useRuntimeConfig()
const { fallbackSubdomain } = useStorefrontUrl()

const mainAppDomain = ref(config.public.storefrontDomain || 'selluee.test')

const form = reactive({
  customDomain: '',
  subDomain: '',
})

const meta = reactive({
  customDomainVerified: false,
  subDomainLocked: false,
  platformDomain: '',
  platformIp: null,
  cnameTarget: '',
  subdomainUrl: null,
  customDomainUrl: null,
})

const pending = ref(true)
const savingSubdomain = ref(false)
const savingCustomDomain = ref(false)
const removingCustomDomain = ref(false)
const verifyingDomain = ref(false)
const checkingSubdomain = ref(false)
const availabilityMessage = ref('')
const availabilityAvailable = ref(null)

const subdomainFull = computed(() => {
  const sub = form.subDomain || fallbackSubdomain.value
  return `${sub}.${mainAppDomain.value}`
})

const cnameTarget = computed(() => meta.cnameTarget || subdomainFull.value)

const customDomainStatusLabel = computed(() => {
  if (!form.customDomain) return ''
  return meta.customDomainVerified ? 'Connected' : 'Pending DNS'
})

const customDomainStatusClass = computed(() => {
  if (meta.customDomainVerified) {
    return 'bg-emerald-50 dark:bg-emerald-900/30 border-emerald-100 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400'
  }
  return 'bg-amber-50 dark:bg-amber-900/30 border-amber-100 dark:border-amber-800 text-amber-600 dark:text-amber-400'
})

const availabilityClass = computed(() => {
  if (availabilityAvailable.value === true) return 'text-emerald-600 dark:text-emerald-400'
  if (availabilityAvailable.value === false) return 'text-red-600 dark:text-red-400'
  return 'text-slate-500'
})

const applySettings = (data) => {
  if (!data) return
  form.customDomain = data.customDomain || ''
  form.subDomain = data.subDomain || fallbackSubdomain.value
  meta.customDomainVerified = !!data.customDomainVerified
  meta.subDomainLocked = !!data.subDomainLocked
  meta.platformDomain = data.platformDomain || mainAppDomain.value
  meta.platformIp = data.platformIp || null
  meta.cnameTarget = data.cnameTarget || ''
  meta.subdomainUrl = data.subdomainUrl || null
  meta.customDomainUrl = data.customDomainUrl || null
  if (data.platformDomain) {
    mainAppDomain.value = data.platformDomain
  }
}

const loadSettings = async () => {
  try {
    pending.value = true
    const response = await getAll('/vendor/settings?group=shop_domain')
    applySettings(response.data)
  } catch (error) {
    if (error.response?.status !== 404) {
      $toast.error('Failed to load domain settings')
    }
  } finally {
    pending.value = false
  }
}

const handleSaveError = (error) => {
  const errData = error?.data || error?.response?.data
  if (errData?.errors) {
    const firstError = Object.values(errData.errors)[0][0]
    $toast.error(firstError)
  } else {
    $toast.error(errData?.message || 'Failed to save domain settings')
  }
}

const saveSubdomain = async () => {
  try {
    savingSubdomain.value = true
    const response = await createItem('/vendor/settings', {
      group: 'shop_domain',
      settings: { subDomain: form.subDomain },
    }, null, false)
    applySettings(response.data)
    availabilityMessage.value = ''
    availabilityAvailable.value = null
    $toast.success('Subdomain saved successfully')
  } catch (error) {
    handleSaveError(error)
  } finally {
    savingSubdomain.value = false
  }
}

const saveCustomDomain = async () => {
  try {
    savingCustomDomain.value = true
    const response = await createItem('/vendor/settings', {
      group: 'shop_domain',
      settings: { customDomain: form.customDomain },
    }, null, false)
    applySettings(response.data)
    $toast.success('Custom domain saved. Configure DNS records below, then verify.')
  } catch (error) {
    handleSaveError(error)
  } finally {
    savingCustomDomain.value = false
  }
}

const removeCustomDomain = async () => {
  if (!confirm('Remove your custom domain? Your store will remain available on your subdomain.')) {
    return
  }
  try {
    removingCustomDomain.value = true
    const config = useRuntimeConfig()
    const tokenStore = useTokenStore()
    const response = await $fetch('/vendor/settings/custom-domain', {
      baseURL: config.public.apiBase,
      method: 'DELETE',
      headers: {
        Accept: 'application/json',
        Authorization: tokenStore.token ? `Bearer ${tokenStore.token}` : '',
      },
    })
    applySettings(response.data)
    $toast.success('Custom domain removed')
  } catch (error) {
    handleSaveError(error)
  } finally {
    removingCustomDomain.value = false
  }
}

const checkAvailability = async () => {
  try {
    checkingSubdomain.value = true
    availabilityMessage.value = ''
    const response = await getAll('/vendor/settings/check-subdomain', {
      subdomain: form.subDomain,
    })
    availabilityAvailable.value = response.available
    availabilityMessage.value = response.message
  } catch (error) {
    availabilityAvailable.value = false
    availabilityMessage.value = error?.data?.message || 'Could not check availability'
  } finally {
    checkingSubdomain.value = false
  }
}

const verifyCustomDomain = async () => {
  try {
    verifyingDomain.value = true
    const config = useRuntimeConfig()
    const tokenStore = useTokenStore()
    const response = await $fetch('/vendor/settings/verify-custom-domain', {
      baseURL: config.public.apiBase,
      method: 'POST',
      headers: {
        Accept: 'application/json',
        Authorization: tokenStore.token ? `Bearer ${tokenStore.token}` : '',
      },
    })
    applySettings(response.data)
    $toast.success(response.message || 'Domain verified successfully')
  } catch (error) {
    applySettings(error?.data?.data)
    $toast.error(error?.data?.message || 'DNS verification failed')
  } finally {
    verifyingDomain.value = false
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
