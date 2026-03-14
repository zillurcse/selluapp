<template>
  <section v-if="promotions && promotions.length > 0" class="bg-white">
    <div class="container mx-auto px-4 sm:px-6">
      <!-- Section Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 sm:mb-10">
        <div>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight font-heading">Exclusive Deals</h2>
          <div class="h-1 w-12 bg-indigo-600 mt-2 rounded-full"></div>
        </div>
        <NuxtLink to="/shop" class="text-xs sm:text-sm font-bold uppercase tracking-widest text-gray-400 hover:text-indigo-600 transition-colors">
          View All Offers
        </NuxtLink>
      </div>

      <!-- Promotions Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="promo in promotions" 
          :key="promo.id"
          class="group relative overflow-hidden rounded-3xl transition-all duration-500 hover:shadow-2xl hover:-translate-y-1"
        >
          <!-- Background Visual -->
          <div 
            class="absolute inset-0 transition-transform duration-700 group-hover:scale-110"
            :class="getPromoBgClass(promo.type)"
          >
            <img 
              v-if="promo.banner" 
              :src="promo.banner" 
              class="w-full h-full object-cover opacity-50 mix-blend-overlay"
              :alt="promo.title"
            />
            <div v-else class="w-full h-full opacity-10 flex items-center justify-center text-[10rem] select-none">
              {{ getPromoEmoji(promo.type) }}
            </div>
          </div>

          <!-- Content Overlay -->
          <div class="relative p-6 sm:p-8 h-full min-h-[200px] sm:min-h-[220px] flex flex-col justify-between z-10">
            <div>
              <div class="flex items-center gap-2 mb-4">
                <span 
                  class="px-3 py-1 bg-white/90 backdrop-blur-sm rounded-full text-[10px] font-bold uppercase tracking-wider shadow-sm"
                  :class="getPromoTextClass(promo.type)"
                >
                  {{ formatType(promo.type) }}
                </span>
                <span v-if="promo.is_stackable" class="px-2 py-1 bg-green-500 text-white rounded-full text-[8px] font-bold uppercase">
                  Stackable
                </span>
              </div>
              <h3 class="text-xl sm:text-2xl font-bold text-gray-900 leading-tight mb-2 group-hover:text-indigo-600 transition-colors">
                {{ promo.title }}
              </h3>
              
              <!-- Countdown Timer -->
              <div v-if="promo.end_date" class="flex gap-2 sm:gap-3 mb-4">
                <div v-for="(val, label) in getCountdown(promo.end_date)" :key="label" class="flex flex-col items-center">
                  <div class="w-8 h-8 sm:w-10 sm:h-10 bg-white/80 backdrop-blur-md rounded-lg sm:rounded-xl border border-white/50 shadow-sm flex items-center justify-center font-bold text-gray-900 text-xs sm:text-sm">
                    {{ val }}
                  </div>
                  <span class="text-[7px] sm:text-[8px] font-bold uppercase tracking-tighter text-gray-400 mt-1">{{ label }}</span>
                </div>
              </div>

              <p v-if="promo.type === 'buy_x_get_y' && promo.rules" class="text-gray-600 text-sm font-medium">
                Buy {{ promo.rules.buy_qty }} Get {{ promo.rules.get_qty }} with 
                {{ formatDiscount(promo.discount_value, promo.discount_unit) }} Off
              </p>
              <p v-else-if="promo.type === 'bundle'" class="text-gray-600 text-sm font-medium">
                Bundle Deal: {{ formatDiscount(promo.discount_value, promo.discount_unit) }} Instant Savings
              </p>
              <p v-else class="text-gray-600 text-sm font-medium">
                Enjoy {{ formatDiscount(promo.discount_value, promo.discount_unit) }} discount today!
              </p>
            </div>

            <div class="mt-5 sm:mt-6">
              <NuxtLink 
                :to="getPromoLink(promo)" 
                class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold uppercase tracking-widest text-gray-900 group/link"
              >
                Claim Offer
                <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 transition-transform duration-300 group-hover/link:translate-x-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
              </NuxtLink>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  promotions: {
    type: Array,
    default: () => []
  }
})

const currentTime = ref(new Date())
let timerInterval = null

onMounted(() => {
  timerInterval = setInterval(() => {
    currentTime.value = new Date()
  }, 1000)
})

onUnmounted(() => {
  if (timerInterval) clearInterval(timerInterval)
})

const getCountdown = (endDate) => {
  const diff = new Date(endDate) - currentTime.value
  
  if (diff <= 0) {
    return { days: '00', hours: '00', min: '00', sec: '00' }
  }

  const days = Math.floor(diff / (1000 * 60 * 60 * 24))
  const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
  const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))
  const seconds = Math.floor((diff % (1000 * 60)) / 1000)

  return {
    days: String(days).padStart(2, '0'),
    hours: String(hours).padStart(2, '0'),
    min: String(minutes).padStart(2, '0'),
    sec: String(seconds).padStart(2, '0')
  }
}

const getPromoLink = (promo) => {
  return `/shop?promotion=${promo.slug}`
}

const formatDiscount = (value, unit) => {
  if (unit === 'percentage') return `${parseFloat(value)}%`
  return `৳${parseFloat(value)}`
}

const formatType = (type) => {
  return type.replace(/_/g, ' ')
}

const getPromoBgClass = (type) => {
  const classes = {
    'flash_sale': 'bg-rose-50',
    'flat_discount': 'bg-indigo-50',
    'buy_x_get_y': 'bg-amber-50',
    'bundle': 'bg-fuchsia-50',
    'category': 'bg-emerald-50',
    'variant': 'bg-blue-50'
  }
  return classes[type] || 'bg-gray-50'
}

const getPromoTextClass = (type) => {
  const classes = {
    'flash_sale': 'text-rose-600',
    'flat_discount': 'text-indigo-600',
    'buy_x_get_y': 'text-amber-600',
    'bundle': 'text-fuchsia-600',
    'category': 'text-emerald-600',
    'variant': 'text-blue-600'
  }
  return classes[type] || 'text-gray-600'
}

const getPromoEmoji = (type) => {
  const emojis = {
    'flash_sale': '⚡',
    'flat_discount': '💰',
    'buy_x_get_y': '🎁',
    'bundle': '📦',
    'category': '🏷️',
    'variant': '✨'
  }
  return emojis[type] || '🎊'
}
</script>

<style scoped>
.font-heading {
  font-family: var(--font-heading);
}
</style>
