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
          <h1 class="text-2xl font-black text-slate-900 tracking-tight leading-none">Update Website Slider</h1>
        </div>
        <button 
          @click="saveSettings" 
          :disabled="saving || pending"
          class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-xl shadow-lg shadow-indigo-200 transition-all active:scale-95 disabled:opacity-50 flex items-center gap-2"
        >
          <Save v-if="!saving" class="w-5 h-5" />
          <span>{{ saving ? 'Saving...' : 'Save Sliders' }}</span>
        </button>
      </div>
    </div>
    
    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
    </div>
    
    <div v-else class="max-w-[1200px] mx-auto space-y-8">
      <!-- Empty State -->
      <div v-if="sliders.length === 0" class="bg-white rounded-[32px] border border-slate-100 p-12 shadow-sm text-center">
        <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center mx-auto mb-6">
          <Sliders class="w-10 h-10" />
        </div>
        <h2 class="text-2xl font-black text-slate-800 mb-4">No Sliders Found</h2>
        <p class="text-slate-500 font-medium max-w-md mx-auto mb-8">
          Manage your website's main sliders here to improve user experience and showcase your best products.
        </p>
        <button @click="addSlider" class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-black rounded-2xl shadow-lg shadow-blue-200 transition-all active:scale-95">
          Add First Slider
        </button>
      </div>

      <!-- Sliders List -->
      <div v-else class="space-y-6">
        <div 
          v-for="(slider, index) in sliders" 
          :key="slider.id"
          class="bg-white rounded-[32px] border border-slate-100 p-8 shadow-sm flex flex-col md:flex-row gap-8 relative overflow-hidden group hover:border-blue-100 transition-all"
        >
          <!-- Image Upload Area -->
          <div class="w-full md:w-1/3 aspect-[21/9] bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200 relative overflow-hidden cursor-pointer hover:bg-slate-100 transition-colors flex items-center justify-center group/img">
            <template v-if="slider.previewUrl || slider.uploadedUrl">
              <img :src="slider.previewUrl || slider.uploadedUrl" class="w-full h-full object-cover" />
              <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center">
                <span class="text-white font-bold text-sm bg-black/50 px-4 py-2 rounded-lg backdrop-blur-sm">Change Image</span>
              </div>
            </template>
            <template v-else>
              <div class="text-center p-4">
                <ImageIcon class="w-8 h-8 text-slate-300 mx-auto mb-2" />
                <span class="text-sm font-bold text-slate-400">Click to upload image<br>(Desktop: 1920x800)</span>
              </div>
            </template>
            <input 
              type="file" 
              accept="image/*" 
              class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
              @change="(e) => handleImageUpload(e, index)"
            >
          </div>

          <!-- Slider Details -->
          <div class="w-full md:w-2/3 space-y-4">
            <div class="flex justify-between items-start">
              <div class="px-3 py-1 bg-blue-50 text-blue-600 font-black text-xs uppercase tracking-wider rounded-lg">
                Slider #{{ index + 1 }}
              </div>
              <button @click="removeSlider(index)" class="w-8 h-8 bg-red-50 text-red-500 rounded-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors">
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
            
            <div class="space-y-4 pt-2">
              <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Redirect Link (Optional)</label>
                <div class="relative">
                  <LinkIcon class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" />
                  <input 
                    v-model="slider.link" 
                    type="text" 
                    placeholder="https://..."
                    class="w-full h-12 pl-12 pr-4 bg-slate-50 border border-slate-100 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-semibold text-slate-700"
                  >
                </div>
              </div>
              <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Status</label>
                <div class="flex items-center gap-3">
                  <span :class="['text-sm font-bold', slider.active ? 'text-emerald-600' : 'text-slate-400']">
                    {{ slider.active ? 'Visible on Website' : 'Hidden' }}
                  </span>
                  <button 
                    @click="slider.active = !slider.active"
                    :class="['w-12 h-6 rounded-full transition-all duration-300 relative', slider.active ? 'bg-emerald-500' : 'bg-slate-200']"
                  >
                    <div :class="['w-4 h-4 bg-white rounded-full absolute top-1 transition-all duration-300 shadow-sm', slider.active ? 'left-7' : 'left-1']"></div>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Add Button Below List -->
        <button @click="addSlider" class="w-full py-6 bg-slate-50 border-2 border-dashed border-slate-200 rounded-[32px] text-slate-400 font-black hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600 transition-all flex items-center justify-center gap-2">
          <Plus class="w-5 h-5" />
          Add Another Slider
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ChevronLeft, Sliders, Trash2, Plus, Image as ImageIcon, Link as LinkIcon, Save } from 'lucide-vue-next'
import { ref, onMounted } from 'vue'

const { $toast } = useNuxtApp()
const { getAll, createItem } = useCrud()

const pending = ref(true)
const saving = ref(false)

const sliders = ref([])
let newFiles = new FormData()

const generateId = () => Math.random().toString(36).substr(2, 9)

const addSlider = () => {
  sliders.value.push({
    id: 'slider_' + generateId(),
    link: '',
    active: true,
    previewUrl: null,
    uploadedUrl: null,
    file: null
  })
}

const removeSlider = (index) => {
  sliders.value.splice(index, 1)
}

const handleImageUpload = (event, index) => {
  const file = event.target.files[0]
  if (!file) return
  
  // Create preview
  sliders.value[index].previewUrl = URL.createObjectURL(file)
  sliders.value[index].file = file
}

const loadSettings = async () => {
  try {
    pending.value = true
    const response = await getAll('/vendor/settings?group=website_sliders')
    if (response?.data) {
      const data = response.data
      if (data.sliders_meta) {
        const parsedMeta = typeof data.sliders_meta === 'string' ? JSON.parse(data.sliders_meta) : data.sliders_meta
        sliders.value = parsedMeta.map(meta => ({
          ...meta,
          uploadedUrl: data[meta.id] || null,
          previewUrl: null,
          file: null
        }))
      }
    }
  } catch (error) {
    if (error.response?.status !== 404) {
      $toast.error('Failed to load sliders')
    }
  } finally {
    pending.value = false
  }
}

const saveSettings = async () => {
  try {
    saving.value = true
    const formData = new FormData()
    formData.append('group', 'website_sliders')
    
    // Save metadata
    const slidersMeta = sliders.value.map(s => ({
      id: s.id,
      link: s.link,
      active: s.active
    }))
    
    formData.append('settings[sliders_meta]', JSON.stringify(slidersMeta))
    
    // Append files
    sliders.value.forEach(slider => {
      if (slider.file) {
        formData.append(`files[${slider.id}]`, slider.file)
      } else if (slider.uploadedUrl) {
        // preserve the old url if not changed
        formData.append(`settings[${slider.id}]`, slider.uploadedUrl)
      }
    })

    await createItem('/vendor/settings', formData, null, false)
    await loadSettings() // Reload to get updated URLs
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
