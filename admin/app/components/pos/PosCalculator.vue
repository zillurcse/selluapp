<template>
  <div v-if="isOpen" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div 
      class="absolute inset-0 bg-[#0f172a]/20 backdrop-blur-sm" 
      @click="$emit('close')"
    ></div>
    
    <!-- Calculator Content -->
    <div class="bg-[#1e293b] rounded-[2rem] w-full max-w-[320px] overflow-hidden relative shadow-3xl animate-in zoom-in duration-300 mx-4 border border-white/10 ring-1 ring-white/10">
      <!-- Header -->
      <div class="p-5 flex items-center justify-between pb-2">
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 rounded-full bg-red-500"></div>
            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
            <div class="w-3 h-3 rounded-full bg-green-500"></div>
        </div>
        <button 
          @click="$emit('close')" 
          class="text-gray-400 hover:text-white transition-colors"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Display -->
      <div class="px-6 py-8 text-right">
        <div class="text-gray-400 text-sm font-bold h-6 truncate mb-1">{{ expression || '' }}</div>
        <div class="text-white text-4xl font-black truncate">{{ display || '0' }}</div>
      </div>

      <!-- Keypad -->
      <div class="grid grid-cols-4 gap-1 p-4 bg-[#0f172a]/50">
        <button 
          v-for="btn in buttons" 
          :key="btn.value"
          @click="handleInput(btn)"
          class="h-14 rounded-xl flex items-center justify-center text-lg font-bold transition-all active:scale-95"
          :class="getBtnClass(btn)"
        >
          {{ btn.label }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { X } from 'lucide-vue-next'

defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close'])

const display = ref('0')
const expression = ref('')
const lastWasOperator = ref(false)

const buttons = [
  { label: 'C', value: 'clear', type: 'utility' },
  { label: '±', value: 'plusminus', type: 'utility' },
  { label: '%', value: 'mod', type: 'utility' },
  { label: '÷', value: '/', type: 'operator' },
  { label: '7', value: '7', type: 'number' },
  { label: '8', value: '8', type: 'number' },
  { label: '9', value: '9', type: 'number' },
  { label: '×', value: '*', type: 'operator' },
  { label: '4', value: '4', type: 'number' },
  { label: '5', value: '5', type: 'number' },
  { label: '6', value: '6', type: 'number' },
  { label: '-', value: '-', type: 'operator' },
  { label: '1', value: '1', type: 'number' },
  { label: '2', value: '2', type: 'number' },
  { label: '3', value: '3', type: 'number' },
  { label: '+', value: '+', type: 'operator' },
  { label: '0', value: '0', type: 'number', wide: true },
  { label: '.', value: '.', type: 'number' },
  { label: '=', value: 'equals', type: 'equals' },
]

const getBtnClass = (btn) => {
  if (btn.type === 'number') return 'bg-[#334155] text-white hover:bg-[#475569]'
  if (btn.type === 'operator') return 'bg-primary-600 text-white hover:bg-primary-700'
  if (btn.type === 'utility') return 'bg-[#475569] text-white hover:bg-[#64748b]'
  if (btn.type === 'equals') return 'bg-green-500 text-white hover:bg-green-600'
  return ''
}

const handleInput = (btn) => {
  if (btn.type === 'number') {
    if (display.value === '0' || lastWasOperator.value) {
      display.value = btn.value
    } else {
      display.value += btn.value
    }
    lastWasOperator.value = false
  } else if (btn.type === 'operator') {
    expression.value = display.value + ' ' + btn.label
    display.value = calculate() || display.value
    lastWasOperator.value = true
  } else if (btn.value === 'clear') {
    display.value = '0'
    expression.value = ''
    lastWasOperator.value = false
  } else if (btn.value === 'equals') {
    if (expression.value) {
        display.value = calculate()
        expression.value = ''
        lastWasOperator.value = true
    }
  }
}

const calculate = () => {
    try {
        const parts = expression.value.split(' ')
        const op = parts[1]
        const v1 = parseFloat(parts[0])
        const v2 = parseFloat(display.value)
        
        switch(op) {
            case '+': return (v1 + v2).toString()
            case '-': return (v1 - v2).toString()
            case '×': return (v1 * v2).toString()
            case '÷': return (v1 / v2).toString()
            default: return v2.toString()
        }
    } catch(e) {
        return '0'
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

.grid-cols-4 > .wide {
    grid-column: span 2;
}
</style>
