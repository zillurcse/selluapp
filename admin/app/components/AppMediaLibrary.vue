<template>
  <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="close"></div>
    
    <!-- Modal Content -->
    <div class="bg-white dark:bg-slate-900 w-full max-w-6xl h-[85vh] rounded-2xl shadow-2xl relative z-10 flex flex-col overflow-hidden animate-in zoom-in-95 duration-200 border border-gray-100 dark:border-slate-800">
      
      <!-- Header -->
      <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-white dark:bg-slate-900 sticky top-0 z-20">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <Library class="w-5 h-5 text-primary-500" />
                Media Gallery
            </h3>
            <p class="text-[10px] text-gray-400 dark:text-slate-500 uppercase tracking-widest font-bold">Select and reuse your media assets</p>
        </div>
        <button @click="close" class="p-2 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-full transition-colors text-gray-500">
            <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Navigation & Filters -->
      <div class="px-6 py-3 border-b border-gray-50 dark:border-slate-800/50 flex flex-wrap items-center justify-between gap-4 bg-gray-50/30 dark:bg-slate-900">
        <div class="flex p-1 bg-gray-100 dark:bg-slate-800 rounded-xl">
            <button 
                @click="activeTab = 'library'" 
                class="px-5 py-1.5 text-xs font-black rounded-lg transition-all uppercase tracking-tight"
                :class="activeTab === 'library' ? 'bg-white dark:bg-slate-700 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-slate-300'"
            >
                Browse Library
            </button>
            <button 
                @click="activeTab = 'upload'" 
                class="px-5 py-1.5 text-xs font-black rounded-lg transition-all uppercase tracking-tight"
                :class="activeTab === 'upload' ? 'bg-white dark:bg-slate-700 text-primary-600 dark:text-primary-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-slate-300'"
            >
                Upload Files
            </button>
        </div>

        <div v-if="activeTab === 'library'" class="flex items-center gap-3 flex-grow max-w-lg">
            <div class="relative w-full">
                <Search class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" />
                <input 
                    v-model="searchQuery" 
                    type="text" 
                    placeholder="Search by file name..." 
                    class="w-full pl-10 pr-4 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-primary-500/20 outline-none transition-all shadow-sm"
                    @input="debounceSearch"
                >
            </div>
            <select v-model="filterType" class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl text-xs font-bold px-3 py-2 focus:ring-2 focus:ring-primary-500/20 outline-none cursor-pointer shadow-sm">
                <option value="">All Media</option>
                <option value="image">Images</option>
                <option value="video">Videos</option>
                <option value="document">Documents</option>
            </select>
        </div>
      </div>

      <!-- Main Body -->
      <div class="flex-grow overflow-y-auto p-6 scrollbar-thin scrollbar-thumb-gray-200 dark:scrollbar-thumb-slate-700">
        
        <!-- Upload Tab -->
        <div v-if="activeTab === 'upload'" class="h-full flex flex-col items-center justify-center border-2 border-dashed border-gray-200 dark:border-slate-800 rounded-3xl bg-gray-50/50 dark:bg-slate-900/50 p-12 transition-all hover:bg-primary-50/10 dark:hover:bg-primary-900/5 hover:border-primary-300 dark:hover:border-primary-500/50 relative group">
            <input 
                type="file" 
                multiple 
                class="absolute inset-0 opacity-0 cursor-pointer z-10" 
                @change="handleFilesUpload"
                accept="image/*,video/*,application/pdf"
            >
            <div class="text-center">
                <div class="w-24 h-24 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center mx-auto mb-6 group-hover:scale-110 group-hover:rotate-3 transition-all duration-500 shadow-xl shadow-primary-500/10">
                    <UploadCloud class="w-12 h-12 text-primary-600 dark:text-primary-400" />
                </div>
                <h4 class="text-2xl font-black text-gray-900 dark:text-white mb-3">Drop files here</h4>
                <p class="text-sm text-gray-500 dark:text-slate-400 mb-8 max-w-sm mx-auto font-medium">Click or drag any media files you want to add to your library. Support for Images, Videos, and PDFs.</p>
                
                <div v-if="uploadingCount > 0" class="flex flex-col items-center gap-3">
                    <div class="w-72 h-3 bg-gray-200 dark:bg-slate-800 rounded-full overflow-hidden shadow-inner">
                        <div class="h-full bg-gradient-to-r from-primary-500 to-primary-600 animate-pulse transition-all duration-300" :style="{ width: uploadProgress + '%' }"></div>
                    </div>
                    <span class="text-xs font-black text-primary-600 dark:text-primary-400 animate-bounce uppercase tracking-widest">Uploading {{ uploadingCount }} files...</span>
                </div>

                <div v-else class="flex flex-wrap items-center justify-center gap-4 text-[10px] font-black uppercase text-gray-400 tracking-widest">
                    <span class="px-2 py-1 bg-gray-100 dark:bg-slate-800 rounded">MAX 5MB</span>
                    <span class="px-2 py-1 bg-gray-100 dark:bg-slate-800 rounded">JPEG/PNG/SVG/MP4</span>
                </div>
            </div>
        </div>

        <!-- Library Tab -->
        <div v-else class="h-full">
            <div v-if="loading && files.length === 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
                <div v-for="i in 18" :key="i" class="aspect-square bg-gray-100 dark:bg-slate-800 rounded-2xl animate-pulse"></div>
            </div>
            
            <div v-else-if="files.length === 0" class="h-full flex flex-col items-center justify-center text-center py-20">
                <div class="w-32 h-32 bg-gray-50 dark:bg-slate-800/50 rounded-full flex items-center justify-center mb-6">
                    <FolderOpen class="w-16 h-16 text-gray-200 dark:text-slate-700" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Your library is empty</h3>
                <p class="text-sm text-gray-500 dark:text-slate-400 mt-2">Start by uploading some professional media for your products.</p>
                <button @click="activeTab = 'upload'" class="mt-6 px-6 py-2 bg-primary-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-primary-500/20 hover:bg-primary-700 transition-all">
                    Upload Now
                </button>
            </div>

            <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6 pb-20">
                <div 
                    v-for="file in files" 
                    :key="file.id" 
                    class="group aspect-square rounded-2xl bg-white dark:bg-slate-800 border-2 overflow-hidden relative transition-all cursor-pointer shadow-sm hover:shadow-xl hover:-translate-y-1"
                    :class="isSelected(file) ? 'border-primary-500 ring-4 ring-primary-500/10' : 'border-gray-50 dark:border-slate-800 hover:border-primary-300 dark:hover:border-primary-700'"
                    @click="toggleSelection(file)"
                >
                    <!-- Image Preview -->
                    <img v-if="file.type === 'image'" :src="file.file_url" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy" />
                    
                    <!-- File Previews -->
                    <div v-else class="w-full h-full flex flex-col items-center justify-center p-4 text-center bg-gray-50/50 dark:bg-slate-900/50">
                        <div class="p-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm mb-3 transition-transform group-hover:scale-110 duration-300">
                             <FileCode v-if="file.type === 'document'" class="w-10 h-10 text-orange-400" />
                             <Video v-else-if="file.type === 'video'" class="w-10 h-10 text-blue-400" />
                             <File v-else class="w-10 h-10 text-gray-400" />
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-tight truncate w-full px-2 text-gray-600 dark:text-slate-400">{{ file.file_original_name }}</span>
                        <span class="text-[8px] font-bold text-gray-400 dark:text-slate-500 mt-1 uppercase">{{ (file.file_size / 1024).toFixed(0) }} KB</span>
                    </div>

                    <!-- Overlay for selection -->
                    <div class="absolute inset-0 bg-primary-600/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    
                    <!-- Selection Indicator -->
                    <div v-if="isSelected(file)" class="absolute top-3 right-3 z-10 animate-in zoom-in-75 duration-200">
                        <div class="bg-primary-600 text-white rounded-full p-1.5 shadow-xl ring-2 ring-white dark:ring-slate-900">
                            <CheckCircle2 class="w-4 h-4" />
                        </div>
                    </div>
                    
                    <!-- Multi-select Count -->
                    <div v-if="isSelected(file) && multiple" class="absolute top-3 left-3 z-10 animate-in slide-in-from-left-4 duration-200">
                         <div class="bg-primary-600 text-white text-[10px] font-black w-6 h-6 flex items-center justify-center rounded-lg shadow-xl ring-2 ring-white dark:ring-slate-900">
                            {{ getSelectionIndex(file) + 1 }}
                         </div>
                    </div>

                    <!-- Delete Button -->
                    <button 
                        @click.stop="confirmDelete(file)" 
                        type="button"
                        class="absolute bottom-3 right-3 p-2.5 bg-white/95 dark:bg-slate-900/95 text-red-500 rounded-xl opacity-0 group-hover:opacity-100 transition-all hover:bg-red-500 hover:text-white shadow-xl translate-y-2 group-hover:translate-y-0"
                    >
                        <Trash2 class="w-4 h-4" />
                    </button>

                    <!-- Type Badge -->
                    <div class="absolute bottom-3 left-3 px-2 py-0.5 bg-black/50 backdrop-blur-md rounded text-[8px] font-black text-white uppercase tracking-widest opacity-0 group-hover:opacity-100 transition-opacity">
                        {{ file.extension }}
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="pagination.last_page > 1" class="sticky bottom-4 inset-x-0 flex justify-center pb-2">
                <nav class="flex items-center gap-1.5 p-1.5 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border border-gray-100 dark:border-slate-800 rounded-2xl shadow-2xl">
                    <button 
                        @click="fetchFiles(pagination.current_page - 1)" 
                        :disabled="pagination.current_page === 1"
                        class="p-2.5 rounded-xl hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors disabled:opacity-20"
                    >
                        <ChevronLeft class="w-5 h-5" />
                    </button>
                    
                    <div class="flex items-center px-4 gap-2">
                        <span class="text-xs font-black uppercase text-gray-400">Page</span>
                        <span class="text-sm font-black text-primary-600">{{ pagination.current_page }}</span>
                        <span class="text-xs font-black uppercase text-gray-400">of</span>
                        <span class="text-sm font-black text-gray-900 dark:text-white">{{ pagination.last_page }}</span>
                    </div>

                    <button 
                        @click="fetchFiles(pagination.current_page + 1)" 
                        :disabled="pagination.current_page === pagination.last_page"
                        class="p-2.5 rounded-xl hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors disabled:opacity-20"
                    >
                        <ChevronRight class="w-5 h-5" />
                    </button>
                </nav>
            </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="px-8 py-5 border-t border-gray-100 dark:border-slate-800 bg-white dark:bg-slate-900 flex flex-col sm:flex-row items-center justify-between gap-4 z-20">
        <div class="flex items-center gap-4">
            <div class="px-4 py-2 bg-primary-50 dark:bg-primary-900/20 rounded-xl border border-primary-100 dark:border-primary-900/50">
                <span class="text-[10px] font-black text-primary-400 uppercase tracking-widest block leading-none mb-1">Items Selected</span>
                <span class="text-lg font-black text-primary-600 dark:text-primary-400 leading-none">{{ selectedFiles.length }}</span>
            </div>
            <button v-if="selectedFiles.length > 0" @click="selectedFiles = []" class="text-xs font-bold text-red-500 hover:underline">Clear Selection</button>
        </div>
        
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <button @click="close" type="button" class="flex-1 sm:flex-none px-8 py-3 text-sm font-black uppercase tracking-tight text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-2xl transition-all">
                Close
            </button>
            <button 
                @click="finalizeSelection" 
                :disabled="selectedFiles.length === 0"
                class="flex-1 sm:flex-none px-10 py-3 text-sm font-black uppercase tracking-tight bg-primary-600 text-white rounded-2xl shadow-xl shadow-primary-500/25 hover:bg-primary-700 hover:scale-[1.02] active:scale-95 disabled:opacity-50 disabled:shadow-none disabled:scale-100 transition-all flex items-center justify-center gap-2 group"
            >
                Confirm Selection
                <ArrowRight class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
            </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
    X, 
    UploadCloud, 
    Library, 
    Search, 
    FolderOpen, 
    CheckCircle2, 
    Trash2, 
    ChevronLeft, 
    ChevronRight,
    ArrowRight,
    FileCode,
    Video,
    File
} from 'lucide-vue-next'
import { toast } from 'vue-sonner'

const props = defineProps({
    show: Boolean,
    multiple: { type: Boolean, default: false },
    typeLabel: { type: String, default: 'Items' },
    allowedTypes: { type: Array, default: () => [] }
})

const emit = defineEmits(['close', 'select'])

const { getAll, deleteItem, createItem } = useCrud()

// State
const activeTab = ref('library')
const loading = ref(false)
const files = ref([])
const selectedFiles = ref([])
const uploadingCount = ref(0)
const uploadProgress = ref(0)
const searchQuery = ref('')
const filterType = ref('')
const pagination = ref({
    current_page: 1,
    last_page: 1
})

// Methods
const close = () => {
    selectedFiles.value = []
    emit('close')
}

const fetchFiles = async (page = 1) => {
    loading.value = true
    try {
        const queryParams = {
            page,
            search: searchQuery.value,
            type: filterType.value,
            limit: 24
        }
        const res = await getAll('/vendor/media', queryParams)
        if (res) {
            files.value = res.data
            pagination.value = {
                current_page: res.current_page,
                last_page: res.last_page
            }
        }
    } catch (e) {
        toast.error('Failed to load media library')
    } finally {
        loading.value = false
    }
}

let timeout = null
const debounceSearch = () => {
    clearTimeout(timeout)
    timeout = setTimeout(() => {
        fetchFiles(1)
    }, 500)
}

watch(filterType, () => fetchFiles(1))

const handleFilesUpload = async (event) => {
    const filesToUpload = Array.from(event.target.files)
    if (filesToUpload.length === 0) return
    
    uploadingCount.value = filesToUpload.length
    uploadProgress.value = 10
    
    let successCount = 0
    const totalFiles = filesToUpload.length
    
    for (let i = 0; i < totalFiles; i++) {
        const file = filesToUpload[i]
        try {
            const formData = new FormData()
            formData.append('file', file)
            
            const res = await createItem('/vendor/media', formData, null, false)
            if (res) {
                successCount++
                uploadProgress.value = 10 + Math.round((successCount / totalFiles) * 90)
            }
        } catch (e) {
            toast.error(`Failed to upload ${file.name}`)
        }
    }
    
    if (successCount > 0) {
        toast.success(`Successfully uploaded ${successCount} files to library`)
        activeTab.value = 'library'
        fetchFiles(1)
    }
    
    uploadingCount.value = 0
    uploadProgress.value = 0
}

const isSelected = (file) => {
    return selectedFiles.value.some(f => f.id === file.id)
}

const getSelectionIndex = (file) => {
    return selectedFiles.value.findIndex(f => f.id === file.id)
}

const toggleSelection = (file) => {
    if (props.multiple) {
        const index = getSelectionIndex(file)
        if (index > -1) {
            selectedFiles.value.splice(index, 1)
        } else {
            selectedFiles.value.push(file)
        }
    } else {
        selectedFiles.value = [file]
    }
}

const confirmDelete = async (file) => {
    if (confirm('Are you sure you want to delete this file permanently from your library?')) {
        try {
            await deleteItem(file.id, '/vendor/media')
            toast.success('File deleted')
            fetchFiles(pagination.value.current_page)
            // Remove from selection if deleted
            const sIndex = getSelectionIndex(file)
            if (sIndex > -1) selectedFiles.value.splice(sIndex, 1)
        } catch (e) {
            toast.error('Failed to delete file')
        }
    }
}


const finalizeSelection = () => {
    emit('select', props.multiple ? selectedFiles.value : selectedFiles.value[0])
    close()
}

// Init
watch(() => props.show, (newVal) => {
    if (newVal) {
        fetchFiles(1)
        selectedFiles.value = []
    }
})
</script>
