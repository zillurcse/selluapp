<template>
  <AccountProfile 
    :user="user" 
    :profile="profileForm"
    @save-profile="saveProfile"
    @change-password="changePassword"
  />
</template>

<script setup>
import AccountProfile from '~/components/account/AccountProfile.vue'

const {
  user,
  profileForm,
  saveProfile,
  changePassword,
  fetchCustomerData
} = useAccount()

onMounted(() => {
  fetchCustomerData()
})

useSeoMeta({
  title: 'My Profile - My Account',
})
</script>
