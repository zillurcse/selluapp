<template>
  <div class="min-h-screen font-sans antialiased text-slate-900 bg-[#fbfcfd] relative overflow-hidden flex items-center justify-center p-6">
    <!-- Sophisticated Background Elements -->
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:24px_24px] opacity-25"></div>
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-4xl h-96 bg-gradient-to-b from-indigo-50/50 to-transparent blur-3xl -z-10"></div>

    <div class="w-full max-w-[480px] animate-in fade-in slide-in-from-bottom-4 duration-1000">
      <!-- Logo Section -->
      <div class="flex flex-col items-center mb-10 text-center">
        <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-xl shadow-indigo-200 mb-6 group hover:scale-105 transition-transform">
          <ShoppingBag class="w-7 h-7 text-white" />
        </div>
        <h1 class="text-3xl font-[1000] text-slate-900 tracking-tight mb-2 font-display italic">SelluEcom<span class="text-indigo-600">.</span></h1>
        <p class="text-slate-400 font-medium tracking-tight">Enterprise Commerce Management</p>
      </div>

      <!-- Main Login Card -->
      <main class="bg-white rounded-[2.5rem] shadow-[0_40px_80px_-16px_rgba(0,0,0,0.06)] border border-slate-200/50 p-8 sm:p-10 relative overflow-hidden">
        
        <Transition name="fade-slide" mode="out-in">
          <!-- EMAIL LOGIN MODE -->
          <div v-if="loginMode === 'email'" key="email" class="space-y-8">
            <div class="text-center">
              <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-1 font-display">Sign In</h2>
              <p class="text-slate-500 text-sm font-medium">Please enter your credentials below</p>
            </div>

            <form class="space-y-6" @submit.prevent="handleEmailLogin">
              <!-- Error Alert -->
              <Transition name="slide-up">
                <div v-if="error" class="bg-rose-50 border border-rose-100 p-4 rounded-2xl flex items-start gap-3 shadow-sm">
                  <AlertTriangle class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" />
                  <span class="text-rose-800 text-[13px] font-bold leading-relaxed">{{ error }}</span>
                </div>
              </Transition>

              <div class="space-y-4">
                <div class="group">
                  <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2 px-1 group-focus-within:text-indigo-600 transition-colors">Email Address</label>
                  <div class="relative">
                    <Mail class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-300 group-focus-within:text-indigo-500 transition-colors pointer-events-none" />
                    <input 
                      v-model="email"
                      type="email"
                      required
                      placeholder="admin@example.com"
                      class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-4 pl-12 pr-4 text-sm font-bold text-slate-900 focus:outline-none focus:border-indigo-600 focus:bg-white transition-all placeholder:text-slate-300 placeholder:font-medium"
                    />
                  </div>
                </div>

                <div class="group">
                  <div class="flex justify-between items-center mb-2 px-1">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest group-focus-within:text-indigo-600 transition-colors">Password</label>
                    <button type="button" class="text-[10px] font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-widest transition-colors">Forgot?</button>
                  </div>
                  <div class="relative">
                    <KeyRound class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-300 group-focus-within:text-indigo-500 transition-colors pointer-events-none" />
                    <input 
                      v-model="password"
                      type="password"
                      required
                      placeholder="••••••••"
                      class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl py-4 pl-12 pr-4 text-sm font-bold text-slate-900 focus:outline-none focus:border-indigo-600 focus:bg-white transition-all placeholder:text-slate-300"
                    />
                  </div>
                </div>
              </div>

              <button 
                type="submit" 
                :disabled="loading"
                class="w-full bg-slate-900 hover:bg-slate-950 text-white text-[13px] font-black uppercase tracking-widest py-5 rounded-[24px] transition-all duration-300 shadow-[0_20px_40px_-12px_rgba(15,23,42,0.3)] hover:shadow-[0_20px_50px_-8px_rgba(15,23,42,0.4)] flex items-center justify-center gap-3 active:scale-[0.98] group overflow-hidden disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <div v-if="loading" class="w-5 h-5 border-[3px] border-white/20 border-t-white rounded-full animate-spin"></div>
                <template v-else>
                  <span>Access Dashboard</span>
                  <ArrowRight class="w-4 h-4 group-hover:translate-x-1.5 transition-transform duration-300" />
                </template>
              </button>
            </form>

            <div class="flex flex-col gap-4">
              <div class="flex items-center gap-4 py-2">
                <div class="h-[1px] flex-1 bg-slate-100"></div>
                <span class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]">Alternative</span>
                <div class="h-[1px] flex-1 bg-slate-100"></div>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <button type="button" class="flex items-center justify-center gap-2 py-3.5 border-2 border-slate-100 rounded-2xl text-[13px] font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-200 transition-all active:scale-[0.98]">
                  <svg class="w-4 h-4" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                  <span>Google</span>
                </button>
                <button 
                  type="button" 
                  @click="loginMode = 'pin'" 
                  class="flex items-center justify-center gap-2 py-3.5 border-2 border-slate-100 rounded-2xl text-[13px] font-bold text-slate-600 hover:bg-slate-50 hover:border-slate-200 transition-all active:scale-[0.98]"
                >
                  <Key class="w-4 h-4 text-slate-400" />
                  <span>Pin Code</span>
                </button>
              </div>
            </div>
          </div>

          <!-- PIN LOGIN MODE -->
          <div v-else key="pin" class="space-y-8">
            <div class="text-center">
              <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-1 font-display">Security PIN</h2>
              <p class="text-slate-500 text-sm font-medium">Verify your identity to proceed</p>
            </div>

            <div class="space-y-8">
              <div class="flex justify-between gap-3">
                <template v-for="(digit, index) in pinning" :key="index">
                  <div class="relative flex-1 group">
                    <input 
                      type="password" 
                      maxlength="1"
                      inputmode="numeric"
                      v-model="pinning[index]"
                      @input="handlePinInput($event, index)"
                      @keydown.delete="handlePinBackspace($event, index)"
                      :ref="el => setPinRef(el, index)"
                      class="w-full aspect-[4/5] bg-slate-50 border-2 border-slate-100 rounded-2xl text-center text-3xl font-black text-slate-900 focus:outline-none focus:border-indigo-600 focus:bg-white focus:ring-4 focus:ring-indigo-50 transition-all"
                      :class="{'border-indigo-600 bg-white ring-4 ring-indigo-50 shadow-sm': pinning[index]}"
                    />
                    <div v-if="pinning[index]" class="absolute bottom-2 left-1/2 -translate-x-1/2 w-1 h-1 rounded-full bg-indigo-400"></div>
                  </div>
                </template>
              </div>

              <div class="space-y-4">
                <button 
                  @click="verifyPin" 
                  :disabled="loading || pinning.some(p => !p)"
                  class="w-full bg-slate-900 hover:bg-slate-950 text-white text-[13px] font-black uppercase tracking-widest py-5 rounded-[24px] transition-all duration-300 shadow-[0_20px_40px_-12px_rgba(15,23,42,0.3)] hover:shadow-[0_20px_50px_-8px_rgba(15,23,42,0.4)] flex items-center justify-center gap-3 active:scale-[0.98] group overflow-hidden disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <div v-if="loading" class="w-5 h-5 border-[3px] border-white/20 border-t-white rounded-full animate-spin"></div>
                  <template v-else>
                    <ShieldCheck class="w-5 h-5 group-hover:scale-110 transition-transform duration-300" />
                    <span>Verify PIN</span>
                  </template>
                </button>

                <button @click="loginMode = 'email'" class="w-full text-slate-400 hover:text-slate-900 text-sm font-bold transition-all flex items-center justify-center gap-2 group">
                  <ArrowRight class="w-4 h-4 rotate-180 group-hover:-translate-x-1 transition-transform" />
                  <span>Back to Credentials</span>
                </button>
              </div>
            </div>
          </div>
        </Transition>
      </main>

      <!-- Footer Info -->
      <div class="mt-12 text-center">
        <p class="text-[11px] text-slate-400 font-bold uppercase tracking-[0.2em] leading-relaxed">
          &copy; 2024 Sellu Technologies Ltd &bull; Security Protocol V4
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  Lock, 
  Mail, 
  Key, 
  ShieldCheck, 
  ArrowRight, 
  ShoppingBag, 
  KeyRound, 
  AlertTriangle 
} from 'lucide-vue-next'
import { ref } from 'vue'

definePageMeta({
  layout: 'auth',
  middleware: 'guest',
})

const auth = useAuthStore()
const loginMode = ref('email')
const loading = ref(false)
const error = ref('')

const email = ref('')
const password = ref('')

const pinning = ref(['', '', '', '', ''])
const pinRefs = ref([])

const setPinRef = (el, index) => {
  if (el) pinRefs.value[index] = el
}

const handlePinInput = (event, index) => {
    const val = event.target.value
    if (val && !isNaN(val) && index < 4) {
        setTimeout(() => pinRefs.value[index + 1]?.focus(), 10)
    }
}

const handlePinBackspace = (event, index) => {
    if (!pinning.value[index] && index > 0) {
        pinRefs.value[index - 1]?.focus()
    }
}

const verifyPin = async () => {
    const pin = pinning.value.join('')
    if (pin.length !== 5) return
    
    loading.value = true
    error.value = ''
    try {
        await new Promise(r => setTimeout(r, 1200))
        await navigateTo('/')
    } catch (e) {
        error.value = 'Verification failed. Invalid PIN.'
        pinning.value = ['', '', '', '', '']
        pinRefs.value[0]?.focus()
    } finally {
        loading.value = false
    }
}

const handleEmailLogin = async () => {
  error.value = ''
  loading.value = true

  try {
    const success = await auth.login({
        email: email.value,
        password: password.value
    })
    
    if (success) {
      await navigateTo('/')
    }
  } catch (e) {
    error.value = e?.data?.message || e?.message || 'Authentication failed. Please verify your credentials.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>

:root {
  --font-display: 'Outfit', sans-serif;
  --font-sans: 'Plus Jakarta Sans', sans-serif;
}

.font-display { font-family: var(--font-display); }
.font-sans { font-family: var(--font-sans); }

.fade-slide-enter-active, .fade-slide-leave-active {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.fade-slide-enter-from { opacity: 0; transform: translateY(20px); }
.fade-slide-leave-to { opacity: 0; transform: translateY(-20px); }

.slide-up-enter-active { transition: all 0.3s ease-out; }
.slide-up-enter-from { opacity: 0; transform: translateY(10px); }

input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>
