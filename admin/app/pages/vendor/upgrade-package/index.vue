<template>
  <div class="p-10 lg:p-10 bg-[#f8fafc] min-h-screen">
    <!-- Header Section -->
    <div class="max-w-[1400px] mx-auto mb-12">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
          <NuxtLink 
            to="/vendor/managed-shop" 
            class="w-12 h-12 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-slate-600 hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all active:scale-95 shadow-sm group"
          >
            <ArrowLeft class="w-6 h-6 group-hover:-translate-x-1 transition-transform" />
          </NuxtLink>
          <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">Upgrade Your Plan</h1>
            <p class="text-slate-500 font-medium mt-1">Choose the perfect plan to scale your business to the next level.</p>
          </div>
        </div>

        <!-- Billing Toggle -->
        <div class="flex items-center gap-4 bg-white p-1.5 border border-slate-200 rounded-[20px] shadow-sm self-start md:self-center">
          <button 
            @click="isYearly = false"
            :class="['px-6 py-2.5 text-sm font-black rounded-[14px] transition-all', !isYearly ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'text-slate-500 hover:text-slate-900']"
          >
            Monthly
          </button>
          <button 
            @click="isYearly = true"
            :class="['px-6 py-2.5 text-sm font-black rounded-[14px] transition-all relative', isYearly ? 'bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'text-slate-500 hover:text-slate-900']"
          >
            Yearly
            <span class="absolute -top-3 -right-2 px-2 py-0.5 bg-emerald-500 text-white text-[9px] font-black rounded-full uppercase tracking-tighter ring-2 ring-white">20% OFF</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Current Plan Banner -->
    <div v-if="currentPackage.id" class="max-w-[1400px] mx-auto mb-12">
      <div class="bg-indigo-600 rounded-3xl p-8 text-white shadow-xl shadow-indigo-200 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative overflow-hidden">
        <!-- Background Decoration -->
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white opacity-10 blur-3xl"></div>
        <div class="relative z-10">
          <div class="flex items-center gap-3 mb-2">
            <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-bold uppercase tracking-wider">Current Plan</span>
          </div>
          <h2 class="text-3xl font-black mb-1">{{ currentPackage.name }}</h2>
          <p class="text-indigo-100 font-medium">
            You have access to {{ currentPackage.product_limit === -1 ? 'unlimited' : currentPackage.product_limit }} products.
            <span v-if="currentPackageExpiry">Expires on: {{ new Date(currentPackageExpiry).toLocaleDateString() }}</span>
          </p>
        </div>
        <div class="relative z-10 flex gap-4">
          <div class="bg-white/10 p-4 rounded-2xl backdrop-blur-sm border border-white/20 text-center">
            <div class="text-2xl font-black">{{ currentPackage.product_limit === -1 ? '∞' : currentPackage.product_limit }}</div>
            <div class="text-indigo-200 text-xs font-bold uppercase tracking-wider">Product Limit</div>
          </div>
           <div class="bg-white/10 p-4 rounded-2xl backdrop-blur-sm border border-white/20 text-center">
            <div class="text-2xl font-black">{{ currentPackage.employee_limit === -1 ? '∞' : currentPackage.employee_limit }}</div>
            <div class="text-indigo-200 text-xs font-bold uppercase tracking-wider">Staff Limit</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Pricing Grid -->
    <div class="max-w-[1400px] mx-auto">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Plan Card -->
        <div 
          v-for="(plan, index) in plans" 
          :key="index"
          :class="[
            'relative p-1 rounded-[40px] transition-all duration-500 group',
            plan.featured ? 'bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 shadow-2xl shadow-indigo-200' : 'bg-white border border-slate-100 hover:border-slate-300 shadow-sm hover:shadow-xl'
          ]"
        >
          <!-- Featured Badge -->
          <div v-if="plan.featured" class="absolute -top-5 left-1/2 -translate-x-1/2 px-6 py-2 bg-slate-900 text-white rounded-full flex items-center gap-2 shadow-xl z-20">
            <Star class="w-4 h-4 text-amber-400 fill-amber-400" />
            <span class="text-xs font-black uppercase tracking-widest">Most Popular</span>
          </div>

          <div :class="['h-full rounded-[38px] p-8 md:p-10 flex flex-col', plan.featured ? 'bg-white/95 backdrop-blur-xl' : 'bg-white']">
            <!-- Header -->
            <div class="mb-10">
              <div :class="['w-16 h-16 rounded-2xl flex items-center justify-center mb-6 shadow-lg rotate-3 group-hover:rotate-0 transition-transform duration-500', plan.iconBg]">
                <component :is="plan.icon" class="w-8 h-8 text-white" />
              </div>
              <h2 class="text-2xl font-black text-slate-900 tracking-tight">{{ plan.name }}</h2>
              <p class="text-slate-500 font-medium mt-2 leading-relaxed min-h-[48px]">{{ plan.description }}</p>
            </div>

            <!-- Price -->
            <div class="mb-10">
              <div class="flex items-baseline gap-1">
                <span class="text-lg font-black text-slate-400">$</span>
                <span class="text-6xl font-black text-slate-900 tracking-tighter">
                  {{ isYearly ? plan.yearlyPrice : plan.monthlyPrice }}
                </span>
                <span class="text-slate-400 font-bold ml-1">/{{ isYearly ? 'year' : 'mo' }}</span>
              </div>
              <p v-if="isYearly && plan.monthlyPrice > 0" class="text-emerald-500 text-xs font-black mt-2 uppercase tracking-widest">Billed annually</p>
              <p v-else class="text-slate-300 text-xs font-black mt-2 uppercase tracking-widest">Billed monthly</p>
            </div>

            <!-- Features -->
            <div class="space-y-4 mb-12 flex-grow">
              <div v-for="(feature, fIndex) in plan.features" :key="fIndex" class="flex items-start gap-3">
                <div :class="['mt-1 flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center', feature.included ? 'bg-indigo-100 text-indigo-600' : 'bg-slate-100 text-slate-400']">
                  <component :is="feature.included ? Check : Minus" class="w-3 h-3" stroke-width="4" />
                </div>
                <span :class="['text-sm font-bold', feature.included ? 'text-slate-700' : 'text-slate-400 line-through decoration-slate-200']">
                  {{ feature.label }}
                </span>
              </div>
            </div>

            <!-- Action Button -->
            <button 
              @click="purchasePlan(plan.id)"
              :disabled="plan.isCurrent || isProcessing"
              :class="[
                'w-full py-5 rounded-2xl font-black text-lg transition-all active:scale-[0.98] shadow-lg flex items-center justify-center gap-3 group/btn',
                (plan.isCurrent || isProcessing)
                  ? 'bg-slate-100 text-slate-400 cursor-not-allowed shadow-none' 
                  : (plan.featured 
                    ? 'bg-slate-900 text-white hover:bg-black shadow-slate-900/20' 
                    : 'bg-white border-2 border-slate-900 text-slate-900 hover:bg-slate-900 hover:text-white')
              ]"
            >
              <span v-if="plan.isCurrent">Active Plan</span>
              <span v-else-if="isProcessing">Processing...</span>
              <template v-else>
                {{ plan.cta }}
                <ArrowRight class="w-5 h-5 group-hover/btn:translate-x-1 transition-transform" />
              </template>
            </button>
          </div>
        </div>
      </div>

      <!-- FAQ / Info Section -->
      <div class="mt-20 max-w-4xl mx-auto text-center">
        <div class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-600 rounded-full mb-6">
          <ShieldCheck class="w-4 h-4" />
          <span class="text-xs font-black uppercase tracking-widest">Secure & Trusted</span>
        </div>
        <h2 class="text-3xl font-black text-slate-900 mb-6">Frequently Asked Questions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-left mt-12">
          <div v-for="(faq, i) in faqs" :key="i" class="p-8 bg-white border border-slate-100 rounded-[32px] hover:shadow-lg transition-all">
            <h3 class="text-lg font-black text-slate-800 mb-3">{{ faq.q }}</h3>
            <p class="text-slate-500 font-medium leading-relaxed">{{ faq.a }}</p>
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
  Check, 
  Minus, 
  Star, 
  Zap, 
  Rocket, 
  Crown, 
  ShieldCheck, 
  HeartHandshake
} from 'lucide-vue-next'

import { toast } from 'vue-sonner'

definePageMeta({
  middleware: 'auth'
})

const isYearly = ref(false)
const isProcessing = ref(false)

const authStore = useAuthStore()
const utilityStore = useUtilityStore()
const tokenStore = useTokenStore()
const config = useRuntimeConfig()

const route = useRoute()
const { deleteItem, getAll } = useCrud()

const packages = await getAll('/vendor/packages')

const currentPackageId = computed(() => authStore.user?.vendor_profile?.package_id || authStore.user?.owner?.vendor_profile?.package_id)
const currentPackage = computed(() => authStore.user?.vendor_profile?.package || authStore.user?.owner?.vendor_profile?.package || {})
const currentPackageExpiry = computed(() => authStore.user?.vendor_profile?.package_expiry_date || authStore.user?.owner?.vendor_profile?.package_expiry_date || null)


const plans = computed(() => {
  if (!packages || !Array.isArray(packages)) return []
  return packages.map((pkg, index) => {
    const icons = [Zap, Rocket, Crown]
    const iconBgs = ['bg-amber-500', 'bg-indigo-600', 'bg-slate-900']
    const icon = icons[index % icons.length]
    const iconBg = iconBgs[index % iconBgs.length]

    const monthlyPrice = parseFloat(pkg.price)
    // If the database has a yearly price we could use it, but here we calculate a 20% discount.
    const yearlyPrice = monthlyPrice * 12 * 0.8 

    const pkgFeatures = pkg.features || []
    
    const featureList = [
      { label: 'Basic Shop Management', included: true },
      { label: pkg.product_limit === -1 ? 'Unlimited Products' : `Up to ${pkg.product_limit} Products`, included: true },
      { label: 'Orders Management', included: true },
      { label: 'Customers', included: pkgFeatures.includes('Customers') },
      { label: 'Basic Reports', included: pkgFeatures.includes('Basic Reports') || pkgFeatures.includes('Advanced Reports') },
      { label: 'Advanced Promotions', included: pkgFeatures.includes('Promotions') },
      { label: 'POS System', included: pkg.pos_access },
      { label: 'HRM Suite', included: pkg.hrm_access },
      { label: 'Fraud Check Security', included: pkgFeatures.includes('Fraud Check') || pkgFeatures.includes('Fraud Check Security') },
      { label: 'Delivery Setup', included: pkgFeatures.includes('Delivery') || pkgFeatures.includes('Delivery Setup') },
      { label: 'Landing Pages', included: pkgFeatures.includes('Landing Pages') },
    ]

    return {
      id: pkg.id,
      name: pkg.name,
      description: pkg.description || 'Elevate your business to the next level.',
      monthlyPrice: monthlyPrice,
      yearlyPrice: yearlyPrice,
      icon,
      iconBg,
      isCurrent: currentPackageId.value === pkg.id,
      featured: index === 1, // Make the 2nd plan featured
      cta: `Request ${pkg.name}`,
      features: featureList
    }
  })
})

const purchasePlan = (packageId) => {
  const router = useRouter();
  router.push({
    path: '/vendor/upgrade-package/payment',
    query: {
      package_id: packageId,
      billing_cycle: isYearly.value ? 'yearly' : 'monthly'
    }
  });
}

const faqs = [
  {
    q: 'Can I change my plan later?',
    a: 'Absolutely! You can upgrade or downgrade your plan at any time from your dashboard settings. Adjustments will be prorated.'
  },
  {
    q: 'Is there a free trial?',
    a: 'We offer a 14-day free trial for the Growth plan so you can explore all the premium features before committing.'
  },
  {
    q: 'What is Fraud Check?',
    a: 'Our AI-powered fraud check system analyzes order patterns and shipping addresses to protect you from fake orders and chargebacks.'
  },
  {
    q: 'Do you offer help with migration?',
    a: 'Yes, for Enterprise customers, we provide a dedicated team to help you migrate your existing data and setup your shop.'
  }
]
</script>

<style scoped>
.backdrop-blur-xl {
  backdrop-filter: blur(24px);
}
</style>
