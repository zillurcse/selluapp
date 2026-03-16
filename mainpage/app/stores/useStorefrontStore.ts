import { defineStore } from 'pinia'
import lampImg from '~/assets/img/lamp.png'
import headphonesImg from '~/assets/img/headphones.png'

export const useStorefrontStore = defineStore('storefront', {
    state: () => ({
        storefrontData: null as any,
        pending: false,
        error: null as any,
        topCategories: [] as any[],
        trendingProducts: [] as any[],
        categoryWiseProducts: [] as any[],
        slides: [] as any[],
        vendorProfile: null as any,
        homeLandingPage: null as any,
        promotions: [] as any[],
        websiteBanners: [] as any[],
        marketing: null as any,
        customPages: [] as any[],
        loyaltyProgram: null as any
    }),

    actions: {
        async fetchStorefront(only: 'essential' | 'full' = 'full') {
            const config = useRuntimeConfig()
            const apiBase = config.public.apiBase
            const domain = useRequestURL().hostname.replace(/^www\./, '')

            this.pending = true
            this.error = null

            try {
                const url = only === 'essential' ? `${apiBase}/storefront?only=essential` : `${apiBase}/storefront`
                const data: any = await $fetch(url, {
                    headers: {
                        'X-Tenant-Domain': domain
                    }
                })
                this.storefrontData = data
                this.processStorefrontData(data, only === 'essential')
                return data
            } catch (err: any) {
                this.error = err?.data || err
                console.error('Storefront Fetch Error:', err)
                throw err
            } finally {
                this.pending = false
            }
        },

        async fetchVendor(slug: string) {
            const config = useRuntimeConfig()
            const apiBase = config.public.apiBase
            this.pending = true
            this.error = null
            try {
                const data = await $fetch(`${apiBase}/storefront/vendors/${slug}`)
                return data
            } catch (err: any) {
                this.error = err?.data || err
                console.error('Vendor Fetch Error:', err)
                throw err
            } finally {
                this.pending = false
            }
        },

        processStorefrontData(data: any, isEssential: boolean = false) {
            if (!data) return

            this.marketing = data.marketing || null
            this.customPages = data.custom_pages || []
            this.vendorProfile = data.vendor || null
            this.loyaltyProgram = data.loyalty_program || null

            if (data.landing_page) {
                this.homeLandingPage = {
                    landingPage: data.landing_page,
                    products: data.products,
                    vendor: data.vendor
                }
                return
            } else {
                this.homeLandingPage = null
            }

            this.topCategories = data.categories?.map((cat: any) => ({
                name: cat.name,
                icon: this.getCategoryIcon(cat.name),
                slug: cat.slug,
                children: cat.children?.map((child: any) => ({
                    name: child.name,
                    slug: child.slug
                })) || []
            })) || []

            if (isEssential) {
                if (data.sliders) {
                    this.slides = this.mapSliders(data.sliders)
                }
                return
            }

            this.trendingProducts = data.trending_products?.map((p: any) => ({
                id: p.id,
                name: p.name,
                price: p.sale_price,
                slug: p.slug,
                image: p.image,
                category: p.categories?.[0]?.name || 'Uncategorized'
            })) || []

            this.promotions = data.promotions?.map((promo: any) => ({
                id: promo.id,
                title: promo.title,
                slug: promo.slug,
                type: promo.type,
                discount_value: promo.discount_value,
                discount_unit: promo.discount_unit,
                banner: promo.banner_url || null,
                rules: typeof promo.rules === 'string' ? JSON.parse(promo.rules) : promo.rules,
                priority: promo.priority,
                is_stackable: promo.is_stackable,
                category_slugs: promo.category_slugs || [],
                end_date: promo.end_date
            })) || []

            this.categoryWiseProducts = data.category_wise_products?.map((cat: any) => ({
                id: cat.id,
                name: cat.name,
                slug: cat.slug,
                products: cat.products?.map((p: any) => ({
                    id: p.id,
                    name: p.name,
                    price: p.sale_price,
                    slug: p.slug,
                    image: p.image,
                    category: cat.name
                })) || []
            })) || []

            let mappedBanners: any[] = []
            if (data.website_banners && typeof data.website_banners === 'object') {
                const bannersObj = data.website_banners
                const meta = Array.isArray(bannersObj.banners_meta) ? bannersObj.banners_meta : []
                if (meta.length > 0) {
                    mappedBanners = meta.map((item: any) => ({
                        id: item.id,
                        link: item.link || '/shop',
                        active: item.active !== false,
                        image: bannersObj[item.id] || null
                    }))
                }
            }
            this.websiteBanners = mappedBanners

            if (data.sliders) {
                const mappedSlides = this.mapSliders(data.sliders)
                if (mappedSlides.length > 0) {
                    this.slides = mappedSlides
                } else if (data.featured_products && data.featured_products.length > 0) {
                    this.slides = data.featured_products.map((p: any, idx: number) => ({
                        badge: 'FEATURED SELECTION',
                        title: p.name,
                        description: p.short_description || `Experience the perfect balance of form and function with our ${p.name}.`,
                        buttonText: 'Shop Now',
                        link: `/product/${p.slug}`,
                        image: p.image || p.image_url || headphonesImg,
                        glowColor: idx % 3 === 0 ? 'radial-gradient(circle, rgba(99,102,241,0.6) 0%, transparent 70%)' :
                            idx % 3 === 1 ? 'radial-gradient(circle, rgba(251,191,36,0.5) 0%, transparent 70%)' :
                                'radial-gradient(circle, rgba(16,185,129,0.5) 0%, transparent 70%)',
                        bgGradient: idx % 3 === 0 ? 'linear-gradient(135deg, #020617 0%, #1e1b4b 100%)' :
                            idx % 3 === 1 ? 'linear-gradient(135deg, #0f172a 0%, #1e293b 100%)' :
                                'linear-gradient(135deg, #000000 0%, #171717 100%)',
                    }))
                } else {
                    this.slides = [{
                        badge: 'NEW ARRIVAL 2026',
                        title: 'The Future of Sound',
                        description: 'Immersive audio quality meets iconic design in our latest headphones series.',
                        buttonText: 'Discover More',
                        link: '/shop',
                        image: headphonesImg,
                        glowColor: 'radial-gradient(circle, rgba(99,102,241,0.6) 0%, transparent 70%)',
                        bgGradient: 'linear-gradient(135deg, #020617 0%, #1e1b4b 100%)',
                    }]
                }
            }
        },

        mapSliders(sliders: any) {
            let mappedSlides: any[] = []
            const meta = Array.isArray(sliders.sliders_meta) ? sliders.sliders_meta : []
            if (meta.length > 0) {
                mappedSlides = meta
                    .filter((item: any) => item.active !== false && sliders[item.id])
                    .map((item: any, idx: number) => ({
                        badge: item.badge,
                        title: item.title,
                        description: item.description,
                        buttonText: item.buttonText,
                        link: item.link,
                        image: item.image || sliders[item.id],
                        glowColor: idx % 3 === 0 ? 'radial-gradient(circle, rgba(99,102,241,0.4) 0%, transparent 70%)' :
                            idx % 3 === 1 ? 'radial-gradient(circle, rgba(251,191,36,0.3) 0%, transparent 70%)' :
                                'radial-gradient(circle, rgba(16,185,129,0.3) 0%, transparent 70%)',
                        bgGradient: idx % 3 === 0 ? 'linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%)' :
                            idx % 3 === 1 ? 'linear-gradient(135deg, #fefce8 0%, #fef9c3 100%)' :
                                'linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%)',
                    }))
            } else {
                mappedSlides = Object.entries(sliders)
                    .filter(([key, val]) => key !== 'sliders_meta' && typeof val === 'string' && (val.startsWith('http') || val.includes('/storage/')))
                    .map(([_, val], idx) => ({
                        badge: 'PROMOTION',
                        title: idx === 0 ? 'Minimalist Style' : idx === 1 ? 'Pure Elegance' : 'Modern Living',
                        description: 'Elevate your space with our curated selection of high-end essentials designed for modern life.',
                        buttonText: 'Discover Now',
                        link: '/shop',
                        image: val as string,
                        glowColor: idx % 3 === 0 ? 'radial-gradient(circle, rgba(99,102,241,0.4) 0%, transparent 70%)' :
                            idx % 3 === 1 ? 'radial-gradient(circle, rgba(251,191,36,0.3) 0%, transparent 70%)' :
                                'radial-gradient(circle, rgba(16,185,129,0.3) 0%, transparent 70%)',
                        bgGradient: idx % 3 === 0 ? 'linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%)' :
                            idx % 3 === 1 ? 'linear-gradient(135deg, #fefce8 0%, #fef9c3 100%)' :
                                'linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%)',
                    }))
            }
            return mappedSlides
        },

        setMappedData(categories: any[], products: any[], slides: any[]) {
            this.topCategories = categories
            this.trendingProducts = products
            this.slides = slides
        },

        getCategoryIcon(name: string) {
            const icons: Record<string, string> = {
                'Lighting': '💡',
                'Furniture': '🛋️',
                'Ceramics': '🏺',
                'Accessories': '⌚',
                'Kitchen': '🍵',
                'Decor': '🖼️',
                'Electronics': '📱',
                'Fashion': '👕',
                'Beauty': '💄',
                'Audio': '🎧'
            }
            return icons[name] || '📦'
        }
    }
})
