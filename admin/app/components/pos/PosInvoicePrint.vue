<template>
  <div v-if="isOpen" style="position:fixed;inset:0;z-index:70;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div style="position:absolute;inset:0;background:rgba(15,23,42,0.85);backdrop-filter:blur(4px);" @click="$emit('close')"></div>

    <div style="position:relative;background:#f1f5f9;border-radius:20px;box-shadow:0 25px 60px rgba(0,0,0,0.4);display:flex;flex-direction:column;width:100%;max-width:680px;max-height:92vh;overflow:hidden;">

      <!-- Modal Toolbar -->
      <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:white;border-bottom:1px solid #e2e8f0;flex-shrink:0;">
        <div style="display:flex;align-items:center;gap:10px;">
          <div style="width:36px;height:36px;background:#4f46e5;border-radius:10px;display:flex;align-items:center;justify-content:center;">
            <Printer style="width:18px;height:18px;color:white;" />
          </div>
          <div>
            <div style="font-weight:900;color:#0f172a;font-size:15px;">Invoice Preview</div>
            <div style="font-size:11px;color:#64748b;font-weight:700;margin-top:1px;">
              {{ printType === 'thermal' ? '🧾 Thermal (80mm)' : '📄 A4 Page' }}
            </div>
          </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
          <button @click="doPrint"
            style="display:flex;align-items:center;gap:8px;padding:10px 20px;background:linear-gradient(135deg,#4f46e5,#7c3aed);color:white;border:none;border-radius:12px;font-weight:900;font-size:13px;cursor:pointer;box-shadow:0 4px 15px rgba(79,70,229,0.4);">
            <Printer style="width:15px;height:15px;" />
            Print Now
          </button>
          <button @click="$emit('close')"
            style="width:36px;height:36px;border-radius:50%;background:#f1f5f9;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#64748b;">
            <X style="width:16px;height:16px;" />
          </button>
        </div>
      </div>

      <!-- Paper Preview Area -->
      <div style="flex:1;overflow-y:auto;padding:24px;display:flex;justify-content:center;background:#e2e8f0;">
        <div id="pos-invoice-print-area">

          <!-- ═══════════════════ A4 INVOICE ═══════════════════ -->
          <div v-if="printType !== 'thermal'" style="background:white;width:660px;min-height:900px;box-shadow:0 4px 30px rgba(0,0,0,0.15);font-family:'Segoe UI',Arial,sans-serif;overflow:hidden;">

            <!-- Accent Header Bar -->
            <div style="background:linear-gradient(135deg,#1e1b4b,#312e81);padding:28px 36px;display:flex;justify-content:space-between;align-items:flex-start;">
              <div>
                <img v-if="shopSettings.logo" :src="shopSettings.logo" style="height:44px;object-fit:contain;margin-bottom:10px;display:block;" />
                <div style="color:white;font-size:22px;font-weight:900;letter-spacing:-0.5px;">{{ shopSettings.name || 'Your Shop' }}</div>
                <div v-if="shopSettings.address || shopSettings.city" style="color:rgba(255,255,255,0.65);font-size:12px;margin-top:4px;">{{ shopSettings.address }}{{ shopSettings.city ? ', ' + shopSettings.city : '' }}</div>
                <div v-if="shopSettings.phone" style="color:rgba(255,255,255,0.65);font-size:12px;margin-top:2px;">{{ shopSettings.phone }}</div>
                <div v-if="shopSettings.email" style="color:rgba(255,255,255,0.65);font-size:12px;margin-top:2px;">{{ shopSettings.email }}</div>
              </div>
              <div style="text-align:right;">
                <div style="color:rgba(255,255,255,0.4);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:2px;margin-bottom:4px;">Invoice</div>
                <div style="color:white;font-size:28px;font-weight:900;letter-spacing:-1px;">#{{ order.reference }}</div>
                <div style="color:rgba(255,255,255,0.65);font-size:12px;margin-top:8px;">{{ orderDate }}</div>
              </div>
            </div>

            <!-- Body -->
            <div style="padding:36px;">

              <!-- Customer + Status Row -->
              <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;">
                <div>
                  <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:2px;color:#94a3b8;margin-bottom:6px;">Billed To</div>
                  <div style="font-size:18px;font-weight:900;color:#0f172a;">{{ order.customerName || 'Walk-in Customer' }}</div>
                </div>
                <div style="text-align:right;">
                  <div style="display:inline-block;background:#dcfce7;color:#16a34a;padding:6px 16px;border-radius:999px;font-size:12px;font-weight:900;text-transform:uppercase;letter-spacing:1px;">✓ Paid</div>
                  <div style="margin-top:8px;font-size:12px;color:#64748b;font-weight:700;text-transform:capitalize;">{{ order.paymentMethod || 'Cash' }}</div>
                </div>
              </div>

              <!-- Items Table -->
              <table style="width:100%;border-collapse:collapse;margin-bottom:24px;">
                <thead>
                  <tr style="background:#f8fafc;">
                    <th style="padding:12px 16px;text-align:left;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:1.5px;color:#64748b;border-bottom:2px solid #e2e8f0;">#</th>
                    <th style="padding:12px 16px;text-align:left;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:1.5px;color:#64748b;border-bottom:2px solid #e2e8f0;">Product</th>
                    <th style="padding:12px 16px;text-align:center;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:1.5px;color:#64748b;border-bottom:2px solid #e2e8f0;">Qty</th>
                    <th style="padding:12px 16px;text-align:right;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:1.5px;color:#64748b;border-bottom:2px solid #e2e8f0;">Price</th>
                    <th style="padding:12px 16px;text-align:right;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:1.5px;color:#64748b;border-bottom:2px solid #e2e8f0;">Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, i) in order.cart" :key="item.id" :style="{ background: i % 2 === 0 ? 'white' : '#f8fafc' }">
                    <td style="padding:14px 16px;font-size:12px;color:#94a3b8;font-weight:600;border-bottom:1px solid #f1f5f9;">{{ i + 1 }}</td>
                    <td style="padding:14px 16px;border-bottom:1px solid #f1f5f9;">
                      <div style="font-size:14px;font-weight:700;color:#0f172a;">{{ item.name }}</div>
                      <div v-if="item.sku" style="font-size:11px;color:#94a3b8;margin-top:2px;">{{ item.sku }}</div>
                    </td>
                    <td style="padding:14px 16px;text-align:center;font-size:13px;font-weight:700;color:#475569;border-bottom:1px solid #f1f5f9;">{{ item.qty }}</td>
                    <td style="padding:14px 16px;text-align:right;font-size:13px;font-weight:700;color:#475569;border-bottom:1px solid #f1f5f9;">{{ currencySymbol }}{{ Number(item.price).toFixed(2) }}</td>
                    <td style="padding:14px 16px;text-align:right;font-size:14px;font-weight:900;color:#0f172a;border-bottom:1px solid #f1f5f9;">{{ currencySymbol }}{{ (item.price * item.qty).toFixed(2) }}</td>
                  </tr>
                </tbody>
              </table>

              <!-- Totals -->
              <div style="display:flex;justify-content:flex-end;">
                <div style="width:260px;">
                  <div v-if="order.discountAmount > 0" style="display:flex;justify-content:space-between;padding:6px 0;font-size:13px;font-weight:600;color:#64748b;">
                    <span>Discount</span><span style="color:#ef4444;">-{{ currencySymbol }}{{ Number(order.discountAmount).toFixed(2) }}</span>
                  </div>
                  <div v-if="order.taxAmount > 0" style="display:flex;justify-content:space-between;padding:6px 0;font-size:13px;font-weight:600;color:#64748b;">
                    <span>Tax ({{ order.taxPercentage }}%)</span><span>+{{ currencySymbol }}{{ Number(order.taxAmount).toFixed(2) }}</span>
                  </div>
                  <div v-if="order.shipping > 0" style="display:flex;justify-content:space-between;padding:6px 0;font-size:13px;font-weight:600;color:#64748b;">
                    <span>Shipping</span><span>+{{ currencySymbol }}{{ Number(order.shipping).toFixed(2) }}</span>
                  </div>
                  <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 20px;background:linear-gradient(135deg,#1e1b4b,#312e81);border-radius:12px;margin-top:8px;">
                    <span style="color:rgba(255,255,255,0.8);font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:1px;">Total</span>
                    <span style="color:white;font-size:20px;font-weight:900;">{{ currencySymbol }}{{ Number(order.total).toFixed(2) }}</span>
                  </div>
                </div>
              </div>

              <!-- Footer -->
              <div style="margin-top:48px;padding-top:24px;border-top:1px dashed #e2e8f0;display:flex;justify-content:space-between;align-items:center;">
                <div style="font-size:12px;color:#94a3b8;font-weight:600;">
                  Thank you for shopping with us! 🎉
                </div>
                <div style="font-size:11px;color:#cbd5e1;font-weight:600;">{{ shopSettings.name }}</div>
              </div>
            </div>
          </div>

          <!-- ═══════════════════ THERMAL RECEIPT ═══════════════════ -->
          <div v-else style="background:white;width:302px;font-family:'Courier New',Courier,monospace;box-shadow:0 4px 30px rgba(0,0,0,0.2);">

            <!-- Shop Header -->
            <div style="text-align:center;padding:18px 14px 14px;border-bottom:2px dashed #374151;">
              <img v-if="shopSettings.logo" :src="shopSettings.logo" style="height:36px;object-fit:contain;display:block;margin:0 auto 8px;" />
              <div style="font-size:16px;font-weight:900;color:#111827;letter-spacing:0.5px;">{{ shopSettings.name || 'SHOP NAME' }}</div>
              <div v-if="shopSettings.address" style="font-size:10px;color:#6b7280;margin-top:3px;">{{ shopSettings.address }}</div>
              <div v-if="shopSettings.phone" style="font-size:10px;color:#6b7280;">{{ shopSettings.phone }}</div>
              <div v-if="shopSettings.email" style="font-size:10px;color:#6b7280;">{{ shopSettings.email }}</div>
            </div>

            <!-- Order Info -->
            <div style="padding:10px 14px;border-bottom:1px dashed #d1d5db;font-size:11px;">
              <div style="display:flex;justify-content:space-between;padding:2px 0;">
                <span style="color:#6b7280;font-weight:700;">REF</span>
                <span style="font-weight:900;color:#111827;">{{ order.reference }}</span>
              </div>
              <div style="display:flex;justify-content:space-between;padding:2px 0;">
                <span style="color:#6b7280;font-weight:700;">DATE</span>
                <span style="font-weight:700;color:#374151;">{{ orderDateShort }}</span>
              </div>
              <div style="display:flex;justify-content:space-between;padding:2px 0;">
                <span style="color:#6b7280;font-weight:700;">CUSTOMER</span>
                <span style="font-weight:700;color:#374151;">{{ (order.customerName || 'Walk-in').substring(0,18) }}</span>
              </div>
              <div style="display:flex;justify-content:space-between;padding:2px 0;">
                <span style="color:#6b7280;font-weight:700;">PAYMENT</span>
                <span style="font-weight:700;color:#374151;text-transform:capitalize;">{{ order.paymentMethod || 'Cash' }}</span>
              </div>
            </div>

            <!-- Items -->
            <div style="padding:10px 14px;border-bottom:1px dashed #d1d5db;">
              <div style="display:flex;justify-content:space-between;font-size:10px;font-weight:900;text-transform:uppercase;letter-spacing:1px;color:#6b7280;padding-bottom:6px;border-bottom:1px solid #e5e7eb;margin-bottom:4px;">
                <span style="flex:1;">Item</span>
                <span style="width:28px;text-align:center;">Qty</span>
                <span style="width:60px;text-align:right;">Price</span>
                <span style="width:60px;text-align:right;">Total</span>
              </div>
              <div v-for="item in order.cart" :key="item.id" style="padding:5px 0;border-bottom:1px dotted #f3f4f6;">
                <div style="font-size:11px;font-weight:900;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:270px;">{{ item.name }}</div>
                <div style="display:flex;justify-content:space-between;font-size:11px;margin-top:2px;">
                  <span style="flex:1;"></span>
                  <span style="width:28px;text-align:center;color:#374151;font-weight:700;">{{ item.qty }}</span>
                  <span style="width:60px;text-align:right;color:#374151;">{{ currencySymbol }}{{ Number(item.price).toFixed(0) }}</span>
                  <span style="width:60px;text-align:right;font-weight:900;color:#111827;">{{ currencySymbol }}{{ (item.price * item.qty).toFixed(0) }}</span>
                </div>
              </div>
            </div>

            <!-- Totals -->
            <div style="padding:10px 14px;border-bottom:2px dashed #374151;font-size:11px;">
              <div style="display:flex;justify-content:space-between;padding:2px 0;color:#6b7280;">
                <span>Subtotal</span><span>{{ currencySymbol }}{{ Number(order.subtotal).toFixed(2) }}</span>
              </div>
              <div v-if="order.discountAmount > 0" style="display:flex;justify-content:space-between;padding:2px 0;color:#ef4444;">
                <span>Discount</span><span>-{{ currencySymbol }}{{ Number(order.discountAmount).toFixed(2) }}</span>
              </div>
              <div v-if="order.taxAmount > 0" style="display:flex;justify-content:space-between;padding:2px 0;color:#6b7280;">
                <span>Tax</span><span>+{{ currencySymbol }}{{ Number(order.taxAmount).toFixed(2) }}</span>
              </div>
              <div v-if="order.shipping > 0" style="display:flex;justify-content:space-between;padding:2px 0;color:#6b7280;">
                <span>Shipping</span><span>+{{ currencySymbol }}{{ Number(order.shipping).toFixed(2) }}</span>
              </div>
              <div style="display:flex;justify-content:space-between;padding:8px 0 2px;font-size:15px;font-weight:900;color:#111827;border-top:1px solid #e5e7eb;margin-top:6px;">
                <span>TOTAL</span><span>{{ currencySymbol }}{{ Number(order.total).toFixed(2) }}</span>
              </div>
            </div>

            <!-- Footer -->
            <div style="text-align:center;padding:16px 14px 20px;font-size:11px;color:#6b7280;">
              <div style="font-size:15px;font-weight:900;color:#111827;margin-bottom:4px;">✦ Thank You! ✦</div>
              <div>Please visit again</div>
              <div style="margin-top:10px;letter-spacing:4px;font-size:10px;color:#d1d5db;">* * * * * * * * * * *</div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, nextTick, ref, watch } from 'vue'
import { X, Printer } from 'lucide-vue-next'
import useCrud from '~/composables/useCrud'

const props = defineProps({
  isOpen:    { type: Boolean, default: false },
  order:     { type: Object,  default: () => ({}) },
  autoPrint: { type: Boolean, default: false },
})

defineEmits(['close'])

const { getAll } = useCrud()
const shopSettings = ref({})
const printType    = ref('a4')

const orderDate = computed(() => {
  return new Date().toLocaleDateString('en-GB', {
    day: 'numeric', month: 'long', year: 'numeric',
    hour: '2-digit', minute: '2-digit'
  })
})

const orderDateShort = computed(() => {
  return new Date().toLocaleDateString('en-GB', {
    day: '2-digit', month: 'short', year: 'numeric',
    hour: '2-digit', minute: '2-digit'
  })
})

const currencySymbol = computed(() => {
  const currency = shopSettings.value.currency || 'BDT'
  return currency === 'USD' ? '$' : '৳'
})

const loadShopSettings = async () => {
  try {
    const res = await getAll('/vendor/settings', { group: 'shop_settings' })
    shopSettings.value = res.data || {}
    printType.value = shopSettings.value.printType || 'a4'
  } catch (e) {
    console.error('Failed to load shop settings for invoice', e)
  } finally {
    if (props.autoPrint) {
      await nextTick()
      doPrint()
    }
  }
}

watch(() => props.isOpen, (val) => {
  if (val) loadShopSettings()
})

const doPrint = () => {
  const printArea = document.getElementById('pos-invoice-print-area')
  if (!printArea) return

  const isThermal = printType.value === 'thermal'
  const pageSize  = isThermal ? '80mm auto' : 'A4'

  // 1. Inject print CSS
  const styleEl = document.createElement('style')
  styleEl.id = 'pos-invoice-print-css'
  styleEl.textContent = `
    @media print {
      body > * { display: none !important; }
      #pos-print-portal {
        display: block !important;
        position: fixed; inset: 0;
        background: white;
        z-index: 99999;
        padding: ${isThermal ? '0' : '0'};
      }
      @page { margin: 0; size: ${pageSize}; }
    }
  `
  document.head.appendChild(styleEl)

  // 2. Clone invoice into temp portal
  const portal = document.createElement('div')
  portal.id = 'pos-print-portal'
  portal.style.cssText = 'display:none;'
  portal.appendChild(printArea.cloneNode(true))
  document.body.appendChild(portal)

  // 3. Print
  window.print()

  // 4. Clean up
  document.body.removeChild(portal)
  document.head.removeChild(styleEl)
}
</script>
