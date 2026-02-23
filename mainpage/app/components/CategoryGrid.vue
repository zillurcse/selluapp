<template>
  <section class="py-12 bg-white relative">
    <div class="container mx-auto px-4 sm:px-6">
      <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-8">
        <div class="max-w-xl">
          <span class="inline-block text-[10px] font-bold uppercase tracking-[0.3em] text-indigo-600 mb-4">Discovery</span>
          <h2 class="text-4xl md:text-5xl font-extrabold tracking-tight text-gray-900 font-heading">Explore our Curated Collections</h2>
        </div>
        <p class="text-gray-500 text-lg md:text-xl font-light max-w-sm">Handcrafted elegance for every room of your modern sanctuary.</p>
      </div>
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-6 md:gap-10">
        <NuxtLink
          v-for="cat in categories"
          :key="cat.name"
          :to="`/shop?category=${cat.slug}`"
          class="group flex flex-col items-center gap-6 no-underline text-inherit"
        >
          <div class="w-24 h-24 sm:w-32 sm:h-32 md:w-40 md:h-40 rounded-[2.5rem] flex items-center justify-center text-4xl sm:text-5xl bg-gray-50 border border-gray-100 shadow-sm transition-all duration-700 group-hover:bg-gray-900 group-hover:shadow-2xl group-hover:shadow-gray-200 group-hover:-translate-y-4">
            <span class="group-hover:scale-125 transition-transform duration-700">{{ cat.icon }}</span>
          </div>
          <span class="text-xs font-bold text-gray-400 uppercase tracking-widest group-hover:text-gray-900 transition-colors">{{ cat.name }}</span>
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
.font-heading {
  font-family: var(--font-heading);
}
</style>
