export default defineNuxtRouteMiddleware(async (to) => {
  const tokenStore = useTokenStore()
  const authStore = useAuthStore()

  // ── 1. No token → go to login ──────────────────────────────────────────────
  if (!tokenStore.getToken) {
    return navigateTo('/login')
  }

  // ── 2. Token exists but user not yet hydrated (e.g. after hard reload) ─────
  if (!authStore.user) {
    try {
      await authStore.fetchUser()
    } catch (e) {
      console.error('auth middleware: fetchUser failed', e)
    }
  }

  // ── 3. Still no user after fetch → token is invalid, go to login ───────────
  if (!authStore.user) {
    return navigateTo('/login')
  }

  // ── 4. Role & Permission Checks ──────────────────────────────────────────
  const { can, hasRole } = usePermissions()

  // Check Roles
  if (to.meta.roles) {
    const requiredRoles = Array.isArray(to.meta.roles) ? to.meta.roles : [to.meta.roles]
    if (!requiredRoles.some(role => hasRole(role))) {
      return navigateTo('/vendor') // or unauthorized page
    }
  }

  // Check Permissions
  if (to.meta.permissions) {
    const requiredPerms = Array.isArray(to.meta.permissions) ? to.meta.permissions : [to.meta.permissions]
    if (!requiredPerms.some(perm => can(perm))) {
      return navigateTo('/vendor') // or unauthorized page
    }
  }
})

