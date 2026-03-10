<template>
  <div class="p-6 lg:p-12 bg-slate-50 dark:bg-[#0a0c10] min-h-screen transition-colors duration-500">
    <!-- Sophisticated Header -->
    <div class="max-w-5xl mx-auto mb-12">
      <div class="flex items-center gap-2 text-[11px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-[0.2em] mb-4">
        <NuxtLink to="/vendor/managed-shop" class="hover:text-indigo-600 transition-colors">Managed Shop</NuxtLink>
        <div class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-700"></div>
        <span class="text-slate-900 dark:text-white">SMS Communications</span>
      </div>
      
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-2">
          <h1 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight">Communications</h1>
          <p class="text-slate-500 dark:text-slate-400 font-medium max-w-xl leading-relaxed text-sm">
            Personalize your customer's journey with automated, human-friendly SMS messages. Define how your shop speaks to your audience.
          </p>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="pending" class="flex justify-center items-center py-32">
      <div class="w-10 h-10 border-[3px] border-indigo-600 dark:border-indigo-400 border-t-transparent rounded-full animate-spin"></div>
    </div>
    
    <div v-else class="max-w-5xl mx-auto space-y-12">
      <!-- Modern Tab Switcher -->
      <div class="inline-flex p-1 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800/60 rounded-2xl shadow-sm mb-4">
        <button 
          v-for="tab in tabs" 
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'px-8 py-3 text-xs font-black rounded-xl transition-all duration-500 flex items-center gap-2.5',
            activeTab === tab.id 
              ? 'bg-slate-950 dark:bg-indigo-600 text-white shadow-xl shadow-slate-950/10' 
              : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
          ]"
        >
          <component :is="tab.icon" class="w-4 h-4" />
          {{ tab.label }}
        </button>
      </div>

      <!-- Templates Section -->
      <div class="grid grid-cols-1 gap-10">
        <TransitionGroup name="fade-slide">
          <div 
            v-for="type in filteredTemplates" 
            :key="type.key" 
            class="group bg-white dark:bg-slate-900 rounded-[32px] border border-slate-200/60 dark:border-slate-800/60 shadow-sm overflow-hidden transition-all duration-500 hover:shadow-2xl hover:shadow-slate-200/50 dark:hover:shadow-indigo-500/5"
          >
            <!-- Card Header -->
            <div class="p-8 border-b border-slate-50 dark:border-slate-800/50 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-slate-50/30 dark:bg-slate-800/10">
              <div class="flex items-start gap-5">
                <div :class="[
                  'w-14 h-14 rounded-2xl flex items-center justify-center transition-all duration-500 group-hover:rotate-3 shadow-sm',
                  activeTab === 'auth' ? 'bg-indigo-600 text-white' : 'bg-emerald-600 text-white'
                ]">
                  <component :is="type.icon" class="w-7 h-7" />
                </div>
                <div class="space-y-0.5">
                  <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">{{ type.label }}</h3>
                  <p class="text-xs font-bold text-slate-400 tracking-wide uppercase">{{ type.description }}</p>
                </div>
              </div>
              
              <div class="flex items-center gap-4">
                <div class="flex items-center gap-3 px-4 py-2 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
                  <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active</span>
                  <button 
                    @click="toggleActive(type.key)"
                    :class="[
                      'w-10 h-5 rounded-full transition-all duration-500 relative',
                      templates[type.key]?.is_active ? 'bg-indigo-600' : 'bg-slate-200 dark:bg-slate-700'
                    ]"
                  >
                    <div :class="[
                      'w-3.5 h-3.5 bg-white rounded-full absolute top-0.75 transition-all duration-500 shadow-sm',
                      templates[type.key]?.is_active ? 'left-5.5' : 'left-1'
                    ]"></div>
                  </button>
                </div>
                
                <button 
                  @click="saveTemplate(type.key)" 
                  :disabled="saving[type.key]"
                  class="h-11 px-6 bg-slate-950 dark:bg-indigo-600 text-white text-[12px] font-black rounded-xl hover:scale-105 active:scale-95 transition-all duration-500 disabled:opacity-50 flex items-center gap-2 shadow-lg shadow-slate-950/10 dark:shadow-indigo-500/10"
                >
                  <Save v-if="!saving[type.key]" class="w-4 h-4" />
                  <div v-else class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                  {{ saving[type.key] ? 'Updating...' : 'Save Changes' }}
                </button>

                <button 
                  @click="openTestModal(type.key)" 
                  class="h-11 px-6 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-[12px] font-black rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-500 flex items-center gap-2 shadow-sm"
                >
                  <Send class="w-4 h-4 text-indigo-500" />
                  <span>Test SMS</span>
                </button>
              </div>
            </div>

            <!-- Card Body -->
            <div class="p-8 space-y-8">
              <!-- Tags Helper -->
              <div class="space-y-3">
                <div class="flex items-center justify-between">
                  <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.1em]">Dynamic Tokens</label>
                  <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold text-slate-400">Click to insert at cursor</span>
                    <HelpCircle class="w-3 h-3 text-slate-300 pointer-events-none" />
                  </div>
                </div>
                <div class="flex flex-wrap gap-2.5">
                  <button 
                    v-for="tag in type.tags" 
                    :key="tag" 
                    @click="insertTag(type.key, tag)" 
                    class="px-3 py-1.5 bg-white dark:bg-slate-800 text-[11px] font-bold text-slate-600 dark:text-slate-300 rounded-lg border border-slate-100 dark:border-slate-700 hover:border-indigo-500 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50/30 dark:hover:bg-indigo-500/10 transition-all duration-300 flex items-center gap-1.5"
                  >
                    <div class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-600 group-hover:bg-indigo-400"></div>
                    {{ tag }}
                  </button>
                </div>
              </div>

              <!-- Editor -->
              <div class="relative group/editor">
                <textarea 
                  v-model="templates[type.key].content"
                  :ref="el => textAreaRefs[type.key] = el"
                  class="w-full min-h-[140px] p-8 bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 rounded-[24px] focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500/30 focus:bg-white dark:focus:bg-slate-800 outline-none transition-all duration-500 font-medium text-slate-800 dark:text-slate-100 leading-relaxed text-base placeholder:text-slate-400"
                  placeholder="Tell your customers something great..."
                ></textarea>
                
                <!-- Character & Reset Overlay -->
                <div class="absolute bottom-4 right-4 flex items-center gap-3">
                  <div class="flex items-center gap-3 px-3 py-1.5 bg-white dark:bg-slate-900 rounded-lg border border-slate-50 dark:border-slate-800 shadow-sm text-[10px] font-black text-slate-400">
                    <span class="flex items-center gap-1">
                      <Hash class="w-3 h-3" /> {{ getCharCount(type.key) }}
                    </span>
                    <div class="w-px h-3 bg-slate-100 dark:bg-slate-800"></div>
                    <span class="text-indigo-600 dark:text-indigo-400 uppercase">{{ Math.ceil(getCharCount(type.key) / 160) || 1 }} Part</span>
                  </div>
                  <button 
                    @click="resetToDefault(type.key)"
                    class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg transition-all duration-300"
                    title="Reset to default"
                  >
                    <RotateCcw class="w-4 h-4" />
                  </button>
                </div>
              </div>

              <!-- Live Preview Section -->
              <div class="pt-8 border-t border-slate-50 dark:border-slate-800 flex items-center gap-6">
                <div class="w-24 h-24 flex-shrink-0 bg-slate-100 dark:bg-slate-800/50 rounded-2xl flex items-center justify-center relative overflow-hidden group/preview">
                  <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 to-transparent opacity-0 group-hover/preview:opacity-100 transition-opacity duration-500"></div>
                  <Smartphone class="w-10 h-10 text-slate-300 dark:text-slate-600 relative z-10" />
                </div>
                <div class="flex-grow space-y-2">
                  <div class="flex items-center gap-2">
                    <div class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></div>
                    <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">Live Experience Preview</span>
                  </div>
                  <div class="bg-indigo-50/50 dark:bg-indigo-500/5 p-5 rounded-[20px] relative">
                    <div class="absolute left-0 top-1/2 -translate-x-1.5 -translate-y-1/2 w-3 h-3 rotate-45 bg-indigo-50/50 dark:bg-indigo-500/5"></div>
                    <p class="text-sm font-semibold text-slate-600 dark:text-slate-300 leading-relaxed italic">
                      "{{ renderPreview(type.key) }}"
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </TransitionGroup>
      </div>
    </div>

    <!-- Test Modal -->
    <div v-if="testModal.show" class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-slate-950/40 backdrop-blur-sm">
      <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-[32px] border border-slate-200 dark:border-slate-800 shadow-2xl overflow-hidden p-8 space-y-6">
        <div class="space-y-2">
          <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Test SMS Template</h3>
          <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Enter a phone number to receive a test message for <span class="text-indigo-600">{{ testModal.type.toUpperCase().replace('_', ' ') }}</span></p>
        </div>

        <div class="space-y-4">
          <div class="relative group">
            <Smartphone class="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors" />
            <input 
              v-model="testModal.phone" 
              type="text" 
              placeholder="01XXXXXXXXX" 
              class="w-full h-14 pl-14 pr-6 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200"
            />
          </div>

          <div class="flex items-center gap-4 pt-2">
            <button 
              @click="testModal.show = false" 
              class="flex-1 h-12 text-sm font-black text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl transition-all"
            >
              Cancel
            </button>
            <button 
              @click="sendTestSms" 
              :disabled="testModal.sending || !testModal.phone"
              class="flex-2 h-12 px-8 bg-indigo-600 text-white text-sm font-black rounded-xl hover:bg-indigo-700 active:scale-95 transition-all disabled:opacity-50 flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/20"
            >
              <div v-if="testModal.sending" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
              <Send v-else class="w-4 h-4" />
              <span>{{ testModal.sending ? 'Sending...' : 'Send Test' }}</span>
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
  MessageSquare, 
  Smartphone,
  ShoppingBag,
  Truck,
  Key,
  RefreshCcw,
  ShieldCheck,
  Save,
  RotateCcw,
  Hash,
  Eye,
  HelpCircle,
  Send
} from 'lucide-vue-next'
import { ref, reactive, onMounted, computed } from 'vue'

const { $toast } = useNuxtApp()
const { getAll, createItem } = useCrud()

const pending = ref(true)
const activeTab = ref('auth')
const saving = reactive({})
const textAreaRefs = reactive({})
const testModal = reactive({
  show: false,
  type: '',
  phone: '',
  sending: false
})

const tabs = [
  { id: 'auth', label: 'Security & Access', icon: ShieldCheck },
  { id: 'orders', label: 'Order Updates', icon: ShoppingBag }
]

const templateTypes = [
  { 
    key: 'otp', 
    tab: 'auth',
    label: 'Verification Code', 
    description: 'The first point of contact for new members.',
    icon: Smartphone,
    tags: ['{{ otp }}', '{{ shop_name }}'],
    default: 'Welcome! Your {{ shop_name }} verification code is: {{ otp }}. Please do not share this with anyone.'
  },
  { 
    key: 'password_reset', 
    tab: 'auth',
    label: 'Account Recovery', 
    description: 'Help users regain access to their account safely.',
    icon: Key,
    tags: ['{{ reset_code }}', '{{ shop_name }}'],
    default: 'We received a request to reset your password for {{ shop_name }}. Your recovery code is: {{ reset_code }}'
  },
  { 
    key: 'order_placed', 
    tab: 'orders',
    label: 'Purchase Receipt', 
    description: 'Reassure customers their order is being processed.',
    icon: ShoppingBag,
    tags: ['{{ order_id }}', '{{ amount }}', '{{ shop_name }}'],
    default: 'Great news! Your order {{ order_id }} for {{ amount }} has been received by {{ shop_name }}. We are preparing it now!'
  },
  { 
    key: 'order_shipped', 
    tab: 'orders',
    label: 'Shipping Update', 
    description: 'Build excitement as the package starts its journey.',
    icon: Truck,
    tags: ['{{ order_id }}', '{{ courier }}', '{{ tracking_id }}'],
    default: 'Your {{ shop_name }} order {{ order_id }} is on its way! Shipped via {{ courier }}. Track here: {{ tracking_id }}'
  },
  { 
    key: 'order_status_change', 
    tab: 'orders',
    label: 'Progress Update', 
    description: 'Keep the customer informed at every step.',
    icon: RefreshCcw,
    tags: ['{{ order_id }}', '{{ status }}', '{{ shop_name }}'],
    default: 'Hi! Just an update: Your order {{ order_id }} is now {{ status }}. Thank you for shopping with {{ shop_name }}!'
  }
]

const templates = reactive({})

// Initialize templates with defaults
templateTypes.forEach(type => {
  templates[type.key] = {
    type: type.key,
    content: type.default,
    is_active: true
  }
  saving[type.key] = false
})

const filteredTemplates = computed(() => {
  return templateTypes.filter(t => t.tab === activeTab.value)
})

const getCharCount = (key) => templates[key]?.content?.length || 0

const renderPreview = (key) => {
  let content = templates[key]?.content || ''
  const mockData = {
    '{{ otp }}': '429011',
    '{{ reset_code }}': 'REC-88',
    '{{ order_id }}': '#99210',
    '{{ amount }}': '৳2,450',
    '{{ shop_name }}': 'Elevate Shop',
    '{{ courier }}': 'Pathao',
    '{{ tracking_id }}': 'TH9081273',
    '{{ status }}': 'Processing'
  }
  
  Object.keys(mockData).forEach(tag => {
    content = content.replaceAll(tag, mockData[tag])
  })
  
  return content
}

const toggleActive = (key) => {
  templates[key].is_active = !templates[key].is_active
}

const resetToDefault = (key) => {
  const type = templateTypes.find(t => t.key === key)
  if (type) {
    templates[key].content = type.default
    $toast.info('Template reset to original design')
  }
}

const insertTag = (key, tag) => {
  const textarea = textAreaRefs[key]
  if (!textarea) {
    templates[key].content += tag
    return
  }

  const start = textarea.selectionStart
  const end = textarea.selectionEnd
  const content = templates[key].content
  
  templates[key].content = content.substring(0, start) + tag + content.substring(end)
  
  setTimeout(() => {
    textarea.focus()
    const newCursorPos = start + tag.length
    textarea.setSelectionRange(newCursorPos, newCursorPos)
  }, 0)
}

const loadTemplates = async () => {
  try {
    pending.value = true
    const response = await getAll('/vendor/sms-templates')
    if (response.data && response.data.length > 0) {
      response.data.forEach(item => {
        if (templates[item.type]) {
          templates[item.type] = {
            id: item.id,
            type: item.type,
            content: item.content,
            is_active: !!item.is_active
          }
        }
      })
    }
  } catch (error) {
    console.error(error)
    $toast.error('Unable to fetch templates')
  } finally {
    pending.value = false
  }
}

const saveTemplate = async (key) => {
  try {
    saving[key] = true
    await createItem('/vendor/sms-templates', templates[key])
    $toast.success(`${templates[key].type.toUpperCase().replace('_', ' ')} updated successfully`)
  } catch (error) {
    console.error(error)
    $toast.error('Something went wrong')
  } finally {
    saving[key] = false
  }
}

const openTestModal = (type) => {
  testModal.type = type
  testModal.show = true
}

const sendTestSms = async () => {
  try {
    testModal.sending = true
    await createItem('/vendor/sms-templates/test', {
      phone: testModal.phone,
      type: testModal.type,
      content: templates[testModal.type]?.content
    })
    navigateTo('/vendor/managed-shop/sms-templates')
    testModal.show = false
  } catch (error) {
    console.error(error)
    $toast.error(error.response?.data?.message || 'Failed to send test SMS')
  } finally {
    testModal.sending = false
  }
}

onMounted(() => {
  loadTemplates()
})

definePageMeta({
  middleware: 'auth',
  permissions: 'settings.view'
})
</script>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}
.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(20px);
}
.fade-slide-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>
