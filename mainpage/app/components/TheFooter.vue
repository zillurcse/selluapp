<template>
  <footer class="bg-[var(--surface)] mt-8 pt-14 pb-8 border-t border-[var(--border)]">
    <div class="container mx-auto">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-12 mb-12">
        <div class="sm:col-span-2 lg:col-span-1">
          <NuxtLink to="/" class="inline-block mb-4">
            <span class="text-xl font-semibold tracking-tight text-gray-900" style="font-family: var(--font-heading)">
              {{ storefrontStore.vendorProfile?.store_name || 'Store' }}
            </span>
          </NuxtLink>
          <p class="text-sm text-[var(--muted-foreground)] leading-relaxed max-w-xs mb-5">
            {{ storefrontStore.generalSettings?.footerAbout || 'Quality products for everyday living. Simple shopping, reliable delivery.' }}
          </p>
          <div class="flex items-center gap-2">
            <a
              v-if="storefrontStore.vendorProfile?.instagram"
              :href="storefrontStore.vendorProfile.instagram"
              target="_blank"
              rel="noopener"
              class="w-9 h-9 rounded-xl bg-white border border-[var(--border)] flex items-center justify-center text-gray-500 hover:text-gray-900 hover:border-gray-300 transition-colors"
              aria-label="Instagram"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
            </a>
            <a
              v-if="storefrontStore.vendorProfile?.facebook"
              :href="storefrontStore.vendorProfile.facebook"
              target="_blank"
              rel="noopener"
              class="w-9 h-9 rounded-xl bg-white border border-[var(--border)] flex items-center justify-center text-gray-500 hover:text-gray-900 hover:border-gray-300 transition-colors"
              aria-label="Facebook"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
            </a>
            <a
              v-if="storefrontStore.vendorProfile?.twitter"
              :href="storefrontStore.vendorProfile.twitter"
              target="_blank"
              rel="noopener"
              class="w-9 h-9 rounded-xl bg-white border border-[var(--border)] flex items-center justify-center text-gray-500 hover:text-gray-900 hover:border-gray-300 transition-colors"
              aria-label="Twitter / X"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.259 5.63zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </a>
            <a
              v-if="storefrontStore.vendorProfile?.youtube"
              :href="storefrontStore.vendorProfile.youtube"
              target="_blank"
              rel="noopener"
              class="w-9 h-9 rounded-xl bg-white border border-[var(--border)] flex items-center justify-center text-gray-500 hover:text-gray-900 hover:border-gray-300 transition-colors"
              aria-label="YouTube"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17"/><path d="m10 15 5-3-5-3z"/></svg>
            </a>
          </div>
        </div>

        <div>
          <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-900 mb-4">Shop</h4>
          <ul class="space-y-2.5">
            <li><NuxtLink to="/shop?sort=newest" class="text-sm text-[var(--muted-foreground)] hover:text-gray-900 transition-colors">New Arrivals</NuxtLink></li>
            <li><NuxtLink to="/shop?sort=best_selling" class="text-sm text-[var(--muted-foreground)] hover:text-gray-900 transition-colors">Best Sellers</NuxtLink></li>
            <li><NuxtLink to="/shop/collections" class="text-sm text-[var(--muted-foreground)] hover:text-gray-900 transition-colors">Collections</NuxtLink></li>
            <li><NuxtLink to="/shop?sale=true" class="text-sm text-[var(--muted-foreground)] hover:text-gray-900 transition-colors">Sale</NuxtLink></li>
          </ul>
        </div>

        <div>
          <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-900 mb-4">Company</h4>
          <ul class="space-y-2.5">
            <li><NuxtLink to="/about" class="text-sm text-[var(--muted-foreground)] hover:text-gray-900 transition-colors">About Us</NuxtLink></li>
            <li><NuxtLink to="/contact" class="text-sm text-[var(--muted-foreground)] hover:text-gray-900 transition-colors">Contact</NuxtLink></li>
          </ul>
        </div>

        <div>
          <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-900 mb-4">Support</h4>
          <ul class="space-y-2.5">
            <li><NuxtLink to="/shipping-policy" class="text-sm text-[var(--muted-foreground)] hover:text-gray-900 transition-colors">Shipping Policy</NuxtLink></li>
            <li><NuxtLink to="/returns-policy" class="text-sm text-[var(--muted-foreground)] hover:text-gray-900 transition-colors">Returns Policy</NuxtLink></li>
            <li><NuxtLink to="/faq" class="text-sm text-[var(--muted-foreground)] hover:text-gray-900 transition-colors">FAQ</NuxtLink></li>
          </ul>
        </div>
      </div>

      <div class="border-t border-[var(--border)] pt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-xs text-[var(--muted-foreground)]">
          {{ storefrontStore.generalSettings?.footerCopyright || `© ${new Date().getFullYear()} ${storefrontStore.vendorProfile?.store_name || 'Store'}. All rights reserved.` }}
        </p>
        <div class="flex items-center gap-1 text-xs text-[var(--muted-foreground)]">
          <span>Powered by</span>
          <span class="font-semibold text-gray-800">Selluee</span>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
const storefrontStore = useStorefrontStore()
</script>
