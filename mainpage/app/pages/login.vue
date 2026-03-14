<template>
  <div class="min-h-screen bg-gray-50 flex">

    <!-- Left Panel — Brand -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 flex-col justify-between p-14">
      <!-- Background pattern -->
      <div class="absolute inset-0 opacity-5">
        <div class="absolute top-20 left-20 w-72 h-72 rounded-full bg-violet-400 blur-3xl"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 rounded-full bg-blue-400 blur-3xl"></div>
      </div>

      <!-- Logo -->
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

      <!-- Center content -->
      <div class="relative z-10 flex flex-col gap-8">
        <div class="flex gap-4">
          <div v-for="i in 3" :key="i" class="w-16 h-16 rounded-2xl bg-white/10 flex items-center justify-center text-3xl backdrop-blur-sm border border-white/10">
            {{ ['🛍️','⌚','🏺'][i-1] }}
          </div>
        </div>
        <div>
          <h2 class="text-4xl font-extrabold text-white leading-tight mb-4">
            Your premium<br/>shopping <span class="text-violet-400">experience</span><br/>starts here.
          </h2>
          <p class="text-white/50 text-base leading-relaxed max-w-sm">
            Join thousands of customers who trust {{ storefrontStore.vendorProfile?.store_name || 'EMU' }} for curated home decor, lighting, and accessories.
          </p>
        </div>
        <!-- Stats -->
        <div class="flex gap-8">
          <div>
            <div class="text-2xl font-extrabold text-white">50K+</div>
            <div class="text-white/40 text-xs mt-0.5">Happy Customers</div>
          </div>
          <div>
            <div class="text-2xl font-extrabold text-white">2K+</div>
            <div class="text-white/40 text-xs mt-0.5">Products</div>
          </div>
          <div>
            <div class="text-2xl font-extrabold text-white">4.9★</div>
            <div class="text-white/40 text-xs mt-0.5">Avg Rating</div>
          </div>
        </div>
      </div>

      <!-- Bottom tagline -->
      <div class="relative z-10 text-white/30 text-xs">© {{ new Date().getFullYear() }} {{ storefrontStore.vendorProfile?.store_name || 'EMU' }}. All rights reserved.</div>
    </div>

    <!-- Right Panel — Login Form -->
    <div class="flex-1 flex items-center justify-center p-6 sm:p-10">
      <div class="w-full max-w-sm">

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

        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-2">Welcome back 👋</h1>
          <p class="text-gray-500 text-sm">Sign in to your account to continue shopping</p>
        </div>

        <!-- Social Login -->
        <div class="flex flex-col gap-3 mb-6">
          <!-- Google GSI Button (rendered by Google SDK) -->
          <div id="google-signin-btn" class="w-full overflow-hidden rounded-xl"></div>
        </div>

        <!-- Divider -->
        <div class="flex items-center gap-3 mb-6">
          <div class="flex-1 h-px bg-gray-100"></div>
          <span class="text-xs text-gray-400 font-medium">or continue with email</span>
          <div class="flex-1 h-px bg-gray-100"></div>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleLogin" class="flex flex-col gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email address</label>
            <input
              v-model="form.email"
              type="email"
              placeholder="you@example.com"
              required
              class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm outline-none transition-all duration-200 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/5 bg-white placeholder-gray-400"
            />
          </div>
          <div>
            <div class="flex justify-between items-center mb-1.5">
              <label class="block text-sm font-semibold text-gray-700">Password</label>
              <NuxtLink to="/forgot-password" class="text-xs text-violet-600 font-semibold hover:text-violet-700 transition">Forgot password?</NuxtLink>
            </div>
            <div class="relative">
              <input
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Enter your password"
                required
                class="w-full px-4 py-3 pr-11 rounded-xl border border-gray-200 text-sm outline-none transition-all duration-200 focus:border-gray-900 focus:ring-2 focus:ring-gray-900/5 bg-white placeholder-gray-400"
              />
              <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition border-none bg-transparent cursor-pointer p-1">
                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
              </button>
            </div>
          </div>

          <!-- Remember me -->
          <label class="flex items-center gap-2 cursor-pointer select-none">
            <input v-model="form.remember" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-gray-900 cursor-pointer" />
            <span class="text-sm text-gray-600">Remember me for 30 days</span>
          </label>

          <!-- Error message -->
          <div v-if="error" class="px-4 py-3 bg-red-50 border border-red-100 rounded-xl text-sm text-red-600 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
            {{ error }}
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full py-3.5 bg-gray-900 text-white font-bold text-sm rounded-xl transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:translate-y-0 border-none cursor-pointer mt-1 flex items-center justify-center gap-2"
          >
            <svg v-if="loading" class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
            {{ loading ? 'Signing in...' : 'Sign in' }}
          </button>
        </form>

        <!-- Register link -->
        <p class="text-center text-sm text-gray-500 mt-6">
          Don't have an account?
          <NuxtLink to="/register" class="font-bold text-gray-900 hover:text-violet-600 transition ml-1">Create one →</NuxtLink>
        </p>

      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
definePageMeta({ layout: false })

import { onMounted } from 'vue'
import { toast } from 'vue-sonner'

const form = ref({ email: '', password: '', remember: false })
const showPassword = ref(false)
const loading = ref(false)
const error = ref('')

const authStore = useAuthStore()
const storefrontStore = useStorefrontStore()
const { renderGoogleButton, error: googleError } = useGoogleAuth()

onMounted(async () => {
  if (storefrontStore.topCategories.length === 0) {
    await storefrontStore.fetchStorefront()
  }
  // Render Google button into the target div
  await renderGoogleButton('#google-signin-btn')
})

// Watch for Google auth errors
watch(googleError, (msg) => {
  if (msg) {
    error.value = msg
    toast.error(msg)
  }
})

const handleLogin = async () => {
  error.value = ''
  loading.value = true

  try {
    const success = await authStore.login({
      email: form.value.email,
      password: form.value.password,
    })

    if (success) {
      toast.success('Successfully logged in!')
      navigateTo('/')
    } else {
      error.value = 'Invalid email or password. Please try again.'
      toast.error('Invalid email or password. Please try again.')
    }
  } catch (err: any) {
    console.error('Login error:', err)
    error.value = err.data?.message || err.data?.errors?.email?.[0] || 'Invalid email or password. Please try again.'
    toast.error(error.value)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@keyframes spin {
  to { transform: rotate(360deg); }
}
.animate-spin { animation: spin 0.8s linear infinite; }
</style>
