<template>
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="isWishlistOpen" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[1000]" @click="closeWishlist"></div>
    </Transition>

    <Transition name="slide">
      <div v-if="isWishlistOpen" class="fixed top-0 right-0 bottom-0 w-full max-w-[450px] bg-white z-[1001] flex flex-col shadow-[-20px_0_60px_rgba(0,0,0,0.1)]">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
          <div class="flex items-center gap-2.5">
            <h2 class="text-lg font-bold text-gray-900 tracking-tight">My Wishlist</h2>
            <span class="bg-rose-500 text-white text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center leading-none">{{ wishlistCount }}</span>
          </div>
          <button class="p-2 rounded-xl hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-900" @click="closeWishlist">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
          </button>
        </div>

        <!-- Empty State -->
        <div v-if="wishlist.length === 0" class="flex-1 flex flex-col items-center justify-center p-8 text-center">
          <div class="w-24 h-24 rounded-[2rem] bg-rose-50 flex items-center justify-center text-4xl mb-6">🖤</div>
          <h3 class="text-xl font-bold text-gray-900 mb-2">Your wishlist is empty</h3>
          <p class="text-sm text-gray-400 font-medium mb-8">Save items you love for later</p>
          <button
            class="px-8 py-3 bg-gray-900 text-white rounded-xl font-bold text-sm hover:bg-indigo-600 transition-colors"
            @click="closeWishlist"
          >Discover Items</button>
        </div>

        <!-- Wishlist Items -->
        <div v-else class="flex-1 overflow-y-auto px-5 py-4 space-y-3">
          <div v-for="item in wishlist" :key="item.id" class="flex gap-4 p-4 rounded-2xl border border-gray-100 hover:border-gray-200 transition-colors bg-white group">
            <div
              class="w-[88px] h-[100px] rounded-xl overflow-hidden bg-gray-50 flex-shrink-0 cursor-pointer"
              @click="navigateToProduct(item)"
            >
              <img v-if="item.image_url" :src="item.image_url" :alt="item.name" class="w-full h-full object-cover" />
              <img v-else-if="item.image" :src="item.image" :alt="item.name" class="w-full h-full object-cover" />
              <div v-else class="flex h-full w-full items-center justify-center text-3xl">{{ item.emoji || '📦' }}</div>
            </div>
            <div class="flex-1 flex flex-col justify-between min-w-0 py-0.5">
              <div>
                <h3
                  class="text-sm font-bold text-gray-900 leading-snug line-clamp-2 mb-1 cursor-pointer hover:text-indigo-600 transition-colors"
                  @click="navigateToProduct(item)"
                >{{ item.name }}</h3>
                <p class="text-base font-extrabold text-gray-900">৳{{ item.sale_price || item.price }}</p>
              </div>
              <div class="flex items-center gap-2 mt-2">
                <button
                  class="flex-1 flex items-center justify-center gap-1.5 py-2 bg-gray-900 text-white rounded-lg font-bold text-xs hover:bg-indigo-600 transition-colors"
                  @click="handleAddToCart(item)"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                  Add to Cart
                </button>
                <button
                  class="p-2 rounded-lg border border-gray-200 text-gray-400 hover:text-rose-500 hover:border-rose-200 transition-colors"
                  @click="removeFromWishlist(item.id)"
                  title="Remove"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-5 border-t border-gray-100 bg-gray-50/50">
          <button
            class="flex items-center justify-center gap-2 w-full py-3.5 border-2 border-gray-200 text-gray-700 rounded-xl font-bold text-sm hover:border-gray-900 hover:text-gray-900 transition-colors"
            @click="navigateTo('/account/wishlist'); closeWishlist()"
          >
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
            View Full Wishlist
          </button>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { toast } from 'vue-sonner'

const { wishlist, isWishlistOpen, closeWishlist, removeFromWishlist, wishlistCount } = useWishlist()
const { addToCart } = useCart()

const handleAddToCart = (item) => {
  addToCart(item, 1)
  toast.success(`${item.name} added to cart!`)
}

const navigateToProduct = (item) => {
  closeWishlist()
  navigateTo(`/product/${item.slug || item.id}`)
}
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
