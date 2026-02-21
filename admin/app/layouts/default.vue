<template>
  <div class="flex min-h-screen bg-[#f8fafc] dark:bg-slate-950 font-sans text-slate-900 dark:text-slate-50 selection:bg-indigo-100 selection:text-indigo-700 dark:selection:bg-indigo-900 dark:selection:text-indigo-100 transition-colors duration-300">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div 
      v-if="sidebar.isOpen.value" 
      @click="sidebar.close()" 
      class="fixed inset-0 bg-slate-900/50 dark:bg-slate-950/80 backdrop-blur-sm z-40 lg:hidden transition-opacity"
    ></div>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col lg:ml-80 transition-all duration-500 w-full overflow-hidden">
      <!-- Header -->
      <Header />

      <!-- Page Content -->
      <main class="flex-1 overflow-y-auto">
        <slot />
      </main>
    </div>
    <ClientOnly>
      <Toaster position="top-right" richColors closeButton />
    </ClientOnly>
  </div>
</template>

<script setup>
import Sidebar from '~/components/Sidebar.vue'
import Header from '~/components/Header.vue'

const auth = useAuthStore()
const sidebar = useSidebar()
</script>

