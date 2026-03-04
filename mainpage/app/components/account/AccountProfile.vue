<template>
  <div class="flex flex-col gap-5">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
      <h2 class="font-bold text-gray-900 mb-6">Personal Information</h2>
      <div class="flex flex-col gap-5">
        <!-- Avatar upload -->
        <div class="flex items-center gap-5">
          <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-violet-500 to-blue-500 flex items-center justify-center text-3xl font-extrabold text-white shadow-lg shrink-0">
            {{ user.initials }}
          </div>
          <div>
            <button class="px-4 py-2 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition border-solid cursor-pointer">Change Photo</button>
            <p class="text-xs text-gray-400 mt-1.5">JPG, PNG or GIF. Max 5MB.</p>
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">First name</label>
            <input v-model="profileForm.firstName" type="text" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/5" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Last name</label>
            <input v-model="profileForm.lastName" type="text" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/5" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email address</label>
            <input v-model="profileForm.email" type="email" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/5" />
          </div>
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Phone number</label>
            <input v-model="profileForm.phone" type="tel" placeholder="+1 (555) 000-0000" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-900/5" />
          </div>
        </div>
        <div class="flex justify-end pt-2">
          <button @click="$emit('save-profile', profileForm)" class="px-6 py-2.5 bg-gray-900 text-white text-sm font-bold rounded-xl transition hover:-translate-y-0.5 hover:shadow-md border-none cursor-pointer">
            Save Changes
          </button>
        </div>
      </div>
    </div>

    <!-- Change Password -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
      <h2 class="font-bold text-gray-900 mb-6">Change Password</h2>
      <div class="flex flex-col gap-4 max-w-md">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1.5">Current password</label>
          <input v-model="passwordForm.current_password" type="password" placeholder="Enter current password" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm outline-none transition focus:border-gray-900" />
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1.5">New password</label>
          <input v-model="passwordForm.password" type="password" placeholder="Min. 8 characters" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm outline-none transition focus:border-gray-900" />
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm new password</label>
          <input v-model="passwordForm.password_confirmation" type="password" placeholder="Re-enter new password" class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm outline-none transition focus:border-gray-900" />
        </div>
        <button @click="$emit('change-password', passwordForm)" class="w-fit px-6 py-2.5 bg-gray-900 text-white text-sm font-bold rounded-xl transition hover:-translate-y-0.5 border-none cursor-pointer">Update Password</button>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  user: { type: Object, default: () => ({}) },
  profile: { type: Object, default: () => ({}) }
})

defineEmits(['save-profile', 'change-password'])

const profileForm = ref({ ...props.profile })
const passwordForm = ref({
  current_password: '',
  password: '',
  password_confirmation: ''
})

// Update local form if prop changes
watch(() => props.profile, (newVal) => {
  profileForm.value = { ...newVal }
}, { deep: true })
</script>
