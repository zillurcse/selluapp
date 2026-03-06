<template>
  <section v-if="activeBanners.length > 0" class="py-4 md:py-12 bg-white">
    <div class="container mx-auto px-4 sm:px-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Loop over banners -->
        <div 
          v-for="(banner, index) in activeBanners" 
          :key="banner.id || index"
          class="relative overflow-hidden rounded-[2rem] bg-indigo-50 flex items-center justify-center p-0 group aspect-[4/3] w-full cursor-pointer shadow-sm hover:shadow-xl transition-all"
        >
          <!-- Background Image -->
          <img 
            v-if="banner.image"
            :src="banner.image" 
            alt="Promotional Banner" 
            class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
          />
          
          <!-- Fallback Color if no image (useful during development or if image is missing) -->
          <div v-else class="absolute inset-0 w-full h-full bg-gradient-to-br from-indigo-100 to-rose-50"></div>

          <!-- Overlay (Optional: slightly darken the image if text exists, though banners typically have text baked in. We'll add a link overlay) -->
          <NuxtLink v-if="banner.link" :to="banner.link" class="absolute inset-0 z-20"></NuxtLink>
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

// Only show active banners that have an image
const activeBanners = computed(() => {
  return props.banners.filter(b => b.active && b.image)
})
</script>

<style scoped>
.font-heading {
  font-family: var(--font-heading);
}
</style>
