<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-[#0f172a]/80 backdrop-blur-lg" @click="$emit('close')"></div>
    <div class="bg-white rounded-[3rem] p-12 max-w-md w-full text-center relative shadow-3xl animate-in zoom-in duration-300">
      <div class="w-28 h-28 bg-[#22c55e] text-white rounded-full flex items-center justify-center mx-auto mb-10 shadow-2xl shadow-green-200">
        <Check class="w-14 h-14 stroke-[4]" />
      </div>
      <h2 class="text-4xl font-black text-[#0f172a] mb-2">Sale Complete!</h2>
      <p class="text-gray-500 font-bold mb-1">Ref: <span class="text-blue-600">{{ lastOrderRef }}</span></p>
      <p class="text-[#64748b] font-bold mb-8 leading-relaxed px-6 text-lg">Total: <span class="text-green-600 font-black">৳{{ lastOrderTotal }}</span></p>
      <div class="flex flex-col gap-4">
        <button @click="$emit('printInvoice')" class="w-full py-5 bg-[#0f172a] text-white rounded-2xl font-black hover:bg-black transition-all text-xl shadow-xl shadow-gray-200 flex items-center justify-center gap-3">
          <Printer class="w-6 h-6" /> Print Invoice
        </button>
        <button @click="$emit('newSale')" class="w-full py-5 text-gray-400 font-black hover:text-[#0f172a] transition-all uppercase tracking-widest text-sm">New Sale</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Check, Printer } from 'lucide-vue-next'

const props = defineProps({
  isOpen: { type: Boolean, default: false },
  lastOrderRef: { type: String, default: '' },
  lastOrderTotal: { type: Number, default: 0 }
})

const emit = defineEmits(['close', 'printInvoice', 'newSale'])
</script>

<style scoped>
@keyframes zoom-in {
  from { opacity: 0; transform: scale(0.95); }
  to   { opacity: 1; transform: scale(1); }
}
.animate-in { animation: zoom-in 0.3s ease-out forwards; }
.shadow-3xl { box-shadow: 0 35px 60px -15px rgba(0,0,0,0.3); }
</style>
