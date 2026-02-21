<template>
  <div v-if="isOpen" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div 
      class="absolute inset-0 bg-[#0f172a]/70 backdrop-blur-md" 
      @click="$emit('close')"
    ></div>
    
    <!-- Modal Content -->
    <div class="bg-white rounded-2xl w-full max-w-[900px] overflow-hidden relative shadow-3xl animate-in zoom-in duration-300 mx-4">
      <!-- Header -->
      <div class="p-10 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-xl font-bold text-gray-800">Recent Sales</h3>
        <button 
          @click="$emit('close')" 
          class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:text-red-500 transition-colors"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="p-12 text-center text-gray-400 font-bold">Loading sales...</div>

      <!-- Table Content -->
      <div v-else class="p-0 overflow-x-auto max-h-[500px] overflow-y-auto">
        <table class="w-full text-left border-collapse">
          <thead class="sticky top-0 z-10">
            <tr class="bg-gray-50/90 backdrop-blur-sm">
              <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">Date</th>
              <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">Reference</th>
              <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">Customer</th>
              <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">Grand Total</th>
              <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">Status</th>
              <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">Payment</th>
              <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 text-center">Items</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="sales.length === 0">
              <td colspan="7" class="px-6 py-12 text-center text-gray-400 font-bold text-sm">No sales yet today</td>
            </tr>
            <template v-for="sale in sales" :key="sale.id">
              <tr class="hover:bg-gray-50/80 transition-colors group">
                <td class="px-6 py-4 text-[12px] font-bold text-gray-600 border-b border-gray-50 whitespace-nowrap">{{ sale.date }}</td>
                <td class="px-6 py-4 text-[13px] font-black text-indigo-700 border-b border-gray-50">{{ sale.reference }}</td>
                <td class="px-6 py-4 text-[13px] font-bold text-gray-500 border-b border-gray-50">{{ sale.customer_name }}</td>
                <td class="px-6 py-4 text-[13px] font-black text-gray-800 border-b border-gray-50">৳ {{ Number(sale.total).toLocaleString() }}</td>
                <td class="px-6 py-4 border-b border-gray-50">
                  <span class="px-2 py-1 rounded text-[10px] font-black uppercase tracking-wider"
                    :class="sale.status === 'paid' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-500'">
                    {{ sale.status }}
                  </span>
                </td>
                <td class="px-6 py-4 text-[12px] font-bold text-gray-500 border-b border-gray-50 capitalize">{{ sale.payment_method || '-' }}</td>
                <td class="px-6 py-4 border-b border-gray-50 text-center">
                  <button v-if="sale.items?.length" @click="toggleExpand(sale.id)"
                    class="p-1.5 text-gray-400 hover:text-blue-500 transition-colors">
                    <ChevronDown v-if="expanded !== sale.id" class="w-4 h-4" />
                    <ChevronUp v-else class="w-4 h-4" />
                  </button>
                </td>
              </tr>
              <!-- Expanded Items -->
              <tr v-if="expanded === sale.id && sale.items?.length" class="bg-indigo-50/30">
                <td colspan="7" class="px-8 py-3">
                  <div class="space-y-1">
                    <div v-for="item in sale.items" :key="item.name" class="flex items-center justify-between text-[12px] font-bold text-gray-600">
                      <span>{{ item.name }} × {{ item.qty }}</span>
                      <span>৳ {{ Number(item.subtotal).toLocaleString() }}</span>
                    </div>
                  </div>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { X, ChevronDown, ChevronUp } from 'lucide-vue-next'

defineProps({
  isOpen:  { type: Boolean, default: false },
  sales:   { type: Array, default: () => [] },
  loading: { type: Boolean, default: false },
})

defineEmits(['close'])

const expanded = ref(null)
const toggleExpand = (id) => { expanded.value = expanded.value === id ? null : id }
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
