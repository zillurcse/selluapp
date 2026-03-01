<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <!-- Breadcrumbs & Header -->
    <div class="max-w-[1400px] mx-auto mb-8">
      <div class="flex items-center gap-2 text-sm text-slate-500 font-medium mb-4">
        <NuxtLink to="/vendor/managed-shop" class="hover:text-indigo-600 dark:hover:text-indigo-400 font-bold transition-colors">Manage Shop</NuxtLink>
        <ChevronRight class="w-4 h-4" />
        <NuxtLink to="/vendor/managed-shop/shipping" class="hover:text-indigo-600 dark:hover:text-indigo-400 font-bold transition-colors">Shipping</NuxtLink>
        <ChevronRight class="w-4 h-4" />
        <span class="text-slate-900 dark:text-white font-black italic">Cities / Areas</span>
      </div>
      
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Cities / Areas</h1>
          <p class="text-slate-500 dark:text-slate-400 font-bold opacity-80 mt-1 uppercase text-[10px] tracking-widest">Configure detailed delivery zones and delivery costs.</p>
        </div>
        <div class="flex items-center gap-3">
          <button 
            @click="syncPathaoCities" 
            :disabled="isSyncing"
            class="flex items-center gap-2 px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-black rounded-2xl transition-all shadow-xl active:scale-95 disabled:opacity-50 group"
          >
            <RefreshCw :class="['w-5 h-5 transition-transform duration-700', isSyncing ? 'animate-spin' : 'group-hover:rotate-180']" />
            {{ isSyncing ? 'Syncing...' : 'Sync Pathao Cities' }}
          </button>
          <button @click="openAddModal" class="flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-black text-white font-black rounded-2xl transition-all shadow-xl active:scale-95 group">
            <Plus class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" />
            Add City
          </button>
        </div>
      </div>
    </div>

    <!-- Filters Section -->
    <div class="max-w-[1400px] mx-auto mb-6 flex flex-col md:flex-row gap-4">
      <div class="relative flex-grow">
        <Search class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" />
        <input 
          v-model="searchQuery"
          @input="handleSearch"
          type="text" 
          placeholder="Search city name..." 
          class="w-full h-12 pl-12 pr-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-700 dark:text-slate-200 shadow-sm"
        >
      </div>
      <div class="w-full md:w-80">
        <AppSearchSelect 
          v-model="filterState"
          :items="states"
          placeholder="Filter by State..."
          class="!flex-row !items-center !gap-0"
        >
          <template #label><span class="hidden"></span></template>
        </AppSearchSelect>
      </div>
    </div>

    <!-- Table -->
    <div class="max-w-[1400px] mx-auto bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-indigo-600 dark:bg-slate-800 border-b border-indigo-700/50 dark:border-slate-800">
              <th class="py-5 px-8 font-black text-[10px] uppercase tracking-widest text-indigo-100/70">#</th>
              <th class="py-5 px-8 font-black text-[10px] uppercase tracking-widest text-white">City Name</th>
              <th class="py-5 px-8 font-black text-[10px] uppercase tracking-widest text-white">State / Country</th>
              <th class="py-5 px-8 font-black text-[10px] uppercase tracking-widest text-white">Delivery Cost</th>
              <th class="py-5 px-8 font-black text-[10px] uppercase tracking-widest text-white">Status</th>
              <th class="py-5 px-8 font-black text-[10px] uppercase tracking-widest text-white text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="pending" v-for="i in 5" :key="i" class="animate-pulse">
              <td colspan="6" class="p-8 border-b border-slate-50 dark:border-slate-800/50">
                <div class="h-4 bg-slate-100 dark:bg-slate-800 rounded w-full"></div>
              </td>
            </tr>
            <tr v-else-if="cities.length === 0">
              <td colspan="6" class="py-20 px-8 text-center text-slate-500 font-black italic">No cities found. Get started by adding a new one!</td>
            </tr>
            <tr v-for="(item, index) in cities" :key="item.id" class="border-b border-slate-50 dark:border-slate-800/50 hover:bg-indigo-50/30 dark:hover:bg-indigo-900/10 transition-colors group">
              <td class="py-5 px-8">
                <span class="text-sm font-black text-slate-300">#{{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}</span>
              </td>
              <td class="py-5 px-8">
                <div class="font-black text-slate-900 dark:text-white text-base">{{ item.name }}</div>
                <div v-if="item.pathao_city_id" class="text-[9px] font-black text-emerald-500 uppercase mt-1 tracking-tighter">Pathao Integrated</div>
              </td>
              <td class="py-5 px-8 font-bold text-slate-500 italic">
                {{ item.state?.name }} <span class="text-[10px] opacity-40 mx-1">/</span> {{ item.state?.country?.name }}
              </td>
              <td class="py-5 px-8">
                <div class="font-black text-slate-900 dark:text-white">
                  {{ item.cost ? `৳ ${item.cost}` : 'Free' }}
                </div>
              </td>
              <td class="py-5 px-8">
                <span :class="[
                  'px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-1.5',
                  item.status ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-rose-50 text-rose-600 border border-rose-100'
                ]">
                  <span class="w-1.5 h-1.5 rounded-full" :class="item.status ? 'bg-emerald-600' : 'bg-rose-600'"></span>
                  {{ item.status ? 'Deliverable' : 'Suspended' }}
                </span>
              </td>
              <td class="py-5 px-8 text-right">
                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                  <button @click="editCity(item)" class="p-2.5 hover:bg-white dark:hover:bg-slate-800 text-indigo-600 border border-transparent hover:border-slate-100 dark:hover:border-slate-700 rounded-xl shadow-sm transition-all hover:scale-110">
                    <Pencil class="w-4 h-4" />
                  </button>
                  <button @click="openDeleteModal(item.id)" class="p-2.5 hover:bg-white dark:hover:bg-slate-800 text-rose-600 border border-transparent hover:border-slate-100 dark:hover:border-slate-700 rounded-xl shadow-sm transition-all hover:scale-110">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="p-8 bg-slate-50/50 dark:bg-slate-800/20 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
        <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Page {{ pagination.current_page }} of {{ pagination.last_page }}</p>
        <div class="flex gap-3">
          <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="h-10 px-4 rounded-xl border border-slate-200 dark:border-slate-800 disabled:opacity-30 bg-white dark:bg-slate-900 transition-all active:scale-95 shadow-sm">
            <span class="text-xs font-black uppercase">Prev</span>
          </button>
          <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="h-10 px-4 rounded-xl border border-slate-200 dark:border-slate-800 disabled:opacity-30 bg-white dark:bg-slate-900 transition-all active:scale-95 shadow-sm">
            <span class="text-xs font-black uppercase">Next</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <TransitionRoot appear :show="isModalOpen" as="template">
      <Dialog as="div" @close="closeModal" class="relative z-50">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md" />
        <div class="fixed inset-0 overflow-y-auto">
          <div class="flex min-h-full items-center justify-center p-4">
            <DialogPanel class="w-full max-w-2xl transform overflow-hidden rounded-[40px] bg-white dark:bg-slate-900 p-10 shadow-3xl transition-all border border-slate-100 dark:border-slate-800">
              <div class="flex items-center justify-between mb-10">
                <div>
                  <DialogTitle class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                    {{ editingId ? 'Update City' : 'Create New City' }}
                  </DialogTitle>
                  <p class="text-sm font-bold text-slate-400 mt-1">Configure area-specific shipping rules and logistics.</p>
                </div>
                <button @click="closeModal" class="p-3 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-2xl transition-all text-slate-300">
                  <X class="w-6 h-6" />
                </button>
              </div>

              <form @submit.prevent="saveCity" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                  <div class="space-y-2">
                    <AppSearchSelect 
                      v-model="form.state_id"
                      :items="states"
                      label="State / Region"
                      placeholder="Search state..."
                      required
                    />
                  </div>

                  <div class="space-y-2">
                    <label class="text-xs font-black text-slate-500 uppercase tracking-widest ml-1">City Name *</label>
                    <input 
                      v-model="form.name"
                      type="text" 
                      placeholder="Enter city or area name" 
                      required
                      class="w-full h-14 px-5 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-700 dark:text-slate-200"
                    >
                  </div>

                  <div class="space-y-2">
                    <label class="text-xs font-black text-slate-500 uppercase tracking-widest ml-1">Delivery Cost (৳)</label>
                    <div class="relative">
                      <div class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 font-black">৳</div>
                      <input 
                        v-model="form.cost"
                        type="number" 
                        placeholder="0.00" 
                        step="0.01"
                        class="w-full h-14 pl-10 pr-5 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-black text-slate-700 dark:text-slate-200"
                      >
                    </div>
                  </div>

                  <div class="space-y-2">
                    <label class="text-xs font-black text-slate-500 uppercase tracking-widest ml-1">Availability Status</label>
                    <div class="h-14 flex items-center px-5 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700">
                      <button 
                        type="button"
                        @click="form.status = !form.status"
                        :class="['w-12 h-6 rounded-full transition-all duration-500 relative mr-4', form.status ? 'bg-indigo-600' : 'bg-slate-200 dark:bg-slate-700']"
                      >
                        <div :class="['w-4 h-4 bg-white rounded-full absolute top-1 transition-all duration-300', form.status ? 'left-7' : 'left-1']"></div>
                      </button>
                      <span class="text-sm font-black text-slate-600 dark:text-slate-400">{{ form.status ? 'Deliverable' : 'Suspended' }}</span>
                    </div>
                  </div>
                </div>

                <div class="bg-indigo-50/50 dark:bg-indigo-900/10 p-8 rounded-[32px] border border-indigo-100/50 dark:border-indigo-800/30 space-y-6">
                   <h4 class="text-xs font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-widest">Logistics Integration (Optional)</h4>
                   <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                      <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Pathao City ID</label>
                        <input v-model="form.pathao_city_id" type="text" class="w-full h-11 px-4 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl outline-none font-bold text-sm">
                      </div>
                      <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Pathao Zone ID</label>
                        <input v-model="form.pathao_zone_id" type="text" class="w-full h-11 px-4 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl outline-none font-bold text-sm">
                      </div>
                      <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-tighter">Pathao Area ID</label>
                        <input v-model="form.pathao_area_id" type="text" class="w-full h-11 px-4 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl outline-none font-bold text-sm">
                      </div>
                   </div>
                </div>

                <div class="pt-4">
                  <button 
                    type="submit" 
                    :disabled="saving"
                    class="w-full h-16 bg-slate-900 hover:bg-black text-white font-black rounded-2xl transition-all shadow-2xl active:scale-[0.98] disabled:opacity-50 flex items-center justify-center gap-3"
                  >
                    <Plus v-if="!editingId" class="w-5 h-5" />
                    <Pencil v-else class="w-5 h-5" />
                    {{ saving ? 'Saving Changes...' : (editingId ? 'Update City Record' : 'Create City Record') }}
                  </button>
                </div>
              </form>
            </DialogPanel>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>

    <AppConfirmationModal
      :isOpen="isDeleteModalOpen"
      title="Delete City"
      message="This action will permanently delete the city and all associated shipping logic. Proceed with caution."
      confirmText="Yes, Delete Permanently"
      :isLoading="isDeleting"
      @close="closeDeleteModal"
      @confirm="confirmDelete"
    />
  </div>
</template>

<script setup>
import { 
  ChevronRight, 
  Plus, 
  Search, 
  Building2,
  Pencil,
  Trash2,
  X,
  RefreshCw
} from 'lucide-vue-next'
import { 
  Dialog, 
  DialogPanel, 
  DialogTitle, 
  TransitionRoot 
} from '@headlessui/vue'
import AppConfirmationModal from '~/components/AppConfirmationModal.vue'
import AppSearchSelect from '~/components/AppSearchSelect.vue'
import { debounce } from 'lodash-es'
import { watch } from 'vue'

definePageMeta({
  middleware: 'auth',
  permissions: 'settings.view'
})

const { getAll, createItem, updateItem, deleteItem } = useCrud()
const { $toast } = useNuxtApp()

const cities = ref([])
const states = ref([])
const pending = ref(true)
const saving = ref(false)
const searchQuery = ref('')
const filterState = ref('')
const isModalOpen = ref(false)
const editingId = ref(null)
const isSyncing = ref(false)

const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
  per_page: 10,
  from: 0,
  to: 0
})

const form = reactive({
  name: '',
  state_id: '',
  cost: null,
  status: true,
  pathao_city_id: '',
  pathao_zone_id: '',
  pathao_area_id: ''
})

const fetchCities = async () => {
  try {
    pending.value = true
    const response = await getAll('/vendor/cities', {
      search: searchQuery.value,
      state_id: filterState.value,
      page: pagination.value.current_page,
      per_page: pagination.value.per_page
    })
    
    if (response.data) {
      cities.value = response.data
      pagination.value = {
        current_page: response.current_page,
        last_page: response.last_page,
        total: response.total,
        per_page: response.per_page,
        from: response.from,
        to: response.to
      }
    }
  } catch (error) {
    console.error(error)
  } finally {
    pending.value = false
  }
}

const fetchStates = async () => {
  try {
    const response = await getAll('/vendor/all-states')
    if (response.data) {
      states.value = response.data.map(s => ({
        ...s,
        name: s.country ? `${s.name} (${s.country.name})` : s.name
      }))
    }
  } catch (error) {
    console.error(error)
  }
}

const handleSearch = debounce(() => {
  pagination.value.current_page = 1
  fetchCities()
}, 500)

const changePage = (page) => {
  pagination.value.current_page = page
  fetchCities()
}

const openAddModal = () => {
  editingId.value = null
  Object.assign(form, {
    name: '',
    state_id: '',
    cost: null,
    status: true,
    pathao_city_id: '',
    pathao_zone_id: '',
    pathao_area_id: ''
  })
  isModalOpen.value = true
}

const editCity = (item) => {
  editingId.value = item.id
  Object.assign(form, {
    name: item.name,
    state_id: item.state_id,
    cost: item.cost,
    status: !!item.status,
    pathao_city_id: item.pathao_city_id || '',
    pathao_zone_id: item.pathao_zone_id || '',
    pathao_area_id: item.pathao_area_id || ''
  })
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
}

const saveCity = async () => {
  try {
    saving.value = true
    if (editingId.value) {
      await updateItem(`/vendor/cities/${editingId.value}`, form)
    } else {
      await createItem('/vendor/cities', form, null, false)
    }
    closeModal()
    fetchCities()
  } catch (error) {
    console.error(error)
  } finally {
    saving.value = false
  }
}

const syncPathaoCities = async () => {
  try {
    isSyncing.value = true
    const response = await getAll(`/vendor/couriers/pathao/bulk-sync`)
    
    if (response.status === 'success') {
      $toast.success(response.message || 'Pathao locations synced successfully')
      fetchCities()
    } else {
      $toast.error(response.message || 'Failed to sync Pathao locations')
    }
  } catch (error) {
    console.error(error)
    $toast.error(error.data?.message || error.message || 'An error occurred during sync')
  } finally {
    isSyncing.value = false
  }
}

// Delete Logic
const isDeleteModalOpen = ref(false)
const itemToDelete = ref(null)
const isDeleting = ref(false)

const openDeleteModal = (id) => {
  itemToDelete.value = id
  isDeleteModalOpen.value = true
}

const closeDeleteModal = () => {
  isDeleteModalOpen.value = false
  itemToDelete.value = null
}

const confirmDelete = async () => {
  try {
    isDeleting.value = true
    await deleteItem(itemToDelete.value, '/vendor/cities')
    closeDeleteModal()
    fetchCities()
  } catch (error) {
    console.error(error)
  } finally {
    isDeleting.value = false
  }
}

watch(filterState, () => {
  pagination.value.current_page = 1
  fetchCities()
})

onMounted(() => {
  fetchCities()
  fetchStates()
})
</script>

<style scoped>
.shadow-3xl {
  box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.15);
}
</style>
