<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <div class="max-w-4xl mx-auto mb-8">
      <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 font-medium mb-4">
        <NuxtLink to="/vendor/managed-shop" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Manage Shop</NuxtLink>
        <ChevronRight class="w-4 h-4" />
        <span class="text-slate-900 dark:text-white font-bold">Custom Pages</span>
      </div>
      
      <div class="flex items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ view === 'list' ? 'Custom Pages' : (editingPageId ? 'Edit Page' : 'Add New Page') }}</h1>
          <p class="text-slate-500 dark:text-slate-400 font-semibold mt-1">
            {{ view === 'list' ? "Manage your shop's policies and informational pages." : "Create or edit your custom page content." }}
          </p>
        </div>
        <div class="flex items-center gap-3">
          <button v-if="view === 'form'" @click="cancelEdit" class="px-6 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-black rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-700 transition-all active:scale-95">
            Cancel
          </button>
          <button v-if="view === 'form'" @click="saveCurrentPage" class="px-6 py-3 bg-indigo-600 text-white font-black rounded-2xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all active:scale-95 flex items-center gap-2">
            <Save class="w-5 h-5" />
            Save Page
          </button>
          <button v-if="view === 'list'" @click="addNewPage" class="px-6 py-3 bg-slate-900 dark:bg-slate-800 text-white font-black rounded-2xl shadow-lg shadow-slate-900/10 hover:bg-slate-800 dark:hover:bg-slate-700 transition-all active:scale-95 flex items-center gap-2">
            <Plus class="w-5 h-5" />
            Add New Page
          </button>
        </div>
      </div>
    </div>

    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
    </div>
    
    <div v-else-if="view === 'list'" class="max-w-4xl mx-auto space-y-4">
      <div v-if="pages.length === 0" class="text-center py-12 bg-white dark:bg-slate-900 rounded-[24px] border border-slate-100 dark:border-slate-800 border-dashed transition-colors duration-300">
        <FileText class="w-12 h-12 text-slate-300 dark:text-slate-700 mx-auto mb-3" />
        <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300">No custom pages yet</h3>
        <p class="text-sm text-slate-500 dark:text-slate-500 mt-1">Create policies, about us, or other informational pages.</p>
      </div>
      <div 
        v-else
        v-for="page in pages" 
        :key="page.id"
        class="bg-white dark:bg-slate-900 p-6 rounded-[24px] border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-all group flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition-colors duration-300"
      >
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-indigo-500">
            <FileText class="w-6 h-6 text-white" />
          </div>
          <div>
            <h3 class="text-lg font-black text-slate-800 dark:text-white tracking-tight">{{ page.title }}</h3>
            <p class="text-sm text-slate-400 dark:text-slate-500 font-bold">Last updated: {{ formatDate(page.updatedAt) }}</p>
          </div>
        </div>
        
        <div class="flex items-center gap-2 self-end sm:self-auto">
          <button @click="editPage(page)" class="p-3 bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-slate-700 rounded-xl transition-all">
            <Edit3 class="w-5 h-5" />
          </button>
          <button @click="confirmDelete(page)" class="p-3 bg-slate-50 dark:bg-slate-800 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-slate-700 rounded-xl transition-all">
            <Trash2 class="w-5 h-5" />
          </button>
        </div>
      </div>
    </div>

    <div v-else-if="view === 'form'" class="max-w-4xl mx-auto bg-white dark:bg-slate-900 rounded-[32px] border border-slate-100 dark:border-slate-800 shadow-sm p-8 space-y-8 transition-colors duration-300">
      <div class="space-y-6">
        <div class="space-y-2">
          <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider ml-1">Page Title</label>
          <input 
            v-model="form.title" 
            @input="generateSlug"
            type="text" 
            placeholder="e.g. Privacy Policy" 
            class="w-full h-14 px-6 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200"
          >
        </div>
        
        <div class="space-y-2">
          <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider ml-1">URL Slug</label>
          <div class="flex items-center">
            <span class="h-14 px-4 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 border-r-0 rounded-l-2xl flex items-center text-slate-500 dark:text-slate-400 font-medium text-sm">/pages/</span>
            <input 
              v-model="form.slug" 
              type="text" 
              placeholder="privacy-policy" 
              class="flex-1 h-14 px-6 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-r-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200"
            >
          </div>
        </div>

        <div class="space-y-2">
          <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider ml-1">Page Content</label>
          <div class="border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
            <AppRichEditor v-model="form.content" />
          </div>
        </div>
      </div>
    </div>
    
    <AppConfirmationModal
      :isOpen="isDeleteModalOpen"
      title="Delete Custom Page"
      message="Are you sure you want to delete this page? This action cannot be undone."
      confirmText="Delete"
      confirmColor="bg-red-600 hover:bg-red-700"
      @close="isDeleteModalOpen = false"
      @confirm="deletePage"
    />
  </div>
</template>

<script setup>
import { 
  ChevronRight, 
  Plus, 
  FileText, 
  Edit3,
  Trash2,
  Save
} from 'lucide-vue-next'
import { ref, reactive, onMounted } from 'vue'

definePageMeta({
  middleware: 'auth',
  permissions: 'settings.view'
})

const { $toast } = useNuxtApp()
const { getAll, createItem } = useCrud()
const router = useRouter()

const view = ref('list') // 'list' or 'form'
const pending = ref(true)
const pages = ref([])

const editingPageId = ref(null)
const isDeleteModalOpen = ref(false)
const pageToDelete = ref(null)

const form = reactive({
  title: '',
  slug: '',
  content: ''
})

const generateSlug = () => {
  if (!editingPageId.value) {
    form.slug = form.title.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '')
  }
}

const formatDate = (dateString) => {
  if (!dateString) return 'Just now'
  return new Date(dateString).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' })
}

const loadSettings = async () => {
  try {
    pending.value = true
    const response = await getAll('/vendor/settings?group=custom_pages')
    if (response.data?.pages) {
      let loadedPages = response.data.pages
      if (typeof loadedPages === 'string') {
        try {
          loadedPages = JSON.parse(loadedPages)
        } catch(e) {}
      }
      pages.value = Array.isArray(loadedPages) ? loadedPages : []
    }
  } catch (error) {
    if (error.response?.status !== 404) {
      $toast.error('Failed to load custom pages')
    }
  } finally {
    pending.value = false
  }
}

const saveSettings = async (updatedPages) => {
  try {
    await createItem('/vendor/settings', {
      group: 'custom_pages',
      settings: {
        pages: updatedPages
      }
    })
    pages.value = updatedPages
    router.push('/vendor/managed-shop/custom-pages')
    return true
  } catch (error) {
    console.error(error)
    $toast.error('Failed to save settings')
    return false
  }
}

const addNewPage = () => {
  form.title = ''
  form.slug = ''
  form.content = ''
  editingPageId.value = null
  view.value = 'form'
}

const editPage = (page) => {
  form.title = page.title
  form.slug = page.slug
  form.content = page.content
  editingPageId.value = page.id
  view.value = 'form'
}

const cancelEdit = () => {
  view.value = 'list'
}

const saveCurrentPage = async () => {
  if (!form.title || !form.slug) {
    $toast.error('Title and Slug are required')
    return
  }

  const newPage = {
    id: editingPageId.value || Date.now().toString(),
    title: form.title,
    slug: form.slug,
    content: form.content,
    updatedAt: new Date().toISOString()
  }

  let updatedPages = [...pages.value]
  if (editingPageId.value) {
    const index = updatedPages.findIndex(p => p.id === editingPageId.value)
    if (index !== -1) {
      updatedPages[index] = newPage
    }
  } else {
    updatedPages.push(newPage)
  }

  const success = await saveSettings(updatedPages)
  if (success) {
    $toast.success(editingPageId.value ? 'Page updated successfully!' : 'Page created successfully!')
    view.value = 'list'
  }
}

const confirmDelete = (page) => {
  pageToDelete.value = page
  isDeleteModalOpen.value = true
}

const deletePage = async () => {
  if (!pageToDelete.value) return
  
  const updatedPages = pages.value.filter(p => p.id !== pageToDelete.value.id)
  const success = await saveSettings(updatedPages)
  
  if (success) {
    $toast.success('Page deleted successfully!')
  }
  
  isDeleteModalOpen.value = false
  pageToDelete.value = null
}

onMounted(() => {
  loadSettings()
})
</script>
