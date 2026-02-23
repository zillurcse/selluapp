<template>
  <div v-if="categories && categories.length > 0">
    <section v-for="(category, index) in categories" :key="category.id" class="py-12 md:py-20" :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50/50'">
      <div class="container mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between mb-10">
          <div>
            <h2 class="text-3xl md:text-5xl font-extrabold tracking-tight text-gray-900 font-heading">{{ category.name }}</h2>
            <div class="w-20 h-1 bg-rose-600 mt-4 rounded-full"></div>
          </div>
          <NuxtLink :to="`/shop?category=${category.slug}`" class="text-sm font-bold uppercase tracking-widest text-gray-900 hover:text-rose-600 transition-colors hidden sm:flex items-center gap-2">
            View All
            <span class="text-lg">→</span>
          </NuxtLink>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-10">
          <ProductCard
            v-for="(product, idx) in category.products.slice(0, 4)"
            :key="product.id"
            :product="product"
            class="animate-fade-in"
            :style="{ animationDelay: `${idx * 0.1}s` }"
            @add-to-cart="$emit('add-to-cart', $event)"
          />
        </div>
        
        <div class="mt-8 text-center sm:hidden">
          <NuxtLink :to="`/shop?category=${category.slug}`" class="inline-flex items-center justify-center px-8 py-4 bg-gray-900 text-white font-bold text-xs uppercase tracking-widest rounded-full transition-all hover:-translate-y-1 hover:shadow-xl w-full">
            View All {{ category.name }}
          </NuxtLink>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
const props = defineProps({
  categories: {
    type: Array,
    required: true,
    default: () => []
  }
})

defineEmits(['add-to-cart'])
</script>

<style scoped>
.font-heading {
  font-family: var(--font-heading, "Inter", sans-serif);
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
