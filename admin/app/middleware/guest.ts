export default defineNuxtRouteMiddleware(() => {
  const tokenStore = useTokenStore()
  if (tokenStore.getToken) {
    // Let index.vue handle the role-based redirect (super-admin → /admin, vendor → /vendor)
    return navigateTo('/')
  }
})
