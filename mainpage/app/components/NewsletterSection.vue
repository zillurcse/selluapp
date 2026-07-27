<template>
  <section v-if="newsletter !== null && newsletter.newsletter_status == '1'" class="section-pad bg-[var(--surface)] border-t border-[var(--border)]">
    <div class="container mx-auto">
      <div class="max-w-xl mx-auto text-center">
        <h2 class="section-title mb-3">
          {{ newsletter.newsletter_title || 'Stay in the loop' }}
        </h2>
        <p class="text-[var(--muted-foreground)] text-sm sm:text-base mb-8 leading-relaxed">
          {{ newsletter.newsletter_description || 'Get new arrivals, exclusive offers, and store updates in your inbox.' }}
        </p>

        <form class="flex flex-col sm:flex-row gap-2.5" @submit.prevent="subscribe">
          <input
            v-model="email"
            type="email"
            :placeholder="newsletter.newsletter_placeholder || 'Enter your email'"
            required
            :disabled="loading"
            class="flex-1 px-4 py-3 rounded-xl border border-[var(--border)] bg-white text-gray-900 placeholder:text-gray-400 text-sm outline-none transition-all focus:border-gray-400 disabled:opacity-50"
          />
          <button
            type="submit"
            :disabled="loading"
            class="btn-primary min-w-[120px] disabled:opacity-50"
          >
            <span v-if="loading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            {{ loading ? '...' : (newsletter.newsletter_button_text || 'Subscribe') }}
          </button>
        </form>

        <div
          v-if="statusMessage"
          :class="['mt-4 text-sm font-medium', statusType === 'success' ? 'text-emerald-600' : 'text-[var(--sale)]']"
        >
          {{ statusMessage }}
        </div>

        <p class="mt-5 text-xs text-[var(--muted-foreground)]">
          {{ newsletter.newsletter_footer_text || 'No spam. Unsubscribe anytime.' }}
        </p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'

defineProps({
  newsletter: {
    type: Object,
    default: null
  }
})

const email = ref('')
const loading = ref(false)
const statusMessage = ref('')
const statusType = ref('')

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
