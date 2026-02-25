<template>
  <div class="bg-white relative overflow-hidden">
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

const storefrontStore = useStorefrontStore()
const { slides, topCategories, trendingProducts: products, categoryWiseProducts } = storeToRefs(storefrontStore)

// Fetch initial data from store
await useAsyncData('storefront-init', async () => {
  await storefrontStore.fetchStorefront()
  return true
})

const { addToCart } = useCart()
</script>

<style scoped>
/* Core layout styles only */
</style>
