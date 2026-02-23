<template>
  <div class="p-10 bg-[#F8F9FB] dark:bg-slate-950 min-h-screen font-sans transition-colors duration-300">
    <!-- Header -->
    <div class="flex items-center mb-1">
      <button @click="$router.back()" class="bg-[#2D333F] text-white p-2.5 rounded-full hover:bg-black transition-colors mr-4 shadow-sm">
        <ChevronLeft class="w-6 h-6" />
      </button>
      <h1 class="text-2xl font-bold text-[#1E293B] dark:text-white">Landing Page Settings</h1>
    </div>
    <p class="text-gray-500 dark:text-slate-400 text-sm mb-8 ml-14">From this page you can set landing page delivery charge, footer text etc.</p>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-[1.5fr_1fr] gap-8 mb-8">
      
      <!-- Left Column: Settings Form -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl p-8 shadow-sm border border-gray-100 dark:border-slate-800 transition-colors duration-300">
        <h2 class="text-[#E11D48] text-xl font-bold mb-2">Set Delivery Charges</h2>
        <p class="text-gray-400 dark:text-slate-500 text-sm mb-6">Here you can set delivery charges for inside and outside Dhaka.</p>
        
        <!-- Delivery Charges Inputs -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
          <div class="flex items-center border-2 border-[#E11D48] rounded-xl overflow-hidden h-14 bg-white dark:bg-slate-900 shadow-sm ring-4 ring-[#FEF2F2] dark:ring-[#E11D48]/10 transition-colors duration-300">
            <span class="px-4 text-sm font-semibold text-gray-600 dark:text-slate-400 bg-gray-50 dark:bg-slate-800 h-full flex items-center border-r border-gray-100 dark:border-slate-700">Inside Dhaka Charge</span>
            <input 
              v-model="settings.inside_dhaka_charge"
              type="text" 
              class="flex-1 px-4 h-full outline-none font-bold text-gray-800"
            />
          </div>
          
          <div class="flex items-center border-2 border-gray-100 dark:border-slate-800 rounded-xl overflow-hidden h-14 bg-white dark:bg-slate-900 transition-colors duration-300">
            <span class="px-4 text-sm font-semibold text-gray-600 dark:text-slate-400 bg-gray-50 dark:bg-slate-800 h-full flex items-center border-r border-gray-100 dark:border-slate-700">Outside Dhaka Charge</span>
            <input 
              v-model="settings.outside_dhaka_charge"
              type="text" 
              class="flex-1 px-4 h-full outline-none font-bold text-gray-800"
            />
          </div>
        </div>

        <!-- Footer Text -->
        <div class="space-y-2 mb-6">
          <label class="block text-sm font-bold text-gray-600 dark:text-slate-400 ml-1">Footer Text</label>
          <input 
            v-model="settings.footer_text"
            type="text" 
            placeholder="Enter footer text" 
            class="w-full h-12 px-5 bg-white dark:bg-slate-800 border-2 border-gray-50 dark:border-slate-700 rounded-xl focus:border-blue-500 outline-none transition-all placeholder:text-gray-300 dark:placeholder:text-slate-600 font-medium dark:text-slate-200"
          />
        </div>

        <!-- Delivery Related Text -->
        <div class="space-y-2">
          <label class="block text-sm font-bold text-gray-600 dark:text-slate-400 ml-1">Delivery Related Text</label>
          <input 
            v-model="settings.delivery_related_text"
            type="text" 
            placeholder="Enter delivery related text" 
            class="w-full h-12 px-5 bg-white dark:bg-slate-800 border-2 border-gray-50 dark:border-slate-700 rounded-xl focus:border-blue-500 outline-none transition-all placeholder:text-gray-300 dark:placeholder:text-slate-600 font-medium dark:text-slate-200"
          />
        </div>
      </div>

      <!-- Right Column: Important Note -->
      <div class="bg-white dark:bg-slate-900 rounded-2xl p-10 border-2 border-dashed border-gray-200 dark:border-slate-800 flex flex-col items-center justify-center text-center transition-colors duration-300">
        <div class="mb-4">
          <AlertTriangle class="w-16 h-16 text-[#F43F5E]" />
        </div>
        <h3 class="text-[#6366F1] text-xl font-bold mb-4">Important Note</h3>
        <p class="text-gray-500 dark:text-slate-400 text-sm leading-relaxed max-w-xs">
          All settings on this page apply only to the landing page. Any changes to delivery charges or other settings here will not affect your main website's checkout or other pages. If you do not add delivery charges or keep this system inactive, the settings from your main website will be used on the landing page.
        </p>
      </div>
    </div>

    <!-- Bottom Toggle Section -->
    <div class="bg-white dark:bg-slate-900 border-2 border-[#A855F7] rounded-2xl p-8 mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 transition-colors duration-300">
      <div>
        <h3 class="text-[#9333EA] text-lg font-bold mb-1">Do you want to activate this delivery system?</h3>
        <p class="text-gray-400 dark:text-slate-500 text-sm font-medium">If activated, this setting will apply only to the landing page.</p>
      </div>
      <div class="flex items-center space-x-4">
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="checkbox" v-model="settings.is_active" class="sr-only peer">
          <div class="w-14 h-7 bg-gray-200 dark:bg-slate-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#9333EA]"></div>
          <span class="ms-3 text-sm font-bold text-gray-500 dark:text-slate-400 uppercase tracking-wider">{{ settings.is_active ? 'Active' : 'Inactive' }}</span>
        </label>
      </div>
    </div>

    <!-- Save Button -->
    <div class="flex justify-end">
      <button 
        @click="saveChanges" 
        :disabled="isLoading"
        class="bg-[#101828] dark:bg-slate-800 text-white px-10 py-3.5 rounded-xl font-bold hover:bg-black dark:hover:bg-slate-700 active:scale-95 transition-all shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
      >
        <Loader2 v-if="isLoading" class="w-5 h-5 mr-2 animate-spin" />
        {{ isLoading ? 'Saving...' : 'Save Changes' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { 
  ChevronLeft,
  AlertTriangle,
  Loader2
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
  inside_dhaka_charge: '80',
  outside_dhaka_charge: '100',
  footer_text: '',
  delivery_related_text: '',
  is_active: true
})

const isLoading = ref(false)

const fetchSettings = async () => {
    try {
        const { remoteSettings } = await getAll('/vendor/settings?group=landing_page')
        if (remoteSettings && remoteSettings.status === 'success') {
            const remoteSettings = data.value.data
            // Merge remote settings with defaults
            if (remoteSettings && Object.keys(remoteSettings).length > 0) {
                if (remoteSettings.inside_dhaka_charge !== undefined) settings.value.inside_dhaka_charge = remoteSettings.inside_dhaka_charge
                if (remoteSettings.outside_dhaka_charge !== undefined) settings.value.outside_dhaka_charge = remoteSettings.outside_dhaka_charge
                if (remoteSettings.footer_text !== undefined) settings.value.footer_text = remoteSettings.footer_text
                if (remoteSettings.delivery_related_text !== undefined) settings.value.delivery_related_text = remoteSettings.delivery_related_text
                if (remoteSettings.is_active !== undefined) settings.value.is_active = remoteSettings.is_active === '1' || remoteSettings.is_active === 1 || remoteSettings.is_active === true
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
    const { data, error } = await useApiFetch('/vendor/settings', {
      method: 'POST',
      body: {
        group: 'landing_page',
        settings: {
          ...settings.value,
          is_active: settings.value.is_active ? 1 : 0
        }
      }
    })

    if (data.value && data.value.status === 'success') {
      toast.success('Settings saved successfully!')
    } else {
      toast.error('Failed to save settings')
    }
  } catch (err) {
    console.error('Error saving settings:', err)
    toast.error('An error occurred while saving settings')
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
/* Custom toggle and input styling to perfectly match screenshot */
input::placeholder {
  color: #D1D5DB;
}
</style>