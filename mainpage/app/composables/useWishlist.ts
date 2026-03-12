export interface WishlistItem {
    id: string | number;
    name: string;
    price: string | number;
    image_url?: string;
    image?: string;
    [key: string]: any;
}

export const useWishlist = () => {
    // Persist wishlist in a cookie so it survives page reloads
    const wishlistCookie = useCookie<WishlistItem[]>('wishlistItems', {
        default: () => [],
        watch: true
    })
    
    // In-memory state tied to the cookie
    const wishlist = useState<WishlistItem[]>('wishlist', () => wishlistCookie.value || [])
    
    // Drawer state
    const isWishlistOpen = useState<boolean>('isWishlistOpen', () => false)

    // Sync state with cookie when it changes
    watch(wishlist, (newVal) => {
        wishlistCookie.value = newVal
    }, { deep: true })

    const addToWishlist = (product: any) => {
        const itemExists = wishlist.value.find(item => item.id === product.id)
        if (!itemExists) {
            wishlist.value.push(product)
            const tracking = useTracking()
            tracking.trackAddToWishlist({
                id: product.id,
                name: product.name || 'Product',
                price: Number(product.price) || 0
            })
        }
    }

    const removeFromWishlist = (productId: string | number) => {
        wishlist.value = wishlist.value.filter(item => item.id !== productId)
    }

    const toggleWishlist = (product: any, openDrawer: boolean = true) => {
        const exists = isInWishlist(product.id)
        if (exists) {
            removeFromWishlist(product.id)
        } else {
            addToWishlist(product)
            if (openDrawer) isWishlistOpen.value = true
        }
        return !exists // Return true if it was added, false if removed
    }

    const isInWishlist = (productId: string | number) => {
        return wishlist.value.some(item => item.id === productId)
    }

    const wishlistCount = computed(() => {
        return wishlist.value.length
    })

    const toggleWishlistDrawer = () => {
        isWishlistOpen.value = !isWishlistOpen.value
    }

    const closeWishlist = () => {
        isWishlistOpen.value = false
    }

    return {
        wishlist,
        isWishlistOpen,
        addToWishlist,
        removeFromWishlist,
        toggleWishlist,
        isInWishlist,
        wishlistCount,
        toggleWishlistDrawer,
        closeWishlist
    }
}
