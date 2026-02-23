<template>
  <div class="p-8">
    <div class="mb-10">
      <h1 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight">Mail Settings</h1>
      <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">Configure SMTP and outgoing email settings for the platform.</p>
    </div>
    
    <GlobalSettings filterGroup="mail" />
  </div>
</template>

<script setup>
definePageMeta({
  middleware: 'auth',
  permissions: 'admin.settings.view'
})
</script>
