/**
 * useGoogleAuth.ts
 *
 * Google Identity Services (GSI) composable for Google OAuth login.
 *
 * Usage in login/register:
 *   const { initGoogleLogin, renderGoogleButton } = useGoogleAuth()
 *   onMounted(() => renderGoogleButton('#google-btn-container'))
 */

import { useAuthStore } from '~/stores/useAuthStore'
import { useStorefrontStore } from '~/stores/useStorefrontStore'
import { useRuntimeConfig } from '#app'
import { ref } from 'vue'

const googleScriptLoaded = ref(false)

function loadGoogleScript(): Promise<void> {
    return new Promise((resolve) => {
        if (googleScriptLoaded.value || (window as any).google?.accounts) {
            googleScriptLoaded.value = true
            resolve()
            return
        }
        const script = document.createElement('script')
        script.src = 'https://accounts.google.com/gsi/client'
        script.async = true
        script.defer = true
        script.onload = () => {
            googleScriptLoaded.value = true
            resolve()
        }
        document.head.appendChild(script)
    })
}

export function useGoogleAuth() {
    const authStore = useAuthStore()
    const storefrontStore = useStorefrontStore()
    const config = useRuntimeConfig()
    const apiBase = config.public.apiBase

    const loading = ref(false)
    const error = ref<string | null>(null)

    async function handleCredentialResponse(credential: string) {
        loading.value = true
        error.value = null
        try {
            const domain = typeof window !== 'undefined' ? window.location.hostname : ''
            const response: any = await $fetch(`${apiBase}/auth/google`, {
                method: 'POST',
                headers: { 'X-Tenant-Domain': domain },
                body: { id_token: credential },
            })

            if (response.access_token) {
                // Use the same method as useAuthStore.login()
                const tokenStore = useTokenStore()
                tokenStore.setToken(response.access_token)
                authStore.user.value = response.user
                return { success: true }
            }
            error.value = 'Google login failed'
            return { success: false }
        } catch (e: any) {
            error.value = e?.data?.message || 'Google login failed. Please try again.'
            return { success: false }
        } finally {
            loading.value = false
        }
    }

    /**
     * Initialize Google GSI with One-Tap + standard button rendering.
     * @param onSuccess callback fired after successful login
     */
    async function initGoogleLogin(onSuccess?: () => void) {
        if (typeof window === 'undefined') return

        await loadGoogleScript()

        // Ensure storefront is loaded so we can get the Google Client ID
        if (!storefrontStore.marketing) {
            try { await storefrontStore.fetchStorefront() } catch (_) { }
        }

        const clientId = storefrontStore.marketing?.googleClientId
        if (!clientId) {
            console.warn('[useGoogleAuth] No googleClientId configured in vendor marketing settings')
            return
        }

        const google = (window as any).google
        google.accounts.id.initialize({
            client_id: clientId,
            callback: async (response: { credential: string }) => {
                const result = await handleCredentialResponse(response.credential)
                if (result.success && onSuccess) {
                    onSuccess()
                }
            },
            auto_select: false,
            cancel_on_tap_outside: true,
        })
    }

    /**
     * Render Google's official sign-in button inside the given CSS selector or element.
     */
    async function renderGoogleButton(
        selectorOrEl: string | HTMLElement,
        options: Record<string, any> = {}
    ) {
        if (typeof window === 'undefined') return
        await initGoogleLogin()

        const el = typeof selectorOrEl === 'string'
            ? document.querySelector(selectorOrEl)
            : selectorOrEl

        if (!el) return

        const google = (window as any).google
        if (!google?.accounts?.id) return

        google.accounts.id.renderButton(el, {
            type: 'standard',
            theme: 'outline',
            size: 'large',
            shape: 'rectangular',
            logo_alignment: 'left',
            width: el.clientWidth || 320,
            text: 'continue_with',
            ...options,
        })
    }

    /**
     * Prompt Google One-Tap dialog.
     */
    async function promptOneTap(onSuccess?: () => void) {
        if (typeof window === 'undefined') return
        await initGoogleLogin(onSuccess)
        const google = (window as any).google
        google?.accounts?.id?.prompt()
    }

    return {
        loading,
        error,
        initGoogleLogin,
        renderGoogleButton,
        promptOneTap,
    }
}
