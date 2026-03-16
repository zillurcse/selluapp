<template>
  <AccountSettings 
    :settings="settings"
    @save-settings="saveSettings"
    @delete-account="deleteAccount"
  />
</template>

<script setup>
import AccountSettings from '~/components/account/AccountSettings.vue'

const {
  settings,
  saveSettings,
  deleteAccount,
  fetchCustomerData
} = useAccount()

onMounted(() => {
  fetchCustomerData()
})

useSeoMeta({
  title: 'Account Settings - My Account',
})
</script>
