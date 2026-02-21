<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 transition-colors duration-300">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 sm:mb-8">
      <div class="flex items-center gap-4">
        <button @click="$router.back()" class="p-2 sm:p-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm active:scale-95 group">
          <ChevronLeft class="w-4 h-4 sm:w-5 sm:h-5 text-slate-600 dark:text-slate-300 group-hover:-translate-x-0.5 transition-transform" />
        </button>
        <div>
          <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white leading-tight tracking-tight">All Orders</h1>
          <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-semibold opacity-80">Manage and track every customer order efficiently.</p>
        </div>
      </div>
    </div>

    <!-- Status Tabs -->
    <div class="mt-8 mb-6 relative group px-2">
      <!-- Navigation Arrows -->
      <button @click="scrollTabs(-200)" class="absolute left-0 top-1/2 -translate-y-1/2 w-8 h-8 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full flex items-center justify-center shadow-md z-10 opacity-0 group-hover:opacity-100 transition-all hover:bg-slate-50 dark:hover:bg-slate-700 hover:scale-105 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400">
        <ChevronLeft class="w-4 h-4" />
      </button>
      <button @click="scrollTabs(200)" class="absolute right-0 top-1/2 -translate-y-1/2 w-8 h-8 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full flex items-center justify-center shadow-md z-10 opacity-0 group-hover:opacity-100 transition-all hover:bg-slate-50 dark:hover:bg-slate-700 hover:scale-105 text-slate-600 dark:text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400">
        <ChevronRight class="w-4 h-4" />
      </button>

      <div ref="tabsContainer" class="flex items-center gap-3 overflow-x-auto hide-scrollbar scroll-smooth px-1 py-1">
        <button 
          v-for="tab in statusTabs" 
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'flex items-center gap-2 px-4 sm:px-6 py-2 sm:py-2.5 rounded-xl whitespace-nowrap transition-all duration-300 border text-sm sm:text-base',
            activeTab === tab.id 
              ? 'bg-[#f0f3ff] dark:bg-indigo-900/30 border-[#4f46e5]/40 dark:border-indigo-500/50 text-[#4f46e5] dark:text-indigo-400 font-bold shadow-sm' 
              : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-300 font-semibold'
          ]"
        >
          <component :is="tab.icon" class="w-4 h-4" />
          <span>{{ tab.label }}</span>
          <span 
            :class="[
              'flex items-center justify-center min-w-[20px] h-[20px] px-1.5 text-[10px] font-black rounded-full transition-colors duration-300',
              activeTab === tab.id ? 'bg-[#4f46e5] text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400'
            ]"
          >
            {{ tab.count }}
          </span>
        </button>
      </div>
    </div>

    <!-- Filter Section Area -->
    <div class="bg-white dark:bg-slate-900 rounded-[24px] shadow-[0_10px_40px_-15px_rgba(0,0,0,0.05)] dark:shadow-none border border-slate-200/60 dark:border-slate-800 mb-6 sm:mb-8 p-5 sm:p-8">
      <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 sm:gap-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-6 flex-grow">
          <!-- Invoice/Phone -->
          <div class="flex flex-col gap-2">
            <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.1em] ml-1">Invoice / Phone</label>
            <div class="relative">
              <input 
                v-model="searchQuery"
                @keyup.enter="handleSearch"
                type="text" 
                placeholder="Invoice / Phone" 
                class="w-full h-12 pl-5 pr-10 bg-slate-50/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 dark:focus:border-indigo-400 outline-none transition-all placeholder:text-slate-400 dark:placeholder:text-slate-500 text-slate-700 dark:text-slate-200 font-semibold"
              >
            </div>
          </div>

          <!-- Order Type -->
          <div class="flex flex-col gap-2">
            <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.1em] ml-1">Order Type</label>
            <div class="relative">
              <select v-model="selectedType" class="w-full h-12 pl-5 pr-10 bg-slate-50/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-2xl appearance-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 dark:focus:border-indigo-400 outline-none transition-all text-slate-700 dark:text-slate-200 font-semibold cursor-pointer">
                <option>All</option>
                <option>Normal</option>
                <option>Pre-order</option>
                <option>POS</option>
              </select>
              <ChevronDown class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 dark:text-slate-500 pointer-events-none" />
            </div>
          </div>

          <!-- Order Date -->
          <div class="flex flex-col gap-2">
            <label class="text-xs font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.1em] ml-1">Order Date</label>
            <div class="relative">
              <input 
                v-model="selectedDate"
                type="date" 
                class="w-full h-12 px-5 bg-slate-50/50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 dark:focus:border-indigo-400 outline-none transition-all text-slate-700 dark:text-slate-200 font-semibold"
              >
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col sm:flex-row gap-3 pt-2 sm:pt-0 lg:pt-6 lg:items-end">
            <button @click="handleSearch" class="w-full sm:w-auto h-12 px-8 bg-slate-900 dark:bg-indigo-600 hover:bg-slate-800 dark:hover:bg-indigo-500 text-white font-black rounded-2xl transition-all shadow-xl shadow-slate-900/10 dark:shadow-indigo-900/20 active:scale-95 flex items-center justify-center gap-2">
              <Search class="w-4 h-4" />
              Search
            </button>
            <button @click="clearFilters" class="w-full sm:w-auto h-12 px-6 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 font-black rounded-2xl transition-all flex items-center justify-center gap-2 active:scale-95">
              <X class="w-4 h-4" />
              Clear
            </button>
          </div>
        </div>

        <div class="flex lg:items-end mt-4 lg:mt-0 w-full lg:w-auto">
          <button class="w-full lg:w-auto justify-center h-12 px-8 bg-red-50 text-red-600 border border-red-100 hover:bg-red-600 hover:text-white font-black rounded-2xl transition-all flex items-center gap-2 group whitespace-nowrap active:scale-95 shadow-lg shadow-red-600/5">
            <Trash2 class="w-4 h-4 group-hover:animate-bounce" />
            Bulk Delete
          </button>
        </div>
      </div>
    </div>

    <!-- Orders Table Container -->
    <div class="bg-white dark:bg-slate-900 rounded-[24px] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.06)] dark:shadow-none border border-slate-200/60 dark:border-slate-800 group transition-colors duration-300">
      <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-900 dark:bg-slate-800 border-b border-slate-800 dark:border-slate-700">
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-slate-400">
                <div class="flex items-center gap-4">
                  <span class="text-white">#</span>
                  <input type="checkbox" class="w-4 h-4 rounded border-slate-700 dark:border-slate-600 bg-transparent dark:bg-slate-700 text-indigo-600 focus:ring-indigo-500/50 cursor-pointer">
                </div>
              </th>
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-[#f8fafc] dark:text-slate-200">Customer Details</th>
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-[#f8fafc] dark:text-slate-200">Order Details</th>
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-[#f8fafc] dark:text-slate-200">Spider Intelligence</th>
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-[#f8fafc] dark:text-slate-200">Products</th>
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-[#f8fafc] dark:text-slate-200">Amount</th>
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-[#f8fafc] dark:text-slate-200">Others</th>
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-[#f8fafc] dark:text-slate-200">Courier</th>
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-[#f8fafc] dark:text-slate-200 text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <!-- Loading State -->
            <tr v-if="loading" v-for="i in 5" :key="`skeleton-${i}`" class="border-b border-slate-50 dark:border-slate-800/50">
              <td class="px-8 py-5">
                <div class="flex items-center gap-4">
                  <div class="h-4 w-8 bg-slate-100 dark:bg-slate-800 rounded animate-pulse"></div>
                  <div class="w-4 h-4 bg-slate-100 dark:bg-slate-800 rounded animate-pulse"></div>
                </div>
              </td>
              <td class="px-8 py-5">
                <div class="flex items-center gap-3">
                  <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 animate-pulse"></div>
                  <div class="space-y-2">
                    <div class="h-3 w-24 bg-slate-100 dark:bg-slate-800 rounded animate-pulse"></div>
                    <div class="h-2 w-16 bg-slate-100 dark:bg-slate-800 rounded animate-pulse"></div>
                  </div>
                </div>
              </td>
              <td class="px-8 py-5">
                <div class="space-y-2">
                  <div class="h-4 w-28 bg-slate-100 dark:bg-slate-800 rounded animate-pulse"></div>
                  <div class="h-2 w-20 bg-slate-100 dark:bg-slate-800 rounded animate-pulse"></div>
                </div>
              </td>
              <td class="px-8 py-5">
                <div class="space-y-2">
                  <div class="h-6 w-16 bg-slate-100 dark:bg-slate-800 rounded animate-pulse"></div>
                  <div class="h-1 w-full bg-slate-100 dark:bg-slate-800 rounded animate-pulse"></div>
                </div>
              </td>
              <td class="px-8 py-5">
                <div class="flex items-center gap-2">
                  <div class="flex -space-x-2">
                    <div v-for="j in 3" :key="j" class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 border-2 border-white dark:border-slate-900 animate-pulse"></div>
                  </div>
                  <div class="h-3 w-12 bg-slate-100 dark:bg-slate-800 rounded animate-pulse"></div>
                </div>
              </td>
              <td class="px-8 py-5">
                <div class="space-y-2">
                  <div class="h-4 w-16 bg-slate-100 dark:bg-slate-800 rounded animate-pulse"></div>
                  <div class="h-3 w-10 bg-slate-100 dark:bg-slate-800 rounded animate-pulse"></div>
                </div>
              </td>
              <td class="px-8 py-5">
                <div class="space-y-2">
                  <div class="h-3 w-16 bg-slate-100 dark:bg-slate-800 rounded animate-pulse"></div>
                  <div class="h-3 w-16 bg-slate-100 dark:bg-slate-800 rounded animate-pulse"></div>
                </div>
              </td>
              <td class="px-8 py-5 text-right">
                <div class="flex justify-end gap-2">
                  <div v-for="k in 3" :key="k" class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 animate-pulse"></div>
                </div>
              </td>
            </tr>

            <!-- Empty State -->
            <tr v-if="!loading && orders.length === 0">
              <td colspan="10" class="py-32 px-8 text-center">
                <div class="flex flex-col items-center justify-center gap-8 max-w-sm mx-auto animate-in fade-in zoom-in duration-700">
                  <div class="w-28 h-28 bg-slate-50 dark:bg-slate-800 rounded-[40px] flex items-center justify-center border-4 border-white dark:border-slate-700 shadow-2xl rotate-3 transition-transform hover:rotate-0 duration-500">
                    <div class="w-16 h-16 bg-white dark:bg-slate-900 rounded-full flex items-center justify-center border border-slate-100 dark:border-slate-700 shadow-inner">
                      <XCircle class="w-8 h-8 text-slate-300 dark:text-slate-500" />
                    </div>
                  </div>
                  <div>
                    <h3 class="text-2xl font-black text-slate-800 dark:text-slate-200 mb-3 tracking-tight">No data found</h3>
                    <p class="text-slate-500 dark:text-slate-400 font-semibold leading-relaxed px-4">
                      Try adjusting your filters or check back later for more data.
                    </p>
                  </div>
                  <button @click="reloadPage" class="flex items-center gap-3 px-10 py-4 bg-slate-900 dark:bg-indigo-600 hover:bg-slate-800 dark:hover:bg-indigo-500 text-white font-black rounded-[20px] transition-all shadow-2xl shadow-slate-900/20 active:scale-95 group">
                    <RefreshCw class="w-5 h-5 group-hover:rotate-180 transition-transform duration-700" />
                    Reload Page
                  </button>
                </div>
              </td>
            </tr>
            <!-- Orders Grid Data -->
            <tr v-else-if="!loading" v-for="(order, index) in orders" :key="order.id" class="border-b border-slate-50 dark:border-slate-800/50 hover:bg-slate-50/80 dark:hover:bg-slate-800/30 transition-colors group/row">
              <!-- Checkbox & Rank -->
              <td class="px-8 py-5">
                <div class="flex items-center gap-4">
                  <span class="text-xs font-black text-slate-400 dark:text-slate-500">#{{ String(index + 1).padStart(2, '0') }}</span>
                  <input type="checkbox" class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-indigo-600 focus:ring-indigo-500/50 cursor-pointer shadow-sm">
                </div>
              </td>

              <!-- Customer Details -->
              <td class="px-8 py-5">
                <div class="flex items-center gap-3">
                  <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900 dark:to-purple-900 flex items-center justify-center border border-indigo-50 dark:border-indigo-800 shadow-sm relative group-hover/row:scale-105 transition-transform duration-300">
                    <span class="text-xs font-black text-indigo-700 dark:text-indigo-300">{{ order.customer.initials }}</span>
                    <div v-if="order.customer.vip" class="absolute -top-1 -right-1 w-4 h-4 bg-amber-400 rounded-full border-2 border-white dark:border-slate-900 flex items-center justify-center shadow-sm" title="VIP Customer">
                      <Star class="w-2.5 h-2.5 text-white" />
                    </div>
                  </div>
                  <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 line-clamp-1 group-hover/row:text-indigo-600 dark:group-hover/row:text-indigo-400 transition-colors">{{ order.customer.name }}</h3>
                    <div class="flex items-center gap-2 mt-0.5">
                      <Phone class="w-3 h-3 text-slate-400" />
                      <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ order.customer.phone }}</span>
                    </div>
                  </div>
                </div>
              </td>

              <!-- Order Details -->
              <td class="px-8 py-5">
                <div class="flex flex-col gap-1.5">
                  <div class="flex items-center gap-2">
                    <span class="text-xs font-black text-slate-800 dark:text-slate-200 bg-slate-100 dark:bg-slate-800 px-2 py-0.5 rounded-md border border-slate-200 dark:border-slate-700 uppercase tracking-widest min-w-[100px] text-center">
                      {{ order.invoice }}
                    </span>
                    <div :class="[
                      'px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-widest border border-transparent dark:border-none shadow-sm whitespace-nowrap',
                      {
                        'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border-emerald-100': order.status === 'Delivered',
                        'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border-blue-100': order.status === 'Processing',
                        'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 border-amber-100': order.status === 'Pending',
                        'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 border-indigo-100': order.status === 'Courier',
                        'bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 border-rose-100': order.status === 'Cancelled'
                      }
                    ]">
                      {{ order.status }}
                    </div>
                  </div>
                  <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 font-medium">
                    <CalendarDays class="w-3.5 h-3.5" />
                    {{ order.date }} <span class="text-slate-300 dark:text-slate-600 text-[10px]">&bull;</span> {{ order.time }}
                  </div>
                </div>
              </td>

              <!-- Spider Intelligence -->
              <td class="px-8 py-5">
                 <div class="flex flex-col gap-1.5 min-w-[140px]">
                    <div class="flex items-center gap-2">
                       <div class="flex items-center gap-1.5 border dark:border-none px-2 py-0.5 rounded-md shadow-sm tooltip-target" :class="{
                          'bg-emerald-50 dark:bg-emerald-900/30 border-emerald-100 text-emerald-600 dark:text-emerald-400': order.risk.score < 20,
                          'bg-amber-50 dark:bg-amber-900/30 border-amber-100 text-amber-600 dark:text-amber-400': order.risk.score >= 20 && order.risk.score < 60,
                          'bg-rose-50 dark:bg-rose-900/30 border-rose-100 text-rose-600 dark:text-rose-400': order.risk.score >= 60
                        }" :title="`Risk Score: ${order.risk.score}/100`">
                        <ShieldCheck v-if="order.risk.score < 20" class="w-3.5 h-3.5" />
                        <AlertTriangle v-else-if="order.risk.score >= 20 && order.risk.score < 60" class="w-3.5 h-3.5" />
                        <ShieldAlert v-else class="w-3.5 h-3.5" />
                        <span class="text-xs font-black">{{ order.risk.score }}</span>
                      </div>
                      <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest" v-if="order.risk.score < 20">Low Risk</span>
                      <span class="text-[10px] font-bold text-amber-500 uppercase tracking-widest" v-else-if="order.risk.score < 60">Med Risk</span>
                      <span class="text-[10px] font-bold text-rose-500 uppercase tracking-widest" v-else>High Risk</span>
                    </div>
                    <!-- Risk Progress Bar -->
                    <div class="w-full bg-slate-100 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                      <div 
                        :class="[
                          'h-full transition-all duration-1000 ease-out',
                          {
                            'bg-emerald-500': order.risk.score < 20,
                            'bg-amber-500': order.risk.score >= 20 && order.risk.score < 60,
                            'bg-rose-500': order.risk.score >= 60
                          }
                        ]" 
                        :style="{ width: `${order.risk.score}%` }"
                      ></div>
                    </div>
                    <div class="flex flex-wrap gap-1" v-if="order.risk.flags.length > 0">
                       <span v-for="(flag, i) in order.risk.flags" :key="i" class="text-[9px] font-bold uppercase tracking-wider px-1.5 py-0.5 rounded bg-slate-50 dark:bg-slate-800/50 text-slate-400 dark:text-slate-500 border border-slate-100 dark:border-slate-700/50">
                         {{ flag }}
                       </span>
                    </div>
                 </div>
              </td>

              <!-- Products -->
              <td class="px-8 py-5">
                <div class="flex items-center gap-2">
                  <div class="flex -space-x-2 mr-2">
                    <img v-for="(img, idx) in order.products.images.slice(0, 3)" :key="idx" :src="img" class="w-8 h-8 rounded-lg border-2 border-white dark:border-slate-800 shadow-sm object-cover bg-slate-100 dark:bg-slate-700 relative z-10 hover:z-20 hover:scale-110 transition-transform cursor-pointer">
                  </div>
                  <div class="flex flex-col">
                    <span class="text-sm font-black text-slate-800 dark:text-slate-200">{{ order.products.count }} Items</span>
                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 tracking-wider">SKUs: {{ order.products.skus }}</span>
                  </div>
                </div>
              </td>

              <!-- Amount -->
              <td class="px-8 py-5">
                <div class="flex flex-col gap-1">
                  <span class="text-sm font-black text-slate-900 dark:text-white tracking-tight">{{ order.amount.total }}</span>
                  <div class="flex items-center gap-1.5">
                    <span class="text-[10px] font-bold uppercase tracking-widest border border-slate-200 dark:border-slate-700 px-1.5 py-0.5 rounded text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800">
                      {{ order.amount.method }}
                    </span>
                    <span :class="[
                      'w-2 h-2 rounded-full shadow-sm',
                      order.amount.paid ? 'bg-emerald-400 shadow-emerald-400/50' : 'bg-rose-400 shadow-rose-400/50 animate-pulse'
                    ]" :title="order.amount.paid ? 'Paid' : 'Unpaid'"></span>
                  </div>
                </div>
              </td>

              <!-- Others -->
              <td class="px-8 py-5">
                <div class="flex flex-col gap-1.5 min-w-[100px]">
                  <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-semibold" title="Order Type">
                    <Tag class="w-3.5 h-3.5" />
                    {{ order.type }}
                  </div>
                  <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 font-semibold truncate" title="Delivery Zone">
                    <MapPin class="w-3.5 h-3.5" />
                    {{ order.zone }}
                  </div>
                </div>
              </td>

              <!-- Courier -->
              <td class="px-8 py-5">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-900/30 flex items-center justify-center border border-orange-100 dark:border-orange-500/20 text-orange-500 shadow-sm shrink-0" v-if="order.courier.name">
                    <Truck class="w-4 h-4" />
                  </div>
                  <div v-if="order.courier.name" class="flex flex-col">
                    <span class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ order.courier.name }}</span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest truncate max-w-[80px]" :title="order.courier.tracking">
                      {{ order.courier.tracking }}
                    </span>
                  </div>
                  <span v-else class="text-xs text-slate-400 dark:text-slate-500 font-medium italic">Unassigned</span>
                </div>
              </td>

              <!-- Actions -->
              <td class="px-8 py-5 text-right">
                <div class="flex items-center justify-end gap-1.5 opacity-0 group-hover/row:opacity-100 transition-opacity">
                  <button @click="openDrawer(order)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:border-indigo-200 dark:hover:border-indigo-500/50 transition-all shadow-sm tooltip-target" title="View Details">
                    <Eye class="w-4 h-4" />
                  </button>
                  <button @click="openDrawer(order)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:border-emerald-200 dark:hover:border-emerald-500/50 transition-all shadow-sm tooltip-target" title="Edit Order">
                    <Edit class="w-4 h-4" />
                  </button>
                  <NuxtLink :to="`/vendor/orders/invoice/${order.id}?type=${order.type}`" target="_blank" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:border-blue-200 dark:hover:border-blue-500/50 transition-all shadow-sm tooltip-target" title="Print Invoice">
                    <Printer class="w-4 h-4" />
                  </NuxtLink>
                  <button @click="confirmDelete(order)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/30 hover:border-rose-200 dark:hover:border-rose-500/50 transition-all shadow-sm tooltip-target" title="Delete Order">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>

          </tbody>
        </table>
      </div>
    </div>

    <!-- Details Drawer -->
    <VendorOrderDetailsDrawer ref="orderDetailsDrawer" @updated="fetchOrders" />

    <!-- Delete Confirmation Modal -->
    <AppConfirmationModal
      :isOpen="isDeleteModalOpen"
      title="Delete Order"
      message="Are you sure you want to completely delete this order? This action cannot be undone."
      icon="Trash2"
      confirmText="Delete Order"
      :isLoading="isDeleting"
      @confirm="handleConfirmDelete"
      @close="isDeleteModalOpen = false"
    />
  </div>
</template>

<script setup>
import { 
  ChevronLeft, 
  ChevronDown, 
  Search, 
  X, 
  Trash2, 
  XCircle, 
  RefreshCw,
  LayoutGrid,
  Clock,
  CheckCircle2,
  Truck,
  Package,
  Star,
  Phone,
  CalendarDays,
  ShieldCheck,
  ShieldAlert,
  AlertTriangle,
  Tag,
  MapPin,
  Eye,
  Edit,
  Printer
} from 'lucide-vue-next'
import { ref, computed, watch } from 'vue'
import { toast } from 'vue-sonner'
import VendorOrderDetailsDrawer from '~/components/vendor/VendorOrderDetailsDrawer.vue'
import AppConfirmationModal from '~/components/AppConfirmationModal.vue'

definePageMeta({
  middleware: 'auth'
})

const activeTab = ref('latest')
const searchQuery = ref('')
const selectedType = ref('All')
const selectedDate = ref('')
const { getAll, createItem, deleteItem } = useCrud()


const config = useRuntimeConfig()

// Computed query params for API call
const queryParams = computed(() => {
  return {
    status: activeTab.value,
    search: searchQuery.value,
    type: selectedType.value,
    date: selectedDate.value,
  }
})

const listData = ref(null)
const loading = ref(false)

// Fetch Orders Dynamically
const fetchOrders = async () => {
  loading.value = true
  try {
    const response = await getAll('/vendor/orders', queryParams.value)
    listData.value = response

    // Update counts if available
    if (response?.counts) {
      statusTabs.value.forEach(tab => {
        if (response.counts[tab.id] !== undefined) {
          tab.count = response.counts[tab.id]
        }
      })
    }
  } catch (error) {
    console.error('Failed to fetch orders', error)
  } finally {
    loading.value = false
  }
}

// Watch filters to refetch
watch(queryParams, () => {
  fetchOrders()
}, { deep: true, immediate: true })

// Orders Computed List
const orders = computed(() => {
  return listData.value?.data || []
})

const handleSearch = () => {
  fetchOrders()
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedType.value = 'All'
  selectedDate.value = ''
  activeTab.value = 'latest'
  fetchOrders()
}

const statusTabs = ref([
  { id: 'latest', label: 'Latest 15 Orders', count: 0, icon: LayoutGrid },
  { id: 'pending', label: 'Pending Orders', count: 0, icon: Clock },
  { id: 'approved', label: 'Approved Orders', count: 0, icon: CheckCircle2 },
  { id: 'process', label: 'Process Orders', count: 0, icon: Package },
  { id: 'courier', label: 'Courier', count: 0, icon: Truck },
  { id: 'delivered', label: 'Delivered Orders', count: 0, icon: CheckCircle2 },
  { id: 'cancelled', label: 'Cancelled Orders', count: 0, icon: XCircle },
])

const reloadPage = () => {
  window.location.reload()
}

const tabsContainer = ref(null)

const scrollTabs = (amount) => {
  if (tabsContainer.value) {
    tabsContainer.value.scrollLeft += amount
  }
}

// Drawer Refs & logic
const orderDetailsDrawer = ref(null)

const openDrawer = (order) => {
  orderDetailsDrawer.value?.openDrawer(order.id, order.type)
}

// Delete Logic
const isDeleteModalOpen = ref(false)
const orderToDelete = ref(null)
const isDeleting = ref(false)

const confirmDelete = (order) => {
  orderToDelete.value = order
  isDeleteModalOpen.value = true
}

const handleConfirmDelete = async () => {
  if (!orderToDelete.value) return
  isDeleting.value = true
  try {
    await deleteItem(`${orderToDelete.value.id}?type=${orderToDelete.value.type}`, '/vendor/orders')
    fetchOrders()
  } catch (e) {
    console.error('Failed to delete order', e)
  } finally {
    isDeleting.value = false
    isDeleteModalOpen.value = false
    orderToDelete.value = null
  }
}
</script>

<style scoped>
.hide-scrollbar::-webkit-scrollbar {
  display: none;
}
.hide-scrollbar {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}
.custom-scrollbar::-webkit-scrollbar {
  height: 8px;
  width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
  border: 2px solid #f1f5f9;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* For Firefox */
.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 #f1f5f9;
}
</style>
