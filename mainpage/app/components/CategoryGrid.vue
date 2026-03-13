<template>
  <section class="py-12 bg-[#fafcfd] relative overflow-hidden">
    <!-- Decorative glassmorphic background elements -->
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-indigo-50/50 rounded-full blur-[100px] -z-10"></div>
    <div class="absolute -bottom-32 -left-32 w-80 h-80 bg-cyan-50/40 rounded-full blur-[120px] -z-10"></div>
    
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 sm:mb-12 gap-4 sm:gap-6">
        <div class="space-y-1 sm:space-y-2">
          <span class="text-[9px] sm:text-[10px] font-black uppercase tracking-[0.3em] text-indigo-500/80 ml-1">Explore Collections</span>
          <h2 class="text-2xl sm:text-3xl md:text-5xl font-black text-gray-900 tracking-tight leading-none">
            Shop By <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-cyan-500">Categories</span>
          </h2>
        </div>
        
        <NuxtLink 
          to="/shop" 
          class="group/all flex items-center gap-2 sm:gap-3 px-4 py-2 sm:px-6 sm:py-3 bg-white border border-gray-100 rounded-xl sm:rounded-2xl shadow-sm hover:shadow-md hover:border-indigo-100 transition-all duration-300 w-fit"
        >
          <span class="text-xs sm:text-sm font-bold text-gray-900 group-hover/all:text-indigo-600 transition-colors">See All Collections</span>
          <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl bg-gray-50 flex items-center justify-center group-hover/all:bg-indigo-50 group-hover/all:translate-x-1 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="text-indigo-600 sm:w-4 sm:h-4"><path d="m9 18 6-6-6-6"/></svg>
          </div>
        </NuxtLink>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 sm:gap-6 md:gap-8">
        <NuxtLink
          v-for="cat in categories"
          :key="cat.name"
          :to="`/shop?category=${cat.slug}`"
          class="category-card group"
        >
          <div class="card-glass-bg"></div>
          
          <div class="icon-container">
            <div class="icon-blob"></div>
            <span class="icon-text">{{ cat.icon }}</span>
          </div>
          
          <div class="category-info">
            <span class="category-name">{{ cat.name }}</span>
            <div class="category-stats">
              <span class="text-[10px] font-bold text-gray-400 group-hover:text-indigo-500 transition-colors uppercase tracking-widest">
                {{ cat.children?.length || 0 }} Items
              </span>
            </div>
          </div>
          
          <!-- Hover Arrow -->
          <div class="hover-arrow">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
          </div>
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
  @apply relative flex flex-col items-center justify-center p-4 sm:p-8 rounded-[1.5rem] sm:rounded-[2.5rem] transition-all duration-500 hover:-translate-y-2 no-underline overflow-hidden min-h-[160px] sm:min-h-[220px];
}

.card-glass-bg {
  @apply absolute inset-0 bg-white border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.02)] transition-all duration-500 -z-10 rounded-[1.5rem] sm:rounded-[2.5rem];
}

.category-card:hover .card-glass-bg {
  @apply border-indigo-100 shadow-[0_20px_50px_rgba(79,70,229,0.1)];
  background: linear-gradient(135deg, #ffffff 0%, #f5f7ff 100%);
}

.icon-container {
  @apply relative mb-4 sm:mb-6 flex items-center justify-center w-14 h-14 sm:w-20 sm:h-20;
}

.icon-blob {
  @apply absolute inset-0 bg-gray-50 rounded-2xl sm:rounded-[2rem] rotate-12 transition-all duration-700 group-hover:rotate-[25deg] group-hover:scale-110 group-hover:bg-indigo-50;
}

.icon-text {
  @apply relative text-2xl sm:text-4xl transition-transform duration-500 group-hover:scale-110 group-hover:-rotate-6;
}

.category-info {
  @apply flex flex-col items-center gap-1;
}

.category-name {
  @apply text-sm md:text-base font-black text-gray-900 tracking-tight transition-colors duration-300 group-hover:text-indigo-600;
}

.hover-arrow {
  @apply absolute bottom-6 opacity-0 translate-y-4 text-indigo-500 transition-all duration-500 group-hover:opacity-100 group-hover:translate-y-0;
}

/* Subtle pattern for first card to add variety if needed */
.category-card:first-child .card-glass-bg::after {
  content: '';
  @apply absolute top-0 right-0 w-16 h-16 bg-gradient-to-br from-indigo-500/5 to-transparent rounded-bl-[3rem];
}
</style>
