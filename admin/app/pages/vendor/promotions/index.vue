<template>
  <div class="min-h-screen bg-[#f8f9fa] dark:bg-slate-950 p-10 transition-colors duration-300">
    <!-- Breadcrumbs -->
    <nav class="flex items-center space-x-2 text-sm text-gray-600 dark:text-slate-400 mb-8">
      <NuxtLink to="/vendor" class="hover:text-black dark:hover:text-white transition-colors">
        <HomeIcon class="w-4 h-4" />
      </NuxtLink>
      <ChevronRightIcon class="w-4 h-4 text-gray-400 dark:text-slate-600" />
      <span class="hover:text-black dark:hover:text-white cursor-pointer">Promotions</span>
      <ChevronRightIcon class="w-4 h-4 text-gray-400 dark:text-slate-600" />
      <span class="text-black dark:text-white font-medium">List</span>
    </nav>

    <!-- Main Content Area -->
    <div v-if="loading" class="flex items-center justify-center min-h-[60vh]">
      <div class="animate-spin rounded-full h-12 w-12 border-4 border-indigo-500 border-t-transparent"></div>
    </div>
    <div v-else-if="items.length === 0" class="flex flex-col items-center justify-center min-h-[60vh] bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 p-8">
      <!-- Empty State Illustration -->
      <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-6 shadow-inner relative">
         <div class="absolute inset-0 bg-gray-100 rounded-full scale-110 opacity-50"></div>
         <MegaphoneIcon class="w-10 h-10 text-gray-400 relative z-10" />
      </div>

      <h1 class="text-2xl font-bold text-[#1e293b] dark:text-white mb-2">No promotions found</h1>
      <p class="text-center text-gray-500 dark:text-slate-400 max-w-md mb-8 leading-relaxed">
        You haven't created any promotions yet. Boost your sales by creating limited-time offers or flash sales.
      </p>

      <button 
        @click="openAddDrawer"
        class="bg-[#0f172a] text-white px-6 py-2.5 rounded-lg flex items-center space-x-2 hover:bg-black transition-all shadow-lg hover:shadow-xl active:scale-95"
      >
        <PlusIcon class="w-5 h-5" />
        <span class="font-medium">Add New Promotion</span>
      </button>
    </div>

    <!-- Data Table -->
    <div v-else class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 overflow-hidden transition-colors">
        <div class="p-10 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">All Promotions</h2>
            <button 
                @click="openAddDrawer"
                class="bg-[#0f172a] text-white px-4 py-2 text-sm rounded-lg flex items-center space-x-2 hover:bg-black transition-all shadow-sm active:scale-95"
            >
                <PlusIcon class="w-4 h-4" />
                <span class="font-medium">Add New</span>
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 dark:bg-slate-800 border-b border-gray-100 dark:border-slate-700">
                        <th class="py-4 px-6 font-semibold text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wider">Promotion Title</th>
                        <th class="py-4 px-6 font-semibold text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wider">Type / Value</th>
                        <th class="py-4 px-6 font-semibold text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wider">Duration</th>
                        <th class="py-4 px-6 font-semibold text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="py-4 px-6 font-semibold text-xs text-gray-500 dark:text-slate-400 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    <tr v-for="promotion in items" :key="promotion.id" class="hover:bg-gray-50/50 dark:hover:bg-slate-800/50 transition-colors group">
                        <td class="px-6 py-4 flex items-center gap-4">
                            <div v-if="promotion.banner" class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                <img :src="promotion.banner" class="w-full h-full object-cover">
                            </div>
                            <div v-else class="w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-400 shrink-0">
                                <ImageIcon class="w-5 h-5" />
                            </div>
                            <div>
                            <div class="font-bold text-gray-900 dark:text-slate-100">{{ promotion.title }}</div>
                                <div class="text-xs text-gray-500 dark:text-slate-400 mt-1 capitalize">{{ formatTarget(promotion.target) }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900 dark:text-slate-100 capitalize">{{ formatType(promotion.type) }}</div>
                            <div class="text-xs text-gray-500 dark:text-slate-400 mt-1">
                                {{ promotion.discount_unit === 'percentage' ? promotion.discount_value + '%' : '$' + promotion.discount_value }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600 dark:text-slate-300">
                                {{ formatDate(promotion.start_date) }} - {{ formatDate(promotion.end_date) }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span :class="[
                                'px-2.5 py-1 text-xs font-bold rounded-full border',
                                promotion.is_active ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border-emerald-100 dark:border-emerald-600/20' : 'bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 border-rose-100 dark:border-rose-600/20'
                            ]">
                                {{ promotion.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                           <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                               <button v-if="promotion.is_active" @click="confirmSendToSubscribers(promotion.id)" class="p-2 text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 rounded-lg transition-colors tooltip-target" title="Send to Subscribers">
                                   <SendIcon class="w-4 h-4" />
                               </button>
                               <button @click="openEditDrawer(promotion)" class="p-2 text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg transition-colors tooltip-target" title="Edit Promotion">
                                   <EditIcon class="w-4 h-4" />
                               </button>
                               <button @click="confirmDelete(promotion.id)" class="p-2 text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/30 hover:bg-rose-100 dark:hover:bg-rose-900/50 rounded-lg transition-colors tooltip-target" title="Delete Promotion">
                                   <TrashIcon class="w-4 h-4" />
                               </button>
                           </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Side Drawer (Slide-over) -->
    <Transition
      enter-active-class="transform transition ease-in-out duration-500 sm:duration-700"
      enter-from-class="translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transform transition ease-in-out duration-500 sm:duration-700"
      leave-from-class="translate-x-0"
      leave-to-class="translate-x-full"
    >
      <div v-if="isOpen" class="fixed inset-y-0 right-0 w-full max-w-md bg-white dark:bg-slate-900 shadow-2xl z-50 flex flex-col">
        <!-- Drawer Header -->
        <div class="px-6 py-6 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between">
          <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ isEditing ? 'Edit Promotion' : 'Add New Promotion' }}</h2>
          <button @click="closeDrawer" class="p-1.5 bg-black text-white rounded-md hover:bg-gray-800 transition-colors">
            <XIcon class="w-5 h-5" />
          </button>
        </div>

        <!-- Drawer Body (Scrollable) -->
        <div class="flex-1 overflow-y-auto px-6 py-8 space-y-6">
          <!-- Title -->
          <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Promotion Title <span class="text-red-500">*</span></label>
            <input 
              v-model="form.title"
              type="text" 
              placeholder="e.g. Black Friday Special" 
              class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-slate-500"
            >
          </div>

          <!-- Promotion Type -->
          <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Promotion Type <span class="text-red-500">*</span></label>
            <div class="relative">
              <select 
                v-model="form.type"
                @change="handleTypeChange"
                class="w-full px-4 py-2.5 bg-[#f0f7ff] dark:bg-slate-800 border border-blue-100 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none appearance-none text-gray-900 dark:text-white"
              >
                <option v-for="ptype in promotionTypes" :key="ptype.value" :value="ptype.value">{{ ptype.label }}</option>
              </select>
              <ChevronDownIcon class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
            </div>
          </div>

          <!-- Dropdown Overlays -->
          <div v-if="isBuyDropdownOpen || isGetDropdownOpen" @click="closeDropdowns" class="fixed inset-0 z-40 bg-transparent"></div>

          <!-- Dynamic JSON Rules based on Type -->
          <div v-if="form.type === 'buy_x_get_y'" class="p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl space-y-4 border border-indigo-100 dark:border-indigo-800/30">
             <h3 class="text-sm font-bold text-indigo-900 dark:text-indigo-300 flex items-center gap-2">
                 <TagIcon class="w-4 h-4" /> Buy X Get Y Configuration
             </h3>
             <div class="grid grid-cols-2 gap-4">
                 <div class="space-y-1.5">
                     <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300">Buy Quantity <span class="text-red-500">*</span></label>
                     <input type="number" v-model="form.rules.buy_qty" min="1" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 outline-none text-sm dark:text-white">
                 </div>
                 <div class="space-y-1.5 relative">
                     <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300">Buy Product <span class="text-red-500">*</span></label>
                     <div @click="isBuyDropdownOpen = !isBuyDropdownOpen; isGetDropdownOpen = false" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg cursor-pointer flex justify-between items-center outline-none text-sm dark:text-white">
                         <span class="truncate block">{{ getProductName(form.rules.buy_product_id) || 'Select Product' }}</span>
                         <ChevronDownIcon class="w-4 h-4 text-gray-400 shrink-0" />
                     </div>
                     <div v-if="isBuyDropdownOpen" class="absolute z-50 w-full mt-1 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg shadow-xl overflow-hidden top-full left-0">
                         <div class="p-2 border-b border-gray-100 dark:border-slate-700 relative">
                             <SearchIcon class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                             <input v-model="buyProductSearch" @click.stop type="text" placeholder="Search product..." class="w-full pl-8 pr-2 py-1.5 text-sm bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 outline-none dark:text-white placeholder:text-gray-400">
                         </div>
                         <div class="max-h-40 overflow-y-auto custom-scrollbar p-1">
                             <div 
                                 v-for="p in filteredBuyProducts" :key="p.id" 
                                 @click="selectBuyProduct(p.id)"
                                 class="px-2 py-1.5 text-sm hover:bg-indigo-50 dark:hover:bg-slate-700 cursor-pointer rounded dark:text-slate-300 truncate transition-colors"
                                 :title="p.name"
                             >
                                 {{ p.name }}
                             </div>
                             <div v-if="filteredBuyProducts.length === 0" class="p-2 text-xs text-gray-500 text-center">No products found</div>
                         </div>
                     </div>
                 </div>
                 <div class="space-y-1.5">
                     <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300">Get Quantity <span class="text-red-500">*</span></label>
                     <input type="number" v-model="form.rules.get_qty" min="1" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 outline-none text-sm dark:text-white">
                 </div>
                 <div class="space-y-1.5 relative">
                     <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300">Get Product <span class="text-red-500">*</span></label>
                     <div @click="isGetDropdownOpen = !isGetDropdownOpen; isBuyDropdownOpen = false" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg cursor-pointer flex justify-between items-center outline-none text-sm dark:text-white">
                         <span class="truncate block">{{ getProductName(form.rules.get_product_id) || 'Select Product' }}</span>
                         <ChevronDownIcon class="w-4 h-4 text-gray-400 shrink-0" />
                     </div>
                     <div v-if="isGetDropdownOpen" class="absolute z-50 w-full mt-1 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg shadow-xl overflow-hidden top-full left-0">
                         <div class="p-2 border-b border-gray-100 dark:border-slate-700 relative">
                             <SearchIcon class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                             <input v-model="getProductSearch" @click.stop type="text" placeholder="Search product..." class="w-full pl-8 pr-2 py-1.5 text-sm bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400 outline-none dark:text-white placeholder:text-gray-400">
                         </div>
                         <div class="max-h-40 overflow-y-auto custom-scrollbar p-1">
                             <div 
                                 v-for="p in filteredGetProducts" :key="p.id" 
                                 @click="selectGetProduct(p.id)"
                                 class="px-2 py-1.5 text-sm hover:bg-indigo-50 dark:hover:bg-slate-700 cursor-pointer rounded dark:text-slate-300 truncate transition-colors"
                                 :title="p.name"
                             >
                                 {{ p.name }}
                             </div>
                             <div v-if="filteredGetProducts.length === 0" class="p-2 text-xs text-gray-500 text-center">No products found</div>
                         </div>
                     </div>
                 </div>
             </div>
             <!-- Buy X Get Y Specific Discount Input -->
             <div class="border-t border-indigo-100 dark:border-indigo-800/30 pt-4 mt-4">
                 <h4 class="text-xs font-semibold text-indigo-800 dark:text-indigo-400 mb-3">Discount applied to the 'Get Product'</h4>
                 <div class="grid grid-cols-2 gap-4">
                     <div class="space-y-1.5">
                         <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300">Discount Value <span class="text-red-500">*</span></label>
                         <input type="number" v-model="form.discount_value" placeholder="0" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 outline-none text-sm dark:text-white">
                     </div>
                     <div class="space-y-1.5">
                         <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300">Discount Unit <span class="text-red-500">*</span></label>
                         <select v-model="form.discount_unit" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg outline-none text-sm dark:text-white">
                             <option value="percentage">Percentage (%)</option>
                             <option value="fixed">Fixed Amount</option>
                         </select>
                     </div>
                 </div>
             </div>
          </div>

          <div v-if="form.type === 'bundle'" class="p-4 bg-fuchsia-50 dark:bg-fuchsia-900/20 rounded-xl space-y-4 border border-fuchsia-100 dark:border-fuchsia-800/30">
             <h3 class="text-sm font-bold text-fuchsia-900 dark:text-fuchsia-300 flex items-center gap-2">
                 <PackageIcon class="w-4 h-4" /> Bundle Configuration
             </h3>
             <div class="space-y-1.5">
                 <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300">Required Items in Cart <span class="text-red-500">*</span></label>
                 <div class="w-full border border-gray-200 dark:border-slate-700 rounded-lg overflow-hidden bg-white dark:bg-slate-800">
                     <div class="p-2 border-b border-gray-100 dark:border-slate-700">
                         <div class="relative">
                             <SearchIcon class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                             <input 
                                 v-model="bundleProductSearch"
                                 type="text" 
                                 placeholder="Search products..." 
                                 class="w-full pl-9 pr-4 py-1.5 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-md focus:ring-2 focus:ring-fuchsia-100 focus:border-fuchsia-400 transition-all outline-none text-xs text-gray-900 dark:text-white placeholder:text-gray-400"
                             >
                         </div>
                     </div>
                     <div class="max-h-40 overflow-y-auto p-2 custom-scrollbar">
                         <label v-for="p in filteredBundleProducts" :key="p.id" class="flex items-center p-1.5 hover:bg-gray-50 dark:hover:bg-slate-700/50 rounded cursor-pointer">
                             <input type="checkbox" :value="p.id" v-model="form.rules.required_items" class="w-3.5 h-3.5 text-fuchsia-600 rounded">
                             <span class="ml-2 text-xs text-gray-700 dark:text-slate-300">{{ p.name }}</span>
                         </label>
                         <div v-if="filteredBundleProducts.length === 0" class="p-3 text-sm text-center text-gray-500 dark:text-gray-400">
                             No products found
                         </div>
                     </div>
                 </div>
             </div>
             <!-- Bundle Specific Discount Input -->
             <div class="border-t border-fuchsia-100 dark:border-fuchsia-800/30 pt-4 mt-4">
                 <h4 class="text-xs font-semibold text-fuchsia-800 dark:text-fuchsia-400 mb-3">Discount applied to the Bundle Total <span class="text-red-500">*</span></h4>
                 <div class="grid grid-cols-2 gap-4">
                     <div class="space-y-1.5">
                         <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300">Discount Value</label>
                         <input type="number" v-model="form.discount_value" placeholder="0" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-fuchsia-100 focus:border-fuchsia-400 outline-none text-sm dark:text-white">
                     </div>
                     <div class="space-y-1.5">
                         <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300">Discount Unit</label>
                         <select v-model="form.discount_unit" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg outline-none text-sm dark:text-white">
                             <option value="percentage">Percentage (%)</option>
                             <option value="fixed">Fixed Amount</option>
                         </select>
                     </div>
                 </div>
             </div>
          </div>

          <!-- Date Range -->
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Start Date <span class="text-red-500">*</span></label>
                <input 
                    v-model="form.start_date"
                    type="date" 
                    class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none text-gray-900 dark:text-white"
                >
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">End Date <span class="text-red-500">*</span></label>
                <input 
                    v-model="form.end_date"
                    type="date" 
                    class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none text-gray-900 dark:text-white"
                >
            </div>
          </div>

          <!-- Target (Hidden for specific strategy types) -->
          <div v-show="!['buy_x_get_y', 'bundle'].includes(form.type)" class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Apply To <span class="text-red-500">*</span></label>
            <div class="relative">
              <select 
                v-model="form.target"
                class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none appearance-none text-gray-900 dark:text-white"
              >
                <option value="all">All Products</option>
                <option value="selected">Selected Products</option>
                <option value="categories">Specific Categories</option>
              </select>
              <ChevronDownIcon class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
            </div>
          </div>

          <div v-show="!['buy_x_get_y', 'bundle'].includes(form.type)">
             <!-- Dynamic Lookups based on target -->
             <div v-if="form.target === 'selected'" class="space-y-1.5">
                 <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Select Products <span class="text-red-500">*</span></label>
                 <div class="w-full border border-gray-200 dark:border-slate-700 rounded-lg overflow-hidden bg-white dark:bg-slate-800">
                     <div class="p-2 border-b border-gray-100 dark:border-slate-700">
                         <div class="relative">
                             <SearchIcon class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                             <input 
                                 v-model="productSearch"
                                 type="text" 
                                 placeholder="Search products..." 
                                 class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-md focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none text-sm text-gray-900 dark:text-white placeholder:text-gray-400"
                             >
                         </div>
                     </div>
                     <div class="max-h-52 overflow-y-auto p-2 space-y-1 custom-scrollbar">
                         <label v-for="p in filteredProducts" :key="p.id" class="flex items-center p-2 hover:bg-gray-50 dark:hover:bg-slate-700/50 rounded-md cursor-pointer transition-colors">
                             <input 
                                 type="checkbox" 
                                 :value="p.id" 
                                 v-model="form.target_ids"
                                 class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 cursor-pointer"
                             >
                             <span class="ml-3 text-sm text-gray-700 dark:text-slate-300">{{ p.name }}</span>
                         </label>
                     </div>
                 </div>
             </div>

             <div v-if="form.target === 'categories'" class="space-y-1.5 mt-4">
                 <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Select Categories <span class="text-red-500">*</span></label>
                 <div class="w-full border border-gray-200 dark:border-slate-700 rounded-lg overflow-hidden bg-white dark:bg-slate-800">
                     <div class="p-2 border-b border-gray-100 dark:border-slate-700">
                         <div class="relative">
                             <SearchIcon class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                             <input 
                                 v-model="categorySearch"
                                 type="text" 
                                 placeholder="Search categories..." 
                                 class="w-full pl-9 pr-4 py-2 bg-gray-50 dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-md focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none text-sm text-gray-900 dark:text-white placeholder:text-gray-400"
                             >
                         </div>
                     </div>
                     <div class="max-h-52 overflow-y-auto p-2 space-y-1 custom-scrollbar">
                         <label v-for="c in filteredCategories" :key="c.id" class="flex items-center p-2 hover:bg-gray-50 dark:hover:bg-slate-700/50 rounded-md cursor-pointer transition-colors">
                             <input 
                                 type="checkbox" 
                                 :value="c.id" 
                                 v-model="form.target_ids"
                                 class="w-4 h-4 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600 cursor-pointer"
                             >
                             <span class="ml-3 text-sm text-gray-700 dark:text-slate-300">{{ c.name }}</span>
                         </label>
                     </div>
                 </div>
             </div>
          </div>

          <!-- Discount Details (Global) -->
          <div v-if="!['buy_x_get_y', 'bundle'].includes(form.type)" class="grid grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Discount Value <span class="text-red-500">*</span></label>
                <input 
                  v-model="form.discount_value"
                  type="number" 
                  placeholder="0.00" 
                  class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none text-gray-900 dark:text-white placeholder:text-gray-400"
                >
            </div>
            <div class="space-y-1.5">
                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Unit <span class="text-red-500">*</span></label>
                <div class="relative">
                  <select 
                    v-model="form.discount_unit"
                    class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none appearance-none text-gray-900 dark:text-white"
                  >
                    <option value="percentage">Percentage (%)</option>
                    <option value="fixed">Fixed Amount</option>
                  </select>
                  <ChevronDownIcon class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
                </div>
            </div>
          </div>

          <!-- Execution Settings -->
          <div class="p-4 bg-gray-50 dark:bg-slate-800/50 rounded-xl space-y-4 border border-gray-100 dark:border-slate-700/50">
             <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
                 <SettingsIcon class="w-4 h-4" /> Execution Rules
             </h3>
             <div class="grid grid-cols-2 gap-4">
                 <div class="space-y-1.5">
                     <label class="block text-xs font-semibold text-gray-700 dark:text-slate-300" title="Higher priority executes first">Priority</label>
                     <input type="number" v-model="form.priority" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg outline-none text-sm dark:text-white" placeholder="0">
                 </div>
                 <div class="flex items-center h-full pt-6">
                     <label class="flex items-center gap-2 cursor-pointer">
                         <input type="checkbox" v-model="form.is_stackable" class="w-4 h-4 text-blue-600 rounded bg-white border-gray-300 focus:ring-blue-500">
                         <span class="text-xs font-semibold text-gray-700 dark:text-slate-300">Stackable Offer</span>
                     </label>
                 </div>
             </div>
          </div>

          <!-- Banner Selector -->
          <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300">Promotion Banner</label>
            <div 
              @click="isMediaLibraryOpen = true"
              class="w-full h-40 border-2 border-dashed border-gray-200 dark:border-slate-700 rounded-2xl flex flex-col items-center justify-center bg-gray-50/50 dark:bg-slate-800/50 hover:bg-white dark:hover:bg-slate-800 hover:border-blue-300 dark:hover:border-blue-500/50 transition-all cursor-pointer relative overflow-hidden group shadow-sm"
            >
              <template v-if="form.bannerPreview || form.bannerUrl">
                <img :src="form.bannerPreview || form.bannerUrl" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" />
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center backdrop-blur-[2px]">
                    <div class="bg-white/90 dark:bg-slate-900/90 text-gray-900 dark:text-white font-black text-[10px] uppercase tracking-widest px-4 py-2 rounded-xl shadow-2xl flex items-center gap-2 transform translate-y-4 group-hover:translate-y-0 transition-transform">
                        <ImageIcon class="w-3.5 h-3.5" />
                        Change Image
                    </div>
                </div>
              </template>
              <template v-else>
                <div class="w-16 h-16 bg-white dark:bg-slate-900 rounded-2xl flex items-center justify-center mb-3 shadow-xl group-hover:scale-110 group-hover:rotate-3 transition-all duration-500">
                    <UploadCloudIcon class="w-8 h-8 text-blue-500" />
                </div>
                <p class="text-[10px] text-gray-400 dark:text-slate-500 font-black uppercase tracking-widest">Click to select banner from gallery</p>
                <p class="text-[8px] text-gray-400 mt-2 font-bold uppercase tracking-tighter italic">High quality images recommended</p>
              </template>
            </div>
          </div>

          <!-- Status -->
          <div class="space-y-1.5">
            <label class="block text-sm font-semibold text-gray-700">Status</label>
            <div class="relative">
              <select 
                v-model="form.is_active"
                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all outline-none appearance-none text-gray-900"
              >
                <option :value="true">Active</option>
                <option :value="false">Inactive</option>
              </select>
              <ChevronDownIcon class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
            </div>
          </div>

          <!-- Notification Rule -->
          <div v-if="!isEditing" class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl space-y-2 border border-emerald-100 dark:border-emerald-800/30">
             <label class="flex items-center gap-3 cursor-pointer group">
                  <div class="relative flex items-center">
                    <input type="checkbox" v-model="form.notify_subscribers" class="w-5 h-5 text-emerald-600 rounded-md border-emerald-300 focus:ring-emerald-500 bg-white transition-all cursor-pointer">
                  </div>
                  <div class="flex flex-col">
                    <span class="text-sm font-bold text-emerald-900 dark:text-emerald-300 group-hover:text-emerald-700 transition-colors">Notify Subscribers via Email</span>
                    <span class="text-[10px] text-emerald-600/70 dark:text-emerald-400/50 font-medium">Send this promotion to all your newsletter subscribers immediately after saving.</span>
                  </div>
             </label>
          </div>
        </div>

        <!-- Drawer Footer -->
        <div class="px-6 py-6 border-t border-gray-100 dark:border-slate-800 flex items-center space-x-3 bg-white dark:bg-slate-900">
          <button 
            @click="closeDrawer"
            type="button"
            class="flex-1 px-4 py-2.5 bg-[#eff4f9] text-[#718eb7] font-semibold rounded-lg hover:bg-[#e2eaf1] transition-colors"
          >
            Cancel
          </button>
          <button 
            @click="handleSubmit"
            type="button"
            :disabled="isSaving"
            class="flex-1 px-4 py-2.5 bg-black text-white font-semibold rounded-lg hover:bg-gray-800 transition-colors shadow-md active:scale-95 disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center space-x-2"
          >
            <span v-if="isSaving" class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></span>
            <span>{{ isEditing ? 'Update Promotion' : 'Save Promotion' }}</span>
          </button>
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
      <div v-if="isOpen" @click="closeDrawer" class="fixed inset-0 bg-black/20 backdrop-blur-sm z-40"></div>
    </Transition>

    <AppConfirmationModal
        :isOpen="isDeleteModalOpen"
        title="Delete Promotion"
        message="Are you sure you want to delete this promotion? This action cannot be undone."
        icon="TrashIcon"
        confirmText="Delete Promotion"
        :isLoading="isDeleting"
        @confirm="handleConfirmDelete"
        @cancel="isDeleteModalOpen = false"
    />
    
    <AppConfirmationModal
        :isOpen="isSendModalOpen"
        title="Send to Subscribers"
        message="Are you sure you want to send this promotion to all your newsletter subscribers? This will start a bulk email campaign."
        icon="SendIcon"
        confirmText="Send Now"
        :isLoading="isSending"
        @confirm="handleConfirmSend"
        @cancel="isSendModalOpen = false"
    />

    <!-- Media Library Component -->
    <AppMediaLibrary 
        :show="isMediaLibraryOpen"
        @close="isMediaLibraryOpen = false"
        @select="handleMediaSelect"
        type-label="Promotion Banner"
    />
  </div>
</template>

<script setup>
import AppMediaLibrary from '~/components/AppMediaLibrary.vue'
import { 
  SearchIcon,
  HomeIcon, 
  ChevronRightIcon, 
  MegaphoneIcon, 
  PlusIcon, 
  XIcon, 
  ChevronDownIcon,
  UploadCloudIcon,
  TrashIcon,
  EditIcon,
  Image as ImageIcon,
  TagIcon,
  PackageIcon,
  SettingsIcon,
  ArrowRightIcon,
  SendIcon
} from 'lucide-vue-next'
import { ref, onMounted, computed } from 'vue'
import { toast } from 'vue-sonner'

const { getAll, createItem, deleteItem } = useCrud()
const router = useRouter()

const items = ref([])
const allProducts = ref([])
const allCategories = ref([])
const productSearch = ref('')
const categorySearch = ref('')
const loading = ref(false)
const isBuyDropdownOpen = ref(false)
const isGetDropdownOpen = ref(false)
const buyProductSearch = ref('')
const getProductSearch = ref('')
const bundleProductSearch = ref('')

const promotionTypes = [
  { label: 'Flash Sale', value: 'flash_sale' },
  { label: 'Flat Discount', value: 'flat_discount' },
  { label: 'Buy X Get Y', value: 'buy_x_get_y' },
  { label: 'Bundle Offer', value: 'bundle' }
]

const filteredProducts = computed(() => {
    if (!productSearch.value) return allProducts.value
    const lower = productSearch.value.toLowerCase()
    return allProducts.value.filter(p => p.name.toLowerCase().includes(lower))
})

const filteredCategories = computed(() => {
    if (!categorySearch.value) return allCategories.value
    const lower = categorySearch.value.toLowerCase()
    return allCategories.value.filter(c => c.name.toLowerCase().includes(lower))
})

const filteredBuyProducts = computed(() => {
    if (!buyProductSearch.value) return allProducts.value
    const lower = buyProductSearch.value.toLowerCase()
    return allProducts.value.filter(p => p.name.toLowerCase().includes(lower))
})

const filteredGetProducts = computed(() => {
    if (!getProductSearch.value) return allProducts.value
    const lower = getProductSearch.value.toLowerCase()
    return allProducts.value.filter(p => p.name.toLowerCase().includes(lower))
})

const filteredBundleProducts = computed(() => {
    if (!bundleProductSearch.value) return allProducts.value
    const lower = bundleProductSearch.value.toLowerCase()
    return allProducts.value.filter(p => p.name.toLowerCase().includes(lower))
})

const getProductName = (id) => {
    if (!id || !allProducts.value) return ''
    const p = allProducts.value.find(p => p.id === id)
    return p ? p.name : ''
}

const handleMediaSelect = (file) => {
    form.value.banner = file.file_url // Store the full URL
    form.value.bannerUrl = file.file_url
    form.value.bannerPreview = null
}

const selectBuyProduct = (id) => {
    form.value.rules.buy_product_id = id
    isBuyDropdownOpen.value = false
    buyProductSearch.value = ''
}

const selectGetProduct = (id) => {
    form.value.rules.get_product_id = id
    isGetDropdownOpen.value = false
    getProductSearch.value = ''
}

const closeDropdowns = () => {
    isBuyDropdownOpen.value = false
    isGetDropdownOpen.value = false
}
const isOpen = ref(false)
const isEditing = ref(false)
const isSaving = ref(false)

const isDeleteModalOpen = ref(false)
const promotionToDelete = ref(null)
const isDeleting = ref(false)

const isSendModalOpen = ref(false)
const promotionToSend = ref(null)
const isSending = ref(false)

const isMediaLibraryOpen = ref(false)

const form = ref({
  id: null,
  title: '',
  type: 'flash_sale',
  start_date: '',
  end_date: '',
  target: 'all',
  target_ids: [],
  discount_value: '',
  discount_unit: 'percentage',
  is_active: true,
  is_stackable: false,
  priority: 0,
  rules: {
      buy_qty: 2,
      buy_product_id: '',
      get_qty: 1,
      get_product_id: '',
      required_items: []
  },
  banner: null,
  bannerPreview: null,
  bannerUrl: null,
  notify_subscribers: false
})

const fetchItems = async () => {
    loading.value = true
    try {
        const response = await getAll('/vendor/promotions')
        items.value = response || []
    } catch (e) {
        console.error('Failed to fetch promotions', e)
        toast.error('Failed to load promotions')
    } finally {
        loading.value = false
    }
}

const fetchLookups = async () => {
    try {
        const pResponse = await getAll('/vendor/products')
        if (pResponse && Array.isArray(pResponse.data)) {
            allProducts.value = pResponse.data
        } else if (Array.isArray(pResponse)) {
            allProducts.value = pResponse
        }

        const cResponse = await getAll('/vendor/attributes/categories')
        if (cResponse && Array.isArray(cResponse.data)) {
            allCategories.value = cResponse.data
        } else if (Array.isArray(cResponse)) {
            allCategories.value = cResponse
        }
    } catch(e) {
        console.error('Failed to fetch lookups')
    }
}

onMounted(() => {
    fetchItems()
    fetchLookups()
})

const openAddDrawer = () => {
    isEditing.value = false
    resetForm()
    isOpen.value = true
}

const handleTypeChange = () => {
    if (form.value.type === 'buy_x_get_y' || form.value.type === 'bundle') {
        form.value.target = 'selected'
    } else if (form.value.type === 'category') {
        form.value.target = 'categories'
    } else {
        form.value.target = 'all'
    }
}

const openEditDrawer = (promotion) => {
    isEditing.value = true
    productSearch.value = ''
    categorySearch.value = ''
    buyProductSearch.value = ''
    getProductSearch.value = ''
    bundleProductSearch.value = ''
    closeDropdowns()
    
    let parsedRules = { buy_qty: 2, buy_product_id: '', get_qty: 1, get_product_id: '', required_items: [] }
    if (promotion.rules) {
        try {
            parsedRules = { ...parsedRules, ...JSON.parse(promotion.rules) }
        } catch (e) { console.error(e) }
    }

    form.value = {
        id: promotion.id,
        title: promotion.title,
        type: promotion.type,
        start_date: promotion.start_date ? promotion.start_date.split('T')[0] : '',
        end_date: promotion.end_date ? promotion.end_date.split('T')[0] : '',
        target: promotion.target,
        target_ids: promotion.target_ids || [],
        discount_value: promotion.discount_value,
        discount_unit: promotion.discount_unit,
        is_active: promotion.is_active,
        is_stackable: promotion.is_stackable || false,
        priority: promotion.priority || 0,
        rules: parsedRules,
        banner: null,
        bannerPreview: null,
        bannerUrl: promotion.banner ? getMediaUrl(promotion.banner) : null,
        notify_subscribers: false
    }
    isOpen.value = true
}

const closeDrawer = () => {
    isOpen.value = false
    setTimeout(resetForm, 300)
}

const resetForm = () => {
    productSearch.value = ''
    categorySearch.value = ''
    buyProductSearch.value = ''
    getProductSearch.value = ''
    bundleProductSearch.value = ''
    closeDropdowns()
    if (form.value.bannerPreview) {
        URL.revokeObjectURL(form.value.bannerPreview)
    }
    form.value = {
        id: null,
        title: '',
        type: 'flash_sale',
        start_date: '',
        end_date: '',
        target: 'all',
        target_ids: [],
        discount_value: '',
        discount_unit: 'percentage',
        is_active: true,
        is_stackable: false,
        priority: 0,
        rules: { buy_qty: 2, buy_product_id: '', get_qty: 1, get_product_id: '', required_items: [] },
        banner: null,
        bannerPreview: null,
        bannerUrl: null,
        notify_subscribers: false
    }
}

const handleBannerUpload = (event) => {
    const file = event.target.files[0]
    if (!file) return
    form.value.banner = file
    if (form.value.bannerPreview) URL.revokeObjectURL(form.value.bannerPreview)
    form.value.bannerPreview = URL.createObjectURL(file)
}

const formatDate = (dateString) => {
    if (!dateString) return ''
    const options = { year: 'numeric', month: 'short', day: 'numeric' }
    return new Date(dateString).toLocaleDateString(undefined, options)
}

const formatType = (type) => {
    if (!type) return ''
    return type.replace(/_/g, ' ')
}

const formatTarget = (target) => {
    if (!target) return ''
    return target.replace(/_/g, ' ')
}

const handleSubmit = async () => {
    if (!form.value.title || !form.value.discount_value || !form.value.start_date || !form.value.end_date) {
        toast.error('Please fill in required fields')
        return
    }

    if (!['buy_x_get_y', 'bundle'].includes(form.value.type)) {
        if (form.value.target !== 'all' && (!form.value.target_ids || form.value.target_ids.length === 0)) {
            toast.error(`Please select at least one ${form.value.target === 'selected' ? 'product' : 'category'}`)
            return
        }
    } else {
        if (form.value.type === 'buy_x_get_y') {
            if (!form.value.rules.buy_qty || !form.value.rules.buy_product_id || !form.value.rules.get_qty || !form.value.rules.get_product_id) {
                toast.error('Please fill in all Buy X Get Y details')
                return
            }
        }
        if (form.value.type === 'bundle') {
            if (!form.value.rules.required_items || form.value.rules.required_items.length === 0) {
                toast.error('Please select at least one required item for the bundle')
                return
            }
        }
    }

    isSaving.value = true
    try {
        const formData = new FormData()
        formData.append('title', form.value.title)
        formData.append('type', form.value.type)
        formData.append('start_date', form.value.start_date)
        formData.append('end_date', form.value.end_date)
        formData.append('target', form.value.target)
        formData.append('discount_value', form.value.discount_value)
        formData.append('discount_unit', form.value.discount_unit)
        formData.append('is_active', form.value.is_active ? 1 : 0)
        formData.append('notify_subscribers', form.value.notify_subscribers ? 1 : 0)
        
        if (form.value.target !== 'all' && form.value.target_ids && form.value.target_ids.length > 0) {
            form.value.target_ids.forEach(id => {
                formData.append('target_ids[]', id)
            })
        }

        // Clean rules based on type and stringify
        let finalRules = {}
        if (form.value.type === 'buy_x_get_y') {
            finalRules = {
                buy_qty: form.value.rules.buy_qty,
                buy_product_id: form.value.rules.buy_product_id,
                get_qty: form.value.rules.get_qty,
                get_product_id: form.value.rules.get_product_id
            }
        } else if (form.value.type === 'bundle') {
            finalRules = {
                required_items: form.value.rules.required_items
            }
        }
        
        formData.append('rules', JSON.stringify(finalRules))
        formData.append('is_stackable', form.value.is_stackable ? 1 : 0)
        formData.append('priority', form.value.priority)

        if (form.value.banner) {
            formData.append('banner', form.value.banner)
        }

        if (isEditing.value) {
            formData.append('_method', 'PUT')
            await createItem(`/vendor/promotions/${form.value.id}`, formData, null, false)
            router.push('/vendor/promotions')
        } else {
            await createItem('/vendor/promotions', formData, null, false)
            router.push('/vendor/promotions')
        }
        closeDrawer()
        fetchItems()
    } catch (e) {
        console.error('Failed to save promotion', e)
        toast.error(e.response?.data?.message || 'Failed to save promotion')
    } finally {
        isSaving.value = false
    }
}

const confirmDelete = (id) => {
    promotionToDelete.value = id
    isDeleteModalOpen.value = true
}

const handleConfirmDelete = async () => {
    if (!promotionToDelete.value) return
    isDeleting.value = true
    try {
        await deleteItem(`/vendor/promotions/${promotionToDelete.value}`)
        router.push('/vendor/promotions')
        fetchItems()
    } catch (e) {
        console.error('Failed to delete promotion', e)
        toast.error('Failed to delete promotion')
    } finally {
        isDeleting.value = false
        isDeleteModalOpen.value = false
        promotionToDelete.value = null
    }
}

const confirmSendToSubscribers = (id) => {
    promotionToSend.value = id
    isSendModalOpen.value = true
}

const handleConfirmSend = async () => {
    if (!promotionToSend.value) return
    isSending.value = true
    try {
        await createItem(`/vendor/promotions/${promotionToSend.value}/send-to-subscribers`, {}, null, false)
    } catch (e) {
        console.error('Failed to send promotion', e)
        toast.error(e.response?.data?.message || 'Failed to start email campaign')
    } finally {
        isSending.value = false
        isSendModalOpen.value = false
        promotionToSend.value = null
    }
}

definePageMeta({
  middleware: 'auth',
  permissions: 'promotions.view'
})
</script>

<style scoped>
.overflow-y-auto::-webkit-scrollbar {
  display: none;
}
.overflow-y-auto {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>
