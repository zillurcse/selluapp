<template>
  <div class="single-product-v2 bg-[#f8f9fa] text-slate-900 min-h-screen font-sans selection:bg-black selection:text-white pb-20">
    <!-- Clean Minimal Header -->
    <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-100 px-6 py-4 flex justify-between items-center animate-fade-in">
      <div class="flex items-center gap-12">
        <NuxtLink to="/" class="text-xl font-black uppercase tracking-tighter">Elluee</NuxtLink>
        <nav class="hidden md:flex gap-8 text-xs font-bold uppercase tracking-widest text-slate-400">
          <NuxtLink to="/shop" class="hover:text-black transition-colors">Catalog</NuxtLink>
          <NuxtLink to="/collections" class="hover:text-black transition-colors">Series</NuxtLink>
          <NuxtLink to="/journal" class="hover:text-black transition-colors">Journal</NuxtLink>
        </nav>
      </div>
      <div class="flex items-center gap-6">
        <button class="w-10 h-10 rounded-full hover:bg-slate-100 flex items-center justify-center transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        </button>
        <button class="relative w-10 h-10 rounded-full bg-black text-white flex items-center justify-center hover:scale-105 transition-transform">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
          <span class="absolute -top-1 -right-1 w-4 h-4 bg-rose-500 rounded-full text-[8px] font-black flex items-center justify-center border-2 border-white">2</span>
        </button>
      </div>
    </header>

    <!-- Main Layout -->
    <main class="container mx-auto px-6 py-12 lg:py-20">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-24 items-start">
        
        <!-- Sticky Image Gallery -->
        <div class="lg:sticky lg:top-32 space-y-4">
          <div class="relative aspect-square rounded-[2rem] overflow-hidden bg-white shadow-2xl shadow-slate-200 group">
            <div class="absolute top-6 right-6 z-10">
              <button class="w-12 h-12 rounded-full bg-white/60 backdrop-blur-md flex items-center justify-center text-slate-900 border border-white opacity-0 group-hover:opacity-100 transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
              </button>
            </div>
            <img :src="product.image" class="w-full h-full object-contain p-12 transition-transform duration-1000 group-hover:scale-110" />
            
            <!-- Zoom Indicator -->
            <div class="absolute bottom-6 left-6 text-[10px] font-bold uppercase tracking-widest text-slate-300 pointer-events-none">Hover to Inspect</div>
          </div>
          
          <div class="grid grid-cols-4 gap-4 px-2">
            <div v-for="i in 4" :key="i" 
              class="aspect-square rounded-2xl bg-white border-2 cursor-pointer transition-all p-4 hover:border-black"
              :class="activeImg === i ? 'border-black' : 'border-transparent'"
              @click="activeImg = i"
            >
              <img :src="product.image" class="w-full h-full object-contain" />
            </div>
          </div>
        </div>

        <!-- Product Details -->
        <div class="space-y-12 py-4">
          <div class="space-y-6 animate-slide-up">
            <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
              <NuxtLink to="/shop" class="hover:text-black">Catalog</NuxtLink>
              <span>/</span>
              <span class="text-black">Essentials</span>
            </nav>
            
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6">
              <h1 class="text-4xl md:text-5xl font-black tracking-tight text-slate-900 leading-none">{{ product.name }}</h1>
              <div class="text-3xl font-bold tracking-tighter">${{ product.price }}</div>
            </div>

            <div class="flex items-center gap-6 py-4 border-y border-slate-100">
              <div class="flex items-center gap-2">
                <span class="text-sm font-bold">4.8</span>
                <div class="flex text-amber-400">
                  <svg v-for="i in 4" :key="i" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" class="text-slate-200"><path d="M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                </div>
              </div>
              <div class="w-1.5 h-1.5 rounded-full bg-slate-200"></div>
              <button class="text-xs font-bold text-slate-400 underline underline-offset-4 hover:text-black transition-colors">84 Reviews</button>
            </div>
          </div>

          <!-- Configuration -->
          <div class="space-y-10 animate-slide-up" style="animation-delay: 0.1s">
            <!-- Material Selector -->
            <div>
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-xs font-black uppercase tracking-[0.2em]">Material</h3>
                <span class="text-xs font-bold text-slate-400">{{ activeMaterial }}</span>
              </div>
              <div class="flex flex-wrap gap-3">
                <button 
                  v-for="mat in ['Brushed Steel', 'Raw Concrete', 'Matte Black']" 
                  :key="mat"
                  @click="activeMaterial = mat"
                  class="px-6 py-3 rounded-xl text-xs font-bold border-2 transition-all"
                  :class="activeMaterial === mat ? 'border-black bg-black text-white shadow-xl translate-y-[-2px]' : 'border-slate-100 bg-white text-slate-400 hover:border-slate-300'"
                >
                  {{ mat }}
                </button>
              </div>
            </div>

            <!-- Size Selector -->
            <div>
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-xs font-black uppercase tracking-[0.2em]">Scale</h3>
                <button class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest hover:underline">Size Guide</button>
              </div>
              <div class="grid grid-cols-3 gap-3">
                <button 
                  v-for="size in ['Compact', 'Standard', 'Architect']" 
                  :key="size"
                  @click="activeSize = size"
                  class="py-4 rounded-xl text-[10px] font-black uppercase tracking-widest border-2 transition-all flex flex-col items-center gap-1"
                  :class="activeSize === size ? 'border-black bg-black text-white' : 'border-slate-100 bg-white text-slate-400 hover:border-slate-300'"
                >
                  {{ size }}
                </button>
              </div>
            </div>

            <!-- Quantitative & Primary Add -->
            <div class="flex gap-4 pt-4">
              <div class="inline-flex items-center gap-6 px-6 py-4 rounded-2xl bg-white border-2 border-slate-100">
                <button @click="quantity = Math.max(1, quantity - 1)" class="text-xl font-bold text-slate-300 hover:text-black transition-colors">-</button>
                <span class="text-sm font-black w-4 text-center">{{ quantity }}</span>
                <button @click="quantity++" class="text-xl font-bold text-slate-300 hover:text-black transition-colors">+</button>
              </div>
              <button class="flex-1 bg-black text-white rounded-2xl py-4 font-black uppercase tracking-[0.2em] text-xs shadow-2xl shadow-slate-300 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-3">
                Acquire Piece
                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
              </button>
            </div>
          </div>

          <!-- Features Accordion -->
          <div class="space-y-4 animate-slide-up" style="animation-delay: 0.2s">
            <div 
              v-for="(feat, idx) in details" 
              :key="idx" 
              class="border-b border-slate-100 overflow-hidden"
            >
              <button 
                @click="openIdx = (openIdx === idx ? null : idx)"
                class="w-full flex items-center justify-between py-6 text-left group"
              >
                <span class="text-sm font-black uppercase tracking-widest group-hover:text-amber-600 transition-colors">{{ feat.title }}</span>
                <div class="w-6 h-6 rounded-full border border-slate-200 flex items-center justify-center transition-transform duration-500" :class="openIdx === idx ? 'rotate-180 bg-slate-100' : ''">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </div>
              </button>
              <div v-if="openIdx === idx" class="pb-6 animate-fade-in">
                <p class="text-sm text-slate-500 leading-relaxed max-w-lg">{{ feat.content }}</p>
              </div>
            </div>
          </div>

          <!-- Trust Banner -->
          <div class="p-8 rounded-[2.5rem] bg-indigo-50 border border-indigo-100 flex flex-col sm:flex-row gap-8 sm:items-center animate-slide-up" style="animation-delay: 0.3s">
            <div class="flex -space-x-3">
              <div v-for="i in 3" :key="i" class="w-10 h-10 rounded-full border-2 border-white bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600">
                <span v-if="i === 1">👨‍🎨</span>
                <span v-if="i === 2">👩‍🏭</span>
                <span v-if="i === 3">🚛</span>
              </div>
            </div>
            <div class="flex-1">
              <p class="text-xs font-black uppercase tracking-widest text-indigo-900 mb-1">Lifetime Assurance</p>
              <p class="text-[10px] text-indigo-600 font-bold">100% Hand-crafted in Scandinavia. 30-day trial period included.</p>
            </div>
            <NuxtLink to="/shipping" class="text-[10px] font-black uppercase tracking-widest text-indigo-900 border-b-2 border-indigo-200 pb-1 hover:border-indigo-500 transition-all">Details</NuxtLink>
          </div>
        </div>
      </div>
    </main>

    <!-- Product Narrative Section -->
    <section class="mt-20 py-32 bg-white relative overflow-hidden">
      <div class="container mx-auto px-6">
        <div class="max-w-4xl mx-auto text-center space-y-12">
          <span class="text-[10px] font-black uppercase tracking-[0.4em] text-slate-300">The Genesis</span>
          <h2 class="text-5xl md:text-7xl font-black tracking-tighter text-slate-900">Where geometry meets <br/>human touch.</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-12 text-left mt-20">
            <p class="text-lg text-slate-500 leading-relaxed font-medium italic border-l-4 border-amber-400 pl-8"> "We wanted to create a silhouette that looked like it was carved by the wind, yet possessed the precision of modern machinery."</p>
            <p class="text-lg text-slate-500 leading-relaxed">The Essence collection represents our commitment to raw aesthetics. We've removed everything unnecessary, leaving only the pure soul of the material. Each piece is individually numbered and signed by the artisan.</p>
          </div>
        </div>
      </div>
      
      <!-- Horizontal Marquee -->
      <div class="mt-32 w-full overflow-hidden border-y border-slate-100 py-10 rotate-1">
        <div class="flex gap-20 animate-marquee whitespace-nowrap">
          <span v-for="i in 10" :key="i" class="text-6xl font-black text-slate-100 uppercase tracking-tighter">{{ product.name }} • ESSENCE SERIES</span>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import watchImg from '~/assets/img/watch.png'

const product = {
  name: 'Essence No. 4',
  price: '245.00',
  image: watchImg
}

const activeImg = ref(1)
const activeMaterial = ref('Brushed Steel')
const activeSize = ref('Standard')
const quantity = ref(1)
const openIdx = ref(0)

const details = [
  { title: 'Dimensions & Weight', content: 'Height: 145mm | Width: 80mm | Depth: 80mm. Weight: 1.2kg solid core construction.' },
  { title: 'Sourcing & Ethicality', content: 'Our steel is 100% recycled from automotive industry waste. Hand-polished using water-based compounds in our Copenhagen studio.' },
  { title: 'Global Delivery', content: 'Ships in 100% plastic-free packaging. Standard shipping: 3-5 days. Express: 24h available.' }
]
</script>

<style scoped>

.single-product-v2 {
  font-family: 'Outfit', sans-serif;
  background-color: #f8f9fa;
  position: relative;
  z-index: 10;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes marquee {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}

.animate-fade-in {
  animation: fadeIn 1s ease forwards;
}

.animate-slide-up {
  opacity: 0;
  animation: slideUp 1s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
}

.animate-marquee {
  animation: marquee 40s linear infinite;
  width: max-content;
}
</style>
