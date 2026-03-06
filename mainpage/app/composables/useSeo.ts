/**
 * useSeo.ts
 *
 * Centralized SEO meta tag helper for all pages.
 * Uses Nuxt's useSeoMeta() for SSR-compatible tag injection.
 *
 * Usage:
 *   const { setSeoMeta } = useSeo()
 *   setSeoMeta({ title: 'Product Name', description: '...', image: '...', url: '/shop/slug' })
 */

import { useStorefrontStore } from '~/stores/useStorefrontStore'

interface SeoOptions {
    title?: string
    description?: string
    image?: string
    url?: string
    type?: 'website' | 'product' | 'article'
    noindex?: boolean
}

export function useSeo() {
    const storefrontStore = useStorefrontStore()

    function getStoreName(): string {
        return storefrontStore.vendorProfile?.store_name || 'Online Store'
    }

    function getDefaultImage(): string {
        return storefrontStore.vendorProfile?.logo_url
            || storefrontStore.vendorProfile?.banner_url
            || ''
    }

    function setSeoMeta(options: SeoOptions) {
        const storeName = getStoreName()
        const fullTitle = options.title ? `${options.title} — ${storeName}` : storeName
        const description = options.description
            || `Shop the best products at ${storeName}. Fast delivery, great prices.`
        const image = options.image || getDefaultImage()
        const url = options.url || (typeof window !== 'undefined' ? window.location.href : '')
        const ogType: 'website' | 'article' = options.type === 'article' ? 'article' : 'website'

        useSeoMeta({
            title: fullTitle,
            description,
            ogTitle: fullTitle,
            ogDescription: description,
            ogImage: image,
            ogUrl: url,
            ogType,
            twitterCard: 'summary_large_image',
            twitterTitle: fullTitle,
            twitterDescription: description,
            twitterImage: image,
            ...(options.noindex ? { robots: 'noindex, nofollow' } : { robots: 'index, follow' }),
        })

        // Canonical URL
        useHead({
            link: [
                { rel: 'canonical', href: url },
            ],
        })
    }

    /** Convenience: set product page SEO from a product object */
    function setProductSeo(product: {
        name: string
        short_description?: string
        description?: string
        image_url?: string
        slug: string
    }) {
        const baseUrl = typeof window !== 'undefined'
            ? `${window.location.protocol}//${window.location.host}`
            : ''

        setSeoMeta({
            title: product.name,
            description: product.short_description || product.description || product.name,
            image: product.image_url || getDefaultImage(),
            url: `${baseUrl}/product/${product.slug}`,
            type: 'product',
        })
    }

    /** Convenience: set homepage SEO from vendor profile */
    function setHomeSeo() {
        const profile = storefrontStore.vendorProfile
        setSeoMeta({
            title: profile?.store_name || getStoreName(),
            description: profile?.description
                || `Welcome to ${getStoreName()}. Shop our latest products with fast delivery.`,
            image: getDefaultImage(),
            type: 'website',
        })
    }

    return {
        setSeoMeta,
        setProductSeo,
        setHomeSeo,
    }
}
