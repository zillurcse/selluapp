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
          <span class="text-2xl font-extrabold text-white tracking-tight">EMU</span>
          <span class="text-2xl font-extrabold text-violet-400">.</span>
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
            Join thousands of customers who trust EMU for curated home decor, lighting, and accessories.
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
      <div class="relative z-10 text-white/30 text-xs">© 2026 EMU. All rights reserved.</div>
    </div>

    <!-- Right Panel — Login Form -->
    <div class="flex-1 flex items-center justify-center p-6 sm:p-10">
      <div class="w-full max-w-sm">

        <!-- Mobile Logo -->
        <div class="lg:hidden mb-8 text-center">
          <NuxtLink to="/" class="inline-flex items-center gap-1">
            <span class="text-2xl font-extrabold text-gray-900">EMU</span>
            <span class="text-2xl font-extrabold text-violet-600">.</span>
          </NuxtLink>
        </div>

        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-2">Welcome back 👋</h1>
          <p class="text-gray-500 text-sm">Sign in to your account to continue shopping</p>
        </div>

        <!-- Social Login -->
        <div class="flex gap-3 mb-6">
          <button class="flex-1 flex items-center justify-center gap-2 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 transition-all duration-200">
            <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
            Google
          </button>
          <button class="flex-1 flex items-center justify-center gap-2 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 transition-all duration-200">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            Facebook
          </button>
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

const form = ref({ email: '', password: '', remember: false })
const showPassword = ref(false)
const loading = ref(false)
const error = ref('')

const authStore = useAuthStore()

import { toast } from 'vue-sonner'

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
