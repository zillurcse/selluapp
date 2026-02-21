<template>
  <div>
    <!-- Side Drawer (Slide-over) -->
    <Transition
      enter-active-class="transform transition ease-in-out duration-500 sm:duration-700"
      enter-from-class="translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transform transition ease-in-out duration-500 sm:duration-700"
      leave-from-class="translate-x-0"
      leave-to-class="translate-x-full"
    >
      <div v-if="isOpen" class="fixed inset-y-0 right-0 w-full max-w-md bg-white dark:bg-slate-900 shadow-2xl z-50 flex flex-col">
        <!-- Drawer Header -->
        <div class="px-6 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
          <h2 class="text-xl font-bold text-gray-900 dark:text-slate-100">{{ isEditing ? 'Edit Customer' : 'Add New Customer' }}</h2>
          <button @click="closeDrawer" class="p-1.5 bg-black dark:bg-white text-white dark:text-slate-900 rounded-md hover:bg-gray-800 dark:hover:bg-gray-200 transition-colors">
            <XIcon class="w-5 h-5" />
          </button>
        </div>

        <!-- Drawer Body (Scrollable) -->
        <div class="flex-1 overflow-y-auto px-6 py-8 space-y-6">
         <div class="grid grid-cols-2 gap-4">
           <!-- first name -->
          <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">First Name</label>
            <input 
              v-model="form.first_name"
              type="text" 
              placeholder="Enter first name" 
              class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none text-gray-900 dark:text-slate-100 placeholder:text-gray-400 dark:placeholder:text-slate-500"
            >
          </div>

          <!-- last name -->
          <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Last Name</label>
            <input 
              v-model="form.last_name"
              type="text" 
              placeholder="Enter last name" 
              class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none text-gray-900 dark:text-slate-100 placeholder:text-gray-400 dark:placeholder:text-slate-500"
            >
          </div>
         </div>

          <!-- Email -->
          <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Customer Email</label>
            <input 
              v-model="form.email"
              type="email" 
              placeholder="Enter customer email" 
              class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none text-gray-900 dark:text-slate-100 placeholder:text-gray-400 dark:placeholder:text-slate-500"
            >
          </div>

          <!-- Phone -->
          <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Customer Phone</label>
            <input 
              v-model="form.phone"
              type="tel" 
              placeholder="Enter phone number" 
              class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none text-gray-900 dark:text-slate-100 placeholder:text-gray-400 dark:placeholder:text-slate-500"
            >
          </div>

          <!-- Emergency Phone -->
          <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Emergency Phone</label>
            <input 
              v-model="form.emergency_phone"
              type="tel" 
              placeholder="Enter emergency contact number" 
              class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none text-gray-900 dark:text-slate-100 placeholder:text-gray-400 dark:placeholder:text-slate-500"
            >
          </div>

          <!-- Additional Details Grid 1 -->
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1.5">
              <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Tax Number</label>
              <input 
                v-model="form.tax_number"
                type="text" 
                placeholder="Tax ID" 
                class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none text-gray-900 dark:text-slate-100 placeholder:text-gray-400 dark:placeholder:text-slate-500"
              >
            </div>
            <div class="space-y-1.5">
              <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Credit Limit</label>
              <input 
                v-model="form.credit_limit"
                type="number" 
                step="0.01"
                placeholder="0.00" 
                class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none text-gray-900 dark:text-slate-100 placeholder:text-gray-400 dark:placeholder:text-slate-500"
              >
            </div>
          </div>

          <!-- Additional Details Grid 2 -->
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1.5">
              <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Opening Balance</label>
              <input 
                v-model="form.opening_balance"
                type="number" 
                step="0.01"
                placeholder="0.00" 
                class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none text-gray-900 dark:text-slate-100 placeholder:text-gray-400 dark:placeholder:text-slate-500"
              >
            </div>
            <div class="space-y-1.5">
              <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Loyalty Points</label>
              <input 
                v-model="form.loyalty_points"
                type="text" 
                placeholder="0" 
                class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none text-gray-900 dark:text-slate-100 placeholder:text-gray-400 dark:placeholder:text-slate-500"
              >
            </div>
          </div>

          <!-- Additional Details Grid 3 -->
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1.5">
              <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Date of Birth</label>
              <input 
                v-model="form.date_of_birth"
                type="date" 
                class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none text-gray-900 dark:text-slate-100 placeholder:text-gray-400 dark:placeholder:text-slate-500"
              >
            </div>
            <div class="space-y-1.5">
              <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Gender</label>
              <div class="relative">
                <select 
                  v-model="form.gender"
                  class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none appearance-none text-gray-900 dark:text-slate-100"
                >
                  <option value="" disabled>Select gender</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                  <option value="other">Other</option>
                </select>
                <ChevronDownIcon class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
              </div>
            </div>
          </div>

          <!-- Password -->
          <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Password <span class="text-xs text-gray-400 font-normal" v-if="isEditing">(Leave blank to keep current)</span></label>
            <div class="relative">
              <input 
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'" 
                placeholder="********" 
                class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none text-gray-900 dark:text-slate-100 placeholder:text-gray-400 dark:placeholder:text-slate-500"
              >
              <button @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <EyeIcon v-if="!showPassword" class="w-4 h-4" />
                <EyeOffIcon v-else class="w-4 h-4" />
              </button>
            </div>
          </div>

          <!-- Confirm Password -->
          <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Confirm Password</label>
            <input 
              v-model="form.password_confirmation"
              type="password" 
              placeholder="Repeat password" 
              class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none text-gray-900 dark:text-slate-100 placeholder:text-gray-400 dark:placeholder:text-slate-500"
            >
          </div>

          <!-- Status -->
          <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Status</label>
            <div class="relative">
              <select 
                v-model="form.is_active"
                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all outline-none appearance-none text-gray-900"
              >
                <option :value="true">Active</option>
                <option :value="false">Inactive</option>
              </select>
              <ChevronDownIcon class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
            </div>
          </div>
        </div>

        <!-- Drawer Footer -->
        <div class="px-6 py-6 border-t border-gray-100 dark:border-slate-800 flex items-center space-x-3 bg-white dark:bg-slate-900">
          <button 
            @click="closeDrawer"
            class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-slate-800 text-gray-700 dark:text-slate-300 font-semibold rounded-lg hover:bg-gray-200 dark:hover:bg-slate-700 transition-colors"
          >
            Cancel
          </button>
          <button 
            @click="handleSubmit"
            :disabled="saving"
            class="flex-1 px-4 py-2.5 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors shadow-md active:scale-95 disabled:opacity-50"
          >
            {{ saving ? 'Saving...' : 'Save Changes' }}
          </button>
        </div>
      </div>
    </Transition>

    <!-- Overlay -->
    <Transition
      enter-active-class="ease-out duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="isOpen" @click="closeDrawer" class="fixed inset-0 bg-black/20 backdrop-blur-sm z-40"></div>
    </Transition>
  </div>
</template>

<script setup>
import { 
  XIcon, 
  EyeIcon, 
  EyeOffIcon, 
  ChevronDownIcon
} from 'lucide-vue-next'
import { ref, computed } from 'vue'
import { toast } from 'vue-sonner'

const emit = defineEmits(['saved'])

const { createItem } = useCrud()

const isOpen = ref(false)
const showPassword = ref(false)
const saving = ref(false)
const editId = ref(null)

const isEditing = computed(() => !!editId.value)

const form = ref({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  emergency_phone: '',
  password: '',
  password_confirmation: '',
  tax_number: '',
  credit_limit: 0,
  opening_balance: 0,
  loyalty_points: '',
  date_of_birth: '',
  gender: '',
  is_active: true
})

const resetForm = () => {
    form.value = {
        first_name: '',
        last_name: '',
        email: '',
        phone: '',
        emergency_phone: '',
        password: '',
        password_confirmation: '',
        tax_number: '',
        credit_limit: 0,
        opening_balance: 0,
        loyalty_points: '',
        date_of_birth: '',
        gender: '',
        is_active: true
    }
    editId.value = null
    showPassword.value = false
}

const openAddDrawer = () => {
    resetForm()
    isOpen.value = true
}

const openEditDrawer = (customer) => {
    resetForm()
    form.value = {
        ...customer,
        password: '', 
        password_confirmation: ''
    }
    editId.value = customer.id
    isOpen.value = true
}

const closeDrawer = () => {
    isOpen.value = false
}

const handleSubmit = async () => {
    try {
        if (!form.value.first_name || !form.value.last_name || !form.value.phone) {
            toast.error('First Name, Last Name and Phone are required')
            return
        }
        
        if (form.value.password && form.value.password !== form.value.password_confirmation) {
            toast.error('Passwords do not match')
            return
        }

        saving.value = true

        const payload = { ...form.value }
        
        if (editId.value) {
           try {
             if (typeof useUtilityStore !== 'undefined') {
                 useUtilityStore.isEditing = true
             }
           } catch (e) {}
           await createItem(`/vendor/customers`, payload, editId.value)
           try {
               if (typeof useUtilityStore !== 'undefined') {
                   useUtilityStore.isEditing = false
               }
           } catch (e) {}
        } else {
           await createItem('/vendor/customers', payload)
        }
        
        isOpen.value = false
        emit('saved')
    } catch (e) {
        console.error('Operation failed', e)
    } finally {
        saving.value = false
    }
}

defineExpose({
    openAddDrawer,
    openEditDrawer
})
</script>

<style scoped>
/* Optional: Hide scrollbar for Chrome, Safari and Opera */
.overflow-y-auto::-webkit-scrollbar {
  display: none;
}

/* Optional: Hide scrollbar for IE, Edge and Firefox */
.overflow-y-auto {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}
</style>
