<template>
  <div class="min-h-screen pt-24 pb-12 bg-white">
    <!-- Clean Hero Section -->
    <header class="container mx-auto px-4 mb-20 animate-fade-in">
      <div class="max-w-3xl mx-auto text-center space-y-4">
        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 tracking-tight">
          Contact Us
        </h1>
        <p class="text-base text-gray-500 max-w-xl mx-auto leading-relaxed">
          We're here to help with any questions about our products, orders, or anything else you might need.
        </p>
      </div>
    </header>

    <main class="container mx-auto px-4 grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-24 max-w-6xl">
      <!-- Minimalist Contact Info -->
      <aside class="lg:col-span-4 space-y-12 animate-slide-up">
        <div class="space-y-8">
          <!-- Address -->
          <div class="space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Our Showroom</h3>
            <p class="text-gray-900 font-medium leading-relaxed">
              {{ storefrontStore.vendorProfile?.address || '123 Minimalist Way, Design District, NY 10001' }}
            </p>
          </div>

          <!-- Contact Details -->
          <div class="space-y-6">
            <div class="space-y-1">
              <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Call Us</h3>
              <p class="text-gray-900 font-medium">
                {{ storefrontStore.vendorProfile?.phone || '+1 (555) 000-0000' }}
              </p>
            </div>
            <div class="space-y-1">
              <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Email</h3>
              <p class="text-gray-900 font-medium">
                {{ storefrontStore.vendorProfile?.email || 'hello@yourstore.com' }}
              </p>
            </div>
          </div>

          <!-- Business Hours -->
          <div class="pt-8 border-t border-gray-100 space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Business Hours</h3>
            <div class="space-y-2 text-sm text-gray-600">
              <div class="flex justify-between">
                <span>Mon — Fri</span>
                <span class="text-gray-900">09:00 - 18:00</span>
              </div>
              <div class="flex justify-between">
                <span>Saturday</span>
                <span class="text-gray-900">10:00 - 16:00</span>
              </div>
              <div class="flex justify-between font-medium">
                <span>Sunday</span>
                <span class="text-gray-400">Closed</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Social Links -->
        <div v-if="storefrontStore.vendorProfile?.facebook || storefrontStore.vendorProfile?.instagram || storefrontStore.vendorProfile?.twitter" class="pt-8 border-t border-gray-100 space-y-4">
          <h3 class="text-xs font-bold uppercase tracking-widest text-gray-400">Connect</h3>
          <div class="flex gap-6">
            <a v-if="storefrontStore.vendorProfile?.facebook" :href="storefrontStore.vendorProfile.facebook" target="_blank" class="text-gray-400 hover:text-gray-900 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
            </a>
            <a v-if="storefrontStore.vendorProfile?.instagram" :href="storefrontStore.vendorProfile.instagram" target="_blank" class="text-gray-400 hover:text-gray-900 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
            </a>
            <a v-if="storefrontStore.vendorProfile?.twitter" :href="storefrontStore.vendorProfile.twitter" target="_blank" class="text-gray-400 hover:text-gray-900 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg>
            </a>
          </div>
        </div>
      </aside>

      <!-- Minimalist Form -->
      <section class="lg:col-span-8 animate-slide-up animation-delay-300">
        <form @submit.prevent="submitForm" class="space-y-8">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-2">
              <label for="name" class="text-xs font-bold text-gray-500 ml-1">Full Name</label>
              <input 
                v-model="form.sender_name"
                type="text" 
                id="name" 
                required
                placeholder="Name"
                class="w-full px-0 py-3 bg-transparent border-b-2 border-gray-100 focus:border-gray-900 outline-none transition-all placeholder:text-gray-200"
              />
            </div>
            <div class="space-y-2">
              <label for="email" class="text-xs font-bold text-gray-500 ml-1">Email Address</label>
              <input 
                v-model="form.sender_email"
                type="email" 
                id="email" 
                required
                placeholder="Email"
                class="w-full px-0 py-3 bg-transparent border-b-2 border-gray-100 focus:border-gray-900 outline-none transition-all placeholder:text-gray-200"
              />
            </div>
          </div>

          <div class="space-y-2">
            <label for="subject" class="text-xs font-bold text-gray-500 ml-1">Subject</label>
            <input 
              v-model="form.subject"
              type="text" 
              id="subject" 
              placeholder="How can we help?"
              class="w-full px-0 py-3 bg-transparent border-b-2 border-gray-100 focus:border-gray-900 outline-none transition-all placeholder:text-gray-200"
            />
          </div>

          <div class="space-y-2">
            <label for="message" class="text-xs font-bold text-gray-500 ml-1">Message</label>
            <textarea 
              v-model="form.message"
              id="message" 
              required
              rows="5"
              placeholder="Your message..."
              class="w-full px-0 py-3 bg-transparent border-b-2 border-gray-100 focus:border-gray-900 outline-none transition-all placeholder:text-gray-200 resize-none"
            ></textarea>
          </div>

          <!-- Simplified Professional Button -->
          <button 
            type="submit" 
            :disabled="isSubmitting"
            class="h-14 px-10 bg-gray-900 text-white font-bold text-sm uppercase tracking-widest hover:bg-gray-800 active:scale-95 transition-all disabled:opacity-50 disabled:pointer-events-none"
          >
            <span v-if="!isSubmitting">Send Message</span>
            <span v-else class="flex items-center gap-2">
              <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Sending...
            </span>
          </button>
        </form>
      </section>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { toast } from 'vue-sonner'

const storefrontStore = useStorefrontStore()
const config = useRuntimeConfig()
const apiBase = config.public.apiBase

const isSubmitting = ref(false)
const form = ref({
  sender_name: '',
  sender_email: '',
  subject: '',
  message: ''
})

const submitForm = async () => {
  isSubmitting.value = true
  
  try {
    const domain = window.location.hostname.replace(/^www\./, '')
    
    await $fetch(`${apiBase}/storefront/contact-message`, {
      method: 'POST',
      body: form.value,
      headers: {
        'X-Tenant-Domain': domain
      }
    })

    toast.success('Message sent successfully')
    
    // Reset form
    form.value = {
      sender_name: '',
      sender_email: '',
      subject: '',
      message: ''
    }
  } catch (error: any) {
    console.error('Contact Form Error:', error)
    toast.error('Failed to send message')
  } finally {
    isSubmitting.value = false
  }
}

useHead({
  title: 'Contact Us',
  meta: [
    { name: 'description', content: 'Get in touch with us.' }
  ]
})
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.animate-slide-up {
  opacity: 0;
  animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

.animation-delay-300 {
  animation-delay: 200ms;
}
</style>
