<template>
  <section class="py-6 md:py-12 animate-fade-in overflow-hidden">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
      <div 
        v-if="slides && slides.length > 0" 
        class="relative h-[360px] sm:h-[440px] md:h-[520px] rounded-[2.5rem] overflow-hidden shadow-[0_40px_100px_-20px_rgba(0,0,0,0.3)] group/slider"
        @mousemove="handleMouseMove"
        @mouseleave="handleMouseLeave"
        ref="sliderRef"
      >

        <!-- Main Slide Transition -->
        <transition name="slide-fade" mode="out-in">
          <div :key="currentSlide" class="absolute inset-0">
            <!-- Background Image with Parallax -->
            <div 
              class="absolute inset-x-[-5%] inset-y-[-5%] transition-transform duration-[1500ms] ease-out"
              :style="{ 
                transform: `translate(${parallaxX * 0.03}px, ${parallaxY * 0.03}px)`
              }"
            >
              <img
                :src="slides[currentSlide].image"
                :alt="slides[currentSlide].title || slides[currentSlide].badge"
                class="w-full h-full object-cover select-none"
              />
              <!-- Dark gradient overlay for text readability -->
              <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
            </div>

            <!-- Content Overlay -->
            <div class="absolute inset-0 z-10 flex flex-col justify-end p-10 sm:p-16 md:p-24">
              <div class="max-w-2xl space-y-4 md:space-y-6">
                <transition name="content-reveal" appear>
                  <div class="space-y-4">
                    <span class="inline-block px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-[10px] md:text-xs font-bold tracking-[0.2em] uppercase text-white/90" v-if="slides[currentSlide].badge">
                      {{ slides[currentSlide].badge }}
                    </span>
                    <h2 class="text-4xl sm:text-5xl md:text-7xl font-black text-white leading-tight drop-shadow-lg" v-if="slides[currentSlide].title">
                      {{ slides[currentSlide].title }}
                    </h2>
                    <p class="text-base md:text-lg text-white/80 max-w-lg font-medium leading-relaxed drop-shadow-md" v-if="slides[currentSlide].description">
                      {{ slides[currentSlide].description }}
                    </p>
                    <div class="pt-4 md:pt-6" v-if="slides[currentSlide].buttonText">
                      <NuxtLink 
                        :to="slides[currentSlide].link"
                        class="group/btn relative inline-flex items-center gap-3 px-8 py-4 bg-white text-black font-bold rounded-2xl overflow-hidden transition-all hover:scale-105 active:scale-95 shadow-2xl"
                      >
                        <span class="relative z-10">{{ slides[currentSlide].buttonText }}</span>
                        <svg class="relative z-10 w-5 h-5 transition-transform group-hover/btn:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                        <div class="absolute inset-0 bg-gradient-to-r from-gray-50 via-white to-gray-50 opacity-0 group-hover/btn:opacity-100 transition-opacity"></div>
                      </NuxtLink>
                    </div>
                  </div>
                </transition>
              </div>
            </div>
          </div>
        </transition>

        <!-- Navigation Controls (Glassmorphic) -->
        <div class="absolute bottom-8 right-8 z-30 flex gap-4">
          <button
            @click="prevSlide"
            class="w-12 h-12 md:w-14 md:h-14 rounded-2xl border border-white/20 bg-white/10 text-white flex items-center justify-center backdrop-blur-xl transition-all hover:bg-white hover:text-black hover:scale-110 active:scale-95 group/prev"
          >
            <svg class="w-6 h-6 transition-transform group-hover/prev:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <button
            @click="nextSlide"
            class="w-12 h-12 md:w-14 md:h-14 rounded-2xl border border-white/20 bg-white/10 text-white flex items-center justify-center backdrop-blur-xl transition-all hover:bg-white hover:text-black hover:scale-110 active:scale-95 group/next"
          >
            <svg class="w-6 h-6 transition-transform group-hover/next:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>

        <!-- Progress Indicator (Top Right) -->
        <div class="absolute top-8 right-8 z-30 flex items-center gap-4">
          <div class="flex gap-2">
            <button
              v-for="(_, i) in slides"
              :key="'dot-'+i"
              @click="goToSlide(i)"
              class="w-8 md:w-12 h-1 rounded-full overflow-hidden bg-white/20 transition-all hover:bg-white/40"
            >
              <div 
                class="h-full bg-white transition-all ease-linear"
                :style="{ 
                  width: currentSlide === i ? `${progress}%` : (currentSlide > i ? '100%' : '0%'),
                  transitionDuration: currentSlide === i ? '100ms' : '500ms'
                }"
              ></div>
            </button>
          </div>
          <span class="text-white/60 text-xs font-mono font-bold">{{ currentSlide + 1 }} / {{ slides.length }}</span>
        </div>

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
const slideInterval = ref(null)
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
  if (props.slides.length > 0) {
    startSlider()
  }
})

watch(() => props.slides, (newSlides) => {
  if (newSlides && newSlides.length > 0 && !slideInterval.value) {
    startSlider()
  }
}, { deep: true })

function startSlider() {
  resetLogic()
  
  progressInterval.value = setInterval(() => {
    progress.value += (100 / (SLIDE_DURATION / 100))
    if (progress.value >= 100) {
      nextSlide()
    }
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
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  opacity: 0;
  animation: fadeIn 1s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
}

/* Main Slide Transition */
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
}
.slide-fade-enter-from { opacity: 0; filter: blur(10px) brightness(1.5); }
.slide-fade-leave-to   { opacity: 0; filter: blur(10px) brightness(0.5); }

/* Content Reveal Animation */
.content-reveal-enter-active {
  transition: all 0.8s cubic-bezier(0.16, 1, 0.3, 1);
  transition-delay: 0.3s;
}
.content-reveal-enter-from {
  opacity: 0;
  transform: translateY(30px);
  filter: blur(5px);
}
</style>


