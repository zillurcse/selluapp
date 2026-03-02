<template>
  <div class="min-h-screen bg-[#f8fafc] dark:bg-slate-950 p-6 lg:p-12 transition-colors duration-300">
    <div class="max-w-4xl mx-auto">
      <!-- Header Section -->
      <div class="mb-12 space-y-4">
        <NuxtLink 
          to="/vendor/managed-shop/shipping"
          class="inline-flex items-center text-sm font-semibold text-slate-500 hover:text-emerald-600 transition-colors gap-2 group"
        >
          <ArrowLeft class="w-4 h-4 group-hover:-translate-x-1 transition-transform" />
          Back to Shipping
        </NuxtLink>
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-200 dark:shadow-emerald-900/20">
            <Truck class="w-6 h-6 text-white" />
          </div>
          <div>
            <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Shipping Method</h1>
            <p class="text-slate-500 dark:text-slate-400 font-medium">Select your preferred shipping calculation method.</p>
          </div>
        </div>
      </div>

      <!-- Methods Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div 
          v-for="method in shippingMethods" 
          :key="method.id"
          @click="selectedMethod = method.id"
          :class="[
            'relative p-6 bg-white dark:bg-slate-900 border-2 rounded-[24px] cursor-pointer transition-all duration-300 group',
            selectedMethod === method.id 
              ? 'border-emerald-500 shadow-xl shadow-emerald-500/10' 
              : 'border-slate-100 dark:border-slate-800 hover:border-emerald-200 dark:hover:border-emerald-900'
          ]"
        >
          <div class="flex items-start gap-4">
            <div :class="[
              'w-12 h-12 rounded-xl flex items-center justify-center transition-colors duration-300',
              selectedMethod === method.id ? 'bg-emerald-500 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'
            ]">
              <component :is="method.icon" class="w-6 h-6" />
            </div>
            <div class="flex-1 pr-8">
              <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-1 group-hover:text-emerald-600 transition-colors">
                {{ method.title }}
              </h3>
              <p class="text-sm text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                {{ method.description }}
              </p>
            </div>
          </div>

          <!-- Selection Indicator -->
          <div 
            v-if="selectedMethod === method.id"
            class="absolute top-6 right-6 w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center shadow-lg animate-in zoom-in duration-300"
          >
            <Check class="w-4 h-4 text-white stroke-[3]" />
          </div>
          <div 
            v-else
            class="absolute top-6 right-6 w-6 h-6 border-2 border-slate-200 dark:border-slate-700 rounded-full group-hover:border-emerald-300 transition-colors"
          ></div>

          <!-- Product Wise Price Input -->
          <div 
            v-if="method.id === 'product_wise' && selectedMethod === 'product_wise'"
            class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 animate-in slide-in-from-top-2 duration-300"
            @click.stop
          >
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
              Default Product Shipping Cost
            </label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">$</span>
              <input 
                v-model="productWiseShippingCost"
                type="number"
                step="0.01"
                placeholder="0.00"
                class="w-full pl-8 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border-2 border-transparent focus:border-emerald-500 focus:bg-white dark:focus:bg-slate-900 rounded-xl outline-none transition-all font-bold text-slate-800 dark:text-slate-100"
              />
            </div>
          </div>

          <!-- Flat Rate Price Input -->
          <div 
            v-if="method.id === 'flat_rate' && selectedMethod === 'flat_rate'"
            class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 animate-in slide-in-from-top-2 duration-300"
            @click.stop
          >
            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
              Flat Rate Price
            </label>
            <div class="relative">
              <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">$</span>
              <input 
                v-model="flatRatePrice"
                type="number"
                step="0.01"
                placeholder="0.00"
                class="w-full pl-8 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border-2 border-transparent focus:border-emerald-500 focus:bg-white dark:focus:bg-slate-900 rounded-xl outline-none transition-all font-bold text-slate-800 dark:text-slate-100"
              />
            </div>
          </div>

          <!-- Area Wise Link -->
          <div 
            v-if="method.id === 'area_wise_flat' && selectedMethod === 'area_wise_flat'"
            class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 animate-in slide-in-from-top-2 duration-300"
            @click.stop
          >
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-4">
              Configure specific flat rates for each city in the Cities management section.
            </p>
            <NuxtLink 
              to="/vendor/managed-shop/shipping/cities"
              class="inline-flex items-center gap-2 text-sm font-bold text-emerald-600 hover:text-emerald-700 transition-colors"
            >
              Configure City Rates
              <ArrowRight class="w-4 h-4" />
            </NuxtLink>
          </div>

          <!-- Carrier Wise Settings -->
          <div 
            v-if="method.id === 'carrier_wise' && selectedMethod === 'carrier_wise'"
            class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 animate-in slide-in-from-top-2 duration-300 space-y-4"
            @click.stop
          >
            <div v-for="carrier in ['RedX', 'Pathao', 'Steadfast', 'Personal']" :key="carrier" class="flex items-center gap-4">
              <label class="w-24 text-sm font-bold text-slate-700 dark:text-slate-300">
                {{ carrier }}
              </label>
              <div class="relative flex-1">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">$</span>
                <input 
                  v-model="carrierPrices[carrier.toLowerCase()]"
                  type="number"
                  step="0.01"
                  placeholder="0.00"
                  class="w-full pl-8 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border-2 border-transparent focus:border-emerald-500 focus:bg-white dark:focus:bg-slate-900 rounded-xl outline-none transition-all font-bold text-slate-800 dark:text-slate-100"
                />
              </div>
            </div>
          </div>

          <!-- Courier Wise Settings -->
          <div 
            v-if="method.id === 'courier_wise' && selectedMethod === 'courier_wise'"
            class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 animate-in slide-in-from-top-2 duration-300 space-y-4"
            @click.stop
          >
            <div class="flex gap-4">
              <button 
                v-for="courier in ['Pathao', 'Steadfast']" 
                :key="courier"
                type="button"
                @click="selectedCourier = courier.toLowerCase()"
                :class="[
                  'flex-1 px-4 py-3 rounded-xl border-2 text-sm font-bold transition-all',
                  selectedCourier === courier.toLowerCase() 
                    ? 'border-emerald-500 bg-emerald-50 text-emerald-700' 
                    : 'border-slate-100 bg-slate-50 text-slate-600 hover:border-slate-200'
                ]"
              >
                {{ courier }}
              </button>
            </div>
            <div v-if="selectedCourier === 'pathao'" class="space-y-4 animate-in fade-in duration-300">
               <div class="relative">
                  <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"><Store class="w-4 h-4" /></span>
                  <input 
                    v-model="courierSettings.pathao_store_id"
                    type="text"
                    placeholder="Pathao Store ID"
                    class="w-full pl-10 pr-4 py-3 bg-slate-50 dark:bg-slate-800 border-2 border-transparent focus:border-emerald-500 focus:bg-white dark:focus:bg-slate-900 rounded-xl outline-none transition-all font-medium"
                  />
               </div>
               <p class="text-xs text-slate-500 px-1">Shipping cost will be calculated automatically via Pathao API based on weight and destination.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Footer -->
      <div class="mt-12 flex justify-end">
        <button
          @click="saveSettings"
          :disabled="loading || !selectedMethod"
          class="px-8 py-4 bg-emerald-500 hover:bg-emerald-600 disabled:bg-slate-300 text-white font-bold rounded-2xl shadow-lg shadow-emerald-200 dark:shadow-none transition-all hover:-translate-y-1 active:scale-95 flex items-center gap-3"
        >
          <Loader2 v-if="loading" class="w-5 h-5 animate-spin" />
          <Save v-else class="w-5 h-5" />
          Save Settings
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  Truck, 
  Check, 
  Save, 
  Loader2, 
  ArrowLeft,
  ArrowRight,
  Package,
  CircleDollarSign,
  MapPin,
  Ship,
  Send
} from 'lucide-vue-next'

const { createItem, getAll } = useCrud()
const loading = ref(false)
const selectedMethod = ref(null)
const flatRatePrice = ref(0)
const productWiseShippingCost = ref(0)
const selectedCourier = ref('pathao')
const courierSettings = ref({
  pathao_store_id: ''
})
const carrierPrices = ref({
  redx: 0,
  pathao: 0,
  steadfast: 0,
  personal: 0
})

const shippingMethods = [
  {
    id: 'product_wise',
    title: 'Product Wise Shipping Cost',
    description: 'Shipping cost is calculated based on individual product settings.',
    icon: Package
  },
  {
    id: 'flat_rate',
    title: 'Flat Rate Shipping Cost',
    description: 'A single fixed shipping rate applies to all orders regardless of size.',
    icon: CircleDollarSign
  },
  {
    id: 'area_wise_flat',
    title: 'Area Wise Flat Shipping Cost',
    description: 'Shipping rates vary based on geographical regions or zones.',
    icon: MapPin
  },
  {
    id: 'carrier_wise',
    title: 'Carrier Wise Shipping Cost',
    description: 'Calculate shipping costs based on selected shipping carriers.',
    icon: Ship
  },
  {
    id: 'courier_wise',
    title: 'Courier Wise Shipping Cost',
    description: 'Integration with Pathao, Steadfast, and other courier services.',
    icon: Send
  }
]

// Fetch current settings
const fetchSettings = async () => {
  const response = await getAll('/vendor/business-settings?group=shipping')
  if (response?.status === 'success') {
    const data = response.data
    selectedMethod.value = data.shipping_method
    flatRatePrice.value = data.flat_rate_price || 0
    productWiseShippingCost.value = data.product_wise_shipping_cost || 0
    selectedCourier.value = data.selected_courier || 'pathao'
    if (data.courier_settings) {
      courierSettings.value = { ...courierSettings.value, ...data.courier_settings }
    }
    if (data.carrier_prices) {
      carrierPrices.value = { ...carrierPrices.value, ...data.carrier_prices }
    }
  }
}



// Save settings
const saveSettings = async () => {
  loading.value = true
  try {
    const settings = {
        shipping_method: selectedMethod.value,
        flat_rate_price: selectedMethod.value === 'flat_rate' ? flatRatePrice.value : 0,
        product_wise_shipping_cost: selectedMethod.value === 'product_wise' ? productWiseShippingCost.value : 0,
        selected_courier: selectedMethod.value === 'courier_wise' ? selectedCourier.value : null,
        courier_settings: selectedMethod.value === 'courier_wise' ? courierSettings.value : null,
        carrier_prices: selectedMethod.value === 'carrier_wise' ? carrierPrices.value : null
    }
    const { data, error } = await createItem('/vendor/business-settings', {
      group: 'shipping',
      ...settings
    })

    navigateTo('/vendor/managed-shop/shipping/shipping-method')
  } catch (err) {
    console.error('Error saving settings:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchSettings()
})

definePageMeta({
  middleware: 'auth',
  permissions: 'settings.view'
})
</script>