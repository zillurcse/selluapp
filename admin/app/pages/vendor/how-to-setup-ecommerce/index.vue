<template>
  <div class="min-h-screen bg-[#F8FAFC] p-6 lg:p-10">
    <!-- Header & Progress Section -->
    <div class="max-w-5xl mx-auto mb-12">
      <div class="bg-white rounded-[40px] p-8 md:p-12 shadow-sm border border-slate-100 overflow-hidden relative">
        <!-- Abstract Background -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-50 rounded-full blur-3xl -mr-32 -mt-32"></div>
        
        <div class="relative z-10">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div class="max-w-xl">
              <h1 class="text-4xl md:text-5xl font-black text-slate-900 tracking-tight mb-4">
                Launch Your <span class="text-emerald-500">Online Store</span>
              </h1>
              <p class="text-slate-500 text-lg font-medium leading-relaxed">
                Follow this simple step-by-step guide to get your business ready for the world. You're just a few steps away from your first sale!
              </p>
            </div>
            
            <div class="flex flex-col items-center">
              <div class="relative w-32 h-32 flex items-center justify-center">
                <svg class="w-full h-full -rotate-90">
                  <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" class="text-slate-100" />
                  <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent" class="text-emerald-500" stroke-dasharray="364.4" :stroke-dashoffset="364.4 * (1 - progress / 100)" stroke-linecap="round" />
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                  <span class="text-2xl font-black text-slate-900">{{ progress }}%</span>
                  <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Done</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Steps Grid -->
    <div class="max-w-5xl mx-auto">
      <div class="space-y-6">
        <div 
          v-for="(step, index) in steps" 
          :key="index"
          :class="[
            'group relative bg-white rounded-[32px] p-6 md:p-8 border transition-all duration-300',
            step.completed ? 'border-emerald-100 bg-emerald-50/20' : 'border-slate-100 hover:border-slate-300 hover:shadow-xl hover:-translate-y-1'
          ]"
        >
          <div class="flex flex-col md:flex-row items-start md:items-center gap-6 md:gap-8">
            <!-- Icon -->
            <div :class="['w-16 h-16 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg transition-transform group-hover:rotate-6', step.completed ? 'bg-emerald-500' : 'bg-slate-900']">
              <component :is="step.icon" class="w-8 h-8 text-white" />
            </div>

            <!-- Content -->
            <div class="flex-grow">
              <div class="flex items-center gap-3 mb-1">
                <h3 :class="['text-xl font-black transition-colors', step.completed ? 'text-emerald-700' : 'text-slate-900']">
                  {{ step.title }}
                </h3>
                <div v-if="step.completed" class="flex items-center gap-1.5 px-3 py-1 bg-emerald-100 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-wider">
                  <Check class="w-3 h-3" stroke-width="4" />
                  Completed
                </div>
              </div>
              <p :class="['font-medium line-clamp-2', step.completed ? 'text-emerald-600/70' : 'text-slate-500']">
                {{ step.description }}
              </p>
            </div>

            <!-- Action -->
            <div class="w-full md:w-auto mt-4 md:mt-0">
              <NuxtLink 
                v-if="!step.completed"
                :to="step.link"
                class="w-full md:w-auto inline-flex items-center justify-center gap-2 bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-sm whitespace-nowrap hover:bg-black transition-all active:scale-95 group/btn"
              >
                {{ step.cta }}
                <ArrowRight class="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" />
              </NuxtLink>
              <div v-else class="flex items-center justify-center w-12 h-12 bg-emerald-100 text-emerald-600 rounded-2xl mx-auto md:mr-0">
                <Check class="w-6 h-6" stroke-width="3" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Motivation & Resources -->
    <div class="max-w-5xl mx-auto mt-20">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div class="bg-indigo-600 rounded-[32px] p-8 text-white relative overflow-hidden group">
          <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -mr-16 -mt-16"></div>
          <Sparkles class="w-8 h-8 mb-6" />
          <h4 class="text-xl font-black mb-3">Professional Success</h4>
          <p class="text-indigo-100 font-medium mb-6 opacity-80">Stores with complete information have a 40% higher conversion rate.</p>
          <button class="text-white font-black text-sm uppercase tracking-widest flex items-center gap-2 group-hover:gap-3 transition-all">
            Read Success Stories <ArrowRight class="w-4 h-4" />
          </button>
        </div>

        <div class="bg-slate-900 rounded-[32px] p-8 text-white flex flex-col justify-between">
          <div>
            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center mb-6">
              <Video class="w-6 h-6 text-indigo-400" />
            </div>
            <h4 class="text-xl font-black mb-3">Video Tutorials</h4>
            <p class="text-slate-400 font-medium mb-6">Watch our quick guides to master the shop management features.</p>
          </div>
          <button class="bg-white text-slate-900 w-full py-4 rounded-2xl font-black text-sm hover:bg-slate-100 transition-colors">
            Browse Tutorials
          </button>
        </div>

        <div class="bg-white border border-slate-100 rounded-[32px] p-8 shadow-sm flex flex-col justify-between">
          <div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-6">
              <Headphones class="w-6 h-6" />
            </div>
            <h4 class="text-xl font-black text-slate-900 mb-3">Need Hands-on?</h4>
            <p class="text-slate-500 font-medium mb-6">Our experts can help you setup your shop for a small fee.</p>
          </div>
          <button class="border-2 border-slate-900 text-slate-900 w-full py-4 rounded-2xl font-black text-sm hover:bg-slate-900 hover:text-white transition-all">
            Contact Concierge
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  Store, 
  Layers, 
  Package, 
  CreditCard, 
  Truck, 
  Globe, 
  Sparkles, 
  Video, 
  Headphones, 
  ArrowRight, 
  Check 
} from 'lucide-vue-next'

const progress = ref(35)

const steps = [
  {
    title: 'Basic Store Information',
    description: 'Setup your shop name, logo, contact details and professional email.',
    icon: Store,
    link: '/vendor/managed-shop/settings',
    cta: 'Complete Info',
    completed: true
  },
  {
    title: 'Create Product Categories',
    description: 'Organize your store by creating meaningful categories for your customers.',
    icon: Layers,
    link: '/vendor/managed-shop/custom-content', // Assuming custom content includes categories or similar
    cta: 'Add Categories',
    completed: true
  },
  {
    title: 'Add Your First Products',
    description: 'Upload high-quality images and descriptions for your amazing products.',
    icon: Package,
    link: '/vendor/products',
    cta: 'Manage Products',
    completed: false
  },
  {
    title: 'Setup Payment Methods',
    description: 'Enable payment gateways and cash on delivery to start accepting orders.',
    icon: CreditCard,
    link: '/vendor/managed-shop/payment-gateway',
    cta: 'Gateways Setup',
    completed: false
  },
  {
    title: 'Configure Delivery Options',
    description: 'Set your delivery zones and shipping rates for local and global orders.',
    icon: Truck,
    link: '/vendor/managed-shop/delivery-support',
    cta: 'Shipping Setup',
    completed: false
  },
  {
    title: 'Connect Custom Domain',
    description: 'Make your store look professional with your own branded web address.',
    icon: Globe,
    link: '/vendor/managed-shop/shop-domain',
    cta: 'Connect Domain',
    completed: false
  }
]
</script>

<style scoped>
/* Smooth circular progress animation */
circle {
  transition: stroke-dashoffset 1s ease-in-out;
}
</style>
