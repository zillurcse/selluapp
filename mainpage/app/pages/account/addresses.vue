<template>
  <AccountAddresses 
    :addresses="addresses"
    @add-address="submitAddress"
    @update-address="updateAddress"
    @delete-address="deleteAddress"
    @set-default="setDefaultAddress"
  />
</template>

<script setup>
import AccountAddresses from '~/components/account/AccountAddresses.vue'

const {
  addresses,
  submitAddress,
  updateAddress,
  deleteAddress,
  setDefaultAddress,
  fetchCustomerData
} = useAccount()

onMounted(() => {
  fetchCustomerData()
})

useSeoMeta({
  title: 'My Addresses - My Account',
})
</script>
