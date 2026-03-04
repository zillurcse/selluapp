<template>
  <div class="p-6 md:p-8 min-h-screen">
    <!-- Barcode Scanner Listener -->
    <BarcodeScanner @scan="handleScan" />

    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
          <div class="flex items-center gap-2">
            <button @click="$router.back()" class="p-2 -ml-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 dark:hover:text-gray-300 rounded-lg transition-colors">
              <ChevronLeft class="w-5 h-5" />
            </button>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Stock Audit</h1>
          </div>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 ml-9">Scan items to cross-check physical inventory with system quantities.</p>
        </div>

        <div class="flex items-center gap-3">
          <button v-if="scannedItems.length > 0" @click="resetAudit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400 text-sm font-semibold rounded-xl hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
            <RotateCcw class="w-4 h-4" />
            Reset Audit
          </button>
          <button @click="submitAudit" :disabled="scannedItems.length === 0 || auditing" class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
            <ClipboardCheck class="w-4 h-4" />
            {{ auditing ? 'Verifying...' : 'Verify Inventory' }}
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Sidebar: Scan Area -->
        <div class="lg:col-span-1 flex flex-col gap-6">
          <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 sm:p-8 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
              <div class="p-2 bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-lg">
                <Keyboard class="w-5 h-5" />
              </div>
              <h2 class="text-base font-semibold text-gray-900 dark:text-white">Manual Entry</h2>
            </div>
            
            <form @submit.prevent="manualSubmit" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5" for="barcode">Barcode Number</label>
                <input id="barcode" class="block w-full rounded-xl border-0 py-3.5 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 text-base dark:bg-gray-800 dark:text-white dark:ring-gray-700" type="text" v-model="manualBarcode" placeholder="Scan or type..." autofocus autocomplete="off" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5" for="qty">Quantity</label>
                <input id="qty" class="block w-full rounded-xl border-0 py-3.5 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 text-base dark:bg-gray-800 dark:text-white dark:ring-gray-700" type="number" min="1" v-model="manualQty" />
              </div>
              <button type="submit" class="mt-2 w-full flex items-center justify-center gap-2 rounded-xl bg-gray-900 dark:bg-gray-800 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 dark:hover:bg-gray-700 transition-colors">
                <Plus class="w-4 h-4" />
                Add to Audit
              </button>
            </form>
          </div>
          
          <div class="bg-indigo-600 rounded-2xl p-6 text-white shadow-sm">
             <div class="flex items-center gap-3 mb-3">
                 <div class="p-2 bg-white/10 rounded-lg">
                   <ScanLine class="w-5 h-5 text-indigo-100" />
                 </div>
                 <h3 class="font-semibold text-lg text-white">Scanner Ready</h3>
             </div>
             <p class="text-indigo-100 text-sm leading-relaxed">
               You can use a standard USB or Bluetooth barcode scanner. Just point and shoot at any time on this page. No need to click any buttons.
             </p>
          </div>
        </div>
        
        <!-- Main: Scanned Items List & Results -->
        <div class="lg:col-span-3">
          <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm flex flex-col h-full overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/20">
              <div class="flex items-center gap-3">
                <div class="p-2 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                  <PackageSearch class="w-5 h-5" />
                </div>
                <div>
                  <h2 class="text-base font-semibold text-gray-900 dark:text-white leading-tight">Scanned Items</h2>
                  <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ scannedItems.length }} {{ scannedItems.length === 1 ? 'Item' : 'Items' }} Detected</p>
                </div>
              </div>
              
              <div v-if="auditResults" class="px-3 py-1.5 bg-green-50 dark:bg-green-500/10 text-green-700 dark:text-green-400 rounded-md text-xs font-medium ring-1 ring-inset ring-green-600/20 dark:ring-green-500/20 flex items-center gap-1.5">
                <CheckCircle2 class="w-4 h-4" />
                Audit Complete
              </div>
            </div>
            
            <div v-if="scannedItems.length === 0" class="flex-1 flex flex-col items-center justify-center py-24 px-6 text-center">
               <div class="mx-auto w-16 h-16 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-full flex items-center justify-center mb-4">
                  <Box class="w-8 h-8 text-gray-400" />
               </div>
               <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No items scanned yet</h3>
               <p class="text-gray-500 dark:text-gray-400 text-sm max-w-sm">Use your barcode scanner or add items manually using the form to begin the audit process.</p>
            </div>
            
            <div v-else class="flex-1 overflow-x-auto custom-scrollbar bg-white dark:bg-gray-900">
              <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                  <tr>
                    <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Barcode / Item</th>
                    <th scope="col" class="px-3 py-3.5 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Scanned QTY</th>
                    <th v-if="auditResults" scope="col" class="px-3 py-3.5 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">System QTY</th>
                    <th v-if="auditResults" scope="col" class="px-3 py-3.5 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Variance</th>
                    <th scope="col" class="py-3.5 pl-3 pr-6 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Action</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                  <tr v-for="(item, index) in scannedItems" :key="index" class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors group/row">
                    <td class="whitespace-nowrap py-4 pl-6 pr-3">
                      <div class="flex items-center gap-3">
                        <div class="p-1.5 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500 border border-gray-200 dark:border-gray-700">
                          <Tag class="w-4 h-4" />
                        </div>
                        <div class="flex flex-col">
                          <span class="font-medium text-sm text-gray-900 dark:text-white">{{ item.barcode }}</span>
                          <span class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate max-w-[200px]" v-if="auditResults && getResult(item.barcode)">{{ getResult(item.barcode).name }}</span>
                        </div>
                      </div>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-center">
                      <input type="number" v-model="item.qty" min="1" class="w-20 rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 dark:bg-gray-800 dark:text-white dark:ring-gray-700 mx-auto text-center font-medium" @change="() => { if(auditResults) auditResults = null }" />
                    </td>
                    <td v-if="auditResults" class="whitespace-nowrap px-3 py-4 text-center">
                      <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-md bg-gray-100 dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ getResult(item.barcode)?.expected ?? '-' }}
                      </span>
                    </td>
                    <td v-if="auditResults" class="whitespace-nowrap px-3 py-4 text-center">
                      <div v-if="getResult(item.barcode)" class="flex justify-center">
                        <span v-if="getResult(item.barcode).status === 'match'" class="inline-flex items-center gap-1 rounded-md bg-green-50 dark:bg-green-500/10 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-400 ring-1 ring-inset ring-green-600/20 dark:ring-green-500/20">
                          <Check class="w-3 h-3" /> Match
                        </span>
                        <span v-else-if="getResult(item.barcode).status === 'surplus'" class="inline-flex items-center gap-1 rounded-md bg-blue-50 dark:bg-blue-500/10 px-2 py-1 text-xs font-medium text-blue-700 dark:text-blue-400 ring-1 ring-inset ring-blue-700/10 dark:ring-blue-500/20">
                          <ArrowUp class="w-3 h-3" /> +{{getResult(item.barcode).diff}} Surplus
                        </span>
                        <span v-else-if="getResult(item.barcode).status === 'missing'" class="inline-flex items-center gap-1 rounded-md bg-yellow-50 dark:bg-yellow-500/10 px-2 py-1 text-xs font-medium text-yellow-800 dark:text-yellow-500 ring-1 ring-inset ring-yellow-600/20 dark:ring-yellow-500/20" title="You scanned less than expected">
                          <ArrowDown class="w-3 h-3" /> {{getResult(item.barcode).diff}} Missing
                        </span>
                        <span v-else class="inline-flex items-center gap-1 rounded-md bg-red-50 dark:bg-red-500/10 px-2 py-1 text-xs font-medium text-red-700 dark:text-red-400 ring-1 ring-inset ring-red-600/10 dark:ring-red-500/20">
                          <AlertCircle class="w-3 h-3" /> Unknown
                        </span>
                      </div>
                      <span v-else class="text-gray-400">-</span>
                    </td>
                    <td class="whitespace-nowrap py-4 pl-3 pr-6 text-right">
                      <div class="flex justify-end opacity-0 group-hover/row:opacity-100 transition-opacity">
                        <button @click="removeItem(index)" class="p-1.5 rounded-md text-red-500 hover:text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10 transition-colors">
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
