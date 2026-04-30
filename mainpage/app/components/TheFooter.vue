<template>
  <footer class="bg-gray-50 mt-20 pt-16 pb-8">
    <div class="container mx-auto px-4 sm:px-6">
      <!-- Top Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-16 mb-12">
        <!-- Brand -->
        <div class="sm:col-span-2 lg:col-span-1">
          <NuxtLink to="/" class="inline-flex items-baseline gap-0.5 mb-5">
            <span class="text-2xl font-extrabold tracking-tighter text-gray-900 font-heading">
              {{ storefrontStore.vendorProfile?.store_name || 'MODERN' }}
            </span>
            <span class="text-2xl font-extrabold text-indigo-600">.</span>
          </NuxtLink>
          <p class="text-sm text-gray-500 leading-relaxed max-w-xs mb-6">
            {{ storefrontStore.generalSettings?.footerAbout || 'Curating the finest essentials for your modern lifestyle. Quality over quantity, always.' }}
          </p>
          <!-- Social Links -->
          <div class="flex items-center gap-3">
            <a
              v-if="storefrontStore.vendorProfile?.instagram"
              :href="storefrontStore.vendorProfile.instagram"
              target="_blank"
              rel="noopener"
              class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-indigo-600 hover:border-indigo-200 transition-all hover:-translate-y-0.5"
              aria-label="Instagram"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
            </a>
            <a
              v-if="storefrontStore.vendorProfile?.facebook"
              :href="storefrontStore.vendorProfile.facebook"
              target="_blank"
              rel="noopener"
              class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-indigo-600 hover:border-indigo-200 transition-all hover:-translate-y-0.5"
              aria-label="Facebook"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
            </a>
            <a
              v-if="storefrontStore.vendorProfile?.twitter"
              :href="storefrontStore.vendorProfile.twitter"
              target="_blank"
              rel="noopener"
              class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-indigo-600 hover:border-indigo-200 transition-all hover:-translate-y-0.5"
              aria-label="Twitter / X"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.259 5.63zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </a>
            <a
              v-if="storefrontStore.vendorProfile?.youtube"
              :href="storefrontStore.vendorProfile.youtube"
              target="_blank"
              rel="noopener"
              class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-rose-500 hover:border-rose-200 transition-all hover:-translate-y-0.5"
              aria-label="YouTube"
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17"/><path d="m10 15 5-3-5-3z"/></svg>
            </a>
          </div>
        </div>

        <!-- Shop Links -->
        <div>
          <h4 class="text-xs font-black uppercase tracking-widest text-gray-900 mb-5">Shop</h4>
          <ul class="space-y-3">
            <li><NuxtLink to="/shop?sort=newest" class="text-sm text-gray-500 hover:text-gray-900 transition-colors font-medium">New Arrivals</NuxtLink></li>
            <li><NuxtLink to="/shop?sort=best_selling" class="text-sm text-gray-500 hover:text-gray-900 transition-colors font-medium">Best Sellers</NuxtLink></li>
            <li><NuxtLink to="/shop/collections" class="text-sm text-gray-500 hover:text-gray-900 transition-colors font-medium">Collections</NuxtLink></li>
            <li><NuxtLink to="/shop?sale=true" class="text-sm text-gray-500 hover:text-gray-900 transition-colors font-medium">Sale</NuxtLink></li>
          </ul>
        </div>

        <!-- Company Links -->
        <div>
          <h4 class="text-xs font-black uppercase tracking-widest text-gray-900 mb-5">Company</h4>
          <ul class="space-y-3">
            <li><NuxtLink to="/about" class="text-sm text-gray-500 hover:text-gray-900 transition-colors font-medium">About Us</NuxtLink></li>
            <li><NuxtLink to="/contact" class="text-sm text-gray-500 hover:text-gray-900 transition-colors font-medium">Contact</NuxtLink></li>
          </ul>
        </div>

        <!-- Support Links -->
        <div>
          <h4 class="text-xs font-black uppercase tracking-widest text-gray-900 mb-5">Support</h4>
          <ul class="space-y-3">
            <li><NuxtLink to="/shipping-policy" class="text-sm text-gray-500 hover:text-gray-900 transition-colors font-medium">Shipping Policy</NuxtLink></li>
            <li><NuxtLink to="/returns-policy" class="text-sm text-gray-500 hover:text-gray-900 transition-colors font-medium">Returns Policy</NuxtLink></li>
            <li><NuxtLink to="/faq" class="text-sm text-gray-500 hover:text-gray-900 transition-colors font-medium">FAQ</NuxtLink></li>
          </ul>
        </div>
      </div>

      <!-- Bottom Bar -->
      <div class="border-t border-gray-200 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-xs text-gray-400 font-medium">
          {{ storefrontStore.generalSettings?.footerCopyright || `© ${new Date().getFullYear()} ${storefrontStore.vendorProfile?.store_name || 'MODERN'}. All rights reserved.` }}
        </p>
        <div class="flex items-center gap-1 text-xs text-gray-400 font-medium">
          <span>Powered by</span>
          <span class="font-bold text-indigo-600">Selluee</span>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
const storefrontStore = useStorefrontStore()
</script>
