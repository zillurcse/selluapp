<template>
  <div class="relative" ref="dropdownRef">
    <label v-if="label" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
      {{ label }} <span v-if="required" class="text-red-500 dark:text-red-400">*</span>
    </label>
    
    <div 
      class="relative w-full cursor-default overflow-hidden rounded-md border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-left focus-within:ring-2 focus-within:ring-blue-500 dark:focus-within:ring-indigo-500 focus-within:border-blue-500 dark:focus-within:border-indigo-500 transition-all"
      @click="isOpen = !isOpen"
    >
      <input
        type="text"
        class="w-full border-none py-2 pl-3 pr-10 text-sm leading-5 text-gray-900 dark:text-slate-100 bg-transparent focus:ring-0 outline-none placeholder-gray-400 dark:placeholder-slate-500"
        :placeholder="selectedItemName || placeholder"
        v-model="searchQuery"
        @input="isOpen = true"
      />
      <div class="absolute inset-y-0 right-0 flex items-center pr-2">
        <ChevronDown
          class="h-4 w-4 text-gray-400 transition-transform duration-200"
          :class="{ 'rotate-180': isOpen }"
          aria-hidden="true"
        />
      </div>
    </div>

    <!-- Dropdown -->
    <div 
      v-if="isOpen"
      class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
    >
      <div v-if="filteredItems.length === 0 && !searchQuery" class="px-4 py-3 text-gray-500 dark:text-slate-400 text-center">
        No {{ label?.toLowerCase() || 'items' }} available
      </div>

      <div
        v-for="item in filteredItems"
        :key="item.id"
        class="relative cursor-pointer select-none py-2 pl-3 pr-9 hover:bg-blue-50 dark:hover:bg-indigo-900/30 transition-colors"
        :class="{ 'bg-blue-50 dark:bg-indigo-900/30 text-blue-900 dark:text-indigo-400': isSelected(item.id), 'text-gray-900 dark:text-slate-200': !isSelected(item.id) }"
        @click="selectItem(item)"
      >
        <span class="block truncate" :class="{ 'font-medium': isSelected(item.id), 'font-normal': !isSelected(item.id) }">
          {{ item.name }}
        </span>
        <span
          v-if="isSelected(item.id)"
          class="absolute inset-y-0 right-0 flex items-center pr-4 text-blue-600 dark:text-indigo-400"
        >
          <Check class="h-4 w-4" aria-hidden="true" />
        </span>
      </div>

      <!-- Add New Option -->
      <div
        v-if="searchQuery && !exactMatch"
        class="relative cursor-pointer select-none py-2 pl-3 pr-9 border-t border-gray-100 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800 bg-gray-50/50 dark:bg-slate-800/50"
        @click="addNewItem"
      >
        <div class="flex items-center text-blue-600 dark:text-indigo-400 font-medium">
          <Plus class="h-4 w-4 mr-2" />
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
import { ChevronDown, Check, Plus } from 'lucide-vue-next'

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
