<template>
  <div class="p-8 bg-[#f8fafc] dark:bg-slate-950 min-h-full">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500 mb-2 font-bold uppercase tracking-widest">
          <Shield class="w-3.5 h-3.5" />
          <NuxtLink to="/admin" class="hover:text-indigo-500 transition-colors">Super Admin</NuxtLink>
          <ChevronRight class="w-3 h-3" />
          <span class="text-slate-600 dark:text-slate-300">Subscriptions</span>
        </div>
        <h1 class="text-3xl font-[1000] text-slate-900 dark:text-white tracking-tight">Subscriptions</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Assign and manage vendor subscription plans.</p>
      </div>
      <button @click="openCreateModal" class="flex items-center gap-2 px-5 py-2.5 bg-slate-900 dark:bg-indigo-600 text-white rounded-xl text-sm font-bold hover:scale-105 transition-all shadow-lg">
        <Plus class="w-4 h-4" /> Assign Subscription
      </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
      <div v-for="s in statCards" :key="s.label" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 p-5">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">{{ s.label }}</p>
        <div v-if="!statsLoading" class="text-2xl font-[1000] text-slate-900 dark:text-white">{{ s.value }}</div>
        <div v-else class="h-7 w-24 bg-slate-100 dark:bg-slate-800 rounded animate-pulse mt-1"></div>
        <div class="flex items-center gap-1.5 mt-2">
          <div class="w-2 h-2 rounded-full" :class="s.dot"></div>
          <span class="text-[10px] font-bold text-slate-400">{{ s.sub }}</span>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-wrap gap-3 mb-6">
      <div class="relative flex-1 min-w-[200px] max-w-sm">
        <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
        <input v-model="searchQuery" @input="debouncedSearch" type="text" placeholder="Search by vendor name or email…"
          class="w-full pl-10 pr-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
      </div>
      <select v-model="statusFilter" @change="fetchSubs"
        class="px-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all">
        <option value="">All Statuses</option>
        <option value="active">Active</option>
        <option value="expired">Expired</option>
        <option value="cancelled">Cancelled</option>
        <option value="pending">Pending</option>
      </select>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm">
      <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 hidden md:grid grid-cols-12 gap-4">
        <div class="col-span-4 text-xs font-black text-slate-400 uppercase tracking-widest">Vendor</div>
        <div class="col-span-2 text-xs font-black text-slate-400 uppercase tracking-widest">Plan</div>
        <div class="col-span-2 text-xs font-black text-slate-400 uppercase tracking-widest">Amount</div>
        <div class="col-span-2 text-xs font-black text-slate-400 uppercase tracking-widest">Expires</div>
        <div class="col-span-1 text-xs font-black text-slate-400 uppercase tracking-widest">Status</div>
        <div class="col-span-1 text-xs font-black text-slate-400 uppercase tracking-widest text-right">Act.</div>
      </div>

      <div v-if="loading" class="divide-y divide-slate-100 dark:divide-slate-800">
        <div v-for="i in 8" :key="i" class="px-6 py-4">
          <div class="h-10 bg-slate-100 dark:bg-slate-800 rounded-xl animate-pulse"></div>
        </div>
      </div>

      <div v-else-if="subs.length" class="divide-y divide-slate-100 dark:divide-slate-800">
        <div v-for="s in subs" :key="s.id"
          class="px-6 py-4 md:grid md:grid-cols-12 md:gap-4 flex flex-col gap-2 hover:bg-slate-50/70 dark:hover:bg-slate-800/40 transition-all group">

          <!-- Vendor -->
          <div class="col-span-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white text-sm font-black flex-shrink-0 shadow-md shadow-emerald-500/20">
              {{ (s.user?.name || 'V').charAt(0).toUpperCase() }}
            </div>
            <div class="min-w-0">
              <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ s.user?.name || '—' }}</p>
              <p class="text-[10px] text-slate-400 truncate">{{ s.user?.email }}</p>
            </div>
          </div>

          <!-- Plan -->
          <div class="col-span-2 flex items-center">
            <div>
              <p class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ s.package?.name || 'No Plan' }}</p>
              <p class="text-[10px] text-slate-400 capitalize">{{ s.billing_cycle }}</p>
            </div>
          </div>

          <!-- Amount -->
          <div class="col-span-2 flex items-center">
            <p class="text-sm font-[1000] text-slate-900 dark:text-white">৳{{ Number(s.amount).toLocaleString() }}</p>
          </div>

          <!-- Expires -->
          <div class="col-span-2 flex items-center">
            <div>
              <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">{{ formatDate(s.ends_at) }}</p>
              <p v-if="s.ends_at && daysLeft(s.ends_at) >= 0" class="text-[10px]"
                :class="daysLeft(s.ends_at) <= 7 ? 'text-rose-400 font-black' : 'text-slate-400'">
                {{ daysLeft(s.ends_at) === 0 ? 'Today' : daysLeft(s.ends_at) + 'd left' }}
              </p>
              <p v-else-if="s.billing_cycle === 'lifetime'" class="text-[10px] text-indigo-400 font-bold">Lifetime</p>
            </div>
          </div>

          <!-- Status -->
          <div class="col-span-1 flex items-center">
            <span class="px-2 py-1 rounded-full text-[9px] font-black uppercase tracking-wider whitespace-nowrap" :class="subStatusClass(s.status)">
              {{ s.status }}
            </span>
          </div>

          <!-- Actions -->
          <div class="col-span-1 flex items-center justify-end gap-1.5">
            <button @click="openEditModal(s)"
              class="w-7 h-7 flex items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-400 hover:bg-indigo-600 hover:text-white transition-all opacity-0 group-hover:opacity-100" title="Edit">
              <Pencil class="w-3.5 h-3.5" />
            </button>
            <button @click="confirmDelete(s)"
              class="w-7 h-7 flex items-center justify-center rounded-lg bg-rose-50 dark:bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white transition-all opacity-0 group-hover:opacity-100" title="Remove">
              <Trash2 class="w-3.5 h-3.5" />
            </button>
          </div>
        </div>
      </div>

      <div v-else class="flex flex-col items-center justify-center py-20">
        <div class="w-14 h-14 bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-4">
          <Zap class="w-7 h-7 text-slate-300 dark:text-slate-600" />
        </div>
        <h3 class="text-base font-[1000] text-slate-900 dark:text-white mb-1">No subscriptions yet</h3>
        <p class="text-slate-400 text-sm mb-6">Assign a plan to a vendor to get started.</p>
        <button @click="openCreateModal" class="px-5 py-2.5 bg-slate-900 dark:bg-indigo-600 text-white rounded-xl text-sm font-bold hover:scale-105 transition-all">
          Assign First Subscription
        </button>
      </div>

      <div v-if="pagination?.last_page > 1" class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
        <p class="text-xs text-slate-400 font-semibold">Showing {{ pagination.from }}–{{ pagination.to }} of {{ pagination.total }}</p>
        <div class="flex gap-2">
          <button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1"
            class="px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 disabled:opacity-40 hover:bg-slate-200 transition-all">Prev</button>
          <button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page"
            class="px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 disabled:opacity-40 hover:bg-slate-200 transition-all">Next</button>
        </div>
      </div>
    </div>

    <!-- ─── Assign / Edit Modal ───────────────────────────────────────── -->
    <Teleport to="body">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModal"></div>
        <div class="relative w-full max-w-xl bg-white dark:bg-slate-900 rounded-3xl shadow-2xl overflow-hidden max-h-[92vh] flex flex-col">

          <!-- Modal Header -->
          <div class="flex items-center justify-between px-8 py-6 border-b border-slate-100 dark:border-slate-800 flex-shrink-0">
            <div>
              <h2 class="text-xl font-[1000] text-slate-900 dark:text-white">
                {{ editingItem ? 'Edit Subscription' : 'Assign Subscription' }}
              </h2>
              <p class="text-xs text-slate-400 mt-0.5">
                {{ editingItem ? "Update the vendor's plan details" : 'Select a vendor and assign a plan' }}
              </p>
            </div>
            <button @click="closeModal" class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-100 dark:bg-slate-800 text-slate-400 hover:bg-slate-200 transition-all">
              <X class="w-4 h-4" />
            </button>
          </div>

          <!-- Modal Body -->
          <div class="p-8 space-y-6 overflow-y-auto">

            <!-- ① Vendor Selector -->
            <div>
              <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-3">
                <span class="flex items-center gap-2"><Store class="w-3.5 h-3.5" /> Select Vendor *</span>
              </label>

              <!-- Search vendors -->
              <div class="relative mb-2">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                <input v-model="vendorSearch" @input="filterVendors" type="text" placeholder="Search vendor by name or email…"
                  class="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
              </div>

              <!-- Vendor list -->
              <div class="max-h-44 overflow-y-auto rounded-xl border border-slate-100 dark:border-slate-800 divide-y divide-slate-50 dark:divide-slate-800/60">
                <div v-if="vendorsLoading" class="p-4 flex items-center justify-center">
                  <Loader2 class="w-5 h-5 animate-spin text-slate-400" />
                </div>
                <div v-else-if="filteredVendors.length === 0" class="p-4 text-center text-sm text-slate-400">No vendors found.</div>
                <button v-for="v in filteredVendors" :key="v.id" @click="selectVendor(v)" type="button"
                  class="w-full flex items-center gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-all text-left"
                  :class="form.user_id === v.id ? 'bg-indigo-50 dark:bg-indigo-500/10' : ''">
                  <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-500 flex items-center justify-center text-white text-xs font-black flex-shrink-0">
                    {{ v.name.charAt(0).toUpperCase() }}
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ v.name }}</p>
                    <p class="text-[10px] text-slate-400 truncate">{{ v.email }}</p>
                  </div>
                  <div v-if="form.user_id === v.id" class="w-5 h-5 rounded-full bg-indigo-600 flex items-center justify-center flex-shrink-0">
                    <Check class="w-3 h-3 text-white" />
                  </div>
                </button>
              </div>

              <!-- Selected vendor chip -->
              <div v-if="selectedVendor && form.user_id" class="mt-2 flex items-center gap-2 px-3 py-2 bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 rounded-xl">
                <div class="w-6 h-6 rounded-lg bg-indigo-600 flex items-center justify-center text-white text-[10px] font-black">
                  {{ selectedVendor.name.charAt(0).toUpperCase() }}
                </div>
                <p class="text-xs font-bold text-indigo-700 dark:text-indigo-400">{{ selectedVendor.name }}</p>
                <p class="text-[10px] text-indigo-400 ml-auto">ID: {{ selectedVendor.id }}</p>
              </div>
            </div>

            <!-- ② Plan Selector -->
            <div>
              <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-3">
                <span class="flex items-center gap-2"><Layers class="w-3.5 h-3.5" /> Select Plan *</span>
              </label>
              <div class="grid grid-cols-1 gap-2">
                <div v-if="plansLoading" class="h-12 bg-slate-50 dark:bg-slate-800 rounded-xl animate-pulse"></div>
                <label v-for="plan in plans" :key="plan.id"
                  class="flex items-center gap-4 p-4 rounded-2xl border cursor-pointer transition-all"
                  :class="planCardClass(plan.id)">
                  <input type="radio" :value="plan.id" v-model="form.package_id" @change="onPlanChange(plan)" class="sr-only" />
                  <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                    :class="planStyle(plan.duration).bg">
                    <component :is="planStyle(plan.duration).icon" class="w-5 h-5" :class="planStyle(plan.duration).color" />
                  </div>
                  <div class="flex-1">
                    <p class="text-sm font-bold text-slate-900 dark:text-white">{{ plan.name }}</p>
                    <p class="text-[10px] text-slate-400 capitalize">{{ plan.duration }} · {{ plan.product_limit === -1 ? 'Unlimited' : plan.product_limit }} products</p>
                  </div>
                  <div class="text-right flex-shrink-0">
                    <p class="text-base font-[1000] text-slate-900 dark:text-white">৳{{ Number(plan.price).toLocaleString() }}</p>
                    <p class="text-[10px] text-slate-400">/ {{ plan.duration }}</p>
                  </div>
                  <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 transition-all"
                    :class="form.package_id === plan.id ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300 dark:border-slate-600'">
                    <div v-if="form.package_id === plan.id" class="w-2 h-2 rounded-full bg-white"></div>
                  </div>
                </label>
              </div>
            </div>

            <!-- ③ Details -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Status</label>
                <select v-model="form.status" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all">
                  <option value="active">Active</option>
                  <option value="pending">Pending</option>
                  <option value="expired">Expired</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Billing Cycle</label>
                <select v-model="form.billing_cycle" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all">
                  <option value="monthly">Monthly</option>
                  <option value="yearly">Yearly</option>
                  <option value="lifetime">Lifetime</option>
                </select>
              </div>
              <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Starts At</label>
                <input v-model="form.starts_at" type="date" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
              </div>
              <div>
                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Ends At</label>
                <input v-model="form.ends_at" type="date" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
              </div>
            </div>

            <!-- Amount (auto-filled from plan, editable) -->
            <div>
              <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Amount (৳) — Auto-filled from plan</label>
              <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">৳</span>
                <input v-model.number="form.amount" type="number" min="0"
                  class="w-full pl-8 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" />
              </div>
            </div>
          </div>

          <!-- Modal Footer -->
          <div class="px-8 py-5 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3 flex-shrink-0">
            <button @click="closeModal" class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-100 transition-all">Cancel</button>
            <button @click="saveItem" :disabled="saving || !form.user_id || !form.package_id"
              class="flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
              <Loader2 v-if="saving" class="w-4 h-4 animate-spin" />
              <Zap v-else class="w-4 h-4" />
              {{ saving ? 'Saving…' : (editingItem ? 'Update Subscription' : 'Assign Plan') }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

    <!-- Delete Modal -->
    <Teleport to="body">
      <div v-if="deleteTarget" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="deleteTarget = null"></div>
        <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl p-8 max-w-sm w-full">
          <div class="w-14 h-14 bg-rose-50 dark:bg-rose-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <Trash2 class="w-7 h-7 text-rose-500" />
          </div>
          <h3 class="text-lg font-[1000] text-center text-slate-900 dark:text-white mb-2">Remove Subscription?</h3>
          <p class="text-sm text-slate-400 text-center mb-6">
            Subscription for <strong class="text-slate-700 dark:text-slate-200">{{ deleteTarget?.user?.name }}</strong> will be permanently removed.
          </p>
          <div class="flex gap-3">
            <button @click="deleteTarget = null" class="flex-1 py-2.5 text-sm font-bold bg-slate-100 dark:bg-slate-800 rounded-xl hover:bg-slate-200 transition-all">Cancel</button>
            <button @click="deleteItem" :disabled="saving" class="flex-1 py-2.5 text-sm font-bold text-white bg-rose-500 rounded-xl hover:bg-rose-600 disabled:opacity-50 transition-all">
              {{ saving ? 'Removing…' : 'Remove' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>

<script setup>
import {
  Shield, ChevronRight, Plus, Search, Pencil, Trash2,
  Zap, X, Loader2, Check, Store, Layers,
  Star
} from 'lucide-vue-next'
import { toast } from 'vue-sonner'

definePageMeta({
  middleware: 'auth',
  layout: 'default',
  permissions: 'admin.subscriptions.view'
})

const { getAll, getHeaders } = useCrud()
const config = useRuntimeConfig()
const baseURL = config.public.apiBase

// ── State ──────────────────────────────────────────────────────────────
const subs        = ref([])
const pagination  = ref(null)
const loading     = ref(true)
const statsLoading= ref(true)
const saving      = ref(false)
const searchQuery = ref('')
const statusFilter= ref('')
const currentPage = ref(1)
const showModal   = ref(false)
const editingItem = ref(null)
const deleteTarget= ref(null)
const rawStats    = ref({})

// Vendors
const allVendors   = ref([])
const vendorSearch = ref('')
const vendorsLoading = ref(false)
const selectedVendor = ref(null)

const filteredVendors = computed(() => {
  if (!vendorSearch.value) return allVendors.value
  const q = vendorSearch.value.toLowerCase()
  return allVendors.value.filter(v =>
    v.name.toLowerCase().includes(q) || v.email.toLowerCase().includes(q)
  )
})

// Plans
const plans       = ref([])
const plansLoading= ref(false)

// Form
const defaultForm = () => ({
  user_id: null, package_id: null, amount: 0,
  status: 'active', billing_cycle: 'monthly',
  starts_at: new Date().toISOString().split('T')[0],
  ends_at: ''
})
const form = ref(defaultForm())

// ── Helpers ─────────────────────────────────────────────────────────────
const planStyle = (duration) => ({
  monthly:  { icon: Zap,      bg: 'bg-indigo-50 dark:bg-indigo-500/10',  color: 'text-indigo-500' },
  yearly:   { icon: Star,     bg: 'bg-amber-50 dark:bg-amber-500/10',    color: 'text-amber-500' },
  lifetime: { icon: Layers,   bg: 'bg-violet-50 dark:bg-violet-500/10',  color: 'text-violet-500' },
}[duration] || { icon: Zap, bg: 'bg-slate-50 dark:bg-slate-800', color: 'text-slate-400' })

const planCardClass = (planId) =>
  form.value.package_id === planId
    ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 dark:border-indigo-500/50'
    : 'border-slate-100 dark:border-slate-800 hover:border-indigo-200 dark:hover:border-indigo-500/30 bg-white dark:bg-slate-900'

const statCards = computed(() => [
  { label: 'Active', value: rawStats.value.active || 0, sub: 'Live subscriptions', dot: 'bg-emerald-500' },
  { label: 'MRR', value: '৳' + Number(rawStats.value.mrr || 0).toLocaleString(), sub: 'Monthly recurring', dot: 'bg-indigo-500' },
  { label: 'ARR', value: '৳' + Number(rawStats.value.arr || 0).toLocaleString(), sub: 'Annual recurring', dot: 'bg-violet-500' },
  { label: 'Cancelled', value: rawStats.value.cancelled || 0, sub: 'Lost subscriptions', dot: 'bg-rose-500' },
])

const subStatusClass = (s) => ({
  active:    'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400',
  pending:   'bg-amber-100  dark:bg-amber-500/20  text-amber-700  dark:text-amber-400',
  expired:   'bg-slate-100  dark:bg-slate-800     text-slate-500',
  cancelled: 'bg-rose-100   dark:bg-rose-500/20   text-rose-600   dark:text-rose-400',
}[s] || 'bg-slate-100 text-slate-500')

const formatDate = (d) => d ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '—'

const daysLeft = (d) => {
  if (!d) return -1
  const diff = new Date(d) - new Date()
  return Math.ceil(diff / (1000 * 60 * 60 * 24))
}

// Auto-set ends_at when billing cycle changes
const onPlanChange = (plan) => {
  form.value.amount = plan.price
  form.value.billing_cycle = plan.duration
  const start = form.value.starts_at ? new Date(form.value.starts_at) : new Date()
  if (plan.duration === 'monthly') {
    start.setMonth(start.getMonth() + 1)
    form.value.ends_at = start.toISOString().split('T')[0]
  } else if (plan.duration === 'yearly') {
    start.setFullYear(start.getFullYear() + 1)
    form.value.ends_at = start.toISOString().split('T')[0]
  } else {
    form.value.ends_at = ''
  }
}

// ── Data loaders ─────────────────────────────────────────────────────────
const fetchStats = async () => {
  statsLoading.value = true
  try { rawStats.value = await getAll('/admin/finance/subscriptions/stats') } catch (e) {}
  finally { statsLoading.value = false }
}

const fetchSubs = async () => {
  loading.value = true
  try {
    const q = { page: currentPage.value }
    if (searchQuery.value) q.search = searchQuery.value
    if (statusFilter.value) q.status = statusFilter.value
    const res = await getAll('/admin/finance/subscriptions', q)
    subs.value      = res.data || []
    pagination.value = res
  } catch (e) { toast.error('Failed to load subscriptions') }
  finally { loading.value = false }
}

const fetchVendors = async () => {
  vendorsLoading.value = true
  try {
    const res = await getAll('/admin/vendors', { per_page: 200 })
    allVendors.value = res.data || []
  } catch (e) { console.error(e) }
  finally { vendorsLoading.value = false }
}

const fetchPlans = async () => {
  plansLoading.value = true
  try { plans.value = await getAll('/admin/packages') }
  catch (e) { console.error(e) }
  finally { plansLoading.value = false }
}

const filterVendors = () => {} // reactivity handled by computed

// ── CRUD ───────────────────────────────────────────────────────────────
const selectVendor = (v) => {
  selectedVendor.value = v
  form.value.user_id = v.id
  vendorSearch.value = ''
}

const openCreateModal = () => {
  editingItem.value  = null
  selectedVendor.value = null
  vendorSearch.value = ''
  form.value = defaultForm()
  showModal.value = true
}

const openEditModal = (item) => {
  editingItem.value = item
  selectedVendor.value = item.user ? { id: item.user.id, name: item.user.name, email: item.user.email } : null
  vendorSearch.value = ''
  form.value = {
    user_id:       item.user_id,
    package_id:    item.package_id,
    amount:        item.amount,
    status:        item.status,
    billing_cycle: item.billing_cycle,
    starts_at:     item.starts_at?.split('T')[0] || '',
    ends_at:       item.ends_at?.split('T')[0]   || '',
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value   = false
  editingItem.value = null
  selectedVendor.value = null
  form.value = defaultForm()
}

const saveItem = async () => {
  if (!form.value.user_id)    { toast.error('Please select a vendor'); return }
  if (!form.value.package_id) { toast.error('Please select a plan');   return }
  saving.value = true
  try {
    if (editingItem.value) {
      await $fetch(`${baseURL}/admin/finance/subscriptions/${editingItem.value.id}`, { method: 'PUT', body: form.value, headers: getHeaders() })
      toast.success('Subscription updated')
    } else {
      await $fetch(`${baseURL}/admin/finance/subscriptions`, { method: 'POST', body: form.value, headers: getHeaders() })
      toast.success('Plan assigned to vendor!')
    }
    closeModal()
    await Promise.all([fetchSubs(), fetchStats()])
  } catch (e) {
    toast.error(e.data?.message || 'Failed to save')
  } finally {
    saving.value = false
  }
}

const confirmDelete = (item) => { deleteTarget.value = item }
const deleteItem = async () => {
  saving.value = true
  try {
    await $fetch(`${baseURL}/admin/finance/subscriptions/${deleteTarget.value.id}`, { method: 'DELETE', headers: getHeaders() })
    toast.success('Subscription removed')
    deleteTarget.value = null
    await Promise.all([fetchSubs(), fetchStats()])
  } catch (e) { toast.error('Failed to remove') }
  finally { saving.value = false }
}

let timer = null
const debouncedSearch = () => { clearTimeout(timer); timer = setTimeout(() => { currentPage.value = 1; fetchSubs() }, 400) }
const changePage = (p) => { currentPage.value = p; fetchSubs() }

onMounted(() => {
  fetchSubs()
  fetchStats()
  fetchVendors()
  fetchPlans()
})
</script>
