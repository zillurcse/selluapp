<template>
  <Transition name="modal">
    <div v-if="isOpen" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="close"></div>

      <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
          <!-- Modal Panel -->
          <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-100 dark:border-slate-800 p-6">
            <div class="flex flex-col items-center text-center gap-4">
              <div class="w-16 h-16 rounded-full bg-red-50 dark:bg-red-900/20 flex items-center justify-center border-4 border-red-100 dark:border-red-900/30">
                <Trash2 class="w-8 h-8 text-red-500 dark:text-red-400" />
              </div>
              
              <h3 class="text-xl font-black text-slate-800 dark:text-slate-100 leading-6" id="modal-title">
                {{ title }}
              </h3>
              
              <div class="mt-2">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                  {{ message }}
                </p>
              </div>

              <div class="mt-6 flex flex-col sm:flex-row gap-3 w-full">
                <button
                  type="button"
                  class="inline-flex flex-1 justify-center rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-3 text-sm font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-100 dark:focus:ring-slate-800 transition-all"
                  @click="close"
                >
                  {{ cancelText }}
                </button>
                <button
                  type="button"
                  class="inline-flex flex-1 justify-center rounded-xl border border-transparent bg-red-600 px-4 py-3 text-sm font-bold text-white hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-500/20 shadow-lg shadow-red-600/20 transition-all"
                  :disabled="isLoading"
                  @click="confirm"
                >
                  <span v-if="isLoading" class="animate-spin mr-2">⏳</span>
                  {{ confirmText }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { Trash2 } from 'lucide-vue-next'

const props = defineProps({
  isOpen: Boolean,
  title: {
    type: String,
    default: 'Delete Item'
  },
  message: {
    type: String,
    default: 'Are you sure you want to delete this item? This action cannot be undone.'
  },
  confirmText: {
    type: String,
    default: 'Yes, Delete it'
  },
  cancelText: {
    type: String,
    default: 'Cancel'
  },
  isLoading: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'confirm'])

const close = () => {
  emit('close')
}

const confirm = () => {
  emit('confirm')
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .transform,
.modal-leave-active .transform {
  transition: all 0.3s ease-out;
}

.modal-enter-from .transform,
.modal-leave-to .transform {
  opacity: 0;
  transform: scale(0.95);
}
</style>
