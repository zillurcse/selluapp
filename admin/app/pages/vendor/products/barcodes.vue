<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 transition-colors duration-300">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div class="flex items-center gap-4">
        <button @click="$router.back()" class="p-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm active:scale-95 group">
          <ChevronLeft class="w-5 h-5 text-slate-600 dark:text-slate-300 group-hover:-translate-x-0.5 transition-transform" />
        </button>
        <div>
          <h1 class="text-2xl font-black text-slate-900 dark:text-white leading-tight tracking-tight">Product Barcodes</h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 font-semibold opacity-80">Generate and print barcodes for your products.</p>
        </div>
      </div>
      <div class="flex items-center gap-3">
        <!-- Status Tabs -->
        <div class="flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl mr-2">
            <button v-for="s in statusOptions" :key="s.value" @click="currentStatus = s.value" :class="[
                'px-4 py-1.5 text-xs font-black uppercase tracking-wider rounded-lg transition-all',
                currentStatus === s.value ? 'bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'
            ]">
                {{ s.label }}
            </button>
        </div>
        <button @click="generateMissing" :disabled="generating" class="flex items-center justify-center gap-2 px-6 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-200 font-black rounded-2xl transition-all shadow-sm hover:shadow-md hover:border-slate-300 dark:hover:border-slate-700 active:scale-95 group disabled:opacity-50 disabled:cursor-not-allowed">
          <RefreshCw class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500" :class="generating ? 'animate-spin' : ''" />
          <span class="hidden xl:inline">{{ generating ? 'Generating...' : 'Generate Missing' }}</span>
        </button>
        <button @click="handlePrint" :disabled="!hasItemsToPrint" class="flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl transition-all shadow-lg shadow-indigo-600/20 active:scale-95 group disabled:opacity-50 disabled:cursor-not-allowed">
          <Printer class="w-5 h-5 group-hover:scale-110 transition-transform duration-300" />
          <span class="hidden sm:inline">Print Labels</span>
        </button>
      </div>
    </div>

    <!-- Cards Layout for Print Setup -->
    <div class="bg-white dark:bg-slate-900 rounded-[24px] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.06)] dark:shadow-none border border-slate-200/60 dark:border-slate-800 p-8 no-print transition-colors duration-300">
      <div v-if="pending" class="flex justify-center py-16">
        <div class="animate-spin rounded-full h-10 w-10 border-b-4 border-indigo-600"></div>
      </div>
      
      <div v-else-if="!data?.data?.data || data.data.data.length === 0" class="text-center py-20 flex flex-col items-center justify-center">
        <div class="w-24 h-24 bg-slate-50 dark:bg-slate-800/50 rounded-full flex items-center justify-center mb-6">
          <Tag class="w-10 h-10 text-slate-400 dark:text-slate-500" />
        </div>
        <h3 class="text-xl font-black text-slate-800 dark:text-slate-200 mb-2">No barcodes found</h3>
        <p class="text-slate-500 dark:text-slate-400 font-semibold mb-6">You need to generate barcodes for your products before printing.</p>
        <button @click="generateMissing" :disabled="generating" class="flex items-center gap-2 px-8 py-3.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-black rounded-2xl transition-all shadow-lg shadow-slate-900/20 dark:shadow-white/20 active:scale-95 group disabled:opacity-50">
          <RefreshCw class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500" :class="generating ? 'animate-spin' : ''" />
          Generate Now
        </button>
      </div>

      <div v-else class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-5">
        <!-- Display items just for preview -->
        <div v-for="item in data.data.data" :key="item.id" class="border border-slate-100 dark:border-slate-800/60 bg-slate-50/50 dark:bg-slate-800/20 hover:bg-white dark:hover:bg-slate-800 p-5 rounded-[20px] text-center flex flex-col justify-center items-center transition-all shadow-sm hover:shadow-md group relative overflow-hidden">
          
          <div class="text-xs font-black text-slate-900 dark:text-white mb-0.5 truncate w-full" :title="item.product.name">{{ item.product.name }}</div>
          <div class="flex items-center gap-2 mb-3">
            <div v-if="item.variant" class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest truncate">V: {{ item.variant.sku }}</div>
            <span v-if="item.is_printed" class="px-1.5 py-0.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded text-[7px] font-black uppercase tracking-tighter border border-emerald-100 dark:border-emerald-500/20">
                Printed
            </span>
          </div>
          
          <div class="bg-white p-2.5 rounded-xl shadow-sm border border-slate-100 w-full mb-1 group-hover:border-indigo-100 transition-colors">
            <img :src="'data:image/png;base64,' + item.barcode_image" class="w-full h-14 object-contain group-hover:scale-105 transition-transform" alt="Barcode" />
          </div>
          <div class="text-[10px] font-bold text-slate-500 tracking-widest mt-2 mb-5">{{ item.barcode }}</div>
          
          <!-- Modern QTY Input -->
          <div class="mt-auto w-full flex items-center justify-between bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700/60 rounded-xl overflow-hidden group-hover:border-indigo-200 dark:group-hover:border-indigo-500/30 transition-colors shadow-sm focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-500">
            <div class="px-3.5 text-[9px] font-black text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 py-2.5 border-r border-indigo-100 dark:border-indigo-500/20 uppercase tracking-[0.2em]">
              QTY
            </div>
            <input type="number" v-model="item.print_qty" class="w-full bg-transparent border-none text-sm font-black text-center text-slate-900 dark:text-white focus:ring-0 py-2 hide-arrows" min="0" />
          </div>
        </div>
      </div>
      
      <!-- Modern Pagination Component -->
      <div v-if="totalPages > 1" class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-10">
        <div class="text-sm font-medium text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800/50 px-4 py-2 rounded-xl">
          Showing <span class="font-black text-slate-900 dark:text-white">{{ pagination.from }}</span> to 
          <span class="font-black text-slate-900 dark:text-white">{{ pagination.to }}</span> of 
          <span class="font-black text-slate-900 dark:text-white">{{ pagination.total }}</span> results
        </div>
        <div class="flex items-center gap-2">
          <button 
            @click="page--" 
            :disabled="page <= 1"
            class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all active:scale-95 bg-white dark:bg-slate-900 shadow-sm"
          >
            <ChevronLeft class="w-4 h-4" />
          </button>
          <div class="flex items-center gap-1">
            <button 
              v-for="p in visiblePages" 
              :key="p"
              @click="p !== '...' && (page = p)"
              :class="[
                'w-10 h-10 flex items-center justify-center rounded-xl text-sm font-bold transition-all',
                p === page 
                  ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' 
                  : p === '...' 
                    ? 'text-slate-400 dark:text-slate-500 cursor-default bg-transparent' 
                    : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer active:scale-95 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm'
              ]"
            >
              {{ p }}
            </button>
          </div>
          <button 
            @click="page++" 
            :disabled="page >= totalPages"
            class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all active:scale-95 bg-white dark:bg-slate-900 shadow-sm"
          >
            <ChevronRight class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- Print Layout (Visible only when printing) -->
    <div class="print-only" id="printable-area">
      <div class="label-grid" v-if="data?.data?.data">
        <template v-for="item in data.data.data" :key="'p_'+item.id">
          <!-- Loop to print identical barcodes based on user input -->
          <div class="label-item" v-for="n in (item.print_qty || 0)" :key="'l_'+item.id+'_'+n">
            <div class="label-content">
              <div class="label-title">{{ item.product.name }}{{ item.variant ? ' ('+item.variant.sku+')' : '' }}</div>
              <img :src="'data:image/png;base64,' + item.barcode_image" class="label-barcode" alt="barcode" />
              <div class="label-number">{{ item.barcode }}</div>
            </div>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({ 
  layout: 'vendor',
  middleware: 'auth',
  permissions: 'barcodes.view'
})
import { ref, watch, onMounted, computed } from 'vue'
import { toast } from 'vue-sonner'
import { ChevronLeft, ChevronRight, Printer, RefreshCw, Tag, Search, X } from 'lucide-vue-next'


const page = ref(1)
const searchQuery = ref('')
const currentStatus = ref('all')
const statusOptions = [
    { label: 'All', value: 'all' },
    { label: 'Not Printed', value: 'not_printed' },
    { label: 'Printed', value: 'printed' },
]
const generating = ref(false)
const pending = ref(true)
const data = ref(null)

const { barcodePrint, createItem } = useCrud()

const hasItemsToPrint = computed(() => {
    return data.value?.data?.data?.some(item => (item.print_qty || 0) > 0)
})

// Modern Pagination Logic
const pagination = computed(() => {
  if (!data.value?.data) return { current_page: 1, last_page: 1, total: 0, from: 0, to: 0 }
  return {
    current_page: data.value.data.current_page,
    last_page: data.value.data.last_page,
    total: data.value.data.total,
    from: data.value.data.from || 0,
    to: data.value.data.to || 0
  }
})

const totalPages = computed(() => pagination.value.last_page)

const visiblePages = computed(() => {
  const current = page.value
  const last = totalPages.value
  
  if (last <= 7) return Array.from({ length: last }, (_, i) => i + 1)
  if (current <= 4) return [1, 2, 3, 4, 5, '...', last]
  if (current >= last - 3) return [1, '...', last - 4, last - 3, last - 2, last - 1, last]
  return [1, '...', current - 1, current, current + 1, '...', last]
})

const fetchBarcodes = async () => {
  pending.value = true
  try {
    const params = {
      page: page.value,
      per_page: 30,
      status: currentStatus.value
    }
    
    if (searchQuery.value) {
      params.search = searchQuery.value
    }

    const res = await barcodePrint('/vendor/barcodes/print', params)
    
    // Set default print quantity to 1 for each loaded item if not set
    if (res?.data?.data) {
      res.data.data.forEach(item => {
        if (item.print_qty === undefined) {
             item.print_qty = item.is_printed ? 0 : 1;
        }
      })
    }
    data.value = res

  } catch (error) {
    console.error('Failed to fetch barcodes', error)
  } finally {
    pending.value = false
  }
}

watch([page, currentStatus], () => {
  fetchBarcodes()
})

const handlePrint = async () => {
    const itemsToPrint = data.value.data.data.filter(item => (item.print_qty || 0) > 0)
    if (itemsToPrint.length === 0) return

    window.print()

    // Mark as printed in the backend
    try {
        await createItem('/vendor/barcodes/mark-as-printed', {
            ids: itemsToPrint.map(b => b.id)
        }, null, false)
        fetchBarcodes() // Refresh to show printed badges
        toast.success('Successfully marked barcodes as printed.')
    } catch (error) {
        console.error('Failed to mark barcodes as printed:', error)
    }
}

const handleSearch = () => {
  page.value = 1
  fetchBarcodes()
}

const clearSearch = () => {
  searchQuery.value = ''
  page.value = 1
  fetchBarcodes()
}

const generateMissing = async () => {
  generating.value = true
  try {
    const res = await createItem('/vendor/barcodes/generate', {}, null, false)
    if (res?.status === 'success') {
      fetchBarcodes()
    }
  } catch (error) {
    console.error(error)
  } finally {
    generating.value = false
  }
}

const printLabels = () => {
  window.print()
}

watch(page, () => {
  fetchBarcodes()
})

onMounted(() => {
  fetchBarcodes()
})
</script>

<style scoped>
/* Hide up/down arrows on number inputs */
.hide-arrows::-webkit-outer-spin-button,
.hide-arrows::-webkit-inner-spin-button {
  -webkit-appearance: none;
  appearance: none;
  margin: 0;
}
.hide-arrows {
  appearance: textfield;
  -moz-appearance: textfield;
}

.print-only {
  display: none;
}

@media print {
  @page {
    margin: 0;
    size: auto;
  }
  
  body * {
    visibility: hidden;
  }
  
  .no-print {
    display: none !important;
  }

  .print-only, .print-only * {
    visibility: visible;
  }
  
  .print-only {
    display: block;
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }

  /* Typical Avery 3x10 setup approximations */
  .label-grid {
    display: flex;
    flex-wrap: wrap;
    align-content: flex-start;
    padding: 10mm 5mm;
    gap: 2mm 5mm;
  }
  
  .label-item {
    width: calc(33.333% - 5mm); /* 3 columns with gap accounted for */
    height: 38mm; /* typical height */
    padding: 3mm;
    text-align: center;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    page-break-inside: avoid;
    break-inside: avoid;
    outline: 1px solid #e1e8f0; /* Very faint outline for cutting references */
    outline-offset: -1px;
    background: #fff;
  }
  
  .label-content {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }

  .label-title {
    font-family: 'Inter', system-ui, sans-serif;
    font-size: 8pt;
    font-weight: 800; /* Extra bold for print readability */
    color: #000;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    width: 100%;
    margin-bottom: 2mm;
    line-height: 1;
  }
  
  .label-barcode {
    height: 14mm;
    width: auto;
    max-width: 100%;
    image-rendering: pixelated; /* Keeps the barcode lines crisp */
    margin: 0 auto;
    display: block;
  }
  
  .label-number {
    font-family: 'Inter', system-ui, sans-serif;
    font-size: 8pt;
    font-weight: 500;
    letter-spacing: 1px;
    margin-top: 1.5mm;
    color: #000;
    line-height: 1;
  }
}
</style>
