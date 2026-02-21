<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300 relative overflow-hidden">
    <div class="max-w-4xl mx-auto mb-8">
      <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 font-medium mb-4">
        <NuxtLink to="/vendor/managed-shop" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Manage Shop</NuxtLink>
        <ChevronRight class="w-4 h-4" />
        <span class="text-slate-900 dark:text-white font-bold">Delivery Zones</span>
      </div>
      
      <div class="flex items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Delivery Zones</h1>
          <p class="text-slate-500 dark:text-slate-400 font-semibold mt-1">Define your shipping locations and delivery fees.</p>
        </div>
        <button 
          @click="openDrawer()"
          class="px-6 py-3 bg-slate-900 text-white font-black rounded-2xl shadow-lg shadow-slate-900/10 hover:bg-slate-800 transition-all active:scale-95 flex items-center gap-2"
        >
          <Plus class="w-5 h-5" />
          Add Zone
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="w-8 h-8 border-4 border-slate-900 dark:border-white border-t-transparent rounded-full animate-spin"></div>
    </div>
    <!-- Zones Table -->
    <div v-else class="max-w-4xl mx-auto bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-300">
      <table class="w-full text-left">
        <thead>
          <tr class="bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
            <th class="py-5 px-8 text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Zone Name</th>
            <th class="py-5 px-8 text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Cities/Areas</th>
            <th class="py-5 px-8 text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Charge</th>
            <th class="py-5 px-8 text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Status</th>
            <th class="py-5 px-8 text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
          <tr v-if="zones.length === 0">
            <td colspan="5" class="py-12 text-center text-slate-500 dark:text-slate-400 font-semibold">No delivery zones found. Add one to get started.</td>
          </tr>
          <tr v-for="zone in zones" :key="zone.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors group">
            <td class="py-5 px-8">
              <span class="font-black text-slate-800 dark:text-slate-200 tracking-tight">{{ zone.name }}</span>
            </td>
            <td class="py-5 px-8">
              <span class="text-sm font-bold text-slate-500 dark:text-slate-400 leading-relaxed max-w-[300px] block">{{ zone.areas || 'All Areas' }}</span>
            </td>
            <td class="py-5 px-8">
              <span class="px-4 py-1.5 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-xs font-black rounded-full">{{ zone.charge }}</span>
            </td>
            <td class="py-5 px-8">
              <span 
                :class="[
                  'px-4 py-1.5 text-[10px] font-black rounded-full uppercase tracking-widest',
                  zone.status === 'Active' ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-slate-100 text-slate-400 dark:bg-slate-700 dark:text-slate-300'
                ]"
              >
                {{ zone.status || 'Active' }}
              </span>
            </td>
            <td class="py-5 px-8 text-right">
              <div class="flex items-center justify-end gap-2">
                <button 
                  @click="openDrawer(zone)"
                  class="p-2.5 bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-indigo-600 hover:bg-white dark:hover:bg-slate-700 rounded-xl transition-all shadow-sm"
                >
                  <Edit3 class="w-4 h-4" />
                </button>
                <button @click="confirmDelete(zone)" class="p-2.5 bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-red-500 hover:bg-white dark:hover:bg-slate-700 rounded-xl transition-all shadow-sm">
                  <Trash2 class="w-4 h-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
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
      <div v-if="isDrawerOpen" class="fixed inset-y-0 right-0 w-[480px] bg-white dark:bg-slate-900 shadow-2xl z-50 flex flex-col border-l border-slate-100 dark:border-slate-800 transition-colors duration-300">
        <!-- Drawer Header -->
        <div class="p-8 flex items-center justify-between pb-4">
          <h2 class="text-xl font-black text-slate-900 dark:text-white">{{ editingZoneId ? 'Edit Delivery Zone' : 'Add New Delivery Zone' }}</h2>
          <button 
            @click="isDrawerOpen = false"
            class="w-10 h-10 bg-black rounded-xl flex items-center justify-center text-white hover:bg-slate-800 transition-all"
          >
            <X class="w-6 h-6" />
          </button>
        </div>

        <!-- Drawer Body -->
        <div class="flex-grow overflow-y-auto px-8 py-4 space-y-6">
          <!-- Name -->
          <div class="space-y-2">
            <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Name <span class="text-red-500">*</span></label>
            <input 
              v-model="form.name"
              type="text" 
              placeholder="e.g. Inside Dhaka" 
              class="w-full h-14 px-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200"
            >
            <p class="text-xs font-bold text-slate-400 dark:text-slate-500">Example: <span class="text-slate-500 dark:text-slate-400">Inside Dhaka, Outside Dhaka</span></p>
          </div>

          <!-- Delivery Charge -->
          <div class="space-y-2">
            <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Delivery Charge <span class="text-red-500">*</span></label>
            <input 
              v-model="form.charge"
              type="text" 
              placeholder="e.g. ৳ 60.00"
              class="w-full h-14 px-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200"
            >
          </div>

          <!-- Areas -->
          <div class="space-y-2">
            <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Areas (optional)</label>
            <textarea 
              v-model="form.areas"
              rows="4"
              placeholder="e.g. Banani, Dhanmondi" 
              class="w-full p-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200 resize-none"
            ></textarea>
            <p class="text-xs font-bold text-slate-400 dark:text-slate-500">Example: <span class="text-slate-500 dark:text-slate-400">Banani, Gulshan, Dhanmondi</span></p>
          </div>

          <!-- Delivery Time -->
          <div class="space-y-2">
            <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Delivery Time</label>
            <input 
              v-model="form.time"
              type="text" 
              placeholder="e.g. 1-2 days" 
              class="w-full h-14 px-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200"
            >
          </div>

          <!-- Status -->
          <div class="space-y-2 relative">
            <label class="text-sm font-bold text-slate-600 dark:text-slate-400">Status</label>
            <select v-model="form.status" class="w-full h-14 px-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-slate-900/5 focus:border-slate-900 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200 appearance-none">
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>
            <ChevronDown class="w-5 h-5 text-slate-400 absolute right-4 top-11 pointer-events-none" />
          </div>
        </div>

        <!-- Drawer Footer -->
        <div class="p-8 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3 z-10 relative transition-colors duration-300">
          <button 
            @click="isDrawerOpen = false"
            class="px-8 py-4 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-black rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-all active:scale-95"
          >
            Cancel
          </button>
          <button @click="saveZone" :disabled="saving" class="px-8 py-4 bg-black text-white font-black rounded-xl hover:bg-slate-800 transition-all active:scale-95 shadow-lg shadow-black/10 disabled:opacity-50">
            {{ saving ? 'Saving...' : 'Save Changes' }}
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
      <div v-if="isDrawerOpen" @click="isDrawerOpen = false" class="fixed inset-0 bg-slate-900/10 backdrop-blur-sm z-40"></div>
    </Transition>

    <AppConfirmationModal
      :isOpen="isDeleteModalOpen"
      title="Delete Delivery Zone"
      message="Are you sure you want to delete this delivery zone? This action cannot be undone."
      confirmText="Delete"
      confirmColor="bg-red-600 hover:bg-red-700"
      @close="isDeleteModalOpen = false"
      @confirm="deleteZone"
    />
  </div>
</template>

<script setup>
import { 
  ChevronRight, 
  Plus, 
  Edit3, 
  Trash2,
  X,
  ChevronDown
} from 'lucide-vue-next'
import { ref, reactive, onMounted } from 'vue'

const { $toast } = useNuxtApp()
const { getAll, createItem } = useCrud()
const router = useRouter()

const isDrawerOpen = ref(false)
const pending = ref(true)
const saving = ref(false)
const zones = ref([])

const editingZoneId = ref(null)
const isDeleteModalOpen = ref(false)
const zoneToDelete = ref(null)

const form = reactive({
  name: '',
  charge: '',
  areas: '',
  time: '',
  status: 'Active'
})

const loadSettings = async () => {
  try {
    pending.value = true
    const response = await getAll('/vendor/settings?group=delivery_zones')
    if (response.data?.zones) {
      let loadedZones = response.data.zones
      if (typeof loadedZones === 'string') {
        try {
          loadedZones = JSON.parse(loadedZones)
        } catch(e) {}
      }
      zones.value = Array.isArray(loadedZones) ? loadedZones : []
    }
  } catch (error) {
    if (error.response?.status !== 404) {
      $toast.error('Failed to load delivery zones')
    }
  } finally {
    pending.value = false
  }
}

const saveSettings = async (updatedZones) => {
  try {
    saving.value = true
    await createItem('/vendor/settings', {
      group: 'delivery_zones',
      settings: {
        zones: updatedZones
      }
    })
    zones.value = updatedZones
    router.push('/vendor/managed-shop/delivery-zones')
    return true
  } catch (error) {
    console.error(error)
    $toast.error('Failed to save settings')
    return false
  } finally {
    saving.value = false
  }
}

const openDrawer = (zone = null) => {
  if (zone) {
    editingZoneId.value = zone.id
    form.name = zone.name || ''
    form.charge = zone.charge || ''
    form.areas = zone.areas || ''
    form.time = zone.time || ''
    form.status = zone.status || 'Active'
  } else {
    editingZoneId.value = null
    form.name = ''
    form.charge = ''
    form.areas = ''
    form.time = ''
    form.status = 'Active'
  }
  isDrawerOpen.value = true
}

const saveZone = async () => {
  if (!form.name || !form.charge) {
    $toast.error('Name and Charge are required')
    return
  }

  const newZone = {
    id: editingZoneId.value || Date.now().toString(),
    ...form
  }

  let updatedZones = [...zones.value]
  if (editingZoneId.value) {
    const index = updatedZones.findIndex(z => z.id === editingZoneId.value)
    if (index !== -1) {
      updatedZones[index] = newZone
    }
  } else {
    updatedZones.push(newZone)
  }

  const success = await saveSettings(updatedZones)
  if (success) {
    $toast.success(editingZoneId.value ? 'Zone updated successfully!' : 'Zone added successfully!')
    isDrawerOpen.value = false
  }
}

const confirmDelete = (zone) => {
  zoneToDelete.value = zone
  isDeleteModalOpen.value = true
}

const deleteZone = async () => {
  if (!zoneToDelete.value) return
  
  const updatedZones = zones.value.filter(z => z.id !== zoneToDelete.value.id)
  const success = await saveSettings(updatedZones)
  
  if (success) {
    $toast.success('Zone deleted successfully!')
  }
  
  isDeleteModalOpen.value = false
  zoneToDelete.value = null
}

onMounted(() => {
  loadSettings()
})
</script>

<style scoped>
/* Custom scrollbar for drawer body */
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
.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #cbd5e1;
}
</style>
