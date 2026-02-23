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
          <div class="flex items-center gap-2">
            <h1 class="text-2xl font-black text-slate-900 tracking-tight leading-none">Customer Review Settings</h1>
            <span class="px-2 py-0.5 bg-orange-500 text-white text-[9px] font-black rounded-full uppercase tracking-wider">PRO</span>
          </div>
        </div>
        <button 
          @click="saveSettings" 
          :disabled="saving || pending"
          class="px-8 py-3 bg-purple-600 hover:bg-purple-700 text-white font-black rounded-xl shadow-lg shadow-purple-200 transition-all active:scale-95 disabled:opacity-50 flex items-center gap-2"
        >
          <Save v-if="!saving" class="w-5 h-5" />
          <span>{{ saving ? 'Saving...' : 'Save Preferences' }}</span>
        </button>
      </div>
    </div>
    
    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="w-8 h-8 border-4 border-purple-600 border-t-transparent rounded-full animate-spin"></div>
    </div>
    
    <div v-else class="max-w-[1200px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
      <div class="lg:col-span-8 space-y-8">
        
        <!-- General Toggles -->
        <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm p-8 space-y-8">
          <div class="flex items-center gap-4 border-b border-slate-50 pb-6">
            <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center">
              <Star class="w-6 h-6 fill-current" />
            </div>
            <div>
              <h2 class="text-xl font-black text-slate-900">Review Preferences</h2>
              <p class="text-sm font-bold text-slate-500">Manage how product reviews work on your shop</p>
            </div>
          </div>
          
          <div class="space-y-6">
            <!-- Allow Reviews -->
            <div class="flex items-center justify-between p-6 bg-slate-50 rounded-2xl border border-slate-100">
              <div>
                <h3 class="font-black text-slate-800">Enable Customer Reviews</h3>
                <p class="text-xs font-bold text-slate-400 mt-0.5">Allow customers to write reviews on your products</p>
              </div>
              <button 
                @click="form.enableReviews = !form.enableReviews"
                :class="['w-14 h-8 rounded-full transition-all duration-300 relative', form.enableReviews ? 'bg-purple-600' : 'bg-slate-200']"
              >
                <div :class="['w-6 h-6 bg-white rounded-full absolute top-1 transition-all duration-300 shadow-sm', form.enableReviews ? 'left-7' : 'left-1']"></div>
              </button>
            </div>

            <!-- Auto Approval -->
            <div class="flex items-center justify-between p-6 bg-slate-50 rounded-2xl border border-slate-100" :class="{'opacity-50 pointer-events-none': !form.enableReviews}">
              <div>
                <h3 class="font-black text-slate-800">Auto-Approve Reviews</h3>
                <p class="text-xs font-bold text-slate-400 mt-0.5">New reviews will be published instantly without manual check</p>
              </div>
              <button 
                @click="form.autoApprove = !form.autoApprove"
                :class="['w-14 h-8 rounded-full transition-all duration-300 relative', form.autoApprove ? 'bg-purple-600' : 'bg-slate-200']"
              >
                <div :class="['w-6 h-6 bg-white rounded-full absolute top-1 transition-all duration-300 shadow-sm', form.autoApprove ? 'left-7' : 'left-1']"></div>
              </button>
            </div>

            <!-- Only Verified Buyers -->
            <div class="flex items-center justify-between p-6 bg-slate-50 rounded-2xl border border-slate-100" :class="{'opacity-50 pointer-events-none': !form.enableReviews}">
              <div>
                <h3 class="font-black text-slate-800">Verified Buyers Only</h3>
                <p class="text-xs font-bold text-slate-400 mt-0.5">Only customers who purchased the item can leave a review</p>
              </div>
              <button 
                @click="form.verifiedOnly = !form.verifiedOnly"
                :class="['w-14 h-8 rounded-full transition-all duration-300 relative', form.verifiedOnly ? 'bg-purple-600' : 'bg-slate-200']"
              >
                <div :class="['w-6 h-6 bg-white rounded-full absolute top-1 transition-all duration-300 shadow-sm', form.verifiedOnly ? 'left-7' : 'left-1']"></div>
              </button>
            </div>
            
            <div class="space-y-2 pt-4" :class="{'opacity-50 pointer-events-none': !form.enableReviews}">
              <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Minimum Rating Required to Auto-Approve (Stars)</label>
              <select v-model="form.minAutoApproveRating" class="w-full h-14 px-6 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-purple-500/10 focus:border-purple-500 outline-none transition-all font-semibold text-slate-700 appearance-none">
                <option value="1">1 Star & Above</option>
                <option value="2">2 Stars & Above</option>
                <option value="3">3 Stars & Above</option>
                <option value="4">4 Stars & Above</option>
                <option value="5">5 Stars Only</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Statistics Sidebar -->
      <div class="lg:col-span-4 space-y-6">
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-[32px] p-8 shadow-xl text-white relative overflow-hidden">
          <!-- decorative bg -->
          <div class="absolute -right-10 -top-10 w-40 h-40 bg-purple-500/20 rounded-full blur-3xl"></div>
          
          <h3 class="text-lg font-black mb-6 flex items-center gap-2">
            <MessageSquare class="w-5 h-5 text-purple-400" />
            Review Insights
          </h3>
          
          <div class="space-y-6">
             <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Average Rating</p>
                <div class="flex items-end gap-2">
                   <span class="text-4xl font-black">4.8</span>
                   <div class="flex pb-1">
                      <Star class="w-4 h-4 text-amber-400 fill-current" />
                      <Star class="w-4 h-4 text-amber-400 fill-current" />
                      <Star class="w-4 h-4 text-amber-400 fill-current" />
                      <Star class="w-4 h-4 text-amber-400 fill-current" />
                      <Star class="w-4 h-4 text-amber-400 fill-current" />
                   </div>
                </div>
             </div>
             
             <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/10">
                <div>
                   <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Total Reviews</p>
                   <span class="text-2xl font-black">1.2k</span>
                </div>
                <div>
                   <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Pending Approval</p>
                   <span class="text-2xl font-black text-amber-400">24</span>
                </div>
             </div>
          </div>
        </div>
        
        <div class="bg-purple-50 rounded-[32px] border border-purple-100 p-8">
           <div class="flex items-center gap-3 mb-4">
              <ShieldCheck class="w-6 h-6 text-purple-600" />
              <h3 class="font-black text-purple-900">Why Verified Reviews?</h3>
           </div>
           <p class="text-sm font-semibold text-purple-800/70 leading-relaxed">
             Turning on "Verified Buyers Only" ensures that competitors or bots cannot leave fake reviews on your shop. It builds immense trust among new customers.
           </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ChevronLeft, Save, Star, MessageSquare, ShieldCheck } from 'lucide-vue-next'
import { ref, reactive, onMounted } from 'vue'

definePageMeta({
  middleware: 'auth',
  permissions: 'settings.view'
})

const { $toast } = useNuxtApp()
const { getAll, createItem } = useCrud()
const router = useRouter()

const pending = ref(true)
const saving = ref(false)

const form = reactive({
  enableReviews: true,
  autoApprove: false,
  verifiedOnly: true,
  minAutoApproveRating: '4'
})

const loadSettings = async () => {
  try {
    pending.value = true
    const response = await getAll('/vendor/settings?group=customer_reviews')
    if (response?.data) {
       if (Object.keys(response.data).length > 0) {
          const loaded = response.data
          form.enableReviews = loaded.enableReviews === 'true' || loaded.enableReviews === true || loaded.enableReviews === '1'
          form.autoApprove = loaded.autoApprove === 'true' || loaded.autoApprove === true || loaded.autoApprove === '1'
          form.verifiedOnly = loaded.verifiedOnly === 'true' || loaded.verifiedOnly === true || loaded.verifiedOnly === '1'
          form.minAutoApproveRating = loaded.minAutoApproveRating || '4'
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
      group: 'customer_reviews',
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
