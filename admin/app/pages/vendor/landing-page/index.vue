<template>
  <div class="flex flex-col h-full bg-[#f8fafc] dark:bg-slate-950 min-h-screen p-10 transition-colors duration-300">
    <!-- Header -->
    <div class="flex items-center mb-8">
      <button @click="$router.back()" class="p-2 mr-4 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
        <ArrowLeft class="w-5 h-5 text-gray-600 dark:text-slate-400" />
      </button>
      <h1 class="text-xl font-bold text-gray-900 dark:text-white">Landing Page</h1>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col items-center justify-center max-w-5xl mx-auto w-full">
      
      <!-- Hero Section -->
      <div class="text-center mb-12">
        <div class="bg-green-50 p-4 rounded-full inline-flex mb-4">
          <LayoutTemplate class="w-8 h-8 text-green-600" />
        </div>
        <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white mb-2">Landing Page</h2>
        <p class="text-gray-500 dark:text-slate-400">Here you will see how to make a landing page in Sellsull</p>
      </div>

      <!-- Cards Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5 w-full mb-12">
        
        <!-- Single Product Landing Page -->
        <NuxtLink to="/vendor/landing-page/create?type=single" class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-xl p-6 text-center shadow-sm hover:shadow-md transition-shadow group flex flex-col items-center h-full">
          <div class="bg-purple-600 p-3 rounded-xl mb-4 group-hover:scale-110 transition-transform">
            <Box class="w-8 h-8 text-white" />
          </div>
          <h3 class="font-bold text-gray-900 dark:text-white mb-2">Single Product</h3>
          <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">Build a landing page for a single product to increase sales.</p>
        </NuxtLink>

        <!-- Multiple Products Landing Page -->
        <div class="relative h-full">
             <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-orange-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full z-10 shadow-sm">PRO</span>
             <NuxtLink to="/vendor/landing-page/create?type=multiple" class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-xl p-6 text-center shadow-sm hover:shadow-md transition-shadow group flex flex-col items-center h-full">
              <div class="bg-orange-500 p-3 rounded-xl mb-4 group-hover:scale-110 transition-transform">
                <Layers class="w-8 h-8 text-white" />
              </div>
              <h3 class="font-bold text-gray-900 dark:text-white mb-2">Multi Product</h3>
              <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">Showcase 2-4 products together on a single landing page.</p>
            </NuxtLink>
        </div>

        <!-- Common Landing Page -->
        <NuxtLink to="/vendor/landing-page/create?type=common" class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-xl p-6 text-center shadow-sm hover:shadow-md transition-shadow group flex flex-col items-center h-full">
          <div class="bg-emerald-600 p-3 rounded-xl mb-4 group-hover:scale-110 transition-transform">
            <Globe class="w-8 h-8 text-white" />
          </div>
          <h3 class="font-bold text-gray-900 dark:text-white mb-2">Common Page</h3>
          <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">Default store page shown when no specific page is chosen.</p>
        </NuxtLink>

        <!-- All Landing Pages -->
        <NuxtLink to="/vendor/landing-page/all" class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-xl p-6 text-center shadow-sm hover:shadow-md transition-shadow group flex flex-col items-center h-full">
          <div class="bg-blue-600 p-3 rounded-xl mb-4 group-hover:scale-110 transition-transform">
            <FileText class="w-8 h-8 text-white" />
          </div>
          <h3 class="font-bold text-gray-900 dark:text-white mb-2">All Landing Pages</h3>
          <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">View and manage all the landing pages you have created.</p>
        </NuxtLink>

        <!-- Landing Page Settings -->
        <NuxtLink to="/vendor/landing-page/settings" class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-xl p-6 text-center shadow-sm hover:shadow-md transition-shadow group flex flex-col items-center h-full">
          <div class="bg-yellow-600 p-3 rounded-xl mb-4 group-hover:scale-110 transition-transform">
            <Settings class="w-8 h-8 text-white" />
          </div>
          <h3 class="font-bold text-gray-900 dark:text-white mb-2">Settings</h3>
          <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">Set delivery charges, footer text and more.</p>
        </NuxtLink>

      </div>

      <!-- Footer Link -->
       <button 
          @click="showVideoModal = true"
          class="flex items-center text-rose-600 hover:text-rose-700 font-bold text-sm transition-all cursor-pointer group hover:scale-105 active:scale-95 bg-white dark:bg-slate-900 px-6 py-3 rounded-full shadow-sm border border-slate-100 dark:border-slate-800"
       >
          <PlayCircle class="w-5 h-5 mr-2 group-hover:fill-rose-50 transition-colors" />
          Watch Tutorial Video
       </button>

    </div>

    <!-- Video Modal -->
    <Transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div v-if="showVideoModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 md:p-10">
        <!-- Backdrop -->
        <div @click="showVideoModal = false" class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm"></div>
        
        <!-- Modal Content -->
        <div class="relative w-full max-w-5xl bg-black rounded-3xl overflow-hidden shadow-2xl border border-slate-800/50 aspect-video group">
          <!-- Close Button -->
          <button 
            @click="showVideoModal = false"
            class="absolute top-4 right-4 z-10 p-2 bg-white/10 hover:bg-white/20 backdrop-blur-md rounded-full text-white transition-all transform hover:rotate-90 group-hover:opacity-100 md:opacity-0"
          >
            <X class="w-6 h-6" />
          </button>

          <!-- Iframe -->
          <iframe 
            class="w-full h-full"
            src="https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1" 
            title="YouTube video player" 
            frameborder="0" 
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
            allowfullscreen
          ></iframe>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { 
  ArrowLeft, 
  LayoutTemplate, 
  Box, 
  Layers, 
  Globe,
  FileText, 
  Settings,
  PlayCircle,
  X
} from 'lucide-vue-next'

const showVideoModal = ref(false)

definePageMeta({
  middleware: 'auth',
  permissions: 'landing_pages.view'
})
</script>
