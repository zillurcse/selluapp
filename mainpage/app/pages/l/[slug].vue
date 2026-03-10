<template>
  <div class="min-h-screen bg-black">
    <div v-if="loading" class="min-h-screen flex items-center justify-center">
      <div class="flex flex-col items-center gap-4">
        <div class="w-12 h-12 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
        <p class="text-white/50 font-black tracking-widest uppercase text-xs">Loading Experience</p>
      </div>
    </div>

    <div v-else-if="error" class="min-h-screen flex flex-col items-center justify-center text-center px-6">
      <div class="text-6xl mb-6">🏜️</div>
      <h1 class="text-4xl font-black text-white mb-4 uppercase tracking-tighter">Page Not Found</h1>
      <p class="text-white/40 mb-10 max-w-md">This landing page might have been moved or is currently offline.</p>
      <NuxtLink to="/" class="px-8 py-4 bg-white text-black font-black uppercase tracking-widest text-xs rounded-full hover:bg-emerald-500 hover:text-white transition-all shadow-xl">Back to Store</NuxtLink>
    </div>

    <component
      v-else-if="landingPage && themeComponent"
      :is="themeComponent"
      :data="landingPageData"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, defineAsyncComponent, shallowRef } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const config = useRuntimeConfig()

const loading = ref(true)
const error = ref(null)
const landingPage = ref(null)
const products = ref([])
const vendor = ref(null)
const themeComponent = shallowRef(null)

const landingPageData = computed(() => ({
  landingPage: landingPage.value,
  products: products.value,
  vendor: vendor.value
}))

// Static theme map for Vite/Nuxt build-time analysis
const themeMap = {
  single: {
    'modern':       () => import('../theme/single/index.vue'),
    'premium_1':    () => import('../theme/single/v2.vue'),
    'premium_2':    () => import('../theme/single/v3.vue'),
    'white_modern': () => import('../theme/single/index.vue'),
    'index':        () => import('../theme/single/index.vue')
  },
  multiple: {
    'bundle_grid':     () => import('../theme/multiple/index.vue'),
    'showcase_slider': () => import('../theme/multiple/index.vue'),
    'vertical_stack':  () => import('../theme/multiple/index.vue'),
    'index':           () => import('../theme/multiple/index.vue')
  },
  common: {
    'store_grid': () => import('../theme/common/index.vue'),
    'store_hero': () => import('../theme/common/index.vue'),
    'index':      () => import('../theme/common/index.vue')
  }
}

const fetchLandingPage = async () => {
  try {
    const slug = route.params.slug
    const res = await $fetch(`${config.public.apiBase}/storefront/landing-page/${slug}`)

    landingPage.value = res.landing_page
    products.value = res.products
    vendor.value = res.vendor

    const type = landingPage.value?.landing_page_type || 'single'
    const template = landingPage.value?.template_name || 'index'

    const themeLoader = themeMap[type]?.[template] || themeMap[type]?.['index']

    if (themeLoader) {
      themeComponent.value = defineAsyncComponent(themeLoader)
    } else {
      console.error('Theme loader not found for:', type, template)
      error.value = 'Theme configuration missing'
    }

  } catch (err) {
    console.error('Failed to fetch landing page:', err)
    error.value = err
  } finally {
    loading.value = false
  }
}

onMounted(() => fetchLandingPage())

useHead(() => ({
  title: landingPage.value
    ? `${landingPage.value.title} | ${vendor.value?.store_name || 'Store'}`
    : 'Landing Page',
  meta: [
    { name: 'description', content: landingPage.value?.settings?.hero_subtitle || landingPage.value?.title || '' },
    { property: 'og:title', content: landingPage.value?.title || 'Landing Page' },
    { property: 'og:description', content: landingPage.value?.settings?.hero_subtitle || '' },
    { property: 'og:type', content: 'website' },
    ...(landingPage.value?.status !== 'active' ? [{ name: 'robots', content: 'noindex, nofollow' }] : [])
  ]
}))
</script>
