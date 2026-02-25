<template>
  <NuxtLayout name="pos">
    <div class="flex flex-col h-screen bg-[#F8F9FB] dark:bg-slate-950 font-sans text-gray-900 dark:text-slate-100 overflow-hidden transition-colors duration-300">
      <!-- POS Top Bar -->
      <PosTopBar
        v-model:selectedCustomer="selectedCustomer"
        v-model:searchQuery="searchQuery"
        v-model:customerSearchQuery="customerSearchQuery"
        :selectedCustomerData="selectedCustomerData"
        :filteredCustomers="filteredCustomers"
        :storeName="vendorProfile?.vendor_profile?.store_name || 'My Store'"
        @openAddCustomerModal="openAddCustomerModal"
      />

      <main class="flex-1 flex overflow-hidden">
        <!-- Left Section: Cart (40%) -->
        <PosCartSection
          :cart="cart"
          :cart-total-qty="cartTotalQty"
          :subtotal="subtotal"
          :discount-amount="discountAmount"
          :tax-amount="taxAmount"
          :total="total"
          :is-placing-order="isPlacingOrder"
          v-model:taxPercentage="taxPercentage"
          v-model:discountType="discountType"
          v-model:discountValue="discountValue"
          v-model:shipping="shipping"
          v-model:paymentMethod="paymentMethod"
          @updateQty="updateQty"
          @holdOrder="holdOrder"
          @resetPOS="resetPOS"
          @completeSale="completeSale"
        />

        <!-- Right Section: Products (60%) -->
        <PosProductsSection
          :categories="categories"
          :brands="brands"
          :filtered-products="filteredProducts"
          :is-loading-products="isLoadingProducts"
          :is-loading-more="isLoadingMore"
          :hold-orders-count="holdOrders.length"
          v-model:selectedCategory="selectedCategory"
          v-model:selectedBrand="selectedBrand"
          @addToCart="addToCart"
          @fetchProducts="fetchProducts"
          @loadMore="loadMoreProducts"
          @showRegisterDetails="fetchStats(); showRegisterDetails = true"
          @showHoldList="showHoldList = true"
          @showRecentSales="fetchSales(); showRecentSales = true"
          @showCalculator="showCalculator = true"
        />
      </main>

      <!-- Success Modal -->
      <PosSuccessModal
        :is-open="showSuccess"
        :last-order-ref="lastOrderRef"
        :last-order-total="lastOrderTotal"
        @close="showSuccess = false"
        @printInvoice="showInvoice = true"
        @newSale="resetPOS"
      />

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
import { ref, computed, onMounted, nextTick, watch } from 'vue'
import { toast } from 'vue-sonner'
import useCrud from '~/composables/useCrud'

definePageMeta({ 
  layout: false,
  middleware: 'auth',
  permissions: 'pos.view'
})

const { getAll, getHeaders } = useCrud()
const config = useRuntimeConfig()
const baseURL = config.public.apiBase



// ── State ──────────────────────────────────────────────────────────
const searchQuery        = ref('')
const selectedCategory   = ref('all')
const selectedBrand      = ref('all')
const selectedCustomer   = ref('walking')
const customerSearchQuery  = ref('')
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
const vendorProfile = ref(null)
const registerStats     = ref({})
const isLoadingProducts = ref(false)
const isLoadingMore     = ref(false)
const isLoadingSales    = ref(false)
const isLoadingStats    = ref(false)
const isPlacingOrder    = ref(false)
const currentPage       = ref(1)
const lastPage          = ref(1)

// ── API Fetches ────────────────────────────────────────────────────
const fetchProducts = async (isLoadMore = false) => {
  if (isLoadMore) {
    if (currentPage.value >= lastPage.value || isLoadingMore.value) return
    isLoadingMore.value = true
    currentPage.value++
  } else {
    currentPage.value = 1
    isLoadingProducts.value = true
  }

  try {
    const query = { page: currentPage.value, limit: 20 }
    if (searchQuery.value)                          query.search      = searchQuery.value
    if (selectedCategory.value !== 'all')           query.category_id = selectedCategory.value
    if (selectedBrand.value !== 'all')              query.brand_id    = selectedBrand.value
    
    const response = await getAll('/vendor/pos/products', query)
    const newProducts = response.data || response // Laravel paginator returns { data: [...], last_page: x }

    if (isLoadMore) {
      products.value = [...products.value, ...newProducts]
    } else {
      products.value = newProducts
    }
    
    lastPage.value = response.last_page || 1
  } catch (e) {
    console.error(e)
    if (isLoadMore) currentPage.value-- // Rollback on fail
  } finally {
    isLoadingProducts.value = false
    isLoadingMore.value = false
  }
}

const loadMoreProducts = () => {
  fetchProducts(true)
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

const fetchVendorProfile = async () => {
  try {
    vendorProfile.value = await getAll('/vendor/profile')
  } catch (e) { 
    console.error('Failed to fetch vendor profile', e)
    vendorProfile.value = { vendor_profile: { store_name: 'My Store' } }
  }
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
  fetchVendorProfile()
})

// ── Customer Logic ─────────────────────────────────────────────────

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

const openAddCustomerModal = () => {
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
</style>
