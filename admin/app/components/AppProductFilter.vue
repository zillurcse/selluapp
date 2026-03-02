<template>
  <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 mb-6 p-6 transition-all duration-300">
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 flex-grow">
        <!-- Product Name -->
        <div class="flex flex-col gap-1.5">
          <label class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Product Name</label>
          <div class="relative">
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Search products..." 
              class="w-full h-10 pl-4 pr-10 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-lg focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400 text-sm font-medium text-slate-700 dark:text-slate-200"
            >
            <div v-if="isLoading" class="absolute right-3 top-1/2 -translate-y-1/2">
                <div class="w-3.5 h-3.5 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
            </div>
            <Search v-else class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
          </div>
        </div>

        <!-- Category -->
        <div class="flex flex-col gap-1.5">
          <label class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Category</label>
          <div class="relative">
            <select v-model="selectedCategory" class="w-full h-10 pl-4 pr-10 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-lg appearance-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all text-sm font-medium text-slate-700 dark:text-slate-200 cursor-pointer">
              <option value="">All Categories</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
            <ChevronDown class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
          </div>
        </div>

        <!-- Brand -->
        <div class="flex flex-col gap-1.5">
          <label class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Brand</label>
          <div class="relative">
            <select v-model="selectedBrand" class="w-full h-10 pl-4 pr-10 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-lg appearance-none focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all text-sm font-medium text-slate-700 dark:text-slate-200 cursor-pointer">
              <option value="">All Brands</option>
              <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
            </select>
            <ChevronDown class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-end gap-2">
          <button @click="$emit('filter')" class="h-10 px-5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-all flex items-center justify-center gap-2 shadow-sm">
            <Search class="w-4 h-4" />
            Filter
          </button>
          <button @click="clear" class="h-10 px-4 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-sm font-semibold rounded-lg transition-all flex items-center justify-center gap-2">
            <X class="w-4 h-4" />
            Clear
          </button>
        </div>
      </div>

      <div class="flex items-end">
        <slot name="actions"></slot>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Search, X, ChevronDown } from 'lucide-vue-next'

const props = defineProps({
  search: {
    type: String,
    default: ''
  },
  category: {
    type: [String, Number],
    default: ''
  },
  categories: {
    type: Array,
    default: () => []
  },
  brand: {
    type: [String, Number],
    default: ''
  },
  brands: {
    type: Array,
    default: () => []
  },
  isLoading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:search', 'update:category', 'update:brand', 'filter', 'clear'])

// Local state for v-model binding
const searchQuery = computed({
  get: () => props.search,
  set: (val) => emit('update:search', val)
})

const selectedCategory = computed({
  get: () => props.category,
  set: (val) => emit('update:category', val)
})

const selectedBrand = computed({
  get: () => props.brand,
  set: (val) => emit('update:brand', val)
})

// Debounce Implementation
let timeout = null

const debouncedFilter = () => {
  if (timeout) clearTimeout(timeout)
  timeout = setTimeout(() => {
    emit('filter')
  }, 500)
}

// Watchers
watch(searchQuery, () => {
  debouncedFilter()
})

// Immediate filter on category change
watch(selectedCategory, () => {
  emit('filter')
})

// Immediate filter on brand change
watch(selectedBrand, () => {
  emit('filter')
})

const clear = () => {
  emit('clear')
}

// Cleanup
onUnmounted(() => {
  if (timeout) clearTimeout(timeout)
})
</script>
