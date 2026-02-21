<template>
  <NuxtLayout name="pos">
    <div class="flex flex-col h-screen bg-[#F8F9FB] dark:bg-slate-950 font-sans text-gray-900 dark:text-slate-100 overflow-hidden transition-colors duration-300">
      <!-- POS Top Bar -->
      <div class="px-6 py-4 bg-white dark:bg-slate-900 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between gap-4 shrink-0 z-10">
        <div class="flex items-center gap-2 flex-1 max-w-sm">
          <div class="flex-1 relative" v-click-outside="() => showCustomerDropdown = false">
            <div
              @click="toggleCustomerDropdown"
              class="w-full h-10 px-4 bg-gray-50 dark:bg-slate-800 border border-transparent rounded-full flex items-center gap-2 text-[13px] font-semibold text-gray-700 dark:text-slate-200 cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700 transition-all border-dashed border-gray-300 dark:border-slate-600"
            >
              <User class="w-4 h-4 text-gray-400" />
              <span class="truncate">{{ selectedCustomerData?.name || 'Walk-in Customer' }}</span>
            </div>
            <!-- Customer Dropdown -->
            <div v-if="showCustomerDropdown" class="absolute top-[calc(100%+6px)] left-0 w-full min-w-[320px] bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-2xl shadow-2xl z-50 overflow-hidden ring-1 ring-black/5 animate-in fade-in zoom-in-95 duration-200">
              <div class="p-4 bg-white dark:bg-slate-900">
                <div class="relative group">
                  <input
                    ref="customerSearchInput"
                    v-model="customerSearchQuery"
                    type="text"
                    placeholder="Search by name, phone..."
                    class="w-full h-10 pl-10 pr-4 bg-gray-50 dark:bg-slate-800 border border-transparent rounded-xl text-[13px] font-bold outline-none focus:border-blue-500 transition-all placeholder:text-gray-300 dark:text-slate-200"
                    @click.stop
                  />
                  <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
                </div>
                <div class="max-h-[320px] overflow-y-auto no-scrollbar pb-3">
                  <div
                    v-for="cust in filteredCustomers"
                    :key="cust.id"
                    @click="selectCustomer(cust)"
                    class="px-6 py-3.5 hover:bg-gray-50 dark:hover:bg-slate-800 cursor-pointer flex flex-col gap-0.5 border-b border-gray-50 dark:border-slate-800 last:border-0"
                    :class="{ 'bg-blue-50/50': selectedCustomer === cust.id }"
                  >
                    <span class="text-[14px] font-black text-[#1e293b] dark:text-slate-100">{{ cust.name }}</span>
                    <div class="flex items-center gap-2">
                      <span v-if="cust.phone" class="text-[11px] text-gray-400 font-bold tracking-tight bg-gray-100 px-1.5 py-0.5 rounded">{{ cust.phone }}</span>
                      <span v-if="cust.id === 'walking'" class="text-[10px] text-blue-500 font-black uppercase tracking-widest bg-blue-50 px-1.5 py-0.5 rounded">Default</span>
                    </div>
                  </div>
                  <div v-if="filteredCustomers.length === 0" class="px-6 py-10 text-center">
                    <p class="text-[13px] text-gray-400 font-bold mb-4 uppercase tracking-wider">No customer found</p>
                    <button
                      @click.stop="openAddCustomerModal"
                      class="w-full flex items-center justify-center gap-2 px-5 py-3 bg-indigo-600 text-white text-[12px] font-black uppercase tracking-widest rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100"
                    >
                      <Plus class="w-4 h-4" />
                      Add New Customer
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <button @click="openAddCustomerModal" class="w-10 h-10 bg-[#6366f1] text-white rounded-lg flex items-center justify-center hover:bg-[#4f46e5] transition-colors shrink-0">
            <UserPlus class="w-5 h-5" />
          </button>
        </div>

        <div class="flex-1 max-w-xl relative">
          <div class="relative group">
            <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Scan/Search Product by Code or Name"
              class="w-full h-10 pl-11 pr-4 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-full text-[13px] font-medium outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/5 transition-all dark:text-slate-200"
            />
          </div>
        </div>
      </div>

      <main class="flex-1 flex overflow-hidden">
        <!-- Left Section: Cart (40%) -->
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
                        <button @click="updateQty(item.id, -1)" class="w-5 h-5 rounded bg-gray-100 dark:bg-slate-700 hover:bg-red-100 flex items-center justify-center transition-colors">-</button>
                        <span>{{ item.qty }}</span>
                        <button @click="updateQty(item.id, 1)" class="w-5 h-5 rounded bg-gray-100 dark:bg-slate-700 hover:bg-green-100 flex items-center justify-center transition-colors">+</button>
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
              <button @click="holdOrder" class="flex-1 bg-[#F175A2] text-white rounded-lg flex items-center justify-center gap-2 text-[13px] font-bold hover:opacity-90 transition-opacity">
                Hold <Hand class="w-4 h-4" />
              </button>
              <button @click="resetPOS" class="flex-1 bg-[#FF3D57] text-white rounded-lg flex items-center justify-center gap-2 text-[13px] font-bold hover:opacity-90 transition-opacity">
                Reset <RefreshCw class="w-4 h-4" />
              </button>
              <button @click="completeSale" :disabled="isPlacingOrder || cart.length === 0" class="flex-[1.5] bg-[#00C484] text-white rounded-lg flex items-center justify-center gap-2 text-[14px] font-black hover:opacity-90 transition-opacity disabled:opacity-50 disabled:cursor-not-allowed">
                <span v-if="isPlacingOrder">Processing...</span>
                <span v-else class="flex items-center gap-2">Pay Now <Banknote class="w-5 h-5" /></span>
              </button>
            </div>
          </div>
        </div>

        <!-- Right Section: Products (60%) -->
        <div class="w-[60%] flex flex-col overflow-hidden bg-[#F8F9FB] dark:bg-slate-950 p-6 pt-4">
          <!-- Action Icons Row -->
          <div class="flex items-center gap-3 mb-4">
            <button @click="fetchStats(); showRegisterDetails = true" class="w-10 h-10 bg-blue-500 text-white rounded-lg flex items-center justify-center shadow-lg shadow-blue-100"><Monitor class="w-5 h-5" /></button>
            <button @click="showHoldList = true" class="w-10 h-10 bg-pink-400 text-white rounded-lg flex items-center justify-center relative shadow-lg shadow-pink-100">
              <span v-if="holdOrders.length" class="absolute -top-1 -right-1 w-5 h-5 bg-blue-500 border-2 border-white rounded-full text-[10px] font-bold flex items-center justify-center">{{ holdOrders.length }}</span>
              <List class="w-5 h-5" />
            </button>
            <button @click="fetchSales(); showRecentSales = true" class="w-10 h-10 bg-blue-600 text-white rounded-lg flex items-center justify-center shadow-lg shadow-blue-200"><LayoutList class="w-5 h-5" /></button>
            <button class="w-10 h-10 bg-green-500 text-white rounded-lg flex items-center justify-center shadow-lg shadow-green-100"><ShoppingBag class="w-5 h-5" /></button>
            <button @click="showCalculator = true" class="w-10 h-10 bg-indigo-400 text-white rounded-lg flex items-center justify-center shadow-lg shadow-indigo-100"><Calculator class="w-5 h-5" /></button>
          </div>

          <!-- Category Pills -->
          <div v-if="categories.length" class="flex items-center gap-2 mb-3 overflow-x-auto no-scrollbar pb-1">
            <button
              @click="selectedCategory = 'all'; fetchProducts()"
              class="px-4 py-1.5 rounded-lg text-[12px] font-bold whitespace-nowrap transition-all"
              :class="selectedCategory === 'all' ? 'bg-blue-500 text-white' : 'bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700'"
            >
              All Categories
            </button>
            <button
              v-for="cat in categories"
              :key="cat.id"
              @click="selectedCategory = cat.id; fetchProducts()"
              class="px-4 py-1.5 rounded-lg text-[12px] font-bold whitespace-nowrap transition-all"
              :class="selectedCategory === cat.id ? 'bg-blue-500 text-white' : 'bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700'"
            >
              {{ cat.name }}
            </button>
          </div>

          <!-- Brand Pills -->
          <div v-if="brands.length" class="flex items-center gap-2 mb-4 overflow-x-auto no-scrollbar pb-1">
            <button
              @click="selectedBrand = 'all'; fetchProducts()"
              class="px-4 py-1.5 rounded-lg text-[12px] font-bold whitespace-nowrap transition-all"
              :class="selectedBrand === 'all' ? 'bg-indigo-500 text-white' : 'bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700'"
            >
              All Brands
            </button>
            <button
              v-for="brand in brands"
              :key="brand.id"
              @click="selectedBrand = brand.id; fetchProducts()"
              class="px-4 py-1.5 rounded-lg text-[12px] font-bold whitespace-nowrap transition-all"
              :class="selectedBrand === brand.id ? 'bg-indigo-500 text-white' : 'bg-white dark:bg-slate-800 text-gray-500 dark:text-slate-400 hover:bg-gray-100 dark:hover:bg-slate-700'"
            >
              {{ brand.name }}
            </button>
          </div>

          <!-- Loading Skeleton -->
          <div v-if="isLoadingProducts" class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 flex-1 overflow-y-auto no-scrollbar pb-8">
            <div v-for="n in 8" :key="n" class="bg-white rounded-[1.5rem] border border-gray-100 overflow-hidden animate-pulse">
              <div class="aspect-square bg-gray-100"></div>
              <div class="p-4 pt-2 space-y-2">
                <div class="h-3 bg-gray-100 rounded w-3/4"></div>
                <div class="h-2 bg-gray-100 rounded w-1/2"></div>
              </div>
            </div>
          </div>

          <!-- Product Grid -->
          <div v-else-if="filteredProducts.length" class="flex-1 overflow-y-auto no-scrollbar pb-8">
            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
              <div
                v-for="product in filteredProducts"
                :key="product.id"
                @click="addToCart(product)"
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
      </main>

      <!-- Success Modal -->
      <div v-if="showSuccess" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-[#0f172a]/80 backdrop-blur-lg" @click="showSuccess = false"></div>
        <div class="bg-white rounded-[3rem] p-12 max-w-md w-full text-center relative shadow-3xl animate-in zoom-in duration-300">
          <div class="w-28 h-28 bg-[#22c55e] text-white rounded-full flex items-center justify-center mx-auto mb-10 shadow-2xl shadow-green-200">
            <Check class="w-14 h-14 stroke-[4]" />
          </div>
          <h2 class="text-4xl font-black text-[#0f172a] mb-2">Sale Complete!</h2>
          <p class="text-gray-500 font-bold mb-1">Ref: <span class="text-blue-600">{{ lastOrderRef }}</span></p>
          <p class="text-[#64748b] font-bold mb-8 leading-relaxed px-6 text-lg">Total: <span class="text-green-600 font-black">৳{{ lastOrderTotal }}</span></p>
          <div class="flex flex-col gap-4">
            <button @click="showInvoice = true" class="w-full py-5 bg-[#0f172a] text-white rounded-2xl font-black hover:bg-black transition-all text-xl shadow-xl shadow-gray-200 flex items-center justify-center gap-3">
              <Printer class="w-6 h-6" /> Print Invoice
            </button>
            <button @click="resetPOS" class="w-full py-5 text-gray-400 font-black hover:text-[#0f172a] transition-all uppercase tracking-widest text-sm">New Sale</button>
          </div>
        </div>
      </div>

      <!-- Modals -->
      <PosAddCustomerModal
        :is-open="showAddCustomer"
        @close="showAddCustomer = false"
        @save="handleNewCustomer"
      />

      <PosHoldListModal
        :is-open="showHoldList"
        :hold-items="holdOrders"
        @close="showHoldList = false"
        @restore="restoreOrder"
        @delete="deleteHoldOrder"
      />

      <PosRecentSalesModal
        :is-open="showRecentSales"
        :sales="recentSales"
        :loading="isLoadingSales"
        @close="showRecentSales = false"
      />

      <PosRegisterDetailsModal
        :is-open="showRegisterDetails"
        :stats="registerStats"
        :loading="isLoadingStats"
        @close="showRegisterDetails = false"
      />

      <PosCalculator
        :is-open="showCalculator"
        @close="showCalculator = false"
      />

      <!-- Invoice Print Modal -->
      <PosInvoicePrint
        :is-open="showInvoice"
        :order="lastOrderData"
        :auto-print="autoPrint"
        @close="showInvoice = false; autoPrint = false"
      />
    </div>
  </NuxtLayout>
</template>

<script setup>
import {
  X, Search, Plus, Minus, Trash2, User, UserPlus,
  ChevronDown, ShoppingBag, ClipboardList, Calendar,
  Check, Home, Monitor, List, LayoutList, Maximize,
  Calculator, Gauge, Hand, RefreshCw, Banknote, Printer
} from 'lucide-vue-next'
import { ref, computed, onMounted, nextTick, watch } from 'vue'
import { toast } from 'vue-sonner'
import useCrud from '~/composables/useCrud'

definePageMeta({ layout: false })

const { getAll, getHeaders } = useCrud()
const config = useRuntimeConfig()
const baseURL = config.public.apiBase

// ── V-Click-Outside Directive ──────────────────────────────────────
const vClickOutside = {
  mounted(el, binding) {
    el.clickOutsideEvent = (event) => {
      if (!(el === event.target || el.contains(event.target))) binding.value(event)
    }
    document.addEventListener('click', el.clickOutsideEvent)
  },
  unmounted(el) {
    document.removeEventListener('click', el.clickOutsideEvent)
  }
}

// ── State ──────────────────────────────────────────────────────────
const searchQuery        = ref('')
const selectedCategory   = ref('all')
const selectedBrand      = ref('all')
const selectedCustomer   = ref('walking')
const showCustomerDropdown = ref(false)
const customerSearchQuery  = ref('')
const customerSearchInput  = ref(null)
const paymentMethod   = ref('cash')
const discountType    = ref('fixed')
const discountValue   = ref(0)
const taxPercentage   = ref(0)
const shipping        = ref(0)
const showSuccess     = ref(false)
const cart            = ref([])
const holdOrders      = ref([])
const recentSales     = ref([])
const lastOrderTotal  = ref(0)
const lastOrderRef    = ref('')

const showAddCustomer    = ref(false)
const showHoldList       = ref(false)
const showRecentSales    = ref(false)
const showRegisterDetails = ref(false)
const showCalculator     = ref(false)
const showInvoice        = ref(false)
const autoPrint          = ref(false)
const lastOrderData      = ref({})

// ── Shop/POS Settings ─────────────────────────────────────────────
const posSettings = ref({ printAfterSale: false, printType: 'a4', currency: 'BDT' })

const fetchPosSettings = async () => {
  try {
    const res = await getAll('/vendor/settings', { group: 'shop_settings' })
    if (res?.data) posSettings.value = { ...posSettings.value, ...res.data }
  } catch (e) { /* silently ignore – non-critical */ }
}

// ── API Data ───────────────────────────────────────────────────────
const products   = ref([])
const customers  = ref([])
const categories = ref([])
const brands     = ref([])
const registerStats     = ref({})
const isLoadingProducts = ref(false)
const isLoadingSales    = ref(false)
const isLoadingStats    = ref(false)
const isPlacingOrder    = ref(false)

// ── API Fetches ────────────────────────────────────────────────────
const fetchProducts = async () => {
  isLoadingProducts.value = true
  try {
    const query = {}
    if (searchQuery.value)                          query.search      = searchQuery.value
    if (selectedCategory.value !== 'all')           query.category_id = selectedCategory.value
    if (selectedBrand.value !== 'all')              query.brand_id    = selectedBrand.value
    products.value = await getAll('/vendor/pos/products', query)
  } catch (e) {
    console.error(e)
  } finally {
    isLoadingProducts.value = false
  }
}

const fetchCustomers = async () => {
  try {
    customers.value = await getAll('/vendor/pos/customers')
  } catch (e) { console.error(e) }
}

const fetchCategories = async () => {
  try {
    categories.value = await getAll('/vendor/pos/categories')
  } catch (e) { console.error(e) }
}

const fetchBrands = async () => {
  try {
    brands.value = await getAll('/vendor/pos/brands')
  } catch (e) { console.error(e) }
}

const fetchSales = async () => {
  isLoadingSales.value = true
  try {
    recentSales.value = await getAll('/vendor/pos/sales')
  } catch (e) { console.error(e) }
  finally { isLoadingSales.value = false }
}

const fetchStats = async () => {
  isLoadingStats.value = true
  try {
    registerStats.value = await getAll('/vendor/pos/stats')
  } catch (e) { console.error(e) }
  finally { isLoadingStats.value = false }
}

// ── Debounced search ───────────────────────────────────────────────
let searchTimeout = null
watch(searchQuery, () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(fetchProducts, 350)
})

// ── Lifecycle ──────────────────────────────────────────────────────
onMounted(() => {
  fetchProducts()
  fetchCustomers()
  fetchCategories()
  fetchBrands()
  fetchPosSettings()
})

// ── Customer Logic ─────────────────────────────────────────────────
const toggleCustomerDropdown = () => {
  showCustomerDropdown.value = !showCustomerDropdown.value
  if (showCustomerDropdown.value) {
    nextTick(() => customerSearchInput.value?.focus())
  }
}

const filteredCustomers = computed(() => {
  if (!customerSearchQuery.value) return customers.value
  const q = customerSearchQuery.value.toLowerCase()
  return customers.value.filter(c =>
    c.name.toLowerCase().includes(q) ||
    (c.phone && c.phone.includes(q)) ||
    (c.email && c.email.toLowerCase().includes(q))
  )
})

const selectedCustomerData = computed(() =>
  customers.value.find(c => c.id === selectedCustomer.value) || { name: 'Walk-in Customer' }
)

const selectCustomer = (customer) => {
  selectedCustomer.value = customer.id
  showCustomerDropdown.value = false
  customerSearchQuery.value = ''
}

const openAddCustomerModal = () => {
  showCustomerDropdown.value = false
  showAddCustomer.value = true
}

const handleNewCustomer = (customer) => {
  customers.value.push(customer)
  selectedCustomer.value = customer.id
  showAddCustomer.value = false
}

// ── Product Logic ──────────────────────────────────────────────────
const filteredProducts = computed(() => products.value)

// ── Cart Logic ─────────────────────────────────────────────────────
const addToCart = (product) => {
  if (product.stock <= 0) {
    toast.error('Product is out of stock')
    return
  }
  const existing = cart.value.find(item => item.id === product.id)
  if (existing) {
    if (existing.qty >= product.stock) {
      toast.warning('Cannot exceed available stock')
      return
    }
    existing.qty++
  } else {
    cart.value.push({ ...product, qty: 1 })
  }
}

const updateQty = (id, delta) => {
  const item = cart.value.find(item => item.id === id)
  if (item) {
    item.qty += delta
    if (item.qty <= 0) removeFromCart(id)
  }
}

const removeFromCart = (id) => {
  cart.value = cart.value.filter(item => item.id !== id)
}

// ── Calculations ───────────────────────────────────────────────────
const subtotal     = computed(() => cart.value.reduce((acc, item) => acc + item.price * item.qty, 0))
const cartTotalQty = computed(() => cart.value.reduce((acc, item) => acc + item.qty, 0))

const discountAmount = computed(() => {
  if (discountType.value === 'percentage') {
    return Math.round(subtotal.value * discountValue.value / 100 * 100) / 100
  }
  return discountValue.value || 0
})

const taxAmount = computed(() => {
  const base = subtotal.value - discountAmount.value
  return Math.round(base * taxPercentage.value / 100 * 100) / 100
})

const total = computed(() =>
  Math.max(0, subtotal.value - discountAmount.value + taxAmount.value + (shipping.value || 0))
)

// ── Order Actions ──────────────────────────────────────────────────
const completeSale = async () => {
  if (cart.value.length === 0) return
  isPlacingOrder.value = true
  try {
    const response = await $fetch('/vendor/pos/order', {
      baseURL,
      method: 'POST',
      headers: getHeaders(),
      body: {
        customer_id:    selectedCustomer.value,
        cart:           cart.value.map(i => ({ id: i.id, name: i.name, sku: i.sku, price: i.price, qty: i.qty })),
        discount_type:  discountType.value,
        discount_value: discountValue.value,
        tax_percentage: taxPercentage.value,
        shipping:       shipping.value,
        payment_method: paymentMethod.value,
      }
    })
    lastOrderTotal.value = response.total
    lastOrderRef.value   = response.reference

    // Store order data for invoice
    lastOrderData.value = {
      reference:      response.reference,
      total:          response.total,
      subtotal:       subtotal.value,
      discountAmount: discountAmount.value,
      taxPercentage:  taxPercentage.value,
      taxAmount:      taxAmount.value,
      shipping:       shipping.value,
      paymentMethod:  paymentMethod.value,
      customerName:   selectedCustomerData.value?.name || 'Walk-in Customer',
      cart:           cart.value.map(i => ({ ...i })),
    }

    // Decide: auto-print or show success modal
    if (posSettings.value.printAfterSale) {
      autoPrint.value  = true
      showInvoice.value = true
    } else {
      showSuccess.value = true
    }

    cart.value           = []
    discountValue.value  = 0
    taxPercentage.value  = 0
    shipping.value       = 0
    // refresh products to reflect stock changes
    fetchProducts()
  } catch (err) {
    toast.error(err.data?.message || 'Failed to place order')
  } finally {
    isPlacingOrder.value = false
  }
}

const holdOrder = () => {
  if (cart.value.length === 0) return
  holdOrders.value.unshift({
    id:       Date.now(),
    date:     new Date().toLocaleString(),
    refId:    'HLD_' + Math.floor(Math.random() * 9000 + 1000),
    cart:     [...cart.value],
    customer: selectedCustomer.value
  })
  cart.value = []
  showHoldList.value = true
}

const restoreOrder = (order) => {
  cart.value = order.cart
  selectedCustomer.value = order.customer
  deleteHoldOrder(order.id)
  showHoldList.value = false
}

const deleteHoldOrder = (id) => {
  holdOrders.value = holdOrders.value.filter(o => o.id !== id)
}

const resetPOS = () => {
  cart.value = []
  showSuccess.value  = false
  searchQuery.value  = ''
  selectedCategory.value = 'all'
  selectedBrand.value    = 'all'
  discountValue.value    = 0
  taxPercentage.value    = 0
  shipping.value         = 0
}
</script>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

@keyframes zoom-in {
  from { opacity: 0; transform: scale(0.95); }
  to   { opacity: 1; transform: scale(1); }
}
.animate-in { animation: zoom-in 0.3s ease-out forwards; }
.shadow-3xl { box-shadow: 0 35px 60px -15px rgba(0,0,0,0.3); }
</style>
