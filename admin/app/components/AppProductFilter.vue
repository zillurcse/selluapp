<template>
  <div class="bg-white dark:bg-slate-900 rounded-[24px] shadow-[0_10px_40px_-15px_rgba(0,0,0,0.05)] dark:shadow-[0_10px_40px_-15px_rgba(0,0,0,0.5)] border border-slate-200/60 dark:border-slate-800/60 mb-8 p-8 transition-colors duration-300">
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 flex-grow">
        <!-- Product Name -->
        <div class="flex flex-col gap-2">
          <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.1em] ml-1">Product Name</label>
          <div class="relative">
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Search products..." 
              class="w-full h-12 pl-5 pr-10 bg-slate-50/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 dark:focus:ring-indigo-500/20 focus:border-indigo-500 dark:focus:border-indigo-400 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500 text-slate-700 dark:text-slate-200 font-semibold"
            >
            <div v-if="isLoading" class="absolute right-4 top-1/2 -translate-y-1/2">
                <div class="w-4 h-4 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
            </div>
          </div>
        </div>

        <!-- Category -->
        <div class="flex flex-col gap-2">
          <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.1em] ml-1">Category</label>
          <div class="relative">
            <select v-model="selectedCategory" class="w-full h-12 pl-5 pr-10 bg-slate-50/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-2xl appearance-none focus:ring-4 focus:ring-indigo-500/10 dark:focus:ring-indigo-500/20 focus:border-indigo-500 dark:focus:border-indigo-400 outline-none transition-all text-slate-700 dark:text-slate-200 font-semibold cursor-pointer">
              <option value="" class="text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-900">All Categories</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id" class="text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-900">{{ cat.name }}</option>
            </select>
            <ChevronDown class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 dark:text-slate-500 pointer-events-none" />
          </div>
        </div>

        <!-- Brand -->
        <div class="flex flex-col gap-2">
          <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.1em] ml-1">Brand</label>
          <div class="relative">
            <select v-model="selectedBrand" class="w-full h-12 pl-5 pr-10 bg-slate-50/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-2xl appearance-none focus:ring-4 focus:ring-indigo-500/10 dark:focus:ring-indigo-500/20 focus:border-indigo-500 dark:focus:border-indigo-400 outline-none transition-all text-slate-700 dark:text-slate-200 font-semibold cursor-pointer">
              <option value="" class="text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-900">All Brands</option>
              <option v-for="brand in brands" :key="brand.id" :value="brand.id" class="text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-900">{{ brand.name }}</option>
            </select>
            <ChevronDown class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 dark:text-slate-500 pointer-events-none" />
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 lg:items-end">
          <!-- Manual Filter button is less needed with auto-search, but kept for clarity/force refresh -->
          <button @click="$emit('filter')" class="h-12 px-8 bg-slate-900 dark:bg-indigo-600 hover:bg-slate-800 dark:hover:bg-indigo-500 text-white font-black rounded-2xl transition-all shadow-xl shadow-slate-900/10 dark:shadow-indigo-900/20 active:scale-95 flex items-center justify-center gap-2">
            <Search class="w-4 h-4" />
            Filter
          </button>
          <button @click="clear" class="h-12 px-6 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-black rounded-2xl transition-all flex items-center justify-center gap-2 active:scale-95">
            <X class="w-4 h-4" />
            Clear
          </button>
        </div>
      </div>

      <div class="flex lg:items-end">
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
