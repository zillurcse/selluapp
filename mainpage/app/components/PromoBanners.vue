<template>
  <section v-if="activeBanners.length > 0" class="py-8 md:py-12">
    <div class="container mx-auto">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div
          v-for="(banner, index) in activeBanners"
          :key="banner.id || index"
          class="relative overflow-hidden rounded-2xl bg-[var(--muted)] aspect-[16/10] md:aspect-[4/3] w-full group"
        >
          <img
            v-if="banner.image"
            :src="banner.image"
            alt="Promotional Banner"
            class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.03]"
            loading="lazy"
          />
          <div v-else class="absolute inset-0 w-full h-full bg-[var(--muted)]"></div>
          <NuxtLink v-if="banner.link" :to="banner.link" class="absolute inset-0 z-20" :aria-label="'View promotion'"></NuxtLink>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  banners: {
    type: Array,
    default: () => []
  }
})

const activeBanners = computed(() => {
  return props.banners.filter(b => b.active && b.image)
})
</script>
