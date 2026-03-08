<template>
  <div class="checkout-page min-h-screen bg-gray-50 flex flex-col items-center font-sans tracking-normal">
    <!-- Header -->
    <header class="w-full bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-900 tracking-tight">Checkout</h1>
        <NuxtLink to="/shop" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors flex items-center gap-1">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
          Back to Shop
        </NuxtLink>
      </div>
    </header>

    <main class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
      <div v-if="cart.length === 0" class="max-w-md mx-auto flex flex-col items-center justify-center py-20 bg-white rounded-2xl shadow-sm border border-gray-100 animate-fade-in">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-3xl mb-6">🛒</div>
        <h2 class="text-xl font-semibold text-gray-900 mb-2">Your cart is empty</h2>
        <p class="text-gray-500 text-center mb-8 px-6 text-sm">Looks like you haven't added anything to your cart yet.</p>
        <NuxtLink to="/shop" class="px-6 py-3 bg-black text-white rounded-xl font-medium text-sm hover:bg-gray-800 transition-colors shadow-sm">
          Continue Shopping
        </NuxtLink>
      </div>

      <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
        <!-- Left Column: Form -->
        <div class="lg:col-span-7 space-y-8 animate-fade-in" style="animation-delay: 0.1s">
          
          <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Contact -->
            <section class="p-6 sm:p-8 border-b border-gray-100">
              <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-3">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-900 text-xs font-bold">1</span>
                Contact Information
              </h2>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Email Address <span class="text-red-500">*</span>
                  </label>
                  <input v-model="form.email" type="email" placeholder="you@example.com" :class="['w-full px-4 py-2.5 bg-white border rounded-xl outline-none focus:ring-2 focus:ring-black focus:border-transparent text-sm transition-shadow', validationErrors.email ? 'border-red-500' : 'border-gray-300']" />
                  <p v-if="validationErrors.email" class="mt-1 text-xs text-red-500">{{ validationErrors.email }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Phone Number <span class="text-red-500">*</span>
                  </label>
                  <vue-tel-input 
                    v-model="form.phone" 
                    mode="auto"
                    :onlyCountries="['BD']"
                    :maxLen="13"
                    :autoFormat="false"
                    @validate="(validState) => isPhoneValid = validState.valid"
                    :dropdownOptions="{
                      showFlags: true,
                      showDialCodeInList: true,
                      showDialCodeInSelection: true,
                      disabled: true
                    }"
                    :inputOptions="{
                      placeholder: '01XXXXXXXXX',
                      styleClasses: '!w-full !px-4 !py-2.5 !bg-white !border !rounded-xl !outline-none !focus:ring-2 !focus:ring-black !focus:border-transparent !text-sm !transition-shadow ' + (validationErrors.phone ? '!border-red-500' : '!border-gray-300'),
                    }"
                    class="tel-input-custom"
                  />
                  <p v-if="validationErrors.phone" class="mt-1 text-xs text-red-500">{{ validationErrors.phone }}</p>
                </div>
              </div>
            </section>

            <section class="p-6 sm:p-8 border-b border-gray-100">
              <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-3">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-900 text-xs font-bold">2</span>
                Shipping Address
              </h2>
              
              <!-- Saved Addresses -->
              <div v-if="authStore.token && savedAddresses.length > 0" class="mb-8">
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-sm font-medium text-gray-700">Select a Saved Address</label>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div 
                    v-for="address in savedAddresses" 
                    :key="address.id"
                    @click="selectSavedAddress(address)"
                    class="border border-gray-200 rounded-xl p-4 cursor-pointer hover:border-black hover:shadow-sm transition-all bg-white relative group"
                  >
                    <div class="flex items-start justify-between mb-2">
                        <span class="font-semibold text-sm text-gray-900">{{ address.name }}</span>
                        <span v-if="address.is_default" class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-medium">Default</span>
                    </div>
                    <p class="text-xs text-gray-600 leading-relaxed mb-1">{{ address.line1 }}</p>
                    <p class="text-xs text-gray-500">{{ address.city }}, {{ address.state }} {{ address.zip }}</p>
                    <div class="absolute inset-x-0 bottom-0 h-1 bg-black translate-y-full opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all rounded-b-xl"></div>
                  </div>
                </div>
                <div class="flex items-center gap-4 my-6">
                    <div class="h-px bg-gray-200 flex-1"></div>
                    <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">Or enter manually</span>
                    <div class="h-px bg-gray-200 flex-1"></div>
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    First Name <span class="text-red-500">*</span>
                  </label>
                  <input v-model="form.first_name" type="text" :class="['w-full px-4 py-2.5 bg-white border rounded-xl outline-none focus:ring-2 focus:ring-black focus:border-transparent text-sm transition-shadow', validationErrors.first_name ? 'border-red-500' : 'border-gray-300']" />
                  <p v-if="validationErrors.first_name" class="mt-1 text-xs text-red-500">{{ validationErrors.first_name }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Last Name <span class="text-red-500">*</span>
                  </label>
                  <input v-model="form.last_name" type="text" :class="['w-full px-4 py-2.5 bg-white border rounded-xl outline-none focus:ring-2 focus:ring-black focus:border-transparent text-sm transition-shadow', validationErrors.last_name ? 'border-red-500' : 'border-gray-300']" />
                  <p v-if="validationErrors.last_name" class="mt-1 text-xs text-red-500">{{ validationErrors.last_name }}</p>
                </div>
                <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
                  <div class="relative z-[60]">
                    <AppSearchSelect 
                      v-model="form.state_id" 
                      :items="states"
                      label="State / Division"
                      placeholder="Select State"
                      required
                      :error="!!validationErrors.state_id"
                      @update:modelValue="handleStateChange"
                      class="!border-gray-300"
                    />
                    <p v-if="validationErrors.state_id" class="mt-1 text-xs text-red-500">{{ validationErrors.state_id }}</p>
                  </div>
                  <div class="relative z-[50]">
                    <AppSearchSelect 
                      v-model="form.city_id" 
                      :items="cities"
                      label="City / Area"
                      :placeholder="loadingCities ? 'Loading...' : 'Select City'"
                      :disabled="!form.state_id || loadingCities"
                      required
                      :error="!!validationErrors.city_id"
                      @update:modelValue="handleCityChange"
                      class="!border-gray-300"
                    />
                    <p v-if="validationErrors.city_id" class="mt-1 text-xs text-red-500">{{ validationErrors.city_id }}</p>
                  </div>
                </div>

                <!-- Carrier Selection (Visible if any vendor uses carrier_wise) -->
                <div v-if="availableCarriers.length > 0" class="sm:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-3">Select Shipping Carrier</label>
                  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <button 
                      v-for="carrier in availableCarriers" 
                      :key="carrier.id"
                      type="button"
                      @click="form.carrier = carrier.id; handleCityChange()"
                      :class="[
                        'px-4 py-3 rounded-xl border-2 text-sm font-bold transition-all flex flex-col items-center gap-2',
                        form.carrier === carrier.id 
                          ? 'border-black bg-black text-white px-8' 
                          : 'border-gray-100 bg-gray-50 text-gray-600 hover:border-gray-200'
                      ]"
                    >
                      {{ carrier.name }}
                    </button>
                  </div>
                </div>

                <div class="sm:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Full Address <span class="text-red-500">*</span>
                  </label>
                  <textarea v-model="form.full_address" rows="2" placeholder="House #, Road #, Area details..." :class="['w-full px-4 py-2.5 bg-white border rounded-xl outline-none focus:ring-2 focus:ring-black focus:border-transparent text-sm transition-shadow resize-none', validationErrors.full_address ? 'border-red-500' : 'border-gray-300']"></textarea>
                  <p v-if="validationErrors.full_address" class="mt-1 text-xs text-red-500">{{ validationErrors.full_address }}</p>
                </div>
                <div class="sm:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1.5">Order Note (Optional)</label>
                  <textarea v-model="form.note" rows="3" placeholder="Notes about your order, e.g. special notes for delivery." class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-black focus:border-transparent text-sm transition-shadow resize-none"></textarea>
                </div>
              </div>
            </section>

            <!-- Payment -->
            <section class="p-6 sm:p-8">
              <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-3">
                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 text-gray-900 text-xs font-bold">3</span>
                Payment Method
              </h2>
              
              <div class="space-y-6">
                <!-- Loading State -->
                <div v-if="loadingGateways" class="flex justify-center py-6">
                  <div class="w-6 h-6 border-2 border-black border-t-transparent rounded-full animate-spin"></div>
                </div>

                <!-- Empty State -->
                <div v-else-if="gateways.length === 0" class="text-center py-8 px-4 border border-dashed border-gray-300 rounded-xl bg-gray-50">
                  <p class="text-sm text-gray-600">No payment methods available for this vendor.</p>
                </div>

                <!-- Gateways List -->
                <div v-else class="flex flex-col gap-3">
                  <label 
                    v-for="gateway in gateways" 
                    :key="gateway.slug"
                    class="relative flex cursor-pointer rounded-xl border-2 p-4 transition-all hover:bg-gray-50 focus:outline-none"
                    :class="paymentMethod === gateway.slug ? 'border-black bg-gray-50/50' : 'border-gray-200'"
                  >
                    <input 
                      type="radio" 
                      name="payment_method" 
                      :value="gateway.slug" 
                      v-model="paymentMethod" 
                      class="sr-only" 
                    />
                    <span class="flex flex-1">
                      <span class="flex flex-col">
                        <span class="block text-sm font-medium text-gray-900">{{ gateway.name }}</span>
                        <span v-if="paymentMethod === gateway.slug && gateway.instruction" class="mt-2 text-sm text-gray-600 block pl-1 border-l-2 border-gray-300">
                          {{ gateway.instruction }}
                        </span>
                      </span>
                    </span>
                    <svg class="h-5 w-5 text-black" v-if="paymentMethod === gateway.slug" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                  </label>
                </div>

                <!-- Custom Card Form Mockup (Optional logic based on Gateway type) -->
                <div v-if="isValidCreditCardSelected" class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
                  <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Card Number</label>
                    <div class="relative">
                      <input v-model="form.card_number" type="text" placeholder="0000 0000 0000 0000" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-black focus:border-transparent text-sm transition-shadow" />
                    </div>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Expiry Date</label>
                    <input v-model="form.expiry_date" type="text" placeholder="MM/YY" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-black focus:border-transparent text-sm transition-shadow" />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">CVC / CVV</label>
                    <input v-model="form.cvc" type="text" placeholder="123" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl outline-none focus:ring-2 focus:ring-black focus:border-transparent text-sm transition-shadow" />
                  </div>
                </div>
              </div>
            </section>
          </div>

        </div>

        <!-- Right Column: Summary -->
        <aside class="lg:col-span-5 lg:sticky lg:top-24 animate-fade-in" style="animation-delay: 0.2s">
          <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Order Summary</h2>
            
            <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar mb-6">
              <div v-for="item in cart" :key="item.id" class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0 border border-gray-200">
                  <img :src="item.image" class="w-full h-full object-cover" />
                </div>
                <div class="flex-1 min-w-0">
                  <h3 class="text-sm font-medium text-gray-900 truncate">{{ item.name }}</h3>
                  <p class="text-sm text-gray-500 mt-0.5">Qty: {{ item.quantity }}</p>
                </div>
                <div class="text-sm font-medium text-gray-900">
                  ৳{{ (parseFloat(item.price) * item.quantity).toFixed(2) }}
                </div>
              </div>
            </div>

            <div class="space-y-3 pt-6 border-t border-gray-100">
              <div class="flex justify-between text-sm text-gray-600">
                <span>Subtotal</span>
                <span class="font-medium text-gray-900">৳{{ cartTotal.toFixed(2) }}</span>
              </div>
              <div v-if="discountTotal > 0" class="flex justify-between text-sm text-rose-600 bg-rose-50 px-3 py-2 rounded-lg border border-rose-100 animate-pulse">
                <div class="flex flex-col gap-1">
                    <span class="font-bold">Discount</span>
                    <div class="flex flex-wrap gap-1">
                        <span v-for="offer in appliedOffers" :key="offer.offer_id" class="text-[10px] bg-rose-100 text-rose-700 px-1.5 py-0.5 rounded-md font-black uppercase tracking-tighter">
                            {{ offer.offer_title }}
                        </span>
                    </div>
                </div>
                <span class="font-black">-৳{{ discountTotal.toFixed(2) }}</span>
              </div>
              <div class="flex justify-between text-sm text-gray-600">
                <span>Estimated Shipping</span>
                <span class="font-medium text-gray-900">{{ `৳${shippingCost.toFixed(2)}` }}</span>
              </div>
              <div class="flex justify-between text-sm text-gray-600">
                <span class="font-medium text-gray-900">Calculated at next step</span>
              </div>

              <!-- Loyalty Points Earned Info -->
              <div v-if="isLoyaltyActive && potentialPoints > 0" class="flex items-center gap-2 p-3 bg-purple-50 rounded-xl border border-purple-100 text-purple-700 animate-fade-in mt-4">
                 <span class="text-xl">✨</span>
                 <div>
                    <p class="text-[10px] font-black uppercase tracking-wider leading-none mb-1 opacity-70">Loyalty Reward</p>
                    <p class="text-xs font-bold leading-tight">You will earn <span class="text-sm font-black underline decoration-2">{{ potentialPoints }} points</span> with this order!</p>
                 </div>
              </div>

              <!-- Message if underneath min order -->
              <div v-else-if="isLoyaltyActive" class="flex items-center gap-2 p-3 bg-gray-50 rounded-xl border border-gray-100 text-gray-500 animate-fade-in mt-4">
                 <span class="text-xl">💡</span>
                 <div>
                    <p class="text-[10px] font-black uppercase tracking-wider leading-none mb-1 opacity-70">Loyalty Program</p>
                    <p class="text-xs font-semibold leading-tight">Add more items to start earning loyalty points!</p>
                 </div>
              </div>

              <!-- Loyalty Points Redemption -->
              <div v-if="isLoyaltyActive && userLoyaltyBalance > 0" class="pt-4 mt-2 border-t border-gray-100">
                <div class="flex items-center justify-between p-3 bg-indigo-50 rounded-xl border border-indigo-100">
                  <div class="flex items-center gap-2">
                    <span class="text-xl">🎁</span>
                    <div>
                      <p class="text-[10px] font-black uppercase tracking-wider leading-none mb-1 text-indigo-400">Redeem Points</p>
                      <p class="text-xs font-bold text-indigo-700 leading-tight">You have {{ userLoyaltyBalance }} points</p>
                    </div>
                  </div>
                  <button 
                    @click="useLoyaltyPoints = !useLoyaltyPoints"
                    :class="[
                      'px-3 py-1.5 rounded-lg text-xs font-black uppercase tracking-tighter transition-all',
                      useLoyaltyPoints ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-200' : 'bg-white text-indigo-600 border border-indigo-200 hover:bg-indigo-50'
                    ]"
                  >
                    {{ useLoyaltyPoints ? 'Applied' : 'Apply' }}
                  </button>
                </div>
                <p v-if="useLoyaltyPoints && loyaltyRedemptionDiscount > 0" class="mt-2 text-[10px] text-indigo-500 font-bold px-1 italic">
                  ✨ ৳{{ loyaltyRedemptionDiscount.toFixed(2) }} discount applied (Redeeming {{ redeemablePoints }} points)
                </p>
                <p v-if="useLoyaltyPoints && loyaltyRedemptionDiscount <= 0 && loyaltyMessage" class="mt-2 text-[10px] text-rose-500 font-bold px-1">
                  ⚠️ {{ loyaltyMessage }}
                </p>
              </div>

              <!-- Coupon Input -->
              <div class="pt-4 mt-2 border-t border-gray-100">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Have a coupon code?</label>
                <div class="flex gap-2">
                  <input 
                    v-model="couponCode" 
                    type="text" 
                    placeholder="Enter code" 
                    class="flex-1 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-all"
                    :class="{'border-red-300 bg-red-50': couponError}"
                    @keyup.enter="calculateDiscounts"
                    @input="couponError = ''"
                  />
                  <button 
                    @click="calculateDiscounts"
                    :disabled="calculatingDiscount || !couponCode"
                    class="px-4 py-2 bg-gray-100 text-gray-900 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Apply
                  </button>
                </div>
                <p v-if="couponError" class="mt-2 text-xs text-red-500 font-medium animate-shake">{{ couponError }}</p>
              </div>
            </div>
            
            <div class="flex justify-between items-end pt-6 mt-6 border-t border-gray-100">
              <span class="text-base font-semibold text-gray-900">Total</span>
              <div class="text-right">
                <span class="text-xs text-gray-500 font-medium mr-2">BDT</span>
                <span class="text-2xl font-bold text-gray-900">৳{{ (cartTotal + shippingCost - discountTotal).toFixed(2) }}</span>
              </div>
            </div>

            <button 
              class="w-full mt-8 py-3.5 bg-black text-white rounded-xl font-medium text-base shadow-sm hover:bg-gray-800 focus:ring-4 focus:ring-gray-200 transition-all flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed" 
              @click="placeOrder"
              :disabled="loading"
            >
              <span v-if="loading" class="flex items-center gap-2">
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Processing...
              </span>
              <span v-else>Place Order - ৳{{ (cartTotal + shippingCost - discountTotal).toFixed(2) }}</span>
            </button>
            
            <div class="mt-6 flex items-center justify-center gap-2 text-xs text-gray-500">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              Secure 256-bit SSL encrypted checkout
            </div>
          </div>
        </aside>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import AppSearchSelect from '~/components/AppSearchSelect.vue'
const { cart, cartTotal } = useCart()
const authStore = useAuthStore()
const router = useRouter()
const config = useRuntimeConfig()
const { trackInitiateCheckout, trackPurchase } = useTracking()
const storefrontStore = useStorefrontStore()

const isLoyaltyActive = computed(() => {
  const loyalty = storefrontStore.loyaltyProgram
  if (!loyalty) return false
  const v = loyalty.is_enabled
  // Loose equality covers '1' == 1, and we check common truths
  return v == '1' || v == 1 || v == true || v == 'true' || v == 'on'
})

const potentialPoints = computed(() => {
  const loyalty = storefrontStore.loyaltyProgram
  if (!isLoyaltyActive.value) return 0
  
  const subtotal = cartTotal.value - discountTotal.value
  const minOrder = parseFloat(loyalty.min_order_total || '0')
  
  if (subtotal < minOrder) return 0
  
  const earningRate = parseFloat(loyalty.point_earning_rate || '0') // points per 100
  if (earningRate <= 0) return 0
  
  return Math.floor((subtotal / 100) * earningRate)
})

const gateways = ref([])
const loadingGateways = ref(false)
const paymentMethod = ref('')
const loading = ref(false)
const validationErrors = ref({})
const isPhoneValid = ref(false)

const states = ref([])
const cities = ref([])
const loadingCities = ref(false)
const shippingCost = ref(0)
const selectedCity = ref(null)
const availableCarriers = ref([])

const prefillAddress = ref(null)
const savedAddresses = ref([])
const loadingAddresses = ref(false)

const discountTotal = ref(0)
const appliedOffers = ref([])
const calculatingDiscount = ref(false)
const couponCode = ref('')
const couponError = ref('')

const useLoyaltyPoints = ref(false)
const userLoyaltyBalance = ref(0)
const loyaltyRedemptionDiscount = ref(0)
const redeemablePoints = ref(0)
const loyaltyMessage = ref('')

const form = ref({
  email: '',
  phone: '',
  first_name: '',
  last_name: '',
  full_address: '',
  state_id: '',
  city_id: '',
  city_name: '',
  note: '',
  card_number: '',
  expiry_date: '',
  cvc: '',
  carrier: 'personal'
})

const fetchStates = async () => {
  try {
    const response = await $fetch(`${config.public.apiBase}/storefront/states`)
    if (response.success) {
      states.value = response.data
    }
  } catch (error) {
    console.error('Failed to fetch states:', error)
  }
}

const fetchCities = async (stateId) => {
  if (!stateId) return
  loadingCities.value = true
  try {
    const response = await $fetch(`${config.public.apiBase}/storefront/cities`, {
      params: { state_id: stateId }
    })
    if (response.success) {
      cities.value = response.data
    }
  } catch (error) {
    console.error('Failed to fetch cities:', error)
  } finally {
    loadingCities.value = false
  }
}

const fetchSavedAddresses = async () => {
    if (!authStore.token) return
    loadingAddresses.value = true
    try {
        const response = await $fetch(`${config.public.apiBase}/customer/addresses`, {
            headers: {
                Authorization: `Bearer ${authStore.token}`
            }
        })
        console.log('Saved addresses response:', response)
        if (response.addresses) {
            savedAddresses.value = response.addresses
        }
    } catch (error) {
        console.error('Failed to fetch saved addresses:', error)
    } finally {
        loadingAddresses.value = false
    }
}

const selectSavedAddress = async (address) => {
    form.value.first_name = authStore.user?.customer?.first_name || authStore.user?.name.split(' ')[0] || ''
    form.value.last_name = authStore.user?.customer?.last_name || authStore.user?.name.split(' ').slice(1).join(' ') || ''
    form.value.full_address = address.line1
    
    // Attempt to match state and city by name
    const stateMatch = states.value.find(s => s.name.toLowerCase() === address.state.toLowerCase())
    if (stateMatch) {
        form.value.state_id = stateMatch.id
        await fetchCities(stateMatch.id)
        
        const cityMatch = cities.value.find(c => c.name.toLowerCase() === address.city.toLowerCase())
        if (cityMatch) {
            form.value.city_id = cityMatch.id
            await handleCityChange()
        } else {
            form.value.city_id = ''
        }
    } else {
        form.value.state_id = ''
        form.value.city_id = ''
    }
}

const handleStateChange = () => {
  form.value.city_id = ''
  selectedCity.value = null
  shippingCost.value = 0
  fetchCities(form.value.state_id)
}

const handleCityChange = async () => {
  const city = cities.value.find(c => c.id === form.value.city_id)
  if (city) {
    selectedCity.value = city
    form.value.city_name = city.name
    
    // Fetch estimated shipping cost from backend
    try {
      const response = await $fetch(`${config.public.apiBase}/checkout/estimate-shipping`, {
        method: 'POST',
        body: { 
          city_id: city.id,
          items: cart.value,
          carrier: form.value.carrier
        }
      })
      if (response.status === 1) {
        shippingCost.value = response.cost
        availableCarriers.value = response.available_carriers || []
        // If current carrier is not in available carriers, select first or default
        if (availableCarriers.value.length > 0) {
           const exists = availableCarriers.value.find(c => c.id === form.value.carrier)
           if (!exists) {
             form.value.carrier = availableCarriers.value[0].id
             // Recalculate if we switched carrier
             handleCityChange()
           }
        }
      } else {
        // Fallback to local cost if API fails or returns error
        shippingCost.value = parseFloat(city.cost || 0)
        availableCarriers.value = []
      }
    } catch (error) {
      console.error('Failed to estimate shipping:', error)
      shippingCost.value = parseFloat(city.cost || 0)
      availableCarriers.value = []
    }
    } else {
    selectedCity.value = null
    shippingCost.value = 0
  }
}

const calculateDiscounts = async () => {
  if (cart.value.length === 0) {
    discountTotal.value = 0
    appliedOffers.value = []
    return
  }

  calculatingDiscount.value = true
  try {
    const response = await $fetch(`${config.public.apiBase}/storefront/checkout/calculate-discount`, {
      method: 'POST',
      body: {
        items: cart.value.map(item => ({
          id: item.id,
          quantity: item.quantity
        })),
        coupon_code: couponCode.value,
        use_loyalty_points: useLoyaltyPoints.value,
        email: form.value.email
      }
    })

    if (response.success) {
      discountTotal.value = response.data.discount_total || 0
      appliedOffers.value = response.data.applied_offers || []
      couponError.value = response.data.coupon_error || ''
      loyaltyRedemptionDiscount.value = response.data.loyalty_discount || 0
      redeemablePoints.value = response.data.redeemable_points || 0
      loyaltyMessage.value = response.data.loyalty_message || ''
    }
  } catch (error) {
    console.error('Failed to calculate discounts:', error)
  } finally {
    calculatingDiscount.value = false
  }
}

const isValidCreditCardSelected = computed(() => {
  const selected = gateways.value.find(g => g.slug === paymentMethod.value)
  return selected && ['stripe'].includes(selected.type)
})

const { getPaymentMethods } = useCrud()

const fetchGateways = async () => {
  if (cart.value.length === 0) return

  loadingGateways.value = true
  try {
    const payload = {
      items: cart.value.map(item => ({ id: item.id }))
    }
    const response = await getPaymentMethods('/storefront/checkout/payment-methods', payload)
    if (response.success) {
      gateways.value = response.gateways || []
      if (gateways.value.length > 0) {
        paymentMethod.value = gateways.value[0].slug
      }
    }
  } catch (error) {
    console.error('Failed to fetch gateways:', error)
  } finally {
    loadingGateways.value = false
  }
}

watch(() => cart.value, () => {
  if(gateways.value.length === 0 && cart.value.length > 0) {
     fetchGateways()
  }
  calculateDiscounts()
}, { deep: true, immediate: true })

watch(useLoyaltyPoints, () => {
  calculateDiscounts()
})

onMounted(async () => {
  if (authStore.user) {
    form.value.email = authStore.user.email || ''
    
    // Fetch latest loyalty balance
    try {
      const profileData = await $fetch(`${config.public.apiBase}/customer/profile`, {
        headers: { Authorization: `Bearer ${useTokenStore().token}` }
      })
      if (profileData && profileData.user) {
        userLoyaltyBalance.value = profileData.user.loyalty_points || 0
      }
    } catch (e) {
      console.error('Failed to fetch user profile for loyalty balance:', e)
      userLoyaltyBalance.value = authStore.user.loyalty_points || 0
    }
    
    if (authStore.user.customer) {
      const customerInfo = authStore.user.customer
      
      form.value.phone = customerInfo.phone || ''
      isPhoneValid.value = !!form.value.phone
      
      if (customerInfo.last_shipping_address) {
        const address = customerInfo.last_shipping_address
        form.value.first_name = address.first_name || ''
        form.value.last_name = address.last_name || ''
        form.value.full_address = address.address || ''
        
        if (address.city_id) {
            prefillAddress.value = address;
        }
      } else {
        const names = (authStore.user.name || '').split(' ')
        form.value.first_name = names[0] || ''
        form.value.last_name = names.slice(1).join(' ') || ''
      }
    } else {
      const names = (authStore.user.name || '').split(' ')
      form.value.first_name = names[0] || ''
      form.value.last_name = names.slice(1).join(' ') || ''
    }
    
    fetchSavedAddresses()
  }
  
  fetchGateways()
  await fetchStates()
  
  // Ensure we have storefront data (loyalty settings depend on this)
  if (!storefrontStore.loyaltyProgram) {
    try {
      await storefrontStore.fetchStorefront()
    } catch (e) {
      console.error('Failed to fetch storefront in checkout:', e)
    }
  }
  
  if (prefillAddress.value && prefillAddress.value.city_id) {
       try {
           // We need the state_id for this city. Let's fetch the city's details or we can search through states if backend provided a way.
           // Since we don't have a direct endpoint for single city, let's try to get all cities if we can, 
           // but the backend `states` and `cities` endpoints probably don't return all cities at once.
           // Let's modify the backend UserResource to include the state_id.
           if (prefillAddress.value.state_id) {
               form.value.state_id = prefillAddress.value.state_id;
               await fetchCities(form.value.state_id);
               form.value.city_id = prefillAddress.value.city_id;
               await handleCityChange();
           }
       } catch (e) {
           console.error("Failed to preselect city/state", e);
       }
  }

  // Fire InitiateCheckout pixel event
  if (cart.value.length > 0) {
    trackInitiateCheckout({
      total: cartTotal.value,
      itemCount: cart.value.reduce((sum, item) => sum + item.quantity, 0)
    })
  }
})

const validateForm = () => {
  validationErrors.value = {}
  let isValid = true

  if (!form.value.email?.trim()) {
    validationErrors.value.email = 'Email address is required'
    isValid = false
  }
  if (!form.value.first_name?.trim()) {
    validationErrors.value.first_name = 'First name is required'
    isValid = false
  }
  if (!form.value.phone?.trim()) {
    validationErrors.value.phone = 'Phone number is required'
    isValid = false
  } else if (!isPhoneValid.value) {
    validationErrors.value.phone = 'Please enter a valid Bangladesh phone number'
    isValid = false
  }
  if (!form.value.last_name?.trim()) {
    validationErrors.value.last_name = 'Last name is required'
    isValid = false
  }
  if (!form.value.state_id) {
    validationErrors.value.state_id = 'State is required'
    isValid = false
  }
  if (!form.value.city_id) {
    validationErrors.value.city_id = 'City is required'
    isValid = false
  }
  if (!form.value.full_address?.trim()) {
    validationErrors.value.full_address = 'Full address is required'
    isValid = false
  }

  return isValid
}

const placeOrder = async () => {
  if (cart.value.length === 0) return
  if (!validateForm()) {
    const firstError = Object.values(validationErrors.value)[0]
    alert(firstError || 'Please fill in all required fields')
    return
  }

  loading.value = true
  try {
    const orderData = {
      ...form.value,
      payment_method: paymentMethod.value,
      items: cart.value.map(item => ({
        id: item.id,
        quantity: item.quantity
      })),
      coupon_code: couponCode.value
    }

    const response = await $fetch(`${config.public.apiBase}/storefront/checkout`, {
      method: 'POST',
      body: {
        ...orderData,
        use_loyalty_points: useLoyaltyPoints.value
      }
    })

    if (response.success) {
      // Clear cart
      const invoice_number = response.orders && response.orders.length > 0 ? response.orders[0].invoice_number : ''
      
      // Fire Purchase tracking event before clearing cart
      trackPurchase({
        id: invoice_number || Date.now(),
        total: cartTotal.value + shippingCost.value - discountTotal.value,
        items: cart.value.map((item) => ({
          id: item.id,
          name: item.name,
          price: item.price,
          quantity: item.quantity
        }))
      })

      cart.value = []
      // Redirect to a success page
      router.push({
        path: '/thank-you',
        query: { 
          invoice_number: invoice_number,
          earned_points: potentialPoints.value
        }
      })
    }
  } catch (error) {
    console.error('Checkout error:', error)
    alert('Failed to place order. Please try again.')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  opacity: 0;
  animation: fadeIn 0.6s ease-out forwards;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f8fafc;
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

@keyframes shake {
  0%, 100% { transform: translateX(0); }
  25% { transform: translateX(-4px); }
  75% { transform: translateX(4px); }
}

.animate-shake {
  animation: shake 0.2s ease-in-out 0s 2;
}

/* Tel Input Styling */
:deep(.tel-input-custom) {
  border: none !important;
  box-shadow: none !important;
}
:deep(.tel-input-custom .vti__dropdown) {
  border-radius: 0.75rem 0 0 0.75rem !important;
  padding: 0 0.5rem !important;
}
:deep(.tel-input-custom .vti__dropdown:hover) {
  background-color: #f8fafc !important;
}
:deep(.tel-input-custom .vti__input) {
  border-radius: 0 0.75rem 0.75rem 0 !important;
}
</style>
