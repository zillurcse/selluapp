<template>
  <AccountOrders 
    :orders="allOrders" 
    @track-order="goToTracking"
    @view-details="goToDetails"
  />
</template>

<script setup>
import AccountOrders from '~/components/account/orders/AccountOrders.vue'

const { allOrders, fetchCustomerData } = useAccount()

const goToTracking = (order) => {
  navigateTo(`/account/orders/tracking/${order.id}`)
}

const goToDetails = (order) => {
  navigateTo(`/account/orders/${order.id}`)
}

onMounted(() => {
  fetchCustomerData()
})

useSeoMeta({
  title: 'My Orders - My Account',
})
</script>
