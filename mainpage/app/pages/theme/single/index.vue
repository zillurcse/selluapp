<template>
  <div class="single-product-premium text-white min-h-screen font-sans selection:bg-indigo-500 selection:text-white">
    <!-- Smooth Navigation -->
    <nav class="fixed top-0 left-0 w-full z-50 px-6 py-8 flex justify-between items-center">
      <NuxtLink to="/" class="group flex items-center gap-3">
        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center group-hover:rotate-[15deg] transition-all duration-500 shadow-xl shadow-white/10">
          <span class="text-black font-black text-2xl">S</span>
        </div>
        <div class="flex flex-col">
          <span class="text-white font-black tracking-tighter text-2xl leading-none">SELLUEE</span>
          <span class="text-indigo-500 text-[10px] font-black uppercase tracking-[0.3em]">Premium</span>
        </div>
      </NuxtLink>
      
      <div class="flex items-center gap-8">
        <button class="text-sm font-bold uppercase tracking-widest hover:text-indigo-400 transition-colors">Search</button>
        <button class="relative group">
          <div class="absolute -top-1 -right-1 w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></div>
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        </button>
        <button class="w-10 h-10 rounded-full bg-white/5 border border-white/10 flex items-center justify-center hover:bg-white/10 transition-all">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
      </div>
    </nav>

    <!-- Hero Showcase Section -->
    <section class="relative min-h-screen flex flex-col lg:flex-row items-center pt-24 pb-12 px-6 lg:px-20 gap-16 overflow-hidden">
      <!-- Background Glow -->
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[80vw] h-[80vw] bg-indigo-500/10 rounded-full blur-[180px] -z-10 pointer-events-none"></div>
      <div class="absolute -top-40 -right-40 w-96 h-96 bg-rose-500/5 rounded-full blur-[120px] -z-10 pointer-events-none animate-pulse"></div>

      <!-- Left: Columnar Info & Gallery -->
      <div class="w-full lg:w-1/2 order-2 lg:order-1 flex flex-col justify-center gap-12 sm:gap-16">
        <div class="space-y-6 animate-slide-up" style="animation-delay: 0.1s">
          <div class="flex items-center gap-4">
            <span class="px-4 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-[10px] font-black uppercase tracking-[0.2em]">New Release</span>
            <div class="flex items-center gap-1 text-amber-400">
              <svg v-for="i in 5" :key="i" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
              <span class="text-[10px] text-white/40 ml-2 font-bold uppercase tracking-wider">4.9 (1,248)</span>
            </div>
          </div>
          
          <h1 class="text-5xl sm:text-7xl font-extrabold tracking-tighter leading-[1] text-white font-heading">
            {{ product.name }}<br/>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-rose-400 to-amber-400">Pro Edition.</span>
          </h1>
          
          <p class="text-white/50 text-base md:text-lg max-w-xl leading-relaxed font-light">
            {{ product.description }}
          </p>
        </div>

        <!-- Variant & Action -->
        <div class="space-y-10 animate-slide-up" style="animation-delay: 0.3s">
          <div class="flex flex-wrap gap-12 items-end">
            <div>
              <h3 class="text-[10px] font-bold uppercase tracking-[0.3em] text-white/30 mb-5">Colorway</h3>
              <div class="flex gap-4">
                <button 
                  v-for="color in colors" 
                  :key="color.hex"
                  @click="activeColor = color.hex"
                  class="relative w-12 h-12 rounded-2xl group transition-all duration-500 flex items-center justify-center shadow-lg shadow-black/20"
                  :style="{ background: color.hex }"
                >
                  <div 
                    class="absolute -inset-2 rounded-[1.25rem] border-2 transition-all duration-700"
                    :class="activeColor === color.hex ? 'border-white/20 scale-100 opacity-100' : 'border-transparent scale-50 opacity-0 group-hover:scale-75 group-hover:opacity-40 group-hover:border-white/10'"
                  ></div>
                  <div v-if="activeColor === color.hex" class="w-1.5 h-1.5 rounded-full bg-white shadow-[0_0_10px_2px_rgba(255,255,255,0.8)]"></div>
                </button>
              </div>
            </div>

            <div class="flex-1 min-w-[200px]">
              <h3 class="text-[10px] font-bold uppercase tracking-[0.3em] text-white/30 mb-5">Investment</h3>
              <div class="flex items-baseline gap-4">
                <span class="text-4xl font-extrabold tracking-tighter">${{ product.price }}</span>
                <span class="text-white/20 line-through text-lg font-bold">${{ (parseFloat(product.price) * 1.25).toFixed(2) }}</span>
              </div>
            </div>
          </div>

          <div class="flex flex-col sm:flex-row gap-4 items-stretch">
            <div class="flex items-center justify-between p-2 rounded-2xl bg-white/5 border border-white/10 min-w-[140px]">
              <button 
                @click="quantity = Math.max(1, quantity - 1)"
                class="w-10 h-10 rounded-xl flex items-center justify-center hover:bg-white/10 transition-colors text-white/40 hover:text-white"
              >−</button>
              <span class="font-black text-xl w-8 text-center">{{ quantity }}</span>
              <button 
                @click="quantity++"
                class="w-10 h-10 rounded-xl flex items-center justify-center hover:bg-white/10 transition-colors text-white/40 hover:text-white"
              >+</button>
            </div>
            
            <button class="flex-1 py-5 rounded-2xl bg-white text-black font-black uppercase tracking-[0.2em] text-sm hover:scale-[1.02] active:scale-[0.98] transition-all duration-500 shadow-[0_20px_40px_-10px_rgba(255,255,255,0.1)] flex items-center justify-center gap-3 group">
              Add To Collection
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="translate-x-0 group-hover:translate-x-2 transition-transform"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </button>

            <button class="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center group hover:bg-rose-500/10 hover:border-rose-500/20 transition-all duration-500">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white/40 group-hover:text-rose-500 transition-colors"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Right: Main Image & Floating Specs -->
      <div class="w-full lg:w-1/2 order-1 lg:order-2 relative flex items-center justify-center animate-fade-in" style="animation-delay: 0.5s">
        <div class="relative w-full max-w-[600px] aspect-square flex items-center justify-center group">
          <!-- Geometric Background -->
          <div class="absolute inset-0 bg-white/[0.02] rounded-[4rem] border border-white/[0.05] -rotate-6 scale-95 group-hover:rotate-0 group-hover:scale-100 transition-all duration-1000"></div>
          <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 to-transparent rounded-[4rem] rotate-3 scale-95 group-hover:rotate-6 group-hover:scale-110 transition-all duration-1000"></div>
          
          <!-- Image -->
          <img 
            :src="product.image" 
            class="relative z-10 w-[85%] h-auto drop-shadow-[0_40px_80px_rgba(0,0,0,0.5)] group-hover:scale-110 group-hover:-translate-y-6 transition-all duration-1000 ease-out"
          />

          <!-- Floating Data Points -->
          <div 
            v-for="(point, idx) in floatingPoints" 
            :key="idx"
            class="absolute z-20 hidden md:flex items-center gap-3 p-3 rounded-2xl bg-black/60 backdrop-blur-xl border border-white/10 shadow-2xl animate-float"
            :style="{ 
              top: point.y + '%', 
              left: point.x + '%', 
              animationDelay: (idx * 1.5) + 's' 
            }"
          >
            <div class="w-8 h-8 rounded-lg bg-indigo-500/20 flex items-center justify-center text-indigo-400">
              <span class="text-xs font-black">{{ point.label[0] }}</span>
            </div>
            <div class="pr-2">
              <div class="text-[10px] font-black uppercase tracking-widest text-white/40">{{ point.label }}</div>
              <div class="text-xs font-bold">{{ point.value }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Key Features Grid (High Impact) -->
    <section class="py-32 px-6 lg:px-20 relative overflow-hidden">
      <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 sm:gap-12">
          <div v-for="(feature, idx) in keyFeatures" :key="idx" 
            class="group p-10 rounded-[3rem] bg-white/[0.03] border border-white/[0.05] hover:bg-white/[0.06] hover:border-white/10 transition-all duration-700 relative overflow-hidden flex flex-col gap-8 animate-slide-up"
            :style="{ animationDelay: (0.4 + idx * 0.1) + 's' }"
          >
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-indigo-500/5 blur-3xl rounded-full group-hover:bg-indigo-500/10 transition-colors duration-700"></div>
            
            <div class="w-16 h-16 rounded-2xl bg-indigo-500/10 flex items-center justify-center text-indigo-400 group-hover:scale-110 group-hover:rotate-12 transition-all duration-700">
              <div v-html="feature.icon" class="w-8 h-8"></div>
            </div>
            
            <div class="space-y-4">
              <h3 class="text-2xl font-black tracking-tight font-heading">{{ feature.title }}</h3>
              <p class="text-white/40 leading-relaxed font-medium">{{ feature.desc }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Detailed Tech Specs Section -->
    <section class="py-32 px-6 lg:px-20 bg-white/[0.02] border-y border-white/[0.05]">
      <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-20">
        <div class="lg:w-1/3 flex flex-col justify-center">
          <span class="text-indigo-400 text-[10px] font-black uppercase tracking-[0.4em] mb-6">Technical Data</span>
          <h2 class="text-4xl md:text-5xl font-black mb-10 font-heading leading-tight tracking-tighter">Engineered for<br/><span class="text-transparent bg-clip-text bg-gradient-to-r from-white to-white/40">Performance.</span></h2>
          <p class="text-white/40 text-lg leading-relaxed font-light mb-12">Every specification is benchmarked against industry standards to ensure you get the absolute best-in-class experience.</p>
          
          <button class="flex items-center gap-4 text-xs font-black uppercase tracking-widest text-white hover:text-indigo-400 transition-colors group">
            Download Tech Sheet (PDF)
            <div class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center group-hover:border-indigo-400 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M12 17V3"/><path d="m6 11 6 6 6-6"/><path d="M19 21H5"/></svg>
            </div>
          </button>
        </div>

        <div class="lg:w-2/3 grid grid-cols-1 sm:grid-cols-2 gap-x-12 gap-y-1">
          <div v-for="(val, label) in specifications" :key="label" class="py-8 border-b border-white/[0.05] group">
            <div class="flex flex-col gap-2">
              <span class="text-[10px] font-black uppercase tracking-widest text-white/20 group-hover:text-indigo-400 transition-colors">{{ label }}</span>
              <span class="text-lg font-bold tracking-tight text-white/80 group-hover:text-white transition-colors">{{ val }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Narrative Gallery Layer -->
    <section class="py-32 px-6 lg:px-20">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch h-auto lg:h-[800px]">
        <div class="lg:col-span-8 group relative rounded-[4rem] overflow-hidden bg-white/[0.02] border border-white/[0.05]">
          <img :src="product.image" class="w-full h-full object-cover scale-110 group-hover:scale-100 group-hover:rotate-1 transition-all duration-1000 opacity-60 group-hover:opacity-100" />
          <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent"></div>
          <div class="absolute bottom-12 left-12 max-w-md">
            <h3 class="text-4xl font-black mb-4 font-heading">Immersive Experience.</h3>
            <p class="text-white/60 text-lg">Designed to fit perfectly within your ecosystem, providing both aesthetic appeal and raw power.</p>
          </div>
        </div>
        <div class="lg:col-span-4 flex flex-col gap-8">
          <div class="flex-1 group relative rounded-[3rem] overflow-hidden bg-white/[0.02] border border-white/[0.05]">
            <img :src="product.image" class="w-full h-full object-cover opacity-40 group-hover:scale-110 transition-transform duration-1000" />
            <div class="absolute inset-0 flex items-center justify-center">
              <div class="w-20 h-20 rounded-full bg-indigo-500 flex items-center justify-center shadow-2xl scale-90 group-hover:scale-100 transition-transform duration-500 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="5 3 19 12 5 21 5 3"/></svg>
              </div>
            </div>
          </div>
          <div class="p-10 rounded-[3rem] bg-indigo-600 flex flex-col justify-end gap-6 group hover:translate-y-[-10px] transition-transform duration-500">
            <div class="text-5xl">🏢</div>
            <h3 class="text-2xl font-black font-heading leading-tight">Visit our flagship <br/>studio for a demo.</h3>
            <button class="w-full py-4 rounded-2xl bg-white text-indigo-600 font-black uppercase tracking-widest text-xs hover:bg-black hover:text-white transition-colors">Book Concierge</button>
          </div>
        </div>
      </div>
    </section>

    <!-- Modern Footer Tab Bar (Sticky Style) -->
    <div class="fixed bottom-8 left-1/2 -translate-x-1/2 z-40 px-4 w-full max-w-2xl">
      <div class="bg-black/40 backdrop-blur-2xl border border-white/10 rounded-full p-2 flex items-center justify-around shadow-2xl shadow-indigo-500/10">
        <button 
          v-for="tab in ['Overview', 'Gallery', 'Features', 'Specs']" 
          :key="tab"
          @click="activeNavTab = tab"
          class="px-6 py-3 rounded-full text-[10px] font-black uppercase tracking-widest transition-all"
          :class="activeNavTab === tab ? 'bg-white text-black' : 'text-white/40 hover:text-white'"
        >
          {{ tab }}
        </button>
        <div class="w-px h-6 bg-white/10 mx-2"></div>
        <button class="bg-indigo-500 px-6 py-3 rounded-full text-[10px] font-black uppercase tracking-widest hover:bg-indigo-400 transition-colors">
          Buy Now — ${{ product.price }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import watchImg from '~/assets/img/watch.png'

const quantity = ref(1)
const activeColor = ref('#1a1a1a')
const activeNavTab = ref('Overview')

const colors = [
  { name: 'Obsidian', hex: '#1a1a1a' },
  { name: 'Silver Slate', hex: '#64748b' },
  { name: 'Indigo Night', hex: '#4338ca' },
]

const product = {
  name: 'Quantum X-1',
  price: '499.00',
  description: 'A masterpiece of minimalist engineering. The Quantum X-1 redefines the intersection of tactile feedback and digital precision. Crafted from aerospace-grade titanium and finished with sapphire glass, it is as durable as it is beautiful.',
  image: watchImg
}

const keyFeatures = [
  { 
    title: 'Adaptive Sync', 
    desc: 'Intelligent state-management that adapts to your workflow patterns in real-time.',
    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>'
  },
  { 
    title: 'Titanium Shell', 
    desc: 'Unibody construction milled from a single block of Grade-5 titanium for absolute rigidity.',
    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>'
  },
  { 
    title: '90h Persistence', 
    desc: 'Extended energy density cells provide industry-leading uptime without the bulk.',
    icon: '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'
  }
]

const specifications = {
  'Processor': 'Custom XM-2 Silicon',
  'Memory': '16GB Integrated Unified Storage',
  'Display': '1.9" Dynamic LTPO AMOLED (2000 nits)',
  'Connectivity': 'WiFi 6E, Bluetooth 5.3, Ultra-Wideband',
  'Sensors': 'Precision SpO2, ECG, Altimeter, Gyro',
  'Durability': 'Water resistant up to 100m (ISO 22810)',
}

const floatingPoints = [
  { x: 15, y: 20, label: 'Materials', value: 'Aerospace Titanium' },
  { x: 75, y: 30, label: 'Precision', value: '2ms Tactile Lag' },
  { x: 25, y: 80, label: 'Optics', value: 'Sapphire Crystal' },
]
</script>

<style scoped>

.font-heading {
  font-family: 'Outfit', sans-serif;
}

.single-product-premium {
  font-family: 'Outfit', sans-serif;
  scroll-behavior: smooth;
  background-color: #0a0a0b;
  position: relative;
  z-index: 10;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes float {
  0% { transform: translateY(0px); }
  50% { transform: translateY(-15px); }
  100% { transform: translateY(0px); }
}

.animate-slide-up {
  opacity: 0;
  animation: slideUp 1.2s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
}

.animate-fade-in {
  opacity: 0;
  animation: fadeIn 1.5s ease forwards;
}

.animate-float {
  animation: float 4s ease-in-out infinite;
}

/* Hide scrollbar but keep functionality */
.single-product-premium::-webkit-scrollbar {
  display: none;
}
.single-product-premium {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
