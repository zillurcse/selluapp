<template>
  <div class="relative" ref="dropdownRef">
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1.5">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>
    
    <div 
      class="relative w-full cursor-default overflow-hidden rounded-xl border border-gray-300 bg-white text-left focus-within:ring-2 focus-within:ring-black focus-within:border-transparent transition-all shadow-sm"
      @click="isOpen = !isOpen"
    >
      <input
        type="text"
        class="w-full border-none py-2.5 pl-4 pr-10 text-sm leading-5 text-gray-900 bg-transparent focus:ring-0 outline-none placeholder-gray-500"
        :placeholder="selectedItemName || placeholder"
        v-model="searchQuery"
        @input="isOpen = true"
      />
      <div class="absolute inset-y-0 right-0 flex items-center pr-2">
        <svg
          class="h-4 w-4 text-gray-400 transition-transform duration-200"
          :class="{ 'rotate-180': isOpen }"
          xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
        >
          <path d="m6 9 6 6 6-6"/>
        </svg>
      </div>
    </div>

    <!-- Dropdown -->
    <div 
      v-if="isOpen"
      class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-xl bg-white border border-gray-200 py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
    >
      <div v-if="filteredItems.length === 0 && !searchQuery" class="px-4 py-3 text-gray-500 text-center">
        No {{ label?.toLowerCase() || 'items' }} available
      </div>

      <div
        v-for="item in filteredItems"
        :key="item.id"
        class="relative cursor-pointer select-none py-2.5 pl-4 pr-9 hover:bg-gray-50 transition-colors"
        :class="{ 'bg-gray-50 text-black font-semibold': isSelected(item.id), 'text-gray-900': !isSelected(item.id) }"
        @click="selectItem(item)"
      >
        <span class="block truncate">
          {{ item.name }}
        </span>
        <span
          v-if="isSelected(item.id)"
          class="absolute inset-y-0 right-0 flex items-center pr-4 text-black"
        >
          <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
        </span>
      </div>

      <!-- Add New Option -->
      <div
        v-if="searchQuery && !exactMatch"
        class="relative cursor-pointer select-none py-2.5 pl-4 pr-9 border-t border-gray-100 hover:bg-gray-50 bg-gray-50/50"
        @click="addNewItem"
      >
        <div class="flex items-center text-black font-medium">
          <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
          </svg>
          <span>Add "{{ searchQuery }}" as new {{ label?.toLowerCase() || 'item' }}</span>
        </div>
      </div>
      
      <div v-if="filteredItems.length === 0 && searchQuery && exactMatch" class="px-4 py-3 text-gray-500 text-center">
        No matches found
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'

const props = defineProps({
  modelValue: [String, Number, Array],
  items: {
    type: Array,
    default: () => []
  },
  label: String,
  placeholder: {
    type: String,
    default: 'Search...'
  },
  required: Boolean,
  multiple: Boolean
})

const emit = defineEmits(['update:modelValue', 'onCreate'])

const isOpen = ref(false)
const searchQuery = ref('')
const dropdownRef = ref(null)

const filteredItems = computed(() => {
  if (!searchQuery.value) return props.items
  return props.items.filter(item => 
    item.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

const exactMatch = computed(() => {
  if (!searchQuery.value) return true
  return props.items.some(item => 
    item.name.toLowerCase() === searchQuery.value.toLowerCase()
  )
})

const selectedItemName = computed(() => {
  if (props.multiple) {
    if (!Array.isArray(props.modelValue) || props.modelValue.length === 0) return ''
    const count = props.modelValue.length
    return `${count} items selected`
  }
  const selected = props.items.find(item => item.id === props.modelValue)
  return selected ? selected.name : ''
})

const isSelected = (id) => {
  if (props.multiple) {
    return Array.isArray(props.modelValue) && props.modelValue.includes(id)
  }
  return props.modelValue === id
}

const selectItem = (item) => {
  if (props.multiple) {
    const newValue = Array.isArray(props.modelValue) ? [...props.modelValue] : []
    const index = newValue.indexOf(item.id)
    if (index > -1) {
      newValue.splice(index, 1)
    } else {
      newValue.push(item.id)
    }
    emit('update:modelValue', newValue)
  } else {
    emit('update:modelValue', item.id)
    searchQuery.value = ''
    isOpen.value = false
  }
}

const addNewItem = () => {
  emit('onCreate', searchQuery.value)
  searchQuery.value = ''
  isOpen.value = false
}

// Close dropdown on click outside
const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    isOpen.value = false
    searchQuery.value = ''
  }
}

onMounted(() => {
  document.addEventListener('mousedown', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('mousedown', handleClickOutside)
})

// Clear search when closed
watch(isOpen, (newVal) => {
  if (!newVal) searchQuery.value = ''
})
</script>
