<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div class="flex items-center gap-4">
        <button @click="$router.back()" class="p-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm active:scale-95 group">
          <ChevronLeft class="w-5 h-5 text-slate-600 dark:text-slate-300 group-hover:-translate-x-0.5 transition-transform" />
        </button>
        <div>
          <h1 class="text-2xl font-black text-slate-900 dark:text-white leading-tight tracking-tight">Suppliers</h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 font-semibold opacity-80">Manage your product sources and contact details.</p>
        </div>
      </div>
      <button @click="openCreateModal" class="flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl transition-all shadow-lg shadow-indigo-600/20 active:scale-95 group">
        <Plus class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" />
        Add Supplier
      </button>
    </div>

    <!-- Content -->
    <div class="bg-white dark:bg-slate-900 rounded-[24px] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.06)] dark:shadow-none border border-slate-200/60 dark:border-slate-800 overflow-hidden">
      <div v-if="loading" class="flex justify-center py-20">
        <div class="animate-spin rounded-full h-10 w-10 border-b-4 border-indigo-600"></div>
      </div>

      <div v-else-if="suppliers.length === 0" class="text-center py-24 flex flex-col items-center justify-center">
        <div class="w-24 h-24 bg-slate-50 dark:bg-slate-800/50 rounded-full flex items-center justify-center mb-6">
          <Users class="w-10 h-10 text-slate-400 dark:text-slate-500" />
        </div>
        <h3 class="text-xl font-black text-slate-800 dark:text-slate-200 mb-2">No suppliers found</h3>
        <p class="text-slate-500 dark:text-slate-400 font-semibold mb-8 max-w-sm mx-auto">Start by adding your first supplier to better track your inventory sources and purchase history.</p>
        <button @click="openCreateModal" class="flex items-center gap-2 px-8 py-3.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-black rounded-2xl transition-all shadow-lg shadow-slate-900/20 dark:shadow-white/20 active:scale-95 group">
          <Plus class="w-5 h-5" />
          Add First Supplier
        </button>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left">
          <thead>
            <tr class="border-b border-slate-100 dark:border-slate-800/60">
              <th class="px-8 py-5 text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Supplier Info</th>
              <th class="px-8 py-5 text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Contact</th>
              <th class="px-8 py-5 text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">Status</th>
              <th class="px-8 py-5 text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50 dark:divide-slate-800/40">
            <tr v-for="supplier in suppliers" :key="supplier.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
              <td class="px-8 py-5">
                <div class="flex items-center gap-4">
                  <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-500/10 rounded-xl flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-black text-lg group-hover:scale-110 transition-transform">
                    {{ supplier.name.charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <div class="font-black text-slate-900 dark:text-white">{{ supplier.name }}</div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5 line-clamp-1 max-w-[200px]">{{ supplier.address || 'No address' }}</div>
                  </div>
                </div>
              </td>
              <td class="px-8 py-5">
                <div class="flex flex-col gap-1">
                  <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 font-medium">
                    <Mail class="w-3.5 h-3.5 opacity-50" />
                    {{ supplier.email || '-' }}
                  </div>
                  <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 font-medium">
                    <Phone class="w-3.5 h-3.5 opacity-50" />
                    {{ supplier.phone || '-' }}
                  </div>
                </div>
              </td>
              <td class="px-8 py-5">
                <span v-if="supplier.is_active" class="px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg text-[10px] font-black uppercase tracking-wider border border-emerald-100 dark:border-emerald-500/20">
                  Active
                </span>
                <span v-else class="px-3 py-1 bg-slate-100 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 rounded-lg text-[10px] font-black uppercase tracking-wider border border-slate-200 dark:border-slate-700/30">
                  Inactive
                </span>
              </td>
              <td class="px-8 py-5 text-right">
                <div class="flex items-center justify-end gap-2">
                  <button @click="openEditModal(supplier)" class="p-2 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                    <Edit3 class="w-5 h-5" />
                  </button>
                  <button @click="deleteSupplier(supplier.id)" class="p-2 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 transition-colors">
                    <Trash2 class="w-5 h-5" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Placeholder (Standard useCrud modal can be used here) -->
    <div v-if="modal.show" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="modal.show = false"></div>
        <div class="relative w-full max-w-lg bg-white dark:bg-slate-900 rounded-[32px] shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300">
            <div class="p-8">
                <h2 class="text-2xl font-black text-slate-900 dark:text-white mb-6">{{ modal.isEdit ? 'Edit Supplier' : 'Add Supplier' }}</h2>
                <div class="space-y-4">
                    <div>
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">Company Name</label>
                        <input v-model="form.name" type="text" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-3.5 text-sm font-bold focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">Email</label>
                            <input v-model="form.email" type="email" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-3.5 text-sm font-bold focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                        </div>
                        <div>
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">Phone</label>
                            <input v-model="form.phone" type="text" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-3.5 text-sm font-bold focus:ring-2 focus:ring-indigo-500/20 transition-all" />
                        </div>
                    </div>
                    <div>
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block">Address</label>
                        <textarea v-model="form.address" rows="3" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-3.5 text-sm font-bold focus:ring-2 focus:ring-indigo-500/20 transition-all"></textarea>
                    </div>
                </div>
                <div class="flex gap-3 mt-8">
                    <button @click="modal.show = false" class="flex-1 px-6 py-3.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-black rounded-2xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">Cancel</button>
                    <button @click="saveSupplier" :disabled="saving" class="flex-2 px-10 py-3.5 bg-indigo-600 text-white font-black rounded-2xl hover:bg-indigo-700 shadow-lg shadow-indigo-600/20 transition-all disabled:opacity-50">
                        {{ saving ? 'Saving...' : 'Save Supplier' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ layout: 'vendor', middleware: 'auth' })
import { ref, onMounted } from 'vue'
import { toast } from 'vue-sonner'
import { ChevronLeft, Plus, Users, Mail, Phone, Edit3, Trash2 } from 'lucide-vue-next'

const { getAll, createItem, updateItem, deleteItem } = useCrud()

const suppliers = ref([])
const loading = ref(true)
const saving = ref(false)

const modal = ref({
    show: false,
    isEdit: false,
    id: null
})

const form = ref({
    name: '',
    email: '',
    phone: '',
    address: '',
    is_active: true
})

const fetchSuppliers = async () => {
    loading.value = true
    try {
        const res = await getAll('/vendor/suppliers')
        suppliers.value = res.data
    } catch (e) {
        console.error(e)
    } finally {
        loading.value = false
    }
}

const openCreateModal = () => {
    modal.value = { show: true, isEdit: false, id: null }
    form.value = { name: '', email: '', phone: '', address: '', is_active: true }
}

const openEditModal = (supplier) => {
    modal.value = { show: true, isEdit: true, id: supplier.id }
    form.value = { ...supplier }
}

const saveSupplier = async () => {
    if (!form.value.name) return toast.error('Name is required')
    saving.value = true
    try {
        if (modal.value.isEdit) {
            await updateItem(`/vendor/suppliers/${modal.value.id}`, form.value)
            toast.success('Supplier updated')
        } else {
            await createItem('/vendor/suppliers', form.value)
            toast.success('Supplier added')
        }
        modal.value.show = false
        fetchSuppliers()
    } catch (e) {
        console.error(e)
    } finally {
        saving.value = false
    }
}

const deleteSupplier = async (id) => {
    if (!confirm('Are you sure you want to delete this supplier?')) return
    try {
        await deleteItem(`/vendor/suppliers/${id}`)
        toast.success('Supplier deleted')
        fetchSuppliers()
    } catch (e) {
        console.error(e)
    }
}

onMounted(fetchSuppliers)
</script>
