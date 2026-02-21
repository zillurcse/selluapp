<template>
  <div class="p-10 lg:p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <!-- Breadcrumb-style Header (Optional, since layout has it, but matching reference) -->
    <div class="max-w-[1400px] mx-auto mb-10">
      <div class="flex items-center gap-4 mb-12">
        <NuxtLink 
          to="/vendor/managed-shop" 
          class="w-12 h-12 bg-slate-900 rounded-full flex items-center justify-center text-white hover:bg-slate-800 transition-all active:scale-95 shadow-lg shadow-slate-900/20 flex-shrink-0"
        >
          <ArrowLeft class="w-6 h-6" />
        </NuxtLink>
        <div class="flex-grow text-center pr-12">
          <h1 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight mb-2">Manage Your Website</h1>
          <p class="text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
            Here you can manage your website by uploading sliders, banners and many more
          </p>
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
              <!-- Icon box -->
              <div :class="['w-14 h-14 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-110 duration-300 shadow-sm flex-shrink-0', card.iconBg]">
                <component :is="card.icon" class="w-7 h-7 text-white" />
              </div>

              <!-- Text content -->
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

              <!-- Chevron -->
              <div class="flex-shrink-0 ml-auto">
                <ChevronRight class="w-6 h-6 text-slate-300 dark:text-slate-600 group-hover:text-slate-900 dark:group-hover:text-white transition-colors" />
              </div>
            </div>
            
            <!-- Hover Gradient Background -->
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-transparent to-slate-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
          </NuxtLink>
        </div>

        <!-- Right Column: Website Preview -->
        <div class="lg:col-span-6 w-full">
          <div class="relative p-2 bg-white dark:bg-slate-900 rounded-[48px] shadow-2xl shadow-indigo-100/50 overflow-hidden border-[8px] border-indigo-50/50 dark:border-slate-800/50">
            <div class="bg-white dark:bg-slate-900 p-3 rounded-[38px]">
              <!-- Website Preview Mockup -->
              <div class="aspect-[4/3] rounded-[32px] bg-slate-100 overflow-hidden border border-slate-100 relative group cursor-pointer shadow-inner">
                 <div class="w-full h-full bg-[#fcfcfc] flex items-center justify-center">
                    <!-- Placeholder that looks like a webpage -->
                    <div class="w-full h-full p-6 space-y-4">
                       <div class="h-8 w-1/3 bg-slate-200 rounded-lg"></div>
                       <div class="h-40 w-full bg-slate-200 rounded-2xl"></div>
                       <div class="grid grid-cols-3 gap-3">
                          <div class="h-24 bg-slate-200 rounded-xl"></div>
                          <div class="h-24 bg-slate-200 rounded-xl"></div>
                          <div class="h-24 bg-slate-200 rounded-xl"></div>
                       </div>
                    </div>
                 </div>
                 <!-- Overlay image matching the idea -->
                 <img 
                  src="/shop_template/template_1/preview.png" 
                  alt="Website Preview" 
                  class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                  @error="(e) => e.target.style.display='none'"
                />
              </div>

              <!-- Preview Footer -->
              <div class="text-center py-10 px-6">
                <h2 class="text-3xl font-black text-slate-900 dark:text-white mb-3">Your current website theme</h2>
                <p class="text-slate-500 dark:text-slate-400 font-medium mb-8 max-w-sm mx-auto leading-relaxed">Modern and trendy design, that matches your brand and identity.</p>
                <button class="px-10 py-4 bg-indigo-100/50 text-indigo-600 hover:bg-indigo-600 hover:text-white font-black rounded-2xl transition-all active:scale-95 shadow-sm shadow-indigo-100">
                  Change template
                </button>
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
  ChevronRight
} from 'lucide-vue-next'

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
    description: 'Here you will see how to set up your webpage in Sellsull',
    icon: Globe,
    iconBg: 'bg-rose-500',
    to: '/vendor/managed-website/settings'
  },
  {
    title: 'Customer Review Setting',
    description: 'Here you will see how to manage your customer reviews in your website',
    icon: Star,
    iconBg: 'bg-purple-500',
    isPro: true,
    to: '/vendor/managed-website/reviews'
  }
]
</script>

<style scoped>
/* Added focus for robustness */
.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>
