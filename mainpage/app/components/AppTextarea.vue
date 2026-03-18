<template>
  <div>
    <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1.5">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>
    <textarea 
      v-bind="$attrs"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      :class="[
        'w-full px-4 py-2.5 bg-white border rounded-xl outline-none focus:ring-2 focus:ring-black focus:border-transparent text-sm transition-all resize-none',
        error ? 'border-red-500 focus:ring-red-500 shadow-sm' : 'border-gray-300 hover:border-gray-400'
      ]"
    ></textarea>
    <p v-if="error" class="mt-1 text-xs text-red-500 animate-shake">{{ error }}</p>
  </div>
</template>

<script setup>
defineOptions({ inheritAttrs: false })
defineProps({
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
  }
})

defineEmits(['update:modelValue'])
</script>
