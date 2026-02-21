<template>
  <div class="min-h-screen bg-[#f8f9fa] dark:bg-slate-950 p-10 transition-colors duration-300">
    <!-- Breadcrumbs -->
    <nav class="flex items-center space-x-2 text-sm text-gray-600 dark:text-slate-400 mb-8">
      <NuxtLink to="/vendor" class="hover:text-black dark:hover:text-white transition-colors">
        <HomeIcon class="w-4 h-4" />
      </NuxtLink>
      <ChevronRightIcon class="w-4 h-4 text-gray-400 dark:text-slate-600" />
      <span class="hover:text-black dark:hover:text-white cursor-pointer">Coupon Code</span>
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
         <TicketIcon class="w-10 h-10 text-gray-400 relative z-10" />
      </div>

      <h1 class="text-2xl font-bold text-[#1e293b] dark:text-white mb-2">No coupons found</h1>
      <p class="text-center text-gray-500 dark:text-slate-400 max-w-md mb-8 leading-relaxed">
        You haven't created any coupons yet. Create your first coupon to start offering discounts to your customers.
      </p>

      <button 
        @click="openAddDrawer"
        class="bg-[#0f172a] text-white px-6 py-2.5 rounded-lg flex items-center space-x-2 hover:bg-black transition-all shadow-lg hover:shadow-xl active:scale-95"
      >
        <PlusIcon class="w-5 h-5" />
        <span class="font-medium">Add New Coupon</span>
      </button>
    </div>

    <!-- Data Table -->
    <div v-else class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden transition-colors">
        <div class="p-10 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">All Coupons</h2>
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
                        <th class="py-4 px-6 font-semibold text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wider">Coupon Code</th>
                        <th class="py-4 px-6 font-semibold text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wider">Value</th>
                        <th class="py-4 px-6 font-semibold text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wider">Expiry</th>
                        <th class="py-4 px-6 font-semibold text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="py-4 px-6 font-semibold text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    <tr v-for="coupon in items" :key="coupon.id" class="hover:bg-gray-50/50 dark:hover:bg-slate-800/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900 dark:text-slate-100">{{ coupon.code }}</div>
                            <div class="text-xs text-gray-500 dark:text-slate-400 mt-1">{{ coupon.title }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-slate-300 font-medium">
                                {{ coupon.discount_type === 'percentage' ? coupon.discount_amount + '%' : '$' + coupon.discount_amount }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600 dark:text-slate-300">
                                {{ formatDate(coupon.start_date) }} - {{ formatDate(coupon.end_date) }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="[
                                'px-2.5 py-1 text-xs font-bold rounded-full border',
                                coupon.is_active ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-600/20' : 'bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 border-rose-100 dark:border-rose-600/20'
                            ]">
                                {{ coupon.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                           <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                               <button @click="openEditDrawer(coupon)" class="p-2 text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg transition-colors tooltip-target" title="Edit Coupon">
                                   <EditIcon class="w-4 h-4" />
                               </button>
                               <button @click="confirmDelete(coupon.id)" class="p-2 text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/30 hover:bg-rose-100 dark:hover:bg-rose-900/50 rounded-lg transition-colors tooltip-target" title="Delete Coupon">
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
          <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ isEditing ? 'Edit Coupon' : 'Add New Coupon' }}</h2>
          <button @click="closeDrawer" class="p-1.5 bg-black text-white rounded-md hover:bg-gray-800 transition-colors">
            <XIcon class="w-5 h-5" />
          </button>
        </div>

        <!-- Drawer Body (Scrollable) -->
        <div class="flex-1 overflow-y-auto px-6 py-8 space-y-6">
          <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700">Coupon Title <span class="text-red-500">*</span></label>
            <input 
              v-model="form.title"
              type="text" 
              placeholder="e.g. Summer Sale 2024" 
              class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none text-gray-900 placeholder:text-gray-400"
            >
          </div>

          <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700">Coupon Code <span class="text-red-500">*</span></label>
            <div class="flex space-x-2">
              <input 
                v-model="form.code"
                type="text" 
                placeholder="SUMMER50" 
                class="flex-1 px-4 py-2.5 bg-[#f0f7ff] border border-blue-100 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none text-gray-900 placeholder:text-gray-400 font-mono"
              >
              <button @click="generateCode" type="button" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                Generate
              </button>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-gray-700">Discount Type</label>
                <div class="relative">
                    <select 
                        v-model="form.discount_type"
                        class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none appearance-none text-gray-900"
                    >
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Amount</option>
                    </select>
                    <ChevronDownIcon class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
                </div>
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-gray-700">Amount <span class="text-red-500">*</span></label>
                <input 
                    v-model="form.discount_amount"
                    type="number" 
                    placeholder="0.00" 
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none text-gray-900 placeholder:text-gray-400"
                >
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-gray-700">Start Date <span class="text-red-500">*</span></label>
                <input 
                    v-model="form.start_date"
                    type="date" 
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none text-gray-900"
                >
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-gray-700">End Date <span class="text-red-500">*</span></label>
                <input 
                    v-model="form.end_date"
                    type="date" 
                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none text-gray-900"
                >
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-gray-700">Min Purchase</label>
                <input 
                  v-model="form.min_purchase"
                  type="number" 
                  placeholder="0.00" 
                  class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none text-gray-900 placeholder:text-gray-400"
                >
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-gray-700">Usage Limit</label>
                <input 
                  v-model="form.usage_limit"
                  type="number" 
                  placeholder="e.g. 100" 
                  class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none text-gray-900 placeholder:text-gray-400"
                >
            </div>
          </div>

          <div v-if="form.discount_type === 'percentage'" class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700">Maximum Discount Amount</label>
            <input 
              v-model="form.max_discount"
              type="number" 
              placeholder="0.00" 
              class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none text-gray-900 placeholder:text-gray-400"
            >
          </div>

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
            <span>{{ isEditing ? 'Update Coupon' : 'Save Coupon' }}</span>
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
        title="Delete Coupon"
        message="Are you sure you want to delete this coupon? This action cannot be undone."
        icon="TrashIcon"
        confirmText="Delete Coupon"
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
  TicketIcon, 
  PlusIcon, 
  XIcon, 
  ChevronDownIcon,
  TrashIcon,
  EditIcon
} from 'lucide-vue-next'
import { ref, onMounted } from 'vue'
import { toast } from 'vue-sonner'

const { getAll, createItem, updateItem, deleteItem } = useCrud()
const router = useRouter()

const items = ref([])
const loading = ref(false)
const isOpen = ref(false)
const isEditing = ref(false)
const isSaving = ref(false)

const isDeleteModalOpen = ref(false)
const couponToDelete = ref(null)
const isDeleting = ref(false)

const form = ref({
  id: null,
  title: '',
  code: '',
  discount_type: 'percentage',
  discount_amount: '',
  start_date: '',
  end_date: '',
  min_purchase: '',
  max_discount: '',
  usage_limit: '',
  is_active: true
})

const fetchItems = async () => {
    loading.value = true
    try {
        const response = await getAll('/vendor/coupons')
        items.value = response || []
    } catch (e) {
        console.error('Failed to fetch coupons', e)
        toast.error('Failed to load coupons')
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    fetchItems()
})

const openAddDrawer = () => {
    isEditing.value = false
    resetForm()
    isOpen.value = true
}

const openEditDrawer = (coupon) => {
    isEditing.value = true
    form.value = {
        id: coupon.id,
        title: coupon.title,
        code: coupon.code,
        discount_type: coupon.discount_type,
        discount_amount: coupon.discount_amount,
        start_date: coupon.start_date,
        end_date: coupon.end_date,
        min_purchase: coupon.min_purchase,
        max_discount: coupon.max_discount,
        usage_limit: coupon.usage_limit,
        is_active: coupon.is_active
    }
    isOpen.value = true
}

const closeDrawer = () => {
    isOpen.value = false
    setTimeout(resetForm, 300)
}

const resetForm = () => {
    form.value = {
        id: null,
        title: '',
        code: '',
        discount_type: 'percentage',
        discount_amount: '',
        start_date: '',
        end_date: '',
        min_purchase: '',
        max_discount: '',
        usage_limit: '',
        is_active: true
    }
}

const generateCode = () => {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
    let result = ''
    for (let i = 0; i < 8; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length))
    }
    form.value.code = result
}

const formatDate = (dateString) => {
    if (!dateString) return ''
    const options = { year: 'numeric', month: 'short', day: 'numeric' }
    return new Date(dateString).toLocaleDateString(undefined, options)
}

const handleSubmit = async () => {
    if (!form.value.title || !form.value.code || !form.value.discount_amount || !form.value.start_date || !form.value.end_date) {
        toast.error('Please fill in required fields')
        return
    }

    isSaving.value = true
    try {
        if (isEditing.value) {
            await updateItem(`/vendor/coupons/${form.value.id}`, form.value)
            router.push('/vendor/coupon-code')
        } else {
            await createItem('/vendor/coupons', form.value)
            router.push('/vendor/coupon-code')
        }
        closeDrawer()
        fetchItems()
    } catch (e) {
        console.error('Failed to save coupon', e)
        toast.error(e.response?.data?.message || 'Failed to save coupon')
    } finally {
        isSaving.value = false
    }
}

const confirmDelete = (id) => {
    couponToDelete.value = id
    isDeleteModalOpen.value = true
}

const handleConfirmDelete = async () => {
    if (!couponToDelete.value) return
    isDeleting.value = true
    try {
        await deleteItem(`/vendor/coupons/${couponToDelete.value}`)
        router.push('/vendor/coupon-code')
    } catch (e) {
        console.error('Failed to delete coupon', e)
        toast.error('Failed to delete coupon')
    } finally {
        isDeleting.value = false
        isDeleteModalOpen.value = false
        couponToDelete.value = null
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
