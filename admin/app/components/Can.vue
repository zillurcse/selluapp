<template>
  <slot v-if="isVisible" />
  <slot v-else name="otherwise" />
</template>

<script setup>
const props = defineProps({
  permission: {
    type: [String, Array],
    default: null
  },
  role: {
    type: [String, Array],
    default: null
  },
  any: {
    type: Boolean,
    default: false
  }
})

const { can, hasRole } = usePermissions()

const isVisible = computed(() => {
  let hasPerm = true
  let hasRl = true

  if (props.permission) {
    hasPerm = can(props.permission)
  }

  if (props.role) {
    hasRl = hasRole(props.role)
  }

  // If both provided, usually we want BOTH to match, unless 'any' is true
  if (props.permission && props.role) {
    return props.any ? (hasPerm || hasRl) : (hasPerm && hasRl)
  }

  return hasPerm && hasRl
})
</script>
