<template>
  <div class="w-[40%] flex flex-col overflow-hidden bg-white dark:bg-slate-900 border-r border-gray-100 dark:border-slate-800">
    <div class="flex-1 overflow-hidden flex flex-col">
      <div class="p-4 flex flex-col h-full">
        <div class="border border-gray-100 dark:border-slate-800 rounded-xl overflow-hidden flex flex-col flex-1">
          <div class="bg-gray-50/50 dark:bg-slate-800/50 px-4 py-3 flex text-[11px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-wider border-b border-gray-100 dark:border-slate-800">
            <div class="w-1/2">PRODUCT</div>
            <div class="w-1/6 text-center">QTY</div>
            <div class="w-1/6 text-right">PRICE</div>
            <div class="w-1/6 text-right">SUBTOTAL</div>
          </div>

          <div class="flex-1 overflow-y-auto no-scrollbar">
            <div v-if="cart.length === 0" class="h-full flex flex-col items-center justify-center p-8 text-center text-gray-400">
              <ShoppingBag class="w-10 h-10 mb-3 opacity-30" />
              <div class="font-bold text-sm">Cart is empty</div>
              <p class="text-xs mt-1">Click a product to add it</p>
            </div>
            <div v-else>
              <div v-for="item in cart" :key="item.id" class="px-4 py-3 flex items-center text-[12px] font-bold text-gray-700 dark:text-slate-300 border-b border-gray-50 dark:border-slate-800 group">
                <div class="w-1/2 truncate pr-2">
                  <div>{{ item.name }}</div>
                  <div class="text-[10px] text-gray-400">{{ item.sku }}</div>
                </div>
                <div class="w-1/6 text-center flex items-center justify-center gap-1">
                  <button @click="$emit('updateQty', item.id, -1)" class="w-5 h-5 rounded bg-gray-100 dark:bg-slate-700 hover:bg-red-100 flex items-center justify-center transition-colors">-</button>
                  <span>{{ item.qty }}</span>
                  <button @click="$emit('updateQty', item.id, 1)" class="w-5 h-5 rounded bg-gray-100 dark:bg-slate-700 hover:bg-green-100 flex items-center justify-center transition-colors">+</button>
                </div>
                <div class="w-1/6 text-right">৳{{ item.price }}</div>
                <div class="w-1/6 text-right">৳{{ (item.price * item.qty).toFixed(2) }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tax/Discount/Summary Section -->
    <div class="p-4 pt-0 space-y-3">
      <div class="flex gap-4">
        <div class="flex-1 space-y-3">
          <!-- Tax -->
          <div class="relative">
            <input v-model.number="taxPercentage" type="number" min="0" max="100" placeholder="Tax %" class="w-full h-11 px-4 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl outline-none text-[13px] font-bold text-gray-700 dark:text-slate-200 focus:border-blue-500 transition-all" />
            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-[13px]">%</span>
          </div>

          <!-- Discount Type -->
          <div class="space-y-1.5">
            <div class="text-[12px] font-bold text-gray-500 dark:text-slate-400">Discount</div>
            <div class="flex items-center gap-3">
              <label class="flex items-center gap-2 cursor-pointer group">
                <div class="w-4 h-4 rounded-full border-2 border-gray-300 flex items-center justify-center group-hover:border-blue-500 transition-colors">
                  <div class="w-2 h-2 rounded-full bg-blue-500" v-if="discountType === 'fixed'"></div>
                </div>
                <span class="text-[12px] font-bold text-gray-600 dark:text-slate-300">Fixed</span>
                <input type="radio" class="hidden" v-model="discountType" value="fixed" />
              </label>
              <label class="flex items-center gap-2 cursor-pointer group">
                <div class="w-4 h-4 rounded-full border-2 border-gray-300 flex items-center justify-center group-hover:border-blue-500 transition-colors">
                  <div class="w-2 h-2 rounded-full bg-blue-500" v-if="discountType === 'percentage'"></div>
                </div>
                <span class="text-[12px] font-bold text-gray-600 dark:text-slate-300">Percentage</span>
                <input type="radio" class="hidden" v-model="discountType" value="percentage" />
              </label>
            </div>
          </div>

          <!-- Discount Value -->
          <div class="relative">
            <input v-model.number="discountValue" type="number" min="0" placeholder="Discount" class="w-full h-11 px-4 bg-gray-50 dark:bg-slate-800 border border-transparent rounded-xl outline-none text-[13px] font-bold text-gray-700 dark:text-slate-200 focus:border-blue-500 transition-all" />
            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-[13px]">{{ discountType === 'percentage' ? '%' : '৳' }}</span>
          </div>

          <!-- Shipping -->
          <div class="relative">
            <input v-model.number="shipping" type="number" min="0" placeholder="Shipping" class="w-full h-11 px-4 bg-gray-50 dark:bg-slate-800 border border-transparent rounded-xl outline-none text-[13px] font-bold text-gray-700 dark:text-slate-200 focus:border-blue-500 transition-all" />
            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-[13px]">৳</span>
          </div>

          <!-- Payment Method -->
          <select v-model="paymentMethod" class="w-full h-11 px-4 bg-gray-50 dark:bg-slate-800 border border-transparent rounded-xl outline-none text-[13px] font-bold text-gray-700 dark:text-slate-200 focus:border-blue-500 transition-all">
            <option value="cash">Cash</option>
            <option value="card">Card</option>
            <option value="mobile_banking">Mobile Banking</option>
          </select>
        </div>

        <div class="w-1/3 flex flex-col justify-end items-end gap-1.5 pb-2">
          <div class="text-[13px] font-bold text-gray-500 dark:text-slate-400">Items: <span class="text-gray-900 dark:text-slate-100">{{ cartTotalQty }}</span></div>
          <div class="text-[13px] font-bold text-gray-500 dark:text-slate-400">Subtotal: <span class="text-blue-600">৳ {{ subtotal.toFixed(2) }}</span></div>
          <div v-if="discountAmount > 0" class="text-[13px] font-bold text-gray-500 dark:text-slate-400">Discount: <span class="text-red-500">-৳ {{ discountAmount.toFixed(2) }}</span></div>
          <div v-if="taxAmount > 0" class="text-[13px] font-bold text-gray-500 dark:text-slate-400">Tax: <span class="text-orange-500">+৳ {{ taxAmount.toFixed(2) }}</span></div>
          <div v-if="shipping > 0" class="text-[13px] font-bold text-gray-500 dark:text-slate-400">Shipping: <span class="text-gray-700 dark:text-slate-300">+৳ {{ shipping.toFixed(2) }}</span></div>
          <div class="text-[16px] font-black text-gray-500 dark:text-slate-400 border-t border-gray-200 dark:border-slate-700 pt-1.5 mt-0.5">Total: <span class="text-green-600 font-black">৳ {{ total.toFixed(2) }}</span></div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="flex gap-2 h-12 pt-2">
        <button @click="$emit('holdOrder')" class="flex-1 bg-[#F175A2] text-white rounded-lg flex items-center justify-center gap-2 text-[13px] font-bold hover:opacity-90 transition-opacity">
          Hold <Hand class="w-4 h-4" />
        </button>
        <button @click="$emit('resetPOS')" class="flex-1 bg-[#FF3D57] text-white rounded-lg flex items-center justify-center gap-2 text-[13px] font-bold hover:opacity-90 transition-opacity">
          Reset <RefreshCw class="w-4 h-4" />
        </button>
        <button @click="$emit('completeSale')" :disabled="isPlacingOrder || cart.length === 0" class="flex-[1.5] bg-[#00C484] text-white rounded-lg flex items-center justify-center gap-2 text-[14px] font-black hover:opacity-90 transition-opacity disabled:opacity-50 disabled:cursor-not-allowed">
          <span v-if="isPlacingOrder">Processing...</span>
          <span v-else class="flex items-center gap-2">Pay Now <Banknote class="w-5 h-5" /></span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ShoppingBag, Hand, RefreshCw, Banknote } from 'lucide-vue-next'

const props = defineProps({
  cart: { type: Array, default: () => [] },
  cartTotalQty: { type: Number, default: 0 },
  subtotal: { type: Number, default: 0 },
  discountAmount: { type: Number, default: 0 },
  taxAmount: { type: Number, default: 0 },
  total: { type: Number, default: 0 },
  isPlacingOrder: { type: Boolean, default: false }
})

const taxPercentage = defineModel('taxPercentage', { type: Number })
const discountType = defineModel('discountType', { type: String })
const discountValue = defineModel('discountValue', { type: Number })
const shipping = defineModel('shipping', { type: Number })
const paymentMethod = defineModel('paymentMethod', { type: String })

const emit = defineEmits(['updateQty', 'holdOrder', 'resetPOS', 'completeSale'])
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
