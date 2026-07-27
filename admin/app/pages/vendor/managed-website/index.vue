<template>
  <div class="p-6 sm:p-10 lg:p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <div class="max-w-[1400px] mx-auto mb-10">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-8">
        <NuxtLink 
          to="/vendor/managed-shop" 
          class="w-12 h-12 bg-slate-900 dark:bg-slate-800 rounded-full flex items-center justify-center text-white hover:bg-slate-800 dark:hover:bg-slate-700 transition-all active:scale-95 shadow-lg shadow-slate-900/20 flex-shrink-0"
        >
          <ArrowLeft class="w-6 h-6" />
        </NuxtLink>
        <div class="flex-grow sm:text-center sm:pr-12">
          <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight mb-2">Manage Your Website</h1>
          <p class="text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
            Upload sliders, banners, and configure your storefront appearance
          </p>
        </div>
        <button
          v-if="storeUrl"
          type="button"
          @click="visitStore"
          class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl transition-all active:scale-95 shadow-lg shadow-indigo-200/50 flex-shrink-0"
        >
          <ExternalLink class="w-4 h-4" />
          Visit Store
        </button>
      </div>

      <!-- Quick Stats -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
          <p class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1">Active Sliders</p>
          <p class="text-3xl font-black text-slate-900 dark:text-white">{{ stats.activeSliders }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
          <p class="text-[10px] font-black uppercase tracking-wider text-emerald-500 mb-1">Banners Set</p>
          <p class="text-3xl font-black text-slate-900 dark:text-white">{{ stats.bannersWithImages }}/3</p>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
          <p class="text-[10px] font-black uppercase tracking-wider text-purple-500 mb-1">Avg. Rating</p>
          <p class="text-3xl font-black text-slate-900 dark:text-white">{{ stats.averageRating || '—' }}</p>
        </div>
        <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl p-5 shadow-sm col-span-2 lg:col-span-1">
          <p class="text-[10px] font-black uppercase tracking-wider text-amber-500 mb-1">Pending Reviews</p>
          <p class="text-3xl font-black text-slate-900 dark:text-white">{{ stats.pendingReviews }}</p>
        </div>
      </div>

      <!-- Main Layout Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
        
        <!-- Left Column: Action Cards -->
        <div class="lg:col-span-6 space-y-4 w-full">
          <NuxtLink 
            v-for="(card, index) in actionCards" 
            :key="index"
            :to="card.to"
            class="group block p-6 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[32px] hover:shadow-xl hover:shadow-indigo-500/10 transition-all duration-300 relative overflow-hidden w-full"
          >
            <div class="flex items-center gap-6 relative z-10 w-full">
              <div :class="['w-14 h-14 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-110 duration-300 shadow-sm flex-shrink-0', card.iconBg]">
                <component :is="card.icon" class="w-7 h-7 text-white" />
              </div>

              <div class="flex-grow min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                  <h3 class="text-xl font-black text-slate-800 dark:text-slate-100 tracking-tight group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors truncate">
                    {{ card.title }}
                  </h3>
                  <span v-if="card.isPro" class="px-2.5 py-0.5 bg-orange-500 text-white text-[9px] font-black rounded-lg uppercase tracking-wider">
                    PRO
                  </span>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mt-0.5 leading-snug">
                  {{ card.description }}
                </p>
              </div>

              <div class="flex-shrink-0 ml-auto">
                <ChevronRight class="w-6 h-6 text-slate-300 dark:text-slate-600 group-hover:text-slate-900 dark:group-hover:text-white transition-colors" />
              </div>
            </div>
            
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-transparent to-slate-50 dark:to-slate-800/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
          </NuxtLink>
        </div>

        <!-- Right Column: Website Preview -->
        <div class="lg:col-span-6 w-full">
          <div class="relative p-2 bg-white dark:bg-slate-900 rounded-[48px] shadow-2xl shadow-indigo-100/50 dark:shadow-none overflow-hidden border-[8px] border-indigo-50/50 dark:border-slate-800/50">
            <div class="bg-white dark:bg-slate-900 p-3 rounded-[38px]">
              <button
                type="button"
                class="w-full aspect-[4/3] rounded-[32px] bg-slate-100 dark:bg-slate-800 overflow-hidden border border-slate-100 dark:border-slate-700 relative group cursor-pointer shadow-inner text-left"
                :disabled="!storeUrl"
                @click="visitStore"
              >
                <div class="w-full h-full bg-[#fcfcfc] dark:bg-slate-800 flex items-center justify-center">
                  <div class="w-full h-full p-6 space-y-4">
                    <div class="h-8 w-1/3 bg-slate-200 dark:bg-slate-700 rounded-lg"></div>
                    <div class="h-40 w-full bg-slate-200 dark:bg-slate-700 rounded-2xl overflow-hidden">
                      <img
                        v-if="previewSliderUrl"
                        :src="previewSliderUrl"
                        alt="Slider preview"
                        class="w-full h-full object-cover"
                      />
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                      <div
                        v-for="(bannerUrl, i) in previewBannerUrls"
                        :key="i"
                        class="h-24 bg-slate-200 dark:bg-slate-700 rounded-xl overflow-hidden"
                      >
                        <img v-if="bannerUrl" :src="bannerUrl" alt="" class="w-full h-full object-cover" />
                      </div>
                    </div>
                  </div>
                </div>
                <div
                  v-if="storeUrl"
                  class="absolute inset-0 bg-slate-900/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm"
                >
                  <span class="text-white text-sm font-black uppercase tracking-widest bg-black/40 px-5 py-2.5 rounded-full flex items-center gap-2">
                    <ExternalLink class="w-4 h-4" />
                    Open Live Store
                  </span>
                </div>
              </button>

              <div class="text-center py-10 px-6">
                <h2 class="text-3xl font-black text-slate-900 dark:text-white mb-3">Your storefront</h2>
                <p class="text-slate-500 dark:text-slate-400 font-medium mb-2 max-w-sm mx-auto leading-relaxed">
                  Preview reflects your current sliders and banners when configured.
                </p>
                <p v-if="storeUrl" class="text-xs font-bold text-indigo-500 dark:text-indigo-400 mb-8 truncate max-w-md mx-auto">{{ storeUrl }}</p>
                <NuxtLink
                  to="/vendor/landing-page"
                  class="inline-block px-10 py-4 bg-indigo-100/50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-600 dark:hover:text-white font-black rounded-2xl transition-all active:scale-95 shadow-sm shadow-indigo-100"
                >
                  Manage Landing Pages
                </NuxtLink>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  ArrowLeft, 
  ArrowRight, 
  Play, 
  Equal, 
  Globe, 
  Star, 
  ChevronRight,
  ExternalLink
} from 'lucide-vue-next'
import { ref, reactive, onMounted } from 'vue'

definePageMeta({
  middleware: 'auth',
  permissions: 'website.view'
})

const { getAll } = useCrud()
const { resolveStoreBaseUrl } = useStorefrontUrl()

const storeUrl = ref('')
const previewSliderUrl = ref(null)
const previewBannerUrls = ref([null, null, null])

const stats = reactive({
  activeSliders: 0,
  bannersWithImages: 0,
  averageRating: 0,
  pendingReviews: 0,
})

const actionCards = [
  {
    title: 'Update Website Slider',
    description: 'Update the website\'s main slider for better UX.',
    icon: ArrowRight,
    iconBg: 'bg-blue-500',
    to: '/vendor/managed-website/sliders'
  },
  {
    title: 'Add 3 Small Banners',
    description: 'Add 3 small banners on the homepage for promotions.',
    icon: Play,
    iconBg: 'bg-emerald-500',
    to: '/vendor/managed-website/banners'
  },
  {
    title: 'Sort Categories',
    description: 'Organize the categories to enhance user navigation.',
    icon: Equal,
    iconBg: 'bg-amber-500',
    to: '/vendor/attributes/sorting'
  },
  {
    title: 'Webpage Setting',
    description: 'Configure SEO, lookbook, newsletter, and announcement bar.',
    icon: Globe,
    iconBg: 'bg-rose-500',
    to: '/vendor/managed-website/settings'
  },
  {
    title: 'Customer Review Setting',
    description: 'Manage how customer reviews work on your website.',
    icon: Star,
    iconBg: 'bg-purple-500',
    isPro: true,
    to: '/vendor/managed-website/reviews'
  }
]

const visitStore = () => {
  if (storeUrl.value) {
    window.open(storeUrl.value, '_blank', 'noopener,noreferrer')
  }
}

const loadOverview = async () => {
  try {
    const [slidersRes, bannersRes, reviewsRes] = await Promise.all([
      getAll('/vendor/settings?group=website_sliders').catch(() => null),
      getAll('/vendor/settings?group=website_banners').catch(() => null),
      getAll('/vendor/reviews/stats').catch(() => null),
    ])

    if (slidersRes?.data?.sliders_meta) {
      const meta = typeof slidersRes.data.sliders_meta === 'string'
        ? JSON.parse(slidersRes.data.sliders_meta)
        : slidersRes.data.sliders_meta

      stats.activeSliders = meta.filter(s => s.active !== false).length

      const firstActive = meta.find(s => s.active !== false && slidersRes.data[s.id])
      if (firstActive) {
        previewSliderUrl.value = slidersRes.data[firstActive.id]
      }
    }

    if (bannersRes?.data?.banners_meta) {
      const meta = typeof bannersRes.data.banners_meta === 'string'
        ? JSON.parse(bannersRes.data.banners_meta)
        : bannersRes.data.banners_meta

      const bannerIds = ['banner_1', 'banner_2', 'banner_3']
      previewBannerUrls.value = bannerIds.map((id, i) => bannersRes.data[id] || null)
      stats.bannersWithImages = bannerIds.filter(id => bannersRes.data[id]).length
    }

    if (reviewsRes?.data) {
      stats.averageRating = reviewsRes.data.average_rating
      stats.pendingReviews = reviewsRes.data.pending_reviews
    }
  } catch {
    // Non-blocking overview load
  }
}

onMounted(async () => {
  storeUrl.value = await resolveStoreBaseUrl()
  await loadOverview()
})
</script>

<style scoped>
.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>
