<template>
  <div class="infinite-category-products">
    <section 
      v-for="(category, index) in loadedCategories" 
      :key="category.id" 
      class="pt-12 md:pt-20" 
      :class="(index + startOffset) % 2 === 0 ? 'bg-white' : 'bg-gray-50/50'"
    >
      <div class="container mx-auto px-4 sm:px-6">
        <div class="flex items-center justify-between mb-10">
          <div>
            <h2 class="text-3xl md:text-5xl font-extrabold tracking-tight text-gray-900 font-heading">
              {{ category.name }}
            </h2>
            <div class="w-20 h-1 bg-rose-600 mt-4 rounded-full"></div>
          </div>
          <NuxtLink 
            :to="`/shop?category=${category.slug}`" 
            class="text-sm font-bold uppercase tracking-widest text-gray-900 hover:text-rose-600 transition-colors hidden sm:flex items-center gap-2"
          >
            View All
            <span class="text-lg">→</span>
          </NuxtLink>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 md:gap-10">
          <ProductCard
            v-for="(product, idx) in category.products"
            :key="product.id"
            :product="product"
            class="animate-fade-in"
            :style="{ animationDelay: `${(idx % 4) * 0.1}s` }"
            @add-to-cart="$emit('add-to-cart', $event)"
          />
        </div>
        
        <div class="mt-8 text-center sm:hidden">
          <NuxtLink 
            :to="`/shop?category=${category.slug}`" 
            class="inline-flex items-center justify-center px-8 py-4 bg-gray-900 text-white font-bold text-xs uppercase tracking-widest rounded-full transition-all hover:-translate-y-1 hover:shadow-xl w-full"
          >
            View All {{ category.name }}
          </NuxtLink>
        </div>
      </div>
    </section>

    <!-- Loading Sentinel -->
    <div ref="sentinel" class="py-12 flex justify-center items-center h-32">
      <div v-if="loading" class="animate-spin rounded-full h-10 w-10 border-b-2 border-rose-600"></div>
      <div v-else-if="allLoaded" class="text-gray-500 font-medium tracking-wide">
        You have reached the end of the catalog
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
const page = ref(1) // We'll query DB with limit=1 and page=N (though offset depends on initial load)
const loading = ref(false)
const allLoaded = ref(false)
const sentinel = ref(null)

let observer = null

// Note: store might already load the first 3 categories. If we rely on absolute DB pages, 
// we might fetch overlapping categories. To fix, our API could take offset instead of page, Or we just pass an initial offset to the endpoint.
// Let's use the DB pagination. Assuming original loads 3 categories, we start fetching from page=4 if limit=1, or we pass `page=1` but offset from previous results?
// Actually API is `paginate($perPage)`. If we use `page=X`, 1st page gives 1 category.
// Since the initial load fetched 3 categories, the next one is index 3 (4th category). So we'll start fetching page=4.

const initialPage = 4 // Start loading from the 4th category overall (since index.vue might show 3 initially in `CategoryWiseProducts`).

const fetchNextCategory = async () => {
  if (loading.value || allLoaded.value) return

  try {
    loading.value = true
    const config = useRuntimeConfig()
    const apiBase = config.public.apiBase
    const domain = useRequestURL().hostname

    // Fetch next category (1 per page)
    const currentPage = (page.value - 1) + initialPage
    
    // Using $fetch
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
    rootMargin: '400px', // Start loading slightly before reaching the element
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
