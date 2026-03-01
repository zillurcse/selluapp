<template>
  <div class="min-h-screen bg-[#f8fafc] dark:bg-slate-950 p-6 lg:p-12 transition-colors duration-300">
    <!-- Header Section -->
    <div class="max-w-7xl mx-auto mb-16 text-center space-y-4">
      <div class="inline-flex items-center justify-center w-16 h-16 bg-[#10b981] rounded-full shadow-lg shadow-emerald-200 mb-2">
        <Truck class="w-8 h-8 text-white" />
      </div>
      <h1 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight">Shipping Management</h1>
      <p class="text-lg text-slate-500 dark:text-slate-400 font-medium max-w-2xl mx-auto leading-relaxed">
        Manage your shipping regions and zones by country, state, and city.
      </p>
    </div>

    <!-- Cards Grid -->
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 text-center">
      <NuxtLink 
        v-for="(card, index) in shippingCards" 
        :key="index"
        :to="card.to"
        class="relative flex flex-col items-center p-10 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[32px] shadow-sm hover:shadow-2xl hover:shadow-emerald-500/10 hover:-translate-y-2 transition-all duration-500 group cursor-pointer"
      >
        <!-- Icon Container -->
        <div :class="[
          'flex-shrink-0 w-20 h-20 rounded-[24px] flex items-center justify-center transition-all duration-700 group-hover:rotate-[10deg] shadow-lg mb-8',
          card.iconBg
        ]">
          <component :is="card.icon" class="w-10 h-10 text-white" />
        </div>

        <!-- Content -->
        <div class="space-y-3">
          <h3 class="text-2xl font-black text-slate-800 dark:text-slate-100 tracking-tight group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
            {{ card.title }}
          </h3>
          <p class="text-sm text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
            {{ card.description }}
          </p>
        </div>

        <!-- Explore Button -->
        <div class="mt-8 px-6 py-2.5 rounded-full bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-sm font-bold group-hover:bg-emerald-500 group-hover:text-white transition-all duration-500 flex items-center gap-2">
          Manage {{ card.title }}
          <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
        </div>

        <!-- Subtle Background Glow -->
        <div :class="['absolute -z-10 inset-0 opacity-0 group-hover:opacity-10 blur-3xl transition-opacity duration-500 rounded-[32px]', card.glowColor]"></div>
      </NuxtLink>
    </div>
  </div>
</template>

<script setup>
import { 
  Truck, 
  Globe, 
  MapPin, 
  Building2,
  ArrowRight
} from 'lucide-vue-next'

const shippingCards = [
  {
    title: 'Countries',
    description: 'Configure and manage shipping availability across different nations.',
    icon: Globe,
    iconBg: 'bg-indigo-500 shadow-indigo-200 dark:shadow-indigo-900/20',
    glowColor: 'bg-indigo-500',
    to: '/vendor/managed-shop/shipping/countries'
  },
  {
    title: 'States',
    description: 'Set up specific shipping rules and zones for individual states or provinces.',
    icon: MapPin,
    iconBg: 'bg-emerald-500 shadow-emerald-200 dark:shadow-emerald-900/20',
    glowColor: 'bg-emerald-500',
    to: '/vendor/managed-shop/shipping/states'
  },
  {
    title: 'Cities',
    description: 'Fine-tune your delivery reach by managing available cities within states.',
    icon: Building2,
    iconBg: 'bg-sky-500 shadow-sky-200 dark:shadow-sky-900/20',
    glowColor: 'bg-sky-500',
    to: '/vendor/managed-shop/shipping/cities'
  }
]

definePageMeta({
  middleware: 'auth',
  permissions: 'settings.view'
})
</script>

<style scoped>
/* Optional: Adding smooth entry animation */
.max-w-7xl {
  animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
