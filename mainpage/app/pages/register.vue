<template>
  <div class="min-h-screen bg-gray-50 flex">

    <!-- Left Panel — Brand (same as login) -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 flex-col justify-between p-14">
      <div class="absolute inset-0 opacity-5">
        <div class="absolute top-20 left-20 w-72 h-72 rounded-full bg-violet-400 blur-3xl"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 rounded-full bg-blue-400 blur-3xl"></div>
      </div>
      <div class="relative z-10">
        <NuxtLink to="/" class="flex items-center gap-2">
          <template v-if="storefrontStore.vendorProfile">
            <img v-if="storefrontStore.vendorProfile.logo_url" :src="storefrontStore.vendorProfile.logo_url" :alt="storefrontStore.vendorProfile.store_name" class="h-8 max-w-[150px] object-contain" />
            <span v-else class="text-2xl font-extrabold tracking-tight text-white font-heading">
              {{ storefrontStore.vendorProfile.store_name }}<span class="text-violet-400">.</span>
            </span>
          </template>
          <template v-else>
            <span class="text-2xl font-extrabold text-white tracking-tight">EMU</span>
            <span class="text-2xl font-extrabold text-violet-400">.</span>
          </template>
        </NuxtLink>
      </div>
      <div class="relative z-10 flex flex-col gap-6">
        <!-- Benefit list -->
        <div v-for="benefit in benefits" :key="benefit.title" class="flex items-start gap-4">
          <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-xl border border-white/10 shrink-0">{{ benefit.icon }}</div>
          <div>
            <div class="text-white font-bold text-base mb-0.5">{{ benefit.title }}</div>
            <div class="text-white/40 text-sm">{{ benefit.desc }}</div>
          </div>
        </div>
      </div>
      <div class="relative z-10 text-white/30 text-xs">© {{ new Date().getFullYear() }} {{ storefrontStore.vendorProfile?.store_name || 'EMU' }}. All rights reserved.</div>
    </div>

    <!-- Right Panel — Register Form -->
    <div class="flex-1 flex items-center justify-center p-6 sm:p-10 overflow-y-auto">
      <div class="w-full max-w-sm py-8">

        <!-- Mobile Logo -->
        <div class="lg:hidden mb-8 flex justify-center text-center">
          <NuxtLink to="/" class="inline-flex items-center gap-1">
            <template v-if="storefrontStore.vendorProfile">
              <img v-if="storefrontStore.vendorProfile.logo_url" :src="storefrontStore.vendorProfile.logo_url" :alt="storefrontStore.vendorProfile.store_name" class="h-8 max-w-[150px] object-contain" />
              <span v-else class="text-2xl font-extrabold tracking-tight text-gray-900 font-heading">
                {{ storefrontStore.vendorProfile.store_name }}<span class="text-violet-600">.</span>
              </span>
            </template>
            <template v-else>
              <span class="text-2xl font-extrabold text-gray-900 tracking-tight">EMU</span>
              <span class="text-2xl font-extrabold text-violet-600">.</span>
            </template>
          </NuxtLink>
        </div>

        <div class="mb-8">
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-2">Create account ✨</h1>
          <p class="text-gray-500 text-sm">Join us and start your premium shopping journey</p>
        </div>

        <!-- Social -->
        <div class="flex flex-col gap-3 mb-6">
          <!-- Google GSI Button (rendered by Google SDK) -->
          <div id="google-signin-btn" class="w-full overflow-hidden rounded-xl"></div>
        </div>

        <div class="flex items-center gap-3 mb-6">
          <div class="flex-1 h-px bg-gray-100"></div>
          <span class="text-xs text-gray-400 font-medium">or with email</span>
          <div class="flex-1 h-px bg-gray-100"></div>
        </div>

        <form @submit.prevent="handleRegister" class="flex flex-col gap-4">
          <!-- Name row -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">First name</label>
              <input v-model="form.firstName" type="text" placeholder="John" required
                class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/5 bg-white placeholder-gray-400" />
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1.5">Last name</label>
              <input v-model="form.lastName" type="text" placeholder="Doe" required
                class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/5 bg-white placeholder-gray-400" />
            </div>
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email address</label>
            <input v-model="form.email" type="email" placeholder="you@example.com" required
              class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/5 bg-white placeholder-gray-400" />
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
            <div class="relative">
              <input v-model="form.password" :type="showPassword ? 'text' : 'password'" placeholder="Min. 8 characters" required
                class="w-full px-4 py-3 pr-11 rounded-xl border border-gray-200 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/5 bg-white placeholder-gray-400" />
              <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition border-none bg-transparent cursor-pointer p-1">
                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
              </button>
            </div>
            <!-- Password strength bar -->
            <div v-if="form.password" class="mt-2 flex gap-1">
              <div v-for="i in 4" :key="i" class="flex-1 h-1 rounded-full transition-all duration-300"
                :class="passwordStrength >= i ? strengthColor : 'bg-gray-100'"></div>
            </div>
            <p v-if="form.password" class="text-xs mt-1" :class="strengthTextColor">{{ strengthLabel }}</p>
          </div>

          <label class="flex items-start gap-2.5 cursor-pointer select-none">
            <input v-model="form.agree" type="checkbox" required class="w-4 h-4 mt-0.5 rounded border-gray-300 cursor-pointer" />
            <span class="text-sm text-gray-600 leading-relaxed">
              I agree to the
              <NuxtLink to="/terms" class="font-semibold text-gray-900 hover:text-violet-600 transition">Terms of Service</NuxtLink>
              and
              <NuxtLink to="/privacy" class="font-semibold text-gray-900 hover:text-violet-600 transition">Privacy Policy</NuxtLink>
            </span>
          </label>

          <div v-if="error" class="px-4 py-3 bg-red-50 border border-red-100 rounded-xl text-sm text-red-600 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
            {{ error }}
          </div>

          <button type="submit" :disabled="loading"
            class="w-full py-3.5 bg-gray-900 text-white font-bold text-sm rounded-xl transition-all hover:-translate-y-0.5 hover:shadow-lg disabled:opacity-60 disabled:cursor-not-allowed border-none cursor-pointer mt-1 flex items-center justify-center gap-2">
            <svg v-if="loading" class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
            {{ loading ? 'Creating account...' : 'Create account' }}
          </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
          Already have an account?
          <NuxtLink to="/login" class="font-bold text-gray-900 hover:text-violet-600 transition ml-1">Sign in →</NuxtLink>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: false })

const benefits = [
  { icon: '🎁', title: 'Exclusive Member Deals', desc: 'Get access to members-only discounts and early sales.' },
  { icon: '📦', title: 'Order Tracking', desc: 'Track all your orders in real time from your dashboard.' },
  { icon: '💳', title: 'Saved Payments', desc: 'Checkout faster with saved addresses and payment methods.' },
  { icon: '⭐', title: 'Wishlist & Reviews', desc: 'Save your favourites and share your honest reviews.' },
]

const form = ref({ firstName: '', lastName: '', email: '', password: '', agree: false })
const showPassword = ref(false)
const loading = ref(false)
const error = ref('')

const passwordStrength = computed(() => {
  const p = form.value.password
  if (!p) return 0
  let s = 0
  if (p.length >= 8) s++
  if (/[A-Z]/.test(p)) s++
  if (/[0-9]/.test(p)) s++
  if (/[^A-Za-z0-9]/.test(p)) s++
  return s
})
const strengthColor = computed(() => {
  return ['bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-green-500'][passwordStrength.value - 1] || 'bg-gray-100'
})
const strengthTextColor = computed(() => {
  return ['text-red-500', 'text-orange-500', 'text-yellow-600', 'text-green-600'][passwordStrength.value - 1] || 'text-gray-400'
})
const strengthLabel = computed(() => {
  return ['Too weak', 'Fair', 'Good', 'Strong'][passwordStrength.value - 1] || ''
})

const authStore = useAuthStore()
const storefrontStore = useStorefrontStore()
const { renderGoogleButton, error: googleError } = useGoogleAuth()

import { toast } from 'vue-sonner'
import { onMounted, watch } from 'vue'

onMounted(async () => {
  if (storefrontStore.topCategories.length === 0) {
    await storefrontStore.fetchStorefront()
  }
  // Render Google button into the target div
  await renderGoogleButton('#google-signin-btn')
})

watch(googleError, (msg) => {
  if (msg) {
    error.value = msg
    toast.error(msg)
  }
})

const handleRegister = async () => {
  error.value = ''
  loading.value = true
  
  try {
    const success = await authStore.register({
      first_name: form.value.firstName,
      last_name: form.value.lastName,
      email: form.value.email,
      password: form.value.password,
      password_confirmation: form.value.password,
    })

    if (success) {
      toast.success('Registration successful! Welcome!')
      navigateTo('/')
    } else {
      error.value = 'Registration failed. Please try again.'
      toast.error('Registration failed. Please try again.')
    }
  } catch (err: any) {
    console.error('Registration error:', err)
    error.value = err.data?.message || 'An error occurred during registration.'
    toast.error(error.value)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@keyframes spin { to { transform: rotate(360deg); } }
.animate-spin { animation: spin 0.8s linear infinite; }
</style>
