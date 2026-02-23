<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <div class="max-w-4xl mx-auto mb-8">
      <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 font-medium mb-4">
        <NuxtLink to="/vendor/managed-shop" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Manage Shop</NuxtLink>
        <ChevronRight class="w-4 h-4" />
        <span class="text-slate-900 dark:text-white font-bold">Spider Intelligence</span>
      </div>
      
      <div class="flex items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 mb-1">
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Spider Intelligence</h1>
            <span class="px-3 py-1 bg-gradient-to-r from-orange-500 to-rose-600 text-white text-[10px] font-black rounded-full uppercase tracking-wider shadow-lg shadow-rose-200">PRO AI</span>
          </div>
          <p class="text-slate-500 dark:text-slate-400 font-semibold">Leverage AI to detect patterns and protect your store from automated fraud.</p>
        </div>
        <button @click="saveSettings" :disabled="saving || pending" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white font-black rounded-2xl shadow-lg shadow-indigo-200 dark:shadow-indigo-900/20 transition-all active:scale-95 flex items-center gap-2 disabled:opacity-50">
          <Save v-if="!saving" class="w-5 h-5" />
          <span>{{ saving ? 'Saving...' : 'Save Changes' }}</span>
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="w-8 h-8 border-4 border-indigo-600 dark:border-indigo-400 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <!-- AI Insights Dashboard -->
    <div v-else class="max-w-4xl mx-auto space-y-6">
      <div class="bg-slate-900 rounded-[40px] p-12 relative overflow-hidden group">
        <!-- Abstract AI Background -->
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_120%,#4f46e5,transparent)] opacity-40"></div>
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-indigo-600/20 rounded-full blur-[100px] group-hover:bg-indigo-500/30 transition-colors duration-1000"></div>

        <div class="relative z-10 flex flex-col items-center text-center gap-8">
          <div :class="['w-24 h-24 backdrop-blur-2xl rounded-3xl flex items-center justify-center border border-white/20 shadow-2xl transition-all', isActive ? 'bg-white/10 animate-pulse' : 'bg-slate-800 opacity-50']">
            <Bug :class="['w-12 h-12', isActive ? 'text-indigo-400' : 'text-slate-500']" />
          </div>
          <div class="space-y-4 max-w-lg">
            <h2 class="text-3xl font-black text-white tracking-widest uppercase">
              {{ isActive ? 'Spider Core Activated' : 'Spider Core Deactivated' }}
            </h2>
            <p class="text-indigo-100/70 font-semibold leading-relaxed">
              {{ isActive ? 'Our advanced neural network is constantly monitoring traffic patterns to identify bot nets and fraudulent purchasing cycles.' : 'AI protection is currently turned off. Turn it on to protect your store from malicious bots and fraud.' }}
            </p>
          </div>
          
          <button 
            @click="isActive = !isActive"
            :class="['w-16 h-8 rounded-full transition-all duration-300 relative', isActive ? 'bg-indigo-600' : 'bg-slate-600']"
          >
            <div :class="['w-6 h-6 bg-white rounded-full absolute top-1 transition-all duration-300 shadow-sm', isActive ? 'left-9' : 'left-1']"></div>
          </button>

          <div v-if="isActive" class="grid grid-cols-3 gap-12 w-full pt-8 border-t border-white/10">
            <div class="text-center">
              <span class="block text-2xl font-black text-white">99.8%</span>
              <span class="text-[10px] uppercase font-black tracking-widest text-indigo-400">Accuracy</span>
            </div>
            <div class="text-center">
              <span class="block text-2xl font-black text-indigo-400">0ms</span>
              <span class="text-[10px] uppercase font-black tracking-widest text-white/50">Latency</span>
            </div>
            <div class="text-center">
              <span class="block text-2xl font-black text-white">{{ blockedCount }}</span>
              <span class="text-[10px] uppercase font-black tracking-widest text-indigo-400">Blocked</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Settings -->
      <div v-if="isActive" class="bg-white dark:bg-slate-900 rounded-[40px] border border-slate-100 dark:border-slate-800 shadow-sm p-10 space-y-8 transition-colors duration-300">
        <div class="flex items-center justify-between">
          <h3 class="text-xl font-black text-slate-800 dark:text-white tracking-tight">AI Protection Sensitivity</h3>
          <div class="flex items-center gap-3">
            <span :class="['text-sm font-bold', dynamicScaling ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400 dark:text-slate-500']">
              Dynamic Scaling {{ dynamicScaling ? 'Enabled' : 'Disabled' }}
            </span>
            <button 
              @click="dynamicScaling = !dynamicScaling"
              :class="['w-10 h-5 rounded-full transition-all duration-300 relative', dynamicScaling ? 'bg-indigo-500' : 'bg-slate-200 dark:bg-slate-700']"
            >
              <div :class="['w-3 h-3 bg-white rounded-full absolute top-1 transition-all duration-300 shadow-sm', dynamicScaling ? 'left-6' : 'left-1']"></div>
            </button>
          </div>
        </div>
        
        <div class="space-y-12 py-4">
          <div class="relative h-4 bg-slate-50 dark:bg-slate-800 rounded-full border border-slate-100 dark:border-slate-700 flex items-center">
            <div :style="{ width: sensitivity + '%' }" class="absolute inset-y-0 left-0 bg-gradient-to-r from-indigo-500 to-rose-500 rounded-full shadow-lg shadow-indigo-200 dark:shadow-indigo-900/40 pointer-events-none transition-all duration-75"></div>
            <input v-model="sensitivity" type="range" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" min="0" max="100">
          </div>
          <div class="flex justify-between text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest px-2">
            <span>Minimum (Passive)</span>
            <span class="text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 px-3 py-1 rounded-full text-center min-w-[120px]">
              {{ sensitivityLabel }}
            </span>
            <span>Aggressive (Maximum)</span>
          </div>
        </div>

        <p class="text-xs text-slate-400 dark:text-slate-500 font-bold leading-relaxed bg-slate-50 dark:bg-slate-800/50 p-6 rounded-3xl border border-dashed border-slate-200 dark:border-slate-700">
          Note: Higher sensitivity might occasionally flag legitimate customers using VPNs. We recommend the "Optimal" level for most shops.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  ChevronRight, 
  Bug,
  Save 
} from 'lucide-vue-next'
import { ref, computed, onMounted } from 'vue'

definePageMeta({
  middleware: 'auth',
  permissions: 'settings.view'
})

const { $toast } = useNuxtApp()
const { getAll, createItem } = useCrud()
const router = useRouter()

const pending = ref(true)
const saving = ref(false)

const isActive = ref(true)
const dynamicScaling = ref(true)
const sensitivity = ref(65)
const blockedCount = ref('2.4k') // Mocked blocked count

const sensitivityLabel = computed(() => {
  if (sensitivity.value < 30) return 'Low Level'
  if (sensitivity.value < 75) return 'Optimal Level'
  return 'High Level'
})

const loadSettings = async () => {
  try {
    pending.value = true
    const response = await getAll('/vendor/settings?group=spider_intelligence')
    if (response.data) {
      const data = response.data
      isActive.value = data.isActive === 'true' || data.isActive === true || data.isActive === '1'
      dynamicScaling.value = data.dynamicScaling === 'true' || data.dynamicScaling === true || data.dynamicScaling === '1'
      sensitivity.value = data.sensitivity ? parseInt(data.sensitivity) : 65
    }
  } catch (error) {
    if (error.response?.status !== 404) {
      $toast.error('Failed to load Spider Intelligence settings')
    }
  } finally {
    pending.value = false
  }
}

const saveSettings = async () => {
  try {
    saving.value = true
    await createItem('/vendor/settings', {
      group: 'spider_intelligence',
      settings: {
        isActive: isActive.value,
        dynamicScaling: dynamicScaling.value,
        sensitivity: sensitivity.value
      }
    })
    router.push('/vendor/managed-shop/spider-intelligence')
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
