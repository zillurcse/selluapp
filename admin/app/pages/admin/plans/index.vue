<template>
  <div class="p-8 bg-[#f8fafc] dark:bg-slate-950 min-h-full">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500 mb-2 font-bold uppercase tracking-widest">
          <Shield class="w-3.5 h-3.5" />
          <NuxtLink to="/admin" class="hover:text-indigo-500 transition-colors">Super Admin</NuxtLink>
          <ChevronRight class="w-3 h-3" />
          <span class="text-slate-600 dark:text-slate-300">Plans</span>
        </div>
        <h1 class="text-3xl font-[1000] text-slate-900 dark:text-white tracking-tight">Subscription Plans</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Create and manage plans you offer to vendors.</p>
      </div>
      <button @click="openCreateModal"
        class="flex items-center gap-2 px-5 py-2.5 bg-slate-900 dark:bg-indigo-600 text-white rounded-xl text-sm font-bold hover:scale-105 transition-all shadow-lg shadow-slate-900/20 dark:shadow-indigo-600/30">
        <Plus class="w-4 h-4" />
        New Plan
      </button>
    </div>

    <!-- Package Cards -->
    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
      <div v-for="i in 3" :key="i" class="h-80 bg-white dark:bg-slate-900 rounded-2xl animate-pulse border border-slate-100 dark:border-slate-800"></div>
    </div>

    <div v-else-if="packages.length" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
      <div v-for="pkg in packages" :key="pkg.id"
        class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 overflow-hidden hover:border-indigo-300/50 dark:hover:border-indigo-500/30 transition-all hover:shadow-xl hover:shadow-slate-200/70 dark:hover:shadow-slate-950/50 group hover:-translate-y-1">

        <!-- Card Top -->
        <div class="relative p-6 pb-4" :class="pkg.is_active ? 'bg-gradient-to-br from-slate-50 to-white dark:from-slate-800/50 dark:to-slate-900' : 'bg-slate-50/50 dark:bg-slate-800/20'">
          <!-- Status badge -->
          <div class="absolute top-4 right-4">
            <span :class="pkg.is_active
                ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400'
                : 'bg-slate-100 dark:bg-slate-800 text-slate-500'"
              class="text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full">
              {{ pkg.is_active ? 'Active' : 'Inactive' }}
            </span>
          </div>

          <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-4 shadow-lg" :class="planStyle(pkg.duration).bg">
            <component :is="planStyle(pkg.duration).icon" class="w-6 h-6" :class="planStyle(pkg.duration).color" />
          </div>

          <h3 class="text-xl font-[1000] text-slate-900 dark:text-white mb-1">{{ pkg.name }}</h3>
          <p class="text-sm text-slate-400 line-clamp-2 mb-4">{{ pkg.description || 'No description provided.' }}</p>

          <div class="flex items-end gap-1.5">
            <span class="text-4xl font-[1000] text-slate-900 dark:text-white tracking-tight">৳{{ Number(pkg.price).toLocaleString() }}</span>
            <span class="text-slate-400 text-sm mb-1.5 font-bold">/ {{ pkg.duration }}</span>
          </div>

          <div class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 px-2.5 py-1 rounded-full">
            <Users class="w-3.5 h-3.5" />
            {{ pkg.vendor_profiles_count }} vendor{{ pkg.vendor_profiles_count !== 1 ? 's' : '' }} subscribed
          </div>
        </div>

        <!-- Divider -->
        <div class="h-px bg-slate-100 dark:bg-slate-800"></div>

        <!-- Features -->
        <div class="p-6 space-y-2.5">
          <div class="flex items-center gap-2.5 text-sm text-slate-600 dark:text-slate-300">
            <div class="w-6 h-6 rounded-lg flex items-center justify-center flex-shrink-0" :class="pkg.product_limit === -1 ? 'bg-indigo-50 dark:bg-indigo-500/10' : 'bg-slate-100 dark:bg-slate-800'">
              <Box class="w-3.5 h-3.5" :class="pkg.product_limit === -1 ? 'text-indigo-500' : 'text-slate-400'" />
            </div>
            <span class="font-semibold">{{ pkg.product_limit === -1 ? 'Unlimited' : pkg.product_limit }} Products</span>
          </div>
          <div class="flex items-center gap-2.5 text-sm text-slate-600 dark:text-slate-300">
            <div class="w-6 h-6 rounded-lg flex items-center justify-center flex-shrink-0" :class="pkg.order_limit === -1 ? 'bg-indigo-50 dark:bg-indigo-500/10' : 'bg-slate-100 dark:bg-slate-800'">
              <ShoppingCart class="w-3.5 h-3.5" :class="pkg.order_limit === -1 ? 'text-indigo-500' : 'text-slate-400'" />
            </div>
            <span class="font-semibold">{{ pkg.order_limit === -1 ? 'Unlimited' : pkg.order_limit }} Orders/month</span>
          </div>
          <div class="flex items-center gap-2.5 text-sm text-slate-600 dark:text-slate-300">
            <div class="w-6 h-6 rounded-lg flex items-center justify-center flex-shrink-0" :class="pkg.employee_limit === -1 ? 'bg-indigo-50 dark:bg-indigo-500/10' : 'bg-slate-100 dark:bg-slate-800'">
              <UserCheck class="w-3.5 h-3.5" :class="pkg.employee_limit === -1 ? 'text-indigo-500' : 'text-slate-400'" />
            </div>
            <span class="font-semibold">{{ pkg.employee_limit === -1 ? 'Unlimited' : pkg.employee_limit }} Employees</span>
          </div>
          <div class="flex items-center gap-2.5 text-sm" :class="pkg.pos_access ? 'text-slate-600 dark:text-slate-300' : 'text-slate-300 dark:text-slate-600'">
            <div class="w-6 h-6 rounded-lg flex items-center justify-center flex-shrink-0" :class="pkg.pos_access ? 'bg-emerald-50 dark:bg-emerald-500/10' : 'bg-slate-100 dark:bg-slate-800'">
              <Monitor class="w-3.5 h-3.5" :class="pkg.pos_access ? 'text-emerald-500' : 'text-slate-400'" />
            </div>
            <span class="font-semibold" :class="{ 'line-through opacity-50': !pkg.pos_access }">POS System Access</span>
            <Check v-if="pkg.pos_access" class="w-3.5 h-3.5 text-emerald-500 ml-auto" />
          </div>
          <div class="flex items-center gap-2.5 text-sm" :class="pkg.hrm_access ? 'text-slate-600 dark:text-slate-300' : 'text-slate-300 dark:text-slate-600'">
            <div class="w-6 h-6 rounded-lg flex items-center justify-center flex-shrink-0" :class="pkg.hrm_access ? 'bg-emerald-50 dark:bg-emerald-500/10' : 'bg-slate-100 dark:bg-slate-800'">
              <Briefcase class="w-3.5 h-3.5" :class="pkg.hrm_access ? 'text-emerald-500' : 'text-slate-400'" />
            </div>
            <span class="font-semibold" :class="{ 'line-through opacity-50': !pkg.hrm_access }">HRM Suite Access</span>
            <Check v-if="pkg.hrm_access" class="w-3.5 h-3.5 text-emerald-500 ml-auto" />
          </div>
        </div>

        <!-- Actions -->
        <div class="px-6 pb-6 flex gap-2">
          <button @click="editPackage(pkg)"
            class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-900 hover:text-white dark:hover:bg-slate-700 transition-all">
            <Pencil class="w-3.5 h-3.5" /> Edit
          </button>
          <button @click="confirmDelete(pkg)"
            class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider border border-rose-200 dark:border-rose-900/40 text-rose-500 hover:bg-rose-500 hover:text-white hover:border-rose-500 transition-all">
            <Trash2 class="w-3.5 h-3.5" />
          </button>
        </div>
      </div>
    </div>

    <div v-else class="flex flex-col items-center justify-center py-24 text-center">
      <div class="w-16 h-16 bg-indigo-50 dark:bg-indigo-500/10 rounded-2xl flex items-center justify-center mb-4">
        <Package class="w-8 h-8 text-indigo-400" />
      </div>
      <h3 class="text-lg font-[1000] text-slate-900 dark:text-white mb-2">No packages yet</h3>
      <p class="text-slate-400 text-sm mb-6">Create your first subscription package to offer vendors.</p>
      <button @click="openCreateModal" class="px-5 py-2.5 bg-slate-900 dark:bg-indigo-600 text-white rounded-xl text-sm font-bold hover:scale-105 transition-all">
        Create First Package
      </button>
    </div>

    <!-- Create/Edit Modal -->
    <Teleport to="body">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm" @click="closeModal"></div>
        <div class="relative w-full max-w-2xl bg-white dark:bg-slate-900 rounded-3xl shadow-2xl overflow-hidden max-h-[90vh] overflow-y-auto">
          <!-- Modal Header -->
          <div class="sticky top-0 z-10 flex items-center justify-between px-8 py-6 bg-white dark:bg-slate-900 border-b border-slate-100 dark:border-slate-800">
            <div>
              <h2 class="text-xl font-[1000] text-slate-900 dark:text-white">{{ editing ? 'Edit Package' : 'Create Package' }}</h2>
              <p class="text-xs text-slate-400 mt-0.5">{{ editing ? 'Update package details' : 'Design a new subscription plan' }}</p>
            </div>
            <button @click="closeModal" class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
              <X class="w-4 h-4" />
            </button>
          </div>

          <!-- Modal Body -->
          <div class="p-8 space-y-6">
            <!-- Name + Duration -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Package Name *</label>
                <input v-model="form.name" type="text" placeholder="e.g. Starter Plan"
                  class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-400 transition-all" />
              </div>
              <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Duration *</label>
                <select v-model="form.duration"
                  class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-400 transition-all">
                  <option value="monthly">Monthly</option>
                  <option value="yearly">Yearly</option>
                  <option value="lifetime">Lifetime</option>
                </select>
              </div>
            </div>

            <!-- Price -->
            <div>
              <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Price (৳) *</label>
              <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">৳</span>
                <input v-model.number="form.price" type="number" min="0" placeholder="0.00"
                  class="w-full pl-8 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-400 transition-all" />
              </div>
            </div>

            <!-- Description -->
            <div>
              <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Description</label>
              <textarea v-model="form.description" rows="3" placeholder="What's included in this plan..."
                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-400 transition-all resize-none"></textarea>
            </div>

            <!-- Limits -->
            <div>
              <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-3">Limits <span class="normal-case font-normal text-slate-400">(use -1 for unlimited)</span></label>
              <div class="grid grid-cols-3 gap-4">
                <div>
                  <label class="block text-xs text-slate-400 mb-1.5 font-semibold">Products</label>
                  <input v-model.number="form.product_limit" type="number" min="-1"
                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
                </div>
                <div>
                  <label class="block text-xs text-slate-400 mb-1.5 font-semibold">Orders/mo</label>
                  <input v-model.number="form.order_limit" type="number" min="-1"
                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
                </div>
                <div>
                  <label class="block text-xs text-slate-400 mb-1.5 font-semibold">Employees</label>
                  <input v-model.number="form.employee_limit" type="number" min="-1"
                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
                </div>
              </div>
            </div>

            <!-- Feature Toggles -->
            <div>
              <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-3">Feature Access</label>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 cursor-pointer hover:border-indigo-400/50 transition-all">
                  <div class="relative flex-shrink-0">
                    <input type="checkbox" v-model="form.pos_access" class="sr-only peer" />
                    <div class="w-10 h-5 bg-slate-200 dark:bg-slate-700 peer-checked:bg-indigo-600 rounded-full transition-all"></div>
                    <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-all peer-checked:translate-x-5"></div>
                  </div>
                  <div>
                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200">POS System</p>
                    <p class="text-xs text-slate-400">Point of Sale access</p>
                  </div>
                </label>
                <label class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 cursor-pointer hover:border-indigo-400/50 transition-all">
                  <div class="relative flex-shrink-0">
                    <input type="checkbox" v-model="form.hrm_access" class="sr-only peer" />
                    <div class="w-10 h-5 bg-slate-200 dark:bg-slate-700 peer-checked:bg-indigo-600 rounded-full transition-all"></div>
                    <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-all peer-checked:translate-x-5"></div>
                  </div>
                  <div>
                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200">HRM Suite</p>
                    <p class="text-xs text-slate-400">Human Resource Management</p>
                  </div>
                </label>
                <label class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 cursor-pointer hover:border-indigo-400/50 transition-all sm:col-span-2">
                  <div class="relative flex-shrink-0">
                    <input type="checkbox" v-model="form.is_active" class="sr-only peer" />
                    <div class="w-10 h-5 bg-slate-200 dark:bg-slate-700 peer-checked:bg-emerald-500 rounded-full transition-all"></div>
                    <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-all peer-checked:translate-x-5"></div>
                  </div>
                  <div>
                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200">Active Package</p>
                    <p class="text-xs text-slate-400">Vendors can subscribe to this package</p>
                  </div>
                </label>
              </div>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="sticky bottom-0 px-8 py-5 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
            <button @click="closeModal" class="px-5 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
              Cancel
            </button>
            <button @click="savePackage" :disabled="saving"
              class="flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
              <Loader2 v-if="saving" class="w-4 h-4 animate-spin" />
              <Check v-else class="w-4 h-4" />
              {{ saving ? 'Saving...' : (editing ? 'Update Package' : 'Create Package') }}
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
          <h3 class="text-lg font-[1000] text-slate-900 dark:text-white text-center mb-2">Delete Package?</h3>
          <p class="text-sm text-slate-400 text-center mb-6">
            Delete <strong class="text-slate-700 dark:text-slate-200">{{ deleteTarget?.name }}</strong>? This cannot be undone.
            Vendors currently on this package must be reassigned first.
          </p>
          <div class="flex gap-3">
            <button @click="deleteTarget = null" class="flex-1 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-800 rounded-xl hover:bg-slate-200 transition-all">Cancel</button>
            <button @click="deletePackage" :disabled="saving" class="flex-1 py-2.5 text-sm font-bold text-white bg-rose-500 rounded-xl hover:bg-rose-600 disabled:opacity-50 transition-all">
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
  Shield, ChevronRight, Plus, Package, Users, Box, ShoppingCart,
  UserCheck, Monitor, Briefcase, Check, Pencil, Trash2, X, Loader2,
  Zap, Star, Infinity
} from 'lucide-vue-next'
import { toast } from 'vue-sonner'

definePageMeta({
  middleware: 'auth',
  layout: 'default',
  permissions: 'admin.plans.view'
})

const { getAll, getHeaders } = useCrud()
const config = useRuntimeConfig()
const baseURL = config.public.apiBase

const packages = ref([])
const loading = ref(true)
const showModal = ref(false)
const editing = ref(false)
const saving = ref(false)
const deleteTarget = ref(null)

const defaultForm = () => ({
  name: '',
  description: '',
  price: 0,
  duration: 'monthly',
  product_limit: -1,
  order_limit: -1,
  employee_limit: -1,
  pos_access: false,
  hrm_access: false,
  is_active: true,
})

const form = ref(defaultForm())
const editingId = ref(null)

const planStyle = (duration) => {
  const styles = {
    monthly: { icon: Zap, bg: 'bg-indigo-50 dark:bg-indigo-500/10', color: 'text-indigo-500' },
    yearly:  { icon: Star, bg: 'bg-amber-50 dark:bg-amber-500/10', color: 'text-amber-500' },
    lifetime:{ icon: Infinity, bg: 'bg-violet-50 dark:bg-violet-500/10', color: 'text-violet-500' },
  }
  return styles[duration] || styles.monthly
}

const fetchPackages = async () => {
  loading.value = true
  try {
    packages.value = await getAll('/admin/packages')
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const openCreateModal = () => {
  editing.value = false
  editingId.value = null
  form.value = defaultForm()
  showModal.value = true
}

const editPackage = (pkg) => {
  editing.value = true
  editingId.value = pkg.id
  form.value = {
    name: pkg.name,
    description: pkg.description || '',
    price: pkg.price,
    duration: pkg.duration,
    product_limit: pkg.product_limit,
    order_limit: pkg.order_limit,
    employee_limit: pkg.employee_limit,
    pos_access: pkg.pos_access,
    hrm_access: pkg.hrm_access,
    is_active: pkg.is_active,
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editing.value = false
  editingId.value = null
  form.value = defaultForm()
}

const savePackage = async () => {
  if (!form.value.name) { toast.error('Package name is required'); return }
  saving.value = true
  try {
    const url = editing.value
      ? `${baseURL}/admin/packages/${editingId.value}`
      : `${baseURL}/admin/packages`
    const method = editing.value ? 'PUT' : 'POST'

    const res = await $fetch(url, { method, body: form.value, headers: getHeaders() })
    toast.success(res.message || 'Package saved!')
    closeModal()
    await fetchPackages()
  } catch (e) {
    toast.error(e.data?.message || 'Failed to save package')
  } finally {
    saving.value = false
  }
}

const confirmDelete = (pkg) => { deleteTarget.value = pkg }

const deletePackage = async () => {
  if (!deleteTarget.value) return
  saving.value = true
  try {
    const res = await $fetch(`${baseURL}/admin/packages/${deleteTarget.value.id}`, {
      method: 'DELETE', headers: getHeaders()
    })
    toast.success(res.message || 'Package deleted')
    deleteTarget.value = null
    await fetchPackages()
  } catch (e) {
    toast.error(e.data?.message || 'Failed to delete')
  } finally {
    saving.value = false
  }
}

onMounted(fetchPackages)
</script>
