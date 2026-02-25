<template>
  <div class="p-8">
    <div class="flex items-center justify-between mb-10">
      <div>
        <h1 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight">Payment Settings</h1>
        <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">Configure global payment gateways and transaction rules.</p>
      </div>
      <button 
        @click="openCreateModal"
        class="px-6 py-3 bg-indigo-600 text-white rounded-xl text-sm font-bold hover:scale-105 transition-all shadow-lg flex items-center gap-2"
      >
        <Plus class="w-4 h-4" /> Add Payment Method
      </button>
    </div>
    
    <!-- Dynamic Payment Methods List -->
    <div class="mb-12">
      <h2 class="text-xl font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-2">
        <CreditCard class="w-5 h-5 text-indigo-500" />
        Configured Gateways
      </h2>
      
      <div v-if="methodsLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="i in 3" :key="i" class="h-48 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 animate-pulse"></div>
      </div>
      
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div 
          v-for="method in paymentMethods" 
          :key="method.id"
          class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/60 dark:border-slate-800/60 p-6 flex flex-col justify-between group hover:shadow-xl transition-all duration-300"
        >
          <div class="flex items-start justify-between mb-4">
            <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-500/10 rounded-2xl flex items-center justify-center">
              <component :is="getIcon(method.icon)" class="w-6 h-6 text-indigo-600" />
            </div>
            <div class="flex items-center gap-2">
              <span 
                :class="['px-2 py-1 rounded-md text-[10px] font-black uppercase tracking-wider', 
                method.is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500']"
              >
                {{ method.is_active ? 'Active' : 'Inactive' }}
              </span>
              <button @click="openEditModal(method)" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg transition-colors">
                <Pencil class="w-4 h-4 text-slate-400" />
              </button>
              <button @click="confirmDelete(method)" class="p-2 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg transition-colors">
                <Trash2 class="w-4 h-4 text-rose-400" />
              </button>
            </div>
          </div>
          
          <div>
            <h3 class="font-black text-slate-900 dark:text-white">{{ method.name }}</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">{{ method.description }}</p>
            <div class="mt-4 flex gap-1 flex-wrap">
              <span v-for="field in method.config_fields" :key="field.name" class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-[9px] font-bold text-slate-500 rounded uppercase">
                {{ field.label }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="border-t border-slate-100 dark:border-slate-800 pt-10">
      <h2 class="text-xl font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-2">
        <Settings class="w-5 h-5 text-indigo-500" />
        Global Preferences
      </h2>
      <GlobalSettings filterGroup="payments" />
    </div>

    <!-- Create/Edit Modal -->
    <Teleport to="body">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModal"></div>
        <div class="relative w-full max-w-2xl bg-white dark:bg-slate-900 rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
          <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between flex-shrink-0">
            <h2 class="text-xl font-black text-slate-900 dark:text-white">{{ editingId ? 'Edit Payment Method' : 'Add Payment Method' }}</h2>
            <button @click="closeModal" class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 transition-all">
              <X class="w-4 h-4" />
            </button>
          </div>
          
          <div class="p-8 space-y-8 overflow-y-auto">
            <!-- Basic Info -->
            <div class="space-y-6">
              <h3 class="text-xs font-black text-indigo-600 uppercase tracking-widest border-b border-indigo-50 pb-2">Basic Info</h3>
              <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                  <label class="text-xs font-black text-slate-500 uppercase">Method Name</label>
                  <input v-model="form.name" type="text" placeholder="e.g. Stripe" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm outline-none focus:ring-2 focus:ring-indigo-500/50" />
                </div>
                <div class="space-y-2">
                  <label class="text-xs font-black text-slate-500 uppercase">Slug</label>
                  <input v-model="form.slug" type="text" placeholder="e.g. stripe" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm outline-none focus:ring-2 focus:ring-indigo-500/50" />
                </div>
              </div>

              <div class="space-y-2">
                <label class="text-xs font-black text-slate-500 uppercase">Description</label>
                <textarea v-model="form.description" rows="2" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm outline-none focus:ring-2 focus:ring-indigo-500/50 resize-none"></textarea>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                  <label class="text-xs font-black text-slate-500 uppercase">Icon</label>
                  <select v-model="form.icon" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm outline-none focus:ring-2 focus:ring-indigo-500/50 appearance-none">
                    <option value="CreditCard">CreditCard</option>
                    <option value="Wallet">Wallet</option>
                    <option value="Banknote">Banknote</option>
                    <option value="Zap">Zap</option>
                    <option value="Globe">Globe</option>
                  </select>
                </div>
                <div class="space-y-2">
                  <label class="text-xs font-black text-slate-500 uppercase">Status</label>
                  <div class="flex items-center mt-2">
                     <button 
                      @click="form.is_active = !form.is_active"
                      class="relative inline-flex h-7 w-12 items-center rounded-full transition-all duration-300"
                      :class="form.is_active ? 'bg-emerald-500' : 'bg-slate-200 dark:bg-slate-700'"
                    >
                      <span 
                        class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform duration-300 shadow-sm"
                        :class="form.is_active ? 'translate-x-6' : 'translate-x-1'"
                      ></span>
                    </button>
                    <span class="ml-3 text-xs font-bold" :class="form.is_active ? 'text-emerald-600' : 'text-slate-400'">
                      {{ form.is_active ? 'Active' : 'Disabled' }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Config Fields -->
            <div class="space-y-6">
              <div class="flex items-center justify-between border-b border-indigo-50 pb-2">
                <h3 class="text-xs font-black text-indigo-600 uppercase tracking-widest">Configuration Fields</h3>
                <button @click="addField" class="text-[10px] font-black uppercase text-indigo-600 hover:text-indigo-700 flex items-center gap-1">
                  <Plus class="w-3 h-3" /> Add Field
                </button>
              </div>

              <div v-if="form.config_fields.length === 0" class="text-center py-6 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700">
                <p class="text-xs text-slate-400 font-bold">No config fields defined. Vendors will only see the toggle.</p>
              </div>

              <div class="space-y-4">
                <div v-for="(field, index) in form.config_fields" :key="index" class="p-5 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl relative group/field">
                  <button @click="removeField(index)" class="absolute -top-2 -right-2 p-1 bg-rose-500 text-white rounded-full opacity-0 group-hover/field:opacity-100 transition-opacity shadow-lg">
                    <X class="w-3 h-3" />
                  </button>
                  
                  <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="space-y-1">
                      <label class="text-[10px] font-black text-slate-400 uppercase">Field Key</label>
                      <input v-model="field.name" type="text" placeholder="e.g. number" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs outline-none focus:ring-2 focus:ring-indigo-500/50" />
                    </div>
                    <div class="space-y-1">
                      <label class="text-[10px] font-black text-slate-400 uppercase">Field Label</label>
                      <input v-model="field.label" type="text" placeholder="e.g. Bkash Number" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs outline-none focus:ring-2 focus:ring-indigo-500/50" />
                    </div>
                    <div class="space-y-1">
                      <label class="text-[10px] font-black text-slate-400 uppercase">Type</label>
                      <select v-model="field.type" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs outline-none focus:ring-2 focus:ring-indigo-500/50">
                        <option value="text">Text</option>
                        <option value="password">Password</option>
                        <option value="select">Select</option>
                      </select>
                    </div>
                    <div class="space-y-1">
                      <label class="text-[10px] font-black text-slate-400 uppercase">Placeholder</label>
                      <input v-model="field.placeholder" type="text" placeholder="Optional" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs outline-none focus:ring-2 focus:ring-indigo-500/50" />
                    </div>
                  </div>

                  <!-- Options for Select Type -->
                  <div v-if="field.type === 'select'" class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                    <div class="flex items-center justify-between mb-2">
                       <label class="text-[10px] font-black text-slate-400 uppercase">Select Options</label>
                       <button @click="addOption(index)" class="text-[9px] font-black text-indigo-500 uppercase">Add Option</button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                      <div v-for="(opt, optIndex) in field.options" :key="optIndex" class="flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg pl-3 pr-1 py-1 gap-2">
                        <input v-model="field.options[optIndex]" type="text" class="bg-transparent border-none outline-none text-[10px] font-bold w-20" />
                        <button @click="removeOption(index, optIndex)" class="text-slate-300 hover:text-rose-500 transition-colors">
                          <X class="w-3 h-3" />
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="px-8 py-5 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3 flex-shrink-0">
            <button @click="closeModal" class="px-5 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 transition-all">Cancel</button>
            <button @click="saveMethod" :disabled="saving" class="flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-all disabled:opacity-50">
              <Loader2 v-if="saving" class="w-4 h-4 animate-spin" />
              {{ saving ? 'Saving...' : 'Save Method' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { Plus, CreditCard, Settings, Pencil, Trash2, X, Loader2, Wallet, Banknote, Zap, Globe } from 'lucide-vue-next'
import { ref, onMounted } from 'vue'
import { toast } from 'vue-sonner'

definePageMeta({
  middleware: 'auth',
  permissions: 'admin.settings.view'
})

const { getAll } = useCrud()
const paymentMethods = ref([])
const methodsLoading = ref(true)
const saving = ref(false)
const showModal = ref(false)
const editingId = ref(null)

const form = ref({
  name: '',
  slug: '',
  description: '',
  icon: 'CreditCard',
  is_active: true,
  config_fields: []
})

const fetchPaymentMethods = async () => {
  methodsLoading.value = true
  try {
    const res = await getAll('/admin/payment-methods')
    paymentMethods.value = res || []
  } catch (e) {
    console.error(e)
    toast.error('Failed to fetch payment methods')
  } finally {
    methodsLoading.value = false
  }
}

const openCreateModal = () => {
  editingId.value = null
  form.value = {
    name: '',
    slug: '',
    description: '',
    icon: 'CreditCard',
    is_active: true,
    config_fields: []
  }
  showModal.value = true
}

const openEditModal = (method) => {
  editingId.value = method.id
  // Deep clone to avoid direct mutation
  form.value = JSON.parse(JSON.stringify(method))
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingId.value = null
}

const addField = () => {
  form.value.config_fields.push({
    name: '',
    label: '',
    type: 'text',
    placeholder: '',
    options: []
  })
}

const removeField = (index) => {
  form.value.config_fields.splice(index, 1)
}

const addOption = (fieldIndex) => {
  if (!form.value.config_fields[fieldIndex].options) {
    form.value.config_fields[fieldIndex].options = []
  }
  form.value.config_fields[fieldIndex].options.push('')
}

const removeOption = (fieldIndex, optIndex) => {
  form.value.config_fields[fieldIndex].options.splice(optIndex, 1)
}

const saveMethod = async () => {
  // Validate basic slug
  if (!form.value.name || !form.value.slug) {
    toast.error('Name and Slug are required')
    return
  }

  saving.value = true
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase
  const { getHeaders } = useCrud()
  
  try {
    const url = editingId.value 
      ? `${apiBase}/admin/payment-methods/${editingId.value}` 
      : `${apiBase}/admin/payment-methods`
    
    await $fetch(url, {
      method: editingId.value ? 'PUT' : 'POST',
      body: form.value,
      headers: getHeaders()
    })
    
    toast.success(editingId.value ? 'Method updated' : 'Method created')
    fetchPaymentMethods()
    closeModal()
  } catch (e) {
    console.error(e)
    toast.error(e.data?.message || 'Failed to save payment method')
  } finally {
    saving.value = false
  }
}

const confirmDelete = async (method) => {
  if (!confirm(`Are you sure you want to delete ${method.name}?`)) return
  
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase
  const { getHeaders } = useCrud()
  
  try {
    await $fetch(`${apiBase}/admin/payment-methods/${method.id}`, {
      method: 'DELETE',
      headers: getHeaders()
    })
    toast.success('Method deleted')
    fetchPaymentMethods()
  } catch (e) {
    toast.error('Failed to delete method')
  }
}

const getIcon = (iconName) => {
  const icons = { CreditCard, Wallet, Banknote, Zap, Globe }
  return icons[iconName] || CreditCard
}

onMounted(fetchPaymentMethods)
</script>

<style scoped>
.overflow-y-auto::-webkit-scrollbar {
  width: 4px;
}
.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}
.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #e2e8f0;
  border-radius: 10px;
}
.dark .overflow-y-auto::-webkit-scrollbar-thumb {
  background: #1e293b;
}
.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #cbd5e1;
}
</style>
