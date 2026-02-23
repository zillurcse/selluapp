<template>
  <div class="min-h-screen bg-white text-slate-900 font-sans print:bg-white p-8 max-w-4xl mx-auto" v-if="order">
    
    <!-- Action Bar (Hidden when printing) -->
    <div class="flex items-center justify-between mb-8 pb-6 border-b border-slate-200 print:hidden">
       <div>
         <button @click="$router.back()" class="text-sm font-bold text-slate-500 hover:text-slate-800 flex items-center gap-2 transition-colors">
           &larr; Back to Orders
         </button>
       </div>
       <div class="flex items-center gap-3">
         <button @click="triggerPrint" class="px-6 py-2 bg-indigo-600 text-white font-black rounded-lg hover:bg-indigo-700 shadow-md flex items-center gap-2">
           <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
           Print Now
         </button>
       </div>
    </div>

    <!-- Invoice Header -->
    <div class="flex justify-between items-start mb-10">
      <div>
        <h1 class="text-4xl font-black tracking-tighter text-slate-900">INVOICE</h1>
        <p class="text-slate-500 font-bold mt-1 tracking-widest uppercase text-xs">#{{ orderType === 'POS' ? order.reference : order.invoice_number }}</p>
      </div>
      <div class="text-right">
        <!-- Assume application logo could go here -->
        <h2 class="text-2xl font-black text-indigo-600 tracking-tight">Sellue</h2>
        <p class="text-sm text-slate-500 mt-1">Vendor Dashboard System</p>
      </div>
    </div>

    <!-- Billing Info Grid -->
    <div class="grid grid-cols-2 gap-12 mb-10">
      <!-- Billed To -->
      <div>
        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Billed To:</h3>
        <p class="font-bold text-slate-800 text-lg">{{ getCustomerName() }}</p>
        <p class="text-slate-600 mt-1">{{ order.customer?.phone || 'N/A' }}</p>
        <p class="text-slate-600">{{ order.customer?.email || 'N/A' }}</p>
        <p v-if="orderType !== 'POS'" class="text-slate-600 mt-2 max-w-xs">{{ order.shipping_address || 'In-Store / Digital' }}</p>
      </div>

      <!-- Invoice Details -->
      <div class="text-right">
         <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Invoice Details:</h3>
         <div class="space-y-1.5">
           <div class="flex justify-between border-b border-slate-100 pb-1">
             <span class="text-slate-500 font-medium">Issue Date:</span>
             <span class="font-bold text-slate-800">{{ new Date(order.created_at).toLocaleDateString() }}</span>
           </div>
           <div class="flex justify-between border-b border-slate-100 pb-1">
             <span class="text-slate-500 font-medium">Order Type:</span>
             <span class="font-bold text-slate-800">{{ orderType }}</span>
           </div>
           <div class="flex justify-between border-b border-slate-100 pb-1">
             <span class="text-slate-500 font-medium">Payment Method:</span>
             <span class="font-bold text-slate-800 capitalize">{{ (order.payment_method || 'Cash').replace('_', ' ') }}</span>
           </div>
           <div class="flex justify-between border-b border-slate-100 pb-1">
             <span class="text-slate-500 font-medium">Payment Status:</span>
             <span class="font-bold text-emerald-600 uppercase tracking-wider text-xs" v-if="order.status === 'paid' || order.payment_status === 'paid'">Paid</span>
             <span class="font-bold text-amber-600 uppercase tracking-wider text-xs" v-else>Unpaid</span>
           </div>
         </div>
      </div>
    </div>

    <!-- Items Table -->
    <div class="mb-10">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-y-2 border-slate-800 bg-slate-50 print:bg-transparent">
             <th class="py-4 px-2 font-black text-xs text-slate-800 uppercase tracking-wider">Item Description</th>
             <th class="py-4 px-2 font-black text-xs text-slate-800 uppercase tracking-wider text-center">Qty</th>
             <th class="py-4 px-2 font-black text-xs text-slate-800 uppercase tracking-wider text-right">Unit Price</th>
             <th class="py-4 px-2 font-black text-xs text-slate-800 uppercase tracking-wider text-right">Total</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-for="item in order.items" :key="item.id">
            <td class="py-4 px-2">
               <span class="font-bold text-slate-800">{{ orderType === 'POS' ? item.product_name : item.product?.name }}</span>
               <div v-if="orderType !== 'POS' && item.product?.sku" class="text-xs text-slate-400 mt-1">SKU: {{ item.product.sku }}</div>
            </td>
            <td class="py-4 px-2 text-center font-bold text-slate-600">{{ orderType === 'POS' ? item.qty : item.quantity }}</td>
            <td class="py-4 px-2 text-right font-bold text-slate-600">৳{{ Number(calculatePrice(item)).toFixed(2) }}</td>
            <td class="py-4 px-2 text-right font-black text-slate-900">৳{{ Number(calculateSubtotal(item)).toFixed(2) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Totals Area -->
    <div class="flex justify-end mb-16">
      <div class="w-full max-w-sm space-y-3">
        <div class="flex justify-between items-center text-sm">
           <span class="font-bold text-slate-500">Subtotal</span>
           <span class="font-bold text-slate-800">৳{{ Number(orderType === 'POS' ? order.subtotal : calculateOnlineSubtotal(order)).toFixed(2) }}</span>
        </div>
        
        <div v-if="orderType === 'POS' && order.discount_amount > 0" class="flex justify-between items-center text-sm border-t border-slate-100 pt-2">
           <span class="font-bold text-rose-500">Discount</span>
           <span class="font-bold text-rose-500">- ৳{{ Number(order.discount_amount).toFixed(2) }}</span>
        </div>
        
        <div v-if="orderType === 'POS' && order.tax_amount > 0" class="flex justify-between items-center text-sm border-t border-slate-100 pt-2">
           <span class="font-bold text-slate-500">Tax ({{ order.tax_percentage }}%)</span>
           <span class="font-bold text-slate-800">৳{{ Number(order.tax_amount).toFixed(2) }}</span>
        </div>

        <div class="flex justify-between items-center text-sm border-t border-slate-100 pt-2">
           <span class="font-bold text-slate-500">Shipping</span>
           <span class="font-bold text-slate-800">৳{{ Number(orderType === 'POS' ? order.shipping : order.delivery_charge || 0).toFixed(2) }}</span>
        </div>

        <div class="flex justify-between items-center border-t-2 border-slate-800 pt-4 mt-4">
           <span class="text-lg font-black text-slate-800 uppercase tracking-widest">Total</span>
           <span class="text-2xl font-black text-indigo-600">৳{{ Number(orderType === 'POS' ? order.total : order.total_amount).toFixed(2) }}</span>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="text-center pt-8 border-t border-slate-200">
      <p class="font-bold text-slate-800">Thank you for your business!</p>
      <p class="text-sm text-slate-500 mt-1">If you have any questions about this invoice, please contact support.</p>
    </div>

  </div>
  
  <div v-else class="min-h-screen flex items-center justify-center bg-slate-50 print:hidden">
    <div class="animate-spin rounded-full h-12 w-12 border-4 border-indigo-500 border-t-transparent"></div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const route = useRoute()
const { getAll } = useCrud()

const order = ref(null)
const orderType = ref('')

definePageMeta({
  layout: 'empty', // use empty layout for printable pages ideally
  middleware: 'auth',
  permissions: 'orders.view'
})

onMounted(async () => {
    const id = route.params.id
    const type = route.query.type || 'Standard'
    
    try {
        const response = await getAll(`/vendor/orders/${id}?type=${type}`)
        order.value = response.data
        orderType.value = response.type
        
        // Auto-print delay
        setTimeout(() => {
            window.print()
        }, 500)
    } catch (e) {
        console.error('Failed to load invoice', e)
    }
})

const getCustomerName = () => {
    if (!order.value.customer) return 'Walk-in Customer'
    const c = order.value.customer
    if (c.name) return c.name;
    return `${c.first_name || ''} ${c.last_name || ''}`.trim()
}

const calculatePrice = (item) => {
    if (orderType.value === 'POS') return item.price;
    return item.unit_price;
}

const calculateSubtotal = (item) => {
    if (orderType.value === 'POS') return item.subtotal;
    return item.total_price;
}

const calculateOnlineSubtotal = (orderData) => {
    if(!orderData.items) return 0;
    return orderData.items.reduce((acc, curr) => acc + Number(curr.total_price), 0)
}

const triggerPrint = () => {
    window.print()
}
</script>

<style>
@media print {
  body {
    background: white !important;
  }
}
</style>
