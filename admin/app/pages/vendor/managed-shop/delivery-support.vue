<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <!-- Header -->
    <div class="max-w-6xl mx-auto mb-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4 mb-2">
          <NuxtLink 
            to="/vendor/managed-shop" 
            class="w-10 h-10 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center border border-slate-200 dark:border-slate-700 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-all text-[#1E293B] dark:text-slate-200"
          >
            <ChevronLeft class="w-5 h-5" />
          </NuxtLink>
          <div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Delivery Support</h1>
            <p class="text-slate-500 dark:text-slate-400 font-semibold mt-1">Configure your courier gateways and delivery preferences.</p>
          </div>
        </div>
        <button @click="saveSettings" :disabled="saving || pending" class="px-8 py-3 bg-[#0F172A] text-white font-black rounded-xl hover:bg-slate-800 transition-all active:scale-95 shadow-xl shadow-slate-900/10 disabled:opacity-50 flex items-center gap-2">
          <Save v-if="!saving" class="w-5 h-5" />
          <span>{{ saving ? 'Saving...' : 'Save Changes' }}</span>
        </button>
      </div>
    </div>

    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="w-8 h-8 border-4 border-[#0F172A] border-t-transparent rounded-full animate-spin"></div>
    </div>
    <div v-else class="max-w-6xl mx-auto">
      <!-- Main Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <!-- RedX Courier -->
        <div class="bg-white dark:bg-slate-900 rounded-[24px] border border-slate-100 dark:border-slate-800 shadow-sm p-8 space-y-6 relative overflow-hidden transition-colors duration-300">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-black text-[#EF4444]">RedX Courier</h2>
            <div class="flex items-center gap-2">
              <span class="text-[10px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest">{{ form.redx.active ? 'Active' : 'Inactive' }}</span>
              <button 
                @click="form.redx.active = !form.redx.active"
                :class="['w-12 h-6 rounded-full transition-all duration-300 relative', form.redx.active ? 'bg-indigo-600' : 'bg-slate-200 dark:bg-slate-700']"
              >
                <div :class="['w-4 h-4 bg-white rounded-full absolute top-1 transition-all duration-300 shadow-sm', form.redx.active ? 'left-7' : 'left-1']"></div>
              </button>
            </div>
          </div>
          <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 leading-relaxed">
            Fast and reliable delivery service across Bangladesh. Seamless integration for managing your shop's shipments. Trusted partner to ensure quick customer deliveries.
          </p>
          <div class="space-y-4" v-show="form.redx.active">
            <div class="space-y-1.5">
              <input v-model="form.redx.storeId" type="text" placeholder="Store ID" class="w-full h-12 px-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200 text-sm">
            </div>
            <div class="relative">
              <input v-model="form.redx.password" :type="showRedxPass ? 'text' : 'password'" placeholder="Password" class="w-full h-12 px-5 pr-12 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200 text-sm">
              <button @click="showRedxPass = !showRedxPass" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                <EyeOff v-if="showRedxPass" class="w-4 h-4" />
                <Eye v-else class="w-4 h-4" />
              </button>
            </div>
            <div class="space-y-2">
              <p class="text-[10px] font-black text-slate-600 dark:text-slate-400">Please add this URL into your RedX courier panel for webhook.</p>
              <div class="flex">
                <input readonly value="https://rakibstore.sellsull.com/redx/status-update" class="flex-grow h-12 px-5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 border-r-0 rounded-l-xl font-semibold text-slate-400 dark:text-slate-500 text-xs">
                <button @click="copyToClipboard('https://rakibstore.sellsull.com/redx/status-update')" class="w-12 h-12 flex items-center justify-center bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-r-xl text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                  <Copy class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Pathao Courier -->
        <div class="bg-white dark:bg-slate-900 rounded-[24px] border border-slate-100 dark:border-slate-800 shadow-sm p-8 space-y-6 relative overflow-hidden transition-colors duration-300">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-black text-[#F97316]">Pathao Courier</h2>
            <div class="flex items-center gap-2">
              <span class="text-[10px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-widest">{{ form.pathao.active ? 'Active' : 'Inactive' }}</span>
              <button 
                @click="form.pathao.active = !form.pathao.active"
                :class="['w-12 h-6 rounded-full transition-all duration-300 relative', form.pathao.active ? 'bg-indigo-600' : 'bg-slate-200 dark:bg-slate-700']"
              >
                <div :class="['w-4 h-4 bg-white rounded-full absolute top-1 transition-all duration-300 shadow-sm', form.pathao.active ? 'left-7' : 'left-1']"></div>
              </button>
            </div>
          </div>
          <p class="text-xs font-semibold text-slate-400 leading-relaxed">
            Nationwide delivery with fast, reliable logistics for your business.
          </p>
          <div class="space-y-4" v-show="form.pathao.active">
            <input v-model="form.pathao.storeId" type="text" placeholder="Store ID" class="w-full h-12 px-5 bg-white border border-slate-200 rounded-xl outline-none focus:border-indigo-500 font-semibold text-slate-700 text-sm">
            <input v-model="form.pathao.clientId" type="text" placeholder="Client ID" class="w-full h-12 px-5 bg-white border border-slate-200 rounded-xl outline-none focus:border-indigo-500 font-semibold text-slate-700 text-sm">
            <div class="relative">
              <input v-model="form.pathao.clientSecret" :type="showPathaoSecret ? 'text' : 'password'" placeholder="Client Secret" class="w-full h-12 px-5 pr-12 bg-white border border-slate-200 rounded-xl outline-none focus:border-indigo-500 font-semibold text-slate-700 text-sm">
              <button @click="showPathaoSecret = !showPathaoSecret" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                <EyeOff v-if="showPathaoSecret" class="w-4 h-4" />
                <Eye v-else class="w-4 h-4" />
              </button>
            </div>
            <input v-model="form.pathao.email" type="email" placeholder="Email" class="w-full h-12 px-5 bg-white border border-slate-200 rounded-xl outline-none focus:border-indigo-500 font-semibold text-slate-700 text-sm">
            <div class="relative">
              <input v-model="form.pathao.password" :type="showPathaoPass ? 'text' : 'password'" placeholder="Password" class="w-full h-12 px-5 pr-12 bg-white border border-slate-200 rounded-xl outline-none focus:border-indigo-500 font-semibold text-slate-700 text-sm">
              <button @click="showPathaoPass = !showPathaoPass" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                <EyeOff v-if="showPathaoPass" class="w-4 h-4" />
                <Eye v-else class="w-4 h-4" />
              </button>
            </div>
            <div class="space-y-2">
              <p class="text-[10px] font-black text-slate-600">Please add this URL into your Pathao courier panel for webhook.</p>
              <div class="flex">
                <input readonly value="https://rakibstore.sellsull.com/pathao/status-update" class="flex-grow h-12 px-5 bg-slate-50 border border-slate-200 border-r-0 rounded-l-xl font-semibold text-slate-400 text-xs">
                <button @click="copyToClipboard('https://rakibstore.sellsull.com/pathao/status-update')" class="w-12 h-12 flex items-center justify-center bg-white border border-slate-200 rounded-r-xl text-slate-400 hover:text-indigo-600 transition-colors">
                  <Copy class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Steadfast Courier -->
        <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm p-8 space-y-6 relative overflow-hidden">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-black text-[#22C55E]">Steadfast Courier</h2>
            <div class="flex items-center gap-2">
              <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">{{ form.steadfast.active ? 'Active' : 'Inactive' }}</span>
              <button 
                @click="form.steadfast.active = !form.steadfast.active"
                :class="['w-12 h-6 rounded-full transition-all duration-300 relative', form.steadfast.active ? 'bg-indigo-600' : 'bg-slate-200']"
              >
                <div :class="['w-4 h-4 bg-white rounded-full absolute top-1 transition-all duration-300 shadow-sm', form.steadfast.active ? 'left-7' : 'left-1']"></div>
              </button>
            </div>
          </div>
          <p class="text-xs font-semibold text-slate-400 leading-relaxed">
            Secure and efficient parcel delivery service across Bangladesh.
          </p>
          <div class="space-y-4" v-show="form.steadfast.active">
            <input v-model="form.steadfast.apiKey" type="text" placeholder="API Key" class="w-full h-12 px-5 bg-white border border-slate-200 rounded-xl outline-none focus:border-indigo-500 font-semibold text-slate-700 text-sm">
            <div class="relative">
              <input v-model="form.steadfast.secretKey" :type="showSteadfastSecret ? 'text' : 'password'" placeholder="Secret Key" class="w-full h-12 px-5 pr-12 bg-white border border-slate-200 rounded-xl outline-none focus:border-indigo-500 font-semibold text-slate-700 text-sm">
              <button @click="showSteadfastSecret = !showSteadfastSecret" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                <EyeOff v-if="showSteadfastSecret" class="w-4 h-4" />
                <Eye v-else class="w-4 h-4" />
              </button>
            </div>
            <div class="space-y-2">
              <p class="text-[10px] font-black text-slate-600">Please add this URL into your Steadfast courier panel for webhook.</p>
              <div class="flex">
                <input readonly value="https://rakibstore.sellsull.com/steadfast/status-update" class="flex-grow h-12 px-5 bg-slate-50 border border-slate-200 border-r-0 rounded-l-xl font-semibold text-slate-400 text-xs">
                <button @click="copyToClipboard('https://rakibstore.sellsull.com/steadfast/status-update')" class="w-12 h-12 flex items-center justify-center bg-white border border-slate-200 rounded-r-xl text-slate-400 hover:text-indigo-600 transition-colors">
                  <Copy class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Upcoming Courier Card -->
        <div class="bg-white rounded-[24px] border border-slate-100 border-dashed border-2 p-12 flex flex-col items-center justify-center text-center space-y-4 group transition-all hover:bg-slate-50/50">
          <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
            <MapPin class="w-7 h-7 text-indigo-500" />
          </div>
          <div>
            <h3 class="text-lg font-black text-indigo-600">Upcoming Courier Integrations</h3>
            <p class="text-xs font-semibold text-slate-400 max-w-[280px] mt-2 leading-relaxed">
              Soon vendors will be able to integrate with more courier partners like <strong class="text-slate-600">Paperfly</strong>, <strong class="text-slate-600">Tiger Delivery</strong>, and many others.
            </p>
          </div>
        </div>
      </div>

      <!-- Footer Section -->
      <div class="space-y-6">
        <div class="grid grid-cols-1 gap-6">
          <div class="bg-[#F8FAFC] rounded-[24px] border border-slate-100 p-8 flex items-center justify-between">
            <div>
              <h3 class="text-lg font-black text-[#1E293B]">Personal Delivery (OWN)</h3>
              <p class="text-sm font-semibold text-slate-400 mt-1">Manage orders with your own delivery team.</p>
            </div>
            <button 
              @click="form.personal.active = !form.personal.active"
              :class="['w-14 h-7 rounded-full transition-all duration-300 relative', form.personal.active ? 'bg-[#334155]' : 'bg-slate-200']"
            >
              <div :class="['w-5 h-5 bg-white rounded-full absolute top-1 transition-all duration-300 shadow-sm', form.personal.active ? 'left-8' : 'left-1']"></div>
              <span class="absolute -right-14 top-1/2 -translate-y-1/2 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                {{ form.personal.active ? 'Active' : 'Inactive' }}
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  ChevronLeft, 
  Copy, 
  Eye,
  EyeOff, 
  MapPin,
  Save
} from 'lucide-vue-next'
import { reactive, ref, onMounted } from 'vue'

const { $toast } = useNuxtApp()
const { getAll, createItem } = useCrud()

const showRedxPass = ref(false)
const showPathaoSecret = ref(false)
const showPathaoPass = ref(false)
const showSteadfastSecret = ref(false)

const form = reactive({
  redx: { active: false, storeId: '', password: '' },
  pathao: { active: false, storeId: '', clientId: '', clientSecret: '', email: '', password: '' },
  steadfast: { active: false, apiKey: '', secretKey: '' },
  personal: { active: true }
})

const pending = ref(true)
const saving = ref(false)

const loadSettings = async () => {
  try {
    pending.value = true
    const response = await getAll('/vendor/delivery')
    if (response.data) {
      const data = response.data
      if (data.redx) Object.assign(form.redx, typeof data.redx === 'string' ? JSON.parse(data.redx) : data.redx)
      if (data.pathao) Object.assign(form.pathao, typeof data.pathao === 'string' ? JSON.parse(data.pathao) : data.pathao)
      if (data.steadfast) Object.assign(form.steadfast, typeof data.steadfast === 'string' ? JSON.parse(data.steadfast) : data.steadfast)
      if (data.personal) Object.assign(form.personal, typeof data.personal === 'string' ? JSON.parse(data.personal) : data.personal)
    }
  } catch (error) {
    if (error.response?.status !== 404) {
      $toast.error('Failed to load delivery settings')
    }
  } finally {
    pending.value = false
  }
}

const saveSettings = async () => {
  try {
    saving.value = true
    await createItem('/vendor/delivery', form, null, false)
    $toast.success('Settings saved successfully')
  } catch (error) {
    console.error(error)
    $toast.error('Failed to save settings')
  } finally {
    saving.value = false
  }
}

const copyToClipboard = (text) => {
  navigator.clipboard.writeText(text)
  $toast.success('Webhook URL copied to clipboard')
}

onMounted(() => {
  loadSettings()
})
</script>

<style scoped>
/* Any additional custom styling */
</style>
