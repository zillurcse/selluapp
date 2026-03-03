<template>
  <div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center px-4 sm:px-6">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-gray-100 p-8 sm:p-12 text-center animate-fade-in">
      <!-- Success Icon -->
      <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-8 animate-bounce-subtle">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
      </div>

      <!-- Content -->
      <h1 class="text-3xl font-extrabold text-gray-900 mb-4 tracking-tight">Order Confirmed!</h1>
      <p class="text-gray-600 mb-8 leading-relaxed">
        Thank you for your purchase. Your order has been placed successfully and is now being processed.
      </p>

      <!-- Order Details Card -->
      <div v-if="orderNumber" class="bg-gray-50 rounded-2xl p-6 mb-10 border border-gray-100">
        <span class="block text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Invoice Number</span>
        <span class="text-lg font-mono font-bold text-gray-900">{{ orderNumber }}</span>
      </div>

      <!-- Actions -->
      <div class="space-y-4">
        <NuxtLink 
          to="/" 
          class="block w-full py-4 bg-black text-white rounded-2xl font-semibold shadow-lg hover:bg-gray-800 transform transition-all active:scale-95 focus:ring-4 focus:ring-gray-200"
        >
          Continue Shopping
        </NuxtLink>
        <NuxtLink 
          v-if="authStore.isLoggedIn"
          to="/customer/orders" 
          class="block w-full py-4 bg-white text-gray-700 border border-gray-200 rounded-2xl font-semibold hover:bg-gray-50 transition-colors"
        >
          View My Orders
        </NuxtLink>
      </div>

      <!-- Footer Info -->
      <div class="mt-12 text-sm text-gray-400">
        <p>A confirmation email has been sent to your inbox.</p>
        <p class="mt-1">Need help? <a href="#" class="text-gray-600 font-medium hover:underline">Contact Support</a></p>
      </div>
    </div>
  </div>
</template>

<script setup>
const route = useRoute()
const authStore = useAuthStore()
const orderNumber = computed(() => route.query.invoice_number)

useHead({
  title: 'Thank You | Order Confirmed',
  meta: [
    { name: 'description', content: 'Your order has been placed successfully.' }
  ]
})
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes bounceSubtle {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-5px); }
}

.animate-fade-in {
  animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.animate-bounce-subtle {
  animation: bounceSubtle 2s ease-in-out infinite;
}
</style>
