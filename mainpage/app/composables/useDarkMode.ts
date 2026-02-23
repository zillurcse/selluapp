/**
 * Composable for managing dark mode
 */
export const useDarkMode = () => {
    const isDark = useState('darkMode', () => false)

    // Initialize dark mode from localStorage
    const initDarkMode = () => {
        if (import.meta.client) {
            const stored = localStorage.getItem('darkMode')
            if (stored !== null) {
                isDark.value = stored === 'true'
            } else {
                // Check system preference
                isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
            }
            updateDocumentClass()
        }
    }

    // Update document class
    const updateDocumentClass = () => {
        if (import.meta.client) {
            if (isDark.value) {
                document.documentElement.classList.add('dark')
            } else {
                document.documentElement.classList.remove('dark')
            }
        }
    }

    // Toggle dark mode
    const toggleDarkMode = () => {
        isDark.value = !isDark.value
        if (import.meta.client) {
            localStorage.setItem('darkMode', String(isDark.value))
            updateDocumentClass()
        }
    }

    // Set dark mode
    const setDarkMode = (value: boolean) => {
        isDark.value = value
        if (import.meta.client) {
            localStorage.setItem('darkMode', String(value))
            updateDocumentClass()
        }
    }

    return {
        isDark: readonly(isDark),
        toggleDarkMode,
        setDarkMode,
        initDarkMode
    }
}
