<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen relative overflow-hidden transition-colors duration-300">
    <div class="max-w-4xl mx-auto mb-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 font-medium mb-4">
          <NuxtLink to="/vendor/managed-shop" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Manage Shop</NuxtLink>
          <ChevronRight class="w-4 h-4" />
          <span class="text-slate-900 dark:text-white font-bold">Payment Gateway</span>
        </div>
      </div>
      
      <div>
        <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Payment Gateway</h1>
        <p class="text-slate-500 dark:text-slate-400 font-semibold mt-1">Configure your payment methods to start accepting orders.</p>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="w-8 h-8 border-4 border-[#0F172A] dark:border-white border-t-transparent rounded-full animate-spin"></div>
    </div>
    
    <!-- Payment Methods Grid -->
    <div v-else class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-6">
      <div 
        v-for="(gateway, index) in gateways" 
        :key="gateway.slug"
        class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm p-8 hover:shadow-xl hover:shadow-indigo-500/5 transition-all duration-500 group relative overflow-hidden flex flex-col justify-between"
      >
        <!-- Background Pattern -->
        <div class="absolute -right-4 -top-4 w-32 h-32 bg-slate-50 dark:bg-slate-800/50 rounded-full group-hover:bg-indigo-50 dark:group-hover:bg-indigo-900/20 transition-colors"></div>

        <div class="relative space-y-6 flex-grow">
          <div class="flex items-start justify-between">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center shadow-inner bg-indigo-600">
              <component :is="getIcon(gateway.icon)" class="w-8 h-8 text-white" />
            </div>
            <button 
              @click="gateway.active = !gateway.active"
              :class="['w-12 h-6 rounded-full transition-all duration-300 relative', gateway.active ? 'bg-emerald-500' : 'bg-slate-200 dark:bg-slate-700']"
            >
              <div :class="['w-4 h-4 bg-white rounded-full absolute top-1 transition-all duration-300', gateway.active ? 'left-7' : 'left-1']"></div>
            </button>
          </div>

          <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">{{ gateway.name }}</h2>
            <p class="text-sm font-bold text-slate-400 dark:text-slate-500 mt-1 line-clamp-2">{{ gateway.description }}</p>
          </div>
        </div>

        <div class="relative mt-6" v-if="gateway.config_fields?.length > 0 && gateway.active">
          <div class="pt-4 border-t border-slate-50 dark:border-slate-800">
            <button @click="openConfig(index)" class="w-full py-4 bg-slate-50 hover:bg-slate-100 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-black rounded-2xl transition-all flex items-center justify-center gap-2">
              <Settings2 class="w-4 h-4 text-slate-400" />
              Settings
            </button>
          </div>
        </div>
      </div>

      <!-- Add New Mock -->
      <div @click="isAddDrawerOpen = true" class="bg-slate-50 dark:bg-slate-900 rounded-[32px] border-2 border-dashed border-slate-200 dark:border-slate-700 p-8 flex flex-col items-center justify-center text-center gap-4 hover:border-indigo-200 hover:bg-white dark:hover:bg-slate-800 transition-all cursor-pointer group">
        <div class="w-16 h-16 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
          <Plus class="w-8 h-8 text-slate-300 group-hover:text-indigo-500" />
        </div>
        <div>
          <h3 class="font-black text-slate-400 group-hover:text-slate-800 dark:group-hover:text-white">Add Custom Gateway</h3>
          <p class="text-xs font-bold text-slate-300 group-hover:text-slate-400 dark:group-hover:text-slate-500">Add Manual Payments, SSLCommerz, Stripe, etc.</p>
        </div>
      </div>
    </div>

    <!-- Floating Save Button -->
    <div class="fixed bottom-8 right-8 z-40">
      <button @click="saveSettings" :disabled="saving || pending" class="px-8 py-4 bg-slate-900 dark:bg-indigo-600 hover:bg-slate-800 dark:hover:bg-indigo-700 text-white font-black rounded-2xl shadow-2xl flex items-center gap-3 transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
        <Save v-if="!saving" class="w-6 h-6" />
        <span v-if="saving">Saving Changes...</span>
        <span v-else>Update Payment Gateways</span>
      </button>
    </div>

    <!-- Side Drawer -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="translate-x-0"
      leave-to-class="translate-x-full"
    >
      <div v-if="isDrawerOpen" class="fixed inset-y-0 right-0 w-[480px] bg-white dark:bg-slate-900 shadow-2xl z-50 flex flex-col border-l border-slate-100 dark:border-slate-800">
        <!-- Drawer Header -->
        <div class="p-8 flex items-center justify-between pb-4">
          <h2 class="text-xl font-black text-slate-900 dark:text-white">Configure {{ gateways[editingIndex]?.name }}</h2>
          <button 
            @click="isDrawerOpen = false"
            class="w-10 h-10 bg-black dark:bg-slate-800 rounded-xl flex items-center justify-center text-white hover:bg-slate-800 dark:hover:bg-slate-700 transition-all"
          >
            <X class="w-6 h-6" />
          </button>
        </div>

        <!-- Drawer Body -->
        <div class="flex-grow overflow-y-auto px-8 py-4 space-y-6" v-if="gateways[editingIndex]">
          <div v-for="field in gateways[editingIndex].config_fields" :key="field.name" class="space-y-2 relative">
             <label class="text-sm font-bold text-slate-600 dark:text-slate-400">{{ field.label }}</label>
             
             <!-- Select Field -->
             <select v-if="field.type === 'select'" v-model="gateways[editingIndex].config[field.name]" class="w-full h-14 px-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200 appearance-none">
                <option v-for="opt in field.options" :key="opt" :value="opt">{{ opt.toUpperCase() }}</option>
             </select>

             <!-- Password Field -->
             <div v-else-if="field.type === 'password'" class="relative">
                <input v-model="gateways[editingIndex].config[field.name]" :type="showSecret ? 'text' : 'password'" :placeholder="field.placeholder" class="w-full h-14 px-5 pr-12 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200">
                <button @click="showSecret = !showSecret" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                  <EyeOff v-if="showSecret" class="w-4 h-4" />
                  <Eye v-else class="w-4 h-4" />
                </button>
             </div>

             <!-- Textarea Field -->
             <textarea v-else-if="field.type === 'textarea'" v-model="gateways[editingIndex].config[field.name]" :placeholder="field.placeholder" rows="4" class="w-full p-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200 resize-none"></textarea>

             <!-- Text Field -->
             <input v-else v-model="gateways[editingIndex].config[field.name]" type="text" :placeholder="field.placeholder" class="w-full h-14 px-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200">
          </div>
        </div>

        <!-- Drawer Footer -->
        <div class="p-8 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3 z-10 relative">
          <button 
            v-if="gateways[editingIndex]?.is_custom"
            @click="removeCustomGateway(editingIndex); isDrawerOpen = false"
            class="px-5 py-4 text-red-500 font-bold hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all flex items-center gap-2"
          >
            <Trash2 class="w-4 h-4" /> Delete
          </button>
          <div v-else></div>
          <button 
            @click="isDrawerOpen = false"
            class="px-8 py-4 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-200 font-black rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-all active:scale-95"
          >
            Done
          </button>
        </div>
      </div>
    </Transition>

    <!-- Add Custom Gateway Drawer -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="translate-x-0"
      leave-to-class="translate-x-full"
    >
      <div v-if="isAddDrawerOpen" class="fixed inset-y-0 right-0 w-[480px] bg-white dark:bg-slate-900 shadow-2xl z-50 flex flex-col border-l border-slate-100 dark:border-slate-800">
        <div class="p-8 flex items-center justify-between pb-4">
          <h2 class="text-xl font-black text-slate-900 dark:text-white">Add Custom Gateway</h2>
          <button 
            @click="isAddDrawerOpen = false"
            class="w-10 h-10 bg-black dark:bg-slate-800 rounded-xl flex items-center justify-center text-white hover:bg-slate-800 dark:hover:bg-slate-700 transition-all"
          >
            <X class="w-6 h-6" />
          </button>
        </div>

        <div class="flex-grow overflow-y-auto px-8 py-4 space-y-6">
          <div class="space-y-2">
            <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Gateway Name</label>
            <input v-model="newGateway.name" type="text" placeholder="e.g. bKash, Dutch Bangla Bank..." class="w-full h-14 px-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200">
          </div>
          
          <div class="space-y-2">
            <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Integration Type</label>
            <select v-model="newGateway.type" class="w-full h-14 px-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200 appearance-none">
              <option v-for="type in gatewayTypes" :key="type.value" :value="type.value">{{ type.label }}</option>
            </select>
          </div>
        </div>

        <div class="p-8 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3 z-10 relative">
          <button 
            @click="addCustomGateway"
            class="px-8 py-4 bg-indigo-600 text-white font-black rounded-xl hover:bg-indigo-700 transition-all active:scale-95 w-full flex justify-center"
          >
            Add Gateway
          </button>
        </div>
      </div>
    </Transition>

    <!-- Overlay -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="isDrawerOpen || isAddDrawerOpen" @click="isDrawerOpen = false; isAddDrawerOpen = false" class="fixed inset-0 bg-slate-900/10 backdrop-blur-sm z-40"></div>
    </Transition>
  </div>
</template>

<script setup>
import { 
  ChevronRight, 
  CreditCard, 
  Settings2, 
  Plus, 
  Wallet, 
  Banknote, 
  Zap,
  Save,
  X,
  Eye,
  EyeOff,
  Globe,
  Trash2
} from 'lucide-vue-next'
import { ref, onMounted } from 'vue'

definePageMeta({
  middleware: 'auth',
  permissions: 'settings.view'
})

const { $toast } = useNuxtApp()
const { getAll, createItem } = useCrud()
const router = useRouter()

const pending = ref(true)
const saving = ref(false)
const isDrawerOpen = ref(false)
const isAddDrawerOpen = ref(false)
const editingIndex = ref(-1)
const showSecret = ref(false)

const gateways = ref([])

const newGateway = ref({ name: '', type: 'manual' })
const gatewayTypes = [
  { value: 'manual', label: 'Manual Payment' },
  { value: 'sslcommerz', label: 'SSLCommerz' },
  { value: 'stripe', label: 'Stripe' },
  { value: 'amarpay', label: 'AmarPay' },
  { value: 'bkash', label: 'bKash (API)' },
]

const getIcon = (iconName) => {
  const icons = { CreditCard, Wallet, Banknote, Zap, Globe }
  return icons[iconName] || Globe
}

const generateConfigFields = (type) => {
  switch(type) {
    case 'manual':
      return [
        { name: 'instruction', label: 'Payment Instructions', type: 'textarea', placeholder: 'e.g. Please send money to 01XXX-XXXXXX. Use Order ID as reference.' }
      ]
    case 'sslcommerz':
      return [
        { name: 'store_id', label: 'Store ID', type: 'text', placeholder: 'Store ID' },
        { name: 'store_password', label: 'Store Password', type: 'password', placeholder: 'Store Password' },
        { name: 'sandbox_mode', label: 'Sandbox Mode', type: 'select', options: ['yes', 'no'] }
      ]
    case 'stripe':
      return [
        { name: 'publishable_key', label: 'Publishable Key', type: 'text', placeholder: 'pk_...' },
        { name: 'secret_key', label: 'Secret Key', type: 'password', placeholder: 'sk_...' }
      ]
    case 'amarpay':
      return [
        { name: 'store_id', label: 'Store ID', type: 'text', placeholder: 'Store ID' },
        { name: 'signature_key', label: 'Signature Key', type: 'password', placeholder: 'Signature Key' },
        { name: 'sandbox_mode', label: 'Sandbox Mode', type: 'select', options: ['yes', 'no'] }
      ]
    case 'bkash':
      return [
        { name: 'app_key', label: 'App Key', type: 'text', placeholder: 'App Key' },
        { name: 'app_secret', label: 'App Secret', type: 'password', placeholder: 'App Secret' },
        { name: 'username', label: 'Username', type: 'text', placeholder: 'Username' },
        { name: 'password', label: 'Password', type: 'password', placeholder: 'Password' },
        { name: 'sandbox_mode', label: 'Sandbox Mode', type: 'select', options: ['yes', 'no'] }
      ]
    default:
      return []
  }
}

const addCustomGateway = () => {
  if (!newGateway.value.name) return $toast.error('Gateway Name is required')
  
  const slug = 'custom_' + newGateway.value.name.toLowerCase().replace(/[^a-z0-9]/g, '_') + '_' + Date.now()
  
  let icon = 'Banknote'
  if (['sslcommerz', 'amarpay', 'stripe', 'bkash'].includes(newGateway.value.type)) icon = 'CreditCard'
  
  gateways.value.push({
    slug: slug,
    name: newGateway.value.name,
    type: newGateway.value.type,
    icon: icon,
    description: `Custom ${gatewayTypes.find(t => t.value === newGateway.value.type)?.label || newGateway.value.type} Integration`,
    active: false,
    is_custom: true,
    config_fields: generateConfigFields(newGateway.value.type),
    config: {}
  })
  
  isAddDrawerOpen.value = false
  newGateway.value = { name: '', type: 'manual' }
  $toast.success('Gateway added. Please configure its settings.')
}

const removeCustomGateway = (index) => {
  gateways.value.splice(index, 1)
  $toast.success('Gateway removed.')
}

const loadSettings = async () => {
  try {
    pending.value = true
    
    // 1. Load active payment methods defined by admin
    const methodsResponse = await getAll('/vendor/payment-methods')
    const methods = methodsResponse || []
    
    // 2. Load vendor's specific configurations
    const configResponse = await getAll('/vendor/settings?group=payment_gateways')
    const savedConfig = configResponse.data || {}

    const adminSlugs = methods.map(m => m.slug)
    
    const combinedGateways = methods.map(method => ({
      ...method,
      active: savedConfig[method.slug]?.active ?? false,
      config: savedConfig[method.slug]?.config ?? {},
      is_custom: false
    }))

    // 3. Load vendor's custom gateways from payload
    Object.keys(savedConfig).forEach(key => {
        if (!adminSlugs.includes(key)) {
            const data = savedConfig[key]
            if (data.custom_meta) {
              combinedGateways.push({
                  ...data.custom_meta,
                  slug: key,
                  active: data.active,
                  config: data.config,
                  is_custom: true
              })
            }
        }
    })

    gateways.value = combinedGateways

  } catch (error) {
    console.error(error)
    $toast.error('Failed to load payment gateways')
  } finally {
    pending.value = false
  }
}

const openConfig = (index) => {
  editingIndex.value = index
  isDrawerOpen.value = true
}

const saveSettings = async () => {
  try {
    saving.value = true
    const settingsPayload = {}
    gateways.value.forEach(gateway => {
      settingsPayload[gateway.slug] = {
        active: gateway.active,
        config: gateway.config
      }
      
      // Save full custom gateway info to settings
      if (gateway.is_custom) {
        settingsPayload[gateway.slug].custom_meta = {
          name: gateway.name,
          type: gateway.type,
          icon: gateway.icon,
          description: gateway.description,
          config_fields: gateway.config_fields,
        }
      }
    })

    await createItem('/vendor/settings', {
      group: 'payment_gateways',
      settings: settingsPayload
    })
    
    $toast.success('Payment gateways updated successfully')
  } catch (error) {
    console.error(error)
    $toast.error('Failed to save settings')
  } finally {
    saving.value = false
  }
}

onMounted(loadSettings)
</script>

<style scoped>
.overflow-y-auto::-webkit-scrollbar {
  width: 4px;
}
.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}
.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
.dark .overflow-y-auto::-webkit-scrollbar-thumb {
  background: #1e293b;
}
.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #cbd5e1;
}
</style>
