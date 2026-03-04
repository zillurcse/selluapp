<template>
  <div class="flex flex-col gap-5">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
      <h2 class="font-bold text-gray-900 mb-6">Notifications</h2>
      <div class="flex flex-col divide-y divide-gray-50">
        <div v-for="notif in settings" :key="notif.id" class="flex items-center justify-between py-4">
          <div>
            <div class="text-sm font-semibold text-gray-900">{{ notif.label }}</div>
            <div class="text-xs text-gray-400 mt-0.5">{{ notif.desc }}</div>
          </div>
          <label class="relative inline-flex items-center cursor-pointer ml-4 shrink-0">
            <input type="checkbox" v-model="notif.enabled" class="sr-only peer" />
            <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:bg-gray-900 transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
          </label>
        </div>
      </div>
      <div class="flex justify-end mt-6">
        <button @click="$emit('save-settings', settings)" class="px-6 py-2.5 bg-gray-900 text-white text-sm font-bold rounded-xl transition hover:-translate-y-0.5 hover:shadow-md border-none cursor-pointer">
          Save Preferences
        </button>
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
      <h2 class="font-bold text-gray-900 mb-6">Danger Zone</h2>
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-4 bg-red-50 border border-red-100 rounded-xl">
        <div>
          <div class="font-semibold text-red-700 text-sm">Delete Account</div>
          <div class="text-xs text-red-500 mt-0.5">This action cannot be undone. All your data will be permanently removed.</div>
        </div>
        <button @click="$emit('delete-account')" class="px-4 py-2 bg-red-600 text-white text-sm font-bold rounded-xl hover:bg-red-700 transition shrink-0 border-none cursor-pointer">Delete Account</button>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  settings: { type: Array, default: () => [] }
})

defineEmits(['save-settings', 'delete-account'])
</script>
