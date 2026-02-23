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

        // Vendor Owner bypass (Only if user has a profile AND is the root owner, i.e., vendor_id is null)
        if (authStore.user.vendor_profile && !authStore.user.vendor_id) return true

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
        const hasExplicitRole = authStore.user.roles?.some((r: any) => roles.includes(r.name)) || false

        // Vendor Profile fallback: if checking for vendor role and user has a profile
        if (!hasExplicitRole && roles.includes('vendor') && authStore.user.vendor_profile) {
            return true
        }

        return hasExplicitRole
    }

    /**
     * Check if user's package has a specific feature (SaaS logic)
     * For vendor owners: checks vendorProfile.package
     * For staff users: checks owner.vendorProfile.package
     */
    const hasFeature = (feature: string): boolean => {
        if (!authStore.user) return false

        // Super admin has all features
        if (authStore.user.roles?.some((r: any) => r.name === 'super-admin')) return true

        // Resolve the package — staff users inherit from their owner
        const pkg = authStore.user.vendor_profile?.package
            || authStore.user.owner?.vendor_profile?.package
        if (!pkg) return false

        if (feature === 'pos') return !!pkg.pos_access
        if (feature === 'hrm') return !!pkg.hrm_access

        if (Array.isArray(pkg.features)) {
            return pkg.features.some((f: string) => f.toLowerCase().includes(feature.toLowerCase()))
        }

        return false
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
        hasFeature,
        userPermissions: computed(() => authStore.userPermissions),
        userRoles: computed(() => authStore.user?.roles || [])
    }
}
