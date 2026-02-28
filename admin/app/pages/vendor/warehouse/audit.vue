<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 transition-colors duration-300">
    <!-- Barcode Scanner Listener -->
    <BarcodeScanner @scan="handleScan" />

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div class="flex items-center gap-4">
        <button @click="$router.back()" class="p-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm active:scale-95 group">
          <ChevronLeft class="w-5 h-5 text-slate-600 dark:text-slate-300 group-hover:-translate-x-0.5 transition-transform" />
        </button>
        <div>
          <h1 class="text-2xl font-black text-slate-900 dark:text-white leading-tight tracking-tight">Stock Audit</h1>
          <p class="text-sm text-slate-500 dark:text-slate-400 font-semibold opacity-80">Scan items to cross-check physical inventory with system quantities.</p>
        </div>
      </div>
      <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-3">
        <button v-if="scannedItems.length > 0" @click="resetAudit" class="flex items-center gap-2 px-6 py-3 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 font-black rounded-2xl transition-all shadow-sm hover:shadow-md hover:bg-red-100 dark:hover:bg-red-500/20 border border-red-100 dark:border-red-500/20 active:scale-95 group">
          <RotateCcw class="w-4 h-4 group-hover:-rotate-180 transition-transform duration-500" />
          Reset Audit
        </button>
        <button @click="submitAudit" :disabled="scannedItems.length === 0 || auditing" class="flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl transition-all shadow-lg shadow-indigo-600/20 active:scale-95 group disabled:opacity-50 disabled:cursor-not-allowed">
          <ClipboardCheck class="w-5 h-5 group-hover:scale-110 transition-transform duration-300" />
          {{ auditing ? 'Verifying...' : 'Verify Inventory' }}
        </button>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <!-- Scan Area -->
      <div class="lg:col-span-1 flex flex-col gap-6">
        <div class="bg-white dark:bg-slate-900 shadow-[0_20px_60px_-15px_rgba(0,0,0,0.06)] dark:shadow-none rounded-[24px] border border-slate-200/60 dark:border-slate-800 p-8 transition-colors duration-300">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
              <Keyboard class="w-5 h-5" />
            </div>
            <h2 class="text-lg font-black text-slate-900 dark:text-white">Manual Entry</h2>
          </div>
          <form @submit.prevent="manualSubmit" class="flex flex-col gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2" for="barcode">Barcode Number</label>
              <input id="barcode" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/50" type="text" v-model="manualBarcode" placeholder="Scan or type barcode" autofocus autocomplete="off" />
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2" for="qty">Quantity</label>
              <input id="qty" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm font-semibold text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/50" type="number" min="1" v-model="manualQty" />
            </div>
            <button type="submit" class="w-full flex justify-center items-center gap-2 mt-2 px-6 py-3.5 bg-slate-900 dark:bg-slate-800 text-white font-black rounded-xl transition-all hover:bg-slate-800 dark:hover:bg-slate-700 active:scale-95 group">
              <Plus class="w-4 h-4" />
              Add to Audit
            </button>
          </form>
        </div>
        
        <div class="bg-gradient-to-br from-indigo-500 to-violet-600 rounded-[24px] p-8 text-white relative overflow-hidden shadow-xl shadow-indigo-500/20">
          <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
          <div class="absolute -left-6 -bottom-6 w-32 h-32 bg-black/10 rounded-full blur-2xl"></div>
          
          <div class="relative z-10">
            <div class="flex items-center gap-3 mb-4">
               <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-md">
                 <ScanLine class="w-5 h-5 text-white animate-pulse" />
               </div>
               <h3 class="font-black text-lg">Scanner Ready</h3>
            </div>
            <p class="text-indigo-100 text-sm font-medium leading-relaxed">
              You can use a standard USB or Bluetooth barcode scanner. Just point and shoot at any time on this page. No need to click any buttons.
            </p>
          </div>
        </div>
      </div>
      
      <!-- Scanned Items List & Results -->
      <div class="lg:col-span-2">
        <div class="bg-white dark:bg-slate-900 rounded-[24px] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.06)] dark:shadow-none border border-slate-200/60 dark:border-slate-800 flex flex-col h-full overflow-hidden transition-colors duration-300">
          <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400">
                <PackageSearch class="w-5 h-5" />
              </div>
              <div>
                <h2 class="text-lg font-black text-slate-900 dark:text-white leading-none">Scanned Items</h2>
                <div class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">{{ scannedItems.length }} {{ scannedItems.length === 1 ? 'Item' : 'Items' }} Detected</div>
              </div>
            </div>
            
            <div v-if="auditResults" class="px-4 py-1.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg text-xs font-black uppercase tracking-widest border border-emerald-100 dark:border-emerald-500/20 flex items-center gap-2">
              <CheckCircle2 class="w-4 h-4" />
              Audit Complete
            </div>
          </div>
          
          <div v-if="scannedItems.length === 0" class="flex-1 flex flex-col items-center justify-center p-12 text-center">
             <div class="w-24 h-24 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mb-6">
                <Box class="w-10 h-10 text-slate-300 dark:text-slate-600" />
             </div>
             <h3 class="text-xl font-black text-slate-800 dark:text-slate-200 mb-2">No items scanned yet</h3>
             <p class="text-slate-500 dark:text-slate-400 font-medium">Use your barcode scanner or add items manually to begin the audit.</p>
          </div>
          
          <div v-else class="flex-1 overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse min-w-[600px]">
              <thead>
                <tr class="bg-slate-50/50 dark:bg-slate-800/30 border-b border-slate-100 dark:border-slate-800">
                  <th class="py-4 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-slate-400">Barcode / Item</th>
                  <th class="py-4 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 text-center">Scanned QTY</th>
                  <th v-if="auditResults" class="py-4 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 text-center">System QTY</th>
                  <th v-if="auditResults" class="py-4 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 text-center">Variance</th>
                  <th class="py-4 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-slate-400 text-right">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in scannedItems" :key="index" class="border-b border-slate-50 dark:border-slate-800/50 hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group/row">
                  <td class="px-8 py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                        <Tag class="w-4 h-4" />
                      </div>
                      <div>
                        <div class="font-black text-sm text-slate-900 dark:text-white">{{ item.barcode }}</div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5 line-clamp-1" v-if="auditResults && getResult(item.barcode)">{{ getResult(item.barcode).name }}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-8 py-4 text-center">
                    <input type="number" v-model="item.qty" min="1" class="w-16 bg-slate-100 dark:bg-slate-800 border-none rounded-lg text-center py-1.5 text-sm font-black text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500/50 mx-auto" @change="() => { if(auditResults) auditResults = null }" />
                  </td>
                  <td v-if="auditResults" class="px-8 py-4 text-center">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 text-sm font-black text-slate-600 dark:text-slate-300">
                      {{ getResult(item.barcode)?.expected ?? '-' }}
                    </span>
                  </td>
                  <td v-if="auditResults" class="px-8 py-4 text-center">
                    <div v-if="getResult(item.barcode)" class="flex justify-center">
                      <span v-if="getResult(item.barcode).status === 'match'" class="px-3 py-1 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-full text-[10px] font-black uppercase tracking-wider border border-emerald-100 dark:border-emerald-500/20 flex items-center gap-1.5">
                        <Check class="w-3 h-3" /> Match
                      </span>
                      <span v-else-if="getResult(item.barcode).status === 'surplus'" class="px-3 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full text-[10px] font-black uppercase tracking-wider border border-blue-100 dark:border-blue-500/20 flex items-center gap-1.5">
                        <ArrowUp class="w-3 h-3" /> +{{getResult(item.barcode).diff}} Surplus
                      </span>
                      <span v-else-if="getResult(item.barcode).status === 'missing'" class="px-3 py-1 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-full text-[10px] font-black uppercase tracking-wider border border-amber-100 dark:border-amber-500/20 flex items-center gap-1.5" title="You scanned less than expected">
                        <ArrowDown class="w-3 h-3" /> {{getResult(item.barcode).diff}} Missing
                      </span>
                      <span v-else class="px-3 py-1 bg-rose-50 dark:bg-rose-900/30 text-rose-500 dark:text-rose-400 rounded-full text-[10px] font-black uppercase tracking-wider border border-rose-100 dark:border-rose-500/20 flex items-center gap-1.5">
                        <AlertCircle class="w-3 h-3" /> Unknown
                      </span>
                    </div>
                    <span v-else class="text-slate-400">-</span>
                  </td>
                  <td class="px-8 py-4 text-right">
                    <div class="flex justify-end opacity-0 group-hover/row:opacity-100 transition-opacity">
                      <button @click="removeItem(index)" class="p-2 bg-rose-50 dark:bg-rose-900/30 text-rose-500 dark:text-rose-400 hover:bg-rose-500 dark:hover:bg-rose-500 hover:text-white rounded-lg transition-all shadow-sm border border-rose-100 dark:border-rose-500/20">
                        <Trash2 class="w-4 h-4" />
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import BarcodeScanner from '~/components/BarcodeScanner.vue'
import { 
  ChevronLeft, 
  RotateCcw, 
  ClipboardCheck, 
  Keyboard, 
  Plus, 
  ScanLine, 
  PackageSearch,
  CheckCircle2,
  Box,
  Tag,
  Trash2,
  Check,
  ArrowUp,
  ArrowDown,
  AlertCircle
} from 'lucide-vue-next'

definePageMeta({ 
  layout: 'vendor',
  middleware: 'auth',
  permissions: 'barcodes.audit'
})

const { createItem } = useCrud()

const manualBarcode = ref('')
const manualQty = ref(1)

// Array of { barcode, qty }
const scannedItems = ref([])
const auditResults = ref(null)
const auditing = ref(false)

const handleScan = (barcode) => {
  addOrUpdateScan(barcode, 1)
}

const manualSubmit = () => {
  if (!manualBarcode.value) return
  addOrUpdateScan(manualBarcode.value, manualQty.value)
  manualBarcode.value = ''
  manualQty.value = 1
  document.getElementById('barcode').focus()
}

const addOrUpdateScan = (barcode, qty) => {
  // Clear any previous results since list is changing
  if (auditResults.value) auditResults.value = null

  const existing = scannedItems.value.find(item => item.barcode === barcode)
  if (existing) {
    existing.qty += Number(qty)
  } else {
    scannedItems.value.unshift({ barcode, qty: Number(qty) })
  }
}

const removeItem = (index) => {
  scannedItems.value.splice(index, 1)
  if (auditResults.value) auditResults.value = null
}

const resetAudit = () => {
  scannedItems.value = []
  auditResults.value = null
}

const submitAudit = async () => {
  if (scannedItems.value.length === 0) return
  
  auditing.value = true
  auditResults.value = null

  try {
    const res = await createItem('/vendor/barcodes/audit', {
      scans: scannedItems.value
    }, null, false)

    if (res?.status === 'success') {
      auditResults.value = res.data
    }
  } catch (error) {
    console.error(error)
  } finally {
    auditing.value = false
  }
}

const getResult = (barcode) => {
  if (!auditResults.value) return null
  return auditResults.value.find(r => r.barcode === barcode) || null
}
</script>

<style scoped>
/* Custom Scrollbar matched with index.vue */
.custom-scrollbar::-webkit-scrollbar {
  height: 8px;
  width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
  border: 2px solid transparent;
  background-clip: padding-box;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #334155;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #94a3b8;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #475569;
}
</style>
