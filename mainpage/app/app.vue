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
  const fbDomainVerification = storefrontStore.marketing?.fbDomainVerification
  
  if (fbDomainVerification) {
    meta.push({ name: 'facebook-domain-verification', content: fbDomainVerification })
  }
  
  return { meta }
})

onMounted(() => {
  fetchCart()
})
</script>
