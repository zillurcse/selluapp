<template>
  <section class="section-pad bg-[var(--surface)]">
    <div class="container mx-auto">
      <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 sm:mb-10 gap-4">
        <div class="space-y-2">
          <span class="section-label">Browse</span>
          <h2 class="section-title">Shop by category</h2>
        </div>
        <NuxtLink to="/shop" class="btn-secondary text-xs sm:text-sm w-fit">
          View all
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
        </NuxtLink>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        <NuxtLink
          v-for="cat in categories"
          :key="cat.name"
          :to="`/shop?category=${cat.slug}`"
          class="group flex flex-col items-center text-center gap-3 p-5 sm:p-6 rounded-2xl bg-white border border-[var(--border)] transition-all duration-200 hover:border-gray-300 hover:shadow-sm"
        >
          <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-[var(--muted)] flex items-center justify-center text-2xl sm:text-3xl transition-transform duration-300 group-hover:scale-105">
            {{ cat.icon || cat.name?.charAt(0) }}
          </div>
          <div>
            <span class="block text-sm font-semibold text-gray-900 group-hover:opacity-70 transition-opacity">
              {{ cat.name }}
            </span>
            <span class="block text-[11px] text-[var(--muted-foreground)] mt-0.5">
              {{ cat.children?.length || 0 }} collections
            </span>
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
