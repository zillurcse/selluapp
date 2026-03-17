<template>
  <div class="p-6 lg:p-12 bg-slate-50 dark:bg-[#0a0c10] min-h-screen transition-colors duration-500">
    <!-- Sophisticated Header -->
    <div class="max-w-5xl mx-auto mb-12">
      <div class="flex items-center gap-2 text-[11px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-[0.2em] mb-4">
        <NuxtLink to="/vendor/managed-shop" class="hover:text-indigo-600 transition-colors">Managed Shop</NuxtLink>
        <div class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-700"></div>
        <span class="text-slate-900 dark:text-white">Email Communications</span>
      </div>
      
      <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-2">
          <h1 class="text-4xl font-black text-slate-900 dark:text-white tracking-tight">Email Library</h1>
          <p class="text-slate-500 dark:text-slate-400 font-medium max-w-xl leading-relaxed text-sm">
            Personalize every touchpoint of your customer's journey. Choose from our comprehensive library of automated email templates.
          </p>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="pending" class="flex justify-center items-center py-32">
      <div class="w-10 h-10 border-[3px] border-indigo-600 dark:border-indigo-400 border-t-transparent rounded-full animate-spin"></div>
    </div>
    
    <div v-else class="max-w-5xl mx-auto space-y-8">
      <!-- Sophisticated Tab Switcher -->
      <div class="flex flex-wrap gap-2 p-1.5 bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-800/60 rounded-[24px] shadow-sm">
        <button 
          v-for="tab in tabs" 
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'px-6 py-3 text-xs font-black rounded-xl transition-all duration-500 flex items-center gap-2.5',
            activeTab === tab.id 
              ? 'bg-slate-950 dark:bg-indigo-600 text-white shadow-xl shadow-slate-950/10' 
              : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'
          ]"
        >
          <component :is="tab.icon" class="w-4 h-4" />
          {{ tab.label }}
        </button>
      </div>

      <!-- Templates Section -->
      <div class="grid grid-cols-1 gap-10">
        <TransitionGroup name="fade-slide">
          <div 
            v-for="type in filteredTemplates" 
            :key="type.key" 
            class="group bg-white dark:bg-slate-900 rounded-[32px] border border-slate-200/60 dark:border-slate-800/60 shadow-sm overflow-hidden transition-all duration-500 hover:shadow-2xl hover:shadow-slate-200/50 dark:hover:shadow-indigo-500/5"
          >
            <!-- Card Header -->
            <div class="p-8 border-b border-slate-50 dark:border-slate-800/50 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-slate-50/30 dark:bg-slate-800/10">
              <div class="flex items-start gap-5">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center transition-all duration-500 group-hover:rotate-3 shadow-sm bg-indigo-600 text-white">
                  <component :is="type.icon" class="w-7 h-7" />
                </div>
                <div class="space-y-0.5">
                  <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">{{ type.label }}</h3>
                  <p class="text-xs font-bold text-slate-400 tracking-wide uppercase">{{ type.description }}</p>
                </div>
              </div>
              
              <div class="flex items-center gap-4">
                <div class="flex items-center gap-3 px-4 py-2 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
                  <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Active</span>
                  <button 
                    @click="toggleActive(type.key)"
                    :class="[
                      'w-10 h-5 rounded-full transition-all duration-500 relative',
                      templates[type.key]?.is_active ? 'bg-indigo-600' : 'bg-slate-200 dark:bg-slate-700'
                    ]"
                  >
                    <div :class="[
                      'w-3.5 h-3.5 bg-white rounded-full absolute top-0.75 transition-all duration-500 shadow-sm',
                      templates[type.key]?.is_active ? 'left-5.5' : 'left-1'
                    ]"></div>
                  </button>
                </div>
                
                <button 
                  @click="saveTemplate(type.key)" 
                  :disabled="saving[type.key]"
                  class="h-11 px-6 bg-slate-950 dark:bg-indigo-600 text-white text-[12px] font-black rounded-xl hover:scale-105 active:scale-95 transition-all duration-500 disabled:opacity-50 flex items-center gap-2 shadow-lg shadow-slate-950/10 dark:shadow-indigo-500/10"
                >
                  <Save v-if="!saving[type.key]" class="w-4 h-4" />
                  <div v-else class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                  {{ saving[type.key] ? 'Updating...' : 'Save Changes' }}
                </button>

                <button 
                  @click="openTestModal(type.key)" 
                  class="h-11 px-6 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-[12px] font-black rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-500 flex items-center gap-2 shadow-sm"
                >
                  <Send class="w-4 h-4 text-indigo-500" />
                  <span>Test Email</span>
                </button>
              </div>
            </div>

            <!-- Card Body -->
            <div class="p-8 space-y-8">
              <!-- Subject Field -->
              <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.1em]">Email Subject</label>
                <input 
                  v-model="templates[type.key].subject"
                  type="text"
                  class="w-full h-12 px-6 bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800 rounded-xl focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500/30 outline-none transition-all duration-500 font-semibold text-slate-700 dark:text-slate-200"
                  placeholder="Enter email subject"
                />
              </div>

              <!-- Tags Helper -->
              <div class="space-y-3">
                <div class="flex items-center justify-between">
                  <label class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.1em]">Dynamic Tokens</label>
                  <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold text-slate-400">Click to insert at cursor</span>
                    <HelpCircle class="w-3 h-3 text-slate-300 pointer-events-none" />
                  </div>
                </div>
                <div class="flex flex-wrap gap-2.5">
                  <button 
                    v-for="tag in type.tags" 
                    :key="tag" 
                    @click="insertTag(type.key, tag)" 
                    class="px-3 py-1.5 bg-white dark:bg-slate-800 text-[11px] font-bold text-slate-600 dark:text-slate-300 rounded-lg border border-slate-100 dark:border-slate-700 hover:border-indigo-500 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50/30 dark:hover:bg-indigo-500/10 transition-all duration-300 flex items-center gap-1.5"
                  >
                    <div class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-600 group-hover:bg-indigo-400"></div>
                    {{ tag }}
                  </button>
                </div>
              </div>

              <!-- Editor -->
            <div class="relative group/editor">
              <AppRichEditor 
                v-model="templates[type.key].content"
                :ref="el => editorRefs[type.key] = el"
                placeholder="Design your email content here..."
              />
              
              <!-- Reset Overlay -->
                <div class="absolute bottom-4 right-4">
                  <button 
                    @click="resetToDefault(type.key)"
                    class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg transition-all duration-300"
                    title="Reset to default"
                  >
                    <RotateCcw class="w-4 h-4" />
                  </button>
                </div>
              </div>

              <!-- Preview Section -->
              <div class="pt-8 border-t border-slate-50 dark:border-slate-800">
                 <div class="flex items-center gap-2 mb-4">
                   <div class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></div>
                   <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest">Live Experience Preview</span>
                 </div>
                 <div class="bg-indigo-50/50 dark:bg-indigo-500/5 p-8 rounded-[24px] border border-dashed border-indigo-200 dark:border-indigo-800/50">
                    <div v-html="renderPreview(type.key)" class="prose dark:prose-invert max-w-none text-slate-600 dark:text-slate-300 overflow-auto max-h-[400px]"></div>
                 </div>
              </div>
            </div>
          </div>
        </TransitionGroup>
      </div>
    </div>

    <!-- Test Modal -->
    <div v-if="testModal.show" class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-slate-950/40 backdrop-blur-sm">
      <div class="bg-white dark:bg-slate-900 w-full max-w-md rounded-[32px] border border-slate-200 dark:border-slate-800 shadow-2xl overflow-hidden p-8 space-y-6">
        <div class="space-y-2">
          <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">Test Email Template</h3>
          <p class="text-sm font-bold text-slate-500 dark:text-slate-400">Enter an email address to receive a test message for <span class="text-indigo-600">{{ testModal.type.toUpperCase().replace('_', ' ') }}</span></p>
        </div>

        <div class="space-y-4">
          <div class="relative group">
            <Mail class="absolute left-6 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors" />
            <input 
              v-model="testModal.email" 
              type="email" 
              placeholder="customer@example.com" 
              class="w-full h-14 pl-14 pr-6 bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 dark:text-slate-200"
            />
          </div>

          <div class="flex items-center gap-4 pt-2">
            <button 
              @click="testModal.show = false" 
              class="flex-1 h-12 text-sm font-black text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 rounded-xl transition-all"
            >
              Cancel
            </button>
            <button 
              @click="sendTestEmail" 
              :disabled="testModal.sending || !testModal.email"
              class="flex-2 h-12 px-8 bg-indigo-600 text-white text-sm font-black rounded-xl hover:bg-indigo-700 active:scale-95 transition-all disabled:opacity-50 flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/20"
            >
              <div v-if="testModal.sending" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
              <Send v-else class="w-4 h-4" />
              <span>{{ testModal.sending ? 'Sending...' : 'Send Test' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  Save, 
  RotateCcw,
  HelpCircle,
  Send,
  Mail,
  UserPlus,
  ShoppingBag,
  Truck,
  FileCheck,
  CreditCard,
  RefreshCcw,
  UserCheck,
  Key,
  MailWarning,
  Flame,
  Newspaper,
  Star,
  Gift,
  Coins,
  ShieldCheck,
  AlertCircle
} from 'lucide-vue-next'
import { ref, reactive, onMounted, computed } from 'vue'

const { $toast } = useNuxtApp()
const { getAll, createItem } = useCrud()

const pending = ref(true)
const activeTab = ref('ecommerce')
const saving = reactive({})
const editorRefs = reactive({})
const testModal = reactive({
  show: false,
  type: '',
  email: '',
  sending: false
})

const tabs = [
  { id: 'ecommerce', label: 'Ecommerce', icon: ShoppingBag },
  { id: 'account', label: 'Account', icon: UserCheck },
  { id: 'marketing', label: 'Marketing', icon: Flame },
  { id: 'admin', label: 'Admin & Support', icon: ShieldCheck }
]

const emailHeader = `
<div style="font-family: 'Inter', system-ui, -apple-system, sans-serif; background-color: #f8fafc; padding: 40px 20px;">
  <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);">
    <div style="background-color: #4f46e5; padding: 40px; text-align: center;">
      <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 900; letter-spacing: -0.025em; text-transform: uppercase;">{{ shop_name }}</h1>
    </div>
    <div style="padding: 40px; color: #1e293b;">
`

const emailFooter = `
    </div>
    <div style="padding: 32px; background-color: #f1f5f9; text-align: center; color: #64748b; font-size: 13px; font-weight: 500;">
      <p style="margin: 0 0 10px 0;">© 2026 {{ shop_name }}. Professional E-commerce Solutions.</p>
      <div style="margin-top: 15px;">
        <a href="#" style="color: #4f46e5; text-decoration: none; font-weight: 700; margin: 0 10px;">Visit Shop</a>
        <span style="color: #cbd5e1;">•</span>
        <a href="#" style="color: #4f46e5; text-decoration: none; font-weight: 700; margin: 0 10px;">Support</a>
      </div>
      <p style="margin-top: 20px; color: #94a3b8; font-size: 11px;">You received this because you are a valued customer of {{ shop_name }}.</p>
    </div>
  </div>
</div>
`

const templateTypes = [
  // Ecommerce
  { 
    key: 'order_confirmation', tab: 'ecommerce', label: 'Order Confirmation', description: 'Sent immediately after a successful order.',
    icon: ShoppingBag, tags: ['{{ first_name }}', '{{ order_id }}', '{{ amount }}', '{{ shop_name }}'],
    defaultSubject: 'Great news! Your order {{ order_id }} is confirmed',
    defaultContent: emailHeader + `
      <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-top: 0;">Thank you for your purchase!</h2>
      <p style="font-size: 16px; line-height: 1.6; margin-bottom: 24px;">Hi {{ first_name }}, we've received your order <strong>{{ order_id }}</strong> and our team is already getting it ready for you.</p>
      <div style="background-color: #f8fafc; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0; margin-bottom: 24px;">
        <h3 style="margin: 0 0 8px 0; font-size: 14px; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em;">Order Summary</h3>
        <p style="font-size: 24px; font-weight: 800; color: #4f46e5; margin: 0;">{{ amount }}</p>
      </div>
      <p style="font-size: 16px; line-height: 1.6;">You'll receive another email with a tracking number as soon as your package ships.</p>
      <div style="margin-top: 32px; text-align: center;">
        <a href="#" style="background-color: #4f46e5; color: #ffffff; padding: 16px 32px; border-radius: 12px; font-weight: 700; text-decoration: none; display: inline-block;">View Order Status</a>
      </div>
    ` + emailFooter
  },
  { 
    key: 'payment_confirmation', tab: 'ecommerce', label: 'Payment Confirmation', description: 'Sent after payment is successfully verified.',
    icon: CreditCard, tags: ['{{ first_name }}', '{{ order_id }}', '{{ amount }}'],
    defaultSubject: 'Payment Successful - Order {{ order_id }}',
    defaultContent: emailHeader + `
      <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-top: 0;">Payment Verified!</h2>
      <p style="font-size: 16px; line-height: 1.6; margin-bottom: 24px;">Hi {{ first_name }}, your payment of <strong>{{ amount }}</strong> for order {{ order_id }} has been successfully processed. Thank you for your prompt payment!</p>
      <div style="padding: 24px; border-left: 4px solid #10b981; background-color: #f0fdf4; border-radius: 0 12px 12px 0;">
        <p style="margin: 0; color: #065f46; font-weight: 600;">Transaction Status: Secured & Paid</p>
      </div>
    ` + emailFooter
  },
  { 
    key: 'order_processing', tab: 'ecommerce', label: 'Order Processing', description: 'Sent when the order starts being processed.',
    icon: RefreshCcw, tags: ['{{ first_name }}', '{{ order_id }}'],
    defaultSubject: 'Your order {{ order_id }} is moving along',
    defaultContent: emailHeader + `
      <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-top: 0;">We're on it!</h2>
      <p style="font-size: 16px; line-height: 1.6; margin-bottom: 24px;">Hi {{ first_name }}, your order {{ order_id }} is currently being picked and packed with care by our fulfillment team.</p>
      <img src="https://images.unsplash.com/photo-1586528116311-ad8693cc560a?auto=format&fit=crop&w=600&h=200" style="width: 100%; border-radius: 12px; margin-bottom: 24px;" alt="Order Processing" />
      <p style="font-size: 16px; line-height: 1.6;">We'll notify you the moment it leaves our warehouse.</p>
    ` + emailFooter
  },
  { 
    key: 'order_shipped', tab: 'ecommerce', label: 'Order Shipped', description: 'Sent when the order is handed to the courier.',
    icon: Truck, tags: ['{{ first_name }}', '{{ order_id }}', '{{ courier }}', '{{ tracking_id }}'],
    defaultSubject: 'Exciting news! Your order {{ order_id }} has shipped',
    defaultContent: emailHeader + `
      <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-top: 0;">Your Package is on the Way!</h2>
      <p style="font-size: 16px; line-height: 1.6; margin-bottom: 24px;">Hi {{ first_name }}, your order {{ order_id }} has been handed over to <strong>{{ courier }}</strong>.</p>
      <div style="background-color: #f8fafc; border-radius: 12px; padding: 24px; border: 1px solid #e2e8f0; margin-bottom: 24px;">
        <p style="margin: 0 0 4px 0; color: #64748b; font-size: 12px; font-weight: 700; text-transform: uppercase;">Tracking ID</p>
        <p style="margin: 0; font-size: 18px; font-weight: 800; color: #0f172a;">{{ tracking_id }}</p>
      </div>
      <div style="text-align: center;">
        <a href="#" style="background-color: #4f46e5; color: #ffffff; padding: 16px 32px; border-radius: 12px; font-weight: 700; text-decoration: none; display: inline-block;">Track Your Package</a>
      </div>
    ` + emailFooter
  },
  { 
    key: 'order_delivered', tab: 'ecommerce', label: 'Order Delivered', description: 'Sent when the order is successfully delivered.',
    icon: FileCheck, tags: ['{{ first_name }}', '{{ order_id }}'],
    defaultSubject: 'Delivered: Order {{ order_id }} has arrived!',
    defaultContent: emailHeader + `
      <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-top: 0;">Package Delivered!</h2>
      <p style="font-size: 16px; line-height: 1.6; margin-bottom: 24px;">Hi {{ first_name }}, our records show that order {{ order_id }} was successfully delivered. We hope you love your new items!</p>
      <div style="background-color: #e0e7ff; border-radius: 12px; padding: 24px; text-align: center;">
        <p style="margin: 0; color: #3730a3; font-weight: 700; font-size: 16px;">We'd love to hear your feedback!</p>
        <div style="margin-top: 16px; font-size: 24px;">⭐⭐⭐⭐⭐</div>
      </div>
    ` + emailFooter
  },
  { 
    key: 'order_cancelled', tab: 'ecommerce', label: 'Order Cancelled', description: 'Sent if an order is cancelled.',
    icon: AlertCircle, tags: ['{{ first_name }}', '{{ order_id }}'],
    defaultSubject: 'Notification: Order {{ order_id }} has been cancelled',
    defaultContent: emailHeader + `
      <h2 style="font-size: 22px; font-weight: 800; color: #ef4444; margin-top: 0;">Order Cancelled</h2>
      <p style="font-size: 16px; line-height: 1.6; margin-bottom: 24px;">Hi {{ first_name }}, we're writing to confirm that order {{ order_id }} has been cancelled. If payment was already made, a refund will be processed shortly.</p>
      <p style="font-size: 14px; color: #64748b;">If you didn't request this cancellation, please contact our support team immediately.</p>
    ` + emailFooter
  },

  // Account
  { 
    key: 'welcome_email', tab: 'account', label: 'Welcome Email', description: 'Sent immediately after account creation.',
    icon: UserPlus, tags: ['{{ first_name }}', '{{ shop_name }}', '{{ email }}'],
    defaultSubject: 'Welcome to the Family! 🚀',
    defaultContent: emailHeader + `
      <h2 style="font-size: 24px; font-weight: 900; color: #0f172a; margin-top: 0;">Glad to have you here, {{ first_name }}!</h2>
      <p style="font-size: 16px; line-height: 1.7; color: #475569; margin-bottom: 24px;">You've officially joined <strong>{{ shop_name }}</strong>. We're dedicated to bringing you the best products and a seamless shopping experience.</p>
      <div style="background-color: #f1f5f9; border-radius: 16px; padding: 32px; margin-bottom: 24px;">
        <h3 style="margin: 0 0 12px 0; font-size: 14px; font-weight: 800; color: #64748b; text-transform: uppercase;">Quick Stats</h3>
        <p style="margin: 0; font-size: 15px; font-weight: 600; color: #0f172a;">Account Email: {{ email }}</p>
        <p style="margin: 4px 0 0 0; font-size: 15px; font-weight: 600; color: #0f172a;">Member Since: 2026</p>
      </div>
      <div style="text-align: center;">
        <a href="#" style="background-color: #4f46e5; color: #ffffff; padding: 18px 40px; border-radius: 12px; font-weight: 800; text-decoration: none; display: inline-block; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);">Start Exploring</a>
      </div>
    ` + emailFooter
  },
  { 
    key: 'password_reset', tab: 'account', label: 'Password Reset', description: 'Sent when a user requests a password reset.',
    icon: Key, tags: ['{{ first_name }}', '{{ reset_link }}'],
    defaultSubject: 'Secure Password Reset Request',
    defaultContent: emailHeader + `
      <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-top: 0;">Password Reset</h2>
      <p style="font-size: 16px; line-height: 1.6; margin-bottom: 24px;">Hi {{ first_name }}, we received a request to reset the password for your account. If you made this request, click the button below:</p>
      <div style="text-align: center; margin-bottom: 24px;">
        <a href="{{ reset_link }}" style="background-color: #slate-900; color: #ffffff; background-color: #0f172a; padding: 16px 32px; border-radius: 12px; font-weight: 700; text-decoration: none; display: inline-block;">Reset Password</a>
      </div>
      <p style="font-size: 13px; color: #94a3b8; text-align: center;">This link will expire in 60 minutes. If you did not request a password reset, no further action is required.</p>
    ` + emailFooter
  },

  // Marketing
  { 
    key: 'abandoned_cart', tab: 'marketing', label: 'Abandoned Cart', description: 'Sent to customers who left items in their cart.',
    icon: ShoppingBag, tags: ['{{ first_name }}', '{{ shop_name }}'],
    defaultSubject: '🛒 Still thinking about it? We saved your cart!',
    defaultContent: emailHeader + `
      <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-top: 0;">Don't let them get away!</h2>
      <p style="font-size: 16px; line-height: 1.6; margin-bottom: 24px;">Hi {{ first_name }}, we noticed you left some great items in your shopping cart. They're still waiting for you, but we can't hold them forever!</p>
      <div style="text-align: center; margin-bottom: 32px;">
         <div style="font-size: 48px; margin-bottom: 16px;">📦</div>
         <a href="#" style="background-color: #ef4444; color: #ffffff; padding: 16px 32px; border-radius: 12px; font-weight: 700; text-decoration: none; display: inline-block;">Checkout Now</a>
      </div>
      <p style="text-align: center; font-size: 14px; font-weight: 600; color: #4f46e5;">Limited Time: Use code SAVE10 for 10% OFF!</p>
    ` + emailFooter
  },

  // Marketing Continued
  { 
    key: 'wishlist_reminder', tab: 'marketing', label: 'Wishlist Reminder', description: 'Remind customers of items they liked.',
    icon: Star, tags: ['{{ first_name }}', '{{ shop_name }}'],
    defaultSubject: 'Your favorites are waiting! ✨',
    defaultContent: emailHeader + `
      <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-top: 0;">Thinking about these?</h2>
      <p style="font-size: 16px; line-height: 1.6; margin-bottom: 24px;">Hi {{ first_name }}, the items in your wishlist at <strong>{{ shop_name }}</strong> are still here! Treat yourself today with a little something special.</p>
      <div style="text-align: center; margin-bottom: 24px;">
        <a href="#" style="background-color: #4f46e5; color: #ffffff; padding: 16px 32px; border-radius: 12px; font-weight: 700; text-decoration: none; display: inline-block;">View My Wishlist</a>
      </div>
    ` + emailFooter
  },
  { 
    key: 'back_in_stock', tab: 'marketing', label: 'Back in Stock', description: 'Notify customers when a product is available again.',
    icon: Flame, tags: ['{{ first_name }}', '{{ product_name }}'],
    defaultSubject: 'It\'s Back! {{ product_name }} is now in stock',
    defaultContent: emailHeader + `
      <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-top: 0;">Look what's back!</h2>
      <p style="font-size: 16px; line-height: 1.6; margin-bottom: 24px;">Hi {{ first_name }}, the <strong>{{ product_name }}</strong> you were looking for is now back in stock and ready to ship!</p>
      <img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=600&h=250" style="width: 100%; border-radius: 12px; margin-bottom: 24px;" alt="Product Back in Stock" />
      <div style="text-align: center;">
        <a href="#" style="background-color: #0f172a; color: #ffffff; padding: 16px 32px; border-radius: 12px; font-weight: 700; text-decoration: none; display: inline-block;">Buy It Now</a>
      </div>
    ` + emailFooter
  },
  { 
    key: 'promotional_campaign', tab: 'marketing', label: 'Promotional Campaign', description: 'General marketing and sales emails.',
    icon: Gift, tags: ['{{ first_name }}', '{{ discount_code }}', '{{ shop_name }}'],
    defaultSubject: 'Exclusive Inside: Your special offer from {{ shop_name }}',
    defaultContent: emailHeader + `
      <h2 style="font-size: 26px; font-weight: 900; color: #4f46e5; margin-top: 0; text-align: center;">VIP OFFER JUST FOR YOU</h2>
      <p style="font-size: 16px; line-height: 1.6; margin-bottom: 24px; text-align: center;">Hi {{ first_name }}, we're giving you early access to our latest collection with an exclusive discount.</p>
      <div style="background-color: #f1f5f9; border: 2px dashed #cbd5e1; border-radius: 16px; padding: 32px; text-align: center; margin-bottom: 24px;">
        <p style="margin: 0 0 8px 0; font-size: 14px; font-weight: 700; color: #64748b;">USE CODE AT CHECKOUT</p>
        <p style="margin: 0; font-size: 32px; font-weight: 900; color: #0f172a; letter-spacing: 0.1em;">{{ discount_code }}</p>
      </div>
      <div style="text-align: center;">
        <a href="#" style="background-color: #4f46e5; color: #ffffff; padding: 18px 40px; border-radius: 14px; font-weight: 800; text-decoration: none; display: inline-block;">Shop the Collection</a>
      </div>
    ` + emailFooter
  },
  { 
    key: 'newsletter', tab: 'marketing', label: 'Newsletter', description: 'Regular updates and store news.',
    icon: Newspaper, tags: ['{{ first_name }}', '{{ shop_name }}'],
    defaultSubject: 'Fresh Daily: What\'s new at {{ shop_name }}',
    defaultContent: emailHeader + `
      <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-top: 0;">The Monthly Digest</h2>
      <p style="font-size: 16px; line-height: 1.6; margin-bottom: 24px;">Hello {{ first_name }}, here are the latest updates, stories, and products we've been working on at <strong>{{ shop_name }}</strong>.</p>
      <div style="border-top: 1px solid #e2e8f0; padding-top: 24px;">
        <h4 style="margin: 0 0 8px 0; font-size: 18px; font-weight: 700;">New Arrivals</h4>
        <p style="margin: 0; font-size: 14px; color: #64748b;">Discover our latest collection designed for the modern lifestyle.</p>
      </div>
    ` + emailFooter
  },
  { 
    key: 'review_request', tab: 'marketing', label: 'Review Request', description: 'Ask customers for feedback after delivery.',
    icon: Star, tags: ['{{ first_name }}', '{{ order_id }}'],
    defaultSubject: 'How did we do? Share your thoughts on order {{ order_id }}',
    defaultContent: emailHeader + `
      <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-top: 0;">We value your voice</h2>
      <p style="font-size: 16px; line-height: 1.6; margin-bottom: 24px;">Hi {{ first_name }}, thank you for your recent purchase! How was your experience with order <strong>{{ order_id }}</strong>? Your feedback helps us improve.</p>
      <div style="text-align: center; margin-bottom: 24px;">
        <div style="font-size: 32px; margin-bottom: 16px;">⭐⭐⭐⭐⭐</div>
        <a href="#" style="background-color: #0f172a; color: #ffffff; padding: 16px 32px; border-radius: 12px; font-weight: 700; text-decoration: none; display: inline-block;">Leave a Review</a>
      </div>
    ` + emailFooter
  },

  // Admin & Support
  { 
    key: 'loyalty_points_update', tab: 'admin', label: 'Loyalty Points Update', description: 'Notify users of earned or spent points.',
    icon: Coins, tags: ['{{ first_name }}', '{{ points }}'],
    defaultSubject: 'Your Loyalty Balance has increased! 💎',
    defaultContent: emailHeader + `
      <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-top: 0;">You've Earned Points!</h2>
      <p style="font-size: 16px; line-height: 1.6; margin-bottom: 24px;">Hi {{ first_name }}, your loyalty account has been credited with new points. Thank you for being a loyal member!</p>
      <div style="background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 16px; padding: 32px; text-align: center;">
        <p style="margin: 0 0 4px 0; font-size: 14px; font-weight: 700; color: #92400e; text-transform: uppercase;">Current Balance</p>
        <p style="margin: 0; font-size: 36px; font-weight: 900; color: #b45309;">{{ points }} Points</p>
      </div>
    ` + emailFooter
  },
  { 
    key: 'affiliate_commission', tab: 'admin', label: 'Affiliate Notification', description: 'Notify affiliates of new commissions.',
    icon: Gift, tags: ['{{ first_name }}', '{{ commission }}'],
    defaultSubject: 'New Commission Earned! 💸',
    defaultContent: emailHeader + `
      <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin-top: 0;">Great Work!</h2>
      <p style="font-size: 16px; line-height: 1.6; margin-bottom: 24px;">Hi {{ first_name }}, you've just earned a new commission of <strong>{{ commission }}</strong>. Your hard work is paying off!</p>
      <div style="background-color: #f8fafc; border-radius: 12px; padding: 24px; text-align: center;">
        <a href="#" style="color: #4f46e5; font-weight: 700; text-decoration: none;">View Affiliate Dashboard</a>
      </div>
    ` + emailFooter
  },
  { 
    key: 'admin_alert', tab: 'admin', label: 'Admin Alert', description: 'Internal notifications for shop owners.',
    icon: AlertCircle, tags: ['{{ type }}', '{{ status }}'],
    defaultSubject: 'System Alert: {{ type }}',
    defaultContent: emailHeader + `
      <h2 style="font-size: 22px; font-weight: 800; color: #ef4444; margin-top: 0;">Internal System Alert</h2>
      <p style="font-size: 16px; line-height: 1.6; margin-bottom: 24px;">This is an automated notification regarding a system event.</p>
      <div style="background-color: #fef2f2; border: 1px solid #fee2e2; border-radius: 12px; padding: 24px;">
        <p style="margin: 0 0 8px 0; font-weight: 700; color: #991b1b;">Event Type: {{ type }}</p>
        <p style="margin: 0; color: #b91c1c;">Current Status: {{ status }}</p>
      </div>
    ` + emailFooter
  }
]

const templates = reactive({})

// Initialize templates with defaults
templateTypes.forEach(type => {
  templates[type.key] = {
    type: type.key,
    subject: type.defaultSubject,
    content: type.defaultContent,
    is_active: true
  }
  saving[type.key] = false
})

const filteredTemplates = computed(() => {
  return templateTypes.filter(t => t.tab === activeTab.value)
})

const renderPreview = (key) => {
  let content = templates[key]?.content || ''
  const mockData = {
    '{{ first_name }}': 'John',
    '{{ last_name }}': 'Doe',
    '{{ shop_name }}': 'Elevate Shop',
    '{{ email }}': 'john@example.com',
    '{{ order_id }}': '#ORD-99210',
    '{{ amount }}': '$250.00',
    '{{ status }}': 'Processing',
    '{{ courier }}': 'Standard Delivery',
    '{{ tracking_id }}': 'TRK12345678',
    '{{ verification_link }}': '#',
    '{{ reset_link }}': '#',
    '{{ product_name }}': 'Premium Wireless Headphones',
    '{{ discount_code }}': 'SAVE20',
    '{{ points }}': '500',
    '{{ commission }}': '$45.00',
    '{{ type }}': 'New Order'
  }
  
  Object.keys(mockData).forEach(tag => {
    content = content.replaceAll(tag, mockData[tag])
  })
  
  return content
}

const toggleActive = (key) => {
  templates[key].is_active = !templates[key].is_active
}

const resetToDefault = (key) => {
  const type = templateTypes.find(t => t.key === key)
  if (type) {
    templates[key].subject = type.defaultSubject
    templates[key].content = type.defaultContent
    $toast.info('Template reset to original design')
  }
}

const insertTag = (key, tag) => {
  const editor = editorRefs[key]
  if (editor && typeof editor.insertContent === 'function') {
    editor.insertContent(tag)
  } else {
    // Fallback if editor not ready
    templates[key].content += tag
  }
}

const loadTemplates = async () => {
  try {
    pending.value = true
    const response = await getAll('/vendor/email-templates')
    if (response.data && response.data.length > 0) {
      response.data.forEach(item => {
        if (templates[item.type]) {
          templates[item.type] = {
            id: item.id,
            type: item.type,
            subject: item.subject,
            content: item.content,
            is_active: !!item.is_active
          }
        }
      })
    }
  } catch (error) {
    console.error(error)
    $toast.error('Unable to fetch templates')
  } finally {
    pending.value = false
  }
}

const saveTemplate = async (key) => {
  try {
    saving[key] = true
    await createItem('/vendor/email-templates', templates[key])
    navigateTo('/vendor/managed-shop/email-templates')
  } catch (error) {
    console.error(error)
    $toast.error('Something went wrong')
  } finally {
    saving[key] = false
  }
}

const openTestModal = (type) => {
  testModal.type = type
  testModal.show = true
}

const sendTestEmail = async () => {
  try {
    testModal.sending = true
    await createItem('/vendor/email-templates/test', {
      email: testModal.email,
      type: testModal.type,
      subject: templates[testModal.type]?.subject,
      content: templates[testModal.type]?.content
    })
    testModal.show = false
    navigateTo('/vendor/managed-shop/email-templates')
  } catch (error) {
    console.error(error)
    $toast.error(error.response?.data?.message || 'Failed to send test email')
  } finally {
    testModal.sending = false
  }
}

onMounted(() => {
  loadTemplates()
})

definePageMeta({
  middleware: 'auth',
  permissions: 'settings.view'
})
</script>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}
.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(20px);
}
.fade-slide-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

.prose h1 { @apply text-2xl font-black mb-4; }
.prose p { @apply text-base mb-2; }
</style>
