<template>
  <div class="max-w-7xl mx-auto p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <!-- Header & Filter Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
      <div class="flex items-center gap-6">
        <div class="w-16 h-16 bg-[#1a1c1e] rounded-2xl flex items-center justify-center shadow-lg">
          <BarChart3 class="w-8 h-8 text-white" />
        </div>
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Your Business Overview</h1>
          <p class="text-gray-500 dark:text-slate-400 font-medium">Reports and performance tracking</p>
        </div>
      </div>
      
      <div class="flex bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-100 dark:border-slate-800 p-1.5 self-start">
        <button class="px-5 py-2 text-sm font-semibold rounded-lg bg-gray-900 dark:bg-indigo-600 text-white transition-all shadow-sm">Last 7 Days</button>
        <button class="px-5 py-2 text-sm font-semibold rounded-lg text-gray-500 dark:text-slate-400 hover:text-gray-800 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-slate-800 transition-all">Last 30 Days</button>
        <button class="px-5 py-2 text-sm font-semibold rounded-lg text-gray-500 dark:text-slate-400 hover:text-gray-800 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-slate-800 transition-all">Custom</button>
      </div>
    </div>

    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <!-- KPI Stats Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
      <div class="bg-white dark:bg-slate-900 rounded-[2rem] p-8 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="text-sm font-bold text-gray-400 dark:text-slate-500 uppercase tracking-widest mb-4">Total Sales</div>
        <div class="text-4xl font-black text-gray-900 dark:text-white mb-4">৳{{ overview?.total_sales?.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) || '0.00' }}</div>
        <div class="flex items-center text-sm font-bold text-emerald-500 bg-emerald-50 px-3 py-1 rounded-full w-fit">
          <ArrowUpRight class="w-4 h-4 mr-1" /> {{ overview?.trends?.sales || 0 }}%
        </div>
      </div>

      <div class="bg-white dark:bg-slate-900 rounded-[2rem] p-8 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="text-sm font-bold text-gray-400 dark:text-slate-500 uppercase tracking-widest mb-4">Expenses</div>
        <div class="text-4xl font-black text-gray-900 dark:text-white mb-4">৳{{ overview?.expenses?.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) || '0.00' }}</div>
        <div class="flex items-center text-sm font-bold bg-rose-50 px-3 py-1 rounded-full w-fit" :class="overview?.trends?.expenses > 0 ? 'text-rose-500' : 'text-emerald-500 bg-emerald-50'">
          <ArrowDownRight v-if="overview?.trends?.expenses > 0" class="w-4 h-4 mr-1" />
          <ArrowUpRight v-else class="w-4 h-4 mr-1" />
          {{ Math.abs(overview?.trends?.expenses || 0) }}%
        </div>
      </div>

      <div class="bg-white dark:bg-slate-900 rounded-[2rem] p-8 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="text-sm font-bold text-gray-400 dark:text-slate-500 uppercase tracking-widest mb-4">Net Profit</div>
        <div class="text-4xl font-black text-indigo-600 mb-4">৳{{ overview?.net_profit?.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) || '0.00' }}</div>
        <div class="flex items-center text-sm font-bold bg-emerald-50 px-3 py-1 rounded-full w-fit" :class="overview?.trends?.profit > 0 ? 'text-emerald-500' : 'text-rose-500 bg-rose-50'">
          <ArrowUpRight v-if="overview?.trends?.profit > 0" class="w-4 h-4 mr-1" />
          <ArrowDownRight v-else class="w-4 h-4 mr-1" />
          {{ Math.abs(overview?.trends?.profit || 0) }}%
        </div>
      </div>

      <div class="bg-white dark:bg-slate-900 rounded-[2rem] p-8 border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-shadow">
        <div class="text-sm font-bold text-gray-400 dark:text-slate-500 uppercase tracking-widest mb-4">Total Orders</div>
        <div class="text-4xl font-black text-gray-900 dark:text-white mb-4">{{ overview?.total_orders || 0 }}</div>
        <div class="text-sm font-bold text-gray-500 dark:text-slate-400">From {{ overview?.total_orders || 0 }} transactions</div>
      </div>
    </div>

    <!-- Detailed Reports Grid -->
    <div class="mb-8">
      <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-8 flex items-center gap-3">
        <div class="w-2 h-8 bg-indigo-600 rounded-full"></div>
        Detailed Reports
      </h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Sales Report -->
        <NuxtLink to="/vendor/reports/sales" class="group bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-slate-800 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
          <div class="flex items-center gap-4 mb-8 bg-[#eef2ff] rounded-2xl p-4">
            <BarChart3 class="w-6 h-6 text-[#4f46e5]" />
            <span class="font-bold text-[#4f46e5] text-lg text-nowrap">Sales Report</span>
          </div>
          <p class="text-gray-500 dark:text-slate-400 leading-relaxed font-medium">
            Track your daily and monthly success sales performance
          </p>
        </NuxtLink>

        <!-- Return Report -->
        <NuxtLink to="/vendor/reports/returns" class="group bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-slate-800 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
          <div class="flex items-center gap-4 mb-8 bg-[#fffbeb] rounded-2xl p-4">
            <RotateCcw class="w-6 h-6 text-[#d97706]" />
            <span class="font-bold text-[#d97706] text-lg text-nowrap">Return Report</span>
          </div>
          <p class="text-gray-500 dark:text-slate-400 leading-relaxed font-medium">
            View and manage your return summary
          </p>
        </NuxtLink>

        <!-- Cancel Report -->
        <NuxtLink to="/vendor/reports/cancels" class="group bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-slate-800 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
          <div class="flex items-center gap-4 mb-8 bg-[#fef2f2] rounded-2xl p-4">
            <XCircle class="w-6 h-6 text-[#ef4444]" />
            <span class="font-bold text-[#ef4444] text-lg text-nowrap">Cancel Report</span>
          </div>
          <p class="text-gray-500 dark:text-slate-400 leading-relaxed font-medium">
            View and manage your cancel order summary
          </p>
        </NuxtLink>

        <!-- Expense Report -->
        <NuxtLink to="/vendor/reports/expenses" class="group bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-slate-800 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
          <div class="flex items-center gap-4 mb-8 bg-[#fff1f2] rounded-2xl p-4">
            <Wallet class="w-6 h-6 text-[#e11d48]" />
            <span class="font-bold text-[#e11d48] text-lg text-nowrap">Expense Report</span>
          </div>
          <p class="text-gray-500 dark:text-slate-400 leading-relaxed font-medium">
            Monitor your operational and product costs
          </p>
        </NuxtLink>

        <!-- Coupon Report -->
        <NuxtLink to="/vendor/reports/coupons" class="group bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-slate-800 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
          <div class="flex items-center gap-4 mb-8 bg-[#f0fdf4] rounded-2xl p-4">
            <Ticket class="w-6 h-6 text-[#059669]" />
            <span class="font-bold text-[#059669] text-lg text-nowrap">Coupon Report</span>
          </div>
          <p class="text-gray-500 dark:text-slate-400 leading-relaxed font-medium">
            Analyze coupon usage and discount impact
          </p>
        </NuxtLink>

        <!-- Product Performance -->
        <NuxtLink to="/vendor/reports/product-performance" class="group bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-slate-800 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
          <div class="flex items-center gap-4 mb-8 bg-[#faf5ff] rounded-2xl p-4">
            <Package class="w-6 h-6 text-[#9333ea]" />
            <span class="font-bold text-[#9333ea] text-lg text-nowrap">Product Performance</span>
          </div>
          <p class="text-gray-500 dark:text-slate-400 leading-relaxed font-medium">
            See top-selling and low-performing items
          </p>
        </NuxtLink>

        <!-- Sales Analytics -->
        <NuxtLink to="/vendor/reports/sales-analytics" class="group bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-slate-800 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
          <div class="flex items-center gap-4 mb-8 bg-[#eef2ff] rounded-2xl p-4">
            <LineChart class="w-6 h-6 text-[#4f46e5]" />
            <span class="font-bold text-[#4f46e5] text-lg text-nowrap">Sales Analytics</span>
          </div>
          <p class="text-gray-500 dark:text-slate-400 leading-relaxed font-medium">
            View your sales performance within a selected date range
          </p>
        </NuxtLink>

        <!-- Customer Report -->
        <NuxtLink to="/vendor/reports/customers" class="group bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-slate-800 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
          <div class="flex items-center gap-4 mb-8 bg-[#eff6ff] rounded-2xl p-4">
            <Users class="w-6 h-6 text-[#2563eb]" />
            <span class="font-bold text-[#2563eb] text-lg text-nowrap">Customer Report</span>
          </div>
          <p class="text-gray-500 dark:text-slate-400 leading-relaxed font-medium">
            Analyze customer behavior, retention and lifetime value
          </p>
        </NuxtLink>

        <!-- Stock Report -->
        <NuxtLink to="/vendor/reports/stock" class="group bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-slate-800 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
          <div class="flex items-center gap-4 mb-8 bg-[#f5f3ff] rounded-2xl p-4">
            <Boxes class="w-6 h-6 text-[#7c3aed]" />
            <span class="font-bold text-[#7c3aed] text-lg text-nowrap">Stock Report</span>
          </div>
          <p class="text-gray-500 dark:text-slate-400 leading-relaxed font-medium">
            Monitor inventory health, movements and stock valuation
          </p>
        </NuxtLink>

        <!-- Earnings Report -->
        <NuxtLink to="/vendor/reports/earnings" class="group bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-slate-800 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
          <div class="flex items-center gap-4 mb-8 bg-[#f0fdf4] rounded-2xl p-4">
            <Banknote class="w-6 h-6 text-[#16a34a]" />
            <span class="font-bold text-[#16a34a] text-lg text-nowrap">Earnings Report</span>
          </div>
          <p class="text-gray-500 dark:text-slate-400 leading-relaxed font-medium">
            Track your financial summaries, balances and payouts
          </p>
        </NuxtLink>

        <!-- Tax Report -->
        <NuxtLink to="/vendor/reports/tax" class="group bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-slate-800 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
          <div class="flex items-center gap-4 mb-8 bg-[#f0fdfa] rounded-2xl p-4">
            <Coins class="w-6 h-6 text-[#0d9488]" />
            <span class="font-bold text-[#0d9488] text-lg text-nowrap">Tax Report</span>
          </div>
          <p class="text-gray-500 dark:text-slate-400 leading-relaxed font-medium">
            View VAT/Tax liabilities and compliance summaries
          </p>
        </NuxtLink>

        <!-- HR Reports -->
        <NuxtLink v-for="report in ['Attendance', 'Leaves', 'Payroll']" :key="report" :to="`/vendor/hrm/${report.toLowerCase()}`" class="group bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-slate-800 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
          <div class="flex items-center gap-4 mb-8 bg-gray-50 dark:bg-slate-800 rounded-2xl p-4">
            <CalendarCheck v-if="report === 'Attendance'" class="w-6 h-6 text-gray-400" />
            <Briefcase v-else-if="report === 'Leave'" class="w-6 h-6 text-gray-400" />
            <Wallet v-else class="w-6 h-6 text-gray-400" />
            <span class="font-bold text-gray-400 text-lg text-nowrap text-capitalize">{{ report }} Report</span>
          </div>
          <p class="text-gray-500 dark:text-slate-400 leading-relaxed font-medium">
            Track {{ report.toLowerCase() }} and performance summary for your team.
          </p>
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  BarChart3, 
  RotateCcw, 
  XCircle, 
  Wallet, 
  Ticket, 
  Package, 
  LineChart, 
  CalendarCheck,
  ArrowUpRight,
  ArrowDownRight,
  Users,
  Boxes,
  Banknote,
  Coins,
  Briefcase
} from 'lucide-vue-next'

definePageMeta({
  middleware: 'auth',
  permissions: 'reports.view'
})

const { data: overview, pending } = useFetch('/api/vendor/reports/overview', {
  headers: {
    Authorization: `Bearer ${useCookie('auth_token').value}`
  }
})
</script>
