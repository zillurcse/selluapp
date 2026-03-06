<template>
  <section class="pt-16  bg-white relative overflow-hidden">
    <!-- Decorative subtle background -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-cyan-50/30 rounded-full blur-3xl -z-10"></div>
    
    <div class="container mx-auto px-4 sm:px-6">
      <div class="flex items-center justify-between mb-10">
        <h2 class="text-2xl md:text-3xl font-bold text-[#10839e] tracking-tight">Shop By Categories</h2>
        
        <div class="flex items-center gap-6">
          <NuxtLink to="/shop" class="text-sm font-bold text-[#10839e] hover:opacity-80 transition-opacity flex items-center gap-1">
            See All
          </NuxtLink>
          
          <div class="flex items-center gap-2">
            <button class="p-2 rounded-lg border border-gray-100 text-gray-300 hover:text-[#10839e] hover:border-[#e0f7fa] transition-all">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            </button>
            <button class="p-2 rounded-lg border border-gray-100 text-gray-300 hover:text-[#10839e] hover:border-[#e0f7fa] transition-all">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6" transform="rotate(180 12 12)"/></svg>
            </button>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 md:gap-5">
        <NuxtLink
          v-for="cat in categories"
          :key="cat.name"
          :to="`/shop?category=${cat.slug}`"
          class="category-card group"
        >
          <div class="icon-wrapper">
            {{ cat.icon }}
          </div>
          <span class="category-name">{{ cat.name }}</span>
          
          <!-- Caret Shadow/Indicator -->
          <div class="caret-indicator"></div>
        </NuxtLink>
      </div>
    </div>
  </section>
</template>

<script setup>
defineProps({
  categories: {
    type: Array,
    required: true
  }
})
</script>

<style scoped>
.category-card {
  @apply relative flex flex-col items-center justify-center aspect-square rounded-xl border-2 border-[#e0f7fa] bg-transparent transition-all duration-300 no-underline;
}

.category-card:hover {
  @apply bg-[#e0f7fa] -translate-y-1;
}

.icon-wrapper {
  @apply text-4xl mb-4 transition-transform duration-500 group-hover:scale-110;
}

.category-name {
  @apply text-[0.75rem] md:text-[0.85rem] font-black uppercase tracking-wider text-[#10839e] text-center px-2 leading-tight;
}

.caret-indicator {
  @apply absolute -bottom-2 left-1/2 -translate-x-1/2 w-4 h-4 bg-[#e0f7fa] rotate-45 opacity-0 transition-opacity duration-300;
  clip-path: polygon(100% 100%, 0% 100%, 100% 0%);
}

.category-card:hover .caret-indicator {
  @apply opacity-100;
}

/* For browsers that don't support group-hover adequately */
.category-card:hover .icon-wrapper {
  transform: scale(1.1);
}
</style>
