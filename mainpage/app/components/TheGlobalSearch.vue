<template>
  <div class="flex items-center bg-[var(--muted)] border border-transparent focus-within:border-[var(--border)] focus-within:bg-white rounded-xl w-full max-w-2xl h-11 relative transition-colors" style="overflow: visible;">
    <div class="flex-1 flex items-center gap-2 px-3.5 h-full">
      <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 shrink-0"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
      <input
        v-model="searchQuery"
        type="text"
        :placeholder="`Search ${searchType}s...`"
        class="bg-transparent border-none outline-none w-full text-sm font-medium text-gray-700 placeholder-gray-400"
        @keyup.enter="handleSearch"
      />
    </div>

    <div class="w-px h-5 bg-gray-200 shrink-0"></div>

    <div class="relative h-full flex items-center shrink-0" v-click-outside="closeDropdown">
      <button
        @click="dropdownOpen = !dropdownOpen"
        class="flex items-center gap-1.5 px-3 h-full hover:bg-black/5 transition-colors rounded-r-xl"
      >
        <span class="text-sm font-medium text-gray-500 capitalize">{{ searchType }}</span>
        <svg
          xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
          fill="none" stroke="currentColor" stroke-width="2.5"
          class="text-gray-400 transition-transform duration-200"
          :class="dropdownOpen ? 'rotate-180' : ''"
        ><path d="m6 9 6 6 6-6"/></svg>
      </button>

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
          class="absolute top-[calc(100%+8px)] right-0 w-36 bg-white border border-[var(--border)] rounded-xl shadow-lg overflow-hidden z-[300]"
        >
          <div class="p-1.5">
            <button
              v-for="type in searchTypes"
              :key="type"
              @click="selectType(type)"
              class="w-full text-left px-3 py-2 rounded-lg text-sm transition-colors capitalize"
              :class="searchType === type
                ? 'bg-[var(--muted)] text-gray-900 font-semibold'
                : 'text-gray-600 hover:bg-[var(--muted)] font-medium'"
            >
              {{ type }}
            </button>
          </div>
        </div>
      </Transition>
    </div>

    <button
      @click="handleSearch"
      class="bg-gray-900 hover:opacity-90 active:scale-95 text-white px-4 h-[calc(100%-6px)] text-sm font-semibold transition-all rounded-lg mr-1 shrink-0"
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

const emit = defineEmits(['select'])

const closeDropdown = () => {
  dropdownOpen.value = false
}

const selectType = (type: string) => {
  searchType.value = type
  dropdownOpen.value = false
}

const handleSearch = () => {
  if (!searchQuery.value.trim()) return
  const query: Record<string, string> = {}
  if (searchType.value === 'category') {
    query.category = searchQuery.value.trim()
  } else if (searchType.value === 'brand') {
    query.brand = searchQuery.value.trim()
  } else {
    query.search = searchQuery.value.trim()
  }
  router.push({ path: '/shop', query })
  emit('select')
}

const vClickOutside = {
  mounted(el: any, binding: any) {
    el._clickOutside = (event: Event) => {
      if (!(el === event.target || el.contains(event.target as Node))) {
        binding.value(event)
      }
    }
    document.addEventListener('click', el._clickOutside)
  },
  unmounted(el: any) {
    document.removeEventListener('click', el._clickOutside)
  }
}
</script>
