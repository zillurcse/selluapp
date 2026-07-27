<template>
  <div v-if="categories && categories.length > 0">
    <section
      v-for="(category, index) in categories"
      :key="category.id"
      class="section-pad"
      :class="index % 2 === 0 ? 'bg-white' : 'bg-[var(--surface)]'"
    >
      <div class="container mx-auto">
        <div class="flex items-end justify-between mb-8 gap-4">
          <div>
            <span class="section-label">Collection</span>
            <h2 class="section-title mt-1">{{ category.name }}</h2>
          </div>
          <NuxtLink
            :to="`/shop?category=${category.slug}`"
            class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-gray-900 hover:opacity-60 transition-opacity"
          >
            View all
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m9 18 6-6-6-6"/></svg>
          </NuxtLink>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
          <ProductCard
            v-for="product in category.products.slice(0, 4)"
            :key="product.id"
            :product="product"
            @add-to-cart="$emit('add-to-cart', $event)"
          />
        </div>

        <div class="mt-8 text-center sm:hidden">
          <NuxtLink :to="`/shop?category=${category.slug}`" class="btn-primary w-full">
            View all {{ category.name }}
          </NuxtLink>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
defineProps({
  categories: {
    type: Array,
    required: true,
    default: () => []
  }
})

defineEmits(['add-to-cart'])
</script>
