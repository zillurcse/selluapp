<template>
  <div class="p-8 bg-[#f8fafc] dark:bg-slate-950 min-h-full">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500 mb-2 font-bold uppercase tracking-widest">
          <Shield class="w-3.5 h-3.5" />
          <NuxtLink to="/admin" class="hover:text-indigo-500 transition-colors">Super Admin</NuxtLink>
          <ChevronRight class="w-3 h-3" />
          <span class="text-slate-600 dark:text-slate-300">Users</span>
        </div>
        <h1 class="text-3xl font-[1000] text-slate-900 dark:text-white tracking-tight">User Management</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Manage all platform users, roles, and access levels.</p>
      </div>
      <button @click="openCreateModal"
        class="flex items-center gap-2 px-5 py-2.5 bg-slate-900 dark:bg-indigo-600 text-white rounded-xl text-sm font-bold hover:scale-105 transition-all shadow-lg shadow-slate-900/20 dark:shadow-indigo-600/30">
        <UserPlus class="w-4 h-4" />
        New User
      </button>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
      <div v-for="stat in roleStats" :key="stat.role"
        class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-5 flex items-center gap-4 hover:shadow-md transition-all">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" :class="stat.bg">
          <component :is="stat.icon" class="w-5 h-5" :class="stat.color" />
        </div>
        <div>
          <p class="text-2xl font-[1000] text-slate-900 dark:text-white">{{ stat.count }}</p>
          <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ stat.role }}</p>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap items-center gap-3 mb-6">
      <div class="relative flex-1 min-w-[200px] max-w-sm">
        <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
        <input v-model="searchQuery" @input="debouncedSearch" type="text" placeholder="Search by name or email…"
          class="w-full pl-10 pr-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
      </div>
      <select v-model="roleFilter" @change="fetchUsers"
        class="px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all">
        <option value="">All Roles</option>
        <option value="super-admin">Super Admin</option>
        <option value="admin">Admin</option>
        <option value="vendor">Vendor</option>
        <option value="customer">Customer</option>
      </select>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm">
      <!-- Table Header -->
      <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 hidden md:grid grid-cols-12 gap-4">
        <div class="col-span-4 text-xs font-black text-slate-400 uppercase tracking-widest">User</div>
        <div class="col-span-3 text-xs font-black text-slate-400 uppercase tracking-widest">Email</div>
        <div class="col-span-2 text-xs font-black text-slate-400 uppercase tracking-widest">Role</div>
        <div class="col-span-2 text-xs font-black text-slate-400 uppercase tracking-widest">Joined</div>
        <div class="col-span-1 text-xs font-black text-slate-400 uppercase tracking-widest text-right">Actions</div>
      </div>

      <!-- Loading Skeleton -->
      <div v-if="loading" class="divide-y divide-slate-100 dark:divide-slate-800">
        <div v-for="i in 8" :key="i" class="px-6 py-4 flex items-center gap-4">
          <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 animate-pulse flex-shrink-0"></div>
          <div class="flex-1 space-y-2">
            <div class="h-3 w-32 bg-slate-100 dark:bg-slate-800 rounded animate-pulse"></div>
            <div class="h-3 w-48 bg-slate-100 dark:bg-slate-800 rounded animate-pulse"></div>
          </div>
          <div class="h-6 w-20 bg-slate-100 dark:bg-slate-800 rounded-full animate-pulse"></div>
        </div>
      </div>

      <!-- Users List -->
      <div v-else-if="users.length" class="divide-y divide-slate-100 dark:divide-slate-800">
        <div v-for="user in users" :key="user.id"
          class="px-6 py-4 md:grid md:grid-cols-12 md:gap-4 flex flex-col gap-3 hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-all group">

          <!-- Avatar + Name -->
          <div class="col-span-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-black text-sm shadow-md flex-shrink-0"
              :class="avatarClass(user.roles?.[0]?.name)">
              {{ (user.name || 'U').charAt(0).toUpperCase() }}
            </div>
            <div class="min-w-0">
              <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ user.name }}</p>
              <p class="text-xs text-slate-400 md:hidden truncate">{{ user.email }}</p>
            </div>
          </div>

          <!-- Email -->
          <div class="col-span-3 hidden md:flex items-center">
            <p class="text-sm text-slate-500 dark:text-slate-400 truncate">{{ user.email }}</p>
          </div>

          <!-- Role Badge -->
          <div class="col-span-2 flex items-center">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider"
              :class="roleBadgeClass(user.roles?.[0]?.name)">
              <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
              {{ user.roles?.[0]?.name || 'No Role' }}
            </span>
          </div>

          <!-- Date -->
          <div class="col-span-2 hidden md:flex items-center">
            <p class="text-xs text-slate-400 font-semibold">{{ formatDate(user.created_at) }}</p>
          </div>

          <!-- Actions -->
          <div class="col-span-1 flex items-center justify-end gap-2">
            <button @click="openEditModal(user)" title="Edit User"
              class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-900 hover:text-white dark:hover:bg-indigo-600 transition-all opacity-0 group-hover:opacity-100">
              <Pencil class="w-4 h-4" />
            </button>
            <button @click="confirmDelete(user)" title="Delete User"
              class="w-8 h-8 flex items-center justify-center rounded-xl bg-rose-50 dark:bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white transition-all opacity-0 group-hover:opacity-100">
              <Trash2 class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="flex flex-col items-center justify-center py-20 text-center">
        <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-4">
          <UsersIcon class="w-8 h-8 text-slate-300 dark:text-slate-600" />
        </div>
        <h3 class="text-base font-[1000] text-slate-900 dark:text-white mb-1">No users found</h3>
        <p class="text-slate-400 text-sm">{{ searchQuery ? 'Try a different search query.' : 'Create the first user account.' }}</p>
      </div>

      <!-- Pagination -->
      <div v-if="pagination && pagination.last_page > 1"
        class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
        <p class="text-xs text-slate-400 font-semibold">
          Showing {{ pagination.from }}–{{ pagination.to }} of {{ pagination.total }} users
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

    <!-- Create / Edit Modal -->
    <Teleport to="body">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" @click="closeModal"></div>
        <div class="relative w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl shadow-2xl overflow-hidden">
          <!-- Modal Header -->
          <div class="flex items-center justify-between px-8 py-6 border-b border-slate-100 dark:border-slate-800">
            <div>
              <h2 class="text-xl font-[1000] text-slate-900 dark:text-white">{{ editingUser ? 'Edit User' : 'Create User' }}</h2>
              <p class="text-xs text-slate-400 mt-0.5">{{ editingUser ? 'Update account details and role' : 'Add a new platform user' }}</p>
            </div>
            <button @click="closeModal" class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
              <X class="w-4 h-4" />
            </button>
          </div>

          <!-- Modal Body -->
          <div class="p-8 space-y-5">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Full Name *</label>
                <input v-model="form.name" type="text" placeholder="John Doe"
                  class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
              </div>
              <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Role *</label>
                <select v-model="form.role"
                  class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all">
                  <option value="super-admin">Super Admin</option>
                  <option value="admin">Admin</option>
                  <option value="vendor">Vendor</option>
                  <option value="customer">Customer</option>
                </select>
              </div>
            </div>
            <div>
              <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Email Address *</label>
              <input v-model="form.email" type="email" placeholder="user@example.com"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
            </div>
            <div>
              <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">
                Password {{ editingUser ? '(leave blank to keep current)' : '*' }}
              </label>
              <input v-model="form.password" type="password" placeholder="Min. 8 characters"
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="px-8 py-5 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
            <button @click="closeModal" class="px-5 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
              Cancel
            </button>
            <button @click="saveUser" :disabled="saving"
              class="flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 disabled:opacity-50 transition-all">
              <Loader2 v-if="saving" class="w-4 h-4 animate-spin" />
              <Check v-else class="w-4 h-4" />
              {{ saving ? 'Saving…' : (editingUser ? 'Update User' : 'Create User') }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Delete Confirm Modal -->
    <Teleport to="body">
      <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="deleteTarget = null"></div>
        <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl p-8 max-w-sm w-full">
          <div class="w-14 h-14 bg-rose-50 dark:bg-rose-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <Trash2 class="w-7 h-7 text-rose-500" />
          </div>
          <h3 class="text-lg font-[1000] text-slate-900 dark:text-white text-center mb-2">Delete User?</h3>
          <p class="text-sm text-slate-400 text-center mb-6">
            Permanently delete <strong class="text-slate-700 dark:text-slate-200">{{ deleteTarget?.name }}</strong>? This action cannot be undone.
          </p>
          <div class="flex gap-3">
            <button @click="deleteTarget = null" class="flex-1 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 rounded-xl hover:bg-slate-200 transition-all">Cancel</button>
            <button @click="deleteUser" :disabled="saving" class="flex-1 py-2.5 text-sm font-bold text-white bg-rose-500 rounded-xl hover:bg-rose-600 disabled:opacity-50 transition-all">
              {{ saving ? 'Deleting…' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>

<script setup>
import {
  Shield, ChevronRight, Search, UserPlus, Pencil, Trash2,
  X, Loader2, Check, Users as UsersIcon, ShieldCheck, Store,
  UserCog, UserCircle, TrendingUp
} from 'lucide-vue-next'
import { toast } from 'vue-sonner'

definePageMeta({ 
  middleware: 'auth', 
  layout: 'default',
  permissions: 'admin.users.view'
})

const { getAll, getHeaders } = useCrud()
const config = useRuntimeConfig()
const baseURL = config.public.apiBase

const users = ref([])
const pagination = ref(null)
const loading = ref(true)
const saving = ref(false)
const searchQuery = ref('')
const roleFilter = ref('')
const currentPage = ref(1)
const showModal = ref(false)
const editingUser = ref(null)
const deleteTarget = ref(null)

const defaultForm = () => ({ name: '', email: '', password: '', role: 'vendor' })
const form = ref(defaultForm())

// ── Derived Role Stats ────────────────────────────────────────────────
const allUsers = ref([])
const roleStats = computed(() => [
  {
    role: 'Super Admin',
    count: allUsers.value.filter(u => u.roles?.[0]?.name === 'super-admin').length,
    icon: ShieldCheck,
    bg: 'bg-violet-50 dark:bg-violet-500/10',
    color: 'text-violet-600 dark:text-violet-400',
  },
  {
    role: 'Admin',
    count: allUsers.value.filter(u => u.roles?.[0]?.name === 'admin').length,
    icon: UserCog,
    bg: 'bg-indigo-50 dark:bg-indigo-500/10',
    color: 'text-indigo-600 dark:text-indigo-400',
  },
  {
    role: 'Vendor',
    count: allUsers.value.filter(u => u.roles?.[0]?.name === 'vendor').length,
    icon: Store,
    bg: 'bg-emerald-50 dark:bg-emerald-500/10',
    color: 'text-emerald-600 dark:text-emerald-400',
  },
  {
    role: 'Customer',
    count: allUsers.value.filter(u => u.roles?.[0]?.name === 'customer').length,
    icon: UserCircle,
    bg: 'bg-amber-50 dark:bg-amber-500/10',
    color: 'text-amber-600 dark:text-amber-400',
  },
])

// ── Helpers ───────────────────────────────────────────────────────────
const avatarClass = (role) => ({
  'super-admin': 'bg-violet-600',
  admin: 'bg-indigo-600',
  vendor: 'bg-emerald-600',
  customer: 'bg-amber-500',
}[role] || 'bg-slate-500')

const roleBadgeClass = (role) => ({
  'super-admin': 'bg-violet-100 dark:bg-violet-500/20 text-violet-700 dark:text-violet-400',
  admin: 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-400',
  vendor: 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400',
  customer: 'bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400',
}[role] || 'bg-slate-100 dark:bg-slate-800 text-slate-500')

const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : '—'

// ── Data Fetching ─────────────────────────────────────────────────────
const fetchUsers = async () => {
  loading.value = true
  try {
    const query = { page: currentPage.value }
    if (searchQuery.value) query.search = searchQuery.value
    if (roleFilter.value)  query.role   = roleFilter.value
    const res = await getAll('/admin/users', query)
    users.value      = res.data || []
    pagination.value = res
  } catch (e) {
    console.error(e)
    toast.error('Failed to load users')
  } finally {
    loading.value = false
  }
}

// Fetch all users once for stats (no pagination params)
const fetchAllForStats = async () => {
  try {
    const res = await getAll('/admin/users', { per_page: 500 })
    allUsers.value = res.data || []
  } catch (e) { /* silent */ }
}

let searchTimer = null
const debouncedSearch = () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => { currentPage.value = 1; fetchUsers() }, 400)
}

const changePage = (page) => { currentPage.value = page; fetchUsers() }

// ── CRUD ──────────────────────────────────────────────────────────────
const openCreateModal = () => {
  editingUser.value = null
  form.value = defaultForm()
  showModal.value = true
}

const openEditModal = (user) => {
  editingUser.value = user
  form.value = { name: user.name, email: user.email, password: '', role: user.roles?.[0]?.name || 'vendor' }
  showModal.value = true
}

const closeModal = () => { showModal.value = false; editingUser.value = null; form.value = defaultForm() }

const saveUser = async () => {
  if (!form.value.name)  { toast.error('Name is required');  return }
  if (!form.value.email) { toast.error('Email is required'); return }
  saving.value = true
  try {
    const body = { ...form.value }
    if (!body.password) delete body.password

    if (editingUser.value) {
      await $fetch(`${baseURL}/admin/users/${editingUser.value.id}`, { method: 'PUT', body, headers: getHeaders() })
      toast.success('User updated successfully')
    } else {
      await $fetch(`${baseURL}/admin/users`, { method: 'POST', body, headers: getHeaders() })
      toast.success('User created successfully')
    }
    closeModal()
    await Promise.all([fetchUsers(), fetchAllForStats()])
  } catch (e) {
    toast.error(e.data?.message || 'Failed to save user')
  } finally {
    saving.value = false
  }
}

const confirmDelete = (user) => { deleteTarget.value = user }

const deleteUser = async () => {
  if (!deleteTarget.value) return
  saving.value = true
  try {
    await $fetch(`${baseURL}/admin/users/${deleteTarget.value.id}`, { method: 'DELETE', headers: getHeaders() })
    toast.success('User deleted')
    deleteTarget.value = null
    await Promise.all([fetchUsers(), fetchAllForStats()])
  } catch (e) {
    toast.error(e.data?.message || 'Failed to delete user')
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  fetchUsers()
  fetchAllForStats()
})
</script>
