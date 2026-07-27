<template>
  <div class="p-6 lg:p-8 bg-slate-50 dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
      <div class="flex items-center gap-4">
        <button
          @click="$router.back()"
          class="p-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm group"
        >
          <ChevronLeft class="w-5 h-5 text-slate-500 dark:text-slate-400 group-hover:-translate-x-0.5 transition-transform" />
        </button>
        <div>
          <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Product Barcodes</h1>
          <p class="text-sm text-slate-500 dark:text-slate-400">Generate and print barcode labels for your products.</p>
        </div>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <button
          @click="generateMissing"
          :disabled="generating"
          class="flex items-center justify-center gap-2 px-4 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-200 text-sm font-semibold rounded-lg transition-all shadow-sm hover:bg-slate-50 dark:hover:bg-slate-800 active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <RefreshCw class="w-4 h-4" :class="generating ? 'animate-spin' : ''" />
          <span>{{ generating ? 'Generating...' : 'Generate Missing' }}</span>
        </button>
        <button
          @click="handlePrint"
          :disabled="!hasItemsToPrint"
          class="flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-all shadow-sm active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <Printer class="w-4 h-4" />
          Print Labels
          <span
            v-if="totalPrintQty > 0"
            class="ml-0.5 px-1.5 py-0.5 bg-white/20 rounded-md text-xs font-bold"
          >
            {{ totalPrintQty }}
          </span>
        </button>
      </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex flex-wrap items-center gap-2 mb-6">
      <button
        v-for="s in statusOptions"
        :key="s.value"
        @click="currentStatus = s.value"
        :class="[
          'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border',
          currentStatus === s.value
            ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 border-indigo-200 dark:border-indigo-500/30'
            : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800'
        ]"
      >
        {{ s.label }}
      </button>
    </div>

    <!-- Search -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 mb-6 p-4 transition-colors duration-300">
      <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="relative flex-1">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by product name, SKU, or barcode..."
            class="w-full h-10 pl-10 pr-10 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-lg focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400 text-sm font-medium text-slate-700 dark:text-slate-200"
            @input="debouncedSearch"
            @keyup.enter="handleSearch"
          />
          <button
            v-if="searchQuery"
            @click="clearSearch"
            class="absolute right-3 top-1/2 -translate-y-1/2 p-0.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
          >
            <X class="w-4 h-4" />
          </button>
        </div>
        <div v-if="!pending && data?.data?.data?.length" class="flex items-center gap-2 shrink-0">
          <button
            @click="setAllQty(1)"
            class="h-10 px-3 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
          >
            Set all to 1
          </button>
          <button
            @click="setAllQty(0)"
            class="h-10 px-3 text-xs font-semibold rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
          >
            Clear all
          </button>
        </div>
      </div>
    </div>

    <!-- Barcode Grid -->
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden transition-colors duration-300">
      <div class="flex items-center justify-between px-6 py-3.5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/20">
        <p class="text-sm text-slate-500 dark:text-slate-400">
          <span class="font-semibold text-slate-900 dark:text-white">{{ pagination.total }}</span>
          {{ pagination.total === 1 ? 'barcode' : 'barcodes' }}
          <span v-if="searchQuery" class="text-slate-400"> · filtered</span>
        </p>
        <p v-if="totalPrintQty > 0" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">
          {{ totalPrintQty }} label{{ totalPrintQty === 1 ? '' : 's' }} selected to print
        </p>
      </div>

      <div class="p-6">
        <!-- Loading -->
        <div v-if="pending" class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">
          <div
            v-for="n in 10"
            :key="`skeleton-${n}`"
            class="border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20 p-4 rounded-xl animate-pulse"
          >
            <div class="h-3.5 w-3/4 bg-slate-200 dark:bg-slate-700 rounded mx-auto mb-2" />
            <div class="h-2.5 w-1/2 bg-slate-100 dark:bg-slate-800 rounded mx-auto mb-4" />
            <div class="h-14 bg-white dark:bg-slate-900 rounded-lg border border-slate-100 dark:border-slate-800 mb-3" />
            <div class="h-2.5 w-2/3 bg-slate-100 dark:bg-slate-800 rounded mx-auto mb-4" />
            <div class="h-9 bg-slate-200 dark:bg-slate-700 rounded-lg" />
          </div>
        </div>

        <!-- Empty State -->
        <div
          v-else-if="!data?.data?.data || data.data.data.length === 0"
          class="text-center py-16 flex flex-col items-center justify-center"
        >
          <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
            <Tag class="w-7 h-7 text-slate-400 dark:text-slate-500" />
          </div>
          <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-1">
            {{ searchQuery ? 'No matching barcodes' : 'No barcodes found' }}
          </h3>
          <p class="text-sm text-slate-500 dark:text-slate-400 mb-6 max-w-sm">
            {{
              searchQuery
                ? 'Try a different search term or clear the filter.'
                : 'Generate barcodes for your products before printing labels.'
            }}
          </p>
          <button
            v-if="searchQuery"
            @click="clearSearch"
            class="px-5 py-2.5 text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline"
          >
            Clear search
          </button>
          <button
            v-else
            @click="generateMissing"
            :disabled="generating"
            class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-all shadow-sm active:scale-[0.98] disabled:opacity-50"
          >
            <RefreshCw class="w-4 h-4" :class="generating ? 'animate-spin' : ''" />
            Generate Now
          </button>
        </div>

        <!-- Barcode Cards -->
        <div v-else class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-4">
          <div
            v-for="item in data.data.data"
            :key="item.id"
            :class="[
              'border rounded-xl p-4 text-center flex flex-col items-center transition-all',
              (item.print_qty || 0) > 0
                ? 'border-indigo-200 dark:border-indigo-500/30 bg-indigo-50/40 dark:bg-indigo-500/5 shadow-sm'
                : 'border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/20 hover:bg-white dark:hover:bg-slate-800/40 hover:shadow-sm'
            ]"
          >
            <div
              class="text-xs font-semibold text-slate-900 dark:text-white mb-1 truncate w-full"
              :title="item.product.name"
            >
              {{ item.product.name }}
            </div>
            <div class="flex items-center justify-center gap-1.5 mb-3 min-h-[18px]">
              <span
                v-if="item.variant"
                class="text-[10px] font-medium text-slate-400 dark:text-slate-500 uppercase tracking-wide truncate max-w-[120px]"
              >
                {{ item.variant.sku }}
              </span>
              <span
                v-if="item.is_printed"
                class="px-1.5 py-0.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded text-[9px] font-semibold uppercase tracking-wide border border-emerald-100 dark:border-emerald-500/20"
              >
                Printed
              </span>
            </div>

            <div class="bg-white dark:bg-slate-900 p-2 rounded-lg border border-slate-100 dark:border-slate-800 w-full mb-2">
              <img
                :src="'data:image/png;base64,' + item.barcode_image"
                class="w-full h-12 object-contain"
                alt="Barcode"
              />
            </div>
            <div class="text-[10px] font-medium text-slate-500 dark:text-slate-400 tracking-wider mb-4 font-mono">
              {{ item.barcode }}
            </div>

            <!-- QTY Stepper -->
            <div class="mt-auto w-full flex items-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden">
              <button
                @click="adjustQty(item, -1)"
                class="px-2.5 py-2 text-slate-500 hover:text-indigo-600 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors disabled:opacity-40"
                :disabled="(item.print_qty || 0) <= 0"
              >
                <Minus class="w-3.5 h-3.5" />
              </button>
              <input
                type="number"
                v-model.number="item.print_qty"
                class="flex-1 min-w-0 bg-transparent border-none text-sm font-semibold text-center text-slate-900 dark:text-white focus:ring-0 py-2 hide-arrows"
                min="0"
              />
              <button
                @click="adjustQty(item, 1)"
                class="px-2.5 py-2 text-slate-500 hover:text-indigo-600 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
              >
                <Plus class="w-3.5 h-3.5" />
              </button>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="!pending && totalPages > 1" class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-8 pt-6 border-t border-slate-100 dark:border-slate-800">
          <p class="text-sm text-slate-500 dark:text-slate-400">
            Showing
            <span class="font-semibold text-slate-900 dark:text-white">{{ pagination.from }}</span>
            to
            <span class="font-semibold text-slate-900 dark:text-white">{{ pagination.to }}</span>
            of
            <span class="font-semibold text-slate-900 dark:text-white">{{ pagination.total }}</span>
          </p>
          <div class="flex items-center gap-1.5">
            <button
              @click="page--"
              :disabled="page <= 1"
              class="p-2 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all bg-white dark:bg-slate-900"
            >
              <ChevronLeft class="w-4 h-4" />
            </button>
            <button
              v-for="p in visiblePages"
              :key="p"
              @click="p !== '...' && (page = p)"
              :class="[
                'min-w-[2.25rem] h-9 px-2 flex items-center justify-center rounded-lg text-sm font-semibold transition-all',
                p === page
                  ? 'bg-indigo-600 text-white shadow-sm'
                  : p === '...'
                    ? 'text-slate-400 cursor-default'
                    : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900'
              ]"
            >
              {{ p }}
            </button>
            <button
              @click="page++"
              :disabled="page >= totalPages"
              class="p-2 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all bg-white dark:bg-slate-900"
            >
              <ChevronRight class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Print Layout -->
    <div class="print-only" id="printable-area">
      <div class="label-grid" v-if="data?.data?.data">
        <template v-for="item in data.data.data" :key="'p_' + item.id">
          <div
            class="label-item"
            v-for="n in (item.print_qty || 0)"
            :key="'l_' + item.id + '_' + n"
          >
            <div class="label-content">
              <div class="label-title">
                {{ item.product.name }}{{ item.variant ? ' (' + item.variant.sku + ')' : '' }}
              </div>
              <img
                :src="'data:image/png;base64,' + item.barcode_image"
                class="label-barcode"
                alt="barcode"
              />
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
import { ChevronLeft, ChevronRight, Printer, RefreshCw, Tag, Search, X, Plus, Minus } from 'lucide-vue-next'
import { debounce } from '~/utils'

const page = ref(1)
const searchQuery = ref('')
const currentStatus = ref('all')
const statusOptions = [
  { label: 'All', value: 'all' },
  { label: 'Not Printed', value: 'not_printed' },
  { label: 'Printed', value: 'printed' }
]
const generating = ref(false)
const pending = ref(true)
const data = ref(null)

const { barcodePrint, createItem } = useCrud()

const hasItemsToPrint = computed(() => {
  return data.value?.data?.data?.some(item => (item.print_qty || 0) > 0)
})

const totalPrintQty = computed(() => {
  if (!data.value?.data?.data) return 0
  return data.value.data.data.reduce((sum, item) => sum + (item.print_qty || 0), 0)
})

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

    if (res?.data?.data) {
      res.data.data.forEach(item => {
        if (item.print_qty === undefined) {
          item.print_qty = item.is_printed ? 0 : 1
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

  try {
    await createItem('/vendor/barcodes/mark-as-printed', {
      ids: itemsToPrint.map(b => b.id)
    }, null, false)
    fetchBarcodes()
    toast.success('Successfully marked barcodes as printed.')
  } catch (error) {
    console.error('Failed to mark barcodes as printed:', error)
  }
}

const handleSearch = () => {
  page.value = 1
  fetchBarcodes()
}

const debouncedSearch = debounce(handleSearch, 400)

const clearSearch = () => {
  searchQuery.value = ''
  page.value = 1
  fetchBarcodes()
}

const adjustQty = (item, delta) => {
  const current = item.print_qty || 0
  item.print_qty = Math.max(0, current + delta)
}

const setAllQty = (qty) => {
  if (!data.value?.data?.data) return
  data.value.data.data.forEach(item => {
    item.print_qty = qty
  })
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

onMounted(() => {
  fetchBarcodes()
})
</script>

<style scoped>
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

  .print-only,
  .print-only * {
    visibility: visible;
  }

  .print-only {
    display: block;
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }

  .label-grid {
    display: flex;
    flex-wrap: wrap;
    align-content: flex-start;
    padding: 10mm 5mm;
    gap: 2mm 5mm;
  }

  .label-item {
    width: calc(33.333% - 5mm);
    height: 38mm;
    padding: 3mm;
    text-align: center;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    page-break-inside: avoid;
    break-inside: avoid;
    outline: 1px solid #e1e8f0;
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
    font-weight: 800;
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
    image-rendering: pixelated;
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
