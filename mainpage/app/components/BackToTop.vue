<template>
  <Transition name="fade">
    <button
      v-show="isVisible"
      class="back-to-top"
      @click="scrollToTop"
      aria-label="Back to top"
    >
      <!-- Progress Circle -->
      <svg class="progress-circle" viewBox="0 0 100 100">
        <!-- Background Track -->
        <circle
          cx="50"
          cy="50"
          r="48"
          fill="none"
          stroke="rgba(255, 255, 255, 0.1)"
          stroke-width="4"
        />
        <!-- Progress Bar -->
        <circle
          cx="50"
          cy="50"
          r="48"
          fill="none"
          stroke="currentColor"
          stroke-width="4"
          stroke-dasharray="301.59"
          :stroke-dashoffset="301.59 - (301.59 * scrollProgress) / 100"
          stroke-linecap="round"
        />
      </svg>

      <!-- Arrow Icon -->
      <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        fill="currentColor"
        class="icon"
      >
        <path d="M13 7.828V20h-2V7.828l-5.364 5.364-1.414-1.414L12 4l7.778 7.778-1.414 1.414L13 7.828z" />
      </svg>
    </button>
  </Transition>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const isVisible = ref(false)
const scrollProgress = ref(0)

const handleScroll = () => {
  const scrollTop = window.scrollY
  const docHeight = document.documentElement.scrollHeight - window.innerHeight
  
  isVisible.value = scrollTop > 300
  scrollProgress.value = (scrollTop / docHeight) * 100
}

const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
  handleScroll() // Initialize on mount
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
.back-to-top {
  position: fixed;
  bottom: 40px;
  right: 40px;
  z-index: 100;
  width: 56px;
  height: 56px;
  border-radius: 16px;
  background: rgba(0, 0, 0, 0.8);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  color: #fff;
  border: 1px solid rgba(255, 255, 255, 0.1);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 
    0 4px 6px -1px rgba(0, 0, 0, 0.1),
    0 2px 4px -1px rgba(0, 0, 0, 0.06),
    0 10px 15px -3px rgba(0, 0, 0, 0.1);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  overflow: hidden;
}

.progress-circle {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  transform: rotate(-90deg);
  pointer-events: none;
  color: #fff; /* Progress color */
}

.progress-circle circle {
  transition: stroke-dashoffset 0.2s ease-out;
}

.back-to-top::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
  transform: translateX(-100%);
  transition: transform 0.6s ease;
}

.back-to-top:hover {
  transform: translateY(-8px) scale(1.05);
  background: #000;
  box-shadow: 
    0 20px 25px -5px rgba(0, 0, 0, 0.2),
    0 10px 10px -5px rgba(0, 0, 0, 0.1);
  border-color: rgba(255, 255, 255, 0.2);
}

.back-to-top:hover::before {
  transform: translateX(100%);
}

.back-to-top:active {
  transform: translateY(-4px) scale(0.95);
}

.icon {
  width: 24px;
  height: 24px;
  transition: transform 0.3s ease;
}

.back-to-top:hover .icon {
  transform: translateY(-2px);
}

/* Fade animation */
.fade-enter-active,
.fade-leave-active {
  transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(20px) scale(0.8);
}

@media (max-width: 768px) {
  .back-to-top {
    bottom: 24px;
    right: 24px;
    width: 48px;
    height: 48px;
    border-radius: 12px;
  }
  
  .icon {
    width: 20px;
    height: 20px;
  }
}
</style>
