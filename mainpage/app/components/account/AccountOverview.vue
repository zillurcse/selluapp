<template>
  <div class="flex flex-col gap-6">
    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div v-for="stat in stats" :key="stat.label" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="text-2xl mb-2">{{ stat.icon }}</div>
        <div class="text-2xl font-extrabold text-gray-900 tracking-tight">{{ stat.value }}</div>
        <div class="text-xs text-gray-500 mt-0.5">{{ stat.label }}</div>
      </div>
    </div>
    <!-- Recent Orders -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
      <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
        <h2 class="font-bold text-gray-900">Recent Orders</h2>
        <button @click="$emit('nav', 'orders')" class="text-xs font-bold text-violet-600 hover:text-violet-700 transition border-none bg-transparent cursor-pointer">View all →</button>
      </div>
      <div class="divide-y divide-gray-50">
        <div v-for="order in recentOrders" :key="order.id" class="flex items-center gap-4 px-6 py-4">
          <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-xl shrink-0">{{ order.icon }}</div>
          <div class="flex-1 min-w-0">
            <div class="font-semibold text-gray-900 text-sm truncate">{{ order.name }}</div>
            <div class="text-xs text-gray-400 mt-0.5">{{ order.date }} · {{ order.items }} item{{ order.items > 1 ? 's' : '' }}</div>
          </div>
          <div class="text-right shrink-0">
            <div class="font-bold text-gray-900 text-sm">৳ {{ order.total }}</div>
            <div class="flex items-center gap-2 mt-1 ml-auto">
                <button @click="$emit('view-details', order.order_details || order)" class="p-2 hover:bg-gray-100 rounded-lg transition border border-gray-100 bg-white cursor-pointer group flex items-center justify-center" title="View Details">
                   <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-500 group-hover:text-gray-900 transition-colors">
                      <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                      <circle cx="12" cy="12" r="3"/>
                   </svg>
                </button>
                <button @click="$emit('track-order', order.order_details || order)" class="text-[10px] font-black text-gray-900 bg-gray-50 px-3 py-1.5 rounded-lg hover:bg-gray-100 transition border border-gray-100 cursor-pointer">Track →</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Wishlist Preview -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6" v-if="wishlist.length > 0">
      <div class="flex items-center justify-between mb-5">
        <h2 class="font-bold text-gray-900">Wishlist</h2>
        <button @click="$emit('nav', 'wishlist')" class="text-xs font-bold text-violet-600 hover:text-violet-700 transition border-none bg-transparent cursor-pointer">View all →</button>
      </div>
      <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        <div v-for="item in wishlist.slice(0,3)" :key="item.id" class="group relative bg-gray-50 rounded-xl p-3 border border-gray-100 hover:border-gray-200 transition cursor-pointer" @click="$router.push(`/product/${item.slug || item.id}`)">
          <div class="h-32 w-full bg-gray-50 rounded-xl mb-3 flex items-center justify-center overflow-hidden cursor-pointer" @click="$router.push(`/product/${item.slug || item.id}`)">
             <img v-if="item.image || item.image_url" :src="item.image || item.image_url" class="h-full w-full object-contain" />
             <div v-else class="text-6xl">{{ item.emoji || '📦' }}</div>
          </div>
          <div class="text-xs font-semibold text-gray-900 truncate">{{ item.name }}</div>
          <div class="text-xs text-gray-500">৳{{ item.sale_price || item.price }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  stats: { type: Array, default: () => [] },
  recentOrders: { type: Array, default: () => [] },
})

const { wishlist } = useWishlist()

defineEmits(['nav', 'track-order', 'view-details'])
</script>
