<template>
  <div class="product-card group relative" @click="navigateToProduct">
    <!-- Image Section -->
    <div class="relative aspect-[4/5] rounded-[16px] overflow-hidden bg-gray-50 border border-gray-100 transition-all duration-500 group-hover:shadow-2xl group-hover:shadow-gray-200 group-hover:-translate-y-1">
      <img :src="product.image" :alt="product.name" class="w-full h-full object-cover rounded-[14px] transition-transform duration-700 group-hover:scale-110" />
      
      <!-- Badges & Wishlist -->
      <div class="absolute top-4 w-full px-4 z-10 flex justify-between items-start pointer-events-none">
        <div class="flex flex-col gap-2">
          <span v-if="product.id % 3 === 0" class="px-3 py-1 bg-gray-900 text-white text-[10px] font-bold uppercase tracking-wider rounded-[10px] shadow-md">New</span>
          <span v-if="product.id % 4 === 0" class="px-3 py-1 bg-rose-500 text-white text-[10px] font-bold uppercase tracking-wider rounded-[10px] shadow-md">Sale</span>
        </div>
        <button 
          @click.stop="handleWishlist"
          class="p-2 rounded-[50%] backdrop-blur-md bg-white/50 border border-white/40 shadow-sm hover:shadow-md transition-all pointer-events-auto"
          :class="isInWishlist(product.id) ? 'text-rose-500' : 'text-gray-900 hover:text-rose-500'"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-colors duration-300" :class="{ 'fill-rose-500 text-rose-500': isInWishlist(product.id) }"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
        </button>
      </div>

      <!-- Quick Actions Overlay -->
      <div class="absolute inset-0 bg-gradient-to-t from-gray-900/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex flex-col justify-end p-2 sm:p-4 md:p-6 pointer-events-none">
        <button 
          class="w-full py-2 sm:py-3 bg-white text-gray-900 rounded-[10px] font-bold text-[9px] sm:text-xs uppercase tracking-wider md:tracking-widest shadow-xl transform translate-y-4 group-hover:translate-y-0 transition-all duration-500 hover:bg-gray-900 hover:text-white flex items-center justify-center gap-1 sm:gap-2 pointer-events-auto"
          @click.stop="$emit('add-to-cart', product)"
        >
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
          <span class="truncate">Quick Add</span>
        </button>
      </div>
    </div>

    <!-- Info Section -->
    <div class="mt-3 sm:mt-5 space-y-0.5 sm:space-y-1 px-1 sm:px-2">
      <div class="flex justify-between items-center">
        <span class="text-[8px] sm:text-[10px] font-bold uppercase tracking-widest text-gray-400">{{ product.category }}</span>
        <div v-if="product.rating" class="flex items-center gap-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="8" height="8" viewBox="0 0 24 24" fill="currentColor" class="text-amber-400 sm:w-2.5 sm:h-2.5"><path d="M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
          <span class="text-[8px] sm:text-[10px] font-bold text-gray-900">{{ product.rating }}</span>
          <span v-if="product.reviewsCount" class="text-[7px] sm:text-[9px] text-gray-400 font-medium">({{ product.reviewsCount }})</span>
        </div>
        <div v-else class="flex items-center gap-1">
          <span class="text-[8px] font-bold text-indigo-500 uppercase tracking-tighter bg-indigo-50 px-1.5 py-0.5 rounded">New</span>
        </div>
      </div>
      
      <!-- Vendor Name -->
      <div v-if="product.vendor" class="flex items-center gap-1 mb-0.5 sm:mb-1">
        <span class="text-[7px] sm:text-[9px] font-bold text-gray-400 uppercase tracking-widest">By</span>
        <NuxtLink 
          :to="`/shop/vendor/${product.vendor.slug}`" 
          class="text-[7px] sm:text-[9px] font-bold text-indigo-600 hover:text-indigo-700 uppercase tracking-widest transition-colors"
          @click.stop
        >
          {{ product.vendor.name }}
        </NuxtLink>
      </div>

      <h3 class="text-xs sm:text-base font-bold text-gray-900 group-hover:text-indigo-600 transition-colors duration-300 truncate font-heading">
        {{ product.name }}
      </h3>
      <div class="flex items-center gap-1.5 sm:gap-2">
        <span class="text-sm sm:text-lg font-extrabold text-gray-900">৳{{ product.price }}</span>
        <span v-if="product.id % 4 === 0" class="text-[10px] sm:text-xs text-gray-400 line-through">৳{{ (parseFloat(product.price) * 1.25).toFixed(2) }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { toast } from 'vue-sonner'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['add-to-cart'])

const { isInWishlist, toggleWishlist } = useWishlist()

const navigateToProduct = () => {
  navigateTo(`/product/${props.product.slug}`)
}

const handleWishlist = () => {
  if (!props.product?.id) return
  const added = toggleWishlist(props.product)
  if (added) {
    toast.success(`${props.product.name} added to wishlist!`)
  } else {
    toast.info(`${props.product.name} removed from wishlist.`)
  }
}
</script>

<style scoped>
.product-card {
  cursor: pointer;
}

.font-heading {
  font-family: var(--font-heading);
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.product-card {
  animation: fadeIn 0.6s ease forwards;
}
</style>

