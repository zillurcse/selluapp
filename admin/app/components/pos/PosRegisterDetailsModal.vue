<template>
  <div v-if="isOpen" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div 
      class="absolute inset-0 bg-[#0f172a]/70 backdrop-blur-md" 
      @click="$emit('close')"
    ></div>
    
    <!-- Modal Content -->
    <div class="bg-white rounded-2xl w-full max-w-lg overflow-hidden relative shadow-3xl animate-in zoom-in duration-300 mx-4">
      <!-- Header -->
      <div class="p-10 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-xl font-bold text-gray-800">Register Details</h3>
        <button 
          @click="$emit('close')" 
          class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:text-red-500 transition-colors"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Register Info -->
      <div class="p-8 space-y-4">
        <div v-if="loading" class="text-center text-gray-400 font-bold py-8">Loading stats...</div>
        <template v-else>
          <div class="grid grid-cols-1 gap-3">
            <div class="flex items-center justify-between py-3 border-b border-gray-50">
              <span class="text-[14px] font-bold text-gray-500">Total Orders</span>
              <span class="text-[15px] font-black text-gray-800">{{ stats.total_orders || 0 }}</span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-gray-50">
              <span class="text-[14px] font-bold text-gray-500">Total Sales</span>
              <span class="text-[15px] font-black text-blue-600">৳ {{ Number(stats.total_sales || 0).toLocaleString() }}</span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-gray-50">
              <span class="text-[14px] font-bold text-gray-500">Cash Payment</span>
              <span class="text-[15px] font-black text-green-600">৳ {{ Number(stats.cash_total || 0).toLocaleString() }}</span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-gray-50">
              <span class="text-[14px] font-bold text-gray-500">Card Payment</span>
              <span class="text-[15px] font-black text-gray-800">৳ {{ Number(stats.card_total || 0).toLocaleString() }}</span>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-gray-50">
              <span class="text-[14px] font-bold text-gray-500">Other Payment</span>
              <span class="text-[15px] font-black text-gray-800">৳ {{ Number(stats.other_total || 0).toLocaleString() }}</span>
            </div>
            <div class="flex items-center justify-between py-3">
              <span class="text-[14px] font-bold text-gray-500">Total Refund</span>
              <span class="text-[15px] font-black text-red-500">৳ {{ Number(stats.total_refund || 0).toLocaleString() }}</span>
            </div>
          </div>

          <div class="bg-indigo-50/50 p-6 rounded-2xl border border-indigo-100 flex flex-col items-center gap-2 mt-4">
            <span class="text-[12px] font-black text-indigo-500 uppercase tracking-widest">Today's Total Cash</span>
            <h2 class="text-3xl font-black text-indigo-700">৳ {{ Number(stats.cash_total || 0).toLocaleString() }}</h2>
          </div>
        </template>
      </div>

      <!-- Footer Actions -->
      <div class="p-10 bg-gray-50/50 flex gap-4">
        <button @click="$emit('close')" class="flex-1 py-4 bg-white border border-gray-100 text-gray-500 rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-gray-100 transition-all">Close</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { X } from 'lucide-vue-next'

defineProps({
  isOpen:  { type: Boolean, default: false },
  stats:   { type: Object, default: () => ({}) },
  loading: { type: Boolean, default: false },
})

defineEmits(['close'])
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
