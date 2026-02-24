<template>
  <div>
    <!-- Side Drawer (Slide-over) -->
    <Transition
      enter-active-class="transform transition ease-in-out duration-500 sm:duration-700"
      enter-from-class="translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transform transition ease-in-out duration-500 sm:duration-700"
      leave-from-class="translate-x-0"
      leave-to-class="translate-x-full"
    >
      <div v-if="isOpen" class="fixed inset-y-0 right-0 w-full max-w-2xl bg-[#f8fafc] shadow-2xl z-50 flex flex-col border-l border-slate-200">
        <!-- Drawer Header -->
        <div class="px-6 py-5 bg-white border-b border-gray-100 flex items-center justify-between shadow-sm z-10">
          <div class="flex flex-col">
            <h2 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-3">
              Order Details
              <span v-if="order" class="text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded-md border tracking-widest uppercase">
                {{ orderType.toUpperCase() === 'POS' ? order.reference : order.invoice_number }}
              </span>
            </h2>
            <p v-if="order" class="text-xs text-slate-500 font-semibold mt-1">
              {{ new Date(order.created_at).toLocaleString() }} &bull; {{ orderType }}
            </p>
          </div>
          <button @click="closeDrawer" class="p-2 bg-slate-100 text-slate-500 rounded-xl hover:bg-slate-200 hover:text-slate-800 transition-colors active:scale-95 shadow-sm border border-slate-200">
            <XIcon class="w-5 h-5" />
          </button>
        </div>

        <!-- Drawer Body (Scrollable) -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-6">
          <div v-if="loading" class="space-y-6">
            <!-- Skeleton for Status Update -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between animate-pulse">
              <div class="space-y-2">
                <div class="h-4 w-24 bg-slate-100 rounded"></div>
                <div class="h-3 w-48 bg-slate-50 rounded"></div>
              </div>
              <div class="h-10 w-40 bg-slate-100 rounded-xl"></div>
            </div>

            <!-- Skeleton for Customer Info -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm grid grid-cols-2 gap-4 animate-pulse">
              <div class="space-y-2">
                <div class="h-3 w-20 bg-slate-50 rounded"></div>
                <div class="h-4 w-32 bg-slate-100 rounded"></div>
                <div class="h-3 w-40 bg-slate-50 rounded"></div>
              </div>
              <div class="space-y-2">
                <div class="h-3 w-20 bg-slate-50 rounded"></div>
                <div class="h-4 w-32 bg-slate-100 rounded"></div>
                <div class="h-3 w-24 bg-slate-50 rounded"></div>
              </div>
            </div>

            <!-- Skeleton for Items Table -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden animate-pulse">
              <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 h-12"></div>
              <div class="p-5 space-y-4">
                <div v-for="i in 3" :key="i" class="flex justify-between items-center">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded bg-slate-100"></div>
                    <div class="h-4 w-40 bg-slate-100 rounded"></div>
                  </div>
                  <div class="h-4 w-12 bg-slate-100 rounded"></div>
                </div>
              </div>
            </div>

            <!-- Skeleton for Summary -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm ml-auto max-w-sm space-y-3 animate-pulse">
              <div v-for="i in 3" :key="i" class="flex justify-between">
                <div class="h-3 w-16 bg-slate-50 rounded"></div>
                <div class="h-3 w-12 bg-slate-100 rounded"></div>
              </div>
              <div class="pt-3 border-t border-slate-100 flex justify-between">
                <div class="h-4 w-20 bg-slate-100 rounded"></div>
                <div class="h-6 w-24 bg-indigo-50 rounded"></div>
              </div>
            </div>
          </div>
          <div v-else-if="order" class="space-y-6">

             <!-- Order Status Update Section -->
             <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                  <h3 class="text-sm font-black text-slate-800">Current Status</h3>
                  <p class="text-xs text-slate-500 font-medium mt-0.5" v-if="orderType.toUpperCase() !== 'POS'">Change the progression status of this order.</p>
                  <p class="text-xs text-slate-500 font-medium mt-0.5" v-else>Walk-in POS sales usually finalize as Paid.</p>
                </div>
                <!-- Status Dropdown -->
                <div class="relative min-w-[160px]">
                  <select 
                    v-model="orderStatus"
                    @change="updateStatus"
                    :disabled="statusUpdating"
                    class="w-full pl-4 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl appearance-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none font-bold text-sm cursor-pointer disabled:opacity-50"
                    :class="{
                      'text-emerald-600': orderStatus === 'delivered' || orderStatus === 'paid',
                      'text-blue-600': orderStatus === 'processing',
                      'text-amber-600': orderStatus === 'pending',
                      'text-rose-600': orderStatus === 'cancelled'
                    }"
                  >
                    <template v-if="orderType.toUpperCase() === 'POS'">
                      <option value="paid">Paid (Delivered)</option>
                      <option value="pending">Pending</option>
                      <option value="cancelled">Cancelled</option>
                    </template>
                    <template v-else>
                      <option value="pending">Pending</option>
                      <option value="approved">Approved</option>
                      <option value="processing">Processing</option>
                      <option value="courier">Courier</option>
                      <option value="delivered">Delivered</option>
                      <option value="cancelled">Cancelled</option>
                    </template>
                  </select>
                  <div v-if="statusUpdating" class="absolute right-3 top-1/2 -translate-y-1/2">
                    <div class="w-4 h-4 border-2 border-slate-400 border-t-transparent rounded-full animate-spin"></div>
                  </div>
                  <ChevronDownIcon v-else class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                </div>
             </div>

             <!-- Customer Overview -->
             <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm grid grid-cols-2 gap-4">
                <div class="space-y-1">
                   <p class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Customer Details</p>
                   <p class="font-bold text-slate-800 text-sm">
                     {{ order.customer ? trimName(order.customer.first_name, order.customer.last_name, order.customer.name) : 'Walk-in Customer' }}
                   </p>
                   <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                     <PhoneIcon class="w-3.5 h-3.5 text-slate-400" />
                     {{ order.customer?.phone || 'N/A' }}
                   </div>
                   <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                     <MailIcon class="w-3.5 h-3.5 text-slate-400" />
                     {{ order.customer?.email || 'N/A' }}
                   </div>
                </div>

                <div class="space-y-1">
                   <p class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Payment Details</p>
                   <p class="font-bold text-slate-800 text-sm capitalize">
                     {{ (order.payment_method || 'Cash').replace('_', ' ') }}
                   </p>
                   <p class="text-xs font-semibold" :class="order.status === 'paid' || order.payment_status === 'paid' ? 'text-emerald-500' : 'text-amber-500'">
                     {{ order.status === 'paid' || order.payment_status === 'paid' ? 'Fully Paid' : 'Payment Pending' }}
                   </p>
                </div>
             </div>

             <!-- Items Table -->
             <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                  <h3 class="text-sm font-black text-slate-800">Order Items</h3>
                  <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">{{ order.items?.length || 0 }} SKUs</span>
                </div>
                <div class="overflow-x-auto">
                   <table class="w-full text-left">
                     <thead class="bg-slate-50 border-b border-slate-100">
                       <tr>
                         <th class="py-3 px-5 text-[10px] font-black uppercase text-slate-400 tracking-wider">Product</th>
                         <th class="py-3 px-5 text-[10px] font-black uppercase text-slate-400 tracking-wider text-center">Qty</th>
                         <th class="py-3 px-5 text-[10px] font-black uppercase text-slate-400 tracking-wider text-right">Price</th>
                         <th class="py-3 px-5 text-[10px] font-black uppercase text-slate-400 tracking-wider text-right">Total</th>
                       </tr>
                     </thead>
                     <tbody class="divide-y divide-slate-100">
                        <tr v-for="item in order.items" :key="item.id" class="hover:bg-slate-50/50">
                           <td class="py-3 px-5">
                             <div class="flex items-center gap-3">
                               <img v-if="orderType.toUpperCase() !== 'POS' && item.product?.thumbnail" :src="`/storage/${item.product.thumbnail}`" class="w-8 h-8 rounded border object-cover">
                               <div v-else class="w-8 h-8 rounded border bg-slate-100 flex items-center justify-center text-[10px] text-slate-400 font-bold">POS</div>
                               <span class="text-xs font-bold text-slate-700 max-w-[180px] line-clamp-2">
                                 {{ orderType.toUpperCase() === 'POS' ? item.product_name : item.product?.name }}
                               </span>
                             </div>
                           </td>
                           <td class="py-3 px-5 text-center text-xs font-bold text-slate-600">{{ orderType.toUpperCase() === 'POS' ? item.qty : item.quantity }}</td>
                           <td class="py-3 px-5 text-right text-xs font-bold text-slate-600">৳{{ calculatePrice(item) }}</td>
                           <td class="py-3 px-5 text-right text-xs font-black text-slate-800">৳{{ calculateSubtotal(item) }}</td>
                        </tr>
                     </tbody>
                   </table>
                </div>
             </div>

             <!-- Summary -->
             <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm ml-auto max-w-sm">
                <div class="space-y-3">
                   <div class="flex justify-between items-center text-xs font-semibold text-slate-500">
                     <span>Subtotal</span>
                     <span>৳{{ orderType.toUpperCase() === 'POS' ? order.subtotal : calculateOnlineSubtotal(order) }}</span>
                   </div>
                   <div v-if="orderType.toUpperCase() === 'POS'" class="flex justify-between items-center text-xs font-semibold text-rose-500">
                     <span>Discount</span>
                     <span>- ৳{{ order.discount_amount }}</span>
                   </div>
                   <div v-if="orderType.toUpperCase() === 'POS'" class="flex justify-between items-center text-xs font-semibold text-slate-500">
                     <span>Tax ({{ order.tax_percentage }}%)</span>
                     <span>৳{{ order.tax_amount }}</span>
                   </div>
                   <div class="flex justify-between items-center text-xs font-semibold text-slate-500">
                     <span>Shipping / Delivery</span>
                     <span>+ ৳{{ orderType.toUpperCase() === 'POS' ? order.shipping : order.delivery_charge || '0.00' }}</span>
                   </div>
                   <div class="pt-3 border-t border-slate-100 flex justify-between items-center">
                     <span class="text-sm font-black text-slate-800">Total Amount</span>
                     <span class="text-lg font-black text-indigo-600">
                       ৳{{ orderType.toUpperCase() === 'POS' ? order.total : order.total_amount }}
                     </span>
                   </div>
                </div>
             </div>
          </div>
        </div>

        <!-- Drawer Footer -->
        <div class="px-6 py-5 border-t border-gray-200 bg-white flex items-center justify-between shadow-[0_-4px_10px_rgba(0,0,0,0.02)] z-10">
          <button 
            @click="closeDrawer"
            class="px-6 py-2.5 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition-colors shadow-sm"
          >
            Close
          </button>
          
          <div class="flex items-center gap-3">
              <button 
                v-if="order"
                @click="syncCourier('steadfast')"
                :disabled="syncingCourier"
                class="px-6 py-2.5 bg-[#22C55E] text-white font-black rounded-xl hover:bg-emerald-600 transition-all shadow-md shadow-emerald-600/20 flex items-center gap-2 active:scale-95 disabled:opacity-50"
              >
                <TruckIcon v-if="!syncingCourier" class="w-4 h-4" />
                <div v-else class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                Send to Steadfast
              </button>

              <!-- Using absolute relative URL based on the Nuxt routes to avoid hard reloading -->
              <NuxtLink 
                v-if="order"
                :to="`/vendor/orders/invoice/${order.id}?type=${orderType}`"
                target="_blank"
                class="px-6 py-2.5 bg-indigo-600 text-white font-black rounded-xl hover:bg-indigo-700 transition-all shadow-md shadow-indigo-600/20 flex items-center gap-2 active:scale-95"
              >
                <PrinterIcon class="w-4 h-4" />
                Print Invoice
              </NuxtLink>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Overlay -->
    <Transition
      enter-active-class="ease-out duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="isOpen" @click="closeDrawer" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40"></div>
    </Transition>
  </div>
</template>

<script setup>
import { 
  XIcon, 
  ChevronDownIcon,
  PhoneIcon,
  MailIcon,
  PrinterIcon,
  TruckIcon
} from 'lucide-vue-next'
import { ref } from 'vue'
import { toast } from 'vue-sonner'

const { getAll, updateItem, createItem } = useCrud()
const emit = defineEmits(['updated'])

const isOpen = ref(false)
const loading = ref(false)
const order = ref(null)
const orderType = ref('Standard')
const orderStatus = ref('')
const statusUpdating = ref(false)
const activeCouriers = ref([])
const syncingCourier = ref(false)

const openDrawer = async (id, type) => {
    isOpen.value = true
    loading.value = true
    order.value = null
    orderType.value = type
    
    try {
        const response = await getAll(`/vendor/orders/${id}?type=${type}`)
        order.value = response.data
        orderType.value = response.type
        orderStatus.value = order.value.status 

        // Fetch active couriers
        if (type.toUpperCase() !== 'POS' && !order.value?.tracking_number) {
           try {
              const delRes = await getAll('/vendor/delivery')
              if (delRes.data) {
                  activeCouriers.value = []
                  if (delRes.data.steadfast) {
                      const sf = typeof delRes.data.steadfast === 'string' ? JSON.parse(delRes.data.steadfast) : delRes.data.steadfast;
                      if (sf.active) activeCouriers.value.push('steadfast');
                  }
                  if (delRes.data.pathao) {
                      const pt = typeof delRes.data.pathao === 'string' ? JSON.parse(delRes.data.pathao) : delRes.data.pathao;
                      if (pt.active) activeCouriers.value.push('pathao');
                  }
              }
           } catch(e) {}
        }
    } catch (e) {
        toast.error('Failed to load order details.')
        closeDrawer()
    } finally {
        loading.value = false
    }
}

const closeDrawer = () => {
    isOpen.value = false
}

const updateStatus = async () => {
    if(!order.value) return;
    statusUpdating.value = true
    try {
        await updateItem(`/api/vendor/orders/${order.value.id}?type=${orderType.value}`, {
           status: orderStatus.value
        })
        emit('updated')
        toast.success(`Successfully updated order status to ${orderStatus.value}`)
    } catch (e) {
        toast.error('Failed to update status.')
    } finally {
        statusUpdating.value = false
    }
}

const syncCourier = async (courier) => {
    if(!order.value) return;
    syncingCourier.value = true
    try {
        const payload = { courier };
        const res = await createItem(`/vendor/orders/${order.value.id}/sync`, payload, null, false)
        if (res && res.data) {
             order.value = res.data
             orderStatus.value = order.value.status
             emit('updated')
             toast.success(`Successfully sent order to ${courier} Courier`)
        }
    } catch (e) {
        toast.error(e.response?.data?.message || `Failed to sync with ${courier} Courier.`)
    } finally {
        syncingCourier.value = false
    }
}

const trimName = (first, last, full) => {
    if (full) return full;
    return `${first || ''} ${last || ''}`.trim();
}

const calculatePrice = (item) => {
    if (orderType.value.toUpperCase() === 'POS') return item.price;
    return item.unit_price;
}

const calculateSubtotal = (item) => {
    if (orderType.value.toUpperCase() === 'POS') return item.subtotal;
    return item.total_price;
}

const calculateOnlineSubtotal = (orderData) => {
    if(!orderData.items) return '0.00';
    return orderData.items.reduce((acc, curr) => acc + Number(curr.total_price), 0).toFixed(2)
}

defineExpose({
    openDrawer
})
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}
</style>
