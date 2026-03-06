<template>
  <div class="flex items-center bg-white border border-gray-200 rounded-full shadow-sm w-full max-w-2xl h-12 relative" style="overflow: visible;">

    <!-- Search Input -->
    <div class="flex-1 flex items-center gap-2 px-5 h-full">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 shrink-0"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
      <input
        v-model="searchQuery"
        type="text"
        :placeholder="`Search for ${searchType}s...`"
        class="bg-transparent border-none outline-none w-full text-sm font-medium text-gray-700 placeholder-gray-300"
        @keyup.enter="handleSearch"
      />
    </div>

    <!-- Divider -->
    <div class="w-px h-6 bg-gray-200 shrink-0"></div>

    <!-- Type Selector -->
    <div class="relative h-full flex items-center shrink-0" v-click-outside="closeDropdown">
      <button
        @click="dropdownOpen = !dropdownOpen"
        class="flex items-center gap-1.5 px-4 h-full hover:bg-gray-50 rounded-r-none transition-colors"
      >
        <span class="text-sm font-semibold text-gray-500">{{ searchType }}</span>
        <svg
          xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
          fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
          class="text-gray-400 transition-transform duration-200"
          :class="dropdownOpen ? 'rotate-180' : ''"
        ><path d="m6 9 6 6 6-6"/></svg>
      </button>

      <!-- Dropdown -->
      <Transition
        enter-active-class="transition duration-150 ease-out"
        enter-from-class="opacity-0 -translate-y-1"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-100 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-1"
      >
        <div
          v-if="dropdownOpen"
          class="absolute top-[calc(100%+8px)] right-0 w-40 bg-white border border-gray-100 rounded-2xl shadow-lg overflow-hidden z-[300]"
        >
          <div class="p-1.5">
            <button
              v-for="type in searchTypes"
              :key="type"
              @click="selectType(type)"
              class="w-full text-left px-3.5 py-2 rounded-xl text-sm transition-colors capitalize"
              :class="searchType === type
                ? 'bg-indigo-50 text-indigo-600 font-semibold'
                : 'text-gray-600 hover:bg-gray-50 font-medium'"
            >
              {{ type }}
            </button>
          </div>
        </div>
      </Transition>
    </div>

    <!-- Search Button -->
    <button
      @click="handleSearch"
      class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white px-6 h-full text-sm font-semibold tracking-wide transition-all rounded-full ml-1 mr-1 shrink-0"
    >
      Search
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const searchQuery = ref('')
const searchType = ref('product')
const dropdownOpen = ref(false)

const searchTypes = ['product', 'category', 'brand']

const selectType = (type: string) => {
  searchType.value = type
  dropdownOpen.value = false
}

const closeDropdown = () => {
  dropdownOpen.value = false
}

// v-click-outside directive
const vClickOutside = {
  mounted(el: HTMLElement, binding: any) {
    el._clickOutside = (event: Event) => {
      if (!el.contains(event.target as Node)) binding.value()
    }
    document.addEventListener('click', el._clickOutside)
  },
  unmounted(el: HTMLElement) {
    document.removeEventListener('click', el._clickOutside)
    delete el._clickOutside
  }
}

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
  router.push({ path: '/shop', query })
}
</script>
