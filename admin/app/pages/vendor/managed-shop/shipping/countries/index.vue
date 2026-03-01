<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <!-- Breadcrumbs & Header -->
    <div class="max-w-[1400px] mx-auto mb-8">
      <div class="flex items-center gap-2 text-sm text-slate-500 font-medium mb-4">
        <NuxtLink to="/vendor/managed-shop" class="hover:text-indigo-600 dark:hover:text-indigo-400">Manage Shop</NuxtLink>
        <ChevronRight class="w-4 h-4" />
        <NuxtLink to="/vendor/managed-shop/shipping" class="hover:text-indigo-600 dark:hover:text-indigo-400">Shipping</NuxtLink>
        <ChevronRight class="w-4 h-4" />
        <span class="text-slate-900 dark:text-white font-bold">Countries</span>
      </div>
      
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Countries</h1>
          <p class="text-slate-500 dark:text-slate-400 font-semibold mt-1">Manage countries where you provide shipping services.</p>
        </div>
        <button @click="openAddModal" class="flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl transition-all shadow-lg shadow-indigo-600/20 active:scale-95 group">
          <Plus class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" />
          Add Country
        </button>
      </div>
    </div>

    <!-- Stats Section -->
    <div class="max-w-[1400px] mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white dark:bg-slate-900 p-6 rounded-[24px] border border-slate-100 dark:border-slate-800 shadow-sm">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center text-indigo-600 dark:text-indigo-400">
            <Globe class="w-6 h-6" />
          </div>
          <div>
            <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total Countries</p>
            <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ pagination.total }}</h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Search & Filters -->
    <div class="max-w-[1400px] mx-auto mb-6 flex flex-col md:flex-row gap-4">
      <div class="relative flex-grow">
        <Search class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" />
        <input 
          v-model="searchQuery"
          @input="handleSearch"
          type="text" 
          placeholder="Search country name or ISO code..." 
          class="w-full h-12 pl-12 pr-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-slate-700 dark:text-slate-200 shadow-sm"
        >
      </div>
    </div>

    <!-- Table -->
    <div class="max-w-[1400px] mx-auto bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800">
              <th class="py-5 px-8 font-black text-[10px] uppercase tracking-wider text-slate-400">#</th>
              <th class="py-5 px-8 font-black text-[10px] uppercase tracking-wider text-slate-400">Country Name</th>
              <th class="py-5 px-8 font-black text-[10px] uppercase tracking-wider text-slate-400">ISO Code</th>
              <th class="py-5 px-8 font-black text-[10px] uppercase tracking-wider text-slate-400">Status</th>
              <th class="py-5 px-8 font-black text-[10px] uppercase tracking-wider text-slate-400 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="pending" v-for="i in 5" :key="i" class="animate-pulse">
              <td colspan="5" class="p-8 border-b border-slate-50 dark:border-slate-800/50">
                <div class="h-4 bg-slate-100 dark:bg-slate-800 rounded w-full"></div>
              </td>
            </tr>
            <tr v-else-if="countries.length === 0">
              <td colspan="5" class="py-20 px-8 text-center text-slate-500 font-medium">No countries found.</td>
            </tr>
            <tr v-for="(item, index) in countries" :key="item.id" class="border-b border-slate-50 dark:border-slate-800/50 hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
              <td class="py-5 px-8">
                <span class="text-sm font-bold text-slate-400">{{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}</span>
              </td>
              <td class="py-5 px-8">
                <div class="font-bold text-slate-900 dark:text-white">{{ item.name }}</div>
              </td>
              <td class="py-5 px-8">
                <span class="px-2 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-lg text-xs font-black uppercase">{{ item.iso_code }}</span>
              </td>
              <td class="py-5 px-8">
                <span :class="[
                  'px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-1.5',
                  item.is_active ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-slate-50 text-slate-400 border border-slate-100'
                ]">
                  <span class="w-1.5 h-1.5 rounded-full" :class="item.is_active ? 'bg-emerald-600' : 'bg-slate-400'"></span>
                  {{ item.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="py-5 px-8 text-right">
                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                  <button @click="editCountry(item)" class="p-2 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 text-indigo-600 rounded-lg transition-all">
                    <Pencil class="w-4 h-4" />
                  </button>
                  <button @click="openDeleteModal(item.id)" class="p-2 hover:bg-red-50 dark:hover:bg-red-900/30 text-red-600 rounded-lg transition-all">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="p-6 border-t border-slate-50 dark:border-slate-800 flex items-center justify-between">
        <p class="text-sm font-medium text-slate-500">Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} countries</p>
        <div class="flex gap-2">
          <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="p-2 rounded-lg border border-slate-200 dark:border-slate-800 disabled:opacity-50">
            <ChevronLeft class="w-4 h-4" />
          </button>
          <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="p-2 rounded-lg border border-slate-200 dark:border-slate-800 disabled:opacity-50">
            <ChevronRight class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <TransitionRoot appear :show="isModalOpen" as="template">
      <Dialog as="div" @close="closeModal" class="relative z-50">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" />
        <div class="fixed inset-0 overflow-y-auto">
          <div class="flex min-h-full items-center justify-center p-4">
            <DialogPanel class="w-full max-w-md transform overflow-hidden rounded-[32px] bg-white dark:bg-slate-900 p-8 shadow-2xl transition-all border border-slate-100 dark:border-slate-800">
              <div class="flex items-center justify-between mb-8">
                <DialogTitle class="text-2xl font-black text-slate-900 dark:text-white">
                  {{ editingId ? 'Update Country' : 'Add New Country' }}
                </DialogTitle>
                <button @click="closeModal" class="p-2 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-full transition-colors text-slate-400">
                  <X class="w-5 h-5" />
                </button>
              </div>

              <form @submit.prevent="saveCountry" class="space-y-6">
                <div class="space-y-2">
                  <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">Country Name *</label>
                  <input 
                    v-model="form.name"
                    type="text" 
                    placeholder="e.g. United States" 
                    required
                    class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-slate-700 dark:text-slate-200"
                  >
                </div>

                <div class="space-y-2">
                  <label class="text-sm font-bold text-slate-700 dark:text-slate-300 ml-1">ISO Code (3 letters) *</label>
                  <input 
                    v-model="form.iso_code"
                    type="text" 
                    placeholder="e.g. USA" 
                    maxlength="3"
                    required
                    class="w-full h-12 px-4 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium text-slate-700 dark:text-slate-200 uppercase"
                  >
                </div>

                <div class="flex items-center gap-3 py-2">
                  <button 
                    type="button"
                    @click="form.is_active = !form.is_active"
                    :class="['w-12 h-6 rounded-full transition-all duration-300 relative', form.is_active ? 'bg-emerald-500' : 'bg-slate-200 dark:bg-slate-700']"
                  >
                    <div :class="['w-4 h-4 bg-white rounded-full absolute top-1 transition-all duration-300', form.is_active ? 'left-7' : 'left-1']"></div>
                  </button>
                  <span class="text-sm font-bold text-slate-600 dark:text-slate-400">Mark as Active</span>
                </div>

                <div class="pt-4">
                  <button 
                    type="submit" 
                    :disabled="saving"
                    class="w-full h-12 bg-slate-900 hover:bg-black text-white font-black rounded-xl transition-all shadow-lg active:scale-95 disabled:opacity-50"
                  >
                    {{ saving ? 'Saving...' : (editingId ? 'Update Country' : 'Create Country') }}
                  </button>
                </div>
              </form>
            </DialogPanel>
          </div>
        </div>
      </Dialog>
    </TransitionRoot>

    <!-- Success Modal/Toast handled by sonner -->
    <AppConfirmationModal
      :isOpen="isDeleteModalOpen"
      title="Delete Country"
      message="Are you sure you want to delete this country? This will also remove associated states and cities."
      confirmText="Yes, Delete"
      :isLoading="isDeleting"
      @close="closeDeleteModal"
      @confirm="confirmDelete"
    />
  </div>
</template>

<script setup>
import { 
  ChevronRight, 
  ChevronLeft,
  Plus, 
  Search, 
  Globe,
  Pencil,
  Trash2,
  X
} from 'lucide-vue-next'
import { 
  Dialog, 
  DialogPanel, 
  DialogTitle, 
  TransitionRoot 
} from '@headlessui/vue'
// import AppConfirmationModal from '~/components/AppConfirmationModal.vue'
import { debounce } from 'lodash-es'

definePageMeta({
  middleware: 'auth',
  permissions: 'settings.view'
})

const { getAll, createItem, updateItem, deleteItem } = useCrud()
const { $toast } = useNuxtApp()

const countries = ref([])
const pending = ref(true)
const saving = ref(false)
const searchQuery = ref('')
const isModalOpen = ref(false)
const editingId = ref(null)

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
  iso_code: '',
  is_active: true
})

const fetchCountries = async () => {
  try {
    pending.value = true
    const response = await getAll('/vendor/countries', {
      search: searchQuery.value,
      page: pagination.value.current_page,
      per_page: pagination.value.per_page
    })
    
    if (response.data) {
      countries.value = response.data
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

const handleSearch = debounce(() => {
  pagination.value.current_page = 1
  fetchCountries()
}, 500)

const changePage = (page) => {
  pagination.value.current_page = page
  fetchCountries()
}

const openAddModal = () => {
  editingId.value = null
  Object.assign(form, {
    name: '',
    iso_code: '',
    is_active: true
  })
  isModalOpen.value = true
}

const editCountry = (item) => {
  editingId.value = item.id
  Object.assign(form, {
    name: item.name,
    iso_code: item.iso_code,
    is_active: !!item.is_active
  })
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
}

const saveCountry = async () => {
  try {
    saving.value = true
    if (editingId.value) {
      await updateItem(`/vendor/countries/${editingId.value}`, form)
    } else {
      await createItem('/vendor/countries', form, null, false)
    }
    closeModal()
    fetchCountries()
  } catch (error) {
    console.error(error)
  } finally {
    saving.value = false
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
    await deleteItem(itemToDelete.value, '/vendor/countries')
    closeDeleteModal()
    fetchCountries()
  } catch (error) {
    console.error(error)
  } finally {
    isDeleting.value = false
  }
}

onMounted(() => {
  fetchCountries()
})
</script>
