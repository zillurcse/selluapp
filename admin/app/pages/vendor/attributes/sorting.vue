<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <!-- Header Section -->
    <div class="max-w-4xl mx-auto mb-8">
      <div class="flex items-center gap-4">
        <button @click="$router.back()" class="p-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm active:scale-95 group">
          <ChevronLeft class="w-5 h-5 text-slate-600 dark:text-slate-300 group-hover:-translate-x-0.5 transition-transform" />
        </button>
        <div>
          <h1 class="text-2xl font-black text-slate-900 dark:text-white leading-tight tracking-tight">Sorting Attributes</h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 font-semibold opacity-80">Drag and drop to reorder categories and brands.</p>
        </div>
      </div>
    </div>
    
    <div class="max-w-4xl mx-auto">
      <div class="bg-white dark:bg-slate-900 rounded-[32px] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.06)] dark:shadow-none border border-slate-200/60 dark:border-slate-800 overflow-hidden group transition-all duration-300">
        <!-- Tabs -->
        <div class="flex border-b border-slate-100 dark:border-slate-800 p-2 gap-2 bg-slate-50/50 dark:bg-slate-900/50">
          <button 
            v-for="tab in tabs" 
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              activeTab === tab.id
                ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm border-slate-200 dark:border-slate-700'
                : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 border-transparent',
              'flex-1 py-4 px-1 text-center font-black text-xs uppercase tracking-widest rounded-2xl border transition-all duration-300'
            ]"
          >
            {{ tab.name }}
          </button>
        </div>

        <div class="p-8 md:p-10">
          <div class="mb-8 p-6 bg-indigo-50/50 dark:bg-indigo-900/20 border-l-4 border-indigo-500 rounded-r-3xl">
             <div class="flex gap-4">
                <div class="flex-shrink-0 w-10 h-10 bg-white dark:bg-slate-900 rounded-xl flex items-center justify-center shadow-sm">
                   <Info class="h-5 w-5 text-indigo-500" />
                </div>
                <div>
                   <h4 class="text-sm font-black text-slate-900 dark:text-white mb-1">Quick Instructions</h4>
                   <p class="text-xs text-slate-600 dark:text-slate-400 font-medium leading-relaxed">Drag and drop items to reorder them vertically. Changes are saved locally until you click "Save Order".</p>
                </div>
             </div>
          </div>

          <!-- Attributes Lists -->
          <div class="relative min-h-[400px]">
            <TransitionGroup 
              name="list" 
              tag="div" 
              class="space-y-4"
              v-show="activeTab === 'categories'"
            >
              <div 
                v-for="(category, index) in categories" 
                :key="category.id"
                class="flex items-center justify-between p-5 bg-white dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 rounded-2xl shadow-sm hover:shadow-md dark:hover:border-indigo-500/50 transition-all cursor-grab active:cursor-grabbing select-none group/item"
                draggable="true"
                @dragstart="startDrag($event, index, 'categories')"
                @drop="onDrop($event, index, 'categories')"
                @dragover.prevent
                @dragenter.prevent
              >
                  <div class="flex items-center pointer-events-none">
                     <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg mr-4 group-hover/item:text-indigo-500 transition-colors">
                        <GripVertical class="h-4 w-4 text-slate-400 dark:text-slate-600" />
                     </div>
                     <div class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-900 flex items-center justify-center mr-4 overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm">
                        <img v-if="category.image" :src="`${config.public.apiBase}/storage/${category.image}`" class="h-full w-full object-cover">
                        <ImageIcon v-else class="w-5 h-5 text-slate-300 opacity-40" />
                     </div>
                     <div>
                        <span class="font-black text-slate-900 dark:text-white text-sm">{{ category.name }}</span>
                        <div class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider mt-0.5">Category</div>
                     </div>
                  </div>
                  <div class="flex items-center gap-4 pointer-events-none">
                     <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-900/50 px-3 py-1.5 rounded-full border border-slate-100 dark:border-slate-800">Pos: {{ index + 1 }}</span>
                  </div>
              </div>
              <div v-if="categories.length === 0" key="empty-cat" class="text-center py-24 bg-slate-50/50 dark:bg-slate-900/50 rounded-3xl border-2 border-dashed border-slate-200 dark:border-slate-800">
                <LayoutGrid class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-4 opacity-50" />
                <h3 class="text-slate-500 dark:text-slate-400 font-black">No categories to sort</h3>
              </div>
            </TransitionGroup>

            <TransitionGroup 
              name="list" 
              tag="div" 
              class="space-y-4"
              v-show="activeTab === 'brands'"
            >
              <div 
                v-for="(brand, index) in brands" 
                :key="brand.id"
                class="flex items-center justify-between p-5 bg-white dark:bg-slate-800/50 border border-slate-100 dark:border-slate-700 rounded-2xl shadow-sm hover:shadow-md dark:hover:border-indigo-500/50 transition-all cursor-grab active:cursor-grabbing select-none group/item"
                draggable="true"
                @dragstart="startDrag($event, index, 'brands')"
                @drop="onDrop($event, index, 'brands')"
                @dragover.prevent
                @dragenter.prevent
              >
                  <div class="flex items-center pointer-events-none">
                     <div class="p-2 bg-slate-50 dark:bg-slate-900 rounded-lg mr-4 group-hover/item:text-indigo-500 transition-colors">
                        <GripVertical class="h-4 w-4 text-slate-400 dark:text-slate-600" />
                     </div>
                     <div class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-900 flex items-center justify-center mr-4 overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm">
                        <img v-if="brand.image" :src="`${config.public.apiBase}/storage/${brand.image}`" class="h-full w-full object-cover">
                        <ImageIcon v-else class="w-5 h-5 text-slate-300 opacity-40" />
                     </div>
                     <div>
                        <span class="font-black text-slate-900 dark:text-white text-sm">{{ brand.name }}</span>
                        <div class="text-[10px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider mt-0.5">Brand</div>
                     </div>
                  </div>
                  <div class="flex items-center gap-4 pointer-events-none">
                     <span class="text-[10px] font-black text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-900/50 px-3 py-1.5 rounded-full border border-slate-100 dark:border-slate-800">Pos: {{ index + 1 }}</span>
                  </div>
              </div>
              <div v-if="brands.length === 0" key="empty-brand" class="text-center py-24 bg-slate-50/50 dark:bg-slate-900/50 rounded-3xl border-2 border-dashed border-slate-200 dark:border-slate-800">
                <Award class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-4 opacity-50" />
                <h3 class="text-slate-500 dark:text-slate-400 font-black">No brands to sort</h3>
              </div>
            </TransitionGroup>
          </div>

          <!-- Bottom Action -->
          <div class="mt-12 flex justify-end pt-10 border-t border-slate-100 dark:border-slate-800">
             <button @click="saveOrder(activeTab)" :disabled="saving" class="w-full md:w-auto flex items-center justify-center gap-2 bg-indigo-600 text-white px-10 py-4 rounded-2xl hover:bg-indigo-700 disabled:opacity-50 font-black shadow-lg shadow-indigo-600/20 active:scale-95 transition-all">
                <Loader2 v-if="saving" class="w-5 h-5 animate-spin" />
                <Save v-else class="w-5 h-5" />
                {{ saving ? 'Saving Order...' : `Save ${activeTab === 'categories' ? 'Category' : 'Brand'} Order` }}
             </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  GripVertical, 
  Info, 
  ChevronLeft, 
  LayoutGrid, 
  Award, 
  Image as ImageIcon, 
  Save, 
  Loader2 
} from 'lucide-vue-next'
import { toast } from 'vue-sonner'

definePageMeta({
  middleware: 'auth',
  permissions: 'attributes.view'
})

const config = useRuntimeConfig()
const auth = useAuthStore()
const { getAll, createItem } = useCrud()
const router = useRouter()

const tabs = [
  { id: 'categories', name: 'Categories' },
  { id: 'brands', name: 'Brands' },
]

const activeTab = ref('categories')
const categories = ref([])
const brands = ref([])
const saving = ref(false)

// Drag and Drop Logic
const startDrag = (event, index, type) => {
    event.dataTransfer.dropEffect = 'move'
    event.dataTransfer.effectAllowed = 'move'
    event.dataTransfer.setData('draggedIndex', index)
    event.dataTransfer.setData('type', type)
}

const onDrop = (event, dropIndex, type) => {
    const draggedIndex = event.dataTransfer.getData('draggedIndex')
    const draggedType = event.dataTransfer.getData('type')
    
    if (draggedType !== type) return 
    
    const oldIndex = parseInt(draggedIndex)
    const newIndex = dropIndex
    
    if (oldIndex === newIndex) return

    const items = type === 'categories' ? categories : brands
    const itemToMove = items.value[oldIndex]
    
    items.value.splice(oldIndex, 1)
    items.value.splice(newIndex, 0, itemToMove)
}

// Fetch Data
const fetchData = async () => {
    try {
        const [cats, brnds] = await Promise.all([
            getAll('/vendor/attributes/categories'),
            getAll('/vendor/attributes/brands')
        ])
        categories.value = (cats.data || cats) || []
        brands.value = (brnds.data || brnds) || []
    } catch (e) {
        console.error('Failed to fetch attributes', e)
    }
}

fetchData()

// Save Order
const saveOrder = async (type) => {
    saving.value = true
    const list = type === 'categories' ? categories.value : brands.value
    
    const items = list.map((item, index) => ({
        id: item.id,
        sort_order: index + 1
    }))

    try {
        await createItem(`/vendor/attributes/${type}/sort`, items)
        toast.success(`${type.charAt(0).toUpperCase() + type.slice(1)} order saved successfully`)
        router.push('/vendor/attributes/brands')
    } catch (error) {
        console.error('Failed to save order', error)
        toast.error('Failed to save order')
    } finally {
        saving.value = false
    }
}
</script>

<style scoped>
.list-move,
.list-enter-active,
.list-leave-active {
  transition: all 0.5s ease;
}

.list-enter-from,
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

.list-leave-active {
  position: absolute;
}
</style>

