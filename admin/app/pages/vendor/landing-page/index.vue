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
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 w-full mb-12">
        
        <!-- Single Product Landing Page -->
        <NuxtLink to="/vendor/landing-page/single" class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-xl p-6 text-center shadow-sm hover:shadow-md transition-shadow group flex flex-col items-center h-full">
          <div class="bg-purple-600 p-3 rounded-xl mb-4 group-hover:scale-110 transition-transform">
            <Box class="w-8 h-8 text-white" />
          </div>
          <h3 class="font-bold text-gray-900 dark:text-white mb-2">Single Product Landing Page</h3>
          <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">Build a landing page for a single product to increase sales.</p>
        </NuxtLink>

        <!-- Multiple Products Landing Page -->
        <div class="relative h-full">
             <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-orange-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full z-10 shadow-sm">PRO</span>
             <NuxtLink to="/vendor/landing-page/multiple" class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-xl p-6 text-center shadow-sm hover:shadow-md transition-shadow group flex flex-col items-center h-full">
              <div class="bg-blue-600 p-3 rounded-xl mb-4 group-hover:scale-110 transition-transform">
                <Layers class="w-8 h-8 text-white" />
              </div>
              <h3 class="font-bold text-gray-900 dark:text-white mb-2">Multiple Products Landing Page</h3>
              <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">Showcase 3-4 products together on a single landing page.</p>
            </NuxtLink>
        </div>

        <!-- All Landing Pages -->
        <NuxtLink to="/vendor/landing-page/all" class="bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-xl p-6 text-center shadow-sm hover:shadow-md transition-shadow group flex flex-col items-center h-full">
          <div class="bg-green-600 p-3 rounded-xl mb-4 group-hover:scale-110 transition-transform">
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
          <h3 class="font-bold text-gray-900 dark:text-white mb-2">Landing Page Settings</h3>
          <p class="text-xs text-gray-500 dark:text-slate-400 leading-relaxed">From this page you can set landing page delivery charge, footer text etc.</p>
        </NuxtLink>

      </div>

      <!-- Footer Link -->
       <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ" target="_blank" class="flex items-center text-red-600 hover:text-red-700 font-medium text-sm transition-colors cursor-pointer">
          <PlayCircle class="w-4 h-4 mr-2" />
          Watch Tutorial Video
       </a>

    </div>
  </div>
</template>

<script setup>
import { 
  ArrowLeft, 
  LayoutTemplate, 
  Box, 
  Layers, 
  FileText, 
  Settings,
  PlayCircle 
} from 'lucide-vue-next'

definePageMeta({
  layout: 'default'
})
</script>
