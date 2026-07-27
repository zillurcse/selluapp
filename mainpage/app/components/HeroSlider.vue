<template>
  <section class="relative animate-fade-in -mt-[72px]">
    <div
      v-if="slides && slides.length > 0"
      class="relative h-[78vh] min-h-[460px] max-h-[720px] w-full overflow-hidden bg-[var(--muted)]"
      @mousemove="handleMouseMove"
      @mouseleave="handleMouseLeave"
      ref="sliderRef"
    >
      <transition name="slide-fade" mode="out-in">
        <div :key="currentSlide" class="absolute inset-0">
          <div
            class="absolute inset-0 scale-105 transition-transform duration-[1200ms] ease-out"
            :style="{
              transform: `translate(${parallaxX * 0.02}px, ${parallaxY * 0.02}px) scale(1.05)`
            }"
          >
            <img
              :src="slides[currentSlide].image"
              :alt="slides[currentSlide].title || slides[currentSlide].badge"
              class="w-full h-full object-cover select-none"
            />
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-black/10"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
          </div>

          <div class="absolute inset-0 z-10 flex items-end md:items-center">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 pb-16 md:pb-0 pt-24">
              <div class="max-w-xl">
                <transition name="content-reveal" appear>
                  <div class="flex flex-col gap-4 sm:gap-5">
                    <span
                      v-if="slides[currentSlide].badge"
                      class="text-[11px] font-semibold uppercase tracking-[0.2em] text-white/70"
                    >
                      {{ slides[currentSlide].badge }}
                    </span>
                    <h1
                      v-if="slides[currentSlide].title"
                      class="text-4xl sm:text-5xl md:text-6xl font-semibold text-white leading-[1.05] tracking-tight"
                      style="font-family: var(--font-heading)"
                    >
                      {{ slides[currentSlide].title }}
                    </h1>
                    <p
                      v-if="slides[currentSlide].description"
                      class="text-sm sm:text-base text-white/75 max-w-md leading-relaxed line-clamp-3"
                    >
                      {{ slides[currentSlide].description }}
                    </p>
                    <div v-if="slides[currentSlide].buttonText" class="pt-2">
                      <NuxtLink
                        :to="slides[currentSlide].link"
                        class="group/btn inline-flex items-center gap-2.5 px-6 py-3.5 bg-white text-gray-900 text-sm font-semibold rounded-xl transition-all hover:bg-gray-100 active:scale-[0.98]"
                      >
                        {{ slides[currentSlide].buttonText }}
                        <svg class="w-4 h-4 transition-transform group-hover/btn:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                      </NuxtLink>
                    </div>
                  </div>
                </transition>
              </div>
            </div>
          </div>
        </div>
      </transition>

      <!-- Controls -->
      <div class="absolute bottom-6 right-4 sm:bottom-8 sm:right-8 z-30 hidden sm:flex gap-2">
        <button
          @click="prevSlide"
          aria-label="Previous slide"
          class="w-11 h-11 rounded-xl border border-white/25 bg-white/10 text-white flex items-center justify-center backdrop-blur-md transition-all hover:bg-white hover:text-black"
        >
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
          </svg>
        </button>
        <button
          @click="nextSlide"
          aria-label="Next slide"
          class="w-11 h-11 rounded-xl border border-white/25 bg-white/10 text-white flex items-center justify-center backdrop-blur-md transition-all hover:bg-white hover:text-black"
        >
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>

      <!-- Progress -->
      <div class="absolute bottom-6 left-4 sm:bottom-8 sm:left-8 z-30 flex items-center gap-3">
        <div class="flex gap-1.5">
          <button
            v-for="(_, i) in slides"
            :key="'dot-'+i"
            @click="goToSlide(i)"
            :aria-label="`Go to slide ${i + 1}`"
            class="h-1 rounded-full overflow-hidden bg-white/25 transition-all"
            :class="currentSlide === i ? 'w-8 sm:w-10' : 'w-4 sm:w-5 hover:bg-white/40'"
          >
            <div
              class="h-full bg-white transition-all ease-linear"
              :style="{
                width: currentSlide === i ? `${progress}%` : (currentSlide > i ? '100%' : '0%'),
                transitionDuration: currentSlide === i ? '100ms' : '400ms'
              }"
            ></div>
          </button>
        </div>
        <span class="text-white/50 text-[11px] font-medium tabular-nums">{{ currentSlide + 1 }} / {{ slides.length }}</span>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else class="h-[70vh] min-h-[420px] -mt-[72px] pt-[72px] bg-[var(--muted)] flex items-center justify-center">
      <div class="text-center px-6">
        <h1 class="text-3xl md:text-5xl font-semibold tracking-tight mb-3" style="font-family: var(--font-heading)">Welcome to our store</h1>
        <p class="text-[var(--muted-foreground)] mb-6">Discover products curated for everyday living.</p>
        <NuxtLink to="/shop" class="btn-primary">Shop now</NuxtLink>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'

const props = defineProps({
  slides: {
    type: Array,
    required: true,
    default: () => []
  }
})

const currentSlide = ref(0)
const progressInterval = ref(null)
const progress = ref(0)
const sliderRef = ref(null)
const parallaxX = ref(0)
const parallaxY = ref(0)

const SLIDE_DURATION = 6000

function handleMouseMove(e) {
  if (!sliderRef.value) return
  const rect = sliderRef.value.getBoundingClientRect()
  parallaxX.value = (e.clientX - rect.left) - rect.width / 2
  parallaxY.value = (e.clientY - rect.top) - rect.height / 2
}

function handleMouseLeave() {
  parallaxX.value = 0
  parallaxY.value = 0
}

onMounted(() => {
  if (props.slides.length > 0) startSlider()
})

watch(() => props.slides, (newSlides) => {
  if (newSlides?.length > 0 && !progressInterval.value) startSlider()
}, { deep: true })

function startSlider() {
  resetLogic()
  progressInterval.value = setInterval(() => {
    progress.value += (100 / (SLIDE_DURATION / 100))
    if (progress.value >= 100) nextSlide()
  }, 100)
}

function resetLogic() {
  if (progressInterval.value) clearInterval(progressInterval.value)
  progress.value = 0
}

const prevSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + props.slides.length) % props.slides.length
  restartSlider()
}

const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % props.slides.length
  restartSlider()
}

const goToSlide = (i) => {
  currentSlide.value = i
  restartSlider()
}

function restartSlider() {
  resetLogic()
  startSlider()
}

onUnmounted(() => {
  resetLogic()
})
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}
.animate-fade-in {
  animation: fadeIn 0.8s ease forwards;
}

.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: opacity 0.7s ease;
}
.slide-fade-enter-from,
.slide-fade-leave-to {
  opacity: 0;
}

.content-reveal-enter-active {
  transition: all 0.7s cubic-bezier(0.16, 1, 0.3, 1);
  transition-delay: 0.15s;
}
.content-reveal-enter-from {
  opacity: 0;
  transform: translateY(20px);
}
</style>
