<template>
  <div class="product-card group relative" @click="navigateToProduct">
    <!-- Image Section -->
    <div class="relative aspect-[4/5] rounded-[2rem] overflow-hidden bg-gray-50 border border-gray-100 transition-all duration-500 group-hover:shadow-2xl group-hover:shadow-gray-200 group-hover:-translate-y-1">
      <img :src="product.image" :alt="product.name" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" />
      
      <!-- Badges -->
      <div class="absolute top-4 left-4 z-10 flex flex-col gap-2">
        <span v-if="product.id % 3 === 0" class="px-3 py-1 bg-gray-900 text-white text-[10px] font-bold uppercase tracking-wider rounded-full">New</span>
        <span v-if="product.id % 4 === 0" class="px-3 py-1 bg-rose-500 text-white text-[10px] font-bold uppercase tracking-wider rounded-full">Sale</span>
      </div>

      <!-- Quick Actions Overlay -->
      <div class="absolute inset-0 bg-gradient-to-t from-gray-900/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-all duration-500 flex flex-col justify-end p-6">
        <button 
          class="w-full py-3 bg-white text-gray-900 rounded-full font-bold text-xs uppercase tracking-widest shadow-xl transform translate-y-4 group-hover:translate-y-0 transition-all duration-500 hover:bg-gray-900 hover:text-white flex items-center justify-center gap-2"
          @click.stop="$emit('add-to-cart', product)"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
          Quick Add
        </button>
      </div>
    </div>

    <!-- Info Section -->
    <div class="mt-5 space-y-1 px-2">
      <div class="flex justify-between items-center">
        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">{{ product.category }}</span>
        <div class="flex items-center gap-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="currentColor" class="text-amber-400"><path d="M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
          <span class="text-[10px] font-bold text-gray-400">4.9</span>
        </div>
      </div>
      
      <!-- Vendor Name -->
      <div v-if="product.vendor" class="flex items-center gap-1 mb-1">
        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">By</span>
        <NuxtLink 
          :to="`/shop/vendor/${product.vendor.slug}`" 
          class="text-[9px] font-bold text-indigo-600 hover:text-indigo-700 uppercase tracking-widest transition-colors"
          @click.stop
        >
          {{ product.vendor.name }}
        </NuxtLink>
      </div>

      <h3 class="text-base font-bold text-gray-900 group-hover:text-indigo-600 transition-colors duration-300 truncate font-heading">
        {{ product.name }}
      </h3>
      <div class="flex items-center gap-2">
        <span class="text-lg font-extrabold text-gray-900">${{ product.price }}</span>
        <span v-if="product.id % 4 === 0" class="text-xs text-gray-400 line-through">${{ (parseFloat(product.price) * 1.25).toFixed(2) }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['add-to-cart'])

const navigateToProduct = () => {
  navigateTo(`/shop/${props.product.slug}`)
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

