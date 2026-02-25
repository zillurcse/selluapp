<template>
  <div class="w-[60%] flex flex-col overflow-hidden bg-[#F8F9FB] dark:bg-slate-950 p-6 pt-4">
    <!-- Action Icons Row -->
    <div class="flex items-center gap-3 mb-4">
      <button @click="$emit('showRegisterDetails')" class="w-10 h-10 bg-blue-500 text-white rounded-lg flex items-center justify-center shadow-lg shadow-blue-100"><Monitor class="w-5 h-5" /></button>
      <button @click="$emit('showHoldList')" class="w-10 h-10 bg-pink-400 text-white rounded-lg flex items-center justify-center relative shadow-lg shadow-pink-100">
        <span v-if="holdOrdersCount" class="absolute -top-1 -right-1 w-5 h-5 bg-blue-500 border-2 border-white rounded-full text-[10px] font-bold flex items-center justify-center">{{ holdOrdersCount }}</span>
        <List class="w-5 h-5" />
      </button>
      <button @click="$emit('showRecentSales')" class="w-10 h-10 bg-blue-600 text-white rounded-lg flex items-center justify-center shadow-lg shadow-blue-200"><LayoutList class="w-5 h-5" /></button>
      <button class="w-10 h-10 bg-green-500 text-white rounded-lg flex items-center justify-center shadow-lg shadow-green-100"><ShoppingBag class="w-5 h-5" /></button>
      <button @click="$emit('showCalculator')" class="w-10 h-10 bg-indigo-400 text-white rounded-lg flex items-center justify-center shadow-lg shadow-indigo-100"><Calculator class="w-5 h-5" /></button>
    </div>

    <!-- Category Pills -->
    <div v-if="categories.length" class="flex items-center gap-2 mb-3 overflow-x-auto no-scrollbar pb-1">
      <button
        @click="selectCategory('all')"
        class="px-4 py-1.5 rounded-lg text-[12px] font-bold whitespace-nowrap transition-all"
        :class="selectedCategory === 'all' ? 'bg-blue-500 text-white' : 'bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700'"
      >
        All Categories
      </button>
      <button
        v-for="cat in categories"
        :key="cat.id"
        @click="selectCategory(cat.id)"
        class="px-4 py-1.5 rounded-lg text-[12px] font-bold whitespace-nowrap transition-all"
        :class="selectedCategory === cat.id ? 'bg-blue-500 text-white' : 'bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700'"
      >
        {{ cat.name }}
      </button>
    </div>

    <!-- Brand Pills -->
    <div v-if="brands.length" class="flex items-center gap-2 mb-4 overflow-x-auto no-scrollbar pb-1">
      <button
        @click="selectBrand('all')"
        class="px-4 py-1.5 rounded-lg text-[12px] font-bold whitespace-nowrap transition-all"
        :class="selectedBrand === 'all' ? 'bg-indigo-500 text-white' : 'bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700'"
      >
        All Brands
      </button>
      <button
        v-for="brand in brands"
        :key="brand.id"
        @click="selectBrand(brand.id)"
        class="px-4 py-1.5 rounded-lg text-[12px] font-bold whitespace-nowrap transition-all"
        :class="selectedBrand === brand.id ? 'bg-indigo-500 text-white' : 'bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700'"
      >
        {{ brand.name }}
      </button>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="isLoadingProducts && !filteredProducts.length" class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 flex-1 overflow-y-auto no-scrollbar pb-8">
      <div v-for="n in 8" :key="n" class="bg-white rounded-[1.5rem] border border-gray-100 overflow-hidden animate-pulse">
        <div class="aspect-square bg-gray-100"></div>
        <div class="p-4 pt-2 space-y-2">
          <div class="h-3 bg-gray-100 rounded w-3/4"></div>
          <div class="h-2 bg-gray-100 rounded w-1/2"></div>
        </div>
      </div>
    </div>

    <!-- Product Grid -->
    <div v-else-if="filteredProducts.length" class="flex-1 overflow-y-auto no-scrollbar pb-8" @scroll="onScroll">
      <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div
          v-for="product in filteredProducts"
          :key="product.id"
          @click="$emit('addToCart', product)"
          class="bg-white dark:bg-slate-900 rounded-[1.5rem] border border-gray-100 dark:border-slate-800 overflow-hidden hover:shadow-xl hover:shadow-gray-200/50 transition-all group cursor-pointer active:scale-95 relative"
        >
          <div class="absolute top-3 left-3 z-10 bg-indigo-500 text-white text-[10px] font-black px-2 py-1 rounded-md">৳ {{ product.price }}</div>
          <div class="absolute top-3 right-3 z-10 text-[10px] font-black px-2 py-1 rounded-md" :class="product.stock > 5 ? 'bg-green-100 text-green-700' : product.stock > 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'">
            {{ product.stock > 0 ? product.stock + ' left' : 'Out of stock' }}
          </div>

          <div class="aspect-square bg-white flex flex-col items-center justify-center p-4">
            <img v-if="product.image" :src="product.image" :alt="product.name" class="w-full h-full object-cover rounded-2xl group-hover:scale-105 transition-transform duration-300" />
            <div v-else class="w-full h-full border-2 border-dashed border-gray-100 rounded-2xl flex flex-col items-center justify-center opacity-50">
              <img src="https://cdn-icons-png.flaticon.com/512/10701/10701484.png" class="w-16 h-16 grayscale opacity-20 mb-2" />
              <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest">No Image</span>
            </div>
          </div>

          <div class="p-4 pt-0">
            <h3 class="text-[13px] font-black text-gray-700 dark:text-slate-200 mb-0.5 leading-tight line-clamp-2">{{ product.name }}</h3>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">{{ product.sku || '—' }}</p>
          </div>
        </div>
      </div>

      <!-- Loading More Spinner -->
      <div v-if="isLoadingMore" class="py-6 flex justify-center items-center gap-2 text-indigo-500 font-bold text-sm">
        <div class="w-5 h-5 border-2 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
        Loading more...
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="flex-1 flex items-center justify-center text-center text-gray-400">
      <div>
        <Search class="w-12 h-12 mb-3 opacity-30 mx-auto" />
        <div class="font-bold">No products found</div>
        <p class="text-sm mt-1">Try a different search or category</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Monitor, List, LayoutList, ShoppingBag, Calculator, Search } from 'lucide-vue-next'

const props = defineProps({
  categories: { type: Array, default: () => [] },
  brands: { type: Array, default: () => [] },
  filteredProducts: { type: Array, default: () => [] },
  isLoadingProducts: { type: Boolean, default: false },
  isLoadingMore: { type: Boolean, default: false },
  holdOrdersCount: { type: Number, default: 0 }
})

const selectedCategory = defineModel('selectedCategory', { type: [String, Number] })
const selectedBrand = defineModel('selectedBrand', { type: [String, Number] })

const emit = defineEmits([
  'addToCart',
  'fetchProducts',
  'showRegisterDetails',
  'showHoldList',
  'showRecentSales',
  'showCalculator',
  'loadMore'
])

const selectCategory = (id) => {
  selectedCategory.value = id
  emit('fetchProducts')
}

const selectBrand = (id) => {
  selectedBrand.value = id
  emit('fetchProducts')
}

const onScroll = (e) => {
  const { scrollTop, scrollHeight, clientHeight } = e.target
  if (scrollTop + clientHeight >= scrollHeight - 50) {
    emit('loadMore')
  }
}
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
