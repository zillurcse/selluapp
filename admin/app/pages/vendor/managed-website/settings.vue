<template>
  <div class="p-10 bg-[#f8fafc] min-h-screen">
    <div class="max-w-[1200px] mx-auto mb-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <NuxtLink 
            to="/vendor/managed-website" 
            class="w-10 h-10 bg-slate-900 rounded-full flex items-center justify-center text-white hover:bg-slate-800 transition-all active:scale-95 shadow-lg shadow-slate-900/20"
          >
            <ChevronLeft class="w-6 h-6" />
          </NuxtLink>
          <h1 class="text-2xl font-black text-slate-900 tracking-tight leading-none">Webpage Settings</h1>
        </div>
        <button 
          @click="saveSettings" 
          :disabled="saving || pending"
          class="px-8 py-3 bg-rose-600 hover:bg-rose-700 text-white font-black rounded-xl shadow-lg shadow-rose-200 transition-all active:scale-95 disabled:opacity-50 flex items-center gap-2"
        >
          <Save v-if="!saving" class="w-5 h-5" />
          <span>{{ saving ? 'Saving...' : 'Save Settings' }}</span>
        </button>
      </div>
    </div>
    
    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="w-8 h-8 border-4 border-rose-600 border-t-transparent rounded-full animate-spin"></div>
    </div>
    
    <div v-else class="max-w-[1200px] mx-auto space-y-8">
      
      <!-- Top Announcement Bar Details -->
      <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex items-center gap-4 bg-rose-50/30">
          <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center">
            <Megaphone class="w-6 h-6" />
          </div>
          <div>
            <h2 class="text-xl font-black text-slate-900">Announcement Bar</h2>
            <p class="text-sm font-bold text-slate-500">Display a prominent message at the top of your website</p>
          </div>
          <div class="ml-auto flex items-center gap-3">
             <span class="text-xs font-black uppercase text-slate-400">Show Bar</span>
             <button 
                @click="form.showAnnouncement = !form.showAnnouncement"
                :class="['w-12 h-6 rounded-full transition-all duration-300 relative', form.showAnnouncement ? 'bg-rose-500' : 'bg-slate-200']"
              >
                <div :class="['w-4 h-4 bg-white rounded-full absolute top-1 transition-all duration-300 shadow-sm', form.showAnnouncement ? 'left-7' : 'left-1']"></div>
              </button>
          </div>
        </div>
        
        <div class="p-8 space-y-6" v-if="form.showAnnouncement">
          <div class="space-y-2">
            <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Announcement Text</label>
            <input 
              v-model="form.announcementText" 
              type="text" 
              placeholder="e.g. Free shipping on all orders over $100!"
              class="w-full h-14 px-6 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all font-semibold text-slate-700"
            >
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Background Color</label>
              <div class="flex items-center gap-4">
                <input type="color" v-model="form.announcementBgColor" class="w-14 h-14 rounded-xl cursor-pointer border-0 p-1 bg-slate-50">
                <input type="text" v-model="form.announcementBgColor" class="flex-grow h-14 px-6 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all font-semibold text-slate-700 uppercase">
              </div>
            </div>
            <div class="space-y-2">
              <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Text Color</label>
              <div class="flex items-center gap-4">
                <input type="color" v-model="form.announcementTextColor" class="w-14 h-14 rounded-xl cursor-pointer border-0 p-1 bg-slate-50">
                <input type="text" v-model="form.announcementTextColor" class="flex-grow h-14 px-6 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all font-semibold text-slate-700 uppercase">
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Footer Settings -->
      <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex items-center gap-4">
          <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center">
            <LayoutBottombar class="w-6 h-6" />
          </div>
          <div>
            <h2 class="text-xl font-black text-slate-900">Footer Settings</h2>
            <p class="text-sm font-bold text-slate-500">Configure your website footer text</p>
          </div>
        </div>
        
        <div class="p-8 space-y-6">
          <div class="space-y-2">
            <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">About Shop (Short Description)</label>
            <textarea 
              v-model="form.footerAbout" 
              rows="4" 
              placeholder="We are a premium brand offering the best products in the market..."
              class="w-full p-6 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all font-semibold text-slate-700 resize-none"
            ></textarea>
          </div>
          <div class="space-y-2">
            <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Copyright Text</label>
            <input 
              v-model="form.footerCopyright" 
              type="text" 
              placeholder="© 2026 YourStore. All rights reserved."
              class="w-full h-14 px-6 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all font-semibold text-slate-700"
            >
          </div>
        </div>
      </div>
      
    </div>
  </div>
</template>

<script setup>
import { ChevronLeft, Save, Megaphone, LayoutList as LayoutBottombar } from 'lucide-vue-next'
import { ref, reactive, onMounted } from 'vue'

const { $toast } = useNuxtApp()
const { getAll, createItem } = useCrud()
const router = useRouter()

const pending = ref(true)
const saving = ref(false)

const form = reactive({
  showAnnouncement: true,
  announcementText: 'Free shipping on all orders over $100!',
  announcementBgColor: '#000000',
  announcementTextColor: '#ffffff',
  footerAbout: '',
  footerCopyright: '© 2026 Sellull. All rights reserved.'
})

const loadSettings = async () => {
  try {
    pending.value = true
    const response = await getAll('/vendor/settings?group=website_general')
    if (response?.data) {
       // if group has settings
       if (Object.keys(response.data).length > 0) {
          const loaded = response.data
          form.showAnnouncement = loaded.showAnnouncement === 'true' || loaded.showAnnouncement === true
          form.announcementText = loaded.announcementText || ''
          form.announcementBgColor = loaded.announcementBgColor || '#000000'
          form.announcementTextColor = loaded.announcementTextColor || '#ffffff'
          form.footerAbout = loaded.footerAbout || ''
          form.footerCopyright = loaded.footerCopyright || `© ${new Date().getFullYear()} My Store. All rights reserved.`
       }
    }
  } catch (error) {
    if (error.response?.status !== 404) {
      $toast.error('Failed to load settings')
    }
  } finally {
    pending.value = false
  }
}

const saveSettings = async () => {
  try {
    saving.value = true
    await createItem('/vendor/settings', {
      group: 'website_general',
      settings: form
    })
    router.push('/vendor/managed-website')
  } catch (error) {
    console.error(error)
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  loadSettings()
})
</script>
