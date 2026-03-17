<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-6">
    <div class="w-full max-w-sm text-center">
      <div v-if="verifying" class="flex flex-col items-center gap-4">
        <div class="w-12 h-12 border-4 border-violet-600 border-t-transparent rounded-full animate-spin"></div>
        <h1 class="text-xl font-bold text-gray-900">Verifying your login...</h1>
        <p class="text-gray-500 text-sm">Please wait while we secure your session.</p>
      </div>
      
      <div v-else-if="error" class="flex flex-col items-center gap-4">
        <div class="w-16 h-16 bg-red-50 text-red-600 rounded-full flex items-center justify-center text-3xl">
          ❌
        </div>
        <h1 class="text-xl font-bold text-gray-900">Verification Failed</h1>
        <p class="text-red-600 text-sm">{{ error }}</p>
        <NuxtLink to="/login" class="mt-4 px-6 py-2 bg-gray-900 text-white font-bold rounded-xl hover:bg-gray-800 transition">
          Back to Login
        </NuxtLink>
      </div>

      <div v-else class="flex flex-col items-center gap-4">
        <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center text-3xl">
          ✅
        </div>
        <h1 class="text-xl font-bold text-gray-900">Success!</h1>
        <p class="text-gray-500 text-sm">You are being redirected...</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
const authStore = useAuthStore()
const verifying = ref(true)
const error = ref('')

onMounted(async () => {
  try {
    // Collect all query params (email, vendor_id, expires, signature)
    const query = route.query
    const success = await authStore.verifyMagicLink(query)
    
    if (success) {
      navigateTo('/')
    } else {
      error.value = 'The login link is invalid or has expired.'
    }
  } catch (err: any) {
    console.error('Magic link verification error:', err)
    error.value = err.data?.message || err.message || 'An error occurred during verification.'
  } finally {
    verifying.value = false
  }
})
</script>

<style scoped>
@keyframes spin {
  to { transform: rotate(360deg); }
}
.animate-spin { animation: spin 1s linear infinite; }
</style>
