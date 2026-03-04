<template>
  <div class="flex flex-col gap-4">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="font-bold text-gray-900">Saved Addresses</h2>
        <button @click="toggleAddForm" class="px-4 py-2 bg-gray-900 text-white text-sm font-bold rounded-xl transition hover:-translate-y-0.5 border-none cursor-pointer">
          {{ (isAddingAddress || isEditingAddress) ? 'Cancel' : '+ Add Address' }}
        </button>
      </div>

      <!-- Add/Edit Address Form -->
      <div v-if="isAddingAddress || isEditingAddress" class="mb-6 p-5 bg-gray-50 border border-gray-100 rounded-2xl">
        <h3 class="text-sm font-bold text-gray-900 mb-4">{{ isEditingAddress ? 'Edit Address' : 'New Address' }}</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Full Name</label>
            <input v-model="addressForm.name" type="text" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm outline-none focus:border-gray-900" placeholder="e.g. John Doe" />
          </div>
          <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Address Line 1</label>
            <input v-model="addressForm.line1" type="text" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm outline-none focus:border-gray-900" placeholder="Street address, P.O. box, etc." />
          </div>
          
          <!-- Searchable State -->
          <div class="relative">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">State/Province</label>
            <input 
              v-model="stateSearch" 
              @focus="showStateSuggestions = true"
              @blur="hideSuggestions('state')"
              type="text" 
              class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm outline-none focus:border-gray-900" 
              placeholder="Search State..."
            />
            <div v-if="showStateSuggestions && filteredStates.length > 0" class="absolute z-10 w-full mt-1 max-h-48 overflow-y-auto bg-white border border-gray-100 rounded-xl shadow-xl py-1">
              <button 
                v-for="state in filteredStates" 
                :key="state.id" 
                @mousedown="selectState(state)"
                class="w-full px-4 py-2.5 text-left text-sm hover:bg-gray-50 transition-colors border-none bg-transparent cursor-pointer"
              >
                {{ state.name }}
              </button>
            </div>
          </div>

          <!-- Searchable City -->
          <div class="relative">
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">City</label>
            <input 
              v-model="citySearch" 
              :disabled="!addressForm.state"
              @focus="showCitySuggestions = true"
              @blur="hideSuggestions('city')"
              type="text" 
              class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm outline-none focus:border-gray-900 disabled:bg-gray-50 disabled:cursor-not-allowed" 
              placeholder="Search City..."
            />
            <div v-if="showCitySuggestions && filteredCities.length > 0" class="absolute z-10 w-full mt-1 max-h-48 overflow-y-auto bg-white border border-gray-100 rounded-xl shadow-xl py-1">
              <button 
                v-for="city in filteredCities" 
                :key="city.id" 
                @mousedown="selectCity(city)"
                class="w-full px-4 py-2.5 text-left text-sm hover:bg-gray-50 transition-colors border-none bg-transparent cursor-pointer"
              >
                {{ city.name }}
              </button>
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Postal/Zip Code</label>
            <input v-model="addressForm.zip" type="text" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm outline-none focus:border-gray-900" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Country</label>
            <input v-model="addressForm.country" type="text" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm outline-none focus:border-gray-900" />
          </div>
          <div class="sm:col-span-2 flex items-center justify-between mt-2" @click.stop>
            <label class="flex items-center gap-2 cursor-pointer select-none">
              <input v-model="addressForm.is_default" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-gray-900 cursor-pointer" />
              <span class="text-sm text-gray-600">Set as default address</span>
            </label>
            <button @click="handleAddressSubmit" class="px-5 py-2 bg-indigo-600 text-white text-xs font-bold rounded-xl hover:bg-indigo-700 transition border-none cursor-pointer">
              {{ isEditingAddress ? 'Update Address' : 'Save Address' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Address Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div v-for="addr in addresses" :key="addr.id" class="relative border border-gray-100 rounded-2xl p-5 hover:border-gray-200 transition-all font-sans"
          :class="addr.is_default ? 'border-gray-900 ring-1 ring-gray-900 bg-gray-50/50' : ''">
          <div v-if="addr.is_default" class="absolute top-3 right-3 px-2 py-0.5 bg-gray-900 text-white text-[10px] font-bold rounded-full">Default</div>
          <div class="text-sm font-bold text-gray-900 mb-1">{{ addr.name }}</div>
          <div class="text-sm text-gray-500 leading-relaxed">{{ addr.line1 }}<br/>{{ addr.city }}, {{ addr.state }} {{ addr.zip }}<br/>{{ addr.country }}</div>
          <div class="flex gap-3 mt-4">
            <button @click="editAddress(addr)" class="text-xs font-bold text-violet-600 hover:text-violet-700 transition border-none bg-transparent cursor-pointer">Edit</button>
            <button v-if="!addr.is_default" @click="$emit('set-default', addr.id)" class="text-xs font-bold text-gray-500 hover:text-gray-700 transition border-none bg-transparent cursor-pointer">Set as default</button>
            <button v-if="!addr.is_default" @click="$emit('delete-address', addr.id)" class="text-xs font-bold text-red-400 hover:text-red-600 transition border-none bg-transparent cursor-pointer ml-auto">Delete</button>
          </div>
        </div>
        <div v-if="addresses.length === 0 && !isAddingAddress && !isEditingAddress" class="col-span-full py-8 text-center text-gray-500 text-sm border-2 border-dashed border-gray-100 rounded-2xl">
          No addresses saved yet. Minimum one address is needed for shipping.
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  addresses: { type: Array, default: () => [] }
})

const emit = defineEmits(['add-address', 'update-address', 'delete-address', 'set-default'])

const { getAll } = useCrud()

const isAddingAddress = ref(false)
const isEditingAddress = ref(false)
const editingId = ref(null)

const states = ref([])
const allCities = ref([])

// Search and suggestions state
const stateSearch = ref('')
const citySearch = ref('')
const showStateSuggestions = ref(false)
const showCitySuggestions = ref(false)

const addressForm = ref({
  name: '',
  line1: '',
  city: '',
  state: '',
  zip: '',
  country: 'Bangladesh',
  is_default: false
})

const filteredStates = computed(() => {
  if (!stateSearch.value) return states.value
  return states.value.filter(s => s.name.toLowerCase().includes(stateSearch.value.toLowerCase()))
})

const filteredCities = computed(() => {
  const selectedState = states.value.find(s => s.name === addressForm.value.state)
  if (!selectedState) return []
  const citiesInState = allCities.value.filter(c => c.state_id === selectedState.id)
  if (!citySearch.value) return citiesInState
  return citiesInState.filter(c => c.name.toLowerCase().includes(citySearch.value.toLowerCase()))
})

const fetchLocations = async () => {
  const [statesRes, citiesRes] = await Promise.all([
    getAll('/storefront/states'),
    getAll('/storefront/cities')
  ])
  if (statesRes?.success) states.value = statesRes.data
  if (citiesRes?.success) allCities.value = citiesRes.data
}

onMounted(fetchLocations)

const selectState = (state) => {
  addressForm.value.state = state.name
  stateSearch.value = state.name
  showStateSuggestions.value = false
  // Reset city
  addressForm.value.city = ''
  citySearch.value = ''
}

const selectCity = (city) => {
  addressForm.value.city = city.name
  citySearch.value = city.name
  showCitySuggestions.value = false
}

const hideSuggestions = (type) => {
  // Use timeout to allow mousedown to trigger first
  setTimeout(() => {
    if (type === 'state') showStateSuggestions.value = false
    if (type === 'city') showCitySuggestions.value = false
  }, 200)
}

const toggleAddForm = () => {
  if (isAddingAddress.value || isEditingAddress.value) {
    isAddingAddress.value = false
    isEditingAddress.value = false
    resetForm()
  } else {
    isAddingAddress.value = true
  }
}

const editAddress = (addr) => {
  isEditingAddress.value = true
  isAddingAddress.value = false
  editingId.value = addr.id
  addressForm.value = { ...addr }
  stateSearch.value = addr.state
  citySearch.value = addr.city
}

const resetForm = () => {
  addressForm.value = { name: '', line1: '', city: '', state: '', zip: '', country: 'Bangladesh', is_default: false }
  stateSearch.value = ''
  citySearch.value = ''
  editingId.value = null
}

const handleAddressSubmit = () => {
  if (isEditingAddress.value) {
    emit('update-address', editingId.value, { ...addressForm.value }, () => {
      isEditingAddress.value = false
      resetForm()
    })
  } else {
    emit('add-address', { ...addressForm.value }, () => {
      isAddingAddress.value = false
      resetForm()
    })
  }
}
</script>
