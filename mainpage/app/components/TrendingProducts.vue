<template>
  <section class="py-2 md:py-4 bg-gray-50/50">
    <div class="container mx-auto px-4 sm:px-6 text-center">
      <span class="inline-block text-[10px] font-bold uppercase tracking-[0.3em] text-rose-600 mb-6">Must Haves</span>
      <h2 class="text-4xl md:text-6xl font-extrabold tracking-tight text-gray-900 mb-8 font-heading">Trending Now</h2>
      
      <div class="flex flex-wrap justify-center gap-3 mb-16">
        <button
          v-for="tab in trendingTabs"
          :key="tab"
          @click="selectedCategory = tab"
          class="px-8 py-3 rounded-full text-xs font-bold uppercase tracking-widest transition-all cursor-pointer border-none outline-none focus:outline-none"
          :class="tab === selectedCategory ? 'bg-gray-900 text-white shadow-2xl scale-105' : 'bg-white text-gray-400 hover:text-gray-900 hover:shadow-lg'"
        >{{ tab }}</button>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-10">
        <ProductCard
          v-for="(product, idx) in filteredProducts"
          :key="product.id"
          :product="product"
          class="animate-fade-in"
          :style="{ animationDelay: `${idx * 0.1}s` }"
          @add-to-cart="$emit('add-to-cart', $event)"
        />
      </div>

      <div class="mt-20">
        <NuxtLink
          to="/shop"
          class="inline-flex items-center px-12 py-5 bg-gray-900 text-white font-bold text-sm uppercase tracking-[0.2em] rounded-full transition-all hover:-translate-y-1 hover:shadow-2xl active:translate-y-0"
        >Browse Full Catalog</NuxtLink>
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

<style scoped>
.font-heading {
  font-family: var(--font-heading);
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  opacity: 0;
  animation: fadeIn 1s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
}
</style>
