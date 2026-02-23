<template>
  <section class="py-2 md:py-4 bg-white">
    <div class="container mx-auto px-4 sm:px-6">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 md:gap-24 items-center bg-gray-50/30 rounded-[4rem] p-10 md:p-24 border border-gray-100 overflow-hidden relative group">
        <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-50 rounded-full blur-[100px] -translate-y-1/2 translate-x-1/2 -z-10"></div>
        
        <!-- Left: Image -->
        <div class="relative rounded-[3rem] overflow-hidden shadow-2xl aspect-square lg:aspect-auto lg:h-[600px] animate-fade-in">
          <img src="~/assets/img/hero.png" alt="Showcase Room" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-[2000ms]" />
          <div class="absolute inset-0 bg-gradient-to-t from-gray-900/40 via-transparent to-transparent"></div>
          <!-- Hotspot -->
          <div class="hidden sm:block absolute" style="top:45%;left:40%">
            <div class="hotspot-wrap group/hot relative">
              <div class="w-8 h-8 bg-white/80 backdrop-blur-md rounded-full shadow-2xl cursor-pointer z-10 relative flex items-center justify-center animate-pulse-ring">
                 <div class="w-2 h-2 bg-gray-900 rounded-full"></div>
              </div>
              <div class="absolute bottom-full left-1/2 -translate-x-1/2 translate-y-2 opacity-0 group-hover/hot:opacity-100 group-hover/hot:-translate-y-4 transition-all duration-500 bg-white backdrop-blur-xl border border-white/50 shadow-2xl rounded-2xl p-4 flex flex-col gap-2 min-w-[180px] z-20 pointer-events-none">
                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">Minimalist</div>
                <div class="flex justify-between items-center">
                  <span class="text-sm font-bold text-gray-900 font-heading">Velvet Sofa</span>
                  <span class="text-sm font-bold text-indigo-600 font-heading">$899</span>
                </div>
                <div class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest">In Stock</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Content -->
        <div class="flex flex-col animate-fade-in" style="animation-delay: 0.2s">
          <span class="inline-block mb-6 text-[10px] font-bold uppercase tracking-[0.3em] text-indigo-600 bg-indigo-50 px-5 py-2 rounded-full w-fit">Concept Lookbook</span>
          <h2 class="text-4xl md:text-6xl font-extrabold tracking-tight text-gray-900 leading-[1.1] mb-8 font-heading">Sculpting Space <br/>with Light</h2>
          <p class="text-gray-500 text-lg leading-relaxed mb-12 font-medium">Create a narrative of peace in your home. Our new minimalist series explores the dialogue between industrial precision and organic warmth.</p>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12 border-t border-gray-100 pt-12">
            <div class="space-y-4">
              <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl bg-white shadow-xl shadow-gray-100">🌿</div>
              <div>
                <h4 class="font-bold text-gray-900 text-lg mb-1 font-heading">Eco Architecture</h4>
                <p class="text-sm text-gray-400 font-medium">100% sustainable materials.</p>
              </div>
            </div>
            <div class="space-y-4">
              <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl bg-white shadow-xl shadow-gray-100">🖐️</div>
              <div>
                <h4 class="font-bold text-gray-900 text-lg mb-1 font-heading">Master Crafted</h4>
                <p class="text-sm text-gray-400 font-medium">Expert artisanal finishes.</p>
              </div>
            </div>
          </div>

          <NuxtLink
            to="/shop"
            class="w-fit inline-flex items-center px-10 py-5 bg-gray-900 text-white font-bold text-sm uppercase tracking-widest rounded-full transition-all hover:-translate-y-1 hover:shadow-2xl"
          >Shop The Concept</NuxtLink>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.font-heading {
  font-family: var(--font-heading);
}

@keyframes pulseRing {
  0%   { box-shadow: 0 0 0 0 rgba(255,255,255,0.8); }
  70%  { box-shadow: 0 0 0 20px rgba(255,255,255,0); }
  100% { box-shadow: 0 0 0 0 rgba(255,255,255,0); }
}
.animate-pulse-ring { animation: pulseRing 3s ease-out infinite; }

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  opacity: 0;
  animation: fadeIn 1s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
}
</style>
