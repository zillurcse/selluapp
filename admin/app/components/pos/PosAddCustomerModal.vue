<template>
  <div v-if="isOpen" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div 
      class="absolute inset-0 bg-[#0f172a]/70 backdrop-blur-md" 
      @click="$emit('close')"
    ></div>
    
    <!-- Modal Content -->
    <div class="bg-white rounded-lg w-full max-w-[500px] overflow-hidden relative shadow-3xl animate-in zoom-in duration-300 mx-4">
      <!-- Header -->
      <div class="p-8 pb-4 flex items-center justify-between">
        <h3 class="text-xl font-black text-[#0f172a]">Add New Customer</h3>
        <button 
          @click="$emit('close')" 
          class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:text-red-500 transition-colors"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Form -->
      <div class="p-8 pt-4 space-y-6">
        <div class="grid grid-cols-1 gap-6">
          <!-- Full Name -->
          <div class="space-y-2">
            <label class="text-[12px] font-black text-gray-400 uppercase tracking-widest">Full Name</label>
            <input 
              v-model="customer.name" 
              type="text" 
              placeholder="Enter customer name..." 
              class="w-full px-5 py-3.5 bg-gray-50/50 border border-gray-100/50 rounded-xl text-[13px] font-bold outline-none focus:ring-2 focus:ring-primary-500/10 placeholder:text-gray-300 transition-all" 
            />
          </div>

          <!-- Phone & Email -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="text-[12px] font-black text-gray-400 uppercase tracking-widest">Phone Number</label>
              <input 
                v-model="customer.phone" 
                type="text" 
                placeholder="Enter phone..." 
                class="w-full px-5 py-3.5 bg-gray-50/50 border border-gray-100/50 rounded-xl text-[13px] font-bold outline-none focus:ring-2 focus:ring-primary-500/10 placeholder:text-gray-300 transition-all" 
              />
            </div>
            <div class="space-y-2">
              <label class="text-[12px] font-black text-gray-400 uppercase tracking-widest">Email (Optional)</label>
              <input 
                v-model="customer.email" 
                type="email" 
                placeholder="Enter email..." 
                class="w-full px-5 py-3.5 bg-gray-50/50 border border-gray-100/50 rounded-xl text-[13px] font-bold outline-none focus:ring-2 focus:ring-primary-500/10 placeholder:text-gray-300 transition-all" 
              />
            </div>
          </div>

          <!-- Address -->
          <div class="space-y-2">
            <label class="text-[12px] font-black text-gray-400 uppercase tracking-widest">Full Address</label>
            <textarea 
              v-model="customer.address" 
              placeholder="Enter address..." 
              class="w-full px-5 py-3.5 bg-gray-50/50 border border-gray-100/50 rounded-xl text-[13px] font-bold outline-none focus:ring-2 focus:ring-primary-500/10 placeholder:text-gray-300 h-24 resize-none transition-all"
            ></textarea>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="px-8 py-6 flex items-center border-t border-gray-50 relative">
        <div class="flex-1"></div>
        <button 
          @click="$emit('close')" 
          class="text-gray-400 font-black text-[11px] uppercase tracking-[0.2em] hover:text-gray-600 transition-colors"
        >
          Cancel
        </button>
        <div class="flex-1 flex justify-end">
          <button 
            @click="submit" 
            class="px-10 py-3.5 bg-primary-600 text-white rounded-xl font-black text-[12px] uppercase tracking-widest hover:bg-primary-700 shadow-xl shadow-primary-100 transition-all shrink-0"
          >
            Save Customer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { X } from 'lucide-vue-next'
import { toast } from 'vue-sonner'
import useCrud from '~/composables/useCrud'

const props = defineProps({
  isOpen: { type: Boolean, default: false }
})

const emit = defineEmits(['close', 'save'])

const { getHeaders } = useCrud()
const config  = useRuntimeConfig()
const baseURL = config.public.apiBase

const isSaving = ref(false)

const customer = ref({
  name: '', phone: '', email: '', address: ''
})

watch(() => props.isOpen, (val) => {
  if (val) customer.value = { name: '', phone: '', email: '', address: '' }
})

const submit = async () => {
  if (!customer.value.name || !customer.value.phone) {
    toast.error('Name and phone are required')
    return
  }

  isSaving.value = true
  try {
    // Split name into first/last
    const parts      = customer.value.name.trim().split(' ')
    const first_name = parts[0]
    const last_name  = parts.slice(1).join(' ') || '-'

    const created = await $fetch('/vendor/customers', {
      baseURL,
      method: 'POST',
      headers: getHeaders(),
      body: {
        first_name,
        last_name,
        phone:    customer.value.phone,
        email:    customer.value.email || undefined,
        address:  customer.value.address || undefined,
        password: Math.random().toString(36).slice(-8), // temp password
      }
    })

    const newCustomer = {
      id:    created.customer?.id || Date.now(),
      name:  customer.value.name,
      phone: customer.value.phone,
      email: customer.value.email,
    }
    toast.success('Customer added successfully')
    emit('save', newCustomer)
  } catch (err) {
    toast.error(err.data?.message || 'Failed to add customer')
  } finally {
    isSaving.value = false
  }
}
</script>

<style scoped>
@keyframes zoom-in {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}

.animate-in {
  animation: zoom-in 0.3s ease-out forwards;
}

.shadow-3xl {
  box-shadow: 0 35px 60px -15px rgba(0, 0, 0, 0.3);
}
</style>
