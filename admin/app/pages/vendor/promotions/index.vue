<template>
  <div class="min-h-screen bg-[#f8f9fa] dark:bg-slate-950 p-10 transition-colors duration-300">
    <!-- Breadcrumbs -->
    <nav class="flex items-center space-x-2 text-sm text-gray-600 dark:text-slate-400 mb-8">
      <NuxtLink to="/vendor" class="hover:text-black dark:hover:text-white transition-colors">
        <HomeIcon class="w-4 h-4" />
      </NuxtLink>
      <ChevronRightIcon class="w-4 h-4 text-gray-400 dark:text-slate-600" />
      <span class="hover:text-black dark:hover:text-white cursor-pointer">Promotions</span>
      <ChevronRightIcon class="w-4 h-4 text-gray-400 dark:text-slate-600" />
      <span class="text-black dark:text-white font-medium">List</span>
    </nav>

    <!-- Main Content Area -->
    <div v-if="loading" class="flex items-center justify-center min-h-[60vh]">
      <div class="animate-spin rounded-full h-12 w-12 border-4 border-indigo-500 border-t-transparent"></div>
    </div>
    <div v-else-if="items.length === 0" class="flex flex-col items-center justify-center min-h-[60vh] bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-8">
      <!-- Empty State Illustration -->
      <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-6 shadow-inner relative">
         <div class="absolute inset-0 bg-gray-100 rounded-full scale-110 opacity-50"></div>
         <MegaphoneIcon class="w-10 h-10 text-gray-400 relative z-10" />
      </div>

      <h1 class="text-2xl font-bold text-[#1e293b] dark:text-white mb-2">No promotions found</h1>
      <p class="text-center text-gray-500 dark:text-slate-400 max-w-md mb-8 leading-relaxed">
        You haven't created any promotions yet. Boost your sales by creating limited-time offers or flash sales.
      </p>

      <button 
        @click="openAddDrawer"
        class="bg-[#0f172a] text-white px-6 py-2.5 rounded-lg flex items-center space-x-2 hover:bg-black transition-all shadow-lg hover:shadow-xl active:scale-95"
      >
        <PlusIcon class="w-5 h-5" />
        <span class="font-medium">Add New Promotion</span>
      </button>
    </div>

    <!-- Data Table -->
    <div v-else class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden transition-colors">
        <div class="p-10 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">All Promotions</h2>
            <button 
                @click="openAddDrawer"
                class="bg-[#0f172a] text-white px-4 py-2 text-sm rounded-lg flex items-center space-x-2 hover:bg-black transition-all shadow-sm active:scale-95"
            >
                <PlusIcon class="w-4 h-4" />
                <span class="font-medium">Add New</span>
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-slate-800 border-b border-gray-100 dark:border-slate-700">
                        <th class="py-4 px-6 font-semibold text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wider">Promotion Title</th>
                        <th class="py-4 px-6 font-semibold text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wider">Type / Value</th>
                        <th class="py-4 px-6 font-semibold text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wider">Duration</th>
                        <th class="py-4 px-6 font-semibold text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="py-4 px-6 font-semibold text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    <tr v-for="promotion in items" :key="promotion.id" class="hover:bg-gray-50/50 dark:hover:bg-slate-800/50 transition-colors group">
                        <td class="px-6 py-4 flex items-center gap-4">
                            <div v-if="promotion.banner" class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                <img :src="getMediaUrl(promotion.banner)" class="w-full h-full object-cover">
                            </div>
                            <div v-else class="w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-400 shrink-0">
                                <ImageIcon class="w-5 h-5" />
                            </div>
                            <div>
                            <div class="font-bold text-gray-900 dark:text-slate-100">{{ promotion.title }}</div>
                                <div class="text-xs text-gray-500 dark:text-slate-400 mt-1 capitalize">{{ formatTarget(promotion.target) }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900 dark:text-slate-100 capitalize">{{ formatType(promotion.type) }}</div>
                            <div class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                                {{ promotion.discount_unit === 'percentage' ? promotion.discount_value + '%' : '$' + promotion.discount_value }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600 dark:text-slate-300">
                                {{ formatDate(promotion.start_date) }} - {{ formatDate(promotion.end_date) }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="[
                                'px-2.5 py-1 text-xs font-bold rounded-full border',
                                promotion.is_active ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-600/20' : 'bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 border-rose-100 dark:border-rose-600/20'
                            ]">
                                {{ promotion.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                           <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                               <button @click="openEditDrawer(promotion)" class="p-2 text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg transition-colors tooltip-target" title="Edit Promotion">
                                   <EditIcon class="w-4 h-4" />
                               </button>
                               <button @click="confirmDelete(promotion.id)" class="p-2 text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/30 hover:bg-rose-100 dark:hover:bg-rose-900/50 rounded-lg transition-colors tooltip-target" title="Delete Promotion">
                                   <TrashIcon class="w-4 h-4" />
                               </button>
                           </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Side Drawer (Slide-over) -->
    <Transition
      enter-active-class="transform transition ease-in-out duration-500 sm:duration-700"
      enter-from-class="translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transform transition ease-in-out duration-500 sm:duration-700"
      leave-from-class="translate-x-0"
      leave-to-class="translate-x-full"
    >
      <div v-if="isOpen" class="fixed inset-y-0 right-0 w-full max-w-md bg-white dark:bg-slate-900 shadow-2xl z-50 flex flex-col">
        <!-- Drawer Header -->
        <div class="px-6 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
          <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ isEditing ? 'Edit Promotion' : 'Add New Promotion' }}</h2>
          <button @click="closeDrawer" class="p-1.5 bg-black text-white rounded-md hover:bg-gray-800 transition-colors">
            <XIcon class="w-5 h-5" />
          </button>
        </div>

        <!-- Drawer Body (Scrollable) -->
        <div class="flex-1 overflow-y-auto px-6 py-8 space-y-6">
          <!-- Title -->
          <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Promotion Title <span class="text-red-500">*</span></label>
            <input 
              v-model="form.title"
              type="text" 
              placeholder="e.g. Black Friday Special" 
              class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-slate-500"
            >
          </div>

          <!-- Promotion Type -->
          <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Promotion Type <span class="text-red-500">*</span></label>
            <div class="relative">
              <select 
                v-model="form.type"
                class="w-full px-4 py-2.5 bg-[#f0f7ff] dark:bg-slate-800 border border-blue-100 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none appearance-none text-gray-900 dark:text-white"
              >
                <option value="flash_sale">Flash Sale</option>
                <option value="flat_discount">Flat Discount</option>
                <option value="buy_x_get_y">Buy X Get Y</option>
                <option value="bundle">Bundle Offer</option>
              </select>
              <ChevronDownIcon class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
            </div>
          </div>

          <!-- Date Range -->
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Start Date <span class="text-red-500">*</span></label>
                <input 
                    v-model="form.start_date"
                    type="date" 
                    class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none text-gray-900 dark:text-white"
                >
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">End Date <span class="text-red-500">*</span></label>
                <input 
                    v-model="form.end_date"
                    type="date" 
                    class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none text-gray-900 dark:text-white"
                >
            </div>
          </div>

          <!-- Target -->
          <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700">Apply To <span class="text-red-500">*</span></label>
            <div class="relative">
              <select 
                v-model="form.target"
                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none appearance-none text-gray-900"
              >
                <option value="all">All Products</option>
                <option value="selected">Selected Products</option>
                <option value="categories">Specific Categories</option>
              </select>
              <ChevronDownIcon class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
            </div>
          </div>

          <!-- Dynamic Lookups based on target -->
          <div v-if="form.target === 'selected'" class="space-y-1.5">
              <label class="block text-sm font-semibold text-gray-700">Select Products <span class="text-red-500">*</span></label>
              <select 
                  v-model="form.target_ids" 
                  multiple 
                  class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none text-gray-900 min-h-[120px]"
              >
                  <option v-for="p in allProducts" :key="p.id" :value="p.id">{{ p.name }}</option>
              </select>
              <p class="text-xs text-gray-500 mt-1">Hold Ctrl (Windows) or Cmd (Mac) to select multiple items.</p>
          </div>

          <div v-if="form.target === 'categories'" class="space-y-1.5">
              <label class="block text-sm font-semibold text-gray-700">Select Categories <span class="text-red-500">*</span></label>
              <select 
                  v-model="form.target_ids" 
                  multiple 
                  class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none text-gray-900 min-h-[120px]"
              >
                  <option v-for="c in allCategories" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
              <p class="text-xs text-gray-500 mt-1">Hold Ctrl (Windows) or Cmd (Mac) to select multiple items.</p>
          </div>

          <!-- Discount Details -->
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-gray-700">Discount Value <span class="text-red-500">*</span></label>
                <input 
                  v-model="form.discount_value"
                  type="number" 
                  placeholder="0.00" 
                  class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none text-gray-900 placeholder:text-gray-400"
                >
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-gray-700">Unit <span class="text-red-500">*</span></label>
                <div class="relative">
                  <select 
                    v-model="form.discount_unit"
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none appearance-none text-gray-900"
                  >
                    <option value="percentage">Percentage (%)</option>
                    <option value="fixed">Fixed Amount</option>
                  </select>
                  <ChevronDownIcon class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
                </div>
            </div>
          </div>

          <!-- Banner Placeholder -->
          <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700">Promotion Banner</label>
            <div class="w-full h-32 border-2 border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer relative overflow-hidden group">
              <template v-if="form.bannerPreview">
                <img :src="form.bannerPreview" class="w-full h-full object-cover" />
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <span class="text-white font-bold text-xs bg-black/50 px-3 py-1.5 rounded-lg backdrop-blur-sm">Change Image</span>
                </div>
              </template>
              <template v-else-if="form.bannerUrl">
                <img :src="form.bannerUrl" class="w-full h-full object-cover" />
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <span class="text-white font-bold text-xs bg-black/50 px-3 py-1.5 rounded-lg backdrop-blur-sm">Change Image</span>
                </div>
              </template>
              <template v-else>
                <UploadCloudIcon class="w-8 h-8 text-gray-400 mb-2" />
                <p class="text-xs text-gray-500 font-medium">Click to upload or drag and drop</p>
                <p class="text-[10px] text-gray-400 mt-1">PNG, JPG or WEBP (Max. 2MB)</p>
              </template>
              <input type="file" @change="handleBannerUpload" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
            </div>
          </div>

          <!-- Status -->
          <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700">Status</label>
            <div class="relative">
              <select 
                v-model="form.is_active"
                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none appearance-none text-gray-900"
              >
                <option :value="true">Active</option>
                <option :value="false">Inactive</option>
              </select>
              <ChevronDownIcon class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
            </div>
          </div>
        </div>

        <!-- Drawer Footer -->
        <div class="px-6 py-6 border-t border-gray-100 dark:border-slate-800 flex items-center space-x-3 bg-white dark:bg-slate-900">
          <button 
            @click="closeDrawer"
            type="button"
            class="flex-1 px-4 py-2.5 bg-[#eff4f9] text-[#718eb7] font-semibold rounded-lg hover:bg-[#e2eaf1] transition-colors"
          >
            Cancel
          </button>
          <button 
            @click="handleSubmit"
            type="button"
            :disabled="isSaving"
            class="flex-1 px-4 py-2.5 bg-black text-white font-semibold rounded-lg hover:bg-gray-800 transition-colors shadow-md active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center space-x-2"
          >
            <span v-if="isSaving" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
            <span>{{ isEditing ? 'Update Promotion' : 'Save Promotion' }}</span>
          </button>
        </div>
      </div>
    </Transition>

    <!-- Overlay -->
    <Transition
      enter-active-class="ease-out duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="isOpen" @click="closeDrawer" class="fixed inset-0 bg-black/20 backdrop-blur-sm z-40"></div>
    </Transition>

    <AppConfirmationModal
        :isOpen="isDeleteModalOpen"
        title="Delete Promotion"
        message="Are you sure you want to delete this promotion? This action cannot be undone."
        icon="TrashIcon"
        confirmText="Delete Promotion"
        :isLoading="isDeleting"
        @confirm="handleConfirmDelete"
        @cancel="isDeleteModalOpen = false"
    />
  </div>
</template>

<script setup>
import { 
  HomeIcon, 
  ChevronRightIcon, 
  MegaphoneIcon, 
  PlusIcon, 
  XIcon, 
  ChevronDownIcon,
  UploadCloudIcon,
  TrashIcon,
  EditIcon,
  Image as ImageIcon
} from 'lucide-vue-next'
import { ref, onMounted } from 'vue'
import { toast } from 'vue-sonner'

const config = useRuntimeConfig()
const getMediaUrl = (path) => path ? `${config.public.apiBase.replace('/api', '')}/storage/${path}` : ''

const { getAll, createItem, deleteItem } = useCrud()
const router = useRouter()

const items = ref([])
const allProducts = ref([])
const allCategories = ref([])
const loading = ref(false)
const isOpen = ref(false)
const isEditing = ref(false)
const isSaving = ref(false)

const isDeleteModalOpen = ref(false)
const promotionToDelete = ref(null)
const isDeleting = ref(false)

const form = ref({
  id: null,
  title: '',
  type: 'flash_sale',
  start_date: '',
  end_date: '',
  target: 'all',
  target_ids: [],
  discount_value: '',
  discount_unit: 'percentage',
  is_active: true,
  banner: null,
  bannerPreview: null,
  bannerUrl: null
})

const fetchItems = async () => {
    loading.value = true
    try {
        const response = await getAll('/vendor/promotions')
        items.value = response || []
    } catch (e) {
        console.error('Failed to fetch promotions', e)
        toast.error('Failed to load promotions')
    } finally {
        loading.value = false
    }
}

const fetchLookups = async () => {
    try {
        const pResponse = await getAll('/vendor/products')
        if (pResponse && Array.isArray(pResponse.data)) {
            allProducts.value = pResponse.data
        } else if (Array.isArray(pResponse)) {
            allProducts.value = pResponse
        }

        const cResponse = await getAll('/vendor/attributes/categories')
        if (cResponse && Array.isArray(cResponse.data)) {
            allCategories.value = cResponse.data
        } else if (Array.isArray(cResponse)) {
            allCategories.value = cResponse
        }
    } catch(e) {
        console.error('Failed to fetch lookups')
    }
}

onMounted(() => {
    fetchItems()
    fetchLookups()
})

const openAddDrawer = () => {
    isEditing.value = false
    resetForm()
    isOpen.value = true
}

const openEditDrawer = (promotion) => {
    isEditing.value = true
    form.value = {
        id: promotion.id,
        title: promotion.title,
        type: promotion.type,
        start_date: promotion.start_date,
        end_date: promotion.end_date,
        target: promotion.target,
        target_ids: promotion.target_ids || [],
        discount_value: promotion.discount_value,
        discount_unit: promotion.discount_unit,
        is_active: promotion.is_active,
        banner: null,
        bannerPreview: null,
        bannerUrl: promotion.banner ? getMediaUrl(promotion.banner) : null
    }
    isOpen.value = true
}

const closeDrawer = () => {
    isOpen.value = false
    setTimeout(resetForm, 300)
}

const resetForm = () => {
    if (form.value.bannerPreview) {
        URL.revokeObjectURL(form.value.bannerPreview)
    }
    form.value = {
        id: null,
        title: '',
        type: 'flash_sale',
        start_date: '',
        end_date: '',
        target: 'all',
        target_ids: [],
        discount_value: '',
        discount_unit: 'percentage',
        is_active: true,
        banner: null,
        bannerPreview: null,
        bannerUrl: null
    }
}

const handleBannerUpload = (event) => {
    const file = event.target.files[0]
    if (!file) return
    form.value.banner = file
    if (form.value.bannerPreview) URL.revokeObjectURL(form.value.bannerPreview)
    form.value.bannerPreview = URL.createObjectURL(file)
}

const formatDate = (dateString) => {
    if (!dateString) return ''
    const options = { year: 'numeric', month: 'short', day: 'numeric' }
    return new Date(dateString).toLocaleDateString(undefined, options)
}

const formatType = (type) => {
    if (!type) return ''
    return type.replace(/_/g, ' ')
}

const formatTarget = (target) => {
    if (!target) return ''
    return target.replace(/_/g, ' ')
}

const handleSubmit = async () => {
    if (!form.value.title || !form.value.discount_value || !form.value.start_date || !form.value.end_date) {
        toast.error('Please fill in required fields')
        return
    }

    if (form.value.target !== 'all' && (!form.value.target_ids || form.value.target_ids.length === 0)) {
        toast.error(`Please select at least one ${form.value.target === 'selected' ? 'product' : 'category'}`)
        return
    }

    isSaving.value = true
    try {
        const formData = new FormData()
        formData.append('title', form.value.title)
        formData.append('type', form.value.type)
        formData.append('start_date', form.value.start_date)
        formData.append('end_date', form.value.end_date)
        formData.append('target', form.value.target)
        formData.append('discount_value', form.value.discount_value)
        formData.append('discount_unit', form.value.discount_unit)
        formData.append('is_active', form.value.is_active ? 1 : 0)
        
        if (form.value.target !== 'all' && form.value.target_ids && form.value.target_ids.length > 0) {
            form.value.target_ids.forEach(id => {
                formData.append('target_ids[]', id)
            })
        }

        if (form.value.banner) {
            formData.append('banner', form.value.banner)
        }

        if (isEditing.value) {
            formData.append('_method', 'PUT')
            await createItem(`/vendor/promotions/${form.value.id}`, formData, null, false)
            router.push('/vendor/promotions')
        } else {
            await createItem('/vendor/promotions', formData, null, false)
            router.push('/vendor/promotions')
        }
        closeDrawer()
        fetchItems()
    } catch (e) {
        console.error('Failed to save promotion', e)
        toast.error(e.response?.data?.message || 'Failed to save promotion')
    } finally {
        isSaving.value = false
    }
}

const confirmDelete = (id) => {
    promotionToDelete.value = id
    isDeleteModalOpen.value = true
}

const handleConfirmDelete = async () => {
    if (!promotionToDelete.value) return
    isDeleting.value = true
    try {
        await deleteItem(`/vendor/promotions/${promotionToDelete.value}`)
        router.push('/vendor/promotions')
        fetchItems()
    } catch (e) {
        console.error('Failed to delete promotion', e)
        toast.error('Failed to delete promotion')
    } finally {
        isDeleting.value = false
        isDeleteModalOpen.value = false
        promotionToDelete.value = null
    }
}

definePageMeta({
  middleware: 'auth'
})
</script>

<style scoped>
.overflow-y-auto::-webkit-scrollbar {
  display: none;
}
.overflow-y-auto {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
