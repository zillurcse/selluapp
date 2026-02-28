<template>
  <div class="hidden" v-if="false">
    <!-- This component mainly registers a global keypress listener and emits a 'scan' event -->
  </div>
</template>

<script setup>
import { onMounted, onUnmounted, ref } from 'vue'
import { toast } from 'vue-sonner'

const emit = defineEmits(['scan'])
const barcodeBuffer = ref('')
const lastKeyTime = ref(0)
const threshold = 50 // ms to distinguish scanner input from typing

const handleKeydown = (e) => {
  // If the user is typing in an input or textarea, ignore
  if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
    return
  }

  const currentTime = new Date().getTime()
  
  // If difference is greater than threshold, it might be a human typing
  if (currentTime - lastKeyTime.value > threshold) {
    barcodeBuffer.value = ''
  }

  lastKeyTime.value = currentTime

  // Barcode scanners usually end with Enter
  if (e.key === 'Enter' && barcodeBuffer.value.length >= 3) {
    emit('scan', barcodeBuffer.value)
    barcodeBuffer.value = ''
    e.preventDefault()
  } else if (e.key.length === 1) { // Ignore modifier keys like Shift, Control
    barcodeBuffer.value += e.key
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown)
})
</script>
