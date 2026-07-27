<template>
  <div class="p-6 lg:p-8 bg-slate-50 dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
          <button
            @click="$router.back()"
            class="p-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm group"
          >
            <ChevronLeft class="w-5 h-5 text-slate-500 dark:text-slate-400 group-hover:-translate-x-0.5 transition-transform" />
          </button>
          <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Restock Inventory</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Add stock to products and track inventory changes.</p>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <button
            v-for="tab in tabs"
            :key="tab.value"
            @click="activeTab = tab.value"
            :class="[
              'px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border',
              activeTab === tab.value
                ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 border-indigo-200 dark:border-indigo-500/30'
                : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800'
            ]"
          >
            {{ tab.label }}
          </button>
        </div>
      </div>

      <!-- Restock Tab -->
      <div v-if="activeTab === 'restock'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Column -->
        <div class="lg:col-span-2 space-y-6">
          <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/30 dark:bg-slate-800/20">
              <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Stock Entry Details</h2>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Search by name, SKU, or scan a barcode.</p>
            </div>

            <div class="p-6 space-y-6">
              <!-- Search -->
              <div ref="searchContainer">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Product Search</label>
                <div class="relative">
                  <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                  <input
                    ref="searchInput"
                    v-model="productSearch"
                    type="text"
                    placeholder="Search by product name, SKU, or barcode..."
                    class="w-full h-11 pl-10 pr-10 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-lg focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400 text-sm font-medium text-slate-700 dark:text-slate-200"
                    autocomplete="off"
                    @input="debouncedSearch"
                    @keydown.enter.prevent="handleSearchEnter"
                  />
                  <button
                    v-if="productSearch"
                    @click="clearSearch"
                    class="absolute right-3 top-1/2 -translate-y-1/2 p-0.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors"
                  >
                    <X class="w-4 h-4" />
                  </button>
                </div>

                <!-- Dropdown -->
                <div
                  v-if="showSearchDropdown"
                  class="absolute z-20 mt-1.5 max-h-60 w-full overflow-auto rounded-xl bg-white dark:bg-slate-900 py-1 text-sm shadow-lg ring-1 ring-black/5 border border-slate-200 dark:border-slate-700"
                  :style="dropdownStyle"
                >
                  <div v-if="searching" class="px-4 py-3 text-sm text-slate-500 dark:text-slate-400 flex items-center gap-2">
                    <Loader2 class="w-4 h-4 animate-spin" />
                    Searching...
                  </div>
                  <template v-else-if="searchResults.length > 0">
                    <button
                      v-for="res in searchResults"
                      :key="res.id"
                      type="button"
                      @click="selectProduct(res)"
                      class="w-full text-left relative cursor-pointer select-none py-2.5 px-3 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-900 dark:text-white flex items-center gap-3 transition-colors"
                    >
                      <img
                        :src="productImage(res)"
                        class="h-10 w-10 flex-shrink-0 rounded-md object-cover border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800"
                        :alt="res.name"
                      />
                      <div class="min-w-0 flex-1">
                        <span class="block truncate font-medium">{{ res.name }}</span>
                        <span class="block truncate text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                          Stock: {{ res.has_variants ? 'Variants' : (res.stock_qty ?? 0) }}
                          <span v-if="res.unit?.name"> {{ res.unit.name }}</span>
                          · SKU: {{ res.sku || 'N/A' }}
                        </span>
                      </div>
                    </button>
                  </template>
                  <div v-else class="px-4 py-3 text-sm text-slate-500 dark:text-slate-400">
                    No products found. Press Enter to try barcode lookup.
                  </div>
                </div>
              </div>

              <!-- Selected Product Preview -->
              <div
                v-if="selectedProduct"
                class="rounded-xl border border-indigo-100 dark:border-indigo-900/50 bg-indigo-50/50 dark:bg-indigo-900/10 p-4"
              >
                <div class="flex items-start justify-between gap-3">
                  <div class="flex items-center gap-4 min-w-0">
                    <img
                      :src="productImage(selectedProduct)"
                      class="h-16 w-16 rounded-lg object-cover border border-white dark:border-slate-800 shadow-sm bg-white dark:bg-slate-800 flex-shrink-0"
                      :alt="selectedProduct.name"
                    />
                    <div class="min-w-0">
                      <h3 class="font-semibold text-slate-900 dark:text-white truncate">{{ selectedProduct.name }}</h3>
                      <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                        Current Stock:
                        <span class="font-medium text-slate-900 dark:text-slate-300">{{ currentStock }}</span>
                        <span v-if="selectedProduct.unit?.name" class="text-slate-400"> {{ selectedProduct.unit.name }}</span>
                      </p>
                      <p v-if="selectedProduct.sku" class="text-xs text-slate-400 mt-0.5">SKU: {{ selectedProduct.sku }}</p>
                    </div>
                  </div>
                  <button
                    type="button"
                    @click="clearSelection"
                    class="text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 p-1.5 rounded-lg transition-colors flex-shrink-0"
                  >
                    <X class="h-5 w-5" />
                  </button>
                </div>
              </div>

              <div v-if="loadingProduct" class="flex items-center justify-center py-8 text-sm text-slate-500 dark:text-slate-400 gap-2">
                <Loader2 class="w-5 h-5 animate-spin text-indigo-600" />
                Loading product details...
              </div>

              <!-- Variant Selection -->
              <div v-else-if="selectedProduct?.variants?.length > 0">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                  Select Variant <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  <button
                    v-for="v in selectedProduct.variants"
                    :key="v.id"
                    type="button"
                    @click="selectedVariant = v"
                    :class="[
                      'text-left rounded-xl border p-4 transition-all duration-200',
                      selectedVariant?.id === v.id
                        ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-900/20 ring-1 ring-indigo-600 shadow-sm'
                        : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800/50 hover:border-slate-300 dark:hover:border-slate-600'
                    ]"
                  >
                    <div class="flex items-start justify-between gap-2">
                      <div class="min-w-0">
                        <span v-if="v.attributes?.length" class="text-sm font-medium text-slate-900 dark:text-white block truncate">
                          {{ v.attributes.map(a => `${a.attribute?.name}: ${a.value}`).join(', ') }}
                        </span>
                        <span v-else class="text-sm font-medium text-slate-900 dark:text-white">Variant {{ v.sku || v.id }}</span>
                        <span class="text-xs text-slate-500 dark:text-slate-400 mt-1 block">SKU: {{ v.sku || 'N/A' }}</span>
                      </div>
                      <span class="inline-flex items-center rounded-md bg-slate-50 dark:bg-slate-800 px-2 py-1 text-xs font-medium text-slate-600 dark:text-slate-400 ring-1 ring-inset ring-slate-500/10 flex-shrink-0">
                        {{ v.stock_qty ?? 0 }}
                      </span>
                    </div>
                  </button>
                </div>
              </div>

              <!-- Restock Details -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                    Quantity to Add <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model.number="form.quantity"
                    type="number"
                    step="0.01"
                    min="0.01"
                    class="w-full h-11 px-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-lg focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all text-sm font-medium text-slate-700 dark:text-slate-200"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                    Unit Purchase Cost <span class="text-slate-400 font-normal">(Optional)</span>
                  </label>
                  <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500 text-sm">৳</span>
                    <input
                      v-model="form.purchase_price"
                      type="number"
                      step="0.01"
                      min="0"
                      placeholder="0.00"
                      class="w-full h-11 pl-9 pr-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-lg focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all text-sm font-medium text-slate-700 dark:text-slate-200"
                    />
                  </div>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                  Supplier <span class="text-slate-400 font-normal">(Optional)</span>
                </label>
                <select
                  v-model="form.supplier_id"
                  class="w-full h-11 px-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-lg focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all text-sm font-medium text-slate-700 dark:text-slate-200"
                >
                  <option value="">Select a supplier...</option>
                  <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                  Note <span class="text-slate-400 font-normal">(Optional)</span>
                </label>
                <textarea
                  v-model="form.note"
                  rows="3"
                  maxlength="255"
                  placeholder="E.g. Restock from new batch, damaged stock replacement..."
                  class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-lg focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all text-sm text-slate-700 dark:text-slate-200 resize-none"
                />
              </div>
            </div>
          </div>

          <!-- Scanner hint -->
          <div class="rounded-xl border border-indigo-100 dark:border-indigo-900/40 bg-indigo-50/50 dark:bg-indigo-900/10 p-5 flex gap-4">
            <div class="p-2.5 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 rounded-lg flex-shrink-0 h-fit">
              <ScanLine class="w-5 h-5" />
            </div>
            <div>
              <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Barcode Scanner Ready</h3>
              <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Type or scan a barcode in the search field and press Enter to select the product instantly.
              </p>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white mb-5">Stock Summary</h2>
            <dl class="space-y-4 text-sm text-slate-600 dark:text-slate-400">
              <div class="flex justify-between items-center pb-4 border-b border-slate-100 dark:border-slate-800">
                <dt>Selected Product</dt>
                <dd class="font-medium text-slate-900 dark:text-white text-right max-w-[150px] truncate" :title="selectedProduct?.name">
                  {{ selectedProduct?.name || '—' }}
                </dd>
              </div>
              <div v-if="selectedVariant" class="flex justify-between items-center pb-4 border-b border-slate-100 dark:border-slate-800">
                <dt>Variant SKU</dt>
                <dd class="font-medium text-slate-900 dark:text-white">{{ selectedVariant.sku || '—' }}</dd>
              </div>
              <div class="flex justify-between items-center pb-4 border-b border-slate-100 dark:border-slate-800">
                <dt>Current Stock</dt>
                <dd class="font-medium text-slate-900 dark:text-white">{{ selectedProduct ? currentStock : '—' }}</dd>
              </div>
              <div class="flex justify-between items-center pb-4 border-b border-slate-100 dark:border-slate-800">
                <dt>Quantity to Add</dt>
                <dd class="font-medium text-emerald-600 dark:text-emerald-400">+{{ validQuantity }}</dd>
              </div>
              <div v-if="form.purchase_price" class="flex justify-between items-center pb-4 border-b border-slate-100 dark:border-slate-800">
                <dt>Total Cost</dt>
                <dd class="font-medium text-slate-900 dark:text-white">৳{{ totalCost }}</dd>
              </div>
              <div class="flex justify-between items-center pt-1">
                <dt class="font-medium text-slate-900 dark:text-white">Projected Stock</dt>
                <dd class="font-bold text-slate-900 dark:text-white text-lg">{{ selectedProduct ? projectedStock : '—' }}</dd>
              </div>
            </dl>

            <button
              @click="processRestock"
              :disabled="!isReady || loading"
              class="mt-6 w-full flex items-center justify-center gap-2 h-11 rounded-lg bg-indigo-600 hover:bg-indigo-700 px-4 text-sm font-semibold text-white shadow-sm transition-all active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <Loader2 v-if="loading" class="w-4 h-4 animate-spin" />
              <PackagePlus v-else class="w-4 h-4" />
              {{ loading ? 'Processing...' : 'Confirm Restock' }}
            </button>

            <p v-if="selectedProduct?.has_variants && !selectedVariant" class="mt-3 text-xs text-amber-600 dark:text-amber-400 text-center">
              Please select a variant to continue.
            </p>
          </div>

          <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 flex flex-col gap-3 shadow-sm">
            <div class="flex items-center gap-3">
              <div class="p-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-lg">
                <Truck class="w-5 h-5" />
              </div>
              <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Supplier Directory</h3>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400">Manage suppliers to keep your inventory ledger organized.</p>
            <NuxtLink
              to="/vendor/products/suppliers"
              class="text-sm font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 inline-flex items-center gap-1 group"
            >
              Go to Suppliers
              <ChevronRight class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" />
            </NuxtLink>
          </div>
        </div>
      </div>

      <!-- History Tab -->
      <div v-else class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50/30 dark:bg-slate-800/20">
          <div>
            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Stock Movement History</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
              <span class="font-semibold text-slate-900 dark:text-white">{{ logsPagination.total }}</span>
              {{ logsPagination.total === 1 ? 'entry' : 'entries' }}
            </p>
          </div>
          <button
            @click="fetchLogs"
            :disabled="loadingLogs"
            class="p-2 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors disabled:opacity-50"
          >
            <RefreshCw class="w-4 h-4" :class="{ 'animate-spin': loadingLogs }" />
          </button>
        </div>

        <div v-if="loadingLogs" class="py-16 flex justify-center">
          <Loader2 class="w-8 h-8 animate-spin text-indigo-600" />
        </div>

        <div v-else-if="logs.length === 0" class="py-16 text-center px-6">
          <div class="mx-auto w-14 h-14 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-3">
            <Package class="h-6 w-6 text-slate-400" />
          </div>
          <h3 class="text-sm font-semibold text-slate-900 dark:text-white">No stock movements found</h3>
          <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Inventory adjustments and restock logs will appear here.</p>
        </div>

        <div v-else>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
              <thead class="bg-slate-50/50 dark:bg-slate-800/50">
                <tr>
                  <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Date</th>
                  <th scope="col" class="px-3 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Product</th>
                  <th scope="col" class="px-3 py-3.5 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Type</th>
                  <th scope="col" class="px-3 py-3.5 text-right text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Qty Change</th>
                  <th scope="col" class="px-3 py-3.5 text-right text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Balance</th>
                  <th scope="col" class="py-3.5 pl-3 pr-6 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Remarks</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                <tr v-for="log in logs" :key="log.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                  <td class="whitespace-nowrap py-4 pl-6 pr-3 text-sm text-slate-500 dark:text-slate-400">
                    {{ formatDate(log.created_at) }}
                  </td>
                  <td class="px-3 py-4 text-sm max-w-[240px]">
                    <div class="font-medium text-slate-900 dark:text-white truncate" :title="log.product?.name">{{ log.product?.name }}</div>
                    <div v-if="log.variant" class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">SKU: {{ log.variant.sku }}</div>
                  </td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm">
                    <span :class="typeBadgeClass(log.type)">
                      <span class="capitalize">{{ log.type }}</span>
                    </span>
                  </td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-right font-medium" :class="log.quantity > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400'">
                    {{ log.quantity > 0 ? '+' : '' }}{{ log.quantity }}
                  </td>
                  <td class="whitespace-nowrap px-3 py-4 text-sm text-right text-slate-500 dark:text-slate-400">
                    <span class="line-through text-slate-400 dark:text-slate-500 text-xs mr-2">{{ log.old_stock }}</span>
                    <span class="font-medium text-slate-900 dark:text-white">{{ log.new_stock }}</span>
                  </td>
                  <td class="py-4 pl-3 pr-6 text-sm text-slate-500 dark:text-slate-400 max-w-[200px]">
                    <div v-if="log.supplier" class="font-medium text-slate-900 dark:text-white truncate" :title="log.supplier.name">{{ log.supplier.name }}</div>
                    <div class="truncate text-xs mt-0.5" :title="log.note">{{ log.note || '—' }}</div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div v-if="logsPagination.last_page > 1" class="flex flex-col sm:flex-row items-center justify-between gap-4 px-6 py-4 border-t border-slate-100 dark:border-slate-800">
            <p class="text-sm text-slate-500 dark:text-slate-400">
              Showing
              <span class="font-semibold text-slate-900 dark:text-white">{{ logsPagination.from }}</span>
              to
              <span class="font-semibold text-slate-900 dark:text-white">{{ logsPagination.to }}</span>
              of
              <span class="font-semibold text-slate-900 dark:text-white">{{ logsPagination.total }}</span>
            </p>
            <div class="flex items-center gap-1.5">
              <button
                @click="changeLogsPage(logsPagination.current_page - 1)"
                :disabled="logsPagination.current_page === 1"
                class="p-2 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 disabled:opacity-40 disabled:cursor-not-allowed transition-all bg-white dark:bg-slate-900"
              >
                <ChevronLeft class="w-4 h-4" />
              </button>
              <button
                v-for="page in visibleLogPages"
                :key="page"
                @click="page !== '...' && changeLogsPage(page)"
                :class="[
                  'w-9 h-9 flex items-center justify-center rounded-lg text-sm font-semibold transition-all',
                  page === logsPagination.current_page
                    ? 'bg-indigo-600 text-white'
                    : page === '...'
                      ? 'text-slate-400 cursor-default'
                      : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800'
                ]"
              >
                {{ page }}
              </button>
              <button
                @click="changeLogsPage(logsPagination.current_page + 1)"
                :disabled="logsPagination.current_page === logsPagination.last_page"
                class="p-2 rounded-lg border border-slate-200 dark:border-slate-800 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 disabled:opacity-40 disabled:cursor-not-allowed transition-all bg-white dark:bg-slate-900"
              >
                <ChevronRight class="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
definePageMeta({
  layout: 'vendor',
  middleware: 'auth',
  permissions: 'products.edit'
})

import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { toast } from 'vue-sonner'
import {
  ChevronLeft,
  ChevronRight,
  Package,
  PackagePlus,
  Search,
  X,
  Truck,
  RefreshCw,
  ScanLine,
  Loader2
} from 'lucide-vue-next'
import { debounce } from '~/utils'

const { getAll, getById, createItem } = useCrud()

const tabs = [
  { label: 'Restock', value: 'restock' },
  { label: 'History', value: 'history' }
]

const activeTab = ref('restock')
const productSearch = ref('')
const searchResults = ref([])
const selectedProduct = ref(null)
const selectedVariant = ref(null)
const suppliers = ref([])
const logs = ref([])
const searching = ref(false)
const hasSearched = ref(false)
const loadingProduct = ref(false)
const loading = ref(false)
const loadingLogs = ref(false)
const searchContainer = ref(null)
const searchInput = ref(null)
const dropdownStyle = ref({})

const logsPagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
  per_page: 15,
  from: 0,
  to: 0
})

const form = ref({
  product_id: '',
  variant_id: '',
  supplier_id: '',
  quantity: 1,
  purchase_price: '',
  note: ''
})

const showSearchDropdown = computed(() => {
  return productSearch.value.length >= 2 && (searching.value || hasSearched.value)
})

const validQuantity = computed(() => {
  const qty = parseFloat(form.value.quantity)
  return qty > 0 ? qty : 0
})

const currentStock = computed(() => {
  if (selectedVariant.value) return parseFloat(selectedVariant.value.stock_qty) || 0
  if (selectedProduct.value) return parseFloat(selectedProduct.value.stock_qty) || 0
  return 0
})

const totalCost = computed(() => {
  const price = parseFloat(form.value.purchase_price)
  if (!price || !validQuantity.value) return '0.00'
  return (price * validQuantity.value).toLocaleString('en-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
})

const isReady = computed(() => {
  if (!selectedProduct.value || validQuantity.value <= 0) return false
  if (selectedProduct.value.has_variants && !selectedVariant.value) return false
  return true
})

const projectedStock = computed(() => {
  return currentStock.value + validQuantity.value
})

const visibleLogPages = computed(() => {
  const current = logsPagination.value.current_page
  const last = logsPagination.value.last_page

  if (last <= 7) return Array.from({ length: last }, (_, i) => i + 1)
  if (current <= 4) return [1, 2, 3, 4, 5, '...', last]
  if (current >= last - 3) return [1, '...', last - 4, last - 3, last - 2, last - 1, last]
  return [1, '...', current - 1, current, current + 1, '...', last]
})

const productImage = (product) => product?.thumbnail || product?.image || '/placeholder.png'

const typeBadgeClass = (type) => {
  const base = 'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset'
  if (type === 'restock') return `${base} bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/10 dark:text-emerald-400 dark:ring-emerald-500/20`
  if (type === 'sale') return `${base} bg-blue-50 text-blue-700 ring-blue-700/10 dark:bg-blue-900/20 dark:text-blue-400 dark:ring-blue-900/30`
  if (type === 'return') return `${base} bg-violet-50 text-violet-700 ring-violet-600/20 dark:bg-violet-500/10 dark:text-violet-400 dark:ring-violet-500/20`
  if (type === 'adjustment') return `${base} bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/10 dark:text-amber-400 dark:ring-amber-500/20`
  return `${base} bg-slate-50 text-slate-600 ring-slate-500/20 dark:bg-slate-400/10 dark:text-slate-400 dark:ring-slate-400/20`
}

const updateDropdownPosition = () => {
  if (!searchContainer.value) return
  const rect = searchContainer.value.getBoundingClientRect()
  dropdownStyle.value = {
    position: 'fixed',
    top: `${rect.bottom + 4}px`,
    left: `${rect.left}px`,
    width: `${rect.width}px`
  }
}

const fetchSuppliers = async () => {
  try {
    const res = await getAll('/vendor/suppliers')
    suppliers.value = res?.data || []
  } catch (e) {
    console.error(e)
  }
}

const fetchLogs = async () => {
  loadingLogs.value = true
  try {
    const res = await getAll('/vendor/stock/logs', {
      page: logsPagination.value.current_page,
      per_page: logsPagination.value.per_page
    })
    const paginator = res?.data
    logs.value = paginator?.data || []
    logsPagination.value = {
      current_page: paginator?.current_page || 1,
      last_page: paginator?.last_page || 1,
      total: paginator?.total || 0,
      per_page: paginator?.per_page || 15,
      from: paginator?.from || 0,
      to: paginator?.to || 0
    }
  } catch (e) {
    console.error(e)
  } finally {
    loadingLogs.value = false
  }
}

const changeLogsPage = (page) => {
  if (page < 1 || page > logsPagination.value.last_page) return
  logsPagination.value.current_page = page
  fetchLogs()
}

const searchProducts = async () => {
  if (productSearch.value.length < 2) {
    searchResults.value = []
    searching.value = false
    hasSearched.value = false
    return
  }

  searching.value = true
  hasSearched.value = true
  updateDropdownPosition()

  try {
    const res = await getAll('/vendor/products', { search: productSearch.value, per_page: 8 })
    searchResults.value = res?.data || []
  } catch (e) {
    console.error(e)
    searchResults.value = []
  } finally {
    searching.value = false
  }
}

const debouncedSearch = debounce(searchProducts, 300)

const handleSearchEnter = async () => {
  const query = productSearch.value.trim()
  if (!query) return

  try {
    const res = await getAll('/vendor/barcodes/scan', { code: query })
    if (res?.data?.product_id) {
      await loadProduct(res.data.product_id, res.data.variant_id)
      clearSearch()
      return
    }
  } catch {
    // Fall through to product search
  }

  if (searchResults.value.length === 1) {
    await selectProduct(searchResults.value[0])
    return
  }

  if (searchResults.value.length === 0) {
    await searchProducts()
    if (searchResults.value.length === 1) {
      await selectProduct(searchResults.value[0])
      return
    }
  }

  if (searchResults.value.length === 0) {
    toast.error('No product found for this search or barcode.')
  }
}

const loadProduct = async (productId, variantId = null) => {
  loadingProduct.value = true
  selectedVariant.value = null

  try {
    const product = await getById('/vendor/products', productId)
    selectedProduct.value = product
    form.value.product_id = product.id

    if (variantId && product.variants?.length) {
      const variant = product.variants.find(v => v.id === variantId)
      if (variant) selectedVariant.value = variant
    } else if (product.variants?.length === 1) {
      selectedVariant.value = product.variants[0]
    }

    form.value.variant_id = selectedVariant.value?.id || ''
    form.value.purchase_price = selectedVariant.value?.purchase_price || product.purchase_price || ''
  } catch (e) {
    console.error(e)
    toast.error('Failed to load product details.')
  } finally {
    loadingProduct.value = false
  }
}

const selectProduct = async (product) => {
  clearSearch()
  await loadProduct(product.id)
}

const clearSearch = () => {
  productSearch.value = ''
  searchResults.value = []
  searching.value = false
  hasSearched.value = false
}

const clearSelection = () => {
  selectedProduct.value = null
  selectedVariant.value = null
  form.value.product_id = ''
  form.value.variant_id = ''
  form.value.purchase_price = ''
}

const handleClickOutside = (event) => {
  if (searchContainer.value && !searchContainer.value.contains(event.target)) {
    searchResults.value = []
  }
}

watch(selectedVariant, (variant) => {
  form.value.variant_id = variant?.id || ''
  if (variant?.purchase_price) {
    form.value.purchase_price = variant.purchase_price
  }
})

const processRestock = async () => {
  if (!isReady.value) return

  loading.value = true
  try {
    const payload = {
      ...form.value,
      quantity: validQuantity.value,
      purchase_price: form.value.purchase_price || null,
      supplier_id: form.value.supplier_id || null,
      note: form.value.note || null
    }

    const res = await createItem('/vendor/stock/restock', payload, null, false)

    const newStock = res?.data?.new_stock
    if (newStock !== undefined) {
      if (selectedVariant.value) {
        selectedVariant.value.stock_qty = newStock
      } else if (selectedProduct.value) {
        selectedProduct.value.stock_qty = newStock
      }
    }

    form.value.quantity = 1
    form.value.note = ''
    form.value.supplier_id = ''

    logsPagination.value.current_page = 1
    activeTab.value = 'history'
    fetchLogs()
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: 'numeric',
    minute: '2-digit'
  })
}

onMounted(() => {
  fetchSuppliers()
  fetchLogs()
  document.addEventListener('click', handleClickOutside)
  window.addEventListener('resize', updateDropdownPosition)
  window.addEventListener('scroll', updateDropdownPosition, true)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  window.removeEventListener('resize', updateDropdownPosition)
  window.removeEventListener('scroll', updateDropdownPosition, true)
})

watch(activeTab, (tab) => {
  if (tab === 'history') fetchLogs()
})

watch(productSearch, (value) => {
  if (value.length < 2) {
    searchResults.value = []
    hasSearched.value = false
  }
})
</script>
