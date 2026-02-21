<template>
  <div v-if="isOpen" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div 
      class="absolute inset-0 bg-[#0f172a]/70 backdrop-blur-md" 
      @click="$emit('close')"
    ></div>
    
    <!-- Modal Content -->
    <div class="bg-white rounded-2xl w-full max-w-2xl overflow-hidden relative shadow-3xl animate-in zoom-in duration-300 mx-4">
      <!-- Header -->
      <div class="p-10 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-xl font-bold text-gray-800">Hold List</h3>
        <button 
          @click="$emit('close')" 
          class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:text-red-500 transition-colors"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Table Content -->
      <div class="p-0 overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-gray-50/50">
              <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">ID</th>
              <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">Date</th>
              <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">Ref.ID</th>
              <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="holdItems.length === 0">
              <td colspan="4" class="px-6 py-12 text-center text-gray-400 font-bold text-sm">
                No Data Available
              </td>
            </tr>
            <tr 
              v-for="(item, index) in holdItems" 
              :key="item.id"
              class="hover:bg-gray-50/80 transition-colors group"
            >
              <td class="px-6 py-4 text-[13px] font-bold text-gray-700 border-b border-gray-50">{{ index + 1 }}</td>
              <td class="px-6 py-4 text-[13px] font-bold text-gray-600 border-b border-gray-50">{{ item.date }}</td>
              <td class="px-6 py-4 text-[13px] font-black text-blue-600 border-b border-gray-50">{{ item.refId }}</td>
              <td class="px-6 py-4 border-b border-gray-50">
                <div class="flex items-center justify-center gap-2">
                  <button 
                    @click="$emit('restore', item)"
                    class="p-2 text-green-500 bg-green-50 rounded-lg hover:bg-green-500 hover:text-white transition-all"
                    title="Restore Order"
                  >
                    <RefreshCw class="w-4 h-4" />
                  </button>
                  <button 
                    @click="$emit('delete', item.id)"
                    class="p-2 text-red-500 bg-red-50 rounded-lg hover:bg-red-500 hover:text-white transition-all"
                    title="Delete"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { X, RefreshCw, Trash2 } from 'lucide-vue-next'

defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  holdItems: {
    type: Array,
    default: () => []
  }
})

defineEmits(['close', 'restore', 'delete'])
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
