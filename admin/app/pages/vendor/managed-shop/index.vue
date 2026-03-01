<template>
  <div class="min-h-screen bg-[#f8fafc] dark:bg-slate-950 p-6 lg:p-12 transition-colors duration-300">
    <!-- Header Section -->
    <div class="max-w-7xl mx-auto mb-16 text-center space-y-4">
      <div class="inline-flex items-center justify-center w-16 h-16 bg-[#10b981] rounded-full shadow-lg shadow-emerald-200 mb-2">
        <Settings2 class="w-8 h-8 text-white" />
      </div>
      <h1 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight">Manage Shop</h1>
      <p class="text-lg text-slate-500 dark:text-slate-400 font-medium max-w-2xl mx-auto leading-relaxed">
        Easily set up and customize all your shop's settings from one place — manage domains, policies, payments, and more.
      </p>
    </div>

    <!-- Cards Grid -->
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <NuxtLink 
        v-for="(card, index) in managementCards" 
        :key="index"
        :to="card.to"
        :class="[
          'relative p-8 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-[32px] shadow-sm hover:shadow-2xl hover:shadow-indigo-500/10 hover:-translate-y-1 transition-all duration-500 group cursor-pointer block',
          card.isPro ? 'ring-2 ring-orange-100' : ''
        ]"
      >
        <div class="flex items-start gap-6">
          <!-- Icon Container -->
          <div :class="[
            'flex-shrink-0 w-14 h-14 rounded-2xl flex items-center justify-center transition-all duration-500 group-hover:scale-110 shadow-sm',
            card.iconBg
          ]">
            <component :is="card.icon" class="w-7 h-7 text-white" />
          </div>

          <!-- Content -->
          <div class="flex-grow space-y-1">
            <div class="flex items-center justify-between">
              <h3 class="text-xl font-black text-slate-800 dark:text-slate-100 tracking-tight group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                {{ card.title }}
              </h3>
              <span v-if="card.isPro" class="px-3 py-1 bg-orange-500 text-white text-[10px] font-black rounded-full uppercase tracking-wider">
                PRO
              </span>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
              {{ card.description }}
            </p>
          </div>
        </div>

        <!-- Hover Indicator -->
        <div class="absolute bottom-4 right-8 opacity-0 group-hover:opacity-100 transition-all duration-500 translate-x-4 group-hover:translate-x-0">
          <ArrowRight class="w-5 h-5 text-indigo-500" />
        </div>
      </NuxtLink>
    </div>
  </div>
</template>

<script setup>
import { 
  Settings2, 
  Settings, 
  Globe, 
  FileText, 
  Truck, 
  MapPin, 
  CreditCard, 
  Share2, 
  Target, 
  MessageSquare, 
  Cpu, 
  ShieldAlert, 
  Bug,
  ArrowRight
} from 'lucide-vue-next'

const managementCards = [
  {
    title: 'Shop Settings',
    description: 'Customize your shop\'s core settings for a seamless experience.',
    icon: Settings,
    iconBg: 'bg-indigo-500',
    to: '/vendor/managed-shop/shop-settings'
  },
  {
    title: 'Shop Domain',
    description: 'Manage domain setup and general shop settings.',
    icon: Globe,
    iconBg: 'bg-blue-600',
    to: '/vendor/managed-shop/shop-domain'
  },
  {
    title: 'Custom Pages',
    description: 'Define returns, refunds, and customer guidelines.',
    icon: FileText,
    iconBg: 'bg-emerald-600',
    to: '/vendor/managed-shop/custom-pages'
  },
  {
    title: 'Shipping',
    description: 'Set shipping options to ensure smooth fulfillment.',
    icon: Truck,
    iconBg: 'bg-green-500',
    to: '/vendor/managed-shop/shipping'
  },
  {
    title: 'Delivery Support',
    description: 'Set delivery options to ensure smooth fulfillment.',
    icon: Truck,
    iconBg: 'bg-rose-500',
    to: '/vendor/managed-shop/delivery-support'
  },
  {
    title: 'Delivery Zones',
    description: 'Manage additional configurations Delivery Charge & Zones.',
    icon: MapPin,
    iconBg: 'bg-teal-600',
    to: '/vendor/managed-shop/delivery-zones'
  },
  {
    title: 'Payment Gateway',
    description: 'Integrate secure and flexible transaction methods.',
    icon: CreditCard,
    iconBg: 'bg-indigo-700',
    to: '/vendor/managed-shop/payment-gateway'
  },
  {
    title: 'SEO & Social Media',
    description: 'Connect SEO tools and Social media integrations.',
    icon: Share2,
    iconBg: 'bg-orange-600',
    to: '/vendor/managed-shop/seo-social'
  },
  {
    title: 'Google , Facebook & Tiktok',
    description: 'Setup your google tag manager, analytics and facebook pixel ,Tiktok Pixel and social login.',
    icon: Target,
    iconBg: 'bg-red-600',
    to: '/vendor/managed-shop/google-fb-tiktok'
  },
  {
    title: 'Chat & Plugins',
    description: 'Setup Messenger, WhatsApp, Tawk.to & more chat integrations for your shop.',
    icon: MessageSquare,
    iconBg: 'bg-cyan-600',
    to: '/vendor/managed-shop/chat-plugins'
  },
  {
    title: 'Third Party APIs',
    description: 'You can set Third Party APIs here, Fraud checking , SMS system etc.',
    icon: Cpu,
    iconBg: 'bg-teal-500',
    to: '/vendor/managed-shop/third-party-apis'
  },
  {
    title: 'Block Fake Order',
    description: 'Identify and block suspicious or fake orders.',
    icon: ShieldAlert,
    iconBg: 'bg-orange-500',
    isPro: true,
    to: '/vendor/managed-shop/block-fake-order'
  },
  {
    title: 'Spider Intelligence',
    description: 'Our Spider Intelligence will help you block fake orders easily.',
    icon: Bug,
    iconBg: 'bg-orange-600',
    isPro: true,
    to: '/vendor/managed-shop/spider-intelligence'
  }
]
definePageMeta({
  middleware: 'auth',
  permissions: 'settings.view'
})
</script>

<style scoped>
/* Custom animations or transitions if needed */
</style>
