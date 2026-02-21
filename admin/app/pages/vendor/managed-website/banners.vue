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
          <h1 class="text-2xl font-black text-slate-900 tracking-tight leading-none">Manage Small Banners</h1>
        </div>
        <button 
          @click="saveSettings" 
          :disabled="saving || pending"
          class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-xl shadow-lg shadow-emerald-200 transition-all active:scale-95 disabled:opacity-50 flex items-center gap-2"
        >
          <Save v-if="!saving" class="w-5 h-5" />
          <span>{{ saving ? 'Saving...' : 'Save Banners' }}</span>
        </button>
      </div>
    </div>
    
    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="w-8 h-8 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin"></div>
    </div>
    
    <div v-else class="max-w-[1200px] mx-auto space-y-8">
      <!-- Info Header -->
      <div class="bg-white rounded-[32px] border border-slate-100 p-8 shadow-sm flex items-center gap-6">
        <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center flex-shrink-0">
          <Image class="w-8 h-8" />
        </div>
        <div>
          <h2 class="text-xl font-black text-slate-800 tracking-tight">Promotional Banners</h2>
          <p class="text-slate-500 font-medium text-sm mt-1">
            These 3 banners will be displayed below the main slider on your homepage. Use them to highlight categories, sales, or featured collections.
          </p>
        </div>
      </div>

      <!-- Banners Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div 
          v-for="(banner, index) in banners" 
          :key="banner.id"
          class="bg-white rounded-[32px] border border-slate-100 p-6 shadow-sm relative group hover:border-emerald-100 transition-all flex flex-col"
        >
          <div class="flex items-center justify-between mb-4">
            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 font-black text-xs uppercase tracking-wider rounded-lg">
              Banner {{ index + 1 }}
            </span>
          </div>
          
          <!-- Image Upload Area -->
          <div class="w-full aspect-[4/3] bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 relative overflow-hidden cursor-pointer hover:bg-slate-100 transition-colors flex items-center justify-center group/img mb-6">
            <template v-if="banner.previewUrl || banner.uploadedUrl">
              <img :src="banner.previewUrl || banner.uploadedUrl" class="w-full h-full object-cover" />
              <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center">
                <span class="text-white font-bold text-xs bg-black/50 px-3 py-1.5 rounded-lg backdrop-blur-sm">Change Image</span>
              </div>
            </template>
            <template v-else>
              <div class="text-center p-4">
                <ImageIcon class="w-6 h-6 text-slate-300 mx-auto mb-2" />
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Upload Image<br>(~600x450)</span>
              </div>
            </template>
            <input 
              type="file" 
              accept="image/*" 
              class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
              @change="(e) => handleImageUpload(e, index)"
            >
          </div>

          <!-- Banner Details -->
          <div class="space-y-4 mt-auto">
            <div class="space-y-2">
              <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider ml-1">Redirect Link (Optional)</label>
              <div class="relative">
                <LinkIcon class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                <input 
                  v-model="banner.link" 
                  type="text" 
                  placeholder="https://..."
                  class="w-full h-10 pl-10 pr-4 bg-slate-50 border border-slate-100 rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-semibold text-slate-700 text-sm"
                >
              </div>
            </div>
            
            <div class="flex items-center justify-between pt-2">
              <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider ml-1">Visibility</label>
              <button 
                @click="banner.active = !banner.active"
                :class="['w-10 h-5 rounded-full transition-all duration-300 relative', banner.active ? 'bg-emerald-500' : 'bg-slate-200']"
              >
                <div :class="['w-3 h-3 bg-white rounded-full absolute top-1 transition-all duration-300 shadow-sm', banner.active ? 'left-6' : 'left-1']"></div>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ChevronLeft, Image, Image as ImageIcon, Link as LinkIcon, Save } from 'lucide-vue-next'
import { ref, onMounted } from 'vue'

const { $toast } = useNuxtApp()
const { getAll, createItem } = useCrud()

const pending = ref(true)
const saving = ref(false)

const banners = ref([
  { id: 'banner_1', link: '', active: true, previewUrl: null, uploadedUrl: null, file: null },
  { id: 'banner_2', link: '', active: true, previewUrl: null, uploadedUrl: null, file: null },
  { id: 'banner_3', link: '', active: true, previewUrl: null, uploadedUrl: null, file: null },
])

const handleImageUpload = (event, index) => {
  const file = event.target.files[0]
  if (!file) return
  
  banners.value[index].previewUrl = URL.createObjectURL(file)
  banners.value[index].file = file
}

const loadSettings = async () => {
  try {
    pending.value = true
    const response = await getAll('/vendor/settings?group=website_banners')
    if (response?.data) {
      const data = response.data
      if (data.banners_meta) {
        const parsedMeta = typeof data.banners_meta === 'string' ? JSON.parse(data.banners_meta) : data.banners_meta
        banners.value.forEach((b, i) => {
          if (parsedMeta[i]) {
            b.link = parsedMeta[i].link
            b.active = parsedMeta[i].active
            b.uploadedUrl = data[b.id] || null
          }
        })
      }
    }
  } catch (error) {
    if (error.response?.status !== 404) {
      $toast.error('Failed to load banners')
    }
  } finally {
    pending.value = false
  }
}

const saveSettings = async () => {
  try {
    saving.value = true
    const formData = new FormData()
    formData.append('group', 'website_banners')
    
    // Save metadata
    const bannersMeta = banners.value.map(s => ({
      id: s.id,
      link: s.link,
      active: s.active
    }))
    
    formData.append('settings[banners_meta]', JSON.stringify(bannersMeta))
    
    // Append files
    banners.value.forEach(banner => {
      if (banner.file) {
        formData.append(`files[${banner.id}]`, banner.file)
      } else if (banner.uploadedUrl) {
        formData.append(`settings[${banner.id}]`, banner.uploadedUrl)
      }
    })

    await createItem('/vendor/settings', formData, null, false)
    await loadSettings()
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
