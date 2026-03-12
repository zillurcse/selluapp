<template>
  <div class="flex flex-col gap-4">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
      <h2 class="font-bold text-gray-900 mb-5">Wishlist ({{ wishlist.length }})</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="item in wishlist" :key="item.id" class="group relative border border-gray-100 rounded-2xl p-4 hover:border-gray-200 hover:shadow-sm transition-all flex flex-col items-center">
          <button @click="removeFromWishlist(item.id)" class="absolute top-2 right-2 p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full transition cursor-pointer" title="Remove">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
          </button>
          <div class="h-32 w-full bg-gray-50 rounded-xl mb-3 flex items-center justify-center overflow-hidden cursor-pointer" @click="$router.push(`/product/${item.slug || item.id}`)">
             <img v-if="item.image_url" :src="item.image_url" class="h-full w-full object-contain" />
             <div v-else class="text-6xl">{{ item.emoji || '📦' }}</div>
          </div>
          <div class="font-semibold text-gray-900 text-sm mb-1 text-center line-clamp-2 min-h-[40px]">{{ item.name }}</div>
          <div class="text-sm font-bold text-gray-900 mb-3 text-center">৳{{ item.sale_price || item.price }}</div>
          <button @click="handleAddToCart(item)" class="w-full mt-auto py-2 bg-gray-900 text-white text-xs font-bold rounded-xl hover:shadow-md transition border-none cursor-pointer">Add to Cart</button>
        </div>
        <div v-if="wishlist.length === 0" class="col-span-full py-12 flex flex-col items-center text-center text-gray-500">
          <div class="text-4xl mb-3">🖤</div>
          <h3 class="font-bold text-gray-900 mb-1">Your wishlist is empty</h3>
          <p class="text-sm">Save items you love to review them later.</p>
          <button @click="$router.push('/')" class="mt-4 px-6 py-2 bg-gray-100 text-gray-900 font-bold text-xs rounded-xl hover:bg-gray-200 transition border-none cursor-pointer">Continue Shopping</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { toast } from 'vue-sonner'

const { wishlist, removeFromWishlist } = useWishlist()
const { addToCart } = useCart()

const handleAddToCart = (item) => {
  addToCart(item, 1)
  toast.success(`${item.name} added to cart!`)
}
</script>
