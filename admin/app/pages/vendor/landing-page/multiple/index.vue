<script setup>
definePageMeta({
  middleware: 'auth',
  permissions: 'landing_pages.create'
})

await navigateTo('/vendor/landing-page/create?type=multiple', { replace: true })
</script>
