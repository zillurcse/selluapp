<template>
  <div class="flex items-center bg-white border border-indigo-100/80 rounded-full overflow-hidden shadow-sm focus-within:ring-4 focus-within:ring-indigo-500/10 focus-within:border-indigo-400 transition-all w-full max-w-2xl h-12">
    <!-- Search Input -->
    <div class="flex-1 flex items-center px-5 h-full">
      <input 
        v-model="searchQuery"
        type="text" 
        :placeholder="`Search ${searchType}...`" 
        class="bg-transparent border-none outline-none w-full text-[0.92rem] font-medium text-gray-700 placeholder-indigo-300/80"
        @keyup.enter="handleSearch"
      />
    </div>

    <!-- Type Dropdown -->
    <div class="relative px-4 h-full bg-gray-50/80 border-x border-indigo-50 flex items-center min-w-[150px] group/cat">
      <select 
        v-model="searchType"
        class="bg-transparent border-none outline-none w-full text-[0.88rem] font-bold text-gray-500 cursor-pointer appearance-none pr-4 capitalize"
      >
        <option value="product">Product</option>
        <option value="category">Category</option>
        <option value="brand">Brand</option>
      </select>
      <div class="absolute right-4 pointer-events-none text-gray-400 group-hover/cat:text-indigo-500 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
      </div>
    </div>

    <!-- Search Button -->
    <button 
      @click="handleSearch"
      class="bg-[#2B99C4] hover:bg-[#2388B0] text-white px-8 h-full text-[0.9rem] font-black tracking-tight transition-all active:scale-95 shrink-0"
    >
      Search
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useStorefrontStore } from '~/stores/useStorefrontStore'

const router = useRouter()
const storefrontStore = useStorefrontStore()

const searchQuery = ref('')
const searchType = ref('product')

const handleSearch = () => {
  if (!searchQuery.value.trim()) return

  const query: any = {}
  
  if (searchType.value === 'category') {
    query.category = searchQuery.value.trim()
  } else if (searchType.value === 'brand') {
    query.brand = searchQuery.value.trim()
  } else {
    query.search = searchQuery.value.trim()
  }

  // Navigate to shop with query params
  router.push({
    path: '/shop',
    query
  })
}
</script>
