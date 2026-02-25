<template>
  <div class="px-6 py-4 bg-white dark:bg-slate-900 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between gap-4 shrink-0 z-10">
    <div class="flex items-center gap-2 flex-1 max-w-sm">
      <button @click="showExitModal = true" class="w-10 h-10 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 rounded-lg flex items-center justify-center hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors shrink-0" title="Close POS">
        <X class="w-5 h-5" />
      </button>
      <div class="flex-1 relative" v-click-outside="() => showDropdown = false">
        <div
          @click="toggleDropdown"
          class="w-full h-10 px-4 bg-gray-50 dark:bg-slate-800 border border-transparent rounded-full flex items-center gap-2 text-[13px] font-semibold text-gray-700 dark:text-slate-200 cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700 transition-all border-dashed border-gray-300 dark:border-slate-600"
        >
          <User class="w-4 h-4 text-gray-400" />
          <span class="truncate">{{ selectedCustomerData?.name || 'Walk-in Customer' }}</span>
        </div>
        <!-- Customer Dropdown -->
        <div v-if="showDropdown" class="absolute top-[calc(100%+6px)] left-0 w-full min-w-[320px] bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-2xl z-50 overflow-hidden ring-1 ring-black/5 animate-in fade-in zoom-in-95 duration-200">
          <div class="p-4 bg-white dark:bg-slate-900">
            <div class="relative group">
              <input
                ref="searchInput"
                v-model="internalCustomerSearchQuery"
                type="text"
                placeholder="Search by name, phone..."
                class="w-full h-10 pl-10 pr-4 bg-gray-50 dark:bg-slate-800 border border-transparent rounded-xl text-[13px] font-bold outline-none focus:border-blue-500 transition-all placeholder:text-gray-300 dark:text-slate-200"
                @click.stop
              />
              <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
            </div>
            <div class="max-h-[320px] overflow-y-auto no-scrollbar pb-3">
              <div
                v-for="cust in filteredCustomers"
                :key="cust.id"
                @click="selectCustomer(cust)"
                class="px-6 py-3.5 hover:bg-gray-50 dark:hover:bg-slate-800 cursor-pointer flex flex-col gap-0.5 border-b border-gray-50 dark:border-slate-800 last:border-0"
                :class="{ 'bg-blue-50/50': selectedCustomer === cust.id }"
              >
                <span class="text-[14px] font-black text-[#1e293b] dark:text-slate-100">{{ cust.name }}</span>
                <div class="flex items-center gap-2">
                  <span v-if="cust.phone" class="text-[11px] text-gray-400 font-bold tracking-tight bg-gray-100 px-1.5 py-0.5 rounded">{{ cust.phone }}</span>
                  <span v-if="cust.id === 'walking'" class="text-[10px] text-blue-500 font-black uppercase tracking-widest bg-blue-50 px-1.5 py-0.5 rounded">Default</span>
                </div>
              </div>
              <div v-if="filteredCustomers.length === 0" class="px-6 py-10 text-center">
                <p class="text-[13px] text-gray-400 font-bold mb-4 uppercase tracking-wider">No customer found</p>
                <button
                  @click.stop="openAddCustomerModal"
                  class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-indigo-600 text-white text-[12px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100"
                >
                  <Plus class="w-4 h-4" />
                  Add New Customer
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <button @click="openAddCustomerModal" class="w-10 h-10 bg-[#6366f1] text-white rounded-lg flex items-center justify-center hover:bg-[#4f46e5] transition-colors shrink-0">
        <UserPlus class="w-5 h-5" />
      </button>
    </div>

    <!-- Clock & Vendor Display -->
    <div class="hidden lg:flex items-center gap-3 px-4 py-1.5 bg-gray-50 dark:bg-slate-800/50 rounded-xl border border-gray-100 dark:border-slate-800">
      <div class="flex items-center gap-3 border-r border-gray-200 dark:border-slate-700 pr-3 mr-1">
        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
          <Store class="w-4 h-4" />
        </div>
        <div class="flex flex-col">
          <span class="text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider leading-none mb-1">Merchant</span>
          <span class="text-[13px] font-black text-gray-800 dark:text-slate-100 leading-none truncate max-w-[120px]">{{ storeName }}</span>
        </div>
      </div>
      <div class="flex items-center gap-3">
        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
          <Clock class="w-4 h-4" />
        </div>
        <div class="flex flex-col">
          <span class="text-[13px] font-black text-gray-800 dark:text-slate-100 tabular-nums leading-none">{{ formattedTime }}</span>
          <span class="text-[10px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider mt-1 leading-none">{{ formattedDate }}</span>
        </div>
      </div>
    </div>

    <div class="flex-1 max-w-xl relative">
      <div class="relative group">
        <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
        <input
          v-model="internalSearchQuery"
          type="text"
          placeholder="Scan/Search Product by Code or Name"
          class="w-full h-10 pl-11 pr-4 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-full text-[13px] font-medium outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all dark:text-slate-200"
        />
      </div>
    </div>
  </div>

  <!-- Exit Modal -->
  <div v-if="showExitModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-2xl w-full max-w-sm p-6 animate-in">
      <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Exit POS</h3>
      <p class="text-[15px] text-gray-600 dark:text-gray-300 mb-8">Are you sure you want to exit the POS system?</p>
      
      <div class="flex justify-end gap-3">
        <button 
          @click="showExitModal = false" 
          class="px-5 py-2.5 bg-gray-200 text-gray-700 dark:bg-slate-800 dark:text-slate-300 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-slate-700 transition-colors"
        >
          Cancel
        </button>
        <button 
          @click="exitPos" 
          class="px-5 py-2.5 bg-[#dc2626] text-white rounded-lg font-medium hover:bg-red-700 transition-colors shadow-sm shadow-red-200"
        >
          Exit
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { User, Search, Plus, UserPlus, X, Clock, Store } from 'lucide-vue-next'

const props = defineProps({
  storeName: {
    type: String,
    default: 'My Store'
  },
  selectedCustomerData: {
    type: Object,
    default: () => ({ name: 'Walk-in Customer' })
  },
  filteredCustomers: {
    type: Array,
    default: () => []
  }
})

const selectedCustomer = defineModel('selectedCustomer', { type: [String, Number] })
const internalCustomerSearchQuery = defineModel('customerSearchQuery', { type: String })
const internalSearchQuery = defineModel('searchQuery', { type: String })

const emit = defineEmits(['openAddCustomerModal'])

const showDropdown = ref(false)
const searchInput = ref(null)
const showAddCustomer = ref(false)
const showExitModal = ref(false)

// Clock Logic
const currentTime = ref(new Date())
let timer = null

const formattedDate = computed(() => {
  return new Intl.DateTimeFormat('en-GB', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  }).format(currentTime.value)
})

const formattedTime = computed(() => {
  return new Intl.DateTimeFormat('en-GB', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    hour12: true
  }).format(currentTime.value)
})

onMounted(() => {
  timer = setInterval(() => {
    currentTime.value = new Date()
  }, 1000)
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})

const exitPos = async () => {
  await navigateTo('/vendor')
}

const vClickOutside = {
  mounted(el, binding) {
    el.clickOutsideEvent = (event) => {
      if (!(el === event.target || el.contains(event.target))) binding.value(event)
    }
    document.addEventListener('click', el.clickOutsideEvent)
  },
  unmounted(el) {
    document.removeEventListener('click', el.clickOutsideEvent)
  }
}

const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value
  if (showDropdown.value) {
    nextTick(() => searchInput.value?.focus())
  }
}

const selectCustomer = (customer) => {
  selectedCustomer.value = customer.id
  showDropdown.value = false
  internalCustomerSearchQuery.value = ''
}

const openAddCustomerModal = () => {
  showDropdown.value = false
  emit('openAddCustomerModal')
}
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

@keyframes zoom-in {
  from { opacity: 0; transform: scale(0.95); }
  to   { opacity: 1; transform: scale(1); }
}
.animate-in { animation: zoom-in 0.3s ease-out forwards; }
</style>
