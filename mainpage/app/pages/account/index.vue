<template>
  <div>
    <AccountOverview 
      :stats="stats" 
      :recent-orders="recentOrders" 
      :wishlist-items="wishlistItems"
      @nav="navigateToTab"
      @track-order="goToTracking"
      @view-details="goToDetails"
    />
  </div>
</template>

<script setup>
import AccountOverview from '~/components/account/AccountOverview.vue'

const {
  stats,
  recentOrders,
  wishlistItems,
  fetchCustomerData
} = useAccount()

const navigateToTab = (tab) => {
  if (tab === 'overview') navigateTo('/account')
  else navigateTo(`/account/${tab}`)
}

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
  title: 'Dashboard - My Account',
})
</script>
