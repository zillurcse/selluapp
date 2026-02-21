import { defineStore } from 'pinia'

export const useUtilityStore = defineStore('utility', {
    state: () => ({
        isLoading: false,
        isEdit: false,
        formData: {} as any,
        pageBackLink: '',
        isCreateMore: false,
        error: null as string | null,
    }),
    actions: {
        setLoading(status: boolean) {
            this.isLoading = status
        },
    }
})
