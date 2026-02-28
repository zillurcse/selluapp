<template>
  <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden text-slate-900">
    <div class="p-10 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-6">
      <h3 class="text-2xl font-black text-gray-900 tracking-tight">Stock Analysis Matrix</h3>
      
      <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">
        <div class="relative w-full md:w-72">
          <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
          <input 
            type="text" 
            :value="search"
            @input="$emit('update:search', $event.target.value)"
            placeholder="Search product name or ID..."
            class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-100 rounded-xl text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
          />
        </div>
        <div class="flex items-center text-xs font-black text-gray-400 gap-2">
           <span class="mr-2">FILTERS:</span>
           <button 
            @click="$emit('update:filter', 'all')"
            :class="filter === 'all' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'bg-gray-50 text-gray-400 hover:bg-gray-100'"
            class="px-4 py-2 rounded-xl transition-all duration-300"
           >All Items</button>
           <button 
            @click="$emit('update:filter', 'low_stock')"
            :class="filter === 'low_stock' ? 'bg-amber-500 text-white shadow-lg shadow-amber-200' : 'bg-gray-50 text-gray-400 hover:bg-gray-100'"
            class="px-4 py-2 rounded-xl transition-all duration-300"
           >Low Stock</button>
           <button 
            @click="$emit('update:filter', 'out_of_stock')"
            :class="filter === 'out_of_stock' ? 'bg-rose-500 text-white shadow-lg shadow-rose-200' : 'bg-gray-50 text-gray-400 hover:bg-gray-100'"
            class="px-4 py-2 rounded-xl transition-all duration-300"
           >Out of Stock</button>
        </div>
      </div>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left">
        <thead class="bg-gray-50/50">
          <tr>
            <th @click="toggleSort('name')" class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest cursor-pointer hover:text-gray-600 transition-colors group">
              <div class="flex items-center gap-2">
                Product
                <component :is="getSortIcon('name')" class="w-3 h-3 transition-opacity group-hover:opacity-100" :class="sortBy === 'name' ? 'text-indigo-600' : 'opacity-0'" />
              </div>
            </th>
            <th @click="toggleSort('stock_qty')" class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest cursor-pointer hover:text-gray-600 transition-colors group">
              <div class="flex items-center gap-2">
                Current Stock
                <component :is="getSortIcon('stock_qty')" class="w-3 h-3 transition-opacity group-hover:opacity-100" :class="sortBy === 'stock_qty' ? 'text-indigo-600' : 'opacity-0'" />
              </div>
            </th>
            <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Incoming</th>
            <th class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest">Status</th>
            <th @click="toggleSort('valuation')" class="px-10 py-6 text-xs font-black text-gray-400 uppercase tracking-widest cursor-pointer hover:text-gray-600 transition-colors group">
              <div class="flex items-center gap-2">
                Valuation
                <component :is="getSortIcon('valuation')" class="w-3 h-3 transition-opacity group-hover:opacity-100" :class="sortBy === 'valuation' ? 'text-indigo-600' : 'opacity-0'" />
              </div>
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
          <tr v-for="item in products?.data || []" :key="item.id" class="hover:bg-gray-50/50 transition-colors group">
            <td class="px-10 py-6">
               <div class="flex items-center gap-3">
                  <img :src="item.thumbnail" :alt="item.name" class="w-10 h-10 rounded-lg object-cover bg-gray-100 group-hover:scale-110 transition-transform duration-300" v-if="item.thumbnail" />
                  <div v-else class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400 group-hover:scale-110 transition-transform duration-300">
                    <Boxes class="w-5 h-5" />
                  </div>
                  <div>
                     <div class="font-bold text-gray-900">{{ item.name }}</div>
                     <div class="text-[10px] font-black text-gray-400 uppercase">ID: {{ item.id }}</div>
                  </div>
               </div>
            </td>
            <td class="px-10 py-6">
               <div class="flex items-center gap-2">
                  <span class="font-black" :class="item.stock_qty <= 5 ? (item.stock_qty === 0 ? 'text-rose-500' : 'text-amber-500') : 'text-gray-900'">{{ item.stock_qty }} units</span>
                  <div v-if="item.stock_qty <= 5 && item.stock_qty > 0" class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></div>
                  <div v-if="item.stock_qty === 0" class="w-2 h-2 rounded-full bg-rose-500 shadow-lg shadow-rose-200"></div>
               </div>
            </td>
            <td class="px-10 py-6 font-medium text-indigo-500">-</td>
            <td class="px-10 py-6">
               <div class="text-xs font-bold px-3 py-1 rounded-full w-fit" :class="item.stock_qty === 0 ? 'bg-rose-50 text-rose-600 border border-rose-100' : (item.stock_qty <= 5 ? 'bg-amber-50 text-amber-600 border border-amber-100' : 'bg-emerald-50 text-emerald-600 border border-emerald-100')">
                {{ item.stock_qty === 0 ? 'Out of Stock' : (item.stock_qty <= 5 ? 'Low Stock' : 'In Stock') }}
               </div>
            </td>
            <td class="px-10 py-6 font-black text-gray-900">৳{{ Number(item.sale_price * item.stock_qty).toLocaleString(undefined, { maximumFractionDigits: 0 }) }}</td>
          </tr>
          <tr v-if="!products?.data?.length">
            <td colspan="5" class="px-10 py-6 text-center text-gray-500 font-medium">
              <div class="flex flex-col items-center gap-2 p-10">
                <Boxes class="w-12 h-12 text-gray-200" />
                <p>No products found matching your criteria</p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="products?.last_page > 1" class="p-10 bg-gray-50/30 border-t border-gray-50 flex items-center justify-between">
      <div class="text-xs font-bold text-gray-400">
        Showing {{ products.from }}-{{ products.to }} of <span class="text-gray-900">{{ products.total }}</span> products
      </div>
      <div class="flex items-center gap-2">
        <button 
          @click="$emit('update:page', products.current_page - 1)"
          :disabled="products.current_page === 1"
          class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black bg-white border border-gray-100 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-indigo-50 hover:border-indigo-100 transition-all shadow-sm"
        >
          <ChevronLeft class="w-4 h-4" /> PREV
        </button>
        <div class="flex items-center gap-1 mx-4">
          <span class="text-xs font-black text-gray-900 bg-white border border-gray-100 w-8 h-8 flex items-center justify-center rounded-lg shadow-sm">{{ products.current_page }}</span>
          <span class="text-xs font-bold text-gray-300">OF</span>
          <span class="text-xs font-black text-gray-400">{{ products.last_page }}</span>
        </div>
        <button 
          @click="$emit('update:page', products.current_page + 1)"
          :disabled="products.current_page === products.last_page"
          class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-black bg-white border border-gray-100 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-indigo-50 hover:border-indigo-100 transition-all shadow-sm"
        >
          NEXT <ChevronRight class="w-4 h-4" />
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Boxes, Search, ChevronLeft, ChevronRight, ArrowUpDown, ArrowUp, ArrowDown } from 'lucide-vue-next'

const props = defineProps({
  products: {
    type: Object,
    default: () => ({ data: [] })
  },
  search: String,
  page: Number,
  filter: {
    type: String,
    default: 'all'
  },
  sortBy: String,
  sortOrder: String
})

const emit = defineEmits(['update:search', 'update:page', 'update:filter', 'update:sort'])

const toggleSort = (column) => {
  let order = 'desc'
  if (props.sortBy === column) {
    order = props.sortOrder === 'desc' ? 'asc' : 'desc'
  }
  emit('update:sort', { column, order })
}

const getSortIcon = (column) => {
  if (props.sortBy !== column) return ArrowUpDown
  return props.sortOrder === 'asc' ? ArrowUp : ArrowDown
}
</script>

