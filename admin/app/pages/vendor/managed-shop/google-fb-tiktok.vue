<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <!-- Header -->
    <div class="max-w-[1200px] mx-auto mb-8">
      <div class="flex items-center gap-4">
        <NuxtLink 
          to="/vendor/managed-shop" 
          class="w-10 h-10 bg-slate-900 rounded-full flex items-center justify-center text-white hover:bg-slate-800 transition-all active:scale-95 shadow-lg shadow-slate-900/20"
        >
          <ChevronLeft class="w-6 h-6" />
        </NuxtLink>
        <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight leading-none">GTM, Facebook & Social Login</h1>
      </div>
    </div>

    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
    </div>
    <div v-else class="max-w-[1200px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
      <!-- Main Content Area -->
      <div class="lg:col-span-8 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- GTM Setup -->
          <div class="bg-white dark:bg-slate-900 rounded-[24px] border border-slate-100 dark:border-slate-800 p-8 shadow-sm transition-colors duration-300">
            <div class="flex items-center gap-2 mb-6">
              <div class="w-8 h-8 rounded-lg flex items-center justify-center">
                <img src="https://www.gstatic.com/images/branding/product/2x/tag_manager_64dp.png" class="w-6 h-6 object-contain" alt="GTM" />
              </div>
              <h2 class="text-lg font-bold text-slate-800 dark:text-white">Setup Google Tag Manager</h2>
            </div>
            <div class="space-y-4">
              <div class="space-y-2">
                <label class="text-sm font-bold text-slate-600 dark:text-slate-400">GTM ID</label>
                <input 
                  v-model="form.gtmId"
                  type="text" 
                  placeholder="GTM-XXXXXXX"
                  class="w-full h-12 px-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200"
                >
              </div>
            </div>
          </div>

          <!-- GA4 Setup -->
          <div class="bg-white dark:bg-slate-900 rounded-[24px] border border-slate-100 dark:border-slate-800 p-8 shadow-sm transition-colors duration-300">
            <div class="flex items-center gap-2 mb-6">
              <div class="w-8 h-8 rounded-lg flex items-center justify-center">
                <img src="https://www.gstatic.com/images/branding/product/2x/analytics_64dp.png" class="w-6 h-6 object-contain" alt="GA4" />
              </div>
              <h2 class="text-lg font-bold text-slate-800 dark:text-white">Setup Google Analytics (GA4)</h2>
            </div>
            <div class="space-y-4">
              <div class="space-y-2">
                <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Measurement ID</label>
                <input 
                  v-model="form.gaMeasurementId"
                  type="text" 
                  placeholder="G-XXXXXXX"
                  class="w-full h-12 px-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200"
                >
              </div>
              <div class="space-y-2 relative">
                <input 
                  v-model="form.gaPassword"
                  :type="showGAPass ? 'text' : 'password'" 
                  placeholder="API Secret"
                  class="w-full h-12 px-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200"
                >
                <button 
                  @click="showGAPass = !showGAPass"
                  class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                >
                  <Eye v-if="!showGAPass" class="w-5 h-5" />
                  <EyeOff v-else class="w-5 h-5" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Facebook Conversion API & Pixel -->
        <div class="bg-white dark:bg-slate-900 rounded-[24px] border border-slate-100 dark:border-slate-800 p-8 shadow-sm transition-colors duration-300">
          <div class="flex items-center gap-2 mb-6">
            <Facebook class="w-6 h-6 text-blue-600 fill-current" />
            <h2 class="text-lg font-bold text-slate-800 dark:text-white">Facebook Conversion API & Pixel</h2>
          </div>
          <div class="space-y-6">
            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Pixel ID</label>
              <input 
                v-model="form.fbPixelId"
                type="text" 
                placeholder="Enter your Facebook Pixel ID"
                class="w-full h-12 px-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200"
              >
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Pixel Access Token</label>
              <div class="relative">
                <input 
                  v-model="form.fbPixelToken"
                  :type="showFBPixelToken ? 'text' : 'password'" 
                  placeholder="Pixel Access Token"
                  class="w-full h-12 px-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200 pr-12"
                >
                <button 
                  @click="showFBPixelToken = !showFBPixelToken"
                  class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                >
                  <Eye v-if="!showFBPixelToken" class="w-5 h-5" />
                  <EyeOff v-else class="w-5 h-5" />
                </button>
              </div>
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Pixel Test Event ID <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">(Just for testing. Clear after testing is done)</span></label>
              <input 
                v-model="form.fbTestEventId"
                type="text" 
                placeholder="Enter test event ID"
                class="w-full h-12 px-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200"
              >
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- TikTok Pixel Setup -->
          <div class="bg-white dark:bg-slate-900 rounded-[24px] border border-slate-100 dark:border-slate-800 p-8 shadow-sm transition-colors duration-300">
            <div class="flex items-center gap-2 mb-6">
              <Music2 class="w-6 h-6 text-black dark:text-white" />
              <h2 class="text-lg font-bold text-slate-800 dark:text-white">Tiktok Pixel Setup</h2>
            </div>
            <div class="space-y-6">
              <div class="space-y-2">
                <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Tiktok Pixel ID</label>
                <input 
                  v-model="form.tiktokPixelId"
                  type="text" 
                  class="w-full h-12 px-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200"
                >
              </div>
              <div class="space-y-2">
                <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Tiktok Pixel Token</label>
                <div class="relative">
                  <input 
                    v-model="form.tiktokPixelToken"
                    :type="showTikTokToken ? 'text' : 'password'" 
                    placeholder="Tiktok pixel token"
                    class="w-full h-12 px-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200 pr-12"
                  >
                  <button 
                    @click="showTikTokToken = !showTikTokToken"
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                  >
                    <Eye v-if="!showTikTokToken" class="w-5 h-5" />
                    <EyeOff v-else class="w-5 h-5" />
                  </button>
                </div>
              </div>
              <div class="space-y-2">
                <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Tiktok Test Event Code</label>
                <input 
                  v-model="form.tiktokTestEventCode"
                  type="text" 
                  class="w-full h-12 px-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200"
                >
                <p class="text-[11px] font-bold text-slate-400 dark:text-slate-500 italic mt-1">clear after when testing is done.</p>
              </div>
            </div>
          </div>

          <!-- Google Social Login -->
          <div class="bg-white dark:bg-slate-900 rounded-[24px] border border-slate-100 dark:border-slate-800 p-8 shadow-sm h-full transition-colors duration-300">
            <div class="flex items-center gap-2 mb-6">
              <div class="w-8 h-8 rounded-lg flex items-center justify-center">
                <img src="https://www.gstatic.com/images/branding/product/2x/googleg_48dp.png" class="w-6 h-6 object-contain" alt="Google" />
              </div>
              <h2 class="text-lg font-bold text-slate-800 dark:text-white">Setup Google Social Login</h2>
            </div>
            <div class="space-y-6">
              <div class="space-y-2">
                <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Google Client ID</label>
                <input 
                  v-model="form.googleClientId"
                  type="text" 
                  placeholder="Google Client ID"
                  class="w-full h-12 px-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200"
                >
              </div>
              <div class="space-y-2">
                <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Google Client Secret</label>
                <div class="relative">
                  <input 
                    v-model="form.googleClientSecret"
                    :type="showGoogleSecret ? 'text' : 'password'" 
                    placeholder="Google Client Secret"
                    class="w-full h-12 px-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200 pr-12"
                  >
                  <button 
                    @click="showGoogleSecret = !showGoogleSecret"
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                  >
                    <Eye v-if="!showGoogleSecret" class="w-5 h-5" />
                    <EyeOff v-else class="w-5 h-5" />
                  </button>
                </div>
              </div>
              <div class="space-y-2">
                <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Google Redirect URL</label>
                <input 
                  v-model="form.googleRedirectUrl"
                  type="text" 
                  placeholder="https://yourstore.sellsull.com/auth/google"
                  class="w-full h-12 px-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200"
                >
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="lg:col-span-4 space-y-6">
        <!-- Sitemap Card -->
        <div class="bg-white dark:bg-slate-900 rounded-[24px] border border-slate-100 dark:border-slate-800 p-8 shadow-sm transition-colors duration-300">
          <div class="flex items-center gap-2 mb-2 font-black text-slate-800 dark:text-white uppercase tracking-tight">
            <Share2 class="w-5 h-5" />
            Sitemaps for Search Engine
          </div>
          <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">
            Copy this link and submit to your Google Search Console to help your shop rank better.
          </p>
          <div class="flex items-center justify-between p-3 px-4 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700">
            <span class="text-[12px] font-bold text-slate-600 dark:text-slate-400 truncate mr-4">https://rakibstore.sellull.com/sitemap.xml</span>
            <button class="text-xs font-black text-indigo-600 dark:text-indigo-400 whitespace-nowrap hover:underline">Copy</button>
          </div>
        </div>

        <!-- Facebook Feed Card -->
        <div class="bg-white dark:bg-slate-900 rounded-[24px] border border-slate-100 dark:border-slate-800 p-8 shadow-sm transition-colors duration-300">
          <div class="flex items-center gap-2 mb-2 font-black text-slate-800 dark:text-white uppercase tracking-tight">
            <Facebook class="w-5 h-5" />
            Facebook Data Feed
          </div>
          <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">
            Upload this feed to your Facebook Business Catalog to sync your shop products with Facebook/Instagram.
          </p>
          <div class="flex items-center justify-between p-3 px-4 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700">
            <span class="text-[12px] font-bold text-slate-600 dark:text-slate-400 truncate mr-4">https://rakibstore.sellull.com/facebook-feed.xml</span>
            <button class="text-xs font-black text-indigo-600 dark:text-indigo-400 whitespace-nowrap hover:underline">Copy</button>
          </div>
        </div>

        <!-- Google Shopping Feed -->
        <div class="bg-white dark:bg-slate-900 rounded-[24px] border border-slate-100 dark:border-slate-800 p-8 shadow-sm border-l-4 border-l-emerald-500 transition-colors duration-300">
          <div class="flex items-center gap-2 mb-2 font-black text-slate-800 dark:text-white uppercase tracking-tight">
            <Search class="w-5 h-5" />
            Google Shopping Feed
          </div>
          <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">
            Your products feed for Google Merchant / Shopping Ads.
          </p>
          <div class="flex items-center justify-between p-3 px-4 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700">
            <span class="text-[12px] font-bold text-slate-600 dark:text-slate-400 truncate mr-4">https://rakibstore.sellull.com/google-feed.xml</span>
            <button class="text-xs font-black text-emerald-600 dark:text-emerald-400 whitespace-nowrap hover:underline">Copy</button>
          </div>
        </div>

        <!-- How to Setup -->
        <div class="bg-white dark:bg-slate-900 rounded-[24px] border border-slate-100 dark:border-slate-800 p-8 shadow-sm transition-colors duration-300">
          <h2 class="text-lg font-black text-slate-800 dark:text-white mb-6">How to Setup</h2>
          <div class="space-y-4">
            <div v-for="guide in setupGuides" :key="guide" class="flex items-center justify-between py-1">
              <span class="text-sm font-bold text-slate-600 dark:text-slate-400">{{ guide }}</span>
              <button class="bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 text-red-600 dark:text-red-400 px-4 py-2 rounded-xl text-xs font-black flex items-center gap-2 transition-all active:scale-95 group">
                <div class="w-6 h-4 bg-red-600 rounded flex items-center justify-center text-white text-[8px]">
                  <Play class="w-2.5 h-2.5 fill-current" />
                </div>
                Watch Now
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Floating Save Button -->
    <div class="fixed bottom-8 right-8 z-40">
      <button @click="saveSettings" :disabled="saving || pending" class="px-8 py-4 bg-slate-900 dark:bg-indigo-600 hover:bg-slate-800 dark:hover:bg-indigo-700 text-white font-black rounded-2xl shadow-2xl flex items-center gap-3 transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
        <Save v-if="!saving" class="w-6 h-6" />
        <span v-if="saving">Saving...</span>
        <span v-else>Update Integrations</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { 
  ChevronLeft,
  ChevronRight,
  Save, 
  Eye,
  EyeOff,
  Facebook,
  Music2,
  Share2,
  Search,
  Play
} from 'lucide-vue-next'
import { reactive, ref, onMounted } from 'vue'

definePageMeta({
  middleware: 'auth',
  permissions: 'settings.view'
})

const { $toast } = useNuxtApp()
const { getAll, createItem } = useCrud()
const router = useRouter()

const showGAPass = ref(false)
const showFBPixelToken = ref(false)
const showTikTokToken = ref(false)
const showGoogleSecret = ref(false)

const setupGuides = [
  'Google Tag Manager ID',
  'Tiktok Pixel',
  'Google Analytics Credentials',
  'Facebook Pixel & API',
  'Google Social Login',
  'Sitemaps Guide',
  'Facebook Data Feed'
]

const form = reactive({
  gtmId: '',
  gaMeasurementId: '',
  gaPassword: '',
  fbPixelId: '',
  fbPixelToken: '',
  fbTestEventId: '',
  tiktokPixelId: '',
  tiktokPixelToken: '',
  tiktokTestEventCode: '',
  googleClientId: '',
  googleClientSecret: '',
  googleRedirectUrl: ''
})

const pending = ref(true)
const saving = ref(false)

const loadSettings = async () => {
  try {
    pending.value = true
    const response = await getAll('/vendor/settings?group=marketing_social')
    if (response.data) {
      Object.assign(form, response.data)
    }
  } catch (error) {
    if (error.response?.status !== 404) {
      $toast.error('Failed to load marketing settings')
    }
  } finally {
    pending.value = false
  }
}

const saveSettings = async () => {
  try {
    saving.value = true
    await createItem('/vendor/settings', {
      group: 'marketing_social',
      settings: form
    })
    router.push('/vendor/managed-shop/google-fb-tiktok')
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
/* Custom input styling to match reference image closely */
input {
  transition: all 0.2s ease-in-out;
}
input::placeholder {
  color: #cbd5e1;
}
</style>
