/**
 * useTracking.ts
 *
 * Full ecommerce tracking composable for:
 *  - GA4 (via GTM dataLayer) — full enhanced ecommerce schema
 *  - Facebook Pixel (browser-side) + Conversion API (server-side via backend)
 *  - TikTok Pixel
 *
 * Features:
 *  - UUID-based event_id for FB Pixel / CAPI deduplication
 *  - user_id forwarded to GA4 & CAPI when logged in
 *  - All events include full items array with ecommerce schema
 */

import { useAuthStore } from '~/stores/useAuthStore'
import { useStorefrontStore } from '~/stores/useStorefrontStore'
import { useRuntimeConfig } from '#app'

// ─── Crypto-safe UUID v4 generator ───────────────────────────────────────────
function generateUUID(): string {
    if (typeof crypto !== 'undefined' && crypto.randomUUID) {
        return crypto.randomUUID()
    }
    // Fallback for older browsers
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
        const r = (Math.random() * 16) | 0
        const v = c === 'x' ? r : (r & 0x3) | 0x8
        return v.toString(16)
    })
}

// ─── GA4 / GTM dataLayer helper ──────────────────────────────────────────────
function pushDataLayer(event: string, params: Record<string, any>) {
    if (typeof window === 'undefined') return
    const dataLayer = (window as any).dataLayer
    if (Array.isArray(dataLayer)) {
        // Clear previous ecommerce object per GA4 best-practice
        dataLayer.push({ ecommerce: null })
        dataLayer.push({ event, ...params })
    }
}

// ─── Facebook Pixel browser helper ───────────────────────────────────────────
function fbTrack(event: string, params: Record<string, any>, eventId: string) {
    if (typeof window === 'undefined') return
    const fbq = (window as any).fbq
    if (typeof fbq === 'function') {
        fbq('track', event, params, { eventID: eventId })
    }
}

// ─── TikTok Pixel helper ──────────────────────────────────────────────────────
function ttTrack(event: string, params: Record<string, any>) {
    if (typeof window === 'undefined') return
    const ttq = (window as any).ttq
    if (ttq && typeof ttq.track === 'function') {
        ttq.track(event, params)
    }
}

export function useTracking() {
    const authStore = useAuthStore()
    const storefrontStore = useStorefrontStore()
    const config = useRuntimeConfig()
    const apiBase = config.public.apiBase

    // ─── CAPI Server-Side Relay ─────────────────────────────────────────────────
    async function sendToCapiServer(payload: {
        event_name: string
        event_id: string
        event_source_url?: string
        custom_data?: Record<string, any>
    }) {
        if (typeof window === 'undefined') return

        const m = storefrontStore.marketing
        if (!m?.fbPixelId) return // No pixel configured — skip silently

        const user = authStore.user
        const userData: Record<string, any> = {
            ip: '',            // Not sent from browser for privacy
            user_agent: navigator.userAgent,
            fbp: getCookie('_fbp'),
            fbc: getCookie('_fbc'),
        }
        if (user?.email) userData.email = user.email
        if (user?.id) userData.user_id = user.id

        try {
            await $fetch(`${apiBase}/storefront/checkout/facebook-capi`, {
                method: 'POST',
                headers: { 'X-Tenant-Domain': window.location.hostname },
                body: {
                    event_name: payload.event_name,
                    event_id: payload.event_id,
                    event_time: Math.floor(Date.now() / 1000),
                    event_source_url: payload.event_source_url || window.location.href,
                    user_data: userData,
                    custom_data: payload.custom_data || {},
                },
            })
        } catch (_) {
            // CAPI is non-critical — fail silently
        }
    }

    function getCookie(name: string): string {
        if (typeof document === 'undefined') return ''
        const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'))
        return match ? match[2]! : ''
    }

    // ─── Shared helpers ──────────────────────────────────────────────────────────
    function buildGa4Item(product: {
        id: string | number
        name: string
        price: number
        category?: string
        brand?: string
        quantity?: number
    }) {
        return {
            item_id: String(product.id),
            item_name: product.name,
            price: product.price,
            item_category: product.category || '',
            item_brand: product.brand || '',
            quantity: product.quantity || 1,
        }
    }

    function getUserId(): string | undefined {
        return authStore.user?.id ? String(authStore.user.id) : undefined
    }

    // ─── 1. ViewContent / view_item ──────────────────────────────────────────────
    function trackViewContent(product: {
        id: string | number
        name: string
        price: number
        category?: string
        brand?: string
        currency?: string
    }) {
        const currency = product.currency || 'BDT'
        const eventId = generateUUID()
        const item = buildGa4Item(product)

        // GA4
        pushDataLayer('view_item', {
            ecommerce: {
                currency,
                value: product.price,
                items: [item],
                ...(getUserId() ? { user_id: getUserId() } : {}),
            },
        })

        // Facebook Pixel (browser)
        fbTrack('ViewContent', {
            content_ids: [String(product.id)],
            content_name: product.name,
            content_type: 'product',
            value: product.price,
            currency,
        }, eventId)

        // TikTok
        ttTrack('ViewContent', {
            content_id: String(product.id),
            content_name: product.name,
            content_type: 'product',
            value: product.price,
            currency,
        })

        // FB CAPI (server-side)
        sendToCapiServer({
            event_name: 'ViewContent',
            event_id: eventId,
            custom_data: { content_ids: [String(product.id)], value: product.price, currency, content_type: 'product' },
        })
    }

    // ─── 2. AddToCart / add_to_cart ──────────────────────────────────────────────
    function trackAddToCart(product: {
        id: string | number
        name: string
        price: number
        quantity?: number
        category?: string
        brand?: string
        currency?: string
    }) {
        const currency = product.currency || 'BDT'
        const qty = product.quantity || 1
        const value = product.price * qty
        const eventId = generateUUID()
        const item = buildGa4Item({ ...product, quantity: qty })

        // GA4
        pushDataLayer('add_to_cart', {
            ecommerce: {
                currency,
                value,
                items: [item],
                ...(getUserId() ? { user_id: getUserId() } : {}),
            },
        })

        // Facebook Pixel
        fbTrack('AddToCart', {
            content_ids: [String(product.id)],
            content_name: product.name,
            content_type: 'product',
            value,
            currency,
            num_items: qty,
        }, eventId)

        // TikTok
        ttTrack('AddToCart', {
            content_id: String(product.id),
            content_name: product.name,
            content_type: 'product',
            value,
            currency,
            quantity: qty,
        })

        // FB CAPI
        sendToCapiServer({
            event_name: 'AddToCart',
            event_id: eventId,
            custom_data: {
                content_ids: [String(product.id)],
                content_name: product.name,
                value,
                currency,
                content_type: 'product',
                contents: [{ id: String(product.id), quantity: qty }],
            },
        })
    }

    // ─── 3. RemoveFromCart / remove_from_cart ────────────────────────────────────
    function trackRemoveFromCart(product: {
        id: string | number
        name: string
        price: number
        quantity?: number
        category?: string
        currency?: string
    }) {
        const currency = product.currency || 'BDT'
        const qty = product.quantity || 1
        const item = buildGa4Item({ ...product, quantity: qty })

        pushDataLayer('remove_from_cart', {
            ecommerce: {
                currency,
                value: product.price * qty,
                items: [item],
            },
        })
        // FB Pixel doesn't have a standard RemoveFromCart event — skip
    }

    // ─── 4. InitiateCheckout / begin_checkout ────────────────────────────────────
    function trackInitiateCheckout(cart: {
        total: number
        itemCount: number
        items?: Array<{ id: string | number; name: string; price: number; quantity?: number }>
        currency?: string
    }) {
        const currency = cart.currency || 'BDT'
        const eventId = generateUUID()
        const items = (cart.items || []).map(i => buildGa4Item(i))
        const contentIds = (cart.items || []).map(i => String(i.id))

        // GA4
        pushDataLayer('begin_checkout', {
            ecommerce: {
                currency,
                value: cart.total,
                items,
                ...(getUserId() ? { user_id: getUserId() } : {}),
            },
        })

        // Facebook Pixel
        fbTrack('InitiateCheckout', {
            value: cart.total,
            currency,
            num_items: cart.itemCount,
            content_ids: contentIds,
        }, eventId)

        // TikTok
        ttTrack('InitiateCheckout', {
            value: cart.total,
            currency,
        })

        // FB CAPI
        sendToCapiServer({
            event_name: 'InitiateCheckout',
            event_id: eventId,
            custom_data: {
                value: cart.total,
                currency,
                num_items: cart.itemCount,
                content_ids: contentIds,
            },
        })
    }

    // ─── 5. Purchase / purchase ───────────────────────────────────────────────────
    function trackPurchase(order: {
        id: string | number
        total: number
        currency?: string
        items?: Array<{ id: string | number; name: string; price: number; quantity?: number; category?: string }>
    }) {
        const currency = order.currency || 'BDT'
        const eventId = generateUUID()
        const items = (order.items || []).map(i => buildGa4Item(i))
        const contentIds = (order.items || []).map(i => String(i.id))
        const contents = (order.items || []).map(i => ({ id: String(i.id), quantity: i.quantity || 1 }))

        // GA4
        pushDataLayer('purchase', {
            ecommerce: {
                transaction_id: String(order.id),
                currency,
                value: order.total,
                items,
                ...(getUserId() ? { user_id: getUserId() } : {}),
            },
        })

        // Facebook Pixel
        fbTrack('Purchase', {
            content_ids: contentIds,
            content_type: 'product',
            value: order.total,
            currency,
            contents,
            order_id: String(order.id),
            num_items: (order.items || []).reduce((s, i) => s + (i.quantity || 1), 0),
        }, eventId)

        // TikTok
        ttTrack('CompletePayment', {
            content_id: contentIds[0] || String(order.id),
            content_type: 'product',
            value: order.total,
            currency,
            order_id: String(order.id),
        })

        // FB CAPI
        sendToCapiServer({
            event_name: 'Purchase',
            event_id: eventId,
            custom_data: {
                currency,
                value: order.total,
                order_id: String(order.id),
                content_ids: contentIds,
                contents,
                content_type: 'product',
            },
        })
    }

    // ─── 6. ViewCart / view_cart ─────────────────────────────────────────────────
    function trackViewCart(cart: {
        total: number
        itemCount: number
        items?: Array<{ id: string | number; name: string; price: number; quantity?: number; category?: string }>
        currency?: string
    }) {
        const currency = cart.currency || 'BDT'
        const eventId = generateUUID()
        const items = (cart.items || []).map(i => buildGa4Item(i))
        const contentIds = (cart.items || []).map(i => String(i.id))

        pushDataLayer('view_cart', {
            ecommerce: {
                currency,
                value: cart.total,
                items,
                ...(getUserId() ? { user_id: getUserId() } : {}),
            },
        })

        fbTrack('ViewCart', {
            value: cart.total,
            currency,
            num_items: cart.itemCount,
            content_ids: contentIds,
        }, eventId)

        ttTrack('ViewCart', {
            value: cart.total,
            currency,
        })

        // FB CAPI
        sendToCapiServer({
            event_name: 'ViewCart',
            event_id: eventId,
            custom_data: {
                value: cart.total,
                currency,
                num_items: cart.itemCount,
                content_ids: contentIds,
            },
        })
    }

    // ─── 7. AddToWishlist / add_to_wishlist ──────────────────────────────────────
    function trackAddToWishlist(product: {
        id: string | number
        name: string
        price: number
        category?: string
        currency?: string
    }) {
        const currency = product.currency || 'BDT'
        const eventId = generateUUID()
        const item = buildGa4Item(product)

        pushDataLayer('add_to_wishlist', {
            ecommerce: {
                currency,
                value: product.price,
                items: [item],
            },
        })

        fbTrack('AddToWishlist', {
            content_ids: [String(product.id)],
            content_name: product.name,
            content_category: product.category || '',
            content_type: 'product',
            value: product.price,
            currency,
        }, eventId)

        ttTrack('AddToWishlist', {
            content_id: String(product.id),
            content_name: product.name,
            content_type: 'product',
            value: product.price,
            currency,
        })
    }

    // ─── 8. AddShippingInfo / add_shipping_info ──────────────────────────────────
    function trackAddShippingInfo(cart: {
        total: number
        itemCount: number
        items?: Array<{ id: string | number; name: string; price: number; quantity?: number; category?: string }>
        currency?: string
    }) {
        const currency = cart.currency || 'BDT'
        const eventId = generateUUID()
        const items = (cart.items || []).map(i => buildGa4Item(i))

        pushDataLayer('add_shipping_info', {
            ecommerce: {
                currency,
                value: cart.total,
                items,
            },
        })

        fbTrack('AddShippingInfo', {
            value: cart.total,
            currency,
        }, eventId)
    }

    // ─── 9. AddPaymentInfo / add_payment_info ────────────────────────────────────
    function trackAddPaymentInfo(cart: {
        total: number
        itemCount: number
        items?: Array<{ id: string | number; name: string; price: number; quantity?: number; category?: string }>
        currency?: string
        paymentMethod?: string
    }) {
        const currency = cart.currency || 'BDT'
        const eventId = generateUUID()
        const items = (cart.items || []).map(i => buildGa4Item(i))
        const contentIds = (cart.items || []).map(i => String(i.id))

        pushDataLayer('add_payment_info', {
            ecommerce: {
                currency,
                value: cart.total,
                payment_type: cart.paymentMethod || '',
                items,
            },
        })

        fbTrack('AddPaymentInfo', {
            value: cart.total,
            currency,
            content_ids: contentIds,
            payment_method: cart.paymentMethod || '',
        }, eventId)

        // FB CAPI
        sendToCapiServer({
            event_name: 'AddPaymentInfo',
            event_id: eventId,
            custom_data: {
                value: cart.total,
                currency,
                content_ids: contentIds,
            },
        })
    }

    // ─── 10. CheckoutReview / checkout_progress ──────────────────────────────────
    function trackCheckoutReview(cart: {
        total: number
        itemCount: number
        items?: Array<{ id: string | number; name: string; price: number; quantity?: number; category?: string }>
        currency?: string
    }) {
        const currency = cart.currency || 'BDT'
        const eventId = generateUUID()
        
        pushDataLayer('checkout_progress', {
            ecommerce: {
                currency,
                value: cart.total,
            },
        })

        fbTrack('CheckoutReview', {
            value: cart.total,
            currency,
        }, eventId)
    }

    // ─── Expose raw helpers for custom events ────────────────────────────────────
    return {
        trackViewContent,
        trackAddToCart,
        trackRemoveFromCart,
        trackInitiateCheckout,
        trackPurchase,
        trackViewCart,
        trackAddToWishlist,
        trackAddShippingInfo,
        trackAddPaymentInfo,
        trackCheckoutReview,
        // Raw helpers for custom events
        pushDataLayer,
        fbTrack,
        ttTrack,
    }
}
