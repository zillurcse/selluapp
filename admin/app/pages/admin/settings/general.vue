<template>
  <div class="p-8">
    <div class="mb-10">
      <h1 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight">General Settings</h1>
      <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">Configure core platform identity and basic information.</p>
    </div>
    
    <GlobalSettings filterGroup="general" />
  </div>
</template>

<script setup>
definePageMeta({
  middleware: 'auth',
  permissions: 'admin.settings.view'
})
</script>
