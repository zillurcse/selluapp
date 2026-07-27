<template>
  <div class="flex flex-col h-full bg-[#f8fafc] dark:bg-slate-950 min-h-screen p-4 sm:p-6 lg:p-10 transition-colors duration-300">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6 sm:mb-8 max-w-6xl mx-auto w-full">
      <div class="flex items-center">
        <NuxtLink to="/vendor" class="p-2 mr-3 sm:mr-4 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
          <ArrowLeft class="w-5 h-5 text-gray-600 dark:text-slate-400" />
        </NuxtLink>
        <div>
          <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Landing Pages</h1>
          <p class="text-sm text-gray-500 dark:text-slate-400 hidden sm:block">Build high-converting product and store pages</p>
        </div>
      </div>
      <NuxtLink
        to="/vendor/landing-page/create"
        class="hidden sm:inline-flex items-center gap-2 bg-black dark:bg-white text-white dark:text-black px-5 py-2.5 rounded-xl font-bold text-sm hover:opacity-90 transition-all shadow-sm"
      >
        <Plus class="w-4 h-4" />
        New Page
      </NuxtLink>
    </div>

    <!-- Stats -->
    <div class="max-w-6xl mx-auto w-full grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
      <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Total Pages</p>
        <p class="text-3xl font-black text-gray-900 dark:text-white">{{ stats.total }}</p>
      </div>
      <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-wider text-green-500 mb-1">Live</p>
        <p class="text-3xl font-black text-gray-900 dark:text-white">{{ stats.active }}</p>
      </div>
      <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Drafts</p>
        <p class="text-3xl font-black text-gray-900 dark:text-white">{{ stats.draft }}</p>
      </div>
      <div class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-5 shadow-sm col-span-2 lg:col-span-1">
        <p class="text-xs font-bold uppercase tracking-wider text-indigo-500 mb-1">Home Page</p>
        <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ stats.homeTitle || 'Not set' }}</p>
      </div>
    </div>

    <div class="flex-1 max-w-6xl mx-auto w-full">
      <!-- Hero -->
      <div class="text-center mb-8 sm:mb-10">
        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-full inline-flex mb-4">
          <LayoutTemplate class="w-8 h-8 text-green-600 dark:text-green-400" />
        </div>
        <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white mb-2">What would you like to build?</h2>
        <p class="text-gray-500 dark:text-slate-400 max-w-xl mx-auto">Choose a page type to start your guided setup — template, products, content, and schedule.</p>
      </div>

      <!-- Cards Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5 w-full mb-10">
        <NuxtLink to="/vendor/landing-page/create?type=single" class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-purple-200 dark:hover:border-purple-800 transition-all group flex gap-4 items-start">
          <div class="bg-purple-600 p-3 rounded-xl group-hover:scale-110 transition-transform flex-shrink-0">
            <Box class="w-7 h-7 text-white" />
          </div>
          <div>
            <h3 class="font-bold text-gray-900 dark:text-white mb-1">Single Product</h3>
            <p class="text-sm text-gray-500 dark:text-slate-400 leading-relaxed">Focused hero page for one product. Best for ads and direct campaigns.</p>
          </div>
        </NuxtLink>

        <div class="relative">
          <span class="absolute -top-2.5 right-4 bg-orange-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full z-10 shadow-sm">PRO</span>
          <NuxtLink to="/vendor/landing-page/create?type=multiple" class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-orange-200 dark:hover:border-orange-800 transition-all group flex gap-4 items-start h-full">
            <div class="bg-orange-500 p-3 rounded-xl group-hover:scale-110 transition-transform flex-shrink-0">
              <Layers class="w-7 h-7 text-white" />
            </div>
            <div>
              <h3 class="font-bold text-gray-900 dark:text-white mb-1">Multi Product</h3>
              <p class="text-sm text-gray-500 dark:text-slate-400 leading-relaxed">Showcase 2–4 products together. Great for bundles and combos.</p>
            </div>
          </NuxtLink>
        </div>

        <NuxtLink to="/vendor/landing-page/create?type=common" class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-emerald-200 dark:hover:border-emerald-800 transition-all group flex gap-4 items-start">
          <div class="bg-emerald-600 p-3 rounded-xl group-hover:scale-110 transition-transform flex-shrink-0">
            <Globe class="w-7 h-7 text-white" />
          </div>
          <div>
            <h3 class="font-bold text-gray-900 dark:text-white mb-1">Common Store Page</h3>
            <p class="text-sm text-gray-500 dark:text-slate-400 leading-relaxed">A flexible storefront-style page for your full catalog.</p>
          </div>
        </NuxtLink>

        <NuxtLink to="/vendor/landing-page/all" class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-blue-200 dark:hover:border-blue-800 transition-all group flex gap-4 items-start">
          <div class="bg-blue-600 p-3 rounded-xl group-hover:scale-110 transition-transform flex-shrink-0">
            <FileText class="w-7 h-7 text-white" />
          </div>
          <div>
            <h3 class="font-bold text-gray-900 dark:text-white mb-1">Manage All Pages</h3>
            <p class="text-sm text-gray-500 dark:text-slate-400 leading-relaxed">Preview, edit, copy links, and toggle status for every page.</p>
          </div>
        </NuxtLink>

        <NuxtLink to="/vendor/landing-page/settings" class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-yellow-200 dark:hover:border-yellow-800 transition-all group flex gap-4 items-start sm:col-span-2 xl:col-span-2">
          <div class="bg-yellow-600 p-3 rounded-xl group-hover:scale-110 transition-transform flex-shrink-0">
            <Settings class="w-7 h-7 text-white" />
          </div>
          <div>
            <h3 class="font-bold text-gray-900 dark:text-white mb-1">Landing Page Settings</h3>
            <p class="text-sm text-gray-500 dark:text-slate-400 leading-relaxed">Configure delivery charges, footer text, and landing-page-specific checkout options.</p>
          </div>
        </NuxtLink>
      </div>

      <!-- Recent pages -->
      <div v-if="recentPages.length" class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl p-6 shadow-sm mb-8">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-bold text-gray-900 dark:text-white">Recent Pages</h3>
          <NuxtLink to="/vendor/landing-page/all" class="text-sm font-bold text-blue-600 hover:text-blue-700">View all</NuxtLink>
        </div>
        <div class="space-y-3">
          <div v-for="page in recentPages" :key="page.id" class="flex items-center justify-between gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors">
            <div class="min-w-0">
              <p class="font-semibold text-gray-900 dark:text-white truncate">{{ page.title }}</p>
              <p class="text-xs text-gray-400">/l/{{ page.slug }}</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
              <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full" :class="page.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'">{{ page.status }}</span>
              <NuxtLink :to="`/vendor/landing-page/create?id=${page.id}`" class="text-xs font-bold text-gray-500 hover:text-black dark:hover:text-white">Edit</NuxtLink>
            </div>
          </div>
        </div>
      </div>

      <div class="flex justify-center">
        <button
          @click="showVideoModal = true"
          class="flex items-center text-rose-600 hover:text-rose-700 font-bold text-sm transition-all cursor-pointer group hover:scale-105 active:scale-95 bg-white dark:bg-slate-900 px-6 py-3 rounded-full shadow-sm border border-slate-100 dark:border-slate-800"
        >
          <PlayCircle class="w-5 h-5 mr-2" />
          How Landing Pages Work
        </button>
      </div>
    </div>

    <!-- Help Modal -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div v-if="showVideoModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 md:p-10">
        <div @click="showVideoModal = false" class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm"></div>
        <div class="relative w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-800 p-8">
          <button @click="showVideoModal = false" class="absolute top-4 right-4 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-slate-800 transition-colors">
            <X class="w-5 h-5 text-gray-500" />
          </button>
          <div class="text-center">
            <div class="w-16 h-16 bg-rose-50 dark:bg-rose-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
              <PlayCircle class="w-8 h-8 text-rose-600" />
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Quick Start Guide</h3>
            <ol class="text-left text-sm text-gray-600 dark:text-slate-400 space-y-3 mb-6">
              <li><strong>1.</strong> Pick a page type — single product, bundle, or full store.</li>
              <li><strong>2.</strong> Choose a template and select your products.</li>
              <li><strong>3.</strong> Add your headline, features, and CTA text.</li>
              <li><strong>4.</strong> Set schedule, publish, then copy your storefront link.</li>
            </ol>
            <NuxtLink to="/vendor/landing-page/create" @click="showVideoModal = false" class="inline-flex items-center gap-2 bg-black dark:bg-white text-white dark:text-black px-6 py-3 rounded-xl font-bold text-sm">
              Start Creating <ArrowRight class="w-4 h-4" />
            </NuxtLink>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import {
  ArrowLeft, ArrowRight, LayoutTemplate, Box, Layers, Globe,
  FileText, Settings, PlayCircle, X, Plus
} from 'lucide-vue-next'

definePageMeta({
  middleware: 'auth',
  permissions: 'landing_pages.view'
})

const { getAll } = useCrud()
const showVideoModal = ref(false)
const pages = ref([])

const stats = computed(() => {
  const list = pages.value
  const home = list.find(p => p.is_home)
  return {
    total: list.length,
    active: list.filter(p => p.status === 'active').length,
    draft: list.filter(p => p.status !== 'active').length,
    homeTitle: home?.title || null,
  }
})

const recentPages = computed(() => pages.value.slice(0, 4))

onMounted(async () => {
  try {
    const res = await getAll('/vendor/landing-pages')
    pages.value = Array.isArray(res) ? res : (res?.data || [])
  } catch (e) {
    console.error('Failed to load landing page stats:', e)
  }
})
</script>
