<template>
  <div class="checkout-page bg-white min-h-screen relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-rose-50/50 rounded-full blur-[120px] -z-10 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-indigo-50/30 rounded-full blur-[100px] -z-10 pointer-events-none"></div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20 relative z-10">
      <header class="mb-12 md:mb-16 animate-fade-in">
        <NuxtLink to="/shop" class="inline-flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-widest hover:text-gray-900 transition-colors mb-6">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
          Back to Shop
        </NuxtLink>
        <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-gray-900 font-heading">Checkout</h1>
      </header>
      
      <div v-if="cart.length === 0" class="flex flex-col items-center justify-center py-20 animate-fade-in">
        <div class="w-24 h-24 rounded-full bg-gray-50 flex items-center justify-center text-4xl mb-8">🛒</div>
        <h2 class="text-2xl font-bold text-gray-900 mb-4 font-heading">Your cart is empty</h2>
        <p class="text-gray-500 mb-10 max-w-xs text-center leading-relaxed">Please add some items to your collection before proceeding to checkout.</p>
        <NuxtLink to="/shop" class="px-8 py-4 bg-gray-900 text-white rounded-full font-bold text-sm uppercase tracking-widest shadow-xl hover:-translate-y-1 transition-all">Start Shopping</NuxtLink>
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20 items-start">
        <!-- Checkout Form (Left) -->
        <div class="lg:col-span-7 space-y-12 animate-fade-in" style="animation-delay: 0.1s">
          <!-- Contact -->
          <section class="space-y-8">
            <div class="flex items-center gap-4">
              <span class="w-8 h-8 rounded-full bg-gray-900 text-white flex items-center justify-center text-xs font-bold">1</span>
              <h2 class="text-xl font-bold text-gray-900 font-heading uppercase tracking-widest">Contact Information</h2>
            </div>
            <div class="grid grid-cols-1 gap-6 p-8 rounded-[2rem] bg-gray-50/50 border border-gray-100 backdrop-blur-sm">
              <div class="space-y-2">
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 ml-1">Email Address</label>
                <input v-model="form.email" type="email" placeholder="you@example.com" class="w-full px-6 py-4 bg-white border border-gray-100 rounded-2xl outline-none focus:ring-2 focus:ring-gray-100 font-medium transition-all" />
              </div>
            </div>
          </section>

          <!-- Shipping -->
          <section class="space-y-8">
            <div class="flex items-center gap-4">
              <span class="w-8 h-8 rounded-full bg-gray-900 text-white flex items-center justify-center text-xs font-bold">2</span>
              <h2 class="text-xl font-bold text-gray-900 font-heading uppercase tracking-widest">Shipping Address</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-8 rounded-[2rem] bg-gray-50/50 border border-gray-100 backdrop-blur-sm">
              <div class="space-y-2">
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 ml-1">First Name</label>
                <input v-model="form.first_name" type="text" class="w-full px-6 py-4 bg-white border border-gray-100 rounded-2xl outline-none focus:ring-2 focus:ring-gray-100 font-medium transition-all" />
              </div>
              <div class="space-y-2">
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 ml-1">Last Name</label>
                <input v-model="form.last_name" type="text" class="w-full px-6 py-4 bg-white border border-gray-100 rounded-2xl outline-none focus:ring-2 focus:ring-gray-100 font-medium transition-all" />
              </div>
              <div class="md:col-span-2 space-y-2">
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 ml-1">Address</label>
                <input v-model="form.address" type="text" placeholder="Street address, apartment, suite, etc." class="w-full px-6 py-4 bg-white border border-gray-100 rounded-2xl outline-none focus:ring-2 focus:ring-gray-100 font-medium transition-all" />
              </div>
              <div class="space-y-2">
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 ml-1">City</label>
                <input v-model="form.city" type="text" class="w-full px-6 py-4 bg-white border border-gray-100 rounded-2xl outline-none focus:ring-2 focus:ring-gray-100 font-medium transition-all" />
              </div>
              <div class="space-y-2">
                <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 ml-1">Postal Code</label>
                <input v-model="form.postal_code" type="text" class="w-full px-6 py-4 bg-white border border-gray-100 rounded-2xl outline-none focus:ring-2 focus:ring-gray-100 font-medium transition-all" />
              </div>
            </div>
          </section>

          <!-- Payment -->
          <section class="space-y-8">
            <div class="flex items-center gap-4">
              <span class="w-8 h-8 rounded-full bg-gray-900 text-white flex items-center justify-center text-xs font-bold">3</span>
              <h2 class="text-xl font-bold text-gray-900 font-heading uppercase tracking-widest">Payment Method</h2>
            </div>
            <div class="p-8 rounded-[2rem] bg-gray-50/50 border border-gray-100 backdrop-blur-sm space-y-8">
              <div class="flex flex-wrap gap-4">
                <button 
                  @click="paymentMethod = 'card'"
                  class="flex-1 min-w-[140px] px-6 py-4 rounded-2xl border-2 transition-all flex items-center justify-center gap-3 font-bold text-sm"
                  :class="paymentMethod === 'card' ? 'bg-gray-900 border-gray-900 text-white shadow-xl translate-y-[-2px]' : 'bg-white border-gray-100 text-gray-400 hover:border-gray-200'"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                  Card
                </button>
                <button 
                  @click="paymentMethod = 'paypal'"
                  class="flex-1 min-w-[140px] px-6 py-4 rounded-2xl border-2 transition-all flex items-center justify-center gap-3 font-bold text-sm"
                  :class="paymentMethod === 'paypal' ? 'bg-gray-900 border-gray-900 text-white shadow-xl translate-y-[-2px]' : 'bg-white border-gray-100 text-gray-400 hover:border-gray-200'"
                >
                  <span class="text-xl">🅿️</span>
                  PayPal
                </button>
              </div>

              <div v-if="paymentMethod === 'card'" class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                <div class="md:col-span-2 space-y-2">
                  <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 ml-1">Card Number</label>
                  <div class="relative">
                    <input v-model="form.card_number" type="text" placeholder="0000 0000 0000 0000" class="w-full px-6 py-4 bg-white border border-gray-100 rounded-2xl outline-none focus:ring-2 focus:ring-gray-100 font-medium transition-all" />
                    <div class="absolute right-6 top-1/2 -translate-y-1/2 flex gap-2">
                      <div class="w-8 h-5 bg-gray-200 rounded"></div>
                      <div class="w-8 h-5 bg-gray-300 rounded"></div>
                    </div>
                  </div>
                </div>
                <div class="space-y-2">
                  <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 ml-1">Expiry Date</label>
                  <input v-model="form.expiry_date" type="text" placeholder="MM/YY" class="w-full px-6 py-4 bg-white border border-gray-100 rounded-2xl outline-none focus:ring-2 focus:ring-gray-100 font-medium transition-all" />
                </div>
                <div class="space-y-2">
                  <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 ml-1">CVC / CVV</label>
                  <input v-model="form.cvc" type="text" placeholder="123" class="w-full px-6 py-4 bg-white border border-gray-100 rounded-2xl outline-none focus:ring-2 focus:ring-gray-100 font-medium transition-all" />
                </div>
              </div>
              <div v-else class="pt-4 text-center py-12 px-6 border border-dashed border-gray-200 rounded-2xl bg-white">
                <p class="text-sm text-gray-500 mb-4">You will be redirected to PayPal to complete your purchase securely.</p>
                <div class="text-4xl">🅿️</div>
              </div>
            </div>
          </section>

          <button 
            class="w-full py-6 bg-gray-900 text-white rounded-[2rem] font-bold text-lg uppercase tracking-[0.2em] shadow-2xl shadow-gray-200 hover:shadow-gray-300 hover:-translate-y-1 active:translate-y-0 transition-all duration-300 flex items-center justify-center gap-4 disabled:opacity-70 disabled:cursor-not-allowed" 
            @click="placeOrder"
            :disabled="loading"
          >
            <span v-if="!loading">Complete Order</span>
            <span v-else class="flex items-center gap-3">
              <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
              Processing...
            </span>
            <svg v-if="!loading" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          </button>
        </div>

        <!-- Order Summary (Right) -->
        <aside class="lg:col-span-5 lg:sticky lg:top-24 animate-fade-in" style="animation-delay: 0.2s">
          <div class="p-10 rounded-[2.5rem] bg-gray-900 text-white shadow-2xl space-y-10 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-[80px] -translate-y-1/2 translate-x-1/2 group-hover:scale-125 transition-transform duration-1000"></div>
            
            <h2 class="text-2xl font-bold font-heading uppercase tracking-widest">Order Summary</h2>
            
            <div class="space-y-6 max-h-[400px] overflow-y-auto pr-4 custom-scrollbar">
              <div v-for="item in cart" :key="item.id" class="flex justify-between items-center gap-6 group/item">
                <div class="flex gap-4">
                  <div class="w-16 h-20 rounded-xl overflow-hidden bg-white/10 flex-shrink-0">
                    <img :src="item.image" class="w-full h-full object-cover opacity-80 group-hover/item:opacity-100 transition-opacity" />
                  </div>
                  <div class="flex flex-col justify-center">
                    <p class="font-bold text-sm leading-tight mb-1">{{ item.name }}</p>
                    <p class="text-xs text-white/50">Quantity: {{ item.quantity }}</p>
                  </div>
                </div>
                <p class="font-bold text-sm">${{ (parseFloat(item.price) * item.quantity).toFixed(2) }}</p>
              </div>
            </div>

            <div class="space-y-4 pt-8 border-t border-white/10">
              <div class="flex justify-between text-white/50 text-sm">
                <span>Subtotal</span>
                <span>${{ cartTotal.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between text-white/50 text-sm">
                <span>Shipping</span>
                <span>$15.00</span>
              </div>
              <div class="flex justify-between text-white/50 text-sm">
                <span>Taxes (Included)</span>
                <span>$0.00</span>
              </div>
              
              <div class="flex justify-between items-end pt-6 mt-6 border-t border-white/20">
                <div class="space-y-1">
                  <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest">Total Amount</p>
                  <p class="text-4xl font-extrabold font-heading tracking-tight">${{ (cartTotal + 15).toFixed(2) }}</p>
                </div>
                <div class="text-xs text-white/30 font-bold uppercase tracking-widest text-right">USD</div>
              </div>
            </div>

            <!-- Trust Badge -->
            <div class="pt-6 flex justify-center border-t border-white/5">
              <div class="flex items-center gap-2 text-[10px] font-bold text-white/40 uppercase tracking-widest">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-500"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/></svg>
                Secure 256-bit SSL encrypted checkout
              </div>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
const { cart, cartTotal } = useCart()
const authStore = useAuthStore()
const router = useRouter()
const config = useRuntimeConfig()

const paymentMethod = ref('card')
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

onMounted(() => {
  if (authStore.user) {
    form.value.email = authStore.user.email || ''
    const names = (authStore.user.name || '').split(' ')
    form.value.first_name = names[0] || ''
    form.value.last_name = names.slice(1).join(' ') || ''
  }
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
.font-heading {
  font-family: var(--font-heading);
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  opacity: 0;
  animation: fadeIn 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(255,255,255,0.05);
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(255,255,255,0.1);
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(255,255,255,0.2);
}
</style>

