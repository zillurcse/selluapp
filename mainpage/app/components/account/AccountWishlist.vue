<template>
  <div class="flex flex-col gap-4">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
      <h2 class="font-bold text-gray-900 mb-5">Wishlist ({{ wishlistItems.length }})</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="item in wishlistItems" :key="item.id" class="group border border-gray-100 rounded-2xl p-4 hover:border-gray-200 hover:shadow-sm transition-all text-center">
          <div class="text-6xl mb-3 py-2">{{ item.emoji || '📦' }}</div>
          <div class="font-semibold text-gray-900 text-sm mb-1">{{ item.name }}</div>
          <div class="text-sm font-bold text-gray-900 mb-3">${{ item.price }}</div>
          <button class="w-full py-2 bg-gray-900 text-white text-xs font-bold rounded-xl hover:shadow-md transition border-none cursor-pointer">Add to Cart</button>
        </div>
        <div v-if="wishlistItems.length === 0" class="col-span-full py-8 text-center text-gray-500 text-sm">
          Your wishlist is currently empty.
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  wishlistItems: { type: Array, default: () => [] }
})
</script>
