<template>
  <div class="max-w-[1200px] mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="md:flex md:gap-6">
      <!-- Sidebar Navigation -->
      <nav class="md:w-1/4 mb-8 md:mb-0">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden sticky top-24">
          <div class="p-10 border-b border-gray-100 bg-gray-50/50">
             <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-md">
                   {{ userInitials }}
                </div>
                <div class="overflow-hidden">
                   <h3 class="text-sm font-bold text-gray-900 truncate">{{ form.name || 'Vendor' }}</h3>
                   <p class="text-xs text-gray-500 truncate">{{ form.email }}</p>
                </div>
             </div>
          </div>
          <div class="p-2 space-y-1">
            <button 
              v-for="tab in tabs" 
              :key="tab.id"
              @click="activeTab = tab.id"
              :class="[
                activeTab === tab.id
                  ? 'bg-indigo-50 text-indigo-700 font-semibold'
                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                'w-full flex items-center px-4 py-3 text-sm rounded-lg transition-all duration-200'
              ]"
            >
              <component :is="tab.icon" class="mr-3 h-5 w-5" :class="activeTab === tab.id ? 'text-indigo-600' : 'text-gray-400'" />
              {{ tab.name }}
            </button>
          </div>
        </div>
      </nav>

      <!-- Main Content Area -->
      <main class="md:w-3/4">
        <form @submit.prevent="updateProfile" class="space-y-6">
          
          <!-- Banner Section (Global for profile) -->
          <div v-if="activeTab === 'merchant'" class="relative h-48 rounded-xl overflow-hidden bg-gradient-to-r from-gray-800 to-gray-900 shadow-md group">
             <img v-if="bannerPreview" :src="bannerPreview" class="w-full h-full object-cover opacity-80 group-hover:opacity-75 transition-opacity" />
             <div v-else class="w-full h-full flex items-center justify-center text-gray-500">
                <ImageIcon class="h-12 w-12 opacity-20" />
             </div>
             
             <button type="button" @click="openBannerMediaLibrary" class="absolute inset-0 flex items-center justify-center cursor-pointer bg-black/0 hover:bg-black/10 transition-colors">
                <div class="bg-white/90 backdrop-blur-sm px-4 py-2 rounded-full shadow-lg text-sm font-medium text-gray-700 flex items-center gap-2 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                   <Camera class="w-4 h-4" /> Change Banner
                </div>
             </button>
          </div>

          <!-- Profile Information Tab -->
          <div v-show="activeTab === 'profile'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
             <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                <div>
                   <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                   <p class="text-sm text-gray-500 mt-1">Update your personal details and contact info.</p>
                </div>
             </div>
             <div class="p-10 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                   <div class="space-y-1">
                      <label class="block text-sm font-medium text-gray-700">Full Name</label>
                      <input v-model="form.name" type="text" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none" placeholder="John Doe">
                   </div>
                   <div class="space-y-1">
                      <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                      <input v-model="form.phone" type="text" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none" placeholder="+1 (555) 000-0000">
                   </div>
                   <div class="space-y-1 md:col-span-2">
                      <label class="block text-sm font-medium text-gray-700">Email Address</label>
                       <div class="relative">
                          <Mail class="absolute left-3 top-3 h-5 w-5 text-gray-400" />
                          <input v-model="form.email" type="email" disabled class="block w-full pl-10 pr-4 py-2.5 bg-gray-100 border border-transparent rounded-lg text-gray-500 cursor-not-allowed">
                       </div>
                       <p class="text-xs text-gray-500 mt-1">Contact support to change your email address.</p>
                   </div>
                </div>
             </div>
          </div>

          <!-- Merchant Settings Tab -->
          <div v-show="activeTab === 'merchant'" class="space-y-6">
             <!-- Shop Details Card -->
             <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                   <h3 class="text-lg font-semibold text-gray-900">Shop Details</h3>
                   <p class="text-sm text-gray-500 mt-1">Manage your store's public profile.</p>
                </div>
                <div class="p-10 space-y-6">
                   <div class="flex items-start gap-6">
                      <!-- Logo Upload -->
                      <div class="flex-shrink-0 group relative">
                         <div class="h-24 w-24 rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden bg-gray-50">
                            <img v-if="logoPreview" :src="logoPreview" class="w-full h-full object-cover" />
                            <Store v-else class="h-8 w-8 text-gray-300" />
                         </div>
                         <button type="button" @click="openLogoMediaLibrary" class="absolute inset-0 flex items-center justify-center cursor-pointer bg-black/0 hover:bg-black/5 rounded-xl transition-all">
                            <div class="bg-white shadow-sm border border-gray-200 p-1.5 rounded-full opacity-0 group-hover:opacity-100 transform scale-90 group-hover:scale-100 transition-all">
                               <Camera class="w-4 h-4 text-gray-600" />
                            </div>
                         </button>
                      </div>
                      
                      <div class="flex-1 space-y-4">
                         <div class="grid grid-cols-1 gap-4">
                            <div class="space-y-1">
                               <label class="block text-sm font-medium text-gray-700">Store Name</label>
                               <input v-model="form.store_name" type="text" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none">
                            </div>
                         </div>
                      </div>
                   </div>

                   <div class="space-y-1">
                      <label class="block text-sm font-medium text-gray-700">Description</label>
                      <textarea v-model="form.description" rows="3" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none resize-none"></textarea>
                   </div>
                   
                   <div class="space-y-1">
                      <label class="block text-sm font-medium text-gray-700">Store Address</label>
                      <textarea v-model="form.address" rows="2" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none resize-none"></textarea>
                   </div>
                </div>
             </div>

             <!-- Socials Card -->
             <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                   <h3 class="text-lg font-semibold text-gray-900">Social Connections</h3>
                   <p class="text-sm text-gray-500 mt-1">Connect your social media accounts.</p>
                </div>
                <div class="p-10 grid grid-cols-1 md:grid-cols-2 gap-4">
                   <div class="relative">
                      <Facebook class="absolute left-3 top-3 h-5 w-5 text-blue-600" />
                      <input v-model="form.facebook" placeholder="Facebook URL" class="pl-10 block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none">
                   </div>
                   <div class="relative">
                      <Instagram class="absolute left-3 top-3 h-5 w-5 text-pink-600" />
                      <input v-model="form.instagram" placeholder="Instagram URL" class="pl-10 block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none">
                   </div>
                   <div class="relative">
                      <Twitter class="absolute left-3 top-3 h-5 w-5 text-sky-500" />
                      <input v-model="form.twitter" placeholder="Twitter URL" class="pl-10 block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none">
                   </div>
                   <div class="relative">
                      <Youtube class="absolute left-3 top-3 h-5 w-5 text-red-600" />
                      <input v-model="form.youtube" placeholder="YouTube URL" class="pl-10 block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none">
                   </div>
                </div>
             </div>
          </div>

          <!-- Password Tab -->
          <div v-show="activeTab === 'password'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
             <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">Security</h3>
                <p class="text-sm text-gray-500 mt-1">Manage your password and security settings.</p>
             </div>
             <div class="p-10 space-y-10">
                <!-- Password Section -->
                <div class="max-w-md space-y-4">
                   <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Change Password</h4>
                   <div class="space-y-1">
                      <label class="block text-sm font-medium text-gray-700">Current Password</label>
                      <input v-model="form.current_password" type="password" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none">
                   </div>
                   <div class="space-y-1">
                      <label class="block text-sm font-medium text-gray-700">New Password</label>
                      <input v-model="form.new_password" type="password" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none">
                   </div>
                   <div class="space-y-1">
                      <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                      <input v-model="form.new_password_confirmation" type="password" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none">
                   </div>
                </div>

                <div class="border-t border-gray-100 pt-10"></div>

                <!-- Security PIN Section -->
                <div class="max-w-md space-y-6">
                   <div>
                      <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Security PIN</h4>
                      <p class="text-xs text-gray-500 mb-4">Set a 5-digit PIN for quick authentication.</p>
                   </div>
                   <div class="flex gap-3">
                      <input 
                        v-for="i in 5" 
                        :key="i"
                        v-model="pinDigits[i-1]"
                        type="password"
                        maxlength="1"
                        inputmode="numeric"
                        @input="handlePinInput($event, i-1)"
                        @keydown.delete="handlePinBackspace($event, i-1)"
                        :ref="el => setPinRef(el, i-1)"
                        class="w-12 h-14 bg-gray-50 border border-gray-200 rounded-xl text-center text-xl font-bold text-gray-900 focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none"
                      >
                   </div>
                   <button 
                     type="button" 
                     @click="saveSecurityPin" 
                     :disabled="isSavingPin || pinDigits.some(d => !d)"
                     class="px-5 py-2.5 text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700 rounded-lg shadow-sm transition-all flex items-center gap-2 disabled:opacity-50"
                   >
                      <div v-if="isSavingPin" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                      <ShieldCheck v-else class="w-4 h-4" />
                      {{ isSavingPin ? 'Saving PIN...' : 'Update Security PIN' }}
                   </button>
                </div>
             </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-3 sticky bottom-6 z-10">
             <div class="bg-white/80 backdrop-blur-md p-2 rounded-xl shadow-lg border border-gray-100 flex gap-3">
                <button type="button" @click="fetchProfile" :disabled="isFetching" class="px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors disabled:opacity-50">
                   {{ isFetching ? 'Resetting...' : 'Reset Changes' }}
                </button>
                <button type="submit" :disabled="isSaving || isFetching" class="px-5 py-2.5 text-sm font-medium bg-black text-white hover:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                   <div v-if="isSaving" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                   <Save v-else class="w-4 h-4" /> 
                   {{ isSaving ? 'Saving...' : 'Save Changes' }}
                </button>
             </div>
          </div>
        </form>
      </main>
      
      <!-- Media Libraries -->
      <AppMediaLibrary
        :show="showLogoMediaModal"
        :multiple="false"
        type-label="Logo"
        @close="showLogoMediaModal = false"
        @select="handleLogoSelection"
      />

      <AppMediaLibrary
        :show="showBannerMediaModal"
        :multiple="false"
        type-label="Banner"
        @close="showBannerMediaModal = false"
        @select="handleBannerSelection"
      />
    </div>
  </div>
</template>

<script setup>
import AppMediaLibrary from '~/components/AppMediaLibrary.vue'
import { 
  User, 
  Store, 
  Lock, 
  Camera, 
  Mail,
  Facebook,
  Instagram,
  Twitter,
  Youtube,
  Image as ImageIcon,
  Save,
  ShieldCheck
} from 'lucide-vue-next'
import { toast } from 'vue-sonner'

definePageMeta({
  middleware: 'auth',
  permissions: 'settings.view'
})

const config = useRuntimeConfig()
const auth = useAuthStore()

const tabs = [
  { id: 'profile', name: 'Profile Information', icon: User },
  { id: 'merchant', name: 'Merchant Settings', icon: Store },
  { id: 'password', name: 'Security', icon: Lock },
]

const activeTab = ref('profile')
const logoPreview = ref(null)
const bannerPreview = ref(null)

const showLogoMediaModal = ref(false)
const showBannerMediaModal = ref(false)

const isFetching = ref(false)
const isSaving = ref(false)
const isSavingPin = ref(false)

const pinDigits = ref(['', '', '', '', ''])
const pinRefs = ref([])

const setPinRef = (el, index) => {
  if (el) pinRefs.value[index] = el
}

const handlePinInput = (event, index) => {
    const val = event.target.value
    if (val && !isNaN(val) && index < 4) {
        setTimeout(() => pinRefs.value[index + 1]?.focus(), 10)
    }
}

const handlePinBackspace = (event, index) => {
    if (!pinDigits.value[index] && index > 0) {
        pinRefs.value[index - 1]?.focus()
    }
}

const saveSecurityPin = async () => {
    const pin = pinDigits.value.join('')
    if (pin.length !== 5) return

    isSavingPin.value = true
    try {
        await createItem('/pin/set', { pin })
        toast.success('Security PIN updated successfully')
        pinDigits.value = ['', '', '', '', '']
    } catch (error) {
        console.error('Failed to save PIN', error)
        toast.error(error.data?.message || 'Failed to update Security PIN')
    } finally {
        isSavingPin.value = false
    }
}

const userInitials = computed(() => {
   const name = auth.user?.name || 'U'
   return name.substring(0, 1).toUpperCase()
})

const form = reactive({
  name: '',
  email: '',
  phone: '',
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
  store_name: '',
  store_slug: '',
  description: '',
  address: '',
  logo: null,
  banner: null,
  facebook: '',
  twitter: '',
  instagram: '',
  youtube: '',
})
const { getAll, createItem } = useCrud()
// Fetch User Profile
const fetchProfile = async () => {
    isFetching.value = true
    try {
        const data = await getAll('/vendor/profile')
        console.log('data', data);
        
        if (data) {
            const user = data
            const profile = user.vendor_profile || {}

            form.name = user.name
            form.email = user.email
            
            // Map vendor profile data
            form.phone = profile.phone || ''
            form.store_name = profile.store_name || ''
            form.store_slug = profile.store_slug || ''
            form.description = profile.description || ''
            form.address = profile.address || ''
            form.facebook = profile.facebook || ''
            form.twitter = profile.twitter || ''
            form.instagram = profile.instagram || ''
            form.youtube = profile.youtube || ''

            form.logo = null
            form.banner = null
            
            logoPreview.value = profile.logo ?? profile.logo
            bannerPreview.value = profile.banner ?? profile.banner
            
            // Reset the slug manual flag so that it gets auto-generated again if name changes
            isSlugManuallyEdited.value = false
        }
    } catch (e) {
        console.error('Failed to fetch profile', e)
    } finally {
        isFetching.value = false
    }
}

fetchProfile()

const handleLogoUpload = (e) => {
    const file = e.target.files[0]
    if (file) {
        form.logo = file
        logoPreview.value = URL.createObjectURL(file)
    }
}

const handleBannerUpload = (e) => {
    const file = e.target.files[0]
    if (file) {
        form.banner = file
        bannerPreview.value = URL.createObjectURL(file)
    }
}

const openLogoMediaLibrary = () => {
    showLogoMediaModal.value = true
}

const openBannerMediaLibrary = () => {
    showBannerMediaModal.value = true
}

const handleLogoSelection = (selected) => {
    form.logo = selected.file_url
    logoPreview.value = selected.file_url
}

const handleBannerSelection = (selected) => {
    form.banner = selected.file_url
    bannerPreview.value = selected.file_url
}

const updateProfile = async () => {
    isSaving.value = true
    try {
        const formData = new FormData()
        
        // Basic Info
        formData.append('name', form.name)
        if(form.phone) formData.append('phone', form.phone)
        
        // Password
        if(form.current_password) formData.append('current_password', form.current_password)
        if(form.new_password) formData.append('new_password', form.new_password)
        if(form.new_password_confirmation) formData.append('new_password_confirmation', form.new_password_confirmation)

        // Vendor Profile
        if(form.store_name) formData.append('store_name', form.store_name)
        if(form.store_slug) formData.append('store_slug', form.store_slug)
        if(form.description) formData.append('description', form.description)
        if(form.address) formData.append('address', form.address)
        if(form.facebook) formData.append('facebook', form.facebook)
        if(form.twitter) formData.append('twitter', form.twitter)
        if(form.instagram) formData.append('instagram', form.instagram)
        if(form.youtube) formData.append('youtube', form.youtube)

        // Files
        if(form.logo instanceof File) formData.append('logo', form.logo)
        else if (form.logo && typeof form.logo === 'string') formData.append('logo', form.logo)

        if(form.banner instanceof File) formData.append('banner', form.banner)
        else if (form.banner && typeof form.banner === 'string') formData.append('banner', form.banner)

        await createItem('/vendor/profile', formData)
        
        // Clear password fields
        form.current_password = ''
        form.new_password = ''
        form.new_password_confirmation = ''
        
        // Refresh auth user data if needed
        auth.fetchUser()
        
    } catch (error) {
        console.error('Update failed', error)
        const msg = error.data?.message || 'Failed to update profile'
        toast.error(msg)
    } finally {
        isSaving.value = false
    }
}

// Auto-generate store slug when typing store name if slug is empty
const isSlugManuallyEdited = ref(false)

const generateSlug = (text) => {
    if (!text) return ''
    return text.toString().toLowerCase()
        .replace(/\s+/g, '-')           // Replace spaces with -
        .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
        .replace(/\-\-+/g, '-')         // Replace multiple - with single -
        .replace(/^-+/, '')             // Trim - from start of text
        .replace(/-+$/, '');            // Trim - from end of text
}

watch(() => form.store_slug, (newSlug, oldSlug) => {
    if (oldSlug !== undefined && newSlug !== generateSlug(form.store_name)) {
        isSlugManuallyEdited.value = true
    }
})

watch(() => form.store_name, (newName) => {
    if (!isSlugManuallyEdited.value) {
        form.store_slug = generateSlug(newName)
    }
})
</script>
