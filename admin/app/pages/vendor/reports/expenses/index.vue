<template>
  <div class="max-w-7xl mx-auto p-10 space-y-8">
    <!-- Breadcrumb & Actions -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div class="flex items-center text-sm text-gray-500 bg-white px-4 py-2 rounded-full border border-gray-100 shadow-sm w-fit">
        <NuxtLink to="/vendor/reports" class="hover:text-indigo-600 transition-colors">Reports</NuxtLink>
        <ChevronRight class="w-4 h-4 mx-2" />
        <span class="font-bold text-gray-900">Expense Report</span>
      </div>
      
      <div class="flex items-center gap-3">
        <button class="flex items-center gap-2 bg-white border border-gray-200 px-4 py-2 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
          <Download class="w-4 h-4" /> Export PDF
        </button>
        <button class="flex items-center gap-2 bg-rose-600 px-6 py-2 rounded-xl text-sm font-bold text-white hover:bg-rose-700 transition-all shadow-lg shadow-rose-100">
          <Plus class="w-4 h-4" /> Add Expense
        </button>
      </div>
    </div>

    <!-- Header Section -->
    <div class="bg-[#1a1c1e] rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl">
      <div class="relative z-10 flex flex-col md:flex-row md:items-end justify-between gap-8">
        <div>
          <h1 class="text-4xl font-black mb-4 tracking-tight">Expense Tracking</h1>
          <p class="text-gray-400 font-medium max-w-md">Monitor your operational costs, product procurement, and overheads.</p>
        </div>
        <div class="flex bg-white/10 backdrop-blur-md rounded-2xl p-1.5 border border-white/10">
          <button class="px-6 py-2 text-sm font-bold rounded-xl bg-white text-gray-900 shadow-xl">Weekly</button>
          <button class="px-6 py-2 text-sm font-bold rounded-xl text-gray-300 hover:text-white transition-all">Monthly</button>
          <button class="px-6 py-2 text-sm font-bold rounded-xl text-gray-300 hover:text-white transition-all">Yearly</button>
        </div>
      </div>
      <!-- Background Decorative Elements -->
      <div class="absolute top-0 right-0 w-64 h-64 bg-rose-500/10 rounded-full blur-3xl -mr-32 -mt-32"></div>
    </div>

    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-rose-600"></div>
    </div>

    <template v-else>
      <!-- KPI Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
          <div class="relative z-10">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Total Expenses</div>
            <div class="text-4xl font-black text-gray-900 mb-4 tracking-tighter">৳{{ expensesData?.kpi?.total_expenses?.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) || '0.00' }}</div>
            <div class="flex items-center text-sm font-bold text-rose-500 bg-rose-50 px-3 py-1 rounded-full w-fit">
              <ArrowUpRight class="w-4 h-4 mr-1" /> +5.4%
            </div>
          </div>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
            <Wallet class="w-32 h-32 text-gray-900" />
          </div>
        </div>

        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
          <div class="relative z-10">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Procurement Cost</div>
            <div class="text-4xl font-black text-gray-900 mb-4 tracking-tighter">৳0.00</div>
            <div class="flex items-center text-sm font-bold text-emerald-500 bg-emerald-50 px-3 py-1 rounded-full w-fit">
              <ArrowDownRight class="w-4 h-4 mr-1" /> -12.2%
            </div>
          </div>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
            <Truck class="w-32 h-32 text-gray-900" />
          </div>
        </div>

        <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
          <div class="relative z-10">
            <div class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Operative Cost</div>
            <div class="text-4xl font-black text-gray-900 mb-4 tracking-tighter">৳0.00</div>
            <div class="flex items-center text-sm font-bold text-rose-500 bg-rose-50 px-3 py-1 rounded-full w-fit">
              <ArrowUpRight class="w-4 h-4 mr-1" /> +2.1%
            </div>
          </div>
          <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform duration-500">
            <Settings2 class="w-32 h-32 text-gray-900" />
          </div>
        </div>
      </div>

      <!-- Expense Breakdown -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm">
          <h3 class="text-2xl font-black text-gray-900 tracking-tight mb-8">Category Breakdown</h3>
          <div class="space-y-8">
            <div v-for="(cat, i) in ['Inventory', 'Logistics', 'Marketing', 'Rent/Utilities']" :key="cat" class="space-y-3">
              <div class="flex justify-between items-end">
                <span class="font-bold text-gray-700">{{ cat }}</span>
                <span class="font-black text-gray-900">৳{{ (15000 / (i + 1)).toFixed(0) }}</span>
              </div>
              <div class="h-4 bg-gray-50 rounded-full overflow-hidden">
                <div class="h-full bg-rose-500 rounded-full transition-all duration-1000" :style="{ width: (80 - (i * 15)) + '%' }"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm">
          <h3 class="text-2xl font-black text-gray-900 tracking-tight mb-8">Recent Expenses</h3>
          <div class="space-y-6">
            <div v-for="expense in expensesData?.recent_expenses?.data || []" :key="expense.id" class="flex items-center justify-between p-4 rounded-3xl hover:bg-gray-50 transition-all border border-transparent hover:border-gray-100 group">
              <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center font-black text-gray-400 group-hover:bg-white transition-colors shadow-sm text-xs">
                  #EX{{ String(expense.id).padStart(3, '0') }}
                </div>
                <div>
                  <div class="font-bold text-gray-900 mb-1">{{ expense.title }}</div>
                  <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ new Date(expense.expense_date || expense.created_at).toLocaleDateString() }}</div>
                </div>
              </div>
              <div class="font-black text-rose-600">- ৳{{ Number(expense.amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}</div>
            </div>
            <div v-if="!expensesData?.recent_expenses?.data?.length" class="text-center text-gray-500 py-4 font-medium">
              No recent expenses found.
            </div>
            <button v-else class="w-full py-4 text-sm font-bold text-gray-500 hover:text-rose-600 transition-colors bg-gray-50 rounded-2xl hover:bg-rose-50">
              View All Expenses
            </button>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { 
  ChevronRight, 
  Download, 
  Plus,
  ArrowUpRight, 
  ArrowDownRight,
  Wallet,
  Truck,
  Settings2
} from 'lucide-vue-next'

const { data: expensesData, pending } = useFetch('/api/vendor/reports/expenses', {
  headers: {
    Authorization: `Bearer ${useCookie('auth_token').value}`
  }
})

definePageMeta({
  middleware: 'auth'
})
</script>
