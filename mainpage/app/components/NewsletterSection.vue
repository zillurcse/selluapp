<template>
  <section v-if="newsletter !== null && newsletter.newsletter_status == '1'" class="pt-4 md:pt-12 bg-white border-t border-gray-100 relative overflow-hidden">
    <div class="container mx-auto px-4 sm:px-6 relative">
      <!-- Decorative background blur -->
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[400px] h-[400px] bg-indigo-50/50 rounded-full blur-[100px] -z-10 pointer-events-none"></div>

      <div class="max-w-2xl mx-auto text-center animate-fade-in">
        <h2 class="text-3xl md:text-5xl font-extrabold tracking-tight text-gray-900 mb-4 font-heading">
          {{ newsletter.newsletter_title || 'Join the Inner Circle' }}
        </h2>
        <p class="text-gray-500 text-base md:text-lg mb-10">
          {{ newsletter.newsletter_description || 'Receive curated design inspiration, early product access, and member-only pricing delivered to your inbox.' }}
        </p>
        
        <form class="flex flex-col sm:flex-row gap-3 max-w-lg mx-auto" @submit.prevent="subscribe">
          <input
            v-model="email"
            type="email"
            :placeholder="newsletter.newsletter_placeholder || 'Enter your email address'"
            required
            :disabled="loading"
            class="flex-1 px-6 py-4 rounded-2xl border border-gray-200 bg-gray-50/50 text-gray-900 placeholder:text-gray-400 text-sm md:text-base outline-none transition-all focus:bg-white focus:ring-4 focus:ring-indigo-50 focus:border-indigo-300 disabled:opacity-50"
          />
          <button
            type="submit"
            :disabled="loading"
            class="px-8 py-4 bg-gray-900 text-white font-bold text-sm rounded-2xl transition-all hover:bg-indigo-600 hover:shadow-lg hover:-translate-y-0.5 whitespace-nowrap disabled:bg-gray-400 flex items-center justify-center gap-2 min-w-[120px]"
          >
            <span v-if="loading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            {{ loading ? 'Subscribing...' : (newsletter.newsletter_button_text || 'Subscribe') }}
          </button>
        </form>

        <div v-if="statusMessage" 
             :class="['mt-4 text-sm font-medium animate-fade-in', statusType === 'success' ? 'text-emerald-600' : 'text-rose-600']">
          {{ statusMessage }}
        </div>
        
        <p class="mt-6 text-xs text-gray-400 font-medium uppercase tracking-widest">
          {{ newsletter.newsletter_footer_text || 'Zero Spam. Only Inspiration.' }}
        </p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  newsletter: {
    type: Object,
    default: null
  }
})

const email = ref('')
const loading = ref(false)
const statusMessage = ref('')
const statusType = ref('') // 'success' or 'error'

const subscribe = async () => { 
  if (!email.value) return
  
  loading.value = true
  statusMessage.value = ''
  
  try {
    const { data, error } = await useApiFetch('/storefront/newsletter-subscribe', {
      method: 'POST',
      body: { email: email.value }
    })

    if (error.value) {
      statusType.value = 'error'
      statusMessage.value = error.value.data?.message || 'Something went wrong. Please try again.'
    } else {
      statusType.value = 'success'
      statusMessage.value = data.value?.message || 'Thank you for subscribing!'
      email.value = ''
    }
  } catch (e) {
    statusType.value = 'error'
    statusMessage.value = 'An unexpected error occurred.'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.font-heading {
  font-family: var(--font-heading, "Inter", sans-serif);
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  opacity: 0;
  animation: fadeIn 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
}
</style>
