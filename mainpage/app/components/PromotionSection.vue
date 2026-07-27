<template>
  <section v-if="promotions && promotions.length > 0" class="section-pad">
    <div class="container mx-auto">
      <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
        <div>
          <span class="section-label">Limited time</span>
          <h2 class="section-title mt-1">Exclusive deals</h2>
        </div>
        <NuxtLink to="/shop" class="text-sm font-semibold text-gray-900 hover:opacity-60 transition-opacity">
          View all offers
        </NuxtLink>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div
          v-for="promo in promotions"
          :key="promo.id"
          class="group relative overflow-hidden rounded-2xl border border-[var(--border)] bg-[var(--muted)] transition-shadow hover:shadow-md"
        >
          <div class="absolute inset-0">
            <img
              v-if="promo.banner"
              :src="promo.banner"
              class="w-full h-full object-cover opacity-40"
              :alt="promo.title"
              loading="lazy"
            />
          </div>

          <div class="relative p-6 sm:p-7 min-h-[200px] flex flex-col justify-between z-10 bg-gradient-to-br from-white/90 via-white/80 to-white/60 backdrop-blur-[2px]">
            <div>
              <div class="flex items-center gap-2 mb-3">
                <span class="px-2.5 py-1 bg-white border border-[var(--border)] rounded-lg text-[10px] font-semibold uppercase tracking-wider text-gray-700">
                  {{ formatType(promo.type) }}
                </span>
                <span v-if="promo.is_stackable" class="px-2 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-[10px] font-semibold uppercase">
                  Stackable
                </span>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 leading-snug mb-3" style="font-family: var(--font-heading)">
                {{ promo.title }}
              </h3>

              <div v-if="promo.end_date" class="flex gap-2 mb-3">
                <div v-for="(val, label) in getCountdown(promo.end_date)" :key="label" class="flex flex-col items-center">
                  <div class="w-9 h-9 bg-white border border-[var(--border)] rounded-lg flex items-center justify-center font-semibold text-gray-900 text-sm tabular-nums">
                    {{ val }}
                  </div>
                  <span class="text-[9px] font-medium uppercase tracking-wide text-[var(--muted-foreground)] mt-1">{{ label }}</span>
                </div>
              </div>

              <p v-if="promo.type === 'buy_x_get_y' && promo.rules" class="text-gray-600 text-sm">
                Buy {{ promo.rules.buy_qty }} Get {{ promo.rules.get_qty }} —
                {{ formatDiscount(promo.discount_value, promo.discount_unit) }} off
              </p>
              <p v-else-if="promo.type === 'bundle'" class="text-gray-600 text-sm">
                Bundle: {{ formatDiscount(promo.discount_value, promo.discount_unit) }} savings
              </p>
              <p v-else class="text-gray-600 text-sm">
                {{ formatDiscount(promo.discount_value, promo.discount_unit) }} discount
              </p>
            </div>

            <div class="mt-5">
              <NuxtLink
                :to="getPromoLink(promo)"
                class="inline-flex items-center gap-2 text-sm font-semibold text-gray-900 group/link"
              >
                Claim offer
                <svg class="w-4 h-4 transition-transform group-hover/link:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
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

defineProps({
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

const getPromoLink = (promo) => `/shop?promotion=${promo.slug}`

const formatDiscount = (value, unit) => {
  if (unit === 'percentage') return `${parseFloat(value)}%`
  return `৳${parseFloat(value)}`
}

const formatType = (type) => type.replace(/_/g, ' ')
</script>
