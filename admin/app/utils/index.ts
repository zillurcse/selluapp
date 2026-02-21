/**
 * Utility functions for the application
 */

/**
 * Format a date to a readable string
 */
export const formatDate = (date: Date | string): string => {
    const d = typeof date === 'string' ? new Date(date) : date
    return d.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

/**
 * Truncate text to a specified length
 */
export const truncateText = (text: string, maxLength: number): string => {
    if (text.length <= maxLength) return text
    return text.slice(0, maxLength) + '...'
}


/**
 * Capitalize the first letter of a string
 */
export const capitalize = (str: string): string => {
    return str.charAt(0).toUpperCase() + str.slice(1)
}

/**
 * Handle form errors from API
 */
export const formError = (errors: any, form: any) => {
    if (form && form.setErrors && errors) {
        const formattedErrors = Object.entries(errors).map(([key, value]: [string, any]) => ({
            path: key,
            message: value[0]
        }));
        form.setErrors(formattedErrors);
    }
}

/**
 * System voice/toast notification helper
 */
export const speak = (message: string) => {
    console.log('System notification:', message);
    // You could implement speech synthesis or a toast here
}

/**
 * Ensure CSRF token exists by fetching it from Sanctum if needed
 */
const ensureCsrfToken = async () => {
    const xsrfToken = useCookie('XSRF-TOKEN').value
    if (!xsrfToken) {
        try {
            const config = useRuntimeConfig()
            const baseUrl = config.public.apiBase.replace('/api', '')
            await $fetch('/sanctum/csrf-cookie', {
                baseURL: baseUrl,
                credentials: 'include'
            })
        } catch (error) {
            console.error('Failed to fetch CSRF token:', error)
        }
    }
}

/**
 * Global API Fetch Wrapper
 */
export const $apiFetch = async <T>(request: any, opts?: any) => {
    const config = useRuntimeConfig()
    const tokenStore = useTokenStore()

    // Ensure CSRF cookie exists for state-changing methods
    const method = opts?.method?.toUpperCase()
    if (['POST', 'PUT', 'DELETE', 'PATCH'].includes(method)) {
        await ensureCsrfToken()
    }

    // Get and decode the XSRF token (Laravel Sanctum stores it URL-encoded)
    const xsrfTokenRaw = useCookie('XSRF-TOKEN').value
    const xsrfToken = xsrfTokenRaw ? decodeURIComponent(xsrfTokenRaw) : null

    return await $fetch<T>(request, {
        baseURL: config.public.apiBase,
        credentials: 'include',
        ...opts,
        headers: {
            Accept: 'application/json',
            ...(xsrfToken ? { 'X-XSRF-TOKEN': xsrfToken } : {}),
            ...opts?.headers,
        },
        onRequest({ options }) {
            options.headers = options.headers || {}

            // Add Authorization header if token exists
            if (tokenStore.token) {
                if (options.headers instanceof Headers) {
                    options.headers.set('Authorization', `Bearer ${tokenStore.token}`)
                } else {
                    (options.headers as any).Authorization = `Bearer ${tokenStore.token}`
                }
            }

            // Decode and validate CSRF token again just in case it wasn't added in the initial object
            const currentXsrfTokenRaw = useCookie('XSRF-TOKEN').value
            const currentXsrfToken = currentXsrfTokenRaw ? decodeURIComponent(currentXsrfTokenRaw) : null
            if (currentXsrfToken) {
                if (options.headers instanceof Headers) {
                    options.headers.set('X-XSRF-TOKEN', currentXsrfToken)
                } else {
                    (options.headers as any)['X-XSRF-TOKEN'] = currentXsrfToken
                }
            }
        }
    })
}
