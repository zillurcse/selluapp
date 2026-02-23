<template>
  <div class="theme-showcase bg-[#050505] text-white min-h-screen font-sans selection:bg-indigo-500 selection:text-white">
    <!-- Hero -->
    <section class="relative py-32 px-6 lg:px-20 overflow-hidden">
      <!-- Background Blobs -->
      <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-indigo-600/10 rounded-full blur-[150px] -z-10"></div>
      <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-rose-600/10 rounded-full blur-[120px] -z-10"></div>

      <div class="max-w-7xl mx-auto space-y-8 text-center animate-fade-in">
        <span class="inline-block px-4 py-1.5 rounded-full bg-white/5 border border-white/10 text-[10px] font-black uppercase tracking-[0.4em] text-white/40">Design System v2.0</span>
        <h1 class="text-6xl md:text-8xl font-black tracking-tighter leading-tight font-heading">
          Premium Product <br/>
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-rose-400 to-amber-400">Themes.</span>
        </h1>
        <p class="text-white/40 text-lg max-w-2xl mx-auto leading-relaxed font-light italic">
          Select a template to preview the future of your e-commerce experience.
        </p>
      </div>
    </section>

    <!-- Grid -->
    <section class="max-w-7xl mx-auto px-6 pb-40">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        
        <!-- Theme Card v1 -->
        <NuxtLink to="/theme/single" class="group relative aspect-[4/5] rounded-[3rem] overflow-hidden bg-white/[0.02] border border-white/[0.05] hover:border-indigo-500/50 transition-all duration-700">
          <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-black z-10"></div>
          <div class="p-10 relative z-20 h-full flex flex-col justify-end gap-4 transform group-hover:translate-y-[-10px] transition-transform duration-700">
            <span class="text-[10px] font-black uppercase tracking-widest text-indigo-400">Theme Noir</span>
            <h3 class="text-3xl font-black font-heading tracking-tight">Quantum High-Tech</h3>
            <p class="text-white/40 text-sm leading-relaxed">Dark mode with floating interactions and immersive glassmorphism effects.</p>
            <div class="mt-4 flex items-center gap-3 text-xs font-black uppercase tracking-widest text-white group-hover:text-amber-400 transition-colors">
              Preview Template
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="translate-x-0 group-hover:translate-x-2 transition-transform duration-500"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
          </div>
          <!-- Decorative SVG Mockup -->
          <div class="absolute top-10 right-10 w-20 h-20 rounded-full bg-indigo-500/20 blur-2xl group-hover:scale-150 transition-transform duration-1000"></div>
        </NuxtLink>

        <!-- Theme Card v2 -->
        <NuxtLink to="/theme/single/v2" class="group relative aspect-[4/5] rounded-[3rem] overflow-hidden bg-white/[0.02] border border-white/[0.05] hover:border-amber-500/50 transition-all duration-700">
          <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-black z-10"></div>
          <div class="p-10 relative z-20 h-full flex flex-col justify-end gap-4 transform group-hover:translate-y-[-10px] transition-transform duration-700">
            <span class="text-[10px] font-black uppercase tracking-widest text-amber-500">Theme Blanc</span>
            <h3 class="text-3xl font-black font-heading tracking-tight">Minimalist Essence</h3>
            <p class="text-white/40 text-sm leading-relaxed">Light-focused design emphasizing material honesty and white space.</p>
            <div class="mt-4 flex items-center gap-3 text-xs font-black uppercase tracking-widest text-white group-hover:text-amber-400 transition-colors">
              Preview Template
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="translate-x-0 group-hover:translate-x-2 transition-transform duration-500"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
          </div>
        </NuxtLink>

        <!-- Theme Card v3 -->
        <NuxtLink to="/theme/single/v3" class="group relative aspect-[4/5] rounded-[3rem] overflow-hidden bg-white/[0.02] border border-white/[0.05] hover:border-emerald-500/50 transition-all duration-700">
          <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-black z-10"></div>
          <div class="p-10 relative z-20 h-full flex flex-col justify-end gap-4 transform group-hover:translate-y-[-10px] transition-transform duration-700">
            <span class="text-[10px] font-black uppercase tracking-widest text-emerald-500">Theme Story</span>
            <h3 class="text-3xl font-black font-heading tracking-tight">Immersive Landing</h3>
            <p class="text-white/40 text-sm leading-relaxed">Bold typography and full-screen storytelling for hero products.</p>
            <div class="mt-4 flex items-center gap-3 text-xs font-black uppercase tracking-widest text-white group-hover:text-amber-400 transition-colors">
              Preview Template
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="translate-x-0 group-hover:translate-x-2 transition-transform duration-500"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </div>
          </div>
        </NuxtLink>

      </div>
    </section>

    <!-- Footer Note -->
    <div class="py-20 text-center text-white/20 text-[10px] font-black uppercase tracking-[0.8em] animate-pulse">
      Curated by DeepMind • Antigravity
    </div>
  </div>
</template>

<style scoped>

.theme-showcase {
  font-family: 'Outfit', sans-serif;
}

.font-heading {
  font-family: 'Outfit', sans-serif;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fadeIn 1s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
}
</style>
