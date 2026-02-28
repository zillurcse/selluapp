<template>
  <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative group overflow-hidden">
      <div class="relative z-10">
        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Inventory Value</div>
        <div class="text-3xl font-black text-gray-900 mb-2">৳{{ kpi?.inventory_value?.toLocaleString(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 0 }) || '0' }}</div>
        <div class="text-xs font-bold text-emerald-500 flex items-center">
          <ArrowUpRight class="w-3 h-3 mr-1" /> +5% this month
        </div>
      </div>
      <Boxes class="absolute -right-4 -bottom-4 w-24 h-24 text-gray-900 opacity-[0.03] group-hover:scale-110 transition-all" />
    </div>

    <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative group overflow-hidden">
      <div class="relative z-10">
        <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Total Items</div>
        <div class="text-3xl font-black text-indigo-600 mb-2">{{ kpi?.total_items || 0 }}</div>
        <div class="text-xs font-bold text-gray-400">Total in-stock units</div>
      </div>
      <BoxSelect class="absolute -right-4 -bottom-4 w-24 h-24 text-gray-900 opacity-[0.03] group-hover:scale-110 transition-all" />
    </div>

    <div class="bg-white rounded-[2rem] p-8 border shadow-sm relative group overflow-hidden border-amber-100 bg-amber-50/10">
      <div class="relative z-10">
        <div class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-4">Low Stock Alerts</div>
        <div class="text-3xl font-black text-amber-600 mb-2 text-glow">{{ kpi?.low_stock_items || 0 }}</div>
        <div class="text-xs font-bold text-amber-500 underline cursor-pointer">View Items</div>
      </div>
      <AlertTriangle class="absolute -right-4 -bottom-4 w-24 h-24 text-amber-600 opacity-[0.05] group-hover:scale-110 transition-all" />
    </div>

    <div class="bg-white rounded-[2rem] p-8 border shadow-sm relative group overflow-hidden border-rose-100 bg-rose-50/10">
      <div class="relative z-10">
        <div class="text-[10px] font-black text-rose-600 uppercase tracking-widest mb-4">Out of Stock</div>
        <div class="text-3xl font-black text-rose-600 mb-2">{{ kpi?.out_of_stock_items || 0 }}</div>
        <div class="text-xs font-bold text-rose-500">Urgent restock needed</div>
      </div>
      <XCircle class="absolute -right-4 -bottom-4 w-24 h-24 text-rose-600 opacity-[0.05] group-hover:scale-110 transition-all" />
    </div>
  </div>
</template>

<script setup>
import { 
  ArrowUpRight,
  Boxes,
  BoxSelect,
  AlertTriangle,
  XCircle 
} from 'lucide-vue-next'

defineProps({
  kpi: {
    type: Object,
    default: () => ({})
  }
})
</script>
