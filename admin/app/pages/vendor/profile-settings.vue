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
             
             <label class="absolute inset-0 flex items-center justify-center cursor-pointer bg-black/0 hover:bg-black/10 transition-colors">
                <div class="bg-white/90 backdrop-blur-sm px-4 py-2 rounded-full shadow-lg text-sm font-medium text-gray-700 flex items-center gap-2 opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                   <Camera class="w-4 h-4" /> Change Banner
                </div>
                <input type="file" @change="handleBannerUpload" accept="image/*" class="hidden" />
             </label>
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
                         <label class="absolute inset-0 flex items-center justify-center cursor-pointer bg-black/0 hover:bg-black/5 rounded-xl transition-all">
                            <div class="bg-white shadow-sm border border-gray-200 p-1.5 rounded-full opacity-0 group-hover:opacity-100 transform scale-90 group-hover:scale-100 transition-all">
                               <Camera class="w-4 h-4 text-gray-600" />
                            </div>
                            <input type="file" @change="handleLogoUpload" accept="image/*" class="hidden" />
                         </label>
                      </div>
                      
                      <div class="flex-1 space-y-4">
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                               <label class="block text-sm font-medium text-gray-700">Store Name</label>
                               <input v-model="form.store_name" type="text" class="block w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-900 focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none">
                            </div>
                            <div class="space-y-1">
                               <label class="block text-sm font-medium text-gray-700">Store Slug (URL)</label>
                               <div class="flex rounded-lg shadow-sm">
                                  <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-200 bg-gray-50 text-gray-500 text-sm">/shop/</span>
                                  <input v-model="form.store_slug" type="text" class="flex-1 min-w-0 block w-full px-4 py-2.5 bg-white border border-gray-200 rounded-r-lg text-gray-900 focus:ring-2 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none">
                               </div>
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
             <div class="p-10 space-y-6">
                <div class="max-w-md space-y-4">
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
             </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-3 sticky bottom-6 z-10">
             <div class="bg-white/80 backdrop-blur-md p-2 rounded-xl shadow-lg border border-gray-100 flex gap-3">
                <button type="button" @click="fetchProfile" class="px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                   Reset Changes
                </button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium bg-black text-white hover:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                   <Save class="w-4 h-4" /> Save Changes
                </button>
             </div>
          </div>
        </form>
      </main>
    </div>
  </div>
</template>

<script setup>
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
  Save
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

// Fetch User Profile
const fetchProfile = async () => {
    try {
        const { data } = await useFetch(`${config.public.apiBase}/vendor/profile`, {
             headers: { Authorization: `Bearer ${auth.token}` }
        })
        
        if (data.value) {
            const user = data.value
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

            if (profile.logo) logoPreview.value = `${config.public.apiBase}/storage/${profile.logo}`
            if (profile.banner) bannerPreview.value = `${config.public.apiBase}/storage/${profile.banner}`
        }
    } catch (e) {
        console.error('Failed to fetch profile', e)
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

const updateProfile = async () => {
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
        if(form.banner instanceof File) formData.append('banner', form.banner)

        await $fetch(`${config.public.apiBase}/vendor/profile`, {
            method: 'POST',
            body: formData,
            headers: {
                Authorization: `Bearer ${auth.token}`
            }
        })
        
        toast.success('Profile updated successfully')
        
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
    }
}
</script>
