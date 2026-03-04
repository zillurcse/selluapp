<template>
  <Transition name="modal">
    <div v-if="isOpen" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="close"></div>

      <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
          <!-- Modal Panel -->
          <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-slate-900 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-3xl border border-slate-100 dark:border-slate-800">
            <!-- Close Button -->
            <button 
              @click="close"
              class="absolute right-4 top-4 z-10 w-8 h-8 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors"
            >
              <X class="w-5 h-5" />
            </button>

            <!-- Video Content -->
            <div class="aspect-video w-full bg-black">
              <iframe
                v-if="videoId"
                class="w-full h-full"
                :src="`https://www.youtube.com/embed/${videoId}?autoplay=1`"
                title="YouTube video player"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
              ></iframe>
              <div v-else class="w-full h-full flex items-center justify-center text-slate-500">
                Invalid Video ID
              </div>
            </div>

            <!-- Title (Optional) -->
            <div v-if="title" class="p-4 border-t border-slate-100 dark:border-slate-800">
              <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">
                {{ title }}
              </h3>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { X } from 'lucide-vue-next'

const props = defineProps({
  isOpen: Boolean,
  videoId: String,
  title: String
})

const emit = defineEmits(['close'])

const close = () => {
  emit('close')
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
