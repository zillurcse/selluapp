<template>
  <div class="min-h-screen bg-[#f8f9fa] dark:bg-gray-900 p-10">
    <!-- Breadcrumbs -->
    <nav class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-300 mb-8">
      <NuxtLink to="/vendor" class="hover:text-black dark:hover:text-white transition-colors">
        <HomeIcon class="w-4 h-4" />
      </NuxtLink>
      <ChevronRightIcon class="w-4 h-4 text-gray-400 dark:text-gray-500" />
      <span class="text-black dark:text-white font-medium">Newsletter Subscribers</span>
    </nav>

    <!-- Main Content Area -->
    <div v-if="loading" class="flex items-center justify-center min-h-[60vh]">
      <div class="animate-spin rounded-full h-12 w-12 border-4 border-indigo-500 border-t-transparent"></div>
    </div>
    
    <div v-else-if="items.length === 0" class="flex flex-col items-center justify-center min-h-[60vh] bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
      <div class="w-24 h-24 bg-gray-50 dark:bg-gray-700 rounded-full flex items-center justify-center mb-6 shadow-inner relative">
         <div class="absolute inset-0 bg-gray-100 dark:bg-gray-600 rounded-full scale-110 opacity-50"></div>
         <MailIcon class="w-10 h-10 text-gray-400 dark:text-gray-400 relative z-10" />
      </div>

      <h1 class="text-2xl font-bold text-[#1e293b] dark:text-white mb-2">No subscribers found</h1>
      <p class="text-center text-gray-500 dark:text-gray-400 max-w-md mb-8 leading-relaxed">
        Your newsletter subscriber list is currently empty. Once users subscribe from your storefront, they will appear here.
      </p>
    </div>

    <!-- Data Table -->
    <div v-else class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-10 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Newsletter Subscribers</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-100 dark:border-gray-600">
                        <th class="py-4 px-6 font-semibold text-xs text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email Address</th>
                        <th class="py-4 px-6 font-semibold text-xs text-gray-500 dark:text-gray-300 uppercase tracking-wider">Subscribed At</th>
                        <th class="py-4 px-6 font-semibold text-xs text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="py-4 px-6 font-semibold text-xs text-gray-500 dark:text-gray-300 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    <tr v-for="subscriber in items" :key="subscriber.id" class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900 dark:text-white">{{ subscriber.email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600 dark:text-gray-300">
                                {{ new Date(subscriber.created_at).toLocaleDateString() }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="[
                                'px-2.5 py-1 text-xs font-bold rounded-full border',
                                subscriber.is_active ? 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-900/30' : 'bg-rose-50 text-rose-600 border-rose-100 dark:bg-rose-900/20 dark:text-rose-400 dark:border-rose-900/30'
                            ]">
                                {{ subscriber.is_active ? 'Subscribed' : 'Unsubscribed' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                           <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                               <button @click="confirmDelete(subscriber.id)" class="p-2 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors tooltip-target dark:bg-rose-900/20 dark:hover:bg-rose-900/40 dark:text-rose-400" title="Delete Subscriber">
                                   <TrashIcon class="w-4 h-4" />
                               </button>
                           </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div v-if="pagination && pagination.last_page > 1" class="p-6 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <span class="text-sm text-gray-500 dark:text-gray-400">
                Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} subscribers
            </span>
            <div class="flex items-center gap-2">
                <button 
                    @click="fetchItems(pagination.prev_page_url)" 
                    :disabled="!pagination.prev_page_url"
                    class="p-2 rounded-lg border border-gray-200 dark:border-gray-700 disabled:opacity-50"
                >
                    <ChevronLeftIcon class="w-4 h-4" />
                </button>
                <button 
                    @click="fetchItems(pagination.next_page_url)" 
                    :disabled="!pagination.next_page_url"
                    class="p-2 rounded-lg border border-gray-200 dark:border-gray-700 disabled:opacity-50"
                >
                    <ChevronRightIcon class="w-4 h-4" />
                </button>
            </div>
        </div>
    </div>

    <AppConfirmationModal
        :isOpen="isDeleteModalOpen"
        title="Remove Subscriber"
        message="Are you sure you want to remove this subscriber from your list?"
        icon="TrashIcon"
        confirmText="Remove"
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
  ChevronLeftIcon,
  MailIcon, 
  TrashIcon
} from 'lucide-vue-next'
import { ref, onMounted } from 'vue'
import { toast } from 'vue-sonner'

const { getAll, deleteItem } = useCrud()

const items = ref([])
const pagination = ref(null)
const loading = ref(false)

const fetchItems = async (url = '/vendor/newsletters') => {
    loading.value = true
    try {
        const response = await getAll(url)
        if (response && response.data) {
            items.value = response.data
            pagination.value = response
        } else {
            items.value = response || []
        }
    } catch (e) {
        console.error('Failed to fetch newsletter subscribers', e)
        toast.error('Failed to load subscribers')
    } finally {
        loading.value = false
    }
}

const isDeleteModalOpen = ref(false)
const subscriberToDelete = ref(null)
const isDeleting = ref(false)

onMounted(() => {
    fetchItems()
})

const confirmDelete = (id) => {
    subscriberToDelete.value = id
    isDeleteModalOpen.value = true
}

const handleConfirmDelete = async () => {
    if (!subscriberToDelete.value) return
    isDeleting.value = true
    try {
        await deleteItem(`/vendor/newsletters/${subscriberToDelete.value}`)
        toast.success('Subscriber removed successfully')
        fetchItems() 
    } catch (e) {
        console.error('Failed to delete subscriber', e)
        toast.error('Failed to remove subscriber')
    } finally {
        isDeleting.value = false
        isDeleteModalOpen.value = false
        subscriberToDelete.value = null
    }
}

definePageMeta({
  middleware: 'auth'
})
</script>
