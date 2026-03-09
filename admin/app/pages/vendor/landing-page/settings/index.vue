<template>
  <div class="p-8 lg:p-12 bg-[#F8FAFC] dark:bg-slate-950 min-h-screen font-sans transition-colors duration-300">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-10">
      <div class="flex items-center">
        <button 
          @click="$router.back()" 
          class="bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 p-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all mr-5 shadow-sm border border-slate-200 dark:border-slate-800 group"
        >
          <ChevronLeft class="w-6 h-6 group-hover:-translate-x-0.5 transition-transform" />
        </button>
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Landing Page Settings</h1>
          <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Configure delivery charges, footer content, and activation status.</p>
        </div>
      </div>

      <div class="flex justify-end">
        <button 
          @click="saveChanges" 
          :disabled="isLoading"
          class="bg-rose-600 dark:bg-rose-700 text-white px-8 py-3 rounded-xl font-bold hover:bg-rose-700 dark:hover:bg-rose-600 active:scale-95 transition-all shadow-lg shadow-rose-200 dark:shadow-none disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
        >
          <Loader2 v-if="isLoading" class="w-5 h-5 mr-2 animate-spin" />
          {{ isLoading ? 'Saving Changes...' : 'Save Settings' }}
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[1.5fr_1fr] gap-8 items-start">
      <div class="space-y-8">
        <!-- Delivery Charges Section -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-8 shadow-sm border border-slate-200 dark:border-slate-800 transition-colors duration-300">
          <div class="flex items-center gap-3 mb-6">
            <div class="p-2 bg-rose-50 dark:bg-rose-900/20 rounded-lg">
              <Truck class="w-6 h-6 text-rose-600" />
            </div>
            <div>
              <h2 class="text-xl font-bold text-slate-900 dark:text-white">Delivery Charges</h2>
              <p class="text-slate-400 dark:text-slate-500 text-sm">Set charges for different delivery zones</p>
            </div>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Inside Dhaka</label>
              <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <MapPin class="w-5 h-5 text-slate-400 group-focus-within:text-rose-500 transition-colors" />
                </div>
                <input 
                  v-model="settings.inside_dhaka_charge"
                  type="number" 
                  class="w-full pl-11 pr-4 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 outline-none transition-all font-bold text-slate-800 dark:text-white"
                  placeholder="0.00"
                />
              </div>
            </div>
            
            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Outside Dhaka</label>
              <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <Globe class="w-5 h-5 text-slate-400 group-focus-within:text-rose-500 transition-colors" />
                </div>
                <input 
                  v-model="settings.outside_dhaka_charge"
                  type="number" 
                  class="w-full pl-11 pr-4 py-3.5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 outline-none transition-all font-bold text-slate-800 dark:text-white"
                  placeholder="0.00"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Content Settings -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-8 shadow-sm border border-slate-200 dark:border-slate-800 transition-colors duration-300">
          <div class="flex items-center gap-3 mb-6">
            <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
              <Layout class="w-6 h-6 text-blue-600" />
            </div>
            <div>
              <h2 class="text-xl font-bold text-slate-900 dark:text-white">Content Settings</h2>
              <p class="text-slate-400 dark:text-slate-500 text-sm">Customize text displayed on the landing page</p>
            </div>
          </div>

          <div class="space-y-6">
            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Footer Text</label>
              <textarea 
                v-model="settings.footer_text"
                rows="3"
                placeholder="Enter footer copyright or brand text..." 
                class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400 font-medium text-slate-800 dark:text-white resize-none"
              ></textarea>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Delivery Related Text</label>
              <textarea 
                v-model="settings.delivery_related_text"
                rows="3"
                placeholder="Information about delivery timelines, policies, etc." 
                class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all placeholder:text-slate-400 font-medium text-slate-800 dark:text-white resize-none"
              ></textarea>
            </div>
          </div>
        </div>
      </div>

      <div class="space-y-8">
        <!-- Activation Toggle -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-8 shadow-sm transition-colors duration-300">
          <div class="flex items-start justify-between">
            <div class="max-w-[200px]">
              <h3 class="text-slate-900 dark:text-white text-lg font-bold mb-1">Activation Status</h3>
              <p class="text-slate-400 dark:text-slate-500 text-sm leading-snug">Toggle the custom delivery system for your landing page.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" v-model="settings.is_active" class="sr-only peer">
              <div class="w-14 h-7 bg-slate-200 dark:bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:start-[4px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
            </label>
          </div>
          <div class="mt-6 flex items-center gap-2">
            <div :class="[settings.is_active ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-slate-50 text-slate-500 dark:bg-slate-800/50 dark:text-slate-400']" 
              class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider transition-colors">
              {{ settings.is_active ? 'Live & Active' : 'System Inactive' }}
            </div>
          </div>
        </div>

        <!-- Note Section -->
        <div class="bg-amber-50 dark:bg-amber-900/10 rounded-2xl p-8 border border-amber-100 dark:border-amber-900/20 transition-colors duration-300">
          <div class="flex items-center gap-3 mb-4">
            <Info class="w-6 h-6 text-amber-600" />
            <h3 class="text-amber-800 dark:text-amber-500 font-bold">Important Notes</h3>
          </div>
          <p class="text-amber-800/70 dark:text-amber-500/70 text-sm leading-relaxed">
            These settings only apply to your <span class="font-bold underline">Landing Pages</span>. Changes here won't affect your main website's checkout logic. If inactive or empty, main website settings will be used as fallbacks.
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  ChevronLeft,
  Loader2,
  Truck,
  MapPin,
  Globe,
  Layout,
  Info
} from 'lucide-vue-next'

const { getAll, createItem } = useCrud()
const utilityStore = useUtilityStore()
utilityStore.pageBackLink = '/vendor/landing-page/all'

definePageMeta({
  layout: 'default',
  middleware: 'auth',
  permissions: 'landing_pages.edit'
})

const settings = ref({
  inside_dhaka_charge: 60,
  outside_dhaka_charge: 120,
  footer_text: '',
  delivery_related_text: '',
  is_active: true
})

const isLoading = ref(false)

const fetchSettings = async () => {
    try {
        const response = await getAll('/vendor/settings?group=landing_page')
        if (response && response.status === 'success') {
            const remoteSettings = response.data
            if (remoteSettings && Object.keys(remoteSettings).length > 0) {
                if (remoteSettings.inside_dhaka_charge !== undefined) settings.value.inside_dhaka_charge = remoteSettings.inside_dhaka_charge
                if (remoteSettings.outside_dhaka_charge !== undefined) settings.value.outside_dhaka_charge = remoteSettings.outside_dhaka_charge
                if (remoteSettings.footer_text !== undefined) settings.value.footer_text = remoteSettings.footer_text
                if (remoteSettings.delivery_related_text !== undefined) settings.value.delivery_related_text = remoteSettings.delivery_related_text
                if (remoteSettings.is_active !== undefined) {
                    settings.value.is_active = !!(remoteSettings.is_active == 1 || remoteSettings.is_active === true)
                }
            }
        }
    } catch (err) {
        console.error('Failed to fetch settings:', err)
    }
}

onMounted(() => {
    fetchSettings()
})

const saveChanges = async () => {
  isLoading.value = true
  try {
    const { responseData } = await createItem('/vendor/settings', {
        group: 'landing_page',
        settings: {
          ...settings.value,
          is_active: settings.value.is_active ? 1 : 0
        }
      })
      navigateTo('/vendor/landing-page/settings')
  } catch (err) {
    console.error('Error saving settings:', err)
    toast.error('Could not save settings. Please try again.')
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
/* Smooth transitions for form elements */
input, textarea {
  transition: all 0.2s ease-in-out;
}
</style>
