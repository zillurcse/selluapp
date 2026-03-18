<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1.5">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>
    <div class="relative">
      <slot name="prefix"></slot>
      
      <input 
        v-bind="$attrs"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :class="[
          'w-full bg-white border outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-all',
          error ? 'border-red-500 focus:ring-red-500 shadow-sm' : 'border-gray-300 hover:border-gray-400',
          $slots.prefix ? 'pl-10' : '',
          sizeClasses
        ]"
      />
      <slot name="suffix"></slot>
    </div>
    <p v-if="error" class="mt-1 text-xs text-red-500 animate-shake">{{ error }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

defineOptions({ inheritAttrs: false })
const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: ''
  },
  size: {
    type: String,
    default: 'md',
    validator: (val) => ['sm', 'md', 'lg'].includes(val)
  }
})

defineEmits(['update:modelValue'])

const sizeClasses = computed(() => {
  switch (props.size) {
    case 'sm':
      return 'px-3 py-2 text-xs rounded-lg'
    case 'lg':
      return 'px-5 py-3 text-base rounded-2xl'
    case 'md':
    default:
      return 'px-4 py-2.5 text-sm rounded-xl'
  }
})
</script>
