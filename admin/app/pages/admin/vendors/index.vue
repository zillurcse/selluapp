<template>
  <div class="p-8 bg-[#f8fafc] dark:bg-slate-950 min-h-full">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500 mb-2 font-bold uppercase tracking-widest">
          <Shield class="w-3.5 h-3.5" />
          <NuxtLink to="/admin" class="hover:text-indigo-500 transition-colors">Super Admin</NuxtLink>
          <ChevronRight class="w-3 h-3" />
          <span class="text-slate-600 dark:text-slate-300">Vendors</span>
        </div>
        <h1 class="text-3xl font-[1000] text-slate-900 dark:text-white tracking-tight">Vendor Management</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Create, manage and assign plans to vendors.</p>
      </div>
      <button @click="openCreateModal"
        class="flex items-center gap-2 px-5 py-2.5 bg-slate-900 dark:bg-indigo-600 text-white rounded-xl text-sm font-bold hover:scale-105 transition-all shadow-lg shadow-slate-900/20 dark:shadow-indigo-600/30">
        <Plus class="w-4 h-4" />
        Add Vendor
      </button>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap items-center gap-3 mb-6">
      <div class="relative flex-1 min-w-[200px] max-w-sm">
        <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
        <input v-model="searchQuery" @input="debouncedSearch" type="text" placeholder="Search vendors..."
          class="w-full pl-10 pr-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
      </div>
      <select v-model="statusFilter" @change="fetchVendors"
        class="px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all">
        <option value="">All Vendors</option>
        <option value="active">With Plan</option>
        <option value="inactive">No Plan</option>
      </select>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm">
      <!-- Table Header -->
      <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 hidden md:grid grid-cols-12 gap-4">
        <div class="col-span-4 text-xs font-black text-slate-400 uppercase tracking-widest">Vendor</div>
        <div class="col-span-3 text-xs font-black text-slate-400 uppercase tracking-widest">Store</div>
        <div class="col-span-3 text-xs font-black text-slate-400 uppercase tracking-widest">Plan</div>
        <div class="col-span-2 text-xs font-black text-slate-400 uppercase tracking-widest text-right">Actions</div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="divide-y divide-slate-100 dark:divide-slate-800">
        <div v-for="i in 6" :key="i" class="px-6 py-4">
          <div class="h-10 bg-slate-50 dark:bg-slate-800 rounded-xl animate-pulse"></div>
        </div>
      </div>

      <!-- Vendors List -->
      <div v-else-if="vendors.length" class="divide-y divide-slate-100 dark:divide-slate-800">
        <div v-for="vendor in vendors" :key="vendor.id"
          class="px-6 py-4 md:grid md:grid-cols-12 md:gap-4 flex flex-col gap-3 hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-all">

          <!-- Vendor Info -->
          <div class="col-span-4 flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-violet-500 rounded-xl flex items-center justify-center text-white font-black text-sm shadow-md shadow-indigo-500/20 flex-shrink-0">
              {{ (vendor.name || 'V').charAt(0).toUpperCase() }}
            </div>
            <div class="min-w-0">
              <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ vendor.name }}</p>
              <p class="text-xs text-slate-400 truncate">{{ vendor.email }}</p>
            </div>
          </div>

          <!-- Store Name -->
          <div class="col-span-3 flex items-center">
            <div class="min-w-0">
              <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 truncate">
                {{ vendor.vendor_profile?.store_name || '—' }}
              </p>
              <p class="text-xs text-slate-400">{{ vendor.vendor_profile?.phone || 'No phone' }}</p>
            </div>
          </div>

          <!-- Package -->
          <div class="col-span-3 flex items-center gap-2">
            <div v-if="vendor.vendor_profile?.package">
              <span class="inline-block px-3 py-1.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-xs font-black rounded-full uppercase tracking-wider">
                {{ vendor.vendor_profile.package.name }}
              </span>
              <p class="text-[10px] text-slate-400 mt-1 font-semibold" v-if="vendor.vendor_profile?.package_expiry_date">
                Expires: {{ new Date(vendor.vendor_profile.package_expiry_date).toLocaleDateString() }}
              </p>
            </div>
            <span v-else class="inline-block px-3 py-1.5 bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 text-xs font-black rounded-full uppercase tracking-wider">
              No Plan
            </span>
          </div>

          <!-- Actions -->
          <div class="col-span-2 flex items-center justify-end gap-2">
            <!-- Login as Vendor -->
            <button @click="loginAsVendor(vendor)" :disabled="loggingInAs === vendor.id" title="Login as Vendor"
              class="w-8 h-8 flex items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition-all disabled:opacity-50">
              <Loader2 v-if="loggingInAs === vendor.id" class="w-4 h-4 animate-spin" />
              <LogIn v-else class="w-4 h-4" />
            </button>
            <button @click="openAssignModal(vendor)" title="Assign Plan"
              class="w-8 h-8 flex items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 transition-all">
              <PackagePlus class="w-4 h-4" />
            </button>
            <button @click="openEditModal(vendor)" title="Edit Vendor"
              class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
              <Pencil class="w-4 h-4" />
            </button>
            <button @click="confirmDelete(vendor)" title="Delete Vendor"
              class="w-8 h-8 flex items-center justify-center rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-500 hover:bg-rose-100 dark:hover:bg-rose-500/20 transition-all">
              <Trash2 class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>

      <!-- Empty -->
      <div v-else class="flex flex-col items-center justify-center py-16 text-center">
        <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-4">
          <Users class="w-8 h-8 text-slate-300 dark:text-slate-600" />
        </div>
        <h3 class="text-base font-[1000] text-slate-900 dark:text-white mb-2">No vendors found</h3>
        <p class="text-slate-400 text-sm">{{ searchQuery ? 'Try a different search term.' : 'Create your first vendor account.' }}</p>
      </div>

      <!-- Pagination -->
      <div v-if="pagination && pagination.last_page > 1" class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
        <p class="text-xs text-slate-400 font-semibold">
          Showing {{ pagination.from }}–{{ pagination.to }} of {{ pagination.total }}
        </p>
        <div class="flex gap-2">
          <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1"
            class="px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 disabled:opacity-40 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
            Prev
          </button>
          <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page"
            class="px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 disabled:opacity-40 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Create/Edit Vendor Modal -->
    <Teleport to="body">
      <div v-if="showVendorModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeVendorModal"></div>
        <div class="relative w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
          <div class="sticky top-0 z-10 flex items-center justify-between px-8 py-6 bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800">
            <div>
              <h2 class="text-xl font-[1000] text-slate-900 dark:text-white">{{ editingVendor ? 'Edit Vendor' : 'Create Vendor' }}</h2>
              <p class="text-xs text-slate-400 mt-0.5">{{ editingVendor ? 'Update vendor details' : 'Add a new vendor account' }}</p>
            </div>
            <button @click="closeVendorModal" class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
              <X class="w-4 h-4" />
            </button>
          </div>

          <div class="p-8 space-y-5">
            <div>
              <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Full Name *</label>
              <input v-model="vendorForm.name" type="text" placeholder="Vendor name"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
            </div>
            <div>
              <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Email *</label>
              <input v-model="vendorForm.email" type="email" placeholder="vendor@example.com"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
            </div>
            <div v-if="!editingVendor">
              <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Password *</label>
              <input v-model="vendorForm.password" type="password" placeholder="Min. 8 characters"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
            </div>
            <div v-else>
              <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">New Password <span class="normal-case font-normal text-slate-400">(leave blank to keep current)</span></label>
              <input v-model="vendorForm.password" type="password" placeholder="New password"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
            </div>
            <div>
              <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Store Name</label>
              <input v-model="vendorForm.store_name" type="text" placeholder="My Store"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
            </div>
            <div>
              <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Phone</label>
              <input v-model="vendorForm.phone" type="text" placeholder="+880..."
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
            </div>
            <div>
              <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Package</label>
              <select v-model="vendorForm.package_id"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all">
                <option :value="null">No Package</option>
                <option v-for="pkg in availablePackages" :key="pkg.id" :value="pkg.id">{{ pkg.name }} — ৳{{ pkg.price }}/{{ pkg.duration }}</option>
              </select>
            </div>
            <div v-if="vendorForm.package_id">
              <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Package Expiry Date</label>
              <input v-model="vendorForm.package_expiry_date" type="date"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
            </div>
          </div>

          <div class="sticky bottom-0 px-8 py-5 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
            <button @click="closeVendorModal" class="px-5 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">Cancel</button>
            <button @click="saveVendor" :disabled="saving" class="flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 disabled:opacity-50 transition-all">
              <Loader2 v-if="saving" class="w-4 h-4 animate-spin" />
              <UserPlus v-else class="w-4 h-4" />
              {{ saving ? 'Saving...' : (editingVendor ? 'Update Vendor' : 'Create Vendor') }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Assign Package Modal -->
    <Teleport to="body">
      <div v-if="showAssignModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showAssignModal = false"></div>
        <div class="relative w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl shadow-2xl p-8">
          <h2 class="text-xl font-[1000] text-slate-900 dark:text-white mb-1">Assign Package</h2>
          <p class="text-sm text-slate-400 mb-6">Assigning package to <strong class="text-slate-700 dark:text-slate-200">{{ assignTarget?.name }}</strong></p>

          <div class="space-y-4">
            <div>
              <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Select Package *</label>
              <select v-model="assignForm.package_id"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all">
                <option :value="null" disabled>Choose a package</option>
                <option v-for="pkg in availablePackages" :key="pkg.id" :value="pkg.id">
                  {{ pkg.name }} — ৳{{ pkg.price }}/{{ pkg.duration }}
                </option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">
                Expiry Date <span class="normal-case font-normal text-slate-400">(auto-set for monthly/yearly)</span>
              </label>
              <input v-model="assignForm.package_expiry_date" type="date"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
            </div>
          </div>

          <div class="flex gap-3 mt-6">
            <button @click="showAssignModal = false" class="flex-1 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 rounded-xl hover:bg-slate-200 transition-all">Cancel</button>
            <button @click="assignPackage" :disabled="saving" class="flex-1 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 disabled:opacity-50 transition-all">
              {{ saving ? 'Assigning...' : 'Assign Package' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Delete Confirm -->
    <Teleport to="body">
      <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="deleteTarget = null"></div>
        <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl p-8 max-w-sm w-full">
          <div class="w-14 h-14 bg-rose-50 dark:bg-rose-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <Trash2 class="w-7 h-7 text-rose-500" />
          </div>
          <h3 class="text-lg font-[1000] text-slate-900 dark:text-white text-center mb-2">Delete Vendor?</h3>
          <p class="text-sm text-slate-400 text-center mb-6">
            Delete <strong class="text-slate-700 dark:text-slate-200">{{ deleteTarget?.name }}</strong>? All their data will be permanently removed.
          </p>
          <div class="flex gap-3">
            <button @click="deleteTarget = null" class="flex-1 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 rounded-xl hover:bg-slate-200 transition-all">Cancel</button>
            <button @click="deleteVendor" :disabled="saving" class="flex-1 py-2.5 text-sm font-bold text-white bg-rose-500 rounded-xl hover:bg-rose-600 disabled:opacity-50 transition-all">
              {{ saving ? 'Deleting...' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import {
  Shield, ChevronRight, Plus, Search, Users, PackagePlus, Pencil, Trash2,
  X, Loader2, UserPlus, LogIn
} from 'lucide-vue-next'
import { toast } from 'vue-sonner'

definePageMeta({
  middleware: 'auth',
  layout: 'default',
  permissions: 'admin.vendors.view'
})

const { getAll, getHeaders } = useCrud()
const config = useRuntimeConfig()
const baseURL = config.public.apiBase

const vendors = ref([])
const pagination = ref(null)
const loading = ref(true)
const saving = ref(false)
const loggingInAs = ref(null)
const searchQuery = ref('')
const statusFilter = ref('')
const currentPage = ref(1)

const showVendorModal = ref(false)
const editingVendor = ref(null)
const vendorForm = ref({})

const showAssignModal = ref(false)
const assignTarget = ref(null)
const assignForm = ref({ package_id: null, package_expiry_date: '' })

const deleteTarget = ref(null)
const availablePackages = ref([])

const defaultVendorForm = () => ({
  name: '', email: '', password: '', store_name: '',
  phone: '', package_id: null, package_expiry_date: ''
})

const fetchVendors = async () => {
  loading.value = true
  try {
    const query = { page: currentPage.value }
    if (searchQuery.value) query.search = searchQuery.value
    if (statusFilter.value) query.status = statusFilter.value

    const res = await getAll('/admin/vendors', query)
    vendors.value = res.data || []
    pagination.value = res
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const fetchPackages = async () => {
  try {
    availablePackages.value = await getAll('/admin/packages')
  } catch (e) { console.error(e) }
}

let searchTimer = null
const debouncedSearch = () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => { currentPage.value = 1; fetchVendors() }, 400)
}

const changePage = (page) => {
  currentPage.value = page
  fetchVendors()
}

// Vendor CRUD
const openCreateModal = () => {
  editingVendor.value = null
  vendorForm.value = defaultVendorForm()
  showVendorModal.value = true
}

const openEditModal = (vendor) => {
  editingVendor.value = vendor
  vendorForm.value = {
    name: vendor.name,
    email: vendor.email,
    password: '',
    store_name: vendor.vendor_profile?.store_name || '',
    phone: vendor.vendor_profile?.phone || '',
    package_id: vendor.vendor_profile?.package_id || null,
    package_expiry_date: vendor.vendor_profile?.package_expiry_date || '',
  }
  showVendorModal.value = true
}

const closeVendorModal = () => {
  showVendorModal.value = false
  editingVendor.value = null
  vendorForm.value = defaultVendorForm()
}

const saveVendor = async () => {
  if (!vendorForm.value.name) { toast.error('Name is required'); return }
  if (!vendorForm.value.email) { toast.error('Email is required'); return }
  saving.value = true
  try {
    const body = { ...vendorForm.value }
    if (!body.password) delete body.password

    const url = editingVendor.value
      ? `${baseURL}/admin/vendors/${editingVendor.value.id}`
      : `${baseURL}/admin/vendors`
    const method = editingVendor.value ? 'PUT' : 'POST'

    const res = await $fetch(url, { method, body, headers: getHeaders() })
    toast.success(res.message || 'Vendor saved!')
    closeVendorModal()
    await fetchVendors()
  } catch (e) {
    toast.error(e.data?.message || 'Failed to save vendor')
  } finally {
    saving.value = false
  }
}

// Assign Package
const openAssignModal = (vendor) => {
  assignTarget.value = vendor
  assignForm.value = {
    package_id: vendor.vendor_profile?.package_id || null,
    package_expiry_date: vendor.vendor_profile?.package_expiry_date || ''
  }
  showAssignModal.value = true
}

const assignPackage = async () => {
  if (!assignForm.value.package_id) { toast.error('Please select a package'); return }
  saving.value = true
  try {
    const res = await $fetch(`${baseURL}/admin/vendors/${assignTarget.value.id}/assign-package`, {
      method: 'POST', body: assignForm.value, headers: getHeaders()
    })
    toast.success(res.message || 'Package assigned!')
    showAssignModal.value = false
    await fetchVendors()
  } catch (e) {
    toast.error(e.data?.message || 'Failed to assign package')
  } finally {
    saving.value = false
  }
}

// Login as Vendor (impersonation)
const loginAsVendor = async (vendor) => {
  loggingInAs.value = vendor.id
  try {
    const res = await $fetch(`${baseURL}/admin/vendors/${vendor.id}/login-as`, {
      method: 'POST',
      headers: getHeaders()
    })
    const tokenStore = useTokenStore()
    const authStore = useAuthStore()
    const userCookie = useCookie('auth_user', { maxAge: 60 * 60 * 24 * 2, path: '/' })

    tokenStore.setToken(res.access_token)
    userCookie.value = res.user
    authStore.user = res.user
    authStore.isAuthenticated = true

    toast.success(`Logged in as ${vendor.name}`)
    await navigateTo('/vendor')
  } catch (e) {
    toast.error(e.data?.message || 'Failed to login as vendor')
  } finally {
    loggingInAs.value = null
  }
}

// Delete
const confirmDelete = (vendor) => { deleteTarget.value = vendor }

const deleteVendor = async () => {
  if (!deleteTarget.value) return
  saving.value = true
  try {
    const res = await $fetch(`${baseURL}/admin/vendors/${deleteTarget.value.id}`, {
      method: 'DELETE', headers: getHeaders()
    })
    toast.success(res.message || 'Vendor deleted!')
    deleteTarget.value = null
    await fetchVendors()
  } catch (e) {
    toast.error(e.data?.message || 'Failed to delete vendor')
  } finally {
    saving.value = false
  }
}

onMounted(async () => {
  await Promise.all([fetchVendors(), fetchPackages()])
})
</script>
