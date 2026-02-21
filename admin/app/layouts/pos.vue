<template>
  <div class="h-screen flex flex-col bg-gray-50 font-sans text-gray-900 overflow-hidden">
    <slot />
    <ClientOnly>
      <Toaster position="top-right" richColors />
    </ClientOnly>
  </div>
</template>

<script setup>
// Minimal layout for POS
</script>
