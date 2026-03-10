export const useNotificationStore = defineStore('notifications', () => {
    const config = useRuntimeConfig()
    const tokenStore = useTokenStore()

    const notifications = ref<any[]>([])
    const unreadCount = ref(0)
    const isOpen = ref(false)

    let pollTimer: ReturnType<typeof setInterval> | null = null

    function apiHeaders() {
        return {
            Accept: 'application/json',
            Authorization: `Bearer ${tokenStore.getToken}`,
        }
    }

    async function fetch() {
        if (!tokenStore.getToken) return
        try {
            const res = await $fetch<any>(`${config.public.apiBase}/vendor/notifications`, {
                headers: apiHeaders(),
            })
            notifications.value = res.data ?? []
            unreadCount.value = notifications.value.filter((n: any) => !n.is_read).length
        } catch (_) {
            // silent fail — don't break UI if polling fails
        }
    }

    async function fetchUnreadCount() {
        if (!tokenStore.getToken) return
        try {
            const res = await $fetch<any>(`${config.public.apiBase}/vendor/notifications/unread-count`, {
                headers: apiHeaders(),
            })
            unreadCount.value = res.count ?? 0
        } catch (_) { }
    }

    async function markRead(id: number) {
        try {
            await $fetch(`${config.public.apiBase}/vendor/notifications/${id}/read`, {
                method: 'POST',
                headers: apiHeaders(),
            })
            const n = notifications.value.find((x: any) => x.id === id)
            if (n) {
                n.is_read = true
                unreadCount.value = Math.max(0, unreadCount.value - 1)
            }
        } catch (_) { }
    }

    async function markAllRead() {
        try {
            await $fetch(`${config.public.apiBase}/vendor/notifications/read-all`, {
                method: 'POST',
                headers: apiHeaders(),
            })
            notifications.value.forEach((n: any) => (n.is_read = true))
            unreadCount.value = 0
        } catch (_) { }
    }

    function startPolling(intervalMs = 30000) {
        fetch()                                        // immediate first fetch
        pollTimer = setInterval(fetchUnreadCount, intervalMs)
    }

    function stopPolling() {
        if (pollTimer) {
            clearInterval(pollTimer)
            pollTimer = null
        }
    }

    function toggleOpen() {
        isOpen.value = !isOpen.value
        if (isOpen.value) fetch()   // fresh list every time the panel opens
    }

    return {
        notifications,
        unreadCount,
        isOpen,
        fetch,
        markRead,
        markAllRead,
        startPolling,
        stopPolling,
        toggleOpen,
    }
})
