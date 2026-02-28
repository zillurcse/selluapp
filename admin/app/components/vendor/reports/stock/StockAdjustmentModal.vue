<template>
  <Teleport to="body">
    <div v-if="modelValue" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
      <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden border border-gray-100 transform transition-all">
        <div class="p-8 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
          <div>
             <h3 class="text-2xl font-black text-gray-900 tracking-tight">Adjust Inventory</h3>
             <p class="text-xs font-bold text-gray-400 mt-1">Manual stock modification</p>
          </div>
          <button @click="$emit('update:modelValue', false)" class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-gray-400 hover:text-rose-600 hover:bg-rose-50 hover:rotate-90 transition-all shadow-sm">
            <X class="w-5 h-5" />
          </button>
        </div>
        
        <div class="p-8 space-y-6">
          <div class="space-y-2">
            <AppSearchSelect 
              v-model="form.product_id"
              :items="products"
              label="Product"
              placeholder="Select a product to adjust..."
            />
          </div>
          
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Type</label>
              <select v-model="form.type" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-bold text-gray-900 outline-none appearance-none">
                <option value="add">Add (+)</option>
                <option value="subtract">Subtract (-)</option>
              </select>
            </div>
            <div class="space-y-2">
              <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Quantity</label>
              <input type="number" v-model="form.quantity" min="1" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-bold text-gray-900 outline-none" placeholder="1" />
            </div>
          </div>

          <div class="space-y-2">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Adjustment Note (Optional)</label>
            <textarea v-model="form.note" rows="2" class="w-full px-5 py-4 bg-gray-50 border border-gray-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-bold text-gray-900 outline-none resize-none" placeholder="e.g. Broken item, Gift, Audit correction..."></textarea>
          </div>
        </div>
        
        <div class="p-8 border-t border-gray-50 bg-gray-50/50 flex justify-end gap-3">
          <button @click="$emit('update:modelValue', false)" class="px-8 py-3 rounded-xl text-sm font-bold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm">
            Cancel
          </button>
          <button @click="handleAdjust" :disabled="loading || !form.product_id" class="flex items-center justify-center min-w-[140px] gap-2 px-8 py-3 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-xl shadow-indigo-600/20">
            <div v-if="loading" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
            <span v-else>Confirm</span>
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { X } from 'lucide-vue-next'
import AppSearchSelect from '~/components/AppSearchSelect.vue'
import useCrud from '~/composables/useCrud'

const props = defineProps({
  modelValue: Boolean,
  products: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['update:modelValue', 'success'])

const { createItem } = useCrud()
const loading = ref(false)

const form = reactive({
  product_id: '',
  type: 'add',
  quantity: 1,
  note: ''
})

const handleAdjust = async () => {
  if (!form.product_id || form.quantity < 1) return

  loading.value = true
  try {
    await createItem('/vendor/reports/stock/adjust', {
      product_id: form.product_id,
      type: form.type,
      quantity: Math.abs(form.quantity),
      note: form.note
    }, null, false)
    
    emit('success')
    emit('update:modelValue', false)
    
    // Reset form
    Object.assign(form, {
      product_id: '',
      type: 'add',
      quantity: 1,
      note: ''
    })
  } catch (error) {
    console.error(error)
  } finally {
    loading.value = false
  }
}
</script>
