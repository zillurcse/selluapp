<template>
  <div v-if="homeLandingPage" class="landing-page-wrapper">
    <component 
      :is="themeComponent" 
      v-if="themeComponent" 
      :data="homeLandingPage"
    />
    <div v-else class="flex items-center justify-center min-h-screen">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900"></div>
    </div>
  </div>
  <div v-else class="bg-white relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-rose-50/50 rounded-full blur-[120px] -z-10 pointer-events-none"></div>
    <div class="absolute bottom-1/4 left-0 w-[400px] h-[400px] bg-indigo-50/30 rounded-full blur-[100px] -z-10 pointer-events-none"></div>

    <!-- ===== HERO SLIDER ===== -->
    <HeroSlider :slides="slides" />

    <!-- ===== TOP CATEGORIES ===== -->
    <CategoryGrid :categories="topCategories" />

    <!-- ===== CATEGORY WISE PRODUCTS ===== -->
    <CategoryWiseProducts 
      :categories="categoryWiseProducts" 
      @add-to-cart="addToCart"
    />

    <!-- ===== PROMO BANNERS ===== -->
    <PromoBanners />

    <!-- ===== TRENDING PRODUCTS ===== -->
    <TrendingProducts 
      :products="products" 
      @add-to-cart="addToCart"
    />

    <!-- ===== FEATURED LOOKBOOK ===== -->
    <LookbookSection />

    <!-- ===== NEWSLETTER ===== -->
    <NewsletterSection />
  </div>
</template>

<script setup>
import { storeToRefs } from 'pinia'
import { defineAsyncComponent, shallowRef, watchEffect } from 'vue'

const storefrontStore = useStorefrontStore()
const { slides, topCategories, trendingProducts: products, categoryWiseProducts, homeLandingPage } = storeToRefs(storefrontStore)

// Fetch initial data from store
await useAsyncData('storefront-init', async () => {
  await storefrontStore.fetchStorefront()
  return true
})

const { addToCart } = useCart()

// Handle Landing Page Theme Loading
const themeComponent = shallowRef(null)

const themeMap = {
  'single': {
    'modern': () => import('./theme/single/index.vue'),
    'premium_1': () => import('./theme/single/v2.vue'),
    'premium_2': () => import('./theme/single/v3.vue'),
    'white_modern': () => import('./theme/single/index.vue'),
    'index': () => import('./theme/single/index.vue')
  },
  'multiple': {
    'bundle_grid': () => import('./theme/multiple/index.vue'),
    'showcase_slider': () => import('./theme/multiple/index.vue'),
    'vertical_stack': () => import('./theme/multiple/index.vue'),
    'index': () => import('./theme/multiple/index.vue')
  }
}

watchEffect(() => {
  if (homeLandingPage.value) {
    const lp = homeLandingPage.value.landingPage
    const type = lp.landing_page_type
    const template = lp.template_name || 'index'
    
    try {
      const themeLoader = themeMap[type]?.[template] || themeMap[type]?.['index']
      if (themeLoader) {
        themeComponent.value = defineAsyncComponent(themeLoader)
      }
    } catch (e) {
      console.error('Failed to load home landing page theme:', e)
    }
  }
})
</script>

<style scoped>
/* Core layout styles only */
</style>
