<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <!-- Header Section -->
    <div class="max-w-4xl mx-auto mb-8">
      <div class="flex items-center gap-4">
        <button @click="$router.push('/vendor/attributes/categories')" class="p-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm active:scale-95 group">
          <ChevronLeft class="w-5 h-5 text-slate-600 dark:text-slate-300 group-hover:-translate-x-0.5 transition-transform" />
        </button>
        <div>
          <h1 class="text-2xl font-black text-slate-900 dark:text-white leading-tight tracking-tight">Create Category</h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 font-semibold opacity-80">Add a new category to organize your products.</p>
        </div>
      </div>
    </div>

    <div class="max-w-4xl mx-auto">
      <div class="bg-white dark:bg-slate-900 rounded-[32px] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.06)] dark:shadow-none border border-slate-200/60 dark:border-slate-800 p-8 md:p-12 transition-all">
        <form @submit.prevent="createCategory" class="space-y-10">
          <!-- Main Information Section -->
          <div class="space-y-6">
            <div class="flex items-center gap-3 mb-2">
              <div class="w-8 h-8 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                <LayoutGrid class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
              </div>
              <h3 class="text-lg font-black text-slate-900 dark:text-white">Basic Information</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <div class="space-y-2">
                <label class="block text-sm font-black text-slate-700 dark:text-slate-300 ml-1">Category Name <span class="text-rose-500">*</span></label>
                <input v-model="form.name" type="text" placeholder="e.g. Electronics" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium" required />
              </div>
              <div class="space-y-2">
                <label class="block text-sm font-black text-slate-700 dark:text-slate-300 ml-1">Parent Category</label>
                <div class="relative group">
                  <select v-model="form.parent_id" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium appearance-none">
                    <option :value="null">-- None (Top Level Parent) --</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                  </select>
                  <ChevronDown class="absolute right-5 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 pointer-events-none group-focus-within:rotate-180 transition-transform duration-300" />
                </div>
              </div>
            </div>

            <div class="space-y-2">
              <label class="block text-sm font-black text-slate-700 dark:text-slate-300 ml-1">Description</label>
              <textarea v-model="form.description" rows="3" placeholder="Describe this category for better SEO..." class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium resize-none"></textarea>
            </div>
          </div>

          <!-- Assets Section -->
          <div class="space-y-6 pt-6 border-t border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-3 mb-2">
              <div class="w-8 h-8 bg-amber-50 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                <ImageIcon class="w-4 h-4 text-amber-600 dark:text-amber-400" />
              </div>
              <h3 class="text-lg font-black text-slate-900 dark:text-white">Media Assets</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <!-- Icon Upload -->
              <div class="space-y-3">
                <label class="block text-sm font-black text-slate-700 dark:text-slate-300 ml-1">Category Icon <span class="text-[10px] text-slate-400 uppercase tracking-widest ml-2">80x80px recommended</span></label>
                <div class="relative group cursor-pointer aspect-square max-w-[160px]">
                  <input type="file" @change="handleIconUpload" class="absolute inset-0 opacity-0 cursor-pointer z-10" accept="image/*" />
                  <div class="h-full border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-3xl flex flex-col items-center justify-center group-hover:bg-slate-50 dark:group-hover:bg-slate-800/50 transition-all overflow-hidden bg-slate-50/50 dark:bg-slate-900/50">
                    <div v-if="iconPreview" class="absolute inset-0">
                      <img :src="iconPreview" class="w-full h-full object-cover" />
                      <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all">
                        <Upload class="w-6 h-6 text-white" />
                      </div>
                    </div>
                    <div v-else class="text-center p-4">
                      <div class="w-10 h-10 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center shadow-sm mx-auto mb-3">
                        <Plus class="w-5 h-5 text-indigo-500" />
                      </div>
                      <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Upload Icon</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Banner Upload -->
              <div class="space-y-3">
                <label class="block text-sm font-black text-slate-700 dark:text-slate-300 ml-1">Category Banner <span class="text-[10px] text-slate-400 uppercase tracking-widest ml-2">300x180px recommended</span></label>
                <div class="relative group cursor-pointer aspect-video">
                  <input type="file" @change="handleImageUpload" class="absolute inset-0 opacity-0 cursor-pointer z-10" accept="image/*" />
                  <div class="h-full border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-3xl flex flex-col items-center justify-center group-hover:bg-slate-50 dark:group-hover:bg-slate-800/50 transition-all overflow-hidden bg-slate-50/50 dark:bg-slate-900/50">
                    <div v-if="imagePreview" class="absolute inset-0">
                      <img :src="imagePreview" class="w-full h-full object-cover" />
                      <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all">
                        <Upload class="w-6 h-6 text-white" />
                      </div>
                    </div>
                    <div v-else class="text-center p-4">
                      <div class="w-12 h-12 bg-white dark:bg-slate-800 rounded-2xl flex items-center justify-center shadow-sm mx-auto mb-3">
                        <ImageIcon class="w-6 h-6 text-amber-500" />
                      </div>
                      <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Upload Banner</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Settings Section -->
          <div class="space-y-6 pt-6 border-t border-slate-100 dark:border-slate-800">
             <div class="flex items-center justify-between p-6 bg-slate-50 dark:bg-slate-800/50 rounded-3xl border border-slate-100 dark:border-slate-700">
                <div class="flex items-center gap-4">
                  <div class="w-10 h-10 bg-white dark:bg-slate-900 rounded-xl flex items-center justify-center shadow-sm">
                    <Power class="w-5 h-5" :class="form.is_active ? 'text-emerald-500' : 'text-slate-400'" />
                  </div>
                  <div>
                    <h4 class="text-sm font-black text-slate-900 dark:text-white leading-none">Category Visibility</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Determine if this category is visible in the storefront.</p>
                  </div>
                </div>
                <!-- Simple Toggle -->
                <button @click.prevent="form.is_active = !form.is_active" :class="form.is_active ? 'bg-indigo-600' : 'bg-slate-300 dark:bg-slate-700'" class="w-14 h-8 rounded-full relative transition-all duration-300">
                  <div :class="form.is_active ? 'translate-x-7' : 'translate-x-1'" class="absolute top-1 left-0 w-6 h-6 bg-white rounded-full shadow-md transition-transform duration-300"></div>
                </button>
             </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-4 pt-10 mt-10 border-t border-slate-100 dark:border-slate-800">
             <button type="button" @click="$router.push('/vendor/attributes/categories')" class="px-8 py-4 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-2xl hover:bg-slate-200 dark:hover:bg-slate-700 font-black text-sm transition-all active:scale-95">
               Cancel
             </button>
             <button type="submit" :disabled="isLoading" class="flex-1 md:flex-none px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl transition-all shadow-lg shadow-indigo-600/20 active:scale-95 disabled:opacity-50 disabled:scale-100 flex items-center justify-center gap-2">
               <Loader2 v-if="isLoading" class="w-5 h-5 animate-spin" />
               <Save v-else class="w-5 h-5" />
               Create Category
             </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  ChevronLeft, 
  ChevronDown,
  LayoutGrid, 
  Image as ImageIcon, 
  Plus, 
  Upload, 
  Power, 
  Save, 
  Loader2 
} from 'lucide-vue-next'

definePageMeta({
  middleware: 'auth'
})

const auth = useAuthStore()
const router = useRouter()
const config = useRuntimeConfig()
const { createItem, getAll } = useCrud()

const isLoading = ref(false)
const form = reactive({
  name: '',
  description: '',
  parent_id: null,
  is_active: true,
  icon: null,
  image: null
})

const iconPreview = ref(null)
const imagePreview = ref(null)
const categories = ref([])

const handleIconUpload = (event) => {
   const file = event.target.files[0]
   if (file) {
      form.icon = file
      iconPreview.value = URL.createObjectURL(file)
   }
}

const handleImageUpload = (event) => {
   const file = event.target.files[0]
   if (file) {
      form.image = file
      imagePreview.value = URL.createObjectURL(file)
   }
}

const fetchCategories = async () => {
    try {
        const res = await getAll('/vendor/attributes/categories')
        if (res && res.data) categories.value = res.data
        else if (res) categories.value = res
    } catch (e) {
        console.error('Failed to fetch categories')
    }
}

fetchCategories()

const createCategory = async () => {
    isLoading.value = true
    try {
        const formData = new FormData()
        formData.append('name', form.name)
        if (form.description) formData.append('description', form.description)
        if (form.parent_id) formData.append('parent_id', form.parent_id)
        formData.append('is_active', form.is_active ? '1' : '0')
        if (form.icon) formData.append('icon', form.icon)
        if (form.image) formData.append('image', form.image)

        await createItem('/vendor/attributes/categories', formData)
        router.push('/vendor/attributes/categories')
    } catch (error) {
        console.error('Failed to create category', error)
    } finally {
        isLoading.value = false
    }
}
</script>

