<template>
  <div class="flex flex-col h-full bg-gray-50/50 dark:bg-slate-950 min-h-screen transition-colors duration-300 p-4 sm:p-6 lg:p-10">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-4 sm:px-8 py-6 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 transition-colors duration-300 rounded-2xl mb-6 sm:mb-8 shadow-sm">
      <div class="flex items-center">
        <NuxtLink to="/vendor/landing-page" class="p-2 bg-black text-white rounded-full hover:bg-gray-800 transition-colors mr-4 sm:mr-6 shadow-sm">
          <ArrowLeft class="w-5 h-5" />
        </NuxtLink>
        <div>
          <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">All Landing Pages</h1>
          <p class="text-gray-500 dark:text-slate-400 mt-1 text-sm">View, preview, and manage every page in your campaign library.</p>
        </div>
      </div>
      <NuxtLink to="/vendor/landing-page/create" class="bg-black dark:bg-slate-800 text-white px-6 py-3 rounded-xl font-bold hover:bg-gray-800 transition-all flex items-center justify-center shadow-lg active:scale-95">
        <Plus class="w-5 h-5 mr-2" />
        Create New
      </NuxtLink>
    </div>

    <div v-if="loading" class="flex flex-col items-center justify-center py-24 gap-3">
      <Loader2 class="w-8 h-8 animate-spin text-gray-400" />
      <p class="text-sm text-gray-500">Loading landing pages...</p>
    </div>

    <!-- Table -->
    <div v-else class="max-w-7xl mx-auto w-full">
      <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden transition-colors duration-300">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
              <th class="px-6 py-5 text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Page</th>
              <th class="px-6 py-5 text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Type</th>
              <th class="px-6 py-5 text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Template</th>
              <th class="px-6 py-5 text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Schedule</th>
              <th class="px-6 py-5 text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Status</th>
              <th class="px-6 py-5 text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50 dark:divide-slate-800">
            <tr v-for="page in landingPages" :key="page.id" class="hover:bg-gray-50/50 dark:hover:bg-slate-800 transition-colors group">

              <!-- Page Info -->
              <td class="px-6 py-5">
                <div class="flex items-center">
                  <div class="w-12 h-12 rounded-lg flex items-center justify-center mr-4 transition-colors border"
                    :class="typeStyle(page.landing_page_type).bg + ' border-transparent'"
                  >
                    <component :is="typeStyle(page.landing_page_type).icon" class="w-6 h-6" :class="typeStyle(page.landing_page_type).color" />
                  </div>
                  <div>
                    <div class="flex items-center gap-2">
                      <div class="font-bold text-gray-900 dark:text-slate-100">{{ page.title }}</div>
                      <span v-if="page.is_home" class="px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 text-[10px] font-black rounded-full uppercase tracking-tighter flex items-center shadow-sm border border-indigo-200 dark:border-indigo-800">
                        <Home class="w-2.5 h-2.5 mr-1" /> Home
                      </span>
                    </div>
                    <div class="text-xs text-gray-400 dark:text-slate-500 mt-1 flex items-center">
                      <Link2 class="w-3 h-3 mr-1" />
                      /l/{{ page.slug }}
                    </div>
                  </div>
                </div>
              </td>

              <!-- Type badge -->
              <td class="px-6 py-5">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full uppercase tracking-wide"
                  :class="typeStyle(page.landing_page_type).badge"
                >
                  <component :is="typeStyle(page.landing_page_type).icon" class="w-3.5 h-3.5" />
                  {{ page.landing_page_type }}
                </span>
              </td>

              <!-- Template -->
              <td class="px-6 py-5">
                <span class="inline-flex items-center px-3 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-bold rounded-lg uppercase tracking-wide">
                  {{ page.template_name }}
                </span>
              </td>

              <!-- Schedule -->
              <td class="px-6 py-5">
                <div v-if="page.campaign_start_at || page.campaign_end_at" class="space-y-0.5">
                  <div v-if="page.campaign_start_at" class="flex items-center gap-1 text-xs text-gray-500 dark:text-slate-400">
                    <Calendar class="w-3 h-3 text-green-500" />
                    {{ formatDate(page.campaign_start_at) }}
                  </div>
                  <div v-if="page.campaign_end_at" class="flex items-center gap-1 text-xs text-gray-500 dark:text-slate-400">
                    <Calendar class="w-3 h-3 text-red-500" />
                    {{ formatDate(page.campaign_end_at) }}
                  </div>
                  <!-- Countdown if active -->
                  <div v-if="page.campaign_end_at && isActive(page.campaign_end_at)" class="flex items-center gap-1 text-[10px] font-bold text-amber-600 dark:text-amber-400">
                    <Clock class="w-3 h-3" />
                    {{ countdown(page.campaign_end_at) }}
                  </div>
                </div>
                <span v-else class="text-xs text-gray-300 dark:text-slate-600">Always on</span>
              </td>

              <!-- Status Toggle -->
              <td class="px-6 py-5">
                <button
                  @click="handleToggleStatus(page)"
                  :disabled="actionId === page.id"
                  class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold transition-all active:scale-95 disabled:opacity-50"
                  :class="page.status === 'active' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 hover:bg-green-200' : 'bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400 hover:bg-gray-200'"
                >
                  <span class="w-1.5 h-1.5 rounded-full mr-2" :class="page.status === 'active' ? 'bg-green-500' : 'bg-gray-400'"></span>
                  {{ page.status === 'active' ? 'Active' : 'Draft' }}
                </button>
              </td>

              <!-- Actions -->
              <td class="px-6 py-5 text-right">
                <div class="flex items-center justify-end space-x-1">
                  <button @click="openPreview(page)" :disabled="actionId === page.id" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Preview on storefront">
                    <Eye class="w-4 h-4" />
                  </button>
                  <NuxtLink :to="`/vendor/landing-page/create?id=${page.id}`" class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="Edit">
                    <Pencil class="w-4 h-4" />
                  </NuxtLink>
                  <button @click="copyLink(page)" :disabled="actionId === page.id" class="p-2 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition-all" title="Copy storefront link">
                    <Copy class="w-4 h-4" />
                  </button>
                  <button @click="handleSetHome(page)" :disabled="actionId === page.id" :class="page.is_home ? 'text-indigo-600 bg-indigo-50' : 'text-gray-400 hover:text-indigo-600 hover:bg-indigo-50'" class="p-2 rounded-lg transition-all" title="Set as Home">
                    <Home class="w-4 h-4" />
                  </button>
                  <button @click="handleDelete(page)" :disabled="actionId === page.id" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Empty State -->
        <div v-if="landingPages.length === 0" class="flex flex-col items-center justify-center py-20 bg-white dark:bg-slate-900">
          <div class="bg-gray-50 dark:bg-slate-800 p-6 rounded-full mb-6">
            <LayoutTemplate class="w-12 h-12 text-gray-300 dark:text-slate-600" />
          </div>
          <h3 class="text-xl font-bold text-gray-900 dark:text-white">No landing pages yet</h3>
          <p class="text-gray-500 dark:text-slate-400 mt-2 mb-8">Create your first landing page to start your campaign.</p>
          <NuxtLink to="/vendor/landing-page/create" class="bg-black dark:bg-slate-800 text-white px-8 py-3 rounded-xl font-bold hover:bg-gray-800 transition-all flex items-center shadow-lg active:scale-95">
            <Plus class="w-5 h-5 mr-2" />
            Create Your First Page
          </NuxtLink>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {
  ArrowLeft, Plus, LayoutTemplate, Eye, Pencil, Copy, Trash2,
  Link2, Home, Box, Layers, Globe, Calendar, Clock, Loader2
} from 'lucide-vue-next'
import { toast } from 'vue-sonner'

definePageMeta({
  layout: 'default',
  middleware: 'auth',
  permissions: 'landing_pages.view'
})

const { getAll, deleteItem, updateItem } = useCrud()
const { buildLandingPageUrl, copyLandingPageLink } = useStorefrontUrl()

const landingPages = ref([])
const loading = ref(true)
const actionId = ref(null)

const fetchLandingPages = async () => {
  loading.value = true
  try {
    const res = await getAll('/vendor/landing-pages')
    landingPages.value = Array.isArray(res) ? res : (res?.data || [])
  } catch (e) {
    console.error('Failed to fetch landing pages:', e)
    toast.error('Could not load landing pages.')
  } finally {
    loading.value = false
  }
}

onMounted(() => fetchLandingPages())

// ---- Type styles ----
const typeStyle = (type) => {
  const map = {
    single:   { bg: 'bg-purple-50 dark:bg-purple-900/20', color: 'text-purple-600', icon: Box,    badge: 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400' },
    multiple: { bg: 'bg-orange-50 dark:bg-orange-900/20', color: 'text-orange-500', icon: Layers,  badge: 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400' },
    common:   { bg: 'bg-emerald-50 dark:bg-emerald-900/20', color: 'text-emerald-600', icon: Globe, badge: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400' },
  }
  return map[type] || map.single
}

// ---- Schedule helpers ----
const formatDate = (dt) => {
  if (!dt) return ''
  return new Date(dt).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}
const isActive = (endDate) => new Date(endDate) > new Date()
const countdown = (endDate) => {
  const diff = new Date(endDate) - new Date()
  if (diff <= 0) return 'Ended'
  const d = Math.floor(diff / 86400000)
  const h = Math.floor((diff % 86400000) / 3600000)
  const m = Math.floor((diff % 3600000) / 60000)
  return `${d}d ${h}h ${m}m left`
}

// ---- Actions ----
const openPreview = async (page) => {
  try {
    const url = await buildLandingPageUrl(page.slug)
    window.open(url, '_blank', 'noopener,noreferrer')
  } catch {
    toast.error('Could not open preview link.')
  }
}

const copyLink = async (page) => {
  try {
    await copyLandingPageLink(page.slug)
    toast.success('Storefront link copied!')
  } catch {
    toast.error('Could not copy link.')
  }
}

const handleToggleStatus = async (page) => {
  actionId.value = page.id
  const newStatus = page.status === 'active' ? 'draft' : 'active'
  try {
    await updateItem(`/vendor/landing-pages/${page.id}`, { status: newStatus })
    page.status = newStatus
    toast.success(newStatus === 'active' ? 'Page is now live.' : 'Page moved to draft.')
  } catch (e) {
    console.error(e)
  } finally {
    actionId.value = null
  }
}

const handleSetHome = async (page) => {
  actionId.value = page.id
  const newHomeStatus = !page.is_home
  try {
    await updateItem(`/vendor/landing-pages/${page.id}`, { is_home: newHomeStatus })
    await fetchLandingPages()
    toast.success(newHomeStatus ? 'Set as store home page!' : 'Home status removed.')
  } catch (e) {
    console.error(e)
  } finally {
    actionId.value = null
  }
}

const handleDelete = async (page) => {
  if (!confirm(`Delete "${page.title}"? This cannot be undone.`)) return
  actionId.value = page.id
  try {
    await deleteItem(page.id, '/vendor/landing-pages')
    await fetchLandingPages()
  } catch (e) {
    console.error(e)
  } finally {
    actionId.value = null
  }
}
</script>
