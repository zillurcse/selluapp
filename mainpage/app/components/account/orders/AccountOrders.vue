<template>
  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
      <h2 class="font-bold text-gray-900">All Orders</h2>
      <div class="inline-flex gap-1 p-1 rounded-xl bg-gray-50 border border-gray-100">
        <button v-for="f in ['All', 'Delivered', 'Shipped', 'Processing']" :key="f"
          @click="orderFilter = f"
          class="px-3 py-1.5 rounded-lg text-xs font-semibold transition border-none cursor-pointer"
          :class="orderFilter === f ? 'bg-white text-gray-900 shadow-sm' : 'bg-transparent text-gray-500 hover:text-gray-900'">
          {{ f }}
        </button>
      </div>
    </div>
    <div class="divide-y divide-gray-50">
      <div v-for="order in filteredOrders" :key="order.id" class="flex flex-col sm:flex-row sm:items-center gap-4 px-6 py-5 hover:bg-gray-50/50 transition">
        <div class="w-14 h-14 rounded-xl bg-gray-100 flex items-center justify-center text-2xl shrink-0">{{ order.icon }}</div>
        <div class="flex-1">
          <div class="font-semibold text-gray-900">{{ order.name }}</div>
          <div class="text-sm text-gray-400 mt-0.5">{{ order.date }}</div>
          <div class="text-sm text-gray-400">{{ order.items_count || order.items }} item{{ (order.items_count || order.items) > 1 ? 's' : '' }}</div>
        </div>
        <div class="flex flex-col sm:items-end gap-2">
          <div class="font-extrabold text-gray-900">৳{{ order.total }}</div>
          <div class="flex items-center gap-3">
              <span v-if="order.order?.discount_amount > 0" class="inline-block px-2 py-0.5 text-[10px] font-black bg-rose-50 text-rose-600 rounded border border-rose-100 uppercase tracking-tighter">
                Promotion
              </span>
              <span class="inline-block px-3 py-1 text-xs font-bold rounded-full text-center"
                :class="{
                  'bg-green-50 text-green-700': order.status === 'Delivered',
                  'bg-blue-50 text-blue-700': order.status === 'Shipped',
                  'bg-yellow-50 text-yellow-700': ['Processing', 'Pending'].includes(order.status),
                  'bg-purple-50 text-purple-700': order.status === 'Courier',
                  'bg-red-50 text-red-700': order.status === 'Cancelled',
                }">{{ order.status }}</span>
          </div>
          <div class="flex items-center gap-3 mt-1 sm:mt-0">
              <button @click="$emit('view-details', order.order)" class="p-2 hover:bg-gray-100 rounded-lg transition border border-gray-100 bg-white cursor-pointer group flex items-center justify-center" title="View Details">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-500 group-hover:text-gray-900 transition-colors">
                  <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                  <circle cx="12" cy="12" r="3"/>
                </svg>
              </button>
              <button @click="$emit('track-order', order.order)" class="text-xs font-black text-gray-900 bg-gray-50 px-4 py-2 rounded-xl hover:bg-gray-100 transition border border-gray-100 cursor-pointer">Track Order →</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  orders: { type: Array, default: () => [] }
})

defineEmits(['track-order', 'view-details'])

const orderFilter = ref('All')

const filteredOrders = computed(() => {
  if (orderFilter.value === 'All') return props.orders
  return props.orders.filter(o => o.status === orderFilter.value)
})
</script>
