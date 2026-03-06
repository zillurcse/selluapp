<template>
  <div class="p-10 bg-[#f8fafc] min-h-screen">
    <div class="max-w-[1200px] mx-auto mb-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <NuxtLink 
            to="/vendor" 
            class="w-10 h-10 bg-slate-900 rounded-full flex items-center justify-center text-white hover:bg-slate-800 transition-all active:scale-95 shadow-lg shadow-slate-900/20"
          >
            <ChevronLeft class="w-6 h-6" />
          </NuxtLink>
          <div class="flex items-center gap-2">
            <h1 class="text-2xl font-black text-slate-900 tracking-tight leading-none">AI & Automation Hub</h1>
            <span class="px-2 py-0.5 bg-indigo-600 text-white text-[9px] font-black rounded-full uppercase tracking-wider">PREMIUM</span>
          </div>
        </div>
        <button 
          @click="saveSettings" 
          :disabled="saving || pending"
          class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-xl shadow-lg shadow-indigo-200 transition-all active:scale-95 disabled:opacity-50 flex items-center gap-2"
        >
          <Save v-if="!saving" class="w-5 h-5" />
          <span>{{ saving ? 'Saving...' : 'Deploy Automation' }}</span>
        </button>
      </div>
    </div>
    
    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
    </div>
    
    <div v-else class="max-w-[1200px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
      <div class="lg:col-span-8 space-y-8">
        
        <!-- Gemini Credentials -->
        <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm p-8 space-y-8">
          <div class="flex items-center gap-4 border-b border-slate-50 pb-6">
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center">
              <Sparkles class="w-6 h-6" />
            </div>
            <div>
              <h2 class="text-xl font-black text-slate-900">AI Engine (Gemini)</h2>
              <p class="text-sm font-bold text-slate-500">Power your automation with Google Gemini AI</p>
            </div>
          </div>
          
          <div class="space-y-6">
            <div class="space-y-2">
              <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Gemini API Key</label>
              <div class="relative">
                <input 
                  v-model="form.gemini_api_key" 
                  type="password" 
                  class="w-full h-14 px-6 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700"
                  placeholder="Paste your API key here..."
                />
                <Key class="w-5 h-5 text-slate-300 absolute right-4 top-1/2 -translate-y-1/2" />
              </div>
              <p class="text-[10px] text-slate-400 font-bold ml-1">Get your key from <a href="https://aistudio.google.com/" target="_blank" class="text-indigo-600 hover:underline">Google AI Studio</a></p>
            </div>
          </div>
        </div>

        <!-- Facebook Messenger Credentials -->
        <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm p-8 space-y-8">
          <div class="flex items-center gap-4 border-b border-slate-50 pb-6">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
              <Facebook class="w-6 h-6" />
            </div>
            <div>
              <h2 class="text-xl font-black text-slate-900">Facebook Messenger</h2>
              <p class="text-sm font-bold text-slate-500">Connect your Facebook Page for AI messaging</p>
            </div>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Page ID</label>
              <input 
                v-model="form.fb_page_id" 
                type="text" 
                class="w-full h-14 px-6 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-semibold text-slate-700"
                placeholder="Your FB Page ID"
              />
            </div>
            <div class="space-y-2">
              <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Verify Token</label>
              <input 
                v-model="form.fb_verify_token" 
                type="text" 
                class="w-full h-14 px-6 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-semibold text-slate-700"
                placeholder="random_string_for_webhook"
              />
            </div>
            <div class="space-y-2 md:col-span-2">
              <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Page Access Token</label>
              <div class="relative">
                <input 
                  v-model="form.fb_access_token" 
                  type="password" 
                  class="w-full h-14 px-6 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-semibold text-slate-700"
                  placeholder="Paste Page Access Token here..."
                />
                <Key class="w-5 h-5 text-slate-300 absolute right-4 top-1/2 -translate-y-1/2" />
              </div>
            </div>
          </div>
        </div>

        <!-- Webhook Configuration -->
        <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm p-8 space-y-8">
          <div class="flex items-center gap-4 border-b border-slate-50 pb-6">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center">
              <Zap class="w-6 h-6" />
            </div>
            <div>
              <h2 class="text-xl font-black text-slate-900">n8n Workflows</h2>
              <p class="text-sm font-bold text-slate-500">Connect your eCommerce events to n8n webhooks</p>
            </div>
          </div>
          
          <div class="space-y-8">
             <div v-for="flow in flows" :key="flow.id" class="p-6 bg-slate-50 rounded-2xl border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                   <div class="flex items-center gap-3">
                      <div :class="['w-10 h-10 rounded-xl flex items-center justify-center', flow.bg]">
                         <component :is="flow.icon" :class="['w-5 h-5', flow.color]" />
                      </div>
                      <div>
                         <h3 class="font-black text-slate-800">{{ flow.name }}</h3>
                         <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ flow.event }}</p>
                      </div>
                   </div>
                   <button 
                    @click="form.enabled_flows[flow.event] = !form.enabled_flows[flow.event]"
                    :class="['w-12 h-7 rounded-full transition-all duration-300 relative', form.enabled_flows[flow.event] ? 'bg-indigo-600' : 'bg-slate-200']"
                  >
                    <div :class="['w-5 h-5 bg-white rounded-full absolute top-1 transition-all duration-300 shadow-sm', form.enabled_flows[flow.event] ? 'left-6' : 'left-1']"></div>
                  </button>
                </div>
                <div class="space-y-2" v-if="form.enabled_flows[flow.event]">
                   <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider ml-1">n8n Webhook URL</label>
                   <input 
                    v-model="form.webhooks[flow.event]" 
                    type="text" 
                    class="w-full h-12 px-4 bg-white border border-slate-200 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 text-sm"
                    placeholder="https://n8n.your-instance.com/webhook/..."
                  />
                </div>
             </div>
          </div>
        </div>
      </div>
      
      <!-- Instructions Sidebar -->
      <div class="lg:col-span-4 space-y-6">
        <div class="bg-gradient-to-br from-indigo-900 to-slate-900 rounded-[32px] p-8 shadow-xl text-white relative overflow-hidden">
          <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-500/20 rounded-full blur-3xl"></div>
          
          <h3 class="text-lg font-black mb-6 flex items-center gap-2">
            <Activity class="w-5 h-5 text-indigo-400" />
            Automation Health
          </h3>
          
          <div class="space-y-6">
             <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/10">
                <span class="text-sm font-bold text-indigo-200">System Status</span>
                <span class="flex items-center gap-2 text-emerald-400 font-black text-xs">
                   <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                   Active
                </span>
             </div>
             
             <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-white/5 rounded-2xl border border-white/10">
                   <p class="text-[10px] font-black uppercase tracking-widest text-indigo-300 mb-1">Executions</p>
                   <span class="text-xl font-black">2.4k</span>
                </div>
                <div class="p-4 bg-white/5 rounded-2xl border border-white/10">
                   <p class="text-[10px] font-black uppercase tracking-widest text-indigo-300 mb-1">Success Rate</p>
                   <span class="text-xl font-black text-indigo-400">99.2%</span>
                </div>
             </div>
          </div>
        </div>
        
        <div class="bg-indigo-50 rounded-[32px] border border-indigo-100 p-8">
           <div class="flex items-center gap-3 mb-4">
              <ShieldCheck class="w-6 h-6 text-indigo-600" />
              <h3 class="font-black text-indigo-900">Expert Mode</h3>
           </div>
           <p class="text-sm font-semibold text-indigo-800/70 leading-relaxed mb-4">
             AI Automation allows you to scale your business without increasing staff. 
           </p>
           <ul class="text-[11px] font-bold text-indigo-900/60 space-y-2">
              <li class="flex items-center gap-2">
                 <div class="w-1 h-1 bg-indigo-400 rounded-full"></div>
                 Real-time Sentiment Analysis
              </li>
              <li class="flex items-center gap-2">
                 <div class="w-1 h-1 bg-indigo-400 rounded-full"></div>
                 Automatic Ticket Escalation
              </li>
              <li class="flex items-center gap-2">
                 <div class="w-1 h-1 bg-indigo-400 rounded-full"></div>
                 Multi-layer Fraud Detection
              </li>
           </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  ChevronLeft, Save, Sparkles, Key, Zap, 
  ShieldAlert, Mail, MessageSquare, Heart, Activity, ShieldCheck, Facebook 
} from 'lucide-vue-next'
import { ref, reactive, onMounted } from 'vue'

definePageMeta({
  middleware: 'auth',
  permissions: 'settings.manage'
})

const { $toast } = useNuxtApp()
const { getAll, createItem } = useCrud()
const router = useRouter()

const pending = ref(true)
const saving = ref(false)

const flows = [
  { id: 1, name: 'AI Fraud Detection', event: 'order_placed', icon: ShieldAlert, color: 'text-amber-600', bg: 'bg-amber-50' },
  { id: 2, name: 'Smart Recommendations', event: 'order_shipped', icon: Heart, icon: Mail, color: 'text-indigo-600', bg: 'bg-indigo-50' },
  { id: 3, name: 'Sentiment & Tickets', event: 'review_submitted', icon: MessageSquare, color: 'text-emerald-600', bg: 'bg-emerald-50' },
  { id: 4, name: 'Customer Messages AI', event: 'message_received', icon: Zap, color: 'text-purple-600', bg: 'bg-purple-50' },
  { id: 5, name: 'Facebook Message AI', event: 'fb_message_received', icon: Facebook, color: 'text-blue-600', bg: 'bg-blue-50' },
]

const form = reactive({
  gemini_api_key: '',
  enabled_flows: {
    order_placed: true,
    order_shipped: false,
    review_submitted: true,
    message_received: true,
    fb_message_received: true,
  },
  fb_page_id: '',
  fb_verify_token: '',
  fb_access_token: '',
  webhooks: {
    order_placed: '',
    order_shipped: '',
    review_submitted: '',
    message_received: '',
    fb_message_received: '',
  }
})

const loadSettings = async () => {
  try {
    pending.value = true
    const response = await getAll('/vendor/settings?group=ai_automation')
    if (response?.data) {
       const settings = response.data
       if (settings.gemini_api_key) form.gemini_api_key = settings.gemini_api_key
       if (settings.enabled_flows) form.enabled_flows = { ...form.enabled_flows, ...settings.enabled_flows }
       if (settings.webhooks) form.webhooks = { ...form.webhooks, ...settings.webhooks }
    }
  } catch (error) {
    console.error('Failed to load settings', error)
  } finally {
    pending.value = false
  }
}

const saveSettings = async () => {
  try {
    saving.value = true
    await createItem('/vendor/settings', {
      group: 'ai_automation',
      settings: form
    })
    $toast.success('AI Automation updated successfully')
  } catch (error) {
    $toast.error('Failed to save settings')
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  loadSettings()
})
</script>
