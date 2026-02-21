export default defineNuxtPlugin((nuxtApp) => {
    const { can, hasRole } = usePermissions()

    // v-can="permission" or v-can="['perm1', 'perm2']"
    nuxtApp.vueApp.directive('can', {
        mounted(el, binding) {
            if (!can(binding.value)) {
                el.style.display = 'none'
                // Alternative: el.remove() if you want to completely remove it
            }
        },
        updated(el, binding) {
            if (!can(binding.value)) {
                el.style.display = 'none'
            } else {
                el.style.display = ''
            }
        }
    })

    // v-role="role"
    nuxtApp.vueApp.directive('role', {
        mounted(el, binding) {
            if (!hasRole(binding.value)) {
                el.style.display = 'none'
            }
        },
        updated(el, binding) {
            if (!hasRole(binding.value)) {
                el.style.display = 'none'
            } else {
                el.style.display = ''
            }
        }
    })
})
