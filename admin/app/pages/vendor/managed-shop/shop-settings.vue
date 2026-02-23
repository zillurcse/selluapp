<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <!-- Breadcrumbs -->
    <div class="max-w-[1400px] mx-auto mb-8">
      <div class="flex items-center gap-2 text-sm text-slate-500 font-medium mb-4">
        <NuxtLink to="/vendor/managed-shop" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Manage Shop</NuxtLink>
        <ChevronRight class="w-4 h-4" />
        <span class="text-slate-900 dark:text-white font-bold">Shop Settings</span>
      </div>
      
      <div>
        <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Shop Settings</h1>
        <p class="text-slate-500 dark:text-slate-400 font-semibold mt-1">Configure your shop's core identity and operational preferences.</p>
      </div>
    </div>

    <!-- Main Content Layout -->
    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
    </div>
    <div v-else class="max-w-[1400px] mx-auto flex flex-col lg:flex-row gap-8">
      <!-- Left Column: Shop Info -->
      <div class="flex-1 space-y-6">
        <div class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm p-8 transition-colors duration-300">
          <h2 class="text-xl font-black text-slate-900 dark:text-white mb-8 mt-2">Shop Basic Info</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
            <!-- Shop Name -->
            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Shop Name *</label>
              <input 
                v-model="form.name"
                type="text" 
                placeholder="Enter shop name" 
                class="w-full h-12 px-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-slate-700 dark:text-slate-200 shadow-sm"
              >
            </div>

            <!-- Phone / Whatsapp -->
            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Phone / Whatsapp</label>
              <input 
                v-model="form.phone"
                type="text" 
                placeholder="Enter contact number" 
                class="w-full h-12 px-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-slate-700 dark:text-slate-200 shadow-sm"
              >
            </div>

            <!-- Shop Email -->
            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Shop Email *</label>
              <input 
                v-model="form.email"
                type="email" 
                placeholder="shop@example.com" 
                class="w-full h-12 px-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-slate-700 dark:text-slate-200 shadow-sm"
              >
            </div>

            <!-- Business Type -->
            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Business Type *</label>
              <div class="relative">
                <select 
                  v-model="form.businessType"
                  class="w-full h-12 px-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-slate-700 dark:text-slate-200 appearance-none shadow-sm"
                >
                  <option value="clothing">Clothing & Apparel</option>
                  <option value="electronics">Electronics</option>
                  <option value="grocery">Grocery</option>
                </select>
                <ChevronDown class="w-4 h-4 text-slate-400 absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none" />
              </div>
            </div>

            <!-- Currency -->
            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Currency</label>
              <div class="relative">
                <select 
                  v-model="form.currency"
                  class="w-full h-12 px-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-slate-700 dark:text-slate-200 appearance-none shadow-sm"
                >
                  <option value="BDT">BDT (৳)</option>
                  <option value="USD">USD ($)</option>
                </select>
                <ChevronDown class="w-4 h-4 text-slate-400 absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none" />
              </div>
            </div>

            <!-- Country -->
            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Country</label>
              <div class="relative">
                <select 
                  v-model="form.country"
                  class="w-full h-12 px-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-slate-700 dark:text-slate-200 appearance-none shadow-sm"
                >
                  <option value="Bangladesh">Bangladesh</option>
                  <option value="USA">USA</option>
                </select>
                <ChevronDown class="w-4 h-4 text-slate-400 absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none" />
              </div>
            </div>

            <!-- City Name -->
            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">City Name</label>
              <input 
                v-model="form.city"
                type="text" 
                placeholder="Enter city name" 
                class="w-full h-12 px-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-slate-700 dark:text-slate-200 shadow-sm"
              >
            </div>

            <!-- Shop Address -->
            <div class="space-y-2">
              <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Shop Address</label>
              <input 
                v-model="form.address"
                type="text" 
                placeholder="Enter full address" 
                class="w-full h-12 px-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-slate-700 dark:text-slate-200 shadow-sm"
              >
            </div>
          </div>

          <!-- Shop Details -->
          <div class="mt-8 space-y-2">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Shop Details *</label>
            <textarea 
              v-model="form.details"
              rows="3"
              placeholder="Tell customers about your shop..."
              class="w-full p-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-slate-700 dark:text-slate-200 shadow-sm resize-none"
            ></textarea>
          </div>

          <!-- Topbar Message -->
          <div class="mt-8 space-y-2">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Topbar Announcement Message</label>
            <textarea 
              v-model="form.announcement"
              rows="2"
              placeholder="Optional: Enter a message for the top notice bar"
              class="w-full p-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-slate-700 dark:text-slate-200 shadow-sm resize-none"
            ></textarea>
          </div>

          <div class="mt-10">
            <button @click="saveSettings" :disabled="saving" class="px-8 h-12 bg-slate-900 hover:bg-black text-white font-black rounded-xl transition-all shadow-lg active:scale-95 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
              <span v-if="saving">Saving...</span>
              <span v-else>Save Changes</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Right Column: Logo & Settings -->
      <div class="lg:w-[400px] space-y-6">
        <!-- Website Logo Card -->
        <div class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm p-8 flex flex-col items-center text-center transition-colors duration-300">
          <h2 class="text-lg font-black text-slate-800 dark:text-white mb-6">Website Logo [220H x 90W]</h2>
          
          <div @click="triggerLogoUpload" class="w-64 aspect-[220/90] bg-slate-50 dark:bg-slate-800 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl flex items-center justify-center p-4 group cursor-pointer hover:border-indigo-300 hover:bg-slate-100/50 dark:hover:bg-slate-700 transition-all relative overflow-hidden">
            <input type="file" ref="logoInput" class="hidden" accept="image/*" @change="onLogoChange">
            
            <img v-if="form.logo" :src="form.logo" alt="Logo preview" class="w-full h-full object-contain absolute inset-0 p-2">
            
            <div v-else class="space-y-1 relative z-10">
              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Upload Merchant Panel</p>
              <p class="text-[10px] font-bold text-slate-400">Logo</p>
              <p class="text-[10px] font-bold text-slate-400">[220 x 90]</p>
            </div>
            
            <div v-if="form.logo" class="absolute inset-0 bg-slate-900/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center z-20">
              <p class="text-white text-xs font-bold">Change Logo</p>
            </div>
          </div>
        </div>

        <!-- Shop Settings Card -->
        <div class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm p-8 space-y-8 transition-colors duration-300">
          <h2 class="text-xl font-black text-slate-900 dark:text-white border-b border-slate-50 dark:border-slate-800 pb-4">Shop Settings</h2>

          <!-- Website Language -->
          <div class="space-y-4">
            <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Website Language</label>
            <div class="flex gap-3">
              <button 
                @click="form.language = 'en'"
                :class="['flex-1 h-10 px-4 rounded-xl font-bold flex items-center justify-center gap-2 transition-all border', 
                  form.language === 'en' ? 'bg-white dark:bg-slate-800 border-slate-900 dark:border-slate-600 text-slate-900 dark:text-white shadow-md ring-2 ring-slate-100 dark:ring-slate-700' : 'bg-slate-50 dark:bg-slate-800 border-transparent text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700']"
              >
                <img src="https://flagcdn.com/w20/us.png" class="w-4 h-3 rounded-sm object-cover" alt="US">
                English
              </button>
              <button 
                @click="form.language = 'bn'"
                :class="['flex-1 h-10 px-4 rounded-xl font-bold flex items-center justify-center gap-2 transition-all border', 
                  form.language === 'bn' ? 'bg-black dark:bg-slate-800 border-slate-900 dark:border-slate-600 text-white dark:text-white shadow-md ring-2 ring-slate-100 dark:ring-slate-700' : 'bg-slate-50 dark:bg-slate-800 border-transparent text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700']"
              >
                <img src="https://flagcdn.com/w20/bd.png" class="w-4 h-3 rounded-sm object-cover" alt="BD">
                Bangla
              </button>
            </div>
          </div>

          <!-- Invoice Print Type -->
          <div class="space-y-4">
            <label class="text-sm font-bold text-slate-700">Invoice Print Type</label>
            <p class="text-[10px] font-bold text-slate-400 leading-tight">From now on, whatever you choose will be printed in your system as an invoice</p>
            <div class="flex gap-3">
              <button 
                @click="form.printType = 'a4'"
                :class="['flex-1 h-10 px-4 rounded-xl font-bold flex items-center justify-center gap-2 transition-all border', 
                  form.printType === 'a4' ? 'bg-black text-white' : 'bg-slate-50 text-slate-500 hover:bg-slate-100']"
              >
                <FileText class="w-4 h-4" />
                A4 Size
              </button>
              <button 
                @click="form.printType = 'thermal'"
                :class="['flex-1 h-10 px-4 rounded-xl font-bold flex items-center justify-center gap-2 transition-all border', 
                  form.printType === 'thermal' ? 'bg-indigo-600 text-white' : 'bg-slate-50 text-slate-500 hover:bg-slate-100']"
              >
                <Printer class="w-4 h-4" />
                Thermal
              </button>
            </div>
          </div>

          <!-- Print After Sale -->
          <div class="space-y-2">
            <h3 class="text-sm font-bold text-slate-700">Print After Sale</h3>
            <p class="text-[10px] font-bold text-slate-400">Print the invoice after the sale is completed.</p>
            <div class="flex items-center gap-3">
              <button 
                @click="form.printAfterSale = !form.printAfterSale"
                :class="['w-12 h-6 rounded-full transition-all duration-300 relative', form.printAfterSale ? 'bg-slate-900' : 'bg-slate-200']"
              >
                <div :class="['w-4 h-4 bg-white rounded-full absolute top-1 transition-all duration-300', form.printAfterSale ? 'left-7' : 'left-1']"></div>
              </button>
              <span class="text-xs font-bold text-slate-500">Enable</span>
            </div>
          </div>

          <!-- Maintain Stock -->
          <div class="space-y-2">
            <h3 class="text-sm font-bold text-slate-700">Maintain Stock Quantity</h3>
            <p class="text-[10px] font-bold text-slate-400">If enabled, out-of-stock products will be shown as "Out of Stock" and customers can't order them.</p>
            <div class="flex items-center gap-3">
              <button 
                @click="form.maintainStock = !form.maintainStock"
                :class="['w-12 h-6 rounded-full transition-all duration-300 relative', form.maintainStock ? 'bg-slate-900' : 'bg-slate-200']"
              >
                <div :class="['w-4 h-4 bg-white rounded-full absolute top-1 transition-all duration-300', form.maintainStock ? 'left-7' : 'left-1']"></div>
              </button>
              <span class="text-xs font-bold text-slate-500">Enable</span>
            </div>
          </div>

          <!-- VAT / Tax -->
          <div class="space-y-2">
            <label class="text-sm font-bold text-slate-700">VAT / Tax Percentage</label>
            <input 
              v-model="form.vat"
              type="text" 
              class="w-full h-11 px-4 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-slate-700 shadow-sm"
              placeholder="0.00"
            >
          </div>

          <div class="pt-4 border-t border-slate-50">
            <button @click="saveSettings" :disabled="saving" class="w-full h-12 bg-slate-900 hover:bg-black text-white font-black rounded-xl transition-all shadow-lg active:scale-95 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
              <span v-if="saving">Saving...</span>
              <span v-else>Save Changes</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  ChevronRight, 
  ChevronDown,
  FileText,
  Printer
} from 'lucide-vue-next'
import { reactive, ref, onMounted } from 'vue'

definePageMeta({
  middleware: 'auth',
  permissions: 'settings.view'
})

const { $toast } = useNuxtApp()
const { getAll, createItem } = useCrud()

const form = reactive({
  name: '',
  phone: '',
  email: '',
  businessType: 'clothing',
  currency: 'BDT',
  country: 'Bangladesh',
  city: '',
  address: '',
  details: '',
  announcement: '',
  language: 'en',
  printType: 'a4',
  printAfterSale: false,
  maintainStock: true,
  vat: '0.00',
  logo: null
})

const pending = ref(true)
const saving = ref(false)
const logoInput = ref(null)

const triggerLogoUpload = () => {
  logoInput.value?.click()
}

const onLogoChange = (event) => {
  const file = event.target.files[0]
  if (!file) return
  
  if (file.size > 2 * 1024 * 1024) {
    $toast.error('Logo image must be less than 2MB')
    return
  }
  
  // Create object URL for immediate preview layout
  form.logo = URL.createObjectURL(file)
  // Store the raw file object secretly for the submit handler
  form._rawLogoFile = file
}

const loadSettings = async () => {
  try {
    pending.value = true
    const response = await getAll('/vendor/settings?group=shop_settings')
    if (response.data) {
      Object.assign(form, response.data)
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

    // Only send the _rawLogoFile if it exists, otherwise send standard JSON.
    if (form._rawLogoFile) {
      const formData = new FormData()
      formData.append('group', 'shop_settings')
      
      // We must stringify the rest of the settings for the 'settings' key
      const formCopy = { ...form }
      delete formCopy.logo // we'll use the uploaded URL returned by backend instead
      delete formCopy._rawLogoFile
      formData.append('settings', JSON.stringify(formCopy))
      
      // Append the file directly
      formData.append('files[logo]', form._rawLogoFile)

      await createItem('/vendor/settings', formData)
    } else {
      await createItem('/vendor/settings', {
        group: 'shop_settings',
        settings: form
      })
    }
    
    // Clear out the temporary raw file pointer so it doesn't try to upload again next time
    delete form._rawLogoFile

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
