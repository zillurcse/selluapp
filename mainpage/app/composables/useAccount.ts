// Shared state
const user = ref({ name: '', email: '', initials: '', loyalty_points: 0 })
const profileForm = ref({ firstName: '', lastName: '', email: '', phone: '' })
const allOrders = ref<any[]>([])
const stats = ref<any[]>([])
const addresses = ref<any[]>([])
const recentOrders = ref<any[]>([])
const wishlistItems = ref<any[]>([])
const settings = ref<any[]>([])
const loading = ref(false)
const error = ref<any>(null)

export const useAccount = () => {
  const { getAll, updateItem, createItem } = useCrud()
  const { wishlistCount } = useWishlist()
  
  // Actions
  const fetchCustomerData = async () => {
    loading.value = true
    error.value = null
    try {
      const [dashRes, profileRes, ordersRes, addrRes]: any = await Promise.all([
        getAll('/customer/dashboard'),
        getAll('/customer/profile'),
        getAll('/customer/orders'),
        getAll('/customer/addresses')
      ])

      if (dashRes) {
        stats.value = dashRes.stats || []
        recentOrders.value = dashRes.recentOrders || []
        wishlistItems.value = dashRes.wishlistItems || []
      }

      if (profileRes) {
        user.value = profileRes.user || user.value
        profileForm.value = profileRes.profile || profileForm.value
        settings.value = profileRes.settings || []
      }

      if (ordersRes) {
        allOrders.value = ordersRes.orders || []
      }

      if (addrRes) {
        addresses.value = addrRes.addresses || []
      }
    } catch (err) {
      error.value = err
      console.error('Error fetching account data:', err)
    } finally {
      loading.value = false
    }
  }

  const saveProfile = async () => {
    loading.value = true
    try {
      await updateItem('/customer/profile', profileForm.value)
      await fetchCustomerData()
    } catch (err) {
      error.value = err
    } finally {
      loading.value = false
    }
  }

  const changePassword = async (data: any) => {
    loading.value = true
    try {
      await updateItem('/customer/password', data)
      alert('Password updated successfully!')
    } catch (err) {
      error.value = err
      alert('Failed to update password. Please check your current password.')
    } finally {
      loading.value = false
    }
  }

  const saveSettings = async () => {
    loading.value = true
    try {
      await updateItem('/customer/settings', { settings: settings.value })
      alert('Settings saved!')
    } catch (err) {
      error.value = err
    } finally {
      loading.value = false
    }
  }

  const refreshAddresses = async () => {
    const res: any = await getAll('/customer/addresses')
    if (res) addresses.value = res.addresses || []
  }

  const submitAddress = async (data: any) => {
    loading.value = true
    try {
      await createItem('/customer/addresses', data)
      await fetchCustomerData()
    } catch (err) {
      error.value = err
    } finally {
      loading.value = false
    }
  }

  const updateAddress = async (id: any, data: any) => {
    loading.value = true
    try {
      await updateItem(`/customer/addresses/${id}`, data)
      await fetchCustomerData()
    } catch (err) {
      error.value = err
    } finally {
      loading.value = false
    }
  }

  const deleteAddress = async (id: any) => {
    if (confirm('Delete this address?')) {
      loading.value = true
      try {
        const { deleteItem } = useCrud()
        await deleteItem(id, '/customer/addresses')
        await fetchCustomerData()
      } catch (err) {
        error.value = err
      } finally {
        loading.value = false
      }
    }
  }

  const setDefaultAddress = async (id: any) => {
    loading.value = true
    try {
      await updateItem(`/customer/addresses/${id}`, { is_default: true })
      await fetchCustomerData()
    } catch (err) {
      error.value = err
    } finally {
      loading.value = false
    }
  }

  const deleteAccount = async () => {
    if (confirm('Are you ABSOLUTELY sure? This will permanently delete your account and all data.')) {
      const res = await $fetch(`${useRuntimeConfig().public.apiBase}/customer/account`, {
        method: 'DELETE',
        headers: {
          Authorization: `Bearer ${useCookie('auth_token').value}`
        }
      })
      if (res) {
        navigateTo('/login')
      }
    }
  }

  const fetchOrderById = async (id: any) => {
    loading.value = true
    error.value = null
    try {
      const res: any = await getAll(`/customer/orders/${id}`)
      return res?.order || null
    } catch (err) {
      error.value = err
      console.error('Error fetching order:', err)
      return null
    } finally {
      loading.value = false
    }
  }

  const logout = () => {
    // Clear cookies/state if needed
    navigateTo('/login')
  }

  return {
    user,
    profileForm,
    stats,
    recentOrders,
    allOrders,
    wishlistItems,
    addresses,
    settings,
    loading,
    error,
    wishlistCount,
    fetchCustomerData,
    fetchOrderById,
    saveProfile,
    changePassword,
    saveSettings,
    submitAddress,
    updateAddress,
    deleteAddress,
    setDefaultAddress,
    deleteAccount,
    logout
  }
}
