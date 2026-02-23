<template>
  <section class="py-4 md:py-12 bg-white">
    <div class="container mx-auto px-4 sm:px-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Banner 1 -->
        <div class="relative overflow-hidden rounded-[3rem] bg-indigo-50 flex items-center p-12 md:p-16 group">
          <div class="relative z-10 max-w-sm">
            <span class="inline-block text-[10px] font-bold uppercase tracking-widest text-indigo-600 mb-4">Limited Edition</span>
            <h3 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight mb-4 font-heading leading-tight">Spring Refresh <br/>Up to 50% Off</h3>
            <p class="text-gray-500 text-lg mb-8 font-medium">Sustainable lighting collection</p>
            <NuxtLink to="/shop" class="inline-flex items-center gap-3 font-bold text-gray-900 text-sm uppercase tracking-widest group-hover:gap-6 transition-all duration-300">
              Explore Collection
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </NuxtLink>
          </div>
          <div class="absolute -right-10 -bottom-10 text-[12rem] opacity-5 transition-transform duration-700 group-hover:scale-125 group-hover:-rotate-12 select-none pointer-events-none">💡</div>
        </div>
        <!-- Banner 2 -->
        <div class="relative overflow-hidden rounded-[3rem] bg-gray-900 flex items-center p-12 md:p-16 group">
          <div class="relative z-10 max-w-sm">
            <span class="inline-block text-[10px] font-bold uppercase tracking-widest text-white/40 mb-4">Curation</span>
            <h3 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight mb-4 font-heading leading-tight">The Ceramic <br/>Zen Series</h3>
            <p class="text-white/60 text-lg mb-8 font-medium">Minimalist art for living</p>
            <NuxtLink to="/shop" class="inline-flex items-center gap-3 font-bold text-white text-sm uppercase tracking-widest group-hover:gap-6 transition-all duration-300">
              Discovery Now
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </NuxtLink>
          </div>
          <div class="absolute -right-10 -bottom-10 text-[12rem] opacity-[0.03] text-white transition-transform duration-700 group-hover:scale-125 group-hover:rotate-12 select-none pointer-events-none">🏺</div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.font-heading {
  font-family: var(--font-heading);
}
</style>
