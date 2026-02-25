<template>
  <div class="product-immersive-v3 bg-white text-black min-h-screen overflow-x-hidden selection:bg-indigo-600 selection:text-white pb-32">
    <!-- Immersive Hero -->
    <section class="relative h-screen flex flex-col items-center justify-center p-6 text-center overflow-hidden">
      <!-- Dynamic Background (Video or Image) -->
      <div class="absolute inset-0 z-0">
        <div class="absolute inset-0 bg-gradient-to-b from-white via-transparent to-white z-10"></div>
        <img :src="product.image" class="w-full h-full object-cover opacity-10 blur-xl scale-125 transition-transform duration-[10s] animate-slow-zoom" />
      </div>

      <div class="relative z-20 space-y-12 max-w-5xl animate-fade-in-up">
        <div class="flex justify-center">
          <span class="inline-flex items-center gap-3 px-6 py-2 bg-indigo-50 border border-indigo-100 rounded-full text-xs font-black uppercase tracking-[0.3em] text-indigo-600">
            {{ settings.hero_subtitle || 'Series 01 // Limited' }}
          </span>
        </div>
        
        <h1 class="text-6xl md:text-9xl font-black tracking-tighter leading-tight drop-shadow-sm font-heading">
          {{ product.name }}<br/>
          <span class="text-indigo-600">Reimagined.</span>
        </h1>

        <div class="flex flex-col items-center gap-12">
          <p class="text-xl md:text-2xl font-medium text-slate-400 max-w-2xl leading-relaxed">
            The ultimate expression of form following function. A piece designed not just to be used, but to be experienced.
          </p>
          
          <div class="flex items-center gap-8">
            <button class="px-12 py-6 bg-black text-white rounded-full font-black uppercase tracking-widest text-xs hover:bg-indigo-600 hover:scale-105 transition-all duration-500 shadow-2xl shadow-indigo-200">
              {{ settings.cta_text ? `${settings.cta_text} — ৳${product.sale_price}` : `Reserve Now — ৳${product.sale_price}` }}
            </button>
            <button class="w-16 h-16 rounded-full border-2 border-slate-100 flex items-center justify-center group hover:border-black transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="group-hover:translate-y-1 transition-transform"><path d="m6 9 6 6 6-6"/></svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Floating Side Labels -->
      <div class="absolute left-12 top-1/2 -translate-y-1/2 hidden xl:flex flex-col gap-20">
        <div class="rotate-90 origin-left text-[10px] font-black uppercase tracking-[0.5em] text-slate-200">Innovation Since 2026</div>
        <div class="rotate-90 origin-left text-[10px] font-black uppercase tracking-[0.5em] text-slate-200">Copenhagen Studio Design</div>
      </div>
    </section>

    <!-- Mid Section: Product Detail Float -->
    <section class="container mx-auto px-6 py-20">
      <div class="relative rounded-[4rem] bg-slate-50 border border-slate-100 p-12 lg:p-24 overflow-hidden flex flex-col lg:flex-row items-center gap-20">
        <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-white rounded-full blur-[100px] -translate-y-1/2 translate-x-1/2 -z-10"></div>
        
        <div class="lg:w-1/2 space-y-12">
          <div class="w-20 h-2 bg-indigo-600 rounded-full"></div>
          <h2 class="text-4xl md:text-6xl font-black tracking-tight font-heading leading-tight">Crafted for the <br/>exceptional <span class="text-indigo-600">minority.</span></h2>
          <p class="text-lg text-slate-500 leading-relaxed max-w-lg">We don't mass produce. Each unit is individually calibrated, tested, and certified. It's not a product; it's a statement of values.</p>
          
          <div class="grid grid-cols-2 gap-12 pt-12 border-t border-slate-200">
            <div v-for="stat in ['3.2s', '98%', '850', '2.5kg']" :key="stat" class="space-y-2">
              <div class="text-3xl font-black tracking-tighter">{{ stat }}</div>
              <div class="text-[10px] font-black uppercase tracking-widest text-slate-400">Precision Metric</div>
            </div>
          </div>
        </div>

        <div class="lg:w-1/2 relative group">
          <div class="absolute inset-0 bg-indigo-600/5 rounded-full blur-[60px] group-hover:bg-indigo-600/10 transition-colors duration-1000"></div>
          <img :src="product.image" class="relative z-10 w-full h-auto drop-shadow-2xl group-hover:scale-110 transition-transform duration-1000" />
        </div>
      </div>
    </section>

    <!-- Scroll Gallery -->
    <section class="py-32">
      <div class="flex overflow-x-auto gap-8 px-8 no-scrollbar scroll-smooth snap-x">
        <div v-for="i in 5" :key="i" class="min-w-[80vw] md:min-w-[40vw] aspect-[4/5] bg-slate-100 rounded-[3rem] overflow-hidden snap-center group">
          <img :src="product.image" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000" />
        </div>
      </div>
    </section>

    <!-- Action Section -->
    <section class="bg-black text-white py-32 mt-32 relative overflow-hidden">
      <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-px h-full bg-white/10"></div>
      </div>
      <div class="container mx-auto px-6 text-center space-y-16 relative z-10">
        <h2 class="text-5xl md:text-8xl font-black tracking-tighter font-heading">Secure yours.</h2>
        <div class="flex flex-col items-center gap-8">
          <div class="flex gap-4">
            <div v-for="color in ['#fff', '#222', '#6366f1']" :key="color" class="w-16 h-16 rounded-full border-2 border-white/20 p-1 cursor-pointer hover:border-white transition-colors">
              <div class="w-full h-full rounded-full" :style="{ background: color }"></div>
            </div>
          </div>
          <button class="px-20 py-8 bg-white text-black rounded-[2rem] font-black uppercase tracking-[0.2em] text-sm hover:bg-indigo-400 hover:text-white transition-all duration-500 scale-100 hover:scale-105 active:scale-95 shadow-2xl shadow-indigo-500/20">
            {{ settings.cta_text || 'Confirm Acquisition' }}
          </button>
          <p class="text-white/40 text-[10px] font-bold uppercase tracking-[0.4em]">Guaranteed Express Global Shipping</p>
        </div>
      </div>
    </section>

    <!-- Sticky Bottom Buy Bar (Shows on Scroll) -->
    <div class="fixed bottom-10 left-1/2 -translate-x-1/2 z-50 w-full max-w-sm px-4">
      <div class="bg-black/80 backdrop-blur-xl border border-white/10 p-4 rounded-[2rem] flex items-center justify-between shadow-2xl">
        <div class="pl-4">
          <p class="text-white font-black tracking-tight">{{ product.name }}</p>
          <p class="text-indigo-400 text-[10px] font-bold uppercase tracking-widest">${{ product.price }}</p>
        </div>
        <button class="px-8 py-4 bg-white text-black rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-indigo-500 hover:text-white transition-colors">
          Buy
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  data: {
    type: Object,
    required: true
  }
})

const product = computed(() => props.data.products?.[0] || {})
const vendor = computed(() => props.data.vendor || {})
const settings = computed(() => props.data.landingPage?.settings || {})
</script>

<style scoped>

.product-immersive-v3 {
  font-family: 'Outfit', sans-serif;
  background-color: #ffffff;
  position: relative;
  z-index: 10;
}

.font-heading {
  font-family: 'Outfit', sans-serif;
}

@keyframes slowZoom {
  0% { transform: scale(1.1); }
  100% { transform: scale(1.2); }
}

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-slow-zoom {
  animation: slowZoom 20s ease-in-out infinite alternate;
}

.animate-fade-in-up {
  animation: fadeInUp 1.5s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
}

.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
