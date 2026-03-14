// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  modules: ['@nuxtjs/tailwindcss', '@pinia/nuxt', '@nuxt/fonts'],
  css: ['~/assets/css/main.css'],
  runtimeConfig: {
    public: {
      apiBase: process.env.NUXT_PUBLIC_API_BASE,
      googleClientId: process.env.NUXT_PUBLIC_GOOGLE_CLIENT_ID || '149175239167-27ktoiknnrjsv9vts1h6k4139r4o8449.apps.googleusercontent.com'
    }
  },
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },
  nitro: {
    compressPublicAssets: true,
  }
})