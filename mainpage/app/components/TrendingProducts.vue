<template>
  <section class="section-pad">
    <div class="container mx-auto text-center">
      <span class="section-label">Popular picks</span>
      <h2 class="section-title mt-2 mb-8">Trending now</h2>

      <div class="flex flex-wrap justify-center gap-2 mb-10">
        <button
          v-for="tab in trendingTabs"
          :key="tab"
          @click="selectedCategory = tab"
          class="px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all border"
          :class="tab === selectedCategory
            ? 'bg-[var(--primary)] text-white border-transparent'
            : 'bg-white text-[var(--muted-foreground)] border-[var(--border)] hover:text-gray-900 hover:border-gray-300'"
        >{{ tab }}</button>
      </div>

      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 text-left">
        <ProductCard
          v-for="product in filteredProducts"
          :key="product.id"
          :product="product"
          @add-to-cart="$emit('add-to-cart', $event)"
        />
      </div>

      <div class="mt-12">
        <NuxtLink
          :to="selectedCategory === 'All Items' ? '/shop?is_trending=true' : `/shop?category=${selectedCategory}&is_trending=true`"
          class="btn-primary"
        >Browse catalog</NuxtLink>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  products: {
    type: Array,
    required: true,
    default: () => []
  }
})

defineEmits(['add-to-cart'])

const selectedCategory = ref('All Items')

const trendingTabs = computed(() => {
  if (!props.products) return ['All Items']
  const cats = new Set(props.products.map(p => p.category))
  return ['All Items', ...Array.from(cats)].filter(c => c !== 'Uncategorized')
})

const filteredProducts = computed(() => {
  if (!props.products) return []
  if (selectedCategory.value === 'All Items') return props.products
  return props.products.filter(p => p.category === selectedCategory.value)
})
</script>
