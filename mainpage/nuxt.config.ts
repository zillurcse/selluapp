// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  modules: ['@nuxtjs/tailwindcss', '@pinia/nuxt', '@nuxt/fonts'],
  fonts: {
    families: [
      { name: 'Outfit', provider: 'google' },
      { name: 'Plus Jakarta Sans', provider: 'google' },
    ]
  },
  css: ['~/assets/css/main.css'],
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE,
      googleClientId: process.env.NUXT_PUBLIC_GOOGLE_CLIENT_ID || '',
      defaultVendorId: process.env.NUXT_PUBLIC_DEFAULT_VENDOR_ID || '',
    }
  },
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  nitro: {
    compressPublicAssets: true,
    // When running behind Docker, proxy API + storage to the backend service so
    // both SSR (inside the container) and the browser can use relative paths.
    routeRules: process.env.API_PROXY_TARGET
      ? {
          '/api/**': { proxy: `${process.env.API_PROXY_TARGET}/api/**` },
          '/storage/**': { proxy: `${process.env.API_PROXY_TARGET}/storage/**` },
        }
      : {},
  }
})