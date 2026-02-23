<template>
  <div class="min-h-screen bg-[#F8FAFC]">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-slate-900 pt-24 pb-32">
      <!-- Background Patterns -->
      <div class="absolute inset-0 z-0">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-indigo-500/10 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-500/10 blur-[120px] rounded-full"></div>
      </div>

      <div class="max-w-4xl mx-auto px-6 relative z-10 text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 backdrop-blur-md border border-white/20 rounded-full text-white/80 text-xs font-bold uppercase tracking-widest mb-8 animate-fade-in">
          <BookOpen class="w-3.5 h-3.5" />
          <span>Knowledge Base</span>
        </div>
        <h1 class="text-4xl md:text-6xl font-black text-white mb-6 tracking-tight leading-[1.1]">
          How can we <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">help you</span> today?
        </h1>
        <p class="text-slate-400 text-lg md:text-xl font-medium mb-12 max-w-2xl mx-auto">
          Search our library of articles, guides, and tutorials to get the most out of your shop.
        </p>

        <!-- Search Bar -->
        <div class="max-w-2xl mx-auto relative group">
          <div class="absolute inset-y-0 left-6 flex items-center pointer-events-none">
            <Search class="w-6 h-6 text-slate-400 group-focus-within:text-indigo-400 transition-colors" />
          </div>
          <input 
            type="text" 
            placeholder="Search for articles, guides, or keywords..."
            class="w-full bg-white/5 border border-white/10 backdrop-blur-xl rounded-[24px] py-6 pl-16 pr-8 text-white text-lg font-medium placeholder:text-slate-500 focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500/50 transition-all shadow-2xl"
          >
          <div class="absolute inset-y-2 right-2 hidden md:flex items-center">
            <button class="bg-indigo-600 hover:bg-indigo-500 text-white px-8 h-full rounded-[18px] font-black text-sm transition-all active:scale-95 shadow-lg shadow-indigo-600/20">
              Search
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-6 -mt-16 relative z-20 pb-24">
      <!-- Categories Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <article 
          v-for="(category, index) in categories" 
          :key="index"
          class="bg-white border border-slate-100 rounded-[32px] p-8 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group cursor-pointer"
        >
          <div :class="['w-16 h-16 rounded-2xl flex items-center justify-center mb-8 shadow-lg transition-transform group-hover:rotate-6 duration-300', category.bg]">
            <component :is="category.icon" class="w-8 h-8 text-white" />
          </div>
          <h3 class="text-xl font-black text-slate-900 mb-3">{{ category.title }}</h3>
          <p class="text-slate-500 font-medium mb-6 leading-relaxed">
            {{ category.description }}
          </p>
          <div class="flex items-center gap-2 text-indigo-600 font-black text-sm uppercase tracking-widest group-hover:gap-3 transition-all">
            <span>{{ category.articleCount }} Articles</span>
            <ArrowRight class="w-4 h-4" />
          </div>
        </article>
      </div>

      <!-- FAQ Section -->
      <div class="mt-32">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
          <div>
            <h2 class="text-3xl font-black text-slate-900 mb-2">Frequently Asked Questions</h2>
            <p class="text-slate-500 font-medium">Quick answers to common questions from our merchant community.</p>
          </div>
          <button class="text-indigo-600 font-black text-sm uppercase tracking-widest hover:text-indigo-700 transition-colors">
            View All FAQs
          </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div 
            v-for="(faq, index) in faqs" 
            :key="index"
            class="bg-white border border-slate-100 rounded-[32px] p-8 md:p-10 hover:border-indigo-200 transition-all duration-300"
          >
            <div class="flex gap-6">
              <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center flex-shrink-0">
                <HelpCircle class="w-5 h-5 text-indigo-600" />
              </div>
              <div>
                <h4 class="text-lg font-black text-slate-900 mb-4">{{ faq.q }}</h4>
                <p class="text-slate-500 font-medium leading-relaxed">{{ faq.a }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Contact Support -->
      <div class="mt-32 relative">
        <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-[48px] rotate-1 scale-[1.02] opacity-10"></div>
        <div class="relative bg-slate-900 rounded-[40px] p-8 md:p-16 overflow-hidden">
          <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-white/5 to-transparent pointer-events-none"></div>
          
          <div class="flex flex-col lg:flex-row items-center justify-between gap-12 relative z-10">
            <div class="max-w-xl text-center lg:text-left">
              <h2 class="text-3xl md:text-4xl font-black text-white mb-6">Still need assistance?</h2>
              <p class="text-slate-400 text-lg font-medium leading-relaxed">
                Our support team is available 24/7 to help you with any technical or business-related queries.
              </p>
            </div>
            
            <div class="flex flex-wrap justify-center gap-4">
              <button class="bg-indigo-600 hover:bg-indigo-500 text-white px-10 py-5 rounded-2xl font-black text-lg transition-all active:scale-95 shadow-xl shadow-indigo-600/20 flex items-center gap-3 group">
                <MessageCircle class="w-6 h-6" />
                Live Chat
                <ArrowRight class="w-5 h-5 group-hover:translate-x-1 transition-transform" />
              </button>
              <button class="bg-white/10 hover:bg-white/20 text-white border border-white/10 px-10 py-5 rounded-2xl font-black text-lg transition-all active:scale-95 flex items-center gap-3">
                <Mail class="w-6 h-6" />
                Email Us
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  Search, 
  BookOpen, 
  ArrowRight, 
  HelpCircle, 
  Layout, 
  Package, 
  CreditCard, 
  Truck, 
  ShieldCheck, 
  Megaphone, 
  MessageCircle,
  Mail,
  Zap
} from 'lucide-vue-next'

definePageMeta({
  middleware: 'auth'
})

const categories = [
  {
    title: 'Getting Started',
    description: 'Learn the basics and get your shop up and running in no time.',
    icon: Zap,
    bg: 'bg-amber-500',
    articleCount: 12
  },
  {
    title: 'Store Settings',
    description: 'Configure your domains, themes, and custom content settings.',
    icon: Layout,
    bg: 'bg-indigo-600',
    articleCount: 8
  },
  {
    title: 'Product Management',
    description: 'Managing products, variants, inventory, and categories.',
    icon: Package,
    bg: 'bg-emerald-500',
    articleCount: 15
  },
  {
    title: 'Payments & Billing',
    description: 'Setting up gateways, managing plans, and handling payouts.',
    icon: CreditCard,
    bg: 'bg-slate-900',
    articleCount: 10
  },
  {
    title: 'Shipping & Delivery',
    description: 'Configure delivery zones, shipping rates, and tracking.',
    icon: Truck,
    bg: 'bg-rose-500',
    articleCount: 6
  },
  {
    title: 'Marketing & SEO',
    description: 'Boost your sales with promotions, coupons, and SEO tools.',
    icon: Megaphone,
    bg: 'bg-violet-600',
    articleCount: 9
  }
]

const faqs = [
  {
    q: 'How do I connect my custom domain?',
    a: 'Go to Managed Shop > Domain Settings and enter your domain. You will need to update your DNS records with our provided IP address.'
  },
  {
    q: 'Can I use multiple payment gateways?',
    a: 'Yes, you can enable multiple gateways like SSLCommerz and Cash on Delivery simultaneously from the Payment Settings.'
  },
  {
    q: 'How are shipping costs calculated?',
    a: 'You can set flat rates, weight-based rates, or dynamic rates based on delivery zones in your Shipping configuration.'
  },
  {
    q: 'Is my vendor data secure?',
    a: 'We use industry-standard encryption and security protocols. For extra safety, you can enable the Fraud Check module.'
  }
]
</script>

<style scoped>
@keyframes fade-in {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fade-in 0.6s ease-out forwards;
}

.bg-white\/5 {
  background-color: rgba(255, 255, 255, 0.05);
}

.bg-white\/10 {
  background-color: rgba(255, 255, 255, 0.1);
}
</style>
