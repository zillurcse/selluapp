<template>
  <div class="min-h-screen bg-[#f8f9fa] dark:bg-gray-900 p-10">
    <!-- Breadcrumbs -->
    <nav class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-300 mb-8">
      <NuxtLink to="/vendor" class="hover:text-black dark:hover:text-white transition-colors">
        <HomeIcon class="w-4 h-4" />
      </NuxtLink>
      <ChevronRightIcon class="w-4 h-4 text-gray-400 dark:text-gray-500" />
      <span class="hover:text-black dark:hover:text-white cursor-pointer">Customers</span>
      <ChevronRightIcon class="w-4 h-4 text-gray-400 dark:text-gray-500" />
      <span class="text-black dark:text-white font-medium">List</span>
    </nav>

    <!-- Main Content Area -->
    <div v-if="loading" class="flex items-center justify-center min-h-[60vh]">
      <div class="animate-spin rounded-full h-12 w-12 border-4 border-indigo-500 border-t-transparent"></div>
    </div>
    <div v-else-if="items.length === 0" class="flex flex-col items-center justify-center min-h-[60vh] bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
      <!-- Empty State Illustration -->
      <div class="w-24 h-24 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6 shadow-inner relative">
         <div class="absolute inset-0 bg-gray-100 dark:bg-gray-600 rounded-full scale-110 opacity-50"></div>
         <FilePlusIcon class="w-10 h-10 text-gray-400 dark:text-gray-400 relative z-10" />
      </div>

      <h1 class="text-2xl font-bold text-[#1e293b] dark:text-white mb-2">No customers found</h1>
      <p class="text-center text-gray-500 dark:text-gray-400 max-w-md mb-8 leading-relaxed">
        Start building your customer base! Click below to add your first customer.
      </p>

      <button 
        @click="openAddDrawer"
        class="bg-primary-600 text-white px-6 py-2.5 rounded-lg flex items-center space-x-2 hover:bg-primary-700 dark:bg-primary-700 dark:hover:bg-primary-600 transition-all shadow-lg hover:shadow-xl active:scale-95"
      >
        <PlusIcon class="w-5 h-5" />
        <span class="font-medium">Add Customer</span>
      </button>
    </div>

    <!-- Data Table -->
    <div v-else class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-10 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">All Customers</h2>
            <button 
                @click="openAddDrawer"
                class="bg-primary-600 text-white px-4 py-2 text-sm rounded-lg flex items-center space-x-2 hover:bg-primary-700 dark:bg-primary-700 dark:hover:bg-primary-600 transition-all shadow-sm active:scale-95"
            >
                <PlusIcon class="w-4 h-4" />
                <span class="font-medium">Add New</span>
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-100 dark:border-gray-600">
                        <th class="py-4 px-6 font-semibold text-xs text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                        <th class="py-4 px-6 font-semibold text-xs text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contact</th>
                        <th class="py-4 px-6 font-semibold text-xs text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="py-4 px-6 font-semibold text-xs text-gray-500 dark:text-gray-300 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr v-for="customer in items" :key="customer.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900 dark:text-white">{{ customer.name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">ID: #{{ customer.id }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300 mb-1">
                                <MailIcon class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" />
                                {{ customer.email || 'N/A' }}
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                <PhoneIcon class="w-3.5 h-3.5 text-gray-400 dark:text-gray-500" />
                                {{ customer.phone }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="[
                                'px-2.5 py-1 text-xs font-bold rounded-full border',
                                customer.is_active ? 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-900/30' : 'bg-rose-50 text-rose-600 border-rose-100 dark:bg-rose-900/20 dark:text-rose-400 dark:border-rose-900/30'
                            ]">
                                {{ customer.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                           <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                               <button @click="openEditDrawer(customer)" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors tooltip-target dark:bg-blue-900/20 dark:hover:bg-blue-900/40 dark:text-blue-400" title="Edit Customer">
                                   <EditIcon class="w-4 h-4" />
                               </button>
                               <button @click="confirmDelete(customer.id)" class="p-2 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors tooltip-target dark:bg-rose-900/20 dark:hover:bg-rose-900/40 dark:text-rose-400" title="Delete Customer">
                                   <TrashIcon class="w-4 h-4" />
                               </button>
                           </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <VendorCustomerFormDrawer ref="customerFormDrawer" @saved="fetchItems('/vendor/customers')" />

    <AppConfirmationModal
        :isOpen="isDeleteModalOpen"
        title="Delete Customer"
        message="Are you sure you want to delete this customer? This action cannot be undone."
        icon="TrashIcon"
        confirmText="Delete Customer"
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
  FilePlusIcon, 
  PlusIcon,
  PhoneIcon,
  MailIcon,
  TrashIcon,
  EditIcon
} from 'lucide-vue-next'
import { ref, onMounted } from 'vue'
import { toast } from 'vue-sonner'

const { getAll, createItem, deleteItem } = useCrud()

const items = ref([])
const loading = ref(false)

const fetchItems = async (url) => {
    loading.value = true
    try {
        const response = await getAll(url)
        items.value = response || []
    } catch (e) {
        console.error('Failed to fetch customers', e)
    } finally {
        loading.value = false
    }
}
const customerFormDrawer = ref(null)

const isDeleteModalOpen = ref(false)
const customerToDelete = ref(null)
const isDeleting = ref(false)

onMounted(() => {
    fetchItems('/vendor/customers')
})

const openAddDrawer = () => {
    customerFormDrawer.value?.openAddDrawer()
}

const openEditDrawer = (customer) => {
    customerFormDrawer.value?.openEditDrawer(customer)
}

const confirmDelete = (id) => {
    customerToDelete.value = id
    isDeleteModalOpen.value = true
}

const handleConfirmDelete = async () => {
    if (!customerToDelete.value) return
    isDeleting.value = true
    try {
        await deleteItem(`/vendor/customers/${customerToDelete.value}`)
        toast.success('Customer deleted successfully')
        fetchItems('/vendor/customers') // Refresh the list
    } catch (e) {
        console.error('Failed to delete customer', e)
    } finally {
        isDeleting.value = false
        isDeleteModalOpen.value = false
        customerToDelete.value = null
    }
}



definePageMeta({
  middleware: 'auth'
})
</script>

<style scoped>
/* Optional: Hide scrollbar for Chrome, Safari and Opera */
.overflow-y-auto::-webkit-scrollbar {
  display: none;
}

/* Optional: Hide scrollbar for IE, Edge and Firefox */
.overflow-y-auto {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}
</style>
