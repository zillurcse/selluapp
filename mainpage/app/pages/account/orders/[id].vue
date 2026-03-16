<template>
  <div class="min-h-[60vh] flex flex-col">
    <div v-if="loading" class="flex-1 flex items-center justify-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>
    
    <template v-else-if="order">
      <AccountOrderDetails
        :order="order"
        @back="navigateTo('/account/orders')"
        @track="navigateTo(`/account/orders/tracking/${order.id}`)"
      />
    </template>

    <div v-else class="flex-1 flex flex-col items-center justify-center py-20 text-center px-4">
      <div class="w-20 h-20 bg-gray-50 rounded-3xl flex items-center justify-center text-4xl mb-6">🔍</div>
      <h2 class="text-xl font-black text-gray-900 mb-2">Order Not Found</h2>
      <p class="text-gray-500 max-w-xs mx-auto mb-8">We couldn't find the order details you're looking for. It might have been deleted or the link is incorrect.</p>
      <button @click="navigateTo('/account/orders')" class="px-8 py-3 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
        Back to Orders
      </button>
    </div>
  </div>
</template>

<script setup>
import AccountOrderDetails from '~/components/account/orders/AccountOrderDetails.vue'

const route = useRoute()
const { fetchOrderById, loading } = useAccount()
const order = ref(null)

const loadOrder = async () => {
    const id = route.params.id
    if (!id) return
    order.value = await fetchOrderById(id)
}

onMounted(() => {
    loadOrder()
})

useSeoMeta({
  title: `Order Details - My Account`,
})
</script>
