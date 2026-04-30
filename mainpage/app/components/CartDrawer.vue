<template>
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="isCartOpen" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[1000]" @click="closeCart"></div>
    </Transition>

    <Transition name="slide">
      <div v-if="isCartOpen" class="fixed top-0 right-0 bottom-0 w-full max-w-[450px] bg-white z-[1001] flex flex-col shadow-[-20px_0_60px_rgba(0,0,0,0.1)]">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
          <div class="flex items-center gap-2.5">
            <h2 class="text-lg font-bold text-gray-900 tracking-tight">Your Cart</h2>
            <span class="bg-gray-900 text-white text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center leading-none">{{ cartCount }}</span>
          </div>
          <button class="p-2 rounded-xl hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-900" @click="closeCart">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
          </button>
        </div>

        <!-- Empty State -->
        <div v-if="cart.length === 0" class="flex-1 flex flex-col items-center justify-center p-8 text-center">
          <div class="w-24 h-24 rounded-[2rem] bg-gray-50 flex items-center justify-center text-4xl mb-6">🛒</div>
          <h3 class="text-xl font-bold text-gray-900 mb-2">Your cart is empty</h3>
          <p class="text-sm text-gray-400 font-medium mb-8">Add some items to get started</p>
          <button
            class="px-8 py-3 bg-gray-900 text-white rounded-xl font-bold text-sm hover:bg-indigo-600 transition-colors"
            @click="closeCart"
          >Start Shopping</button>
        </div>

        <!-- Cart Items -->
        <div v-else class="flex-1 overflow-y-auto px-5 py-4 space-y-3">
          <div v-for="item in cart" :key="item.id" class="flex gap-4 p-4 rounded-2xl border border-gray-100 hover:border-gray-200 transition-colors bg-white">
            <div class="w-[88px] h-[100px] rounded-xl overflow-hidden bg-gray-50 flex-shrink-0">
              <img :src="item.image" :alt="item.name" class="w-full h-full object-cover" />
            </div>
            <div class="flex-1 flex flex-col justify-between min-w-0 py-0.5">
              <div>
                <h3 class="text-sm font-bold text-gray-900 leading-snug line-clamp-2 mb-1">{{ item.name }}</h3>
                <p class="text-base font-extrabold text-gray-900">৳{{ item.price }}</p>
              </div>
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5 border border-gray-200 rounded-full px-3 py-1.5">
                  <button
                    class="w-4 h-4 flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors font-bold text-base leading-none"
                    @click="updateQuantity(item.id, item.quantity - 1)"
                  >−</button>
                  <span class="text-sm font-bold text-gray-900 min-w-[18px] text-center">{{ item.quantity }}</span>
                  <button
                    class="w-4 h-4 flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors font-bold text-base leading-none"
                    @click="updateQuantity(item.id, item.quantity + 1)"
                  >+</button>
                </div>
                <button
                  class="text-xs font-semibold text-rose-400 hover:text-rose-600 transition-colors underline underline-offset-2"
                  @click="removeFromCart(item.id)"
                >Remove</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div v-if="cart.length > 0" class="px-6 py-5 border-t border-gray-100 space-y-4 bg-gray-50/50">
          <div class="flex items-center justify-between">
            <span class="text-sm font-semibold text-gray-500">Subtotal</span>
            <span class="text-xl font-extrabold text-gray-900">৳{{ cartTotal.toFixed(2) }}</span>
          </div>
          <p class="text-xs text-gray-400 font-medium -mt-1">Shipping & taxes calculated at checkout.</p>
          <NuxtLink
            to="/checkout"
            class="flex items-center justify-center gap-2 w-full py-4 bg-gray-900 text-white rounded-xl font-bold text-sm hover:bg-indigo-600 transition-colors shadow-lg shadow-gray-900/10"
            @click="closeCart"
          >
            Proceed to Checkout
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          </NuxtLink>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { watch } from 'vue'

const { cart, isCartOpen, closeCart, removeFromCart, updateQuantity, cartTotal, cartCount } = useCart()
const { trackViewCart } = useTracking()

watch(isCartOpen, (newVal) => {
  if (newVal && cart.value.length > 0) {
    trackViewCart({
      total: cartTotal.value,
      itemCount: cartCount.value,
      items: cart.value
    })
  }
})
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.slide-enter-active, .slide-leave-active {
  transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}
.slide-enter-from, .slide-leave-to {
  transform: translateX(100%);
}
</style>
