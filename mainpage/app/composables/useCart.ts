export interface CartItem {
    id: string | number;
    name: string;
    price: string | number;
    image?: string;
    quantity: number;
    [key: string]: any;
}

export const useCart = () => {
    const cart = useState<CartItem[]>('cart', () => [])
    const isCartOpen = useState<boolean>('isCartOpen', () => false)
    const authStore = useAuthStore()
    const tokenStore = useTokenStore()
    const config = useRuntimeConfig()

    const STORAGE_KEY = 'guest_cart'
    const EXPIRATION_TIME = 24 * 60 * 60 * 1000 // 1 day in ms

    const saveCartToStorage = () => {
        if (process.server || authStore.isAuthenticated) return
        localStorage.setItem(STORAGE_KEY, JSON.stringify({
            items: cart.value,
            timestamp: Date.now()
        }))
    }

    const loadCartFromStorage = () => {
        if (process.server || authStore.isAuthenticated) return
        const stored = localStorage.getItem(STORAGE_KEY)
        if (stored) {
            try {
                const { items, timestamp } = JSON.parse(stored)
                if (Date.now() - timestamp < EXPIRATION_TIME) {
                    cart.value = items
                } else {
                    localStorage.removeItem(STORAGE_KEY)
                }
            } catch (e) {
                console.error('Failed to parse stored cart', e)
            }
        }
    }

    // Initialize on client side
    if (process.client) {
        loadCartFromStorage()
    }

    // Watch for changes and save to storage
    watch(cart, () => {
        if (authStore.isAuthenticated) {
            if (process.client) localStorage.removeItem(STORAGE_KEY)
            return
        }
        saveCartToStorage()
    }, { deep: true })

    // Also watch for auth changes to clear guest cart
    watch(() => authStore.isAuthenticated, (isAuth) => {
        if (isAuth && process.client) {
            localStorage.removeItem(STORAGE_KEY)
        }
    })

    const fetchCart = async () => {
        if (!authStore.isAuthenticated) return;
        try {
            const res: any = await $fetch(`${config.public.apiBase}/cart`, {
                headers: {
                    Authorization: `Bearer ${tokenStore.getToken}`
                }
            })
            if (res && res.data) {
                cart.value = res.data
            }
        } catch (error) {
            console.error('Error fetching cart:', error)
        }
    }

    const addToCart = async (product: any, quantity: number = 1, openCart: boolean = true) => {
        const existingItem = cart.value.find(item => item.id === product.id)
        if (existingItem) {
            existingItem.quantity += quantity
        } else {
            cart.value.push({ ...product, quantity })
        }
        if (openCart) {
            isCartOpen.value = true
        }

        const tracking = useTracking()
        tracking.trackAddToCart({
            id: product.id,
            name: product.name || 'Product',
            price: Number(product.price) || 0,
            quantity: quantity
        })

        if (authStore.isAuthenticated) {
            try {
                await $fetch(`${config.public.apiBase}/cart`, {
                    method: 'POST',
                    headers: {
                        Authorization: `Bearer ${tokenStore.getToken}`
                    },
                    body: {
                        product_id: product.id,
                        quantity: quantity
                    }
                })
            } catch (error) {
                console.error('Error adding to backend cart:', error)
            }
        }
    }

    const removeFromCart = async (productId: string | number) => {
        const itemToRemove = cart.value.find(item => item.id === productId)
        if (itemToRemove) {
            const tracking = useTracking()
            tracking.trackRemoveFromCart({
                id: itemToRemove.id,
                name: itemToRemove.name || 'Product',
                price: Number(itemToRemove.price) || 0,
                quantity: itemToRemove.quantity
            })
        }
        cart.value = cart.value.filter(item => item.id !== productId)

        if (authStore.isAuthenticated) {
            try {
                await $fetch(`${config.public.apiBase}/cart/${productId}`, {
                    method: 'DELETE',
                    headers: {
                        Authorization: `Bearer ${tokenStore.getToken}`
                    }
                })
            } catch (error) {
                console.error('Error removing from backend cart:', error)
            }
        }
    }

    const updateQuantity = async (productId: string | number, quantity: number) => {
        const item = cart.value.find(item => item.id === productId)
        if (item) {
            item.quantity = Math.max(1, quantity)
        }

        if (authStore.isAuthenticated) {
            try {
                await $fetch(`${config.public.apiBase}/cart/${productId}`, {
                    method: 'PUT',
                    headers: {
                        Authorization: `Bearer ${tokenStore.getToken}`
                    },
                    body: {
                        quantity: Math.max(1, quantity)
                    }
                })
            } catch (error) {
                console.error('Error updating backend cart quantity:', error)
            }
        }
    }

    const cartTotal = computed(() => {
        return cart.value.reduce((total, item) => total + (Number(item.price) * item.quantity), 0)
    })

    const cartCount = computed(() => {
        return cart.value.reduce((count, item) => count + item.quantity, 0)
    })

    const toggleCart = () => {
        isCartOpen.value = !isCartOpen.value
    }

    const closeCart = () => {
        isCartOpen.value = false
    }

    return {
        cart,
        isCartOpen,
        fetchCart,
        addToCart,
        removeFromCart,
        updateQuantity,
        cartTotal,
        cartCount,
        toggleCart,
        closeCart
    }
}
