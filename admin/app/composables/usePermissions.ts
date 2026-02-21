import { useAuthStore } from '../stores/useAuthStore'

export const usePermissions = () => {
    const authStore = useAuthStore()

    /**
     * Check if user has a specific permission
     * Supports arrays of permissions (returns true if ANY match)
     */
    const can = (permission: string | string[]): boolean => {
        if (!authStore.user) return false

        // Super Admin bypass
        if (authStore.user.roles?.some((r: any) => r.name === 'super-admin')) return true

        const permissions = Array.isArray(permission) ? permission : [permission]
        const userPerms = authStore.userPermissions as string[]

        // Check if user has '*' or any of the required permissions
        return userPerms.includes('*') || permissions.some(p => userPerms.includes(p))
    }

    /**
     * Check if user has a specific role
     */
    const hasRole = (role: string | string[]): boolean => {
        if (!authStore.user) return false

        const roles = Array.isArray(role) ? role : [role]
        return authStore.user.roles?.some((r: any) => roles.includes(r.name)) || false
    }

    /**
     * Check if user is super admin
     */
    const isSuperAdmin = computed(() => {
        return authStore.user?.roles?.some((r: any) => r.name === 'super-admin') || false
    })

    return {
        can,
        hasRole,
        isSuperAdmin,
        userPermissions: computed(() => authStore.userPermissions),
        userRoles: computed(() => authStore.user?.roles || [])
    }
}
