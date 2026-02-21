<template>
  <!-- Loading spinner while we determine the destination -->
  <div class="min-h-screen flex items-center justify-center bg-[#f8fafc] dark:bg-slate-950">
    <div class="flex flex-col items-center gap-4">
      <div class="w-12 h-12 rounded-2xl bg-slate-900 dark:bg-indigo-600 flex items-center justify-center shadow-xl animate-pulse">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7"><path d="M7 20l10-16l-5 16l-5-16"></path></svg>
      </div>
      <p class="text-sm font-bold text-slate-400 dark:text-slate-500 animate-pulse">Loading your dashboard…</p>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ middleware: 'auth', layout: false })

const auth = useAuthStore()
const tokenStore = useTokenStore()
const config = useRuntimeConfig()

// Make sure user data is loaded before we decide where to send them
if (!auth.user && tokenStore.getToken) {
  try {
    await auth.fetchUser()
  } catch (e) {
    console.error('index.vue: fetchUser failed', e)
  }
}

// Resolve destination based on roles
const roles = auth.user?.roles || []
const isSuperAdmin = roles.some((r) => r.name === 'super-admin')
const isVendor     = roles.some((r) => r.name === 'vendor')
const isAdmin      = roles.some((r) => r.name === 'admin')

if (isSuperAdmin || isAdmin) {
  await navigateTo('/admin', { replace: true })
} else if (auth.user) {
  // Default to vendor for all other authenticated users (e.g. if isVendor is false or not defined)
  await navigateTo('/vendor', { replace: true })
} else {
  await navigateTo('/login', { replace: true })
}
</script>
