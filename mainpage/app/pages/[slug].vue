<template>
  <div class="min-h-screen bg-white relative overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-gray-50 rounded-full blur-[120px] -z-10 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-gray-50 rounded-full blur-[100px] -z-10 pointer-events-none"></div>

    <!-- Page not found fallback -->
    <div v-if="!page" class="flex flex-col items-center justify-center min-h-[60vh] text-center px-6 animate-fade-in">
      <div class="text-8xl mb-6">🔍</div>
      <h1 class="text-4xl font-extrabold text-gray-900 mb-3 font-heading">Page not found</h1>
      <p class="text-gray-500 mb-8 max-w-md mx-auto">The page you're looking for doesn't exist or has been moved to a new location.</p>
      <NuxtLink to="/" class="px-8 py-3 bg-gray-900 text-white font-bold text-sm rounded-full hover:-translate-y-0.5 hover:shadow-lg transition-all inline-block">← Back to Home</NuxtLink>
    </div>

    <template v-else>
      <!-- ── HERO HEADER ── -->
      <div class="relative overflow-hidden min-h-[50vh] flex items-center" :class="page.heroBg || 'bg-gray-900'">
        <!-- Background blobs -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
          <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full blur-[100px] opacity-20" :class="page.blob1 || 'bg-violet-400'"></div>
          <div class="absolute -bottom-24 -left-16 w-80 h-80 rounded-full blur-[100px] opacity-15" :class="page.blob2 || 'bg-blue-400'"></div>
        </div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-6 py-20 md:py-32 text-center animate-fade-in">
          <div class="flex justify-center mb-8">
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 border border-white/20 rounded-full text-[10px] md:text-xs font-bold uppercase tracking-[0.2em] text-white backdrop-blur-md">
              {{ page.label }}
            </span>
          </div>
          <h1 class="text-4xl sm:text-5xl md:text-7xl font-extrabold text-white leading-[1.1] tracking-tight max-w-4xl mx-auto mb-8 font-heading">
            {{ page.title }}
          </h1>
          <p class="text-white/60 text-lg md:text-xl max-w-2xl mx-auto leading-relaxed font-light">
            {{ page.subtitle }}
          </p>
          <!-- Updated date for legal pages -->
          <p v-if="page.updated" class="mt-8 text-white/30 text-xs font-bold uppercase tracking-widest">Last updated: {{ page.updated }}</p>
        </div>
      </div>

      <!-- ── ABOUT PAGE ── -->
      <template v-if="slug === 'about'">
        <!-- Our Story -->
        <section class="py-24 md:py-32 bg-white">
          <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
              <div class="animate-fade-in" style="animation-delay: 0.1s">
                <span class="inline-block text-[10px] font-bold uppercase tracking-[0.2em] text-indigo-600 bg-indigo-50 px-4 py-2 rounded-full mb-8">Creative Legacy</span>
                <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight leading-[1.1] mb-8 font-heading">
                  Designed for modern<br/><span class="text-indigo-600">living spaces.</span>
                </h2>
                <div class="space-y-6 text-gray-500 text-lg leading-relaxed max-w-xl">
                  <p>EMU was founded in 2020 with a single vision — to bring premium, thoughtfully designed home decor to every corner of the world. We believe your surroundings shape how you feel, so we obsess over every detail.</p>
                  <p>From hand-picked ceramics to architectural lighting, every product in our catalogue is curated by our in-house design team with sustainability and craftsmanship as non-negotiables.</p>
                </div>
                <div class="grid grid-cols-3 gap-8 mt-12 border-t border-gray-100 pt-10">
                  <div v-for="s in aboutStats" :key="s.label">
                    <div class="text-3xl font-extrabold text-gray-900 tracking-tight font-heading">{{ s.value }}</div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">{{ s.label }}</div>
                  </div>
                </div>
              </div>
              <div class="relative animate-fade-in" style="animation-delay: 0.3s">
                <div class="rounded-[2.5rem] overflow-hidden bg-gray-100 shadow-2xl skew-y-1 transform hover:skew-y-0 transition-transform duration-700">
                  <img src="~/assets/img/hero.png" alt="About EMU" class="w-full h-[500px] object-cover scale-110 hover:scale-100 transition-transform duration-700" />
                </div>
                <div class="absolute -bottom-10 -left-10 bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl p-8 border border-white/50 max-w-[240px]">
                  <div class="text-4xl mb-4">🌱</div>
                  <div class="font-bold text-gray-900 text-lg mb-1 font-heading leading-tight">Carbon Neutral by 2026</div>
                  <div class="text-xs text-gray-400 font-medium">Every purchase plants a tree in our reforestation projects.</div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- Team -->
        <section class="py-24 md:py-32 bg-gray-50/50">
          <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-20 animate-fade-in">
              <span class="inline-block text-[10px] font-bold uppercase tracking-[0.2em] text-rose-600 bg-rose-50 px-4 py-2 rounded-full mb-6">Our Team</span>
              <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6 font-heading">The visionaries behind EMU</h2>
              <p class="text-gray-500 text-lg max-w-xl mx-auto">Meet the designers and strategists dedicated to redefining modern living.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10">
              <div v-for="(member, idx) in team" :key="member.name" 
                class="group bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm transition-all duration-500 hover:shadow-2xl hover:shadow-gray-200/50 hover:-translate-y-2 animate-fade-in"
                :style="{ animationDelay: `${0.2 + idx * 0.1}s` }">
                <div class="w-24 h-24 rounded-3xl mb-8 flex items-center justify-center text-4xl shadow-xl transition-transform group-hover:scale-110 duration-500" :style="{ background: member.bg }">
                  {{ member.avatar }}
                </div>
                <h3 class="font-extrabold text-2xl text-gray-900 mb-1 group-hover:text-indigo-600 transition-colors font-heading">{{ member.name }}</h3>
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-6">{{ member.role }}</div>
                <p class="text-sm text-gray-500 leading-relaxed font-medium">{{ member.bio }}</p>
              </div>
            </div>
          </div>
        </section>
      </template>

      <!-- ── CONTACT PAGE ── -->
      <template v-else-if="slug === 'contact'">
        <section class="py-24 bg-white">
          <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-start">
              <!-- Left: Info -->
              <div class="lg:col-span-5 space-y-12 animate-fade-in">
                <div>
                  <h2 class="text-4xl font-extrabold text-gray-900 mb-6 font-heading leading-tight">Questions?<br/>Let's talk design.</h2>
                  <p class="text-gray-500 leading-relaxed text-lg max-w-md">Our team is available to assist you with everything from order status to interior design consultation.</p>
                </div>
                <div class="space-y-6">
                  <div v-for="info in contactInfo" :key="info.label" class="flex items-center gap-6 p-6 rounded-3xl bg-gray-50/50 border border-gray-100/50 transition-all hover:bg-white hover:shadow-xl group">
                    <div class="w-14 h-14 rounded-2xl bg-white shadow-sm flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">{{ info.icon }}</div>
                    <div>
                      <div class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">{{ info.label }}</div>
                      <div class="font-bold text-gray-900 text-lg mb-1 leading-tight font-heading">{{ info.value }}</div>
                      <div class="text-xs text-gray-400 font-bold uppercase">{{ info.sub }}</div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Right: Form -->
              <div class="lg:col-span-7 animate-fade-in" style="animation-delay: 0.2s">
                <div class="bg-gray-900 rounded-[2.5rem] p-10 md:p-14 shadow-2xl relative overflow-hidden">
                  <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                  <h3 class="text-2xl font-bold text-white mb-10 font-heading uppercase tracking-widest">Send a Message</h3>
                  <form @submit.prevent="submitContact" class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                      <div class="space-y-2">
                        <label class="text-[10px] font-bold text-white/40 uppercase tracking-widest ml-1">First name</label>
                        <input type="text" placeholder="John" required class="w-full px-6 py-4 rounded-2xl bg-white/5 border border-white/10 text-white placeholder:text-white/20 outline-none focus:ring-2 focus:ring-white/20 transition-all font-medium" />
                      </div>
                      <div class="space-y-2">
                        <label class="text-[10px] font-bold text-white/40 uppercase tracking-widest ml-1">Last name</label>
                        <input type="text" placeholder="Doe" required class="w-full px-6 py-4 rounded-2xl bg-white/5 border border-white/10 text-white placeholder:text-white/20 outline-none focus:ring-2 focus:ring-white/20 transition-all font-medium" />
                      </div>
                    </div>
                    <div class="space-y-2">
                      <label class="text-[10px] font-bold text-white/40 uppercase tracking-widest ml-1">Email Address</label>
                      <input type="email" placeholder="you@example.com" required class="w-full px-6 py-4 rounded-2xl bg-white/5 border border-white/10 text-white placeholder:text-white/20 outline-none focus:ring-2 focus:ring-white/20 transition-all font-medium" />
                    </div>
                    <div class="space-y-2">
                      <label class="text-[10px] font-bold text-white/40 uppercase tracking-widest ml-1">Message</label>
                      <textarea placeholder="Tell us more about your inquiry..." rows="5" required class="w-full px-6 py-4 rounded-2xl bg-white/5 border border-white/10 text-white placeholder:text-white/20 outline-none focus:ring-2 focus:ring-white/20 transition-all font-medium resize-none"></textarea>
                    </div>
                    <button type="submit" class="w-full py-5 bg-white text-gray-900 font-bold text-sm rounded-2xl uppercase tracking-[0.2em] shadow-xl hover:-translate-y-1 active:translate-y-0 transition-all duration-300">
                      Submit Message
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </section>
      </template>

      <!-- ── FAQ PAGE ── -->
      <template v-else-if="slug === 'faq'">
        <section class="py-24 md:py-32 bg-white flex flex-col items-center">
          <div class="max-w-4xl w-full px-6">
            <div class="flex flex-wrap gap-4 mb-16 justify-center animate-fade-in">
              <button v-for="cat in faqCategories" :key="cat"
                @click="activeFaqCat = cat"
                class="px-6 py-3 rounded-full text-xs font-bold uppercase tracking-widest transition-all border-none cursor-pointer"
                :class="activeFaqCat === cat ? 'bg-gray-900 text-white shadow-xl translate-y-[-2px]' : 'bg-gray-100 text-gray-400 hover:bg-gray-200'">
                {{ cat }}
              </button>
            </div>
            
            <div class="space-y-4 animate-fade-in" style="animation-delay: 0.2s">
              <div v-for="faq in filteredFaqs" :key="faq.q"
                class="border border-gray-100 rounded-3xl overflow-hidden transition-all group"
                :class="openFaq === faq.q ? 'shadow-2xl shadow-gray-100 border-indigo-100' : 'hover:border-gray-200'">
                <button
                  @click="openFaq = openFaq === faq.q ? null : faq.q"
                  class="w-full flex items-center justify-between gap-6 px-8 py-6 text-left font-bold text-gray-900 text-lg bg-white hover:bg-gray-50 transition-all border-none cursor-pointer font-heading">
                  {{ faq.q }}
                  <div class="w-8 h-8 rounded-full bg-gray-50 flex items-center justify-center text-xl text-gray-400 group-hover:bg-gray-900 group-hover:text-white transition-all duration-500" :class="openFaq === faq.q ? 'rotate-45 bg-gray-900 text-white' : ''">+</div>
                </button>
                <div v-if="openFaq === faq.q" class="px-8 pb-8 pt-0 text-gray-500 text-lg leading-relaxed bg-white border-t border-gray-50 animate-fade-in">
                  {{ faq.a }}
                </div>
              </div>
            </div>

            <div class="mt-20 text-center p-12 rounded-[3rem] bg-indigo-600 text-white shadow-2xl relative overflow-hidden group animate-fade-in" style="animation-delay: 0.3s">
              <div class="absolute top-0 left-0 w-64 h-64 bg-white/10 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
              <div class="text-5xl mb-6">💬</div>
              <h3 class="text-3xl font-bold mb-4 font-heading">Still have questions?</h3>
              <p class="text-indigo-100 text-lg mb-10 max-w-sm mx-auto font-medium">Our customer experience team is available 24/7 to help you with anything.</p>
              <NuxtLink to="/contact" class="inline-block px-10 py-5 bg-white text-indigo-600 font-bold uppercase tracking-[0.2em] rounded-full hover:-translate-y-1 hover:shadow-2xl transition-all duration-300">Contact Support</NuxtLink>
            </div>
          </div>
        </section>
      </template>

      <!-- ── LEGAL PAGES ── -->
      <template v-else-if="slug === 'privacy' || slug === 'terms'">
        <section class="py-24 bg-white">
          <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col lg:flex-row gap-16">
              <!-- TOC sidebar -->
              <aside class="lg:w-64 shrink-0 animate-fade-in">
                <div class="sticky top-24 p-8 rounded-3xl bg-gray-50 border border-gray-100">
                  <div class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400 mb-6">On this page</div>
                  <nav class="space-y-1">
                    <a v-for="section in legalSections[slug]" :key="section.id"
                      :href="'#' + section.id"
                      class="block px-4 py-2.5 rounded-xl text-sm text-gray-500 font-bold hover:bg-white hover:text-indigo-600 hover:shadow-sm transition-all no-underline">
                      {{ section.title }}
                    </a>
                  </nav>
                </div>
              </aside>

              <!-- Article content -->
              <article class="flex-1 max-w-3xl animate-fade-in" style="animation-delay: 0.1s">
                <div v-for="section in legalSections[slug]" :key="section.id" :id="section.id" class="mb-16 scroll-mt-32 group">
                  <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 rounded-2xl bg-gray-900 flex items-center justify-center text-xl shrink-0 text-white shadow-xl transition-transform group-hover:scale-110">{{ section.icon }}</div>
                    <h2 class="text-2xl font-bold text-gray-900 font-heading uppercase tracking-widest">{{ section.title }}</h2>
                  </div>
                  <div class="text-gray-500 text-lg leading-relaxed space-y-6 font-medium">
                    <p v-for="(para, i) in section.content" :key="i">{{ para }}</p>
                  </div>
                </div>
              </article>
            </div>
          </div>
        </section>
      </template>

      <!-- ── CTA STRIP ── -->
      <section class="py-20 bg-gray-900 relative overflow-hidden group">
        <div class="absolute inset-0 bg-indigo-600 translate-y-full group-hover:translate-y-0 transition-transform duration-700"></div>
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-10 relative z-10">
          <div class="text-center md:text-left">
            <h3 class="text-3xl md:text-4xl font-extrabold text-white mb-3 font-heading">Ready to transform your home?</h3>
            <p class="text-white/50 text-lg font-medium">Browse our full collection of designer-approved home essentials.</p>
          </div>
          <NuxtLink to="/shop" class="group/btn relative px-10 py-5 bg-white text-gray-900 font-bold uppercase tracking-[0.2em] rounded-full overflow-hidden transition-all hover:shadow-2xl hover:-translate-y-1 active:translate-y-0">
            <span class="relative z-10 group-hover/btn:text-white transition-colors duration-300">Shop The Collection</span>
            <div class="absolute inset-0 bg-gray-900 translate-y-full group-hover/btn:translate-y-0 transition-transform duration-300"></div>
          </NuxtLink>
        </div>
      </section>
    </template>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute } from 'vue-router'
const route = useRoute()
const slug = computed(() => route.params.slug)

// ─── Page Meta per slug ──────────────────────────────────────────────
const pages = {
  about: {
    label: 'Since 2020',
    title: 'Minimalist Design. Purposeful Living.',
    subtitle: 'We craft timeless essentials for the modern home, prioritizing quality materials and ethical craftsmanship.',
    heroBg: 'bg-gradient-to-br from-indigo-950 via-gray-950 to-indigo-950',
    blob1: 'bg-indigo-500', blob2: 'bg-indigo-700',
  },
  contact: {
    label: 'Need Help?',
    title: "We'd love to hear from you.",
    subtitle: 'Our concierge team is here to help with everything from order support to design advice.',
    heroBg: 'bg-gradient-to-br from-gray-950 via-slate-900 to-gray-950',
    blob1: 'bg-blue-400', blob2: 'bg-indigo-600',
  },
  faq: {
    label: 'Support Center',
    title: 'Common Questions',
    subtitle: 'Everything you need to know about our products, shipping, and sustainable mission.',
    heroBg: 'bg-gradient-to-br from-slate-950 via-indigo-950 to-gray-950',
    blob1: 'bg-violet-600', blob2: 'bg-indigo-400',
  },
  privacy: {
    label: 'Security',
    title: 'Privacy Policy',
    subtitle: 'How we collect, use, and protect your personal information in accordance with global standards.',
    heroBg: 'bg-gradient-to-br from-gray-950 to-slate-900',
    blob1: 'bg-gray-600', blob2: 'bg-gray-800',
    updated: 'February 22, 2026',
  },
  terms: {
    label: 'Rules',
    title: 'Terms & Conditions',
    subtitle: 'The legal framework for using our platform and purchasing our products.',
    heroBg: 'bg-gradient-to-br from-gray-950 to-slate-900',
    blob1: 'bg-gray-600', blob2: 'bg-gray-800',
    updated: 'February 22, 2026',
  }
}

const page = computed(() => pages[slug.value] || null)

useHead(() => ({
  title: page.value ? `${page.value.title} | EMU` : 'Page Not Found | EMU',
  meta: [{ name: 'description', content: page.value?.subtitle || '' }],
}))

// ─── ABOUT data ─────────────────────────────────────────────────────
const aboutStats = [
  { value: '120K+', label: 'Happy Customers' },
  { value: '450+', label: 'Designers' },
  { value: '85+', label: 'Countries' },
]

const team = [
  { name: 'Sofia Martins', role: 'Founder & Creative Lead', avatar: '👩', bg: 'linear-gradient(135deg,#6366f1,#a855f7)', bio: 'Believes that good design should be accessible, sustainable, and built to last a lifetime.' },
  { name: 'James Park', role: 'Head of Quality', avatar: '👨', bg: 'linear-gradient(135deg,#10b981,#3b82f6)', bio: 'Ensures every single material meets our rigorous ethical and durability standards.' },
  { name: 'Aiko Tanaka', role: 'Lead Architect', avatar: '👩‍🎨', bg: 'linear-gradient(135deg,#f43f5e,#fb923c)', bio: 'Focuses on the intersection of modern geometry and organic materials.' },
]

// ─── CONTACT data ────────────────────────────────────────────────────
const contactInfo = [
  { icon: '📍', label: 'Flagship Studio', value: '77 Design Sq, Copenhagen, DK', sub: '9AM–5PM DAILY' },
  { icon: '📧', label: 'Message Us', value: 'hello@emu-store.com', sub: 'FAST RESPONSE' },
  { icon: '📞', label: 'Call Support', value: '+45 80 82 04 20', sub: 'WORLDWIDE' },
]

const submitContact = () => alert('Thank you! Your message has been sent.')

// ─── FAQ data ────────────────────────────────────────────────────────
const faqCategories = ['All Topics', 'Orders', 'Shipping', 'Returns', 'Sustainability']
const activeFaqCat = ref('All Topics')
const openFaq = ref(null)
const faqs = [
  { cat: 'Orders', q: 'How can I check my order status?', a: 'You can check your status in real-time by clicking the tracking link in your confirmation email, or by logging into your account dashboard.' },
  { cat: 'Shipping', q: 'Do you offer international weightless shipping?', a: 'We offer carbon-neutral shipping to over 85 countries. Delivery typically takes 5–12 business days depending on your location.' },
  { cat: 'Returns', q: 'What is your return window?', a: 'We offer a 30-day "Happiness Guarantee". If you are not completely satisfied, we will provide a prepaid return label and a full refund.' },
  { cat: 'Sustainability', q: 'How are your materials sourced?', a: 'We only work with FSC-certified wood, recycled metals, and OEKO-TEX certified fabrics. 100% of our production is ethically audited.' },
]
const filteredFaqs = computed(() =>
  activeFaqCat.value === 'All Topics' ? faqs : faqs.filter(f => f.cat === activeFaqCat.value)
)

// ─── Legal sections ──────────────────────────────────────────────────
const legalSections = {
  privacy: [
    { id: 'data', icon: '🛡️', title: '1. Data Collection', content: ['We prioritize your privacy above all. We only collect the minimal amount of data necessary to process your order and improve your experience.', 'This includes your name, shipping address, and encrypted payment tokens handled securely via Stripe.'] },
    { id: 'use', icon: '⚙️', title: '2. Data Usage', content: ['Your data is used exclusively to fulfill orders, provide support, and (optionally) send you design inspiration via our newsletter.'] },
  ],
  terms: [
    { id: 'use', icon: '📜', title: '1. Platform Usage', content: ['By using EMU, you agree to treat our community with respect and use our platform for lawful shopping purposes only.'] },
    { id: 'sales', icon: '💳', title: '2. Sales & Tax', content: ['All prices include applicable local taxes. We reserve the right to modify pricing or cancel orders due to technical errors.'] },
  ],
}
</script>

<style scoped>
.font-heading {
  font-family: var(--font-heading);
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  opacity: 0;
  animation: fadeIn 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
}

::-webkit-scrollbar {
  width: 5px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: #f1f5f9;
  border-radius: 10px;
}
</style>

