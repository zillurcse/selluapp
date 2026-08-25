<template>
  <div v-if="isOpen && product" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="$emit('close')"></div>

    <div class="relative w-full max-w-lg bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
      <div class="flex items-start justify-between p-5 border-b border-slate-100 dark:border-slate-800">
        <div>
          <h3 class="text-base font-black text-slate-900 dark:text-white">Select a variant</h3>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ product.name }}</p>
        </div>
        <button @click="$emit('close')" class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
          <X class="w-5 h-5" />
        </button>
      </div>

      <div class="p-5 max-h-[60vh] overflow-y-auto grid grid-cols-1 sm:grid-cols-2 gap-3">
        <button
          v-for="variant in product.variants"
          :key="variant.id"
          type="button"
          :disabled="variant.stock <= 0"
          @click="$emit('select', product, variant)"
          class="text-left p-4 rounded-xl border transition-all disabled:opacity-40 disabled:cursor-not-allowed"
          :class="variant.stock > 0
            ? 'border-slate-200 dark:border-slate-700 hover:border-indigo-500 hover:bg-indigo-50/50 dark:hover:bg-indigo-900/20'
            : 'border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40'"
        >
          <div class="text-sm font-bold text-slate-900 dark:text-white">{{ variant.label || variant.sku || 'Variant' }}</div>
          <div class="mt-1 flex items-center justify-between">
            <span class="text-sm font-black text-indigo-600 dark:text-indigo-400">৳{{ Number(variant.price).toFixed(2) }}</span>
            <span class="text-[11px] font-bold uppercase tracking-wide"
              :class="variant.stock > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-500'">
              {{ variant.stock > 0 ? variant.stock + ' in stock' : 'Out of stock' }}
            </span>
          </div>
          <div v-if="variant.sku" class="mt-1 text-[10px] text-slate-400">{{ variant.sku }}</div>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { X } from 'lucide-vue-next'

defineProps({
  isOpen: Boolean,
  product: { type: Object, default: null },
})

defineEmits(['close', 'select'])
</script>
