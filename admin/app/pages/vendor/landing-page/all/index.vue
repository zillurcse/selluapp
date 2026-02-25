<template>
  <div class="flex flex-col h-full bg-gray-50/50 dark:bg-slate-950 min-h-screen transition-colors duration-300 p-10">
    <!-- Header -->
    <div class="flex items-center justify-between px-8 py-6 bg-white dark:bg-slate-900 border-b border-gray-100 dark:border-slate-800 transition-colors duration-300">
      <div class="flex items-center">
        <button @click="$router.back()" class="p-2 bg-black text-white rounded-full hover:bg-gray-800 transition-colors mr-6 shadow-sm">
          <ArrowLeft class="w-5 h-5" />
        </button>
        <div>
          <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">All Landing Pages</h1>
          <p class="text-gray-500 dark:text-slate-400 mt-1">View and manage all the landing pages you have created.</p>
        </div>
      </div>
      <NuxtLink to="/vendor/landing-page/single" class="bg-black dark:bg-slate-800 text-white px-6 py-3 rounded-xl font-bold hover:bg-gray-800 dark:hover:bg-slate-700 transition-all flex items-center shadow-lg active:scale-95">
        <Plus class="w-5 h-5 mr-2" />
        Create New
      </NuxtLink>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto w-full p-8">
      <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden transition-colors duration-300">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
              <th class="px-6 py-5 text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Page Information</th>
              <th class="px-6 py-5 text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Template</th>
              <th class="px-6 py-5 text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Status</th>
              <th class="px-6 py-5 text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50 dark:divide-slate-800">
            <tr v-for="page in landingPages" :key="page.id" class="hover:bg-gray-50/50 dark:hover:bg-slate-800 transition-colors group">
              <td class="px-6 py-5">
                <div class="flex items-center">
                  <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-slate-800 flex items-center justify-center mr-4 group-hover:bg-white dark:group-hover:bg-slate-700 transition-colors border border-transparent group-hover:border-gray-100 dark:group-hover:border-slate-700">
                    <LayoutTemplate class="w-6 h-6 text-gray-400" />
                  </div>
                  <div>
                    <div class="flex items-center gap-2">
                      <div class="font-bold text-gray-900 dark:text-slate-100">{{ page.title }}</div>
                      <span v-if="page.is_home" class="px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 text-[10px] font-black rounded-full uppercase tracking-tighter flex items-center shadow-sm border border-indigo-200 dark:border-indigo-800">
                        <Home class="w-2.5 h-2.5 mr-1" /> Home Page
                      </span>
                    </div>
                    <div class="text-xs text-gray-400 dark:text-slate-500 mt-1 flex items-center">
                      <Link2 class="w-3 h-3 mr-1" />
                      /l/{{ page.slug }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-5">
                <span class="inline-flex items-center px-3 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-bold rounded-lg uppercase tracking-wide">
                  {{ page.template_name }}
                </span>
              </td>
              <td class="px-6 py-5">
                <button 
                  @click="handleToggleStatus(page)"
                  class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold transition-all active:scale-95"
                  :class="page.status === 'active' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 hover:bg-green-200' : 'bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400 hover:bg-gray-200'"
                >
                  <span class="w-1.5 h-1.5 rounded-full mr-2" :class="page.status === 'active' ? 'bg-green-500' : 'bg-gray-400'"></span>
                  {{ page.status === 'active' ? 'Active' : 'Draft' }}
                </button>
              </td>
              <td class="px-6 py-5 text-right">
                <div class="flex items-center justify-end space-x-2">
                  <a :href="`/l/${page.slug}`" target="_blank" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="View">
                    <Eye class="w-5 h-5" />
                  </a>
                  <NuxtLink :to="page.landing_page_type === 'multiple' ? `/vendor/landing-page/multiple?id=${page.id}` : `/vendor/landing-page/single?id=${page.id}`" class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="Edit">
                    <Pencil class="w-5 h-5" />
                  </NuxtLink>
                  <button @click="copyLink(page.slug)" class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-all" title="Copy Link">
                    <Copy class="w-5 h-5" />
                  </button>
                  <button @click="handleSetHome(page)" :class="page.is_home ? 'text-indigo-600 bg-indigo-50' : 'text-gray-400 hover:text-indigo-600 hover:bg-indigo-50'" class="p-2 rounded-lg transition-all" title="Set as Home Page">
                    <Home class="w-5 h-5" />
                  </button>
                  <button @click="handleDelete(page.id)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete">
                    <Trash2 class="w-5 h-5" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Empty State -->
        <div v-if="landingPages.length === 0" class="flex flex-col items-center justify-center py-20 bg-white dark:bg-slate-900">
          <div class="bg-gray-50 dark:bg-slate-800 p-6 rounded-full mb-6">
            <LayoutTemplate class="w-12 h-12 text-gray-300 dark:text-slate-600" />
          </div>
          <h3 class="text-xl font-bold text-gray-900 dark:text-white">No landing pages found</h3>
          <p class="text-gray-500 dark:text-slate-400 mt-2 mb-8">You haven't created any landing pages yet.</p>
          <NuxtLink to="/vendor/landing-page/single" class="bg-black dark:bg-slate-800 text-white px-8 py-3 rounded-xl font-bold hover:bg-gray-800 dark:hover:bg-slate-700 transition-all flex items-center shadow-lg active:scale-95">
            <Plus class="w-5 h-5 mr-2" />
            Create Your First Page
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  ArrowLeft, 
  Plus, 
  LayoutTemplate, 
  Eye, 
  Pencil, 
  Copy, 
  Trash2,
  Link2,
  Home
} from 'lucide-vue-next'
import { toast } from 'vue-sonner'

definePageMeta({
  layout: 'default',
  middleware: 'auth',
  permissions: 'landing_pages.view'
})

const { getAll, deleteItem, updateItem } = useCrud()
const landingPages = ref([])

// Fetch landing pages
const fetchLandingPages = async () => {
  try {
    const res = await getAll('/vendor/landing-pages')
    landingPages.value = res || []
  } catch (error) {
    console.error('Failed to fetch landing pages:', error)
  }
}

onMounted(() => {
  fetchLandingPages()
})

const copyLink = (slug) => {
  if (typeof window !== 'undefined') {
    const url = `${window.location.origin}/l/${slug}`
    navigator.clipboard.writeText(url)
    toast.success('Link copied to clipboard!')
  }
}

const handleToggleStatus = async (page) => {
  try {
    const newStatus = page.status === 'active' ? 'draft' : 'active'
    await updateItem(`/vendor/landing-pages/${page.id}`, { status: newStatus })
    page.status = newStatus
  } catch (error) {
    console.error('Error toggling status:', error)
  }
}

const handleSetHome = async (page) => {
  try {
    const newHomeStatus = !page.is_home
    await updateItem(`/vendor/landing-pages/${page.id}`, { is_home: newHomeStatus })
    // Fetch all to update the list since handleSetHome in backend unsets others
    fetchLandingPages()
    toast.success(newHomeStatus ? 'Page set as home!' : 'Home page status removed.')
  } catch (error) {
    console.error('Error setting home status:', error)
  }
}

const handleDelete = async (id) => {
  if (confirm('Are you sure you want to delete this landing page?')) {
     try {
       await deleteItem(id, '/vendor/landing-pages')
       fetchLandingPages()
     } catch (error) {
       console.error('Error deleting landing page:', error)
     }
  }
}
</script>
