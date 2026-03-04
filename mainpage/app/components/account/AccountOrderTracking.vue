<template>
  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <button @click="$emit('back')" class="p-2 hover:bg-gray-50 rounded-lg transition border-none bg-transparent cursor-pointer text-gray-400 hover:text-gray-900">
          <span class="text-xl">←</span>
        </button>
        <div>
          <h2 class="font-bold text-gray-900">Track Order #{{ order.invoice_number }}</h2>
          <p class="text-xs text-gray-500">Placed on {{ order.date }}</p>
        </div>
      </div>
      <div class="text-right">
        <span class="inline-block px-3 py-1 text-xs font-bold rounded-full"
          :class="statusClasses[order.status.toLowerCase()] || 'bg-gray-50 text-gray-700'">
          {{ order.status }}
        </span>
      </div>
    </div>

    <div class="p-6 md:p-8">
      <!-- Tracking Timeline -->
      <div class="relative">
        <!-- Vertical Line -->
        <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-100 md:left-1/2 md:-ml-px"></div>

        <div class="space-y-12">
          <div v-for="(step, index) in trackingSteps" :key="step.title" 
            class="relative flex items-center md:justify-between"
            :class="index % 2 === 0 ? 'md:flex-row-reverse' : ''">
            
            <!-- Icon/Dot -->
            <div class="absolute left-0 w-8 h-8 rounded-full border-4 border-white shadow-sm flex items-center justify-center z-10 md:left-1/2 md:-ml-4 transition-all duration-500"
              :class="step.completed ? 'bg-gray-900 text-white' : 'bg-gray-100 text-gray-400'">
              <span class="text-xs">{{ step.completed ? '✓' : '' }}</span>
            </div>

            <!-- Content -->
            <div class="ml-12 md:ml-0 md:w-[45%]">
              <div class="bg-gray-50/50 rounded-2xl p-4 border border-gray-100 hover:border-gray-200 transition">
                <h3 class="font-bold text-sm text-gray-900">{{ step.title }}</h3>
                <p class="text-xs text-gray-500 mt-1">{{ step.desc }}</p>
                <p v-if="step.date" class="text-[10px] font-bold text-gray-400 mt-2 uppercase tracking-wider">{{ step.date }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Courier Info -->
      <div v-if="order.tracking_number" class="mt-12 p-5 bg-violet-50 rounded-2xl border border-violet-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-2xl">🚚</div>
          <div>
            <div class="text-xs font-bold text-violet-900 uppercase tracking-tight">Tracking Number</div>
            <div class="text-sm font-extrabold text-violet-700">{{ order.tracking_number }}</div>
          </div>
        </div>
        <button class="px-5 py-2.5 bg-violet-600 text-white text-xs font-bold rounded-xl hover:bg-violet-700 hover:shadow-md transition border-none cursor-pointer">
          Track on Courier Site →
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  order: { type: Object, required: true }
})

defineEmits(['back'])

const statusClasses = {
  pending: 'bg-yellow-50 text-yellow-700',
  processing: 'bg-blue-50 text-blue-700',
  shipped: 'bg-indigo-50 text-indigo-700',
  delivered: 'bg-green-50 text-green-700',
  courier: 'bg-purple-50 text-purple-700',
  cancelled: 'bg-red-50 text-red-700'
}

const trackingSteps = computed(() => {
  const status = props.order.status.toLowerCase()
  const steps = [
    { title: 'Order Placed', desc: 'We have received your order.', completed: true, date: props.order.date },
    { title: 'Processing', desc: 'Your order is being prepared and packed.', completed: ['processing', 'shipped', 'delivered', 'courier'].includes(status) },
    { title: 'Shipped', desc: 'Your order has been handed over to the courier.', completed: ['shipped', 'delivered', 'courier'].includes(status) },
    { title: 'Out for Delivery', desc: 'The courier is on the way to your address.', completed: ['delivered'].includes(status) },
    { title: 'Delivered', desc: 'Package has been delivered successfully.', completed: status === 'delivered' }
  ]
  return steps
})
</script>
