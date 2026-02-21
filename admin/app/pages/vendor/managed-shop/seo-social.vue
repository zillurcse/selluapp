<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <div class="max-w-4xl mx-auto mb-8">
      <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 font-medium mb-4">
        <NuxtLink to="/vendor/managed-shop" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Manage Shop</NuxtLink>
        <ChevronRight class="w-4 h-4" />
        <span class="text-slate-900 dark:text-white font-bold">SEO & Social Media</span>
      </div>
      
      <div class="flex items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">SEO & Social Media</h1>
          <p class="text-slate-500 dark:text-slate-400 font-semibold mt-1">Optimize your store for search engines and social platforms.</p>
        </div>
        <button @click="saveSettings" :disabled="saving || pending" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white font-black rounded-2xl shadow-lg shadow-indigo-200 dark:shadow-indigo-900/20 transition-all active:scale-95 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
          <Save v-if="!saving" class="w-5 h-5" />
          <span v-if="saving">Saving...</span>
          <span v-else>Save Settings</span>
        </button>
      </div>
    </div>

    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="w-8 h-8 border-4 border-indigo-600 dark:border-indigo-400 border-t-transparent rounded-full animate-spin"></div>
    </div>
    <div v-else class="max-w-4xl mx-auto space-y-6">
      <!-- SEO Global -->
      <div class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm p-8 space-y-8 transition-colors duration-300">
        <div class="flex items-center gap-3 border-b border-slate-50 dark:border-slate-800 pb-6">
          <div class="w-12 h-12 bg-orange-50 dark:bg-orange-900/20 rounded-2xl flex items-center justify-center">
            <Search class="w-6 h-6 text-orange-600 dark:text-orange-400" />
          </div>
          <h2 class="text-xl font-black text-slate-900 dark:text-white">Search Engine Optimization</h2>
        </div>

        <div class="space-y-6">
          <div class="space-y-2">
            <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider ml-1">Meta Title</label>
            <input v-model="form.metaTitle" type="text" placeholder="The best store for..." class="w-full h-14 px-6 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200">
          </div>
          <div class="space-y-2">
            <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider ml-1">Meta Description</label>
            <textarea v-model="form.metaDescription" rows="3" placeholder="Discover a wide range of products..." class="w-full p-6 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200 resize-none"></textarea>
          </div>
          <div class="space-y-2">
            <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider ml-1">Keywords</label>
            <input v-model="form.keywords" type="text" placeholder="ecommerce, shop, fashion, ..." class="w-full h-14 px-6 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200">
          </div>
        </div>
      </div>

      <!-- Social Media Links -->
      <div class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm p-8 space-y-8 transition-colors duration-300">
        <div class="flex items-center gap-3 border-b border-slate-50 dark:border-slate-800 pb-6">
          <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl flex items-center justify-center">
            <Share2 class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
          </div>
          <h2 class="text-xl font-black text-slate-900 dark:text-white">Social Media Connections</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div 
            v-for="platform in platforms" 
            :key="platform.id"
            class="space-y-2"
          >
            <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider ml-1">{{ platform.name }}</label>
            <div class="relative">
              <input 
                v-model="form.social[platform.key]"
                type="text" 
                :placeholder="platform.placeholder" 
                class="w-full h-14 pl-14 pr-6 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200"
              >
              <component :is="platform.icon" class="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 dark:text-slate-500" />
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
  Search, 
  Share2, 
  Facebook, 
  Instagram, 
  Twitter, 
  Linkedin, 
  Youtube 
} from 'lucide-vue-next'
import { reactive, ref, onMounted } from 'vue'

const { $toast } = useNuxtApp()
const { getAll, createItem } = useCrud()
const router = useRouter()

const platforms = [
  { id: 1, key: 'facebook', name: 'Facebook', placeholder: 'https://facebook.com/yourshop', icon: Facebook },
  { id: 2, key: 'instagram', name: 'Instagram', placeholder: 'https://instagram.com/yourshop', icon: Instagram },
  { id: 3, key: 'twitter', name: 'Twitter / X', placeholder: 'https://x.com/yourshop', icon: Twitter },
  { id: 4, key: 'linkedin', name: 'LinkedIn', placeholder: 'https://linkedin.com/company/yourshop', icon: Linkedin },
  { id: 5, key: 'youtube', name: 'YouTube', placeholder: 'https://youtube.com/@yourshop', icon: Youtube },
]

const form = reactive({
  metaTitle: '',
  metaDescription: '',
  keywords: '',
  social: {
    facebook: '',
    instagram: '',
    twitter: '',
    linkedin: '',
    youtube: ''
  }
})

const pending = ref(true)
const saving = ref(false)

const loadSettings = async () => {
  try {
    pending.value = true
    const response = await getAll('/vendor/settings?group=seo_social')
    if (response.data) {
      if (response.data.social && typeof response.data.social === 'string') {
          // It might be double encoded or not parsed correctly, handle just in case
          try {
              response.data.social = JSON.parse(response.data.social)
          } catch(e){}
      }
      
      // Merge social separately to avoid overwriting the whole object if some keys are missing
      const { social, ...rest } = response.data.data
      Object.assign(form, rest)
      if (social && typeof social === 'object') {
        Object.assign(form.social, social)
      }
    }
  } catch (error) {
    console.error(error)
    // if (error.response?.status !== 404) {
    //   $toast.error('Failed to load SEO & Social settings')
    // }
  } finally {
    pending.value = false
  }
}

const saveSettings = async () => {
  try {
    saving.value = true
    await createItem('/vendor/settings', {
      group: 'seo_social',
      settings: form
    })
    router.push('/vendor/managed-shop/seo-social')
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
