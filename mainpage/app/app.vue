<template>
  <div>
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
    <Toaster position="top-right" rich-colors />
    <CartDrawer />
    <WishlistDrawer />
  </div>
</template>

<script setup lang="ts">
import { Toaster } from 'vue-sonner'
import 'vue-sonner/style.css'
import { onMounted } from 'vue'

const { fetchCart } = useCart()
const storefrontStore = useStorefrontStore()

useHead(() => {
  const meta: any[] = []
  const title = storefrontStore.generalSettings?.seoTitle || storefrontStore.vendorProfile?.store_name || 'Sellull'
  const description = storefrontStore.generalSettings?.seoDescription || ''
  const favicon = storefrontStore.generalSettings?.favicon || '/favicon.ico'

  if (description) {
    meta.push({ name: 'description', content: description })
  }
  
  return { 
    title,
    meta,
    link: [
      { rel: 'icon', type: 'image/x-icon', href: favicon }
    ]
  }
})

onMounted(() => {
  fetchCart()
})
</script>
