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

        <!-- ═══ OVERVIEW ═══ -->
        <div v-if="activeTab === 'overview'" class="flex flex-col gap-6">
          <!-- Stats -->
          <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div v-for="stat in stats" :key="stat.label" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
              <div class="text-2xl mb-2">{{ stat.icon }}</div>
              <div class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ stat.value }}</div>
              <div class="text-xs text-gray-500 mt-0.5">{{ stat.label }}</div>
            </div>
          </div>
          <!-- Recent Orders -->
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
              <h2 class="font-bold text-gray-900">Recent Orders</h2>
              <button @click="activeTab = 'orders'" class="text-xs font-bold text-violet-600 hover:text-violet-700 transition border-none bg-transparent cursor-pointer">View all →</button>
            </div>
            <div class="divide-y divide-gray-50">
              <div v-for="order in recentOrders" :key="order.id" class="flex items-center gap-4 px-6 py-4">
                <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-xl shrink-0">{{ order.icon }}</div>
                <div class="flex-1 min-w-0">
                  <div class="font-semibold text-gray-900 text-sm truncate">{{ order.name }}</div>
                  <div class="text-xs text-gray-400 mt-0.5">{{ order.date }} · {{ order.items }} item{{ order.items > 1 ? 's' : '' }}</div>
                </div>
                <div class="text-right shrink-0">
                  <div class="font-bold text-gray-900 text-sm">${{ order.total }}</div>
                  <span class="inline-block mt-1 px-2 py-0.5 text-xs font-bold rounded-full"
                    :class="{
                      'bg-green-50 text-green-700': order.status === 'Delivered',
                      'bg-blue-50 text-blue-700': order.status === 'Shipped',
                      'bg-yellow-50 text-yellow-700': order.status === 'Processing',
                    }">{{ order.status }}</span>
                </div>
              </div>
            </div>
          </div>
          <!-- Wishlist Preview -->
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-5">
              <h2 class="font-bold text-gray-900">Wishlist</h2>
              <button @click="activeTab = 'wishlist'" class="text-xs font-bold text-violet-600 hover:text-violet-700 transition border-none bg-transparent cursor-pointer">View all →</button>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
              <div v-for="item in wishlistItems.slice(0,3)" :key="item.id" class="group relative bg-gray-50 rounded-xl p-3 border border-gray-100 hover:border-gray-200 transition cursor-pointer">
                <div class="text-4xl text-center mb-2">{{ item.emoji }}</div>
                <div class="text-xs font-semibold text-gray-900 truncate">{{ item.name }}</div>
                <div class="text-xs text-gray-500">${{ item.price }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- ═══ ORDERS ═══ -->
        <div v-else-if="activeTab === 'orders'" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
            <h2 class="font-bold text-gray-900">All Orders</h2>
            <div class="inline-flex gap-1 p-1 rounded-xl bg-gray-50 border border-gray-100">
              <button v-for="f in ['All', 'Delivered', 'Shipped', 'Processing']" :key="f"
                @click="orderFilter = f"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition border-none cursor-pointer"
                :class="orderFilter === f ? 'bg-white text-gray-900 shadow-sm' : 'bg-transparent text-gray-500 hover:text-gray-900'">
                {{ f }}
              </button>
            </div>
          </div>
          <div class="divide-y divide-gray-50">
            <div v-for="order in filteredOrders" :key="order.id" class="flex flex-col sm:flex-row sm:items-center gap-4 px-6 py-5 hover:bg-gray-50/50 transition">
              <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center text-2xl shrink-0">{{ order.icon }}</div>
              <div class="flex-1">
                <div class="font-semibold text-gray-900">{{ order.name }}</div>
                <div class="text-sm text-gray-400 mt-0.5">{{ order.date }}</div>
                <div class="text-sm text-gray-400">{{ order.items }} item{{ order.items > 1 ? 's' : '' }}</div>
              </div>
              <div class="flex flex-col sm:items-end gap-2">
                <div class="font-extrabold text-gray-900">${{ order.total }}</div>
                <span class="inline-block px-3 py-1 text-xs font-bold rounded-full"
                  :class="{
                    'bg-green-50 text-green-700': order.status === 'Delivered',
                    'bg-blue-50 text-blue-700': order.status === 'Shipped',
                    'bg-yellow-50 text-yellow-700': order.status === 'Processing',
                  }">{{ order.status }}</span>
                <button class="text-xs font-bold text-violet-600 hover:text-violet-700 transition border-none bg-transparent cursor-pointer">Track Order →</button>
              </div>
            </div>
          </div>
        </div>

        <!-- ═══ WISHLIST ═══ -->
        <div v-else-if="activeTab === 'wishlist'" class="flex flex-col gap-4">
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-bold text-gray-900 mb-5">Wishlist ({{ wishlistItems.length }})</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
              <div v-for="item in wishlistItems" :key="item.id" class="group border border-gray-100 rounded-2xl p-4 hover:border-gray-200 hover:shadow-sm transition-all text-center">
                <div class="text-6xl mb-3 py-2">{{ item.emoji || '📦' }}</div>
                <div class="font-semibold text-gray-900 text-sm mb-1">{{ item.name }}</div>
                <div class="text-sm font-bold text-gray-900 mb-3">${{ item.price }}</div>
                <button class="w-full py-2 bg-gray-900 text-white text-xs font-bold rounded-xl hover:shadow-md transition border-none cursor-pointer">Add to Cart</button>
              </div>
              <div v-if="wishlistItems.length === 0" class="col-span-full py-8 text-center text-gray-500 text-sm">
                Your wishlist is currently empty.
              </div>
            </div>
          </div>
        </div>

        <!-- ═══ PROFILE ═══ -->
        <div v-else-if="activeTab === 'profile'" class="flex flex-col gap-5">
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-bold text-gray-900 mb-6">Personal Information</h2>
            <div class="flex flex-col gap-5">
              <!-- Avatar upload -->
              <div class="flex items-center gap-5">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-violet-500 to-blue-500 flex items-center justify-center text-3xl font-extrabold text-white shadow-lg shrink-0">
                  {{ user.initials }}
                </div>
                <div>
                  <button class="px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition border-solid cursor-pointer">Change Photo</button>
                  <p class="text-xs text-gray-400 mt-1.5">JPG, PNG or GIF. Max 5MB.</p>
                </div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-1.5">First name</label>
                  <input v-model="profileForm.firstName" type="text" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/5" />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-1.5">Last name</label>
                  <input v-model="profileForm.lastName" type="text" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/5" />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email address</label>
                  <input v-model="profileForm.email" type="email" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/5" />
                </div>
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-1.5">Phone number</label>
                  <input v-model="profileForm.phone" type="tel" placeholder="+1 (555) 000-0000" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/5" />
                </div>
              </div>
              <div class="flex justify-end pt-2">
                <button @click="saveProfile" class="px-6 py-2.5 bg-gray-900 text-white text-sm font-bold rounded-xl transition hover:-translate-y-0.5 hover:shadow-md border-none cursor-pointer">
                  Save Changes
                </button>
              </div>
            </div>
          </div>

          <!-- Change Password -->
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-bold text-gray-900 mb-6">Change Password</h2>
            <div class="flex flex-col gap-4 max-w-md">
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Current password</label>
                <input v-model="passwordForm.current_password" type="password" placeholder="Enter current password" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm outline-none transition focus:border-gray-900" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">New password</label>
                <input v-model="passwordForm.password" type="password" placeholder="Min. 8 characters" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm outline-none transition focus:border-gray-900" />
              </div>
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm new password</label>
                <input v-model="passwordForm.password_confirmation" type="password" placeholder="Re-enter new password" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm outline-none transition focus:border-gray-900" />
              </div>
              <button @click="changePassword" class="w-fit px-6 py-2.5 bg-gray-900 text-white text-sm font-bold rounded-xl transition hover:-translate-y-0.5 border-none cursor-pointer">Update Password</button>
            </div>
          </div>
        </div>

        <!-- ═══ ADDRESSES ═══ -->
        <div v-else-if="activeTab === 'addresses'" class="flex flex-col gap-4">
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
              <h2 class="font-bold text-gray-900">Saved Addresses</h2>
              <button @click="isAddingAddress = !isAddingAddress" class="px-4 py-2 bg-gray-900 text-white text-sm font-bold rounded-xl transition hover:-translate-y-0.5 border-none cursor-pointer">
                {{ isAddingAddress ? 'Cancel' : '+ Add Address' }}
              </button>
            </div>

            <!-- Add Address Form -->
            <div v-if="isAddingAddress" class="mb-6 p-5 bg-gray-50 border border-gray-100 rounded-2xl">
              <h3 class="text-sm font-bold text-gray-900 mb-4">New Address</h3>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                  <label class="block text-xs font-semibold text-gray-700 mb-1.5">Full Name</label>
                  <input v-model="addressForm.name" type="text" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm outline-none focus:border-gray-900" />
                </div>
                <div class="sm:col-span-2">
                  <label class="block text-xs font-semibold text-gray-700 mb-1.5">Address Line 1</label>
                  <input v-model="addressForm.line1" type="text" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm outline-none focus:border-gray-900" />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-700 mb-1.5">City</label>
                  <input v-model="addressForm.city" type="text" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm outline-none focus:border-gray-900" />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-700 mb-1.5">State/Province</label>
                  <input v-model="addressForm.state" type="text" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm outline-none focus:border-gray-900" />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-700 mb-1.5">Postal/Zip Code</label>
                  <input v-model="addressForm.zip" type="text" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm outline-none focus:border-gray-900" />
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-700 mb-1.5">Country</label>
                  <input v-model="addressForm.country" type="text" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm outline-none focus:border-gray-900" />
                </div>
                <div class="sm:col-span-2 flex items-center justify-between mt-2">
                  <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input v-model="addressForm.is_default" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-gray-900 cursor-pointer" />
                    <span class="text-sm text-gray-600">Set as default address</span>
                  </label>
                  <button @click="submitAddress" class="px-5 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 transition border-none cursor-pointer">
                    Save Address
                  </button>
                </div>
              </div>
            </div>

            <!-- Address Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div v-for="addr in addresses" :key="addr.id" class="relative border border-gray-100 rounded-2xl p-5 hover:border-gray-200 transition-all"
                :class="addr.is_default ? 'border-gray-900 ring-1 ring-gray-900 bg-gray-50/50' : ''">
                <div v-if="addr.is_default" class="absolute top-3 right-3 px-2 py-0.5 bg-gray-900 text-white text-[10px] font-bold rounded-full">Default</div>
                <div class="text-sm font-bold text-gray-900 mb-1">{{ addr.name }}</div>
                <div class="text-sm text-gray-500 leading-relaxed">{{ addr.line1 }}<br/>{{ addr.city }}, {{ addr.state }} {{ addr.zip }}<br/>{{ addr.country }}</div>
                <div class="flex gap-3 mt-4">
                  <button class="text-xs font-bold text-violet-600 hover:text-violet-700 transition border-none bg-transparent cursor-pointer">Edit</button>
                  <button v-if="!addr.is_default" class="text-xs font-bold text-gray-500 hover:text-gray-700 transition border-none bg-transparent cursor-pointer">Set as default</button>
                  <button v-if="!addr.is_default" class="text-xs font-bold text-red-400 hover:text-red-600 transition border-none bg-transparent cursor-pointer ml-auto">Delete</button>
                </div>
              </div>
              <div v-if="addresses.length === 0 && !isAddingAddress" class="col-span-full py-8 text-center text-gray-500 text-sm border-2 border-dashed border-gray-100 rounded-2xl">
                No addresses saved yet. Minimum one address is needed for shipping.
              </div>
            </div>
          </div>
        </div>

        <!-- ═══ SETTINGS ═══ -->
        <div v-else-if="activeTab === 'settings'" class="flex flex-col gap-5">
          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-bold text-gray-900 mb-6">Notifications</h2>
            <div class="flex flex-col divide-y divide-gray-50">
              <div v-for="notif in notifications" :key="notif.id" class="flex items-center justify-between py-4">
                <div>
                  <div class="text-sm font-semibold text-gray-900">{{ notif.label }}</div>
                  <div class="text-xs text-gray-400 mt-0.5">{{ notif.desc }}</div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer ml-4 shrink-0">
                  <input type="checkbox" v-model="notif.enabled" class="sr-only peer" />
                  <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:bg-gray-900 transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                </label>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-bold text-gray-900 mb-6">Danger Zone</h2>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-4 bg-red-50 border border-red-100 rounded-xl">
              <div>
                <div class="font-semibold text-red-700 text-sm">Delete Account</div>
                <div class="text-xs text-red-500 mt-0.5">This action cannot be undone. All your data will be permanently removed.</div>
              </div>
              <button class="px-4 py-2 bg-red-600 text-white text-sm font-bold rounded-xl hover:bg-red-700 transition shrink-0 border-none cursor-pointer">Delete Account</button>
            </div>
          </div>
        </div>

      </main>
    </div>
  </div>
</template>

<script setup>
const activeTab = ref('overview')
const orderFilter = ref('All')
const { getAll, updateItem, createItem } = useCrud()

const user = ref({
  name: '',
  email: '',
  initials: '',
})

const profileForm = ref({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
})

const fetchCustomerData = async () => {
  try {
    const dashboardData = await getAll('/customer/dashboard')
    if (dashboardData) {
      stats.value = dashboardData.stats || []
      recentOrders.value = dashboardData.recentOrders || []
      wishlistItems.value = dashboardData.wishlistItems || []
    }

    const profileData = await getAll('/customer/profile')
    if (profileData) {
      user.value = profileData.user || {}
      profileForm.value = profileData.profile || {}
    }

    const ordersData = await getAll('/customer/orders')
    if (ordersData) {
      allOrders.value = ordersData.orders || []
    }
    const addressesData = await getAll('/customer/addresses')
    if (addressesData) {
      addresses.value = addressesData.addresses || []
    }
  } catch (error) {
    console.error('Error fetching customer data:', error)
  }
}

onMounted(fetchCustomerData)

const logout = () => navigateTo('/login')

const navItems = computed(() => [
  { id: 'overview',   icon: '🏠', label: 'Dashboard' },
  { id: 'orders',     icon: '📦', label: 'My Orders',   badge: allOrders.value.length > 0 ? allOrders.value.length.toString() : null },
  { id: 'wishlist',   icon: '❤️', label: 'Wishlist',    badge: wishlistItems.value.length > 0 ? wishlistItems.value.length.toString() : null },
  { id: 'addresses',  icon: '📍', label: 'Addresses' },
  { id: 'profile',    icon: '👤', label: 'Profile' },
  { id: 'settings',   icon: '⚙️', label: 'Settings' },
])

const stats = ref([])
const recentOrders = ref([])
const allOrders = ref([])
const wishlistItems = ref([])
const addresses = ref([])
const isAddingAddress = ref(false)

const addressForm = ref({
  name: '',
  line1: '',
  city: '',
  state: '',
  zip: '',
  country: '',
  is_default: false
})

const passwordForm = ref({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const filteredOrders = computed(() => {
  if (orderFilter.value === 'All') return allOrders.value
  return allOrders.value.filter(o => o.status === orderFilter.value)
})

const saveProfile = async () => {
  await updateItem('/customer/profile', profileForm.value)
}

const changePassword = async () => {
  await updateItem('/customer/password', passwordForm.value)
  passwordForm.value = { current_password: '', password: '', password_confirmation: '' }
}

const submitAddress = async () => {
  await createItem('/customer/addresses', addressForm.value)
  isAddingAddress.value = false
  addressForm.value = { name: '', line1: '', city: '', state: '', zip: '', country: '', is_default: false }
  
  // Refetch the addresses to update the list
  const addressesData = await getAll('/customer/addresses')
  if (addressesData) addresses.value = addressesData.addresses || []
}
</script>
