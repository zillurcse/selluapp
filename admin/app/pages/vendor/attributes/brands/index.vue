<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div class="flex items-center gap-4">
        <button @click="$router.back()" class="p-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm active:scale-95 group">
          <ChevronLeft class="w-5 h-5 text-slate-600 dark:text-slate-300 group-hover:-translate-x-0.5 transition-transform" />
        </button>
        <div>
          <h1 class="text-2xl font-black text-slate-900 dark:text-white leading-tight tracking-tight">Brands</h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 font-semibold opacity-80">Manage and showcase your product brands.</p>
        </div>
      </div>
      <NuxtLink to="/vendor/attributes/brands/create" class="flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl transition-all shadow-lg shadow-indigo-600/20 active:scale-95 group">
        <Plus class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" />
        Add Brand
      </NuxtLink>
    </div>
    
    <!-- Brands Table Container -->
    <div class="bg-white dark:bg-slate-900 rounded-[24px] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.06)] dark:shadow-none border border-slate-200/60 dark:border-slate-800 group overflow-hidden transition-colors duration-300">
      <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-900 dark:bg-slate-800 border-b border-slate-800 dark:border-slate-700">
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-[#f8fafc] dark:text-slate-200">Brand Name</th>
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-[#f8fafc] dark:text-slate-200">Slug</th>
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-[#f8fafc] dark:text-slate-200">Status</th>
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-[#f8fafc] dark:text-slate-200 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50 dark:divide-slate-800/50">
            <tr v-for="brand in brands" :key="brand.id" class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group/row">
              <td class="px-8 py-5 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="h-12 w-12 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 overflow-hidden flex-shrink-0 shadow-sm transition-transform group-hover/row:scale-105 duration-300">
                      <img v-if="brand.image" :src="`${config.public.apiBase}/storage/${brand.image}`" alt="" class="h-full w-full object-cover">
                      <div v-else class="h-full w-full flex items-center justify-center text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-800">
                        <ImageIcon class="w-5 h-5 opacity-40" />
                      </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-black text-slate-900 dark:text-white">{{ brand.name }}</div>
                    <div v-if="brand.description" class="text-[10px] font-medium text-slate-500 dark:text-slate-400 max-w-[200px] truncate">{{ brand.description }}</div>
                  </div>
                </div>
              </td>
              <td class="px-8 py-5 whitespace-nowrap">
                <span class="text-xs font-bold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-lg">{{ brand.slug }}</span>
              </td>
              <td class="px-8 py-5 whitespace-nowrap">
                <span :class="[
                  'px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-1.5',
                  brand.is_active ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20' : 
                  'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-500/20'
                ]">
                  <span class="w-1.5 h-1.5 rounded-full" :class="brand.is_active ? 'bg-emerald-600 dark:bg-emerald-400' : 'bg-red-600 dark:bg-red-400'"></span>
                  {{ brand.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-8 py-5 whitespace-nowrap text-right">
                <div class="flex justify-end gap-2 opacity-0 group-hover/row:opacity-100 transition-all duration-300 translate-x-4 group-hover/row:translate-x-0">
                  <NuxtLink :to="`/vendor/attributes/brands/${brand.id}`" class="p-2 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-600 dark:hover:bg-indigo-500 hover:text-white rounded-lg transition-all shadow-sm border border-indigo-100 dark:border-indigo-500/20">
                    <Pencil class="w-4 h-4" />
                  </NuxtLink>
                  <button @click="deleteBrand(brand.id)" class="p-2 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-600 dark:hover:bg-red-500 hover:text-white rounded-lg transition-all shadow-sm border border-red-100 dark:border-red-500/20">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="brands.length === 0">
                <td colspan="4" class="py-32 px-8 text-center">
                  <div class="flex flex-col items-center justify-center gap-4 animate-in fade-in zoom-in duration-700">
                    <div class="w-20 h-20 bg-slate-50 dark:bg-slate-800 rounded-3xl flex items-center justify-center border border-slate-100 dark:border-slate-700 shadow-inner">
                       <Award class="w-10 h-10 text-slate-300 dark:text-slate-500" />
                    </div>
                    <div class="text-center">
                      <h3 class="text-lg font-black text-slate-800 dark:text-slate-200">No brands found</h3>
                      <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Start by adding your first brand.</p>
                    </div>
                  </div>
                </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  ChevronLeft, 
  Plus, 
  Pencil, 
  Trash2, 
  Image as ImageIcon, 
  Award 
} from 'lucide-vue-next'

definePageMeta({
  middleware: 'auth',
  permissions: 'brands.view'
})

const config = useRuntimeConfig()
const auth = useAuthStore()
const brands = ref([])
const { deleteItem, getAll } = useCrud()

// Fetch brands
const fetchBrands = async () => {
    try {
        const res = await getAll('/vendor/attributes/brands')
        if (res) {
            brands.value = res.data || res
        }
    } catch (e) {
        console.error('Failed to fetch brands')
    }
}

await fetchBrands()

const deleteBrand = async (id) => {
    if (!confirm('Are you sure you want to delete this brand?')) return

    try {
        await deleteItem(id, '/vendor/attributes/brands')
        await fetchBrands()
    } catch (error) {
        console.error('Failed to delete brand', error)
    }
}
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  height: 8px;
  width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
  border: 2px solid #f1f5f9;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
  border-radius: 10px;
}

.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 #f1f5f9;
}
</style>

