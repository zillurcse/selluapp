<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <!-- Header Section -->
    <div class="max-w-4xl mx-auto mb-8">
      <div class="flex items-center gap-4">
        <button @click="$router.push('/vendor/attributes/units')" class="p-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm active:scale-95 group">
          <ChevronLeft class="w-5 h-5 text-slate-600 dark:text-slate-300 group-hover:-translate-x-0.5 transition-transform" />
        </button>
        <div>
          <h1 class="text-2xl font-black text-slate-900 dark:text-white leading-tight tracking-tight">Create Unit</h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 font-semibold opacity-80">Add a new unit of measurement.</p>
        </div>
      </div>
    </div>

    <div class="max-w-4xl mx-auto">
      <div class="bg-white dark:bg-slate-900 rounded-[32px] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.06)] dark:shadow-none border border-slate-200/60 dark:border-slate-800 p-8 md:p-12 transition-all">
        <form @submit.prevent="createUnit" class="space-y-10">
          <!-- Basic Information -->
          <div class="space-y-6">
            <div class="flex items-center gap-3 mb-2">
              <div class="w-8 h-8 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                <LayoutGrid class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
              </div>
              <h3 class="text-lg font-black text-slate-900 dark:text-white">Unit Details</h3>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
              <div class="space-y-2">
                <label class="block text-sm font-black text-slate-700 dark:text-slate-300 ml-1">Unit Name <span class="text-rose-500">*</span></label>
                <input v-model="form.name" type="text" placeholder="e.g. Kilogram, Litre" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium" required />
              </div>
              <div class="space-y-2">
                <label class="block text-sm font-black text-slate-700 dark:text-slate-300 ml-1">Symbol <span class="text-rose-500">*</span></label>
                <input v-model="form.symbol" type="text" placeholder="e.g. kg, L" class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium" required />
              </div>
            </div>

            <div class="space-y-2">
              <label class="block text-sm font-black text-slate-700 dark:text-slate-300 ml-1">Description</label>
              <textarea v-model="form.description" rows="3" placeholder="Brief unit description..." class="w-full px-5 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-medium resize-none"></textarea>
            </div>
          </div>

          <!-- Visibility Settings -->
          <div class="space-y-6 pt-6 border-t border-slate-100 dark:border-slate-800">
             <div class="flex items-center justify-between p-6 bg-slate-50 dark:bg-slate-800/50 rounded-3xl border border-slate-100 dark:border-slate-700">
                <div class="flex items-center gap-4">
                  <div class="w-10 h-10 bg-white dark:bg-slate-900 rounded-xl flex items-center justify-center shadow-sm">
                    <Power class="w-5 h-5 transition-colors duration-300" :class="form.is_active ? 'text-emerald-500' : 'text-slate-400'" />
                  </div>
                  <div>
                    <h4 class="text-sm font-black text-slate-900 dark:text-white leading-none">Unit Visibility</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Determine if this unit is active and selectable.</p>
                  </div>
                </div>
                <!-- Simple Toggle -->
                <button @click.prevent="form.is_active = !form.is_active" :class="form.is_active ? 'bg-indigo-600' : 'bg-slate-300 dark:bg-slate-700'" class="w-14 h-8 rounded-full relative transition-all duration-300">
                  <div :class="form.is_active ? 'translate-x-7' : 'translate-x-1'" class="absolute top-1 left-0 w-6 h-6 bg-white rounded-full shadow-md transition-transform duration-300"></div>
                </button>
             </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center justify-end gap-4 pt-10 mt-10 border-t border-slate-100 dark:border-slate-800">
             <button type="button" @click="$router.push('/vendor/attributes/units')" class="px-8 py-4 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-2xl hover:bg-slate-200 dark:hover:bg-slate-700 font-black text-sm transition-all active:scale-95">
               Cancel
             </button>
             <button type="submit" :disabled="isLoading" class="flex-1 md:flex-none px-10 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl transition-all shadow-lg shadow-indigo-600/20 active:scale-95 disabled:opacity-50 disabled:scale-100 flex items-center justify-center gap-2">
               <Loader2 v-if="isLoading" class="w-5 h-5 animate-spin" />
               <Save v-else class="w-5 h-5" />
               Create Unit
             </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  ChevronLeft, 
  LayoutGrid, 
  Power, 
  Save, 
  Loader2 
} from 'lucide-vue-next'

definePageMeta({
  middleware: 'auth',
  permissions: 'units.create'
})

const auth = useAuthStore()
const router = useRouter()
const { createItem } = useCrud()

const isLoading = ref(false)
const form = reactive({
  name: '',
  symbol: '',
  description: '',
  is_active: true
})

const createUnit = async () => {
    isLoading.value = true
    try {
        await createItem('/vendor/attributes/units', form)
        router.push('/vendor/attributes/units')
    } catch (error) {
        console.error('Failed to create unit', error)
    }
}
</script>
