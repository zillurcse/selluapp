<template>
  <div class="space-y-6">
    <div v-if="loading" class="flex items-center justify-center p-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
    </div>

    <div v-else v-for="(settings, group) in groupedSettings" :key="group" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/60 dark:border-slate-800/60 overflow-hidden group shadow-sm hover:shadow-md transition-all duration-300">
      <div class="p-6 border-b border-slate-200/60 dark:border-slate-800/60 bg-slate-50/50 dark:bg-slate-800/50">
        <h2 class="text-lg font-black text-slate-900 dark:text-white capitalize flex items-center gap-2">
          <Settings class="w-5 h-5 text-indigo-500" />
          {{ group }} Settings
        </h2>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-tight font-medium">Manage your {{ group }} configuration</p>
      </div>

      <div class="p-8 space-y-8">
        <div v-for="setting in settings" :key="setting.key" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
          <div>
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 capitalize mb-1">
              {{ formatKey(setting.key) }}
            </label>
            <p class="text-[11px] text-slate-400 font-medium">Key: <span class="font-mono">{{ setting.key }}</span></p>
          </div>

          <div class="md:col-span-2">
            <!-- Text Input -->
            <input v-if="setting.type === 'text' || setting.type === 'color'" 
              :type="setting.type === 'color' ? 'color' : 'text'"
              v-model="setting.value"
              class="w-full px-5 py-3 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all duration-300 text-sm"
              :placeholder="formatKey(setting.key)"
            />

            <!-- Textarea -->
            <textarea v-else-if="setting.type === 'textarea'"
              v-model="setting.value"
              rows="4"
              class="w-full px-5 py-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all duration-300 text-sm resize-none"
              :placeholder="formatKey(setting.key)"
            ></textarea>

            <!-- Boolean / Switch -->
            <div v-else-if="setting.type === 'boolean'" class="flex items-center">
              <button 
                @click="setting.value = setting.value === 'true' ? 'false' : 'true'"
                class="relative inline-flex h-7 w-12 items-center rounded-full transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-indigo-500/20"
                :class="setting.value === 'true' ? 'bg-indigo-600' : 'bg-slate-200 dark:bg-slate-700'"
              >
                <span 
                  class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform duration-300 shadow-sm"
                  :class="setting.value === 'true' ? 'translate-x-6' : 'translate-x-1'"
                ></span>
              </button>
              <span class="ml-3 text-xs font-bold" :class="setting.value === 'true' ? 'text-indigo-600' : 'text-slate-400'">
                {{ setting.value === 'true' ? 'Enabled' : 'Disabled' }}
              </span>
            </div>

            <!-- Image Upload -->
            <div v-else-if="setting.type === 'image'" class="space-y-4">
              <div v-if="setting.value" class="relative w-32 h-32 group/img">
                <img :src="getImageUrl(setting.value)" class="w-full h-full object-contain rounded-2xl border border-slate-200 dark:border-slate-700 p-2 bg-white dark:bg-slate-800" />
                <button @click="setting.value = null" class="absolute -top-2 -right-2 p-1.5 bg-rose-500 text-white rounded-full shadow-lg opacity-0 group-hover/img:opacity-100 transition-opacity translate-y-2 group-hover/img:translate-y-0 duration-300">
                  <X class="w-3 h-3" />
                </button>
              </div>
              <div v-else class="flex items-center justify-center w-32 h-32 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700 hover:border-indigo-500/50 hover:bg-indigo-50/50 dark:hover:bg-indigo-500/5 transition-all duration-300 relative overflow-hidden group/upload">
                <input type="file" @change="(e) => handleFileUpload(e, setting)" class="absolute inset-0 opacity-0 cursor-pointer z-10" accept="image/*" />
                <div class="text-center group-hover/upload:scale-110 transition-transform duration-300">
                  <Upload class="w-6 h-6 mx-auto text-slate-300 group-hover/upload:text-indigo-500" />
                  <span class="text-[10px] font-black text-slate-400 uppercase mt-2 block">Upload</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="px-8 py-6 bg-slate-50/50 dark:bg-slate-800/50 border-t border-slate-200/60 dark:border-slate-800/60 flex justify-end">
        <button 
          @click="saveGroup(group)" 
          :disabled="saving === group"
          class="px-8 py-3 bg-slate-900 dark:bg-indigo-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-800 dark:hover:bg-indigo-500 hover:scale-105 active:scale-95 transition-all flex items-center gap-3 disabled:opacity-50 shadow-xl shadow-indigo-900/10"
        >
          <div v-if="saving === group" class="animate-spin rounded-full h-3 w-3 border-2 border-white/20 border-t-white"></div>
          <Save v-else class="w-4 h-4" />
          Save {{ group }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Settings, Save, Upload, X } from 'lucide-vue-next'

const props = defineProps({
  filterGroup: {
    type: String,
    default: null
  }
})

const config = useRuntimeConfig()
const apiBase = config.public.apiBase
const loading = ref(true)
const saving = ref(null)
const groupedSettings = ref({})

const fetchSettings = async () => {
  loading.value = true
  try {
    const { data } = await useApiFetch('/admin/settings')
    if (props.filterGroup) {
      groupedSettings.value = { [props.filterGroup]: data.value[props.filterGroup] || [] }
    } else {
      groupedSettings.value = data.value
    }
  } catch (e) {
    console.error('Failed to fetch settings', e)
  } finally {
    loading.value = false
  }
}

const saveGroup = async (group) => {
  saving.value = group
  const settingsToUpdate = {}
  groupedSettings.value[group].forEach(s => {
    settingsToUpdate[s.key] = s.value
  })

  try {
    await useApiFetch('/admin/settings', {
      method: 'POST',
      body: { settings: settingsToUpdate }
    })
    // Add toast notification here if available
    alert(`${group} settings updated!`)
  } catch (e) {
    console.error('Failed to save settings', e)
    alert('Failed to save settings')
  } finally {
    saving.value = null
  }
}

const handleFileUpload = async (event, setting) => {
  const file = event.target.files[0]
  if (!file) return

  const formData = new FormData()
  formData.append('file', file)
  formData.append('key', setting.key)

  try {
    const { data } = await useApiFetch('/admin/settings/upload', {
      method: 'POST',
      body: formData
    })
    setting.value = data.value.url
  } catch (e) {
    console.error('Upload failed', e)
    alert('Upload failed')
  }
}

const formatKey = (key) => {
  return key.replace(/_/g, ' ').replace(/\w\S*/g, (w) => (w.replace(/^\w/, (c) => c.toUpperCase())))
}

const getImageUrl = (url) => {
  if (url && (url.startsWith('http') || url.startsWith('https'))) return url
  return `${apiBase.replace('/api', '')}${url}`
}

onMounted(fetchSettings)
</script>
