<template>
  <div class="multiple-product-theme text-white min-h-screen font-sans selection:bg-rose-500 selection:text-white overflow-x-hidden">
    <!-- Premium Navigation -->
    <nav class="fixed top-0 left-0 w-full z-50 px-6 py-8 flex justify-between items-center transition-all duration-300" :class="{ 'bg-black/80 backdrop-blur-xl border-b border-white/5 py-4': scrolled }">
      <NuxtLink to="/" class="group flex items-center gap-3">
        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center group-hover:rotate-[15deg] transition-all duration-500 shadow-xl shadow-white/10">
          <span class="text-black font-black text-2xl">S</span>
        </div>
        <div class="flex flex-col">
          <span class="text-white font-black tracking-tighter text-2xl leading-none">SELLUEE</span>
          <span class="text-rose-500 text-[10px] font-black uppercase tracking-[0.3em]">Signature</span>
        </div>
      </NuxtLink>
      
      <div class="hidden md:flex items-center gap-10">
        <a v-for="link in navLinks" :key="link" href="#" class="text-[10px] font-bold uppercase tracking-[0.3em] text-white/40 hover:text-white transition-colors">{{ link }}</a>
      </div>

      <div class="flex items-center gap-6">
        <button class="relative group p-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="group-hover:stroke-rose-500 transition-colors"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        </button>
        <button class="relative group bg-white/5 border border-white/10 p-3 rounded-2xl hover:bg-rose-500/10 hover:border-rose-500/20 transition-all">
          <span class="absolute -top-1 -right-1 w-5 h-5 bg-rose-500 rounded-full flex items-center justify-center text-[10px] font-black">2</span>
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        </button>
      </div>
    </nav>

    <!-- Dynamic Hero Section -->
    <header class="relative pt-40 pb-20 px-6 lg:px-20 min-h-[70vh] flex flex-col items-center justify-center text-center overflow-hidden">
      <!-- Ambient Background -->
      <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full -z-10">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[120vw] h-[120vw] bg-rose-600/5 rounded-full blur-[150px] animate-pulse"></div>
        <div class="absolute top-0 right-0 w-[50vw] h-[50vw] bg-indigo-600/5 rounded-full blur-[100px]"></div>
      </div>

      <div class="max-w-4xl space-y-8 animate-slide-up">
        <span class="px-5 py-2 rounded-full bg-white/5 border border-white/10 text-rose-400 text-[10px] font-black uppercase tracking-[0.4em] inline-block">SS2026 Collection</span>
        <h1 class="text-6xl md:text-8xl font-black tracking-[ -0.05em] leading-[0.9] font-heading">
          ELEVATE YOUR <br/>
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-white/40 to-white/20">AESTHETIC.</span>
        </h1>
        <p class="text-white/40 text-lg md:text-xl max-w-2xl mx-auto font-light leading-relaxed">
          A definitive selection of industrial design excellence. Each piece curated for its unique contribution to the modern workspace and living environment.
        </p>
        <div class="flex items-center justify-center gap-6 pt-6">
          <button class="px-10 py-5 rounded-2xl bg-white text-black font-black uppercase tracking-[0.2em] text-xs hover:scale-105 active:scale-95 transition-all shadow-2xl shadow-white/5">Explore All</button>
          <button class="px-10 py-5 rounded-2xl bg-white/5 border border-white/10 text-white font-black uppercase tracking-[0.2em] text-xs hover:bg-white/10 transition-all">Our Story</button>
        </div>
      </div>
    </header>

    <!-- 4 Products Grid Showcase -->
    <section class="py-20 px-6 lg:px-20 relative">
      <div class="max-w-[1600px] mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-16">
          <!-- Product Card Loop -->
          <div 
            v-for="(prod, idx) in showcaseProducts" 
            :key="idx"
            class="group relative aspect-[4/5] rounded-[3rem] overflow-hidden bg-white/[0.02] border border-white/5 p-12 flex flex-col justify-between hover:bg-white/[0.04] hover:border-white/10 transition-all duration-1000 animate-slide-up"
            :style="{ animationDelay: (0.2 + idx * 0.1) + 's' }"
          >
            <!-- Background Glow -->
            <div 
              class="absolute -right-20 -top-20 w-80 h-80 blur-[100px] rounded-full opacity-0 group-hover:opacity-20 transition-opacity duration-1000"
              :style="{ backgroundColor: prod.themeColor }"
            ></div>

            <div class="relative z-10 flex justify-between items-start">
              <div class="space-y-2">
                <span class="text-[10px] font-black uppercase tracking-[0.3em] text-white/30">{{ prod.category }}</span>
                <h3 class="text-3xl lg:text-4xl font-black font-heading tracking-tighter">{{ prod.name }}</h3>
              </div>
              <div class="text-2xl font-black font-heading mt-1">${{ prod.price }}</div>
            </div>

            <!-- Product Image -->
            <div class="relative flex-1 flex items-center justify-center py-10">
              <img 
                :src="prod.image" 
                class="w-[80%] h-auto object-contain drop-shadow-[0_30px_60px_rgba(0,0,0,0.5)] group-hover:scale-110 group-hover:-translate-y-8 transition-all duration-1000 ease-out" 
              />
              
              <!-- Interactive Hotspots (Hidden on Desktop, shown on Hover) -->
              <div v-if="prod.specs" class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none">
                 <div v-for="(spec, sidx) in prod.specs" :key="sidx" 
                   class="absolute px-4 py-2 rounded-xl bg-black/60 backdrop-blur-md border border-white/10 text-[10px] font-bold uppercase tracking-widest whitespace-nowrap animate-float"
                   :style="{ top: spec.y + '%', left: spec.x + '%', animationDelay: (sidx * 0.5) + 's' }"
                 >
                   {{ spec.label }}
                 </div>
              </div>
            </div>

            <div class="relative z-10 flex items-center justify-between">
              <button class="text-[10px] font-black uppercase tracking-[0.3em] text-white/40 hover:text-white transition-colors flex items-center gap-2 group/btn">
                View Details
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="translate-x-0 group-hover/btn:translate-x-2 transition-transform"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
              </button>
              
              <div class="flex gap-2">
                 <button class="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-white text-white hover:text-black transition-all">
                   <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                 </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Newsletter / CTA -->
    <section class="py-40 px-6 lg:px-20 overflow-hidden relative">
      <div class="max-w-7xl mx-auto rounded-[4rem] bg-gradient-to-br from-white/[0.05] to-transparent border border-white/10 p-12 lg:p-24 relative overflow-hidden text-center">
        <div class="absolute -right-40 -bottom-40 w-96 h-96 bg-rose-500/10 blur-[100px] rounded-full"></div>
        
        <div class="relative z-10 space-y-8">
          <h2 class="text-4xl lg:text-6xl font-black font-heading tracking-tighter">Stay Ahead of the Curve.</h2>
          <p class="text-white/40 text-lg max-w-xl mx-auto font-light">Join 10k+ collectors receiving early access to our limited series drops and industrial insights.</p>
          
          <div class="flex flex-col sm:flex-row gap-4 max-w-lg mx-auto pt-6">
            <input type="email" placeholder="Email Address" class="flex-1 px-8 py-5 rounded-2xl bg-white/5 border border-white/10 text-white placeholder:text-white/20 focus:outline-none focus:border-white/30 transition-all font-bold" />
            <button class="px-10 py-5 rounded-2xl bg-white text-black font-black uppercase tracking-[0.2em] text-xs hover:bg-rose-500 hover:text-white transition-all">Join Club</button>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="py-20 px-6 lg:px-20 border-t border-white/5 bg-black">
      <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-12">
        <div class="flex flex-col items-center md:items-start gap-4">
          <NuxtLink to="/" class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center">
              <span class="text-black font-black text-xl">S</span>
            </div>
            <span class="text-white font-black tracking-tighter text-xl">SELLUEE</span>
          </NuxtLink>
          <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-white/20">© 2026 Selluee Limited. All Rights Reserved.</p>
        </div>
        
        <div class="flex gap-12">
          <div v-for="(links, category) in footerLinks" :key="category" class="flex flex-col gap-4">
            <h4 class="text-[10px] font-black uppercase tracking-[0.4em] text-white/40">{{ category }}</h4>
            <a v-for="link in links" :key="link" href="#" class="text-[10px] font-bold uppercase tracking-[0.2em] text-white/60 hover:text-white transition-colors">{{ link }}</a>
          </div>
        </div>
        
        <div class="flex gap-4">
          <button v-for="i in 4" :key="i" class="w-12 h-12 rounded-full border border-white/10 flex items-center justify-center hover:bg-white/5 transition-colors">
            <div class="w-4 h-4 bg-white/20 rounded-sm"></div>
          </button>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

definePageMeta({
  layout: 'theme'
})

// Import existing assets
import watchImg from '~/assets/img/watch.png'
import headphonesImg from '~/assets/img/headphones.png'
import lampImg from '~/assets/img/lamp.png'
import vaseImg from '~/assets/img/vase.png'

const scrolled = ref(false)
const handleScroll = () => {
  scrolled.ref = window.scrollY > 50
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})

const navLinks = ['Collections', 'Technology', 'Studio', 'Archives']

const footerLinks = {
  'Shop': ['New Arrivals', 'Best Sellers', 'Exclusive'],
  'Company': ['About Us', 'Sustainability', 'Careers'],
  'Support': ['Shipping', 'Returns', 'Contact']
}

const showcaseProducts = [
  {
    name: 'Quantum X-1',
    category: 'Timepieces',
    price: '499.00',
    image: watchImg,
    themeColor: '#4338ca',
    specs: [
      { x: 30, y: 30, label: 'Titanium Shell' },
      { x: 70, y: 60, label: 'Sapphire Glass' }
    ]
  },
  {
    name: 'Acoustics Pro',
    category: 'Audio',
    price: '349.00',
    image: headphonesImg,
    themeColor: '#e11d48',
    specs: [
      { x: 20, y: 40, label: 'Hifi Sound' },
      { x: 80, y: 70, label: '40h Battery' }
    ]
  },
  {
    name: 'Aura Lumina',
    category: 'Lighting',
    price: '219.00',
    image: lampImg,
    themeColor: '#f59e0b',
    specs: [
      { x: 50, y: 20, label: 'Smart Dimming' },
      { x: 40, y: 80, label: 'Alloy Core' }
    ]
  },
  {
    name: 'Minimalist Vase',
    category: 'Decor',
    price: '89.00',
    image: vaseImg,
    themeColor: '#10b981',
    specs: [
      { x: 50, y: 50, label: 'Handcrafted' }
    ]
  }
]
</script>

<style scoped>

.font-heading {
  font-family: 'Outfit', sans-serif;
}

.multiple-product-theme {
  font-family: 'Outfit', sans-serif;
  scroll-behavior: smooth;
  background-color: #050505;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(60px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}

.animate-slide-up {
  opacity: 0;
  animation: slideUp 1.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.animate-float {
  animation: float 5s ease-in-out infinite;
}

/* Custom Hide Scrollbar */
.multiple-product-theme::-webkit-scrollbar {
  display: none;
}
.multiple-product-theme {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

/* Glassmorphism utility */
.glass {
  background: rgba(255, 255, 255, 0.03);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.05);
}
</style>
