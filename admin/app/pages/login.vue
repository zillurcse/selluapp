<template>
  <div class="min-h-screen flex bg-slate-50">
    <!-- Left branding panel -->
    <aside class="hidden lg:flex lg:w-[52%] xl:w-[55%] relative overflow-hidden bg-gradient-to-br from-indigo-600 via-indigo-700 to-violet-800 text-white">
      <div class="absolute inset-0 opacity-30">
        <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-white/20 blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-[28rem] h-[28rem] rounded-full bg-violet-400/30 blur-3xl"></div>
        <div class="absolute top-1/2 left-1/3 w-64 h-64 rounded-full bg-indigo-300/20 blur-2xl"></div>
      </div>
      <div class="absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,rgba(255,255,255,0.08)_1px,transparent_0)] [background-size:28px_28px]"></div>

      <div class="relative z-10 flex flex-col justify-between p-12 xl:p-16 w-full">
        <div>
          <div class="flex items-center gap-3 mb-16">
            <div class="w-11 h-11 bg-white/15 backdrop-blur-sm rounded-xl flex items-center justify-center ring-1 ring-white/20">
              <ShoppingBag class="w-5 h-5 text-white" />
            </div>
            <span class="text-xl font-semibold tracking-tight">SelluEcom</span>
          </div>

          <h1 class="text-4xl xl:text-5xl font-bold leading-tight tracking-tight mb-5">
            Manage your store<br />
            <span class="text-indigo-200">from one place.</span>
          </h1>
          <p class="text-indigo-100/80 text-lg max-w-md leading-relaxed">
            Track orders, manage inventory, and grow your business with our all-in-one commerce platform.
          </p>
        </div>

        <div class="space-y-4">
          <div
            v-for="feature in features"
            :key="feature.title"
            class="flex items-center gap-4 p-4 rounded-2xl bg-white/10 backdrop-blur-sm ring-1 ring-white/10"
          >
            <div class="w-10 h-10 rounded-xl bg-white/15 flex items-center justify-center shrink-0">
              <component :is="feature.icon" class="w-5 h-5 text-indigo-100" />
            </div>
            <div>
              <p class="font-semibold text-sm">{{ feature.title }}</p>
              <p class="text-indigo-200/70 text-sm">{{ feature.desc }}</p>
            </div>
          </div>
        </div>
      </div>
    </aside>

    <!-- Right form panel -->
    <main class="flex-1 flex items-center justify-center p-6 sm:p-10 relative overflow-hidden">
      <div class="absolute inset-0 lg:hidden bg-gradient-to-b from-indigo-50/80 to-slate-50 -z-10"></div>
      <div class="absolute top-0 right-0 w-72 h-72 bg-indigo-100/40 rounded-full blur-3xl -z-10 lg:hidden"></div>

      <div class="w-full max-w-[420px]">
        <!-- Mobile logo -->
        <div class="flex flex-col items-center mb-8 lg:hidden text-center">
          <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200 mb-4">
            <ShoppingBag class="w-6 h-6 text-white" />
          </div>
          <h1 class="text-2xl font-bold text-slate-900 tracking-tight">SelluEcom</h1>
          <p class="text-slate-500 text-sm mt-1">Sign in to your account</p>
        </div>

        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100 p-7 sm:p-9">
          <Transition name="fade-slide" mode="out-in">
            <!-- Email login -->
            <div v-if="loginMode === 'email'" key="email">
              <div class="mb-7 hidden lg:block">
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Welcome back</h2>
                <p class="text-slate-500 text-sm mt-1.5">Enter your details to sign in</p>
              </div>

              <form class="space-y-5" @submit.prevent="handleEmailLogin">
                <Transition name="slide-up">
                  <div
                    v-if="error"
                    class="flex items-start gap-3 p-3.5 rounded-xl bg-red-50 border border-red-100 text-red-700 text-sm"
                    role="alert"
                  >
                    <AlertCircle class="w-4 h-4 shrink-0 mt-0.5" />
                    <span>{{ error }}</span>
                  </div>
                </Transition>

                <div class="space-y-4">
                  <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email address</label>
                    <div class="relative group">
                      <Mail class="absolute left-4 top-1/2 z-10 -translate-y-1/2 w-5 h-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors pointer-events-none" />
                      <input
                        id="email"
                        v-model="email"
                        type="email"
                        required
                        autocomplete="email"
                        placeholder="you@company.com"
                        class="login-input pl-12"
                      />
                    </div>
                  </div>

                  <div>
                    <div class="flex items-center justify-between mb-1.5">
                      <label for="password" class="text-sm font-medium text-slate-700">Password</label>
                      <button type="button" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 transition-colors">
                        Forgot password?
                      </button>
                    </div>
                    <div class="relative group">
                      <Lock class="absolute left-4 top-1/2 z-10 -translate-y-1/2 w-5 h-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors pointer-events-none" />
                      <input
                        id="password"
                        v-model="password"
                        :type="showPassword ? 'text' : 'password'"
                        required
                        autocomplete="current-password"
                        placeholder="Enter your password"
                        class="login-input pl-12 pr-12"
                      />
                      <button
                        type="button"
                        class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-slate-600 transition-colors"
                        :aria-label="showPassword ? 'Hide password' : 'Show password'"
                        @click="showPassword = !showPassword"
                      >
                        <EyeOff v-if="showPassword" class="w-[18px] h-[18px]" />
                        <Eye v-else class="w-[18px] h-[18px]" />
                      </button>
                    </div>
                  </div>
                </div>

                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                  <input
                    v-model="rememberMe"
                    type="checkbox"
                    class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 focus:ring-offset-0"
                  />
                  <span class="text-sm text-slate-600">Remember me for 30 days</span>
                </label>

                <button type="submit" :disabled="loading" class="login-btn group">
                  <div v-if="loading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                  <template v-else>
                    <span>Sign in</span>
                    <ArrowRight class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" />
                  </template>
                </button>
              </form>

              <div class="mt-7">
                <div class="flex items-center gap-3 mb-5">
                  <div class="h-px flex-1 bg-slate-100"></div>
                  <span class="text-xs text-slate-400 font-medium">or continue with</span>
                  <div class="h-px flex-1 bg-slate-100"></div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <button type="button" class="alt-btn">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" aria-hidden="true">
                      <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" />
                      <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                      <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05" />
                      <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                    </svg>
                    Google
                  </button>
                  <button type="button" class="alt-btn" @click="switchToPin">
                    <Key class="w-4 h-4 text-slate-400" />
                    PIN code
                  </button>
                </div>
              </div>
            </div>

            <!-- PIN login -->
            <div v-else key="pin">
              <div class="mb-7 text-center lg:text-left">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 mb-4">
                  <ShieldCheck class="w-6 h-6" />
                </div>
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Enter your PIN</h2>
                <p class="text-slate-500 text-sm mt-1.5">Use your 5-digit security PIN to sign in</p>
              </div>

              <div class="space-y-5">
                <Transition name="slide-up">
                  <div
                    v-if="error"
                    class="flex items-start gap-3 p-3.5 rounded-xl bg-red-50 border border-red-100 text-red-700 text-sm"
                    role="alert"
                  >
                    <AlertCircle class="w-4 h-4 shrink-0 mt-0.5" />
                    <span>{{ error }}</span>
                  </div>
                </Transition>

                <div>
                  <label for="pin-email" class="block text-sm font-medium text-slate-700 mb-1.5">Email address</label>
                  <div class="relative group">
                    <Mail class="absolute left-4 top-1/2 z-10 -translate-y-1/2 w-5 h-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors pointer-events-none" />
                    <input
                      id="pin-email"
                      v-model="email"
                      type="email"
                      required
                      autocomplete="email"
                      placeholder="you@company.com"
                      class="login-input pl-12"
                    />
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-slate-700 mb-3 text-center lg:text-left">Security PIN</label>
                  <div class="flex justify-center gap-2.5 sm:gap-3">
                    <template v-for="(_, index) in pinning" :key="index">
                      <input
                        :ref="el => setPinRef(el, index)"
                        v-model="pinning[index]"
                        type="password"
                        inputmode="numeric"
                        maxlength="1"
                        class="pin-input"
                        :class="{ 'pin-input--filled': pinning[index] }"
                        :aria-label="`PIN digit ${index + 1}`"
                        @input="handlePinInput($event, index)"
                        @keydown.delete="handlePinBackspace($event, index)"
                        @paste="handlePinPaste"
                      />
                    </template>
                  </div>
                </div>

                <button
                  type="button"
                  :disabled="loading || pinning.some(p => !p)"
                  class="login-btn group"
                  @click="verifyPin"
                >
                  <div v-if="loading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                  <template v-else>
                    <span>Verify &amp; sign in</span>
                    <ArrowRight class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" />
                  </template>
                </button>

                <button
                  type="button"
                  class="w-full flex items-center justify-center gap-2 py-2.5 text-sm font-medium text-slate-500 hover:text-slate-800 transition-colors"
                  @click="switchToEmail"
                >
                  <ArrowLeft class="w-4 h-4" />
                  Back to email sign in
                </button>
              </div>
            </div>
          </Transition>
        </div>

        <p class="mt-8 text-center text-xs text-slate-400">
          &copy; {{ currentYear }} Sellu Technologies. All rights reserved.
        </p>
      </div>
    </main>
  </div>
</template>

<script setup>
import {
  Mail,
  Key,
  ShieldCheck,
  ArrowRight,
  ArrowLeft,
  ShoppingBag,
  Lock,
  AlertCircle,
  Eye,
  EyeOff,
  BarChart3,
  Package,
  Zap,
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
const showPassword = ref(false)
const rememberMe = ref(true)
const currentYear = new Date().getFullYear()

const email = ref(auth.last_email || '')
const password = ref('')

const pinning = ref(['', '', '', '', ''])
const pinRefs = ref([])

const features = [
  { icon: BarChart3, title: 'Real-time analytics', desc: 'Monitor sales and performance live' },
  { icon: Package, title: 'Inventory control', desc: 'Stay on top of stock levels' },
  { icon: Zap, title: 'Fast checkout', desc: 'POS and online orders in sync' },
]

const setPinRef = (el, index) => {
  if (el) pinRefs.value[index] = el
}

const switchToPin = () => {
  error.value = ''
  loginMode.value = 'pin'
  nextTick(() => pinRefs.value[0]?.focus())
}

const switchToEmail = () => {
  error.value = ''
  loginMode.value = 'email'
}

const handlePinInput = (event, index) => {
  const val = event.target.value.replace(/\D/g, '')
  pinning.value[index] = val.slice(-1)
  event.target.value = pinning.value[index]

  if (pinning.value[index] && index < 4) {
    pinRefs.value[index + 1]?.focus()
  }
}

const handlePinBackspace = (event, index) => {
  if (!pinning.value[index] && index > 0) {
    pinRefs.value[index - 1]?.focus()
  }
}

const handlePinPaste = (event) => {
  event.preventDefault()
  const pasted = event.clipboardData.getData('text').replace(/\D/g, '').slice(0, 5)
  if (!pasted) return

  pasted.split('').forEach((digit, i) => {
    pinning.value[i] = digit
  })

  const focusIndex = Math.min(pasted.length, 4)
  pinRefs.value[focusIndex]?.focus()
}

const verifyPin = async () => {
  const pin = pinning.value.join('')
  if (pin.length !== 5) return

  if (!email.value) {
    error.value = 'Please enter your email address.'
    return
  }

  loading.value = true
  error.value = ''
  try {
    const success = await auth.loginViaPin(email.value, pin)

    if (success) {
      await navigateTo('/')
    }
  } catch (e) {
    console.error('PIN verification failed', e)
    error.value = e?.data?.message || 'Invalid PIN. Please try again.'
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
      password: password.value,
    })

    if (success) {
      await navigateTo('/')
    }
  } catch (e) {
    error.value = e?.data?.message || e?.message || 'Invalid email or password. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-input {
  @apply w-full rounded-xl border border-slate-200 bg-white py-3 pl-4 pr-4 text-sm text-slate-900
    placeholder:text-slate-400
    focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10
    transition-all duration-200;
  line-height: 1.5rem;
}

.login-btn {
  @apply w-full flex items-center justify-center gap-2
    bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800
    text-white text-sm font-semibold py-3.5 rounded-xl
    transition-all duration-200
    shadow-lg shadow-indigo-600/25 hover:shadow-indigo-600/35
    disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none;
}

.alt-btn {
  @apply flex items-center justify-center gap-2 py-3
    border border-slate-200 rounded-xl text-sm font-medium text-slate-600
    hover:bg-slate-50 hover:border-slate-300
    transition-all duration-200 active:scale-[0.98];
}

.pin-input {
  @apply w-11 h-14 sm:w-12 sm:h-14
    rounded-xl border-2 border-slate-200 bg-white
    text-center text-xl font-bold text-slate-900
    focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10
    transition-all duration-200;
}

.pin-input--filled {
  @apply border-indigo-500 bg-indigo-50/50;
}

.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(12px);
}

.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-12px);
}

.slide-up-enter-active {
  transition: all 0.25s ease-out;
}

.slide-up-enter-from {
  opacity: 0;
  transform: translateY(8px);
}

input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
</style>
