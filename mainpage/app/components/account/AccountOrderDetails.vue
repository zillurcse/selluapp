<template>
  <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden animate-in fade-in slide-in-from-right-4 duration-500">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
      <div class="flex items-center gap-4">
        <button @click="$emit('back')" class="p-2 hover:bg-gray-100 rounded-lg transition border-none bg-transparent cursor-pointer text-gray-400 hover:text-gray-900">
          <span class="text-xl">←</span>
        </button>
        <div>
          <h2 class="font-bold text-gray-900 flex items-center gap-2">
            Order #{{ order.invoice_number }}
            <span v-if="order.discount_amount > 0" class="text-[10px] bg-rose-100 text-rose-700 px-2 py-0.5 rounded-md font-black uppercase tracking-widest border border-rose-200">
                Promotion
            </span>
          </h2>
          <p class="text-xs text-gray-500">Placed on {{ formatDate(order.created_at) }}</p>
        </div>
      </div>
      <div class="text-right">
        <span class="inline-block px-3 py-1 text-xs font-bold rounded-full"
          :class="statusClasses[order.status.toLowerCase()] || 'bg-gray-50 text-gray-700'">
          {{ order.status.toUpperCase() }}
        </span>
      </div>
    </div>

    <div class="p-6 space-y-8">
      <!-- Order Items -->
      <div class="space-y-4">
        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Order Items</h3>
        <div class="divide-y divide-gray-50 border border-gray-50 rounded-2xl overflow-hidden">
          <div v-for="item in order.items" :key="item.id" class="flex items-center gap-4 p-4 hover:bg-gray-50/30 transition">
            <div class="w-16 h-16 rounded-xl bg-gray-100 border border-gray-100 overflow-hidden shrink-0">
                <img v-if="item.product?.thumbnail" :src="`/storage/${item.product.thumbnail}`" class="w-full h-full object-cover">
                <div v-else class="w-full h-full flex items-center justify-center text-gray-400 text-xs">No Img</div>
            </div>
            <div class="flex-1 min-w-0">
              <div class="font-bold text-gray-900 truncate">{{ item.product?.name || 'Product' }}</div>
              <div class="text-xs text-gray-400 mt-1">Quantity: <span class="text-gray-900 font-bold">{{ item.quantity }}</span></div>
            </div>
            <div class="text-right shrink-0">
              <div class="font-bold text-gray-900">৳{{ item.total_price }}</div>
              <div class="text-[10px] text-gray-400">৳{{ item.unit_price }} / unit</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Cost Breakdown -->
      <div class="flex flex-col md:flex-row gap-6">
        <!-- Status Timeline Mini -->
        <div class="flex-1 space-y-4">
            <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Order Timeline</h3>
            <div class="space-y-4 relative before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-50">
                <div v-for="step in timelineSteps" :key="step.label" class="flex items-center gap-4 relative">
                    <div class="w-5 h-5 rounded-full border-4 border-white shadow-sm shrink-0 z-10 transition-colors duration-500" 
                        :class="step.completed ? 'bg-gray-900' : 'bg-gray-100'"></div>
                    <div>
                        <div class="text-xs font-bold" :class="step.completed ? 'text-gray-900' : 'text-gray-400'">{{ step.label }}</div>
                        <div v-if="step.completed && step.date" class="text-[10px] text-gray-400">{{ step.date }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Card -->
        <div class="w-full md:w-80 shrink-0">
            <div class="bg-gray-900 text-white rounded-2xl p-6 shadow-xl shadow-gray-200/50">
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-xs font-semibold text-gray-400">
                        <span>Items Subtotal</span>
                        <span>৳{{ order.subtotal }}</span>
                    </div>
                    <div v-if="order.discount_amount > 0" class="space-y-2">
                        <div class="flex justify-between items-center text-xs font-semibold text-rose-400">
                            <span>Promotional Discount</span>
                            <span>- ৳{{ order.discount_amount }}</span>
                        </div>
                        <div v-if="order.applied_promotions && order.applied_promotions.length > 0" class="flex flex-wrap gap-1">
                             <span v-for="(p, idx) in order.applied_promotions" :key="idx" class="text-[8px] bg-rose-500/20 text-rose-300 px-1.5 py-0.5 rounded border border-rose-500/30 uppercase font-black">
                                {{ p.offer_title }}
                             </span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center text-xs font-semibold text-gray-400">
                        <span>Shipping Cost</span>
                        <span>+ ৳{{ order.shipping_cost || '0.00' }}</span>
                    </div>
                    <div class="pt-4 border-t border-white/10 flex justify-between items-end">
                        <div>
                            <div class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Total Amount</div>
                            <div class="text-2xl font-black">৳{{ order.total_amount }}</div>
                        </div>
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter bg-white/5 px-2 py-1 rounded">
                            {{ order.payment_method?.replace('_', ' ').toUpperCase() || 'CASH' }}
                        </div>
                    </div>
                </div>
            </div>
            <button @click="$emit('track')" class="w-full mt-4 py-3 bg-gray-50 text-gray-900 text-xs font-black rounded-xl border border-gray-100 hover:bg-gray-100 transition active:scale-[0.98] cursor-pointer">
                Track Order Execution →
            </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  order: { type: Object, required: true }
})

defineEmits(['back', 'track'])

const formatDate = (dateStr) => {
    if(!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric'
    });
}

const statusClasses = {
  pending: 'bg-yellow-50 text-yellow-700 border border-yellow-100',
  processing: 'bg-blue-50 text-blue-700 border border-blue-100',
  shipped: 'bg-purple-50 text-purple-700 border border-purple-100',
  delivered: 'bg-green-50 text-green-700 border border-green-100',
  courier: 'bg-indigo-50 text-indigo-700 border border-indigo-100',
  cancelled: 'bg-red-50 text-red-700 border border-red-100'
}

const timelineSteps = computed(() => {
    const status = props.order.status.toLowerCase()
    return [
        { label: 'Confirmed', completed: true, date: formatDate(props.order.created_at) },
        { label: 'Processing', completed: ['processing', 'shipped', 'delivered', 'courier'].includes(status) },
        { label: 'Handed to Courier', completed: ['shipped', 'delivered', 'courier'].includes(status) },
        { label: 'Delivered', completed: status === 'delivered' }
    ]
})
</script>
