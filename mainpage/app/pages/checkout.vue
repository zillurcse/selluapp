<template>
  <div class="checkout-page min-h-screen bg-gray-50 flex flex-col items-center font-sans tracking-normal">
    <!-- Header -->
    <header class="w-full bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-900 tracking-tight">Checkout</h1>
        <NuxtLink to="/shop" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors flex items-center gap-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
          Back to Shop
        </NuxtLink>
      </div>
    </header>

    <main class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
      <div v-if="cart.length === 0" class="max-w-md mx-auto flex flex-col items-center justify-center py-20 bg-white rounded-2xl shadow-sm border border-gray-100 animate-fade-in">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-3xl mb-6">🛒</div>
        <h2 class="text-xl font-semibold text-gray-900 mb-2">Your cart is empty</h2>
        <p class="text-gray-500 text-center mb-8 px-6 text-sm">Looks like you haven't added anything to your cart yet.</p>
        <NuxtLink to="/shop" class="px-6 py-3 bg-black text-white rounded-xl font-medium text-sm hover:bg-gray-800 transition-colors shadow-sm">
          Continue Shopping
        </NuxtLink>
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
        <!-- Left Column: Form -->
        <div class="lg:col-span-7 space-y-8 animate-fade-in" style="animation-delay: 0.1s">
          
          <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Contact -->
            <section class="p-6 sm:p-8 border-b border-gray-100">
              <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-3">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-900 text-xs font-bold">1</span>
                Contact Information
              </h2>
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                  <input v-model="form.email" type="email" placeholder="you@example.com" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-black focus:border-transparent text-sm transition-shadow" />
                </div>
              </div>
            </section>

            <!-- Shipping -->
            <section class="p-6 sm:p-8 border-b border-gray-100">
              <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-3">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-900 text-xs font-bold">2</span>
                Shipping Address
              </h2>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1.5">First Name</label>
                  <input v-model="form.first_name" type="text" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-black focus:border-transparent text-sm transition-shadow" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1.5">Last Name</label>
                  <input v-model="form.last_name" type="text" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-black focus:border-transparent text-sm transition-shadow" />
                </div>
                <div class="sm:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1.5">Address</label>
                  <input v-model="form.address" type="text" placeholder="Street address, apartment, suite, etc." class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-black focus:border-transparent text-sm transition-shadow" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1.5">City</label>
                  <input v-model="form.city" type="text" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-black focus:border-transparent text-sm transition-shadow" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1.5">Postal Code</label>
                  <input v-model="form.postal_code" type="text" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-black focus:border-transparent text-sm transition-shadow" />
                </div>
              </div>
            </section>

            <!-- Payment -->
            <section class="p-6 sm:p-8">
              <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-3">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-900 text-xs font-bold">3</span>
                Payment Method
              </h2>
              
              <div class="space-y-6">
                <!-- Loading State -->
                <div v-if="loadingGateways" class="flex justify-center py-6">
                  <div class="w-6 h-6 border-2 border-black border-t-transparent rounded-full animate-spin"></div>
                </div>

                <!-- Empty State -->
                <div v-else-if="gateways.length === 0" class="text-center py-8 px-4 border border-dashed border-gray-300 rounded-xl bg-gray-50">
                  <p class="text-sm text-gray-600">No payment methods available for this vendor.</p>
                </div>

                <!-- Gateways List -->
                <div v-else class="flex flex-col gap-3">
                  <label 
                    v-for="gateway in gateways" 
                    :key="gateway.slug"
                    class="relative flex cursor-pointer rounded-xl border-2 p-4 transition-all hover:bg-gray-50 focus:outline-none"
                    :class="paymentMethod === gateway.slug ? 'border-black bg-gray-50/50' : 'border-gray-200'"
                  >
                    <input 
                      type="radio" 
                      name="payment_method" 
                      :value="gateway.slug" 
                      v-model="paymentMethod" 
                      class="sr-only" 
                    />
                    <span class="flex flex-1">
                      <span class="flex flex-col">
                        <span class="block text-sm font-medium text-gray-900">{{ gateway.name }}</span>
                        <span v-if="paymentMethod === gateway.slug && gateway.instruction" class="mt-2 text-sm text-gray-600 block pl-1 border-l-2 border-gray-300">
                          {{ gateway.instruction }}
                        </span>
                      </span>
                    </span>
                    <svg class="h-5 w-5 text-black" v-if="paymentMethod === gateway.slug" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                  </label>
                </div>

                <!-- Custom Card Form Mockup (Optional logic based on Gateway type) -->
                <div v-if="isValidCreditCardSelected" class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
                  <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Card Number</label>
                    <div class="relative">
                      <input v-model="form.card_number" type="text" placeholder="0000 0000 0000 0000" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-black focus:border-transparent text-sm transition-shadow" />
                    </div>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Expiry Date</label>
                    <input v-model="form.expiry_date" type="text" placeholder="MM/YY" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-black focus:border-transparent text-sm transition-shadow" />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">CVC / CVV</label>
                    <input v-model="form.cvc" type="text" placeholder="123" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-black focus:border-transparent text-sm transition-shadow" />
                  </div>
                </div>
              </div>
            </section>
          </div>

        </div>

        <!-- Right Column: Summary -->
        <aside class="lg:col-span-5 lg:sticky lg:top-24 animate-fade-in" style="animation-delay: 0.2s">
          <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Order Summary</h2>
            
            <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar mb-6">
              <div v-for="item in cart" :key="item.id" class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-200">
                  <img :src="item.image" class="w-full h-full object-cover" />
                </div>
                <div class="flex-1 min-w-0">
                  <h3 class="text-sm font-medium text-gray-900 truncate">{{ item.name }}</h3>
                  <p class="text-sm text-gray-500 mt-0.5">Qty: {{ item.quantity }}</p>
                </div>
                <div class="text-sm font-medium text-gray-900">
                  ${{ (parseFloat(item.price) * item.quantity).toFixed(2) }}
                </div>
              </div>
            </div>

            <div class="space-y-3 pt-6 border-t border-gray-100">
              <div class="flex justify-between text-sm text-gray-600">
                <span>Subtotal</span>
                <span class="font-medium text-gray-900">${{ cartTotal.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between text-sm text-gray-600">
                <span>Shipping</span>
                <span class="font-medium text-gray-900">$15.00</span>
              </div>
              <div class="flex justify-between text-sm text-gray-600">
                <span>Taxes</span>
                <span class="font-medium text-gray-900">Calculated at next step</span>
              </div>
            </div>
            
            <div class="flex justify-between items-end pt-6 mt-6 border-t border-gray-100">
              <span class="text-base font-semibold text-gray-900">Total</span>
              <div class="text-right">
                <span class="text-xs text-gray-500 font-medium mr-2">USD</span>
                <span class="text-2xl font-bold text-gray-900">${{ (cartTotal + 15).toFixed(2) }}</span>
              </div>
            </div>

            <button 
              class="w-full mt-8 py-3.5 bg-black text-white rounded-xl font-medium text-base shadow-sm hover:bg-gray-800 focus:ring-4 focus:ring-gray-200 transition-all flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed" 
              @click="placeOrder"
              :disabled="loading"
            >
              <span v-if="loading" class="flex items-center gap-2">
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Processing...
              </span>
              <span v-else>Pay ${{ (cartTotal + 15).toFixed(2) }}</span>
            </button>
            
            <div class="mt-6 flex items-center justify-center gap-2 text-xs text-gray-500">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              Secure 256-bit SSL encrypted checkout
            </div>
          </div>
        </aside>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
const { cart, cartTotal } = useCart()
const authStore = useAuthStore()
const router = useRouter()
const config = useRuntimeConfig()

const gateways = ref([])
const loadingGateways = ref(false)
const paymentMethod = ref('')
const loading = ref(false)

const form = ref({
  email: '',
  first_name: '',
  last_name: '',
  address: '',
  city: '',
  postal_code: '',
  card_number: '',
  expiry_date: '',
  cvc: ''
})

const isValidCreditCardSelected = computed(() => {
  const selected = gateways.value.find(g => g.slug === paymentMethod.value)
  return selected && ['stripe'].includes(selected.type)
})

const { getPaymentMethods } = useCrud()

const fetchGateways = async () => {
  if (cart.value.length === 0) return

  loadingGateways.value = true
  try {
    const payload = {
      items: cart.value.map(item => ({ id: item.id }))
    }
    const response = await getPaymentMethods('/storefront/checkout/payment-methods', payload)
    if (response.success) {
      gateways.value = response.gateways || []
      if (gateways.value.length > 0) {
        paymentMethod.value = gateways.value[0].slug
      }
    }
  } catch (error) {
    console.error('Failed to fetch gateways:', error)
  } finally {
    loadingGateways.value = false
  }
}

watch(() => cart.value, () => {
  if(gateways.value.length === 0 && cart.value.length > 0) {
     fetchGateways()
  }
}, { deep: true, immediate: true })

onMounted(() => {
  if (authStore.user) {
    form.value.email = authStore.user.email || ''
    const names = (authStore.user.name || '').split(' ')
    form.value.first_name = names[0] || ''
    form.value.last_name = names.slice(1).join(' ') || ''
  }
  
  fetchGateways()
})

const placeOrder = async () => {
  if (cart.value.length === 0) return

  loading.value = true
  try {
    const orderData = {
      ...form.value,
      payment_method: paymentMethod.value,
      items: cart.value.map(item => ({
        id: item.id,
        quantity: item.quantity
      }))
    }

    const response = await $fetch(`${config.public.apiBase}/storefront/checkout`, {
      method: 'POST',
      body: orderData
    })

    if (response.success) {
      alert('Order placed successfully! Thank you for your purchase.')
      // Clear cart
      cart.value = []
      // Redirect to home or a success page
      router.push('/')
    }
  } catch (error) {
    console.error('Checkout error:', error)
    alert('Failed to place order. Please try again.')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  opacity: 0;
  animation: fadeIn 0.6s ease-out forwards;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f8fafc;
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
