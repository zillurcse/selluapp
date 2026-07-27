<template>
  <div class="infinite-category-products">
    <section
      v-for="(category, index) in loadedCategories"
      :key="category.id"
      class="section-pad"
      :class="(index + startOffset) % 2 === 0 ? 'bg-white' : 'bg-[var(--surface)]'"
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
            v-for="product in category.products"
            :key="product.id"
            :product="product"
            @add-to-cart="$emit('add-to-cart', $event)"
          />
        </div>

        <div class="mt-8 text-center sm:hidden">
          <NuxtLink :to="`/shop?category=${category.slug}`" class="btn-primary w-full">
            Explore {{ category.name }}
          </NuxtLink>
        </div>
      </div>
    </section>

    <div ref="sentinel" class="py-12 flex justify-center items-center h-28">
      <div v-if="loading" class="animate-spin rounded-full h-8 w-8 border-2 border-gray-200 border-t-gray-900"></div>
      <div v-else-if="allLoaded" class="text-sm text-[var(--muted-foreground)]">
        You’ve reached the end of the catalog
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  startOffset: {
    type: Number,
    default: 0
  }
})

defineEmits(['add-to-cart'])

const loadedCategories = ref([])
const page = ref(1)
const loading = ref(false)
const allLoaded = ref(false)
const sentinel = ref(null)

let observer = null

const initialPage = 4

const fetchNextCategory = async () => {
  if (loading.value || allLoaded.value) return

  try {
    loading.value = true
    const config = useRuntimeConfig()
    const apiBase = config.public.apiBase
    const domain = useRequestURL().hostname

    const currentPage = (page.value - 1) + initialPage

    const response = await $fetch(`${apiBase}/storefront/infinite-categories`, {
      params: {
        limit: 1,
        page: currentPage
      },
      headers: {
        'X-Tenant-Domain': domain
      }
    })

    if (response?.data?.length > 0) {
      loadedCategories.value.push(...response.data)

      if (currentPage >= response.last_page) {
        allLoaded.value = true
      } else {
        page.value++
      }
    } else {
      allLoaded.value = true
    }
  } catch (error) {
    console.error('Failed to load infinite categories:', error)
  } finally {
    loading.value = false
  }
}

const setupIntersectionObserver = () => {
  observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting && !loading.value && !allLoaded.value) {
      fetchNextCategory()
    }
  }, {
    rootMargin: '400px',
    threshold: 0.1
  })

  if (sentinel.value) {
    observer.observe(sentinel.value)
  }
}

onMounted(() => {
  setupIntersectionObserver()
})

onUnmounted(() => {
  if (observer) {
    observer.disconnect()
  }
})
</script>
