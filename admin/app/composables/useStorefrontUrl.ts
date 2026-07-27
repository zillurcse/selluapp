/**
 * Build storefront URLs for preview / copy-link actions in the admin panel.
 */
export function useStorefrontUrl() {
  const config = useRuntimeConfig()
  const auth = useAuthStore()
  const { getAll } = useCrud()

  const mainAppDomain = config.public.storefrontDomain || 'selluee.test'
  const cachedBaseUrl = ref<string | null>(null)

  const fallbackSubdomain = computed(() => {
    const profile = auth.user?.vendor_profile
    if (profile?.store_slug) return profile.store_slug
    if (profile?.store_name) {
      return profile.store_name.toLowerCase().replace(/[^a-z0-9]/g, '')
    }
    if (auth.user?.name) {
      return auth.user.name.toLowerCase().replace(/[^a-z0-9]/g, '')
    }
    return 'yourstore'
  })

  const resolveStoreBaseUrl = async (): Promise<string> => {
    if (cachedBaseUrl.value) return cachedBaseUrl.value

    const explicitBase = config.public.storefrontBase
    if (explicitBase) {
      cachedBaseUrl.value = explicitBase.replace(/\/$/, '')
      return cachedBaseUrl.value
    }

    try {
      const response = await getAll('/vendor/settings?group=shop_domain')
      const data = response?.data ?? response ?? {}

      if (data.customDomain) {
        const domain = String(data.customDomain).replace(/^https?:\/\//, '').replace(/\/$/, '')
        cachedBaseUrl.value = `https://${domain}`
        return cachedBaseUrl.value
      }

      const sub = data.subDomain || fallbackSubdomain.value
      cachedBaseUrl.value = `https://${sub}.${mainAppDomain}`
      return cachedBaseUrl.value
    } catch {
      cachedBaseUrl.value = `https://${fallbackSubdomain.value}.${mainAppDomain}`
      return cachedBaseUrl.value
    }
  }

  const buildLandingPageUrl = async (slug: string) => {
    const base = await resolveStoreBaseUrl()
    return `${base}/l/${slug}`
  }

  const copyLandingPageLink = async (slug: string) => {
    const url = await buildLandingPageUrl(slug)
    if (typeof navigator !== 'undefined' && navigator.clipboard) {
      await navigator.clipboard.writeText(url)
    }
    return url
  }

  return {
    resolveStoreBaseUrl,
    buildLandingPageUrl,
    copyLandingPageLink,
    fallbackSubdomain,
    mainAppDomain,
  }
}
