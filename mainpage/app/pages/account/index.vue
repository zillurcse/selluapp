<template>
  <div class="min-h-screen bg-gray-50">

    <!-- Page Header -->
    <div class="bg-white border-b border-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 md:py-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div class="flex items-center gap-4">
            <!-- Avatar -->
            <div class="w-14 h-14 md:w-16 md:h-16 rounded-2xl bg-gradient-to-br from-violet-500 to-blue-500 flex items-center justify-center text-2xl font-extrabold text-white shrink-0 shadow-lg">
              {{ user.initials }}
            </div>
            <div>
              <h1 class="text-xl md:text-2xl font-extrabold text-gray-900 tracking-tight">{{ user.name }}</h1>
              <p class="text-sm text-gray-500">{{ user.email }}</p>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 text-green-700 text-xs font-bold rounded-full border border-green-100">
              <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
              Active Member
            </span>
            <span v-if="user.loyalty_points > 0" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-50 text-purple-700 text-xs font-bold rounded-full border border-purple-100 italic">
              ✨ {{ user.loyalty_points }} Points
            </span>
            <NuxtLink to="/" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">
              ← Back to Shop
            </NuxtLink>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 flex flex-col lg:flex-row gap-8">

      <!-- Sidebar -->
      <aside class="lg:w-64 shrink-0">
        <nav class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <div class="p-2">
            <button
              v-for="item in navItems"
              :key="item.id"
              @click="activeTab = item.id"
              class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 text-left border-none cursor-pointer"
              :class="activeTab === item.id
                ? 'bg-gray-900 text-white shadow-sm'
                : 'text-gray-600 bg-transparent hover:bg-gray-50 hover:text-gray-900'"
            >
              <span class="text-lg">{{ item.icon }}</span>
              <span>{{ item.label }}</span>
              <span v-if="item.badge" class="ml-auto text-xs px-2 py-0.5 rounded-full"
                :class="activeTab === item.id ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500'">
                {{ item.badge }}
              </span>
            </button>
          </div>
          <!-- Logout -->
          <div class="p-2 border-t border-gray-100 mt-1">
            <button @click="logout" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-red-500 hover:bg-red-50 transition-all border-none bg-transparent cursor-pointer text-left">
              <span class="text-lg">🚪</span>
              Sign out
            </button>
          </div>
        </nav>
      </aside>

      <!-- Main content -->
      <main class="flex-1 min-w-0">
        <AccountOverview 
          v-if="activeTab === 'overview'" 
          :stats="stats" 
          :recent-orders="recentOrders" 
          :wishlist-items="wishlistItems"
          @nav="(tab) => activeTab = tab"
          @track-order="(order) => { trackingOrder = order; activeTab = 'tracking' }"
          @view-details="(order) => { selectedOrder = order; activeTab = 'details' }"
        />

        <AccountOrders 
          v-else-if="activeTab === 'orders'" 
          :orders="allOrders" 
          @track-order="(order) => { trackingOrder = order; activeTab = 'tracking' }"
          @view-details="(order) => { selectedOrder = order; activeTab = 'details' }"
        />

        <AccountOrderTracking
          v-else-if="activeTab === 'tracking'"
          :order="trackingOrder"
          @back="activeTab = 'orders'"
        />

        <AccountOrderDetails
          v-else-if="activeTab === 'details'"
          :order="selectedOrder"
          @back="activeTab = 'orders'"
          @track="activeTab = 'tracking'; trackingOrder = selectedOrder"
        />

        <!-- <AccountWishlist 
          v-else-if="activeTab === 'wishlist'" 
          :wishlist-items="wishlistItems" 
        /> -->

        <AccountProfile 
          v-else-if="activeTab === 'profile'" 
          :user="user" 
          :profile="profileForm"
          @save-profile="saveProfile"
          @change-password="changePassword"
        />

        <AccountAddresses 
          v-else-if="activeTab === 'addresses'" 
          :addresses="addresses"
          @add-address="submitAddress"
          @update-address="updateAddress"
          @delete-address="deleteAddress"
          @set-default="setDefaultAddress"
        />

        <AccountSettings 
          v-else-if="activeTab === 'settings'" 
          :settings="settings"
          @save-settings="saveSettings"
          @delete-account="handleDeleteAccount"
        />
      </main>
    </div>
  </div>
</template>

<script setup>
import AccountOverview from '~/components/account/AccountOverview.vue'
import AccountOrders from '~/components/account/AccountOrders.vue'
import AccountWishlist from '~/components/account/AccountWishlist.vue'
import AccountProfile from '~/components/account/AccountProfile.vue'
import AccountAddresses from '~/components/account/AccountAddresses.vue'
import AccountSettings from '~/components/account/AccountSettings.vue'
import AccountOrderTracking from '~/components/account/AccountOrderTracking.vue'
import AccountOrderDetails from '~/components/account/AccountOrderDetails.vue'

const activeTab = ref('overview')
const { getAll, updateItem, createItem } = useCrud()

// Data State
const user = ref({ name: '', email: '', initials: '' })
const profileForm = ref({ firstName: '', lastName: '', email: '', phone: '' })
const stats = ref([])
const recentOrders = ref([])
const allOrders = ref([])
const wishlistItems = ref([])
const addresses = ref([])
const settings = ref([])
const trackingOrder = ref(null)
const selectedOrder = ref(null)

// Navigation
const navItems = computed(() => [
  { id: 'overview',   icon: '🏠', label: 'Dashboard' },
  { id: 'orders',     icon: '📦', label: 'My Orders',   badge: allOrders.value.length > 0 ? allOrders.value.length.toString() : null },
  // { id: 'wishlist',   icon: '❤️', label: 'Wishlist',    badge: wishlistItems.value.length > 0 ? wishlistItems.value.length.toString() : null },
  { id: 'addresses',  icon: '📍', label: 'Addresses' },
  { id: 'profile',    icon: '👤', label: 'Profile' },
  { id: 'settings',   icon: '⚙️', label: 'Settings' },
])

const logout = () => navigateTo('/login')

// API Actions
const fetchCustomerData = async () => {
  try {
    const [dashboardData, profileData, ordersData, addressesData] = await Promise.all([
      getAll('/customer/dashboard'),
      getAll('/customer/profile'),
      getAll('/customer/orders'),
      getAll('/customer/addresses')
    ])

    if (dashboardData) {
      stats.value = dashboardData.stats || []
      recentOrders.value = dashboardData.recentOrders || []
      wishlistItems.value = dashboardData.wishlistItems || []
    }
    if (profileData) {
      user.value = profileData.user || {}
      profileForm.value = profileData.profile || {}
    }
    if (ordersData) {
      allOrders.value = ordersData.orders || []
    }
    if (addressesData) {
      addresses.value = addressesData.addresses || []
    }
    if (profileData && profileData.settings) {
      settings.value = profileData.settings || []
    }
  } catch (error) {
    console.error('Error fetching customer data:', error)
  }
}

onMounted(fetchCustomerData)

const saveProfile = async (formData) => {
  await updateItem('/customer/profile', formData)
  // Optionally refresh profile data
}

const changePassword = async (passwordData) => {
  await updateItem('/customer/password', passwordData)
}

const saveSettings = async (settingsData) => {
  await updateItem('/customer/settings', { settings: settingsData })
}

const handleDeleteAccount = async () => {
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

const submitAddress = async (addressData, callback) => {
  await createItem('/customer/addresses', addressData)
  if (callback) callback()
  // Refetch addresses
  const res = await getAll('/customer/addresses')
  if (res) addresses.value = res.addresses || []
}

const updateAddress = async (id, addressData, callback) => {
  await updateItem(`/customer/addresses/${id}`, addressData)
  if (callback) callback()
  // Refetch addresses
  const res = await getAll('/customer/addresses')
  if (res) addresses.value = res.addresses || []
}

const deleteAddress = async (id) => {
  if (confirm('Are you sure you want to delete this address?')) {
    await useFetch(`/customer/addresses/${id}`, { 
      method: 'DELETE',
      baseURL: useRuntimeConfig().public.apiBase,
      headers: {
        Authorization: `Bearer ${useCookie('auth_token').value}`
      }
    })
    // Refetch addresses
    const res = await getAll('/customer/addresses')
    if (res) addresses.value = res.addresses || []
  }
}

const setDefaultAddress = async (id) => {
  await updateItem(`/customer/addresses/${id}`, { is_default: true })
  // Refetch addresses
  const res = await getAll('/customer/addresses')
  if (res) addresses.value = res.addresses || []
}
</script>
