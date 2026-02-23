<template>
  <div>
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
    <Toaster position="top-right" rich-colors />
  </div>
</template>

<script setup lang="ts">
import { Toaster } from 'vue-sonner'
import { onMounted } from 'vue'

const { fetchCart } = useCart()

onMounted(() => {
  fetchCart()
})
</script>
