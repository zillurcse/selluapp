<template>
  <div v-if="isListView" class="max-w-[1600px] mx-auto p-10 bg-[#f8fafc]">
    <!-- LIST VIEW CONTENT -->
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div class="flex items-center gap-4">
        <button @click="$router.back()" class="p-2.5 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition-all shadow-sm active:scale-95 group">
          <ChevronLeft class="w-5 h-5 text-slate-600 group-hover:-translate-x-0.5 transition-transform" />
        </button>
        <div>
          <h1 class="text-2xl font-black text-slate-900 leading-tight tracking-tight">Products</h1>
          <p class="text-sm text-slate-500 font-semibold opacity-80">Manage and track your product inventory efficiently.</p>
        </div>
      </div>
      <NuxtLink to="/vendor/products/create" class="flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black rounded-2xl transition-all shadow-lg shadow-indigo-600/20 active:scale-95 group">
        <Plus class="w-5 h-5 group-hover:rotate-90 transition-transform duration-300" />
        Add Product
      </NuxtLink>
    </div>

    <!-- Status Tabs -->
    <div class="flex flex-wrap items-center gap-2 mb-8">
      <NuxtLink 
        v-for="tab in tabs" 
        :key="tab.value"
        :to="tab.value === 'all' ? '/vendor/products' : `/vendor/products/${tab.value}`"
        :class="[
          'px-5 py-2.5 rounded-xl font-bold text-sm transition-all duration-300 border',
          activeStatus === tab.value 
            ? 'bg-indigo-600 text-white border-indigo-600 shadow-lg shadow-indigo-600/20' 
            : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300 hover:bg-slate-50'
        ]"
      >
        {{ tab.label }}
      </NuxtLink>
    </div>

    <!-- Filter Section Area -->
    <div class="bg-white rounded-[24px] shadow-[0_10px_40px_-15px_rgba(0,0,0,0.05)] border border-slate-200/60 mb-8 p-8">
      <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 flex-grow">
          <!-- Product Name -->
          <div class="flex flex-col gap-2">
            <label class="text-xs font-black text-slate-400 uppercase tracking-[0.1em] ml-1">Product Name</label>
            <div class="relative">
              <input 
                v-model="searchQuery"
                type="text" 
                placeholder="Search products..." 
                class="w-full h-12 pl-5 pr-10 bg-slate-50/50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all placeholder:text-slate-400 text-slate-700 font-semibold"
              >
            </div>
          </div>

          <!-- Category -->
          <div class="flex flex-col gap-2">
            <label class="text-xs font-black text-slate-400 uppercase tracking-[0.1em] ml-1">Category</label>
            <div class="relative">
              <select v-model="selectedCategory" class="w-full h-12 pl-5 pr-10 bg-slate-50/50 border border-slate-200 rounded-2xl appearance-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all text-slate-700 font-semibold cursor-pointer">
                <option value="">All Categories</option>
                <option v-for="cat in listCategories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
              </select>
              <ChevronDown class="absolute right-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-3 lg:items-end">
            <button @click="fetchProducts" class="h-12 px-8 bg-slate-900 hover:bg-slate-800 text-white font-black rounded-2xl transition-all shadow-xl shadow-slate-900/10 active:scale-95 flex items-center justify-center gap-2">
              <Search class="w-4 h-4" />
              Filter
            </button>
            <button @click="clearFilters" class="h-12 px-6 bg-slate-100 hover:bg-slate-200 text-slate-600 font-black rounded-2xl transition-all flex items-center justify-center gap-2 active:scale-95">
              <X class="w-4 h-4" />
              Clear
            </button>
          </div>
        </div>

        <div class="flex lg:items-end">
          <button @click="bulkDelete" :disabled="selectedItems.length === 0" :class="[
            'h-12 px-8 font-black rounded-2xl transition-all flex items-center gap-2 group whitespace-nowrap active:scale-95 shadow-lg',
            selectedItems.length > 0 ? 'bg-red-50 text-red-600 border border-red-100 hover:bg-red-600 hover:text-white shadow-red-600/5' : 'bg-slate-50 text-slate-300 border border-slate-100 cursor-not-allowed shadow-none'
          ]">
            <Trash2 class="w-4 h-4" :class="{ 'group-hover:animate-bounce': selectedItems.length > 0 }" />
            Bulk Delete
          </button>
        </div>
      </div>
    </div>

    <!-- Products Table Container -->
    <div class="bg-white rounded-[24px] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.06)] border border-slate-200/60 group overflow-hidden">
      <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-900 border-b border-slate-800">
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-slate-400">
                <div class="flex items-center gap-4">
                  <span class="text-white">#</span>
                  <input type="checkbox" @change="toggleAll" :checked="isAllSelected" class="w-4 h-4 rounded border-slate-700 bg-transparent text-indigo-600 focus:ring-indigo-500/50 cursor-pointer">
                </div>
              </th>
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-[#f8fafc]">Product</th>
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-[#f8fafc]">Category</th>
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-[#f8fafc]">Price</th>
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-[#f8fafc]">Status</th>
              <th class="py-6 px-8 font-black text-[10px] uppercase tracking-[0.2em] text-[#f8fafc] text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <!-- Empty State -->
            <tr v-if="products.length === 0">
              <td colspan="6" class="py-32 px-8 text-center">
                <div class="flex flex-col items-center justify-center gap-8 max-w-sm mx-auto animate-in fade-in zoom-in duration-700">
                  <div class="w-28 h-28 bg-slate-50 rounded-[40px] flex items-center justify-center border-4 border-white shadow-2xl rotate-3 transition-transform hover:rotate-0 duration-500">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center border border-slate-100 shadow-inner">
                      <Box class="w-8 h-8 text-slate-300" />
                    </div>
                  </div>
                  <div>
                    <h3 class="text-2xl font-black text-slate-800 mb-3 tracking-tight">No products found</h3>
                    <p class="text-slate-500 font-semibold leading-relaxed px-4">
                      Try adjusting your filters or add your first product to get started.
                    </p>
                  </div>
                  <button @click="fetchProducts" class="flex items-center gap-3 px-10 py-4 bg-slate-900 hover:bg-slate-800 text-white font-black rounded-[20px] transition-all shadow-2xl shadow-slate-900/20 active:scale-95 group">
                    <RefreshCw class="w-5 h-5 group-hover:rotate-180 transition-transform duration-700" />
                    Reload Page
                  </button>
                </div>
              </td>
            </tr>
            <!-- Products Rows -->
            <tr v-else v-for="(product, index) in products" :key="product.id" class="border-b border-slate-50 hover:bg-slate-50 transition-colors group/row">
              <td class="px-8 py-5">
                <div class="flex items-center gap-4">
                  <span class="text-xs font-bold text-slate-400">#{{ index + 1 }}</span>
                  <input type="checkbox" v-model="selectedItems" :value="product.id" class="w-4 h-4 rounded border-slate-200 text-indigo-600 focus:ring-indigo-500/50 cursor-pointer">
                </div>
              </td>
              <td class="px-8 py-5">
                <div class="flex items-center">
                  <div class="h-12 w-12 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden flex-shrink-0 shadow-sm">
                    <img v-if="product.image" :src="product.image" class="h-full w-full object-cover">
                    <div v-else class="h-full w-full flex items-center justify-center text-slate-400 bg-slate-50">
                      <Image class="w-5 h-5 opacity-40" />
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-black text-slate-900">{{ product.name }}</div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">SKU: {{ product.sku || 'N/A' }}</div>
                  </div>
                </div>
              </td>
              <td class="px-8 py-5">
                <div class="flex flex-wrap gap-1.5">
                  <span v-for="cat in product.categories" :key="cat.id" class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-black uppercase tracking-wider">
                    {{ cat.name }}
                  </span>
                  <span v-if="!product.categories?.length || product.categories?.length === 0" class="text-xs text-slate-400 font-mediumitalic">Uncategorized</span>
                </div>
              </td>
              <td class="px-8 py-5">
                <div class="text-sm font-black text-slate-900">৳{{ product.sale_price }}</div>
                <div v-if="product.purchase_price" class="text-[10px] font-bold text-slate-400 line-through opacity-60 mt-0.5">৳{{ product.purchase_price }}</div>
              </td>
              <td class="px-8 py-5">
                <span :class="[
                  'px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider inline-flex items-center gap-1.5',
                  product.status === 'published' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 
                  product.status === 'draft' ? 'bg-slate-50 text-slate-500 border border-slate-100' :
                  'bg-amber-50 text-amber-600 border border-amber-100'
                ]">
                  <span class="w-1.5 h-1.5 rounded-full" :class="[
                    product.status === 'published' ? 'bg-emerald-600' : 
                    product.status === 'draft' ? 'bg-slate-500' :
                    'bg-amber-600'
                  ]"></span>
                  {{ product.status }}
                </span>
              </td>
              <td class="px-8 py-5 text-right">
                <div class="flex justify-end gap-2 opacity-0 group-hover/row:opacity-100 transition-all duration-300 translate-x-4 group-hover/row:translate-x-0">
                  <NuxtLink :to="`/vendor/products/${product.id}`" class="p-2 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-lg transition-all shadow-sm">
                    <Pencil class="w-4 h-4" />
                  </NuxtLink>
                  <button @click="deleteProduct(product.id)" class="p-2 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition-all shadow-sm">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div v-else class="max-w-[1600px] mx-auto p-10 bg-[#f8fafc] dark:bg-slate-950 transition-colors duration-300">
    <!-- EDIT VIEW CONTENT -->
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">Edit Product</h1>
      <NuxtLink to="/vendor/products" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 border border-transparent dark:border-slate-700 text-gray-700 dark:text-slate-300 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-700 text-sm font-medium transition-colors">
        Back to List
      </NuxtLink>
    </div>

    <div v-if="isLoading" class="flex flex-col items-center justify-center py-20">
       <div class="w-12 h-12 border-4 border-blue-600 border-t-transparent rounded-full animate-spin mb-4"></div>
       <p class="text-gray-500">Loading product details...</p>
    </div>

    <form v-else @submit.prevent="updateProduct" class="grid grid-cols-12 gap-6">
      <!-- Left Column (Main Content) -->
      <div class="col-span-12 lg:col-span-9 space-y-6">
        
        <!-- Basic Info -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 p-6 transition-colors">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Product Name <span class="text-red-500">*</span></label>
              <input v-model="form.name" type="text" placeholder="Product Name" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all bg-white dark:bg-slate-900 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-slate-500" required @input="generateSlug" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Product Slug <span class="text-red-500">*</span></label>
              <input v-model="form.slug" type="text" placeholder="Product Slug" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all bg-gray-50/50 dark:bg-slate-800/50 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-slate-500" required />
            </div>
            <div class="col-span-1 md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Short Description</label>
              <textarea v-model="form.short_description" rows="3" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all bg-white dark:bg-slate-900 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-slate-500"></textarea>
            </div>
          </div>
        </div>

        <!-- Identifiers -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 p-6 transition-colors">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5 uppercase tracking-tighter font-black opacity-60">SKU (Stock Keeping Unit)</label>
              <div class="flex gap-2">
                 <input v-model="form.sku" type="text" placeholder="Ex: UORLA-14464" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all bg-white dark:bg-slate-900 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-slate-500 font-bold uppercase"  />
                 <button @click="generateRandomSku" type="button" class="px-4 py-2 bg-gray-100 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-gray-700 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-700 transition-colors shadow-sm">
                   <RefreshCw class="w-4 h-4" />
                 </button>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Product Code <span class="text-red-500">*</span></label>
              <input v-model="form.product_code" type="text" placeholder="Ex: PRD-260219010119" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all bg-white dark:bg-slate-900 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-slate-500" required />
            </div>
          </div>
        </div>

        <!-- Relations -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 p-6 transition-colors">
           <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
             <div>
                <AppSearchSelect
                  v-model="form.category_ids"
                  :items="formCategories"
                  label="Category"
                  required
                  multiple
                  @onCreate="handleCategoryCreate"
                />
             </div>
             <div>
                <AppSearchSelect
                  v-model="form.brand_id"
                  :items="brands"
                  label="Brand"
                  @onCreate="handleBrandCreate"
                />
             </div>
             <div>
                <AppSearchSelect
                  v-model="form.unit_id"
                  :items="units"
                  label="Unit"
                  @onCreate="handleUnitCreate"
                />
             </div>
           </div>
        </div>

        <!-- Pricing & Inventory -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 p-6 transition-colors">
           <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Purchase Price</label>
                <div class="relative">
                  <span class="absolute left-3 top-2.5 text-gray-500 dark:text-slate-400 text-sm font-semibold">৳</span>
                  <input v-model="form.purchase_price" type="number" step="0.01" class="w-full pl-7 pr-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all bg-white dark:bg-slate-900 text-gray-900 dark:text-white" />
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Sale Price <span class="text-red-500">*</span></label>
                <div class="relative">
                  <span class="absolute left-3 top-2.5 text-gray-500 dark:text-slate-400 text-sm font-semibold">৳</span>
                  <input v-model="form.sale_price" type="number" step="0.01" class="w-full pl-7 pr-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all bg-white dark:bg-slate-900 text-gray-900 dark:text-white" required />
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Discount Price</label>
                <div class="relative">
                  <span class="absolute left-3 top-2.5 text-gray-500 dark:text-slate-400 text-sm font-semibold">৳</span>
                  <input v-model="form.discount_price" type="number" step="0.01" class="w-full pl-7 pr-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all bg-white dark:bg-slate-900 text-gray-900 dark:text-white" />
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Stock Qty <span class="text-red-500">*</span></label>
                <input v-model="form.stock_qty" type="number" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all bg-white dark:bg-slate-900 text-gray-900 dark:text-white" required />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Weight (kg)</label>
                <div class="relative">
                  <input v-model="form.weight" type="number" step="0.01" min="0" placeholder="0.00" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all bg-white dark:bg-slate-900 text-gray-900 dark:text-white pr-10" />
                  <span class="absolute right-3 top-2.5 text-gray-400 dark:text-slate-500 text-xs font-semibold">kg</span>
                </div>
              </div>
           </div>
           
           <div class="mt-6 flex items-center">
             <label class="relative inline-flex items-center cursor-pointer">
               <input type="checkbox" v-model="form.has_variants" class="sr-only peer">
               <div class="w-11 h-6 bg-gray-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 dark:after:border-slate-600 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
               <span class="ml-3 text-sm font-medium text-gray-700 dark:text-slate-300">Enable Variants</span>
             </label>
           </div>
        </div>

        <!-- Product Variants Configuration -->
        <div v-if="form.has_variants" class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 p-6 transition-colors space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <Box class="w-5 h-5 text-primary-500" />
                    Product Variants
                </h3>
                <button type="button" @click="addAttributeLine" class="flex items-center gap-2 px-4 py-2 bg-primary-50 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900/50 text-sm font-bold transition-all">
                    <Plus class="w-4 h-4" />
                    Add Attribute
                </button>
            </div>

            <div class="space-y-4">
                <div v-for="(config, index) in selectedAttributesConfig" :key="index" class="p-4 bg-gray-50/50 dark:bg-slate-800/50 rounded-xl border border-gray-100 dark:border-slate-800 relative group animate-in fade-in slide-in-from-top-2 duration-300">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <AppSearchSelect
                            v-model="config.attribute_id"
                            :items="productAttributesList"
                            label="Select Attribute"
                            placeholder="Choice Attribute"
                        />
                        <div v-if="config.attribute_id">
                            <label class="block text-xs font-black text-gray-500 dark:text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Select Values</label>
                            <AppSearchSelect
                                v-model="config.value_ids"
                                :items="productAttributesList.find(a => a.id === config.attribute_id)?.values || []"
                                label-key="value"
                                value-key="id"
                                multiple
                                placeholder="Choose Values"
                            />
                        </div>
                    </div>
                    <button v-if="selectedAttributesConfig.length > 1" @click="removeAttributeLine(index)" class="absolute -top-2 -right-2 p-1.5 bg-red-500 text-white rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-all hover:scale-110">
                        <X class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>

            <!-- Variants Table (Matrix) -->
            <div v-if="generatedVariants.length > 0" class="mt-8 border-t border-gray-100 dark:border-slate-800 pt-8 overflow-hidden">
                <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest mb-6">Generated Variants Matrix ({{ generatedVariants.length }})</h4>
                <div class="overflow-x-auto rounded-xl border border-gray-100 dark:border-slate-800">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-slate-800/50 border-b border-gray-100 dark:border-slate-800">
                                <th class="p-4 text-[10px] font-black uppercase text-gray-400 tracking-tighter">Variant Info</th>
                                <th class="p-4 text-[10px] font-black uppercase text-gray-400 tracking-tighter">SKU</th>
                                <th class="p-4 text-[10px] font-black uppercase text-gray-400 tracking-tighter">Price (৳)</th>
                                <th class="p-4 text-[10px] font-black uppercase text-gray-400 tracking-tighter">Stock</th>
                                <th class="p-4 text-[10px] font-black uppercase text-gray-400 tracking-tighter text-center">Image</th>
                                <th class="p-4 text-[10px] font-black uppercase text-gray-400 tracking-tighter text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-slate-800">
                            <tr v-for="(variant, vIdx) in generatedVariants" :key="variant._key" class="hover:bg-gray-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="p-4">
                                    <div class="flex items-center gap-2">
                                        <div v-for="attr in variant.attributes" :key="attr.id" class="px-2 py-1 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-md shadow-sm">
                                            <span class="text-[10px] font-black text-gray-400 dark:text-slate-500 block leading-none mb-0.5">{{ attr.attribute_name }}</span>
                                            <span class="text-xs font-bold text-gray-900 dark:text-white">{{ attr.value }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <input v-model="variant.sku" type="text" class="w-32 px-3 py-1.5 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-lg text-xs font-bold focus:ring-2 focus:ring-primary-500/20" placeholder="SKU" />
                                </td>
                                <td class="p-4">
                                    <input v-model="variant.price" type="number" class="w-24 px-3 py-1.5 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-lg text-xs font-bold focus:ring-2 focus:ring-primary-500/20" />
                                </td>
                                <td class="p-4">
                                    <input v-model="variant.stock_qty" type="number" class="w-20 px-3 py-1.5 bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-800 rounded-lg text-xs font-bold focus:ring-2 focus:ring-primary-500/20" />
                                </td>
                                <td class="p-4">
                                    <div class="flex flex-col items-center gap-1.5">
                                       <button type="button" @click="openMediaLibrary('variant', vIdx)" class="w-14 h-14 bg-gray-50 dark:bg-slate-800 border-2 border-dashed border-gray-200 dark:border-slate-700 rounded-xl flex items-center justify-center hover:border-primary-500 transition-all overflow-hidden relative group">
                                          <img v-if="variant.imagePreview" :src="variant.imagePreview" class="w-full h-full object-cover" />
                                          <Plus v-else class="w-4 h-4 text-gray-400" />
                                          <div v-if="variant.imagePreview" class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                             <Pencil class="w-4 h-4 text-white" />
                                          </div>
                                       </button>
                                       <button v-if="variant.imagePreview" type="button" @click="removeVariantImage(vIdx)" class="text-[9px] font-black text-red-400 hover:text-red-600 uppercase tracking-widest flex items-center gap-0.5 transition-colors">
                                          <X class="w-2.5 h-2.5" /> Remove
                                       </button>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="flex justify-center">
                                        <button type="button" @click="variant.is_active = !variant.is_active" :class="[
                                            'px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest transition-all',
                                            variant.is_active ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-red-50 text-red-600 border border-red-100'
                                        ]">
                                            {{ variant.is_active ? 'Active' : 'Hidden' }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Accordions -->
        <div class="space-y-4">
           <!-- Product Images -->
           <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 overflow-hidden transition-colors">
               <button type="button" @click="toggleAccordion('images')" class="w-full px-8 py-5 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-slate-800/50 bg-white dark:bg-slate-900 transition-colors">
                  <div class="flex items-center font-medium text-blue-700 dark:text-blue-400 group">
                    <span class="mr-3 p-1 rounded-md bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/50 transition-colors">
                         <Image class="w-4 h-4" />
                     </span>
                     Product Images
                  </div>
                  <ChevronDown class="w-5 h-5 text-gray-400 dark:text-slate-500 transition-transform duration-300" :class="{'rotate-180': sections.images}" />
               </button>
               <div v-show="sections.images" class="p-8 border-t border-gray-100 dark:border-slate-800">
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                     <!-- Thumbnail -->
                     <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Thumbnail (Max 5MB)</label>
                        <div 
                           @click="openMediaLibrary('thumbnail')" 
                           class="border-2 border-dashed border-gray-200 dark:border-slate-700 hover:border-primary-400 dark:hover:border-primary-500 bg-gray-50/50 dark:bg-slate-800/50 hover:bg-primary-50/10 dark:hover:bg-primary-900/10 rounded-2xl h-64 flex flex-col items-center justify-center transition-all cursor-pointer relative overflow-hidden group shadow-sm"
                        >
                           <div v-if="thumbnailPreview" class="absolute inset-0 w-full h-full p-2 animate-in fade-in duration-300">
                              <img :src="thumbnailPreview" class="w-full h-full object-cover rounded-xl shadow-md" />
                              <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-xl m-2">
                                <span class="text-white text-[10px] font-black uppercase tracking-widest bg-primary-600 px-4 py-2 rounded-lg shadow-lg">Change Image</span>
                              </div>
                           </div>
                           <div v-else class="text-center p-6">
                              <div class="w-16 h-16 rounded-2xl bg-white dark:bg-slate-800 shadow-sm flex items-center justify-center mx-auto mb-4 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                                <UploadCloud class="w-8 h-8 text-primary-500" />
                              </div>
                              <span class="text-xs font-black text-gray-900 dark:text-white block mb-1 uppercase tracking-tight">Select from Gallery</span>
                              <span class="text-[10px] text-gray-400 dark:text-slate-500 font-bold uppercase">400x400 Recommended</span>
                           </div>
                        </div>
                     </div>
                     <!-- Gallery -->
                      <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Gallery Images (Max 10 Images)</label>
                         <div 
                           @click="openMediaLibrary('gallery')"
                           class="border-2 border-dashed border-gray-200 dark:border-slate-700 hover:border-primary-400 dark:hover:border-primary-500 bg-gray-50/50 dark:bg-slate-800/50 hover:bg-primary-50/10 dark:hover:bg-primary-900/10 rounded-2xl h-64 flex flex-col items-center justify-center transition-all cursor-pointer relative overflow-hidden group shadow-sm"
                         >
                           <div class="text-center p-6">
                              <div class="w-16 h-16 rounded-2xl bg-white dark:bg-slate-800 shadow-sm flex items-center justify-center mx-auto mb-4 group-hover:scale-110 group-hover:-rotate-3 transition-all duration-300">
                                <Library class="w-8 h-8 text-primary-500" />
                              </div>
                              <span class="text-xs font-black text-gray-900 dark:text-white block mb-1 uppercase tracking-tight">Open Media Gallery</span>
                              <span class="text-[10px] text-gray-400 dark:text-slate-500 font-bold uppercase">Select multiple images</span>
                           </div>
                        </div>
                     </div>
                        <div v-if="galleryItems.length > 0" class="mt-6 flex flex-wrap gap-4">
                           <div 
                             v-for="(item, idx) in galleryItems" 
                             :key="item.id"
                             draggable="true"
                             @dragstart="onDragStart(idx)"
                             @dragover.prevent="onDragOver($event, idx)"
                             @drop="onDrop(idx)"
                             class="w-24 h-24 border-2 border-transparent hover:border-primary-500 dark:hover:border-primary-400 rounded-xl overflow-hidden relative group shadow-sm ring-1 ring-black/5 dark:ring-white/5 cursor-move transition-all active:scale-95 bg-white dark:bg-slate-800"
                           >
                              <img :src="item.preview" class="w-full h-full object-cover pointer-events-none" />
                              <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                                 <GripVertical class="w-6 h-6 text-white" />
                              </div>
                              <button @click.stop="removeGalleryImage(idx)" type="button" class="absolute -top-1 -right-1 bg-red-500 text-white p-1 rounded-full opacity-100 sm:opacity-0 group-hover:opacity-100 transition-all hover:bg-red-600 shadow-sm z-20">
                                 <X class="w-3.5 h-3.5" />
                              </button>
                              <div class="absolute bottom-1 left-1 bg-black/50 text-[8px] text-white px-1.5 py-0.5 rounded font-bold uppercase transition-opacity opacity-0 group-hover:opacity-100">
                                 #{{ idx + 1 }}
                              </div>
                           </div>
                        </div>
                     </div>
               </div>
           </div>

           <!-- Product Description & SEO -->
           <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 overflow-hidden transition-colors">
               <button type="button" @click="toggleAccordion('seo')" class="w-full px-8 py-5 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-slate-800/50 bg-white dark:bg-slate-900 transition-colors">
                  <div class="flex items-center font-medium text-orange-700 dark:text-orange-400 group">
                    <span class="mr-3 p-1 rounded-md bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 group-hover:bg-orange-100 dark:group-hover:bg-orange-900/50 transition-colors">
                        <Pencil class="w-4 h-4" />
                     </span>
                     Product Description & SEO Settings
                  </div>
                  <ChevronDown class="w-5 h-5 text-gray-400 dark:text-slate-500 transition-transform duration-300" :class="{'rotate-180': sections.seo}" />
               </button>
               <div v-show="sections.seo" class="p-8 border-t border-gray-100 dark:border-slate-800 space-y-8">
                  <!-- Description -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Product Description <span class="text-red-500">*</span></label>
                     <div class="border border-gray-200 dark:border-slate-700 rounded-xl overflow-hidden shadow-sm">
                        <AppRichEditor v-model="form.description" placeholder="Enter comprehensive product description here..." />
                     </div>
                  </div>

                  <!-- SEO Fields -->
                  <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500 mr-2"></span> Search Engine Optimization
                    </h3>
                    <div class="space-y-5 pl-3 border-l-2 border-orange-100 dark:border-orange-900/50">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Meta Title</label>
                            <input v-model="form.seo_title" type="text" placeholder="Best Product Name | YourBrand" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-slate-500 bg-white dark:bg-slate-900 text-gray-900 dark:text-white" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Meta Description</label>
                            <textarea v-model="form.seo_description" rows="3" placeholder="Brief and catchy description for search engines (150-160 characters)..." class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-slate-500 bg-white dark:bg-slate-900 text-gray-900 dark:text-white"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Meta Keywords</label>
                            <input v-model="form.seo_keywords" type="text" placeholder="electronics, gadget, smart home" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-slate-500 bg-white dark:bg-slate-900 text-gray-900 dark:text-white" />
                        </div>
                    </div>
                  </div>
               </div>
           </div>

           <!-- FAQ -->
           <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 overflow-hidden transition-colors">
               <button type="button" @click="toggleAccordion('faq')" class="w-full px-8 py-5 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-slate-800/50 bg-white dark:bg-slate-900 transition-colors">
                  <div class="flex items-center font-medium text-gray-900 dark:text-white group">
                     <span class="mr-3 p-1 rounded-md bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400 group-hover:bg-primary-50 dark:group-hover:bg-primary-900/30 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                        <HelpCircle class="w-4 h-4" />
                     </span>
                     FAQ (Questions & Answers)
                  </div>
                  <ChevronDown class="w-5 h-5 text-gray-400 dark:text-slate-500 transition-transform duration-300" :class="{'rotate-180': sections.faq}" />
               </button>
               <div v-show="sections.faq" class="p-8 border-t border-gray-100 dark:border-slate-800 space-y-4">
                  <div v-for="(faq, index) in localFaqs" :key="index" class="border border-gray-200 dark:border-slate-700 rounded-xl p-5 bg-gray-50/50 dark:bg-slate-800/50 relative group hover:border-gray-300 dark:hover:border-slate-600 transition-colors">
                     <div class="grid gap-4">
                        <div>
                           <label class="block text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Question</label>
                           <input v-model="faq.question" type="text" placeholder="Enter question" class="w-full px-4 py-2 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-sm transition-all placeholder:text-gray-400 dark:placeholder:text-slate-500" />
                        </div>
                        <div>
                           <label class="block text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Answer</label>
                           <textarea v-model="faq.answer" rows="2" placeholder="Enter answer" class="w-full px-4 py-2 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-sm transition-all placeholder:text-gray-400 dark:placeholder:text-slate-500"></textarea>
                        </div>
                     </div>
                     <button @click="removeFaq(index)" type="button" class="absolute -top-2 -right-2 bg-white dark:bg-slate-800 text-red-500 dark:text-red-400 p-1 rounded-full shadow-sm border border-gray-100 dark:border-slate-700 opacity-0 group-hover:opacity-100 transition-all hover:bg-red-50 dark:hover:bg-red-900/50">
                        <X class="w-4 h-4" />
                     </button>
                  </div>
                  <button @click="addFaq" type="button" class="px-4 py-2 bg-white dark:bg-slate-900 border border-dashed border-gray-300 dark:border-slate-700 text-gray-600 dark:text-slate-300 text-sm font-medium rounded-lg hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 dark:hover:border-primary-400 flex items-center w-full justify-center transition-all">
                     <Plus class="w-4 h-4 mr-2" /> Add New FAQ
                  </button>
               </div>
           </div>

           <!-- Specifications -->
           <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 overflow-hidden transition-colors border border-gray-100 dark:border-slate-800">
               <button type="button" @click="toggleAccordion('specifications')" class="w-full px-8 py-5 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-slate-800/50 bg-white dark:bg-slate-900 transition-colors">
                  <div class="flex items-center font-medium text-gray-900 dark:text-white group">
                     <span class="mr-3 p-1 rounded-md bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400 group-hover:bg-primary-50 dark:group-hover:bg-primary-900/30 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                        <ListChecks class="w-4 h-4" />
                     </span>
                     Specifications & Key Features
                  </div>
                  <ChevronDown class="w-5 h-5 text-gray-400 dark:text-slate-500 transition-transform duration-300" :class="{'rotate-180': sections.specifications}" />
               </button>
                <div v-show="sections.specifications" class="p-8 border-t border-gray-100 dark:border-slate-800 space-y-8">
                   <!-- Grouped Dynamic Specifications -->
                   <div v-for="(group, groupIndex) in localSpecificationGroups" :key="groupIndex" class="p-6 bg-gray-50/50 dark:bg-slate-800/30 rounded-2xl border border-gray-100 dark:border-slate-800/50 relative group/section">
                      <div class="flex items-center justify-between mb-6">
                         <div class="flex-grow max-w-md">
                            <label class="block text-[10px] font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Section Title</label>
                            <input v-model="group.title" type="text" placeholder="e.g. Key Features, Power Source, Dimensions" class="w-full px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none text-sm font-bold text-gray-900 dark:text-white transition-all shadow-sm" />
                         </div>
                         <button @click="removeSpecGroup(groupIndex)" type="button" class="mt-4 p-2 text-red-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all opacity-0 group-hover/section:opacity-100">
                            <Trash2 class="w-4 h-4" />
                         </button>
                      </div>

                      <div class="space-y-3">
                         <div v-for="(item, itemIndex) in group.items" :key="itemIndex" class="grid grid-cols-12 gap-3 items-start group/item">
                            <div class="col-span-12 sm:col-span-5">
                               <input v-model="item.label" type="text" placeholder="Label (e.g. Battery)" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-sm transition-all" />
                            </div>
                            <div class="col-span-12 sm:col-span-6">
                               <input v-model="item.value" type="text" placeholder="Value (e.g. 5000mAh)" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-sm transition-all shadow-sm" />
                            </div>
                            <div class="col-span-12 sm:col-span-1 flex justify-end">
                               <button @click="removeSpecItem(groupIndex, itemIndex)" type="button" class="p-2.5 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                                  <X class="w-4 h-4" />
                               </button>
                            </div>
                         </div>
                      </div>

                      <button @click="addSpecItem(groupIndex)" type="button" class="mt-4 px-3 py-1.5 text-xs font-bold text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-all flex items-center">
                         <Plus class="w-3.5 h-3.5 mr-1" /> Add Item
                      </button>
                   </div>

                   <button @click="addSpecGroup" type="button" class="w-full py-4 bg-white dark:bg-slate-900 border-2 border-dashed border-gray-200 dark:border-slate-800 text-gray-500 dark:text-slate-400 font-bold rounded-2xl hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 transition-all flex items-center justify-center gap-2 group">
                      <div class="p-1 px-2.5 bg-gray-50 dark:bg-slate-800 rounded-lg group-hover:bg-primary-50 dark:group-hover:bg-primary-900/30 transition-colors">
                        <Plus class="w-4 h-4" />
                      </div>
                      Add New Specification Section
                   </button>
                </div>

           </div>

           <!-- Dropshipping -->
           <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 overflow-hidden transition-colors">
               <button type="button" @click="toggleAccordion('dropshipping')" class="w-full px-8 py-5 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-slate-800/50 bg-white dark:bg-slate-900 transition-colors">
                  <div class="flex items-center font-medium text-purple-700 dark:text-purple-400 group">
                    <span class="mr-3 p-1 rounded-md bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 group-hover:bg-purple-100 dark:group-hover:bg-purple-900/50 transition-colors">
                        <Box class="w-4 h-4" />
                     </span>
                     Dropshipping Product
                  </div>
                  <ChevronDown class="w-5 h-5 text-gray-400 dark:text-slate-500 transition-transform duration-300" :class="{'rotate-180': sections.dropshipping}" />
               </button>
               <div v-show="sections.dropshipping" class="p-8 border-t border-gray-100 dark:border-slate-800 bg-purple-50/30 dark:bg-purple-900/10">
                  <div class="mb-6">
                     <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" v-model="form.is_dropshipping" class="w-5 h-5 text-purple-600 dark:text-purple-500 focus:ring-purple-500 border-gray-300 dark:border-slate-600 dark:bg-slate-800 rounded transition-all" />
                        <span class="text-sm font-medium text-gray-700 dark:text-slate-300">Enable Dropshipping for this product</span>
                     </label>
                  </div>

                  <div v-if="form.is_dropshipping" class="space-y-6 pl-8 border-l-2 border-purple-200 dark:border-purple-900">
                     <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Product Full URL <span class="text-red-500">*</span></label>
                        <input v-model="form.dropshipping_url" type="url" placeholder="https://example.com/product" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-slate-500 bg-white dark:bg-slate-900 text-gray-900 dark:text-white" />
                     </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Product Code/SKU</label>
                        <input v-model="form.dropshipping_sku" type="text" placeholder="Vendor SKU" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-slate-500 bg-white dark:bg-slate-900 text-gray-900 dark:text-white" />
                     </div>
                  </div>
               </div>
           </div>
        </div>

      </div>

      <!-- Right Column (Sidebar) -->
      <div class="col-span-12 lg:col-span-3 space-y-6">
        
        <!-- Product Attributes -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 p-6 transition-colors">
           <h3 class="font-medium text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-100 dark:border-slate-800">Product Attributes</h3>
           <div class="space-y-4">
              <div v-for="(label, key) in attributeToggles" :key="key" class="flex items-center justify-between">
                 <span class="text-sm text-gray-700 dark:text-slate-300">{{ label }}</span>
                 <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="form[key]" class="sr-only peer" :checked="form[key]">
                    <div class="w-9 h-5 bg-gray-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 dark:after:border-slate-600 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary-600"></div>
                 </label>
              </div>
           </div>

           <!-- Stock Visibility (New Section) -->
           <h3 class="font-medium text-gray-900 dark:text-white mt-6 mb-4 pb-2 border-b border-gray-100 dark:border-slate-800">Stock Visibility State</h3>
           <div class="space-y-4">
              <div class="grid grid-cols-1 gap-3">
                 <label v-for="state in ['quantity', 'text', 'hide']" :key="state" class="flex items-center p-3 rounded-xl border border-gray-100 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer group" :class="{'ring-2 ring-primary-500/20 border-primary-500 bg-primary-50/5': form.stock_visibility_state === state}">
                    <input type="radio" v-model="form.stock_visibility_state" :value="state" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 focus:ring-primary-500 dark:bg-slate-700 dark:border-slate-600">
                    <div class="ml-3">
                       <span class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tighter block">
                          {{ state === 'quantity' ? 'Show Stock Quantity' : state === 'text' ? 'Show Stock With Text Only' : 'Hide Stock' }}
                       </span>
                    </div>
                 </label>
              </div>

              <div class="mt-4 p-4 bg-orange-50/30 dark:bg-orange-900/10 rounded-xl border border-orange-100 dark:border-orange-900/20">
                 <label class="block text-[10px] font-black text-orange-600 dark:text-orange-400 uppercase tracking-widest mb-1.5">Low Stock Quantity Warning</label>
                 <input v-model="form.low_stock_warning_qty" type="number" class="w-full px-4 py-2 bg-white dark:bg-slate-900 border border-orange-200 dark:border-orange-800 rounded-lg focus:ring-2 focus:ring-orange-500/20 outline-none text-sm font-bold" />
              </div>
           </div>
        </div>

        <!-- Product Discount -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 p-6 transition-colors">
           <h3 class="font-medium text-gray-900 dark:text-white mb-4">Product Discount</h3>
           <div class="space-y-4">
              <input v-model="form.discount_value" type="number" placeholder="Discount" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all bg-white dark:bg-slate-900 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-slate-500" />
              <select v-model="form.discount_type" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all bg-gray-50/50 dark:bg-slate-800/50 text-gray-900 dark:text-white">
                 <option value="" disabled>Select Type</option>
                 <option value="flat">Fixed</option>
                 <option value="percent">Percent (%)</option>
              </select>
           </div>
        </div>

        <!-- Note -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 p-6 transition-colors">
           <h3 class="font-medium text-gray-900 dark:text-white mb-4">Note</h3>
           <textarea v-model="form.note" rows="3" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all bg-white dark:bg-slate-900 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-slate-500 text-sm"></textarea>
        </div>

        <!-- Product Status -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 p-6 transition-colors">
           <h3 class="font-medium text-gray-900 dark:text-white mb-4">Product Status</h3>
           <select v-model="form.status" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all bg-gray-50/50 dark:bg-slate-800/50 text-gray-900 dark:text-white">
              <option value="published">Published</option>
              <option value="draft">Draft</option>
              <option value="pending">Pending</option>
           </select>
        </div>

        <!-- Product Video -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 p-6 transition-colors">
           <h3 class="font-medium text-gray-900 dark:text-white mb-4">Product Video</h3>
           <div class="relative">
              <input v-model="form.video_url" type="text" placeholder="Paste YouTube / Vimeo link here" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all pr-10 bg-white dark:bg-slate-900 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-slate-500" />
              <div class="absolute right-3 top-2.5 text-gray-400 dark:text-slate-500 cursor-pointer">
                 <PlayCircle class="w-5 h-5" />
              </div>
           </div>
        </div>

        <!-- Actions -->
        <div class="grid grid-cols-2 gap-4">
           <button type="button" @click="$router.push('/vendor/products')" class="px-4 py-2.5 border border-gray-300 dark:border-slate-700 rounded-lg text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 font-medium text-center shadow-sm transition-colors">Cancel</button>
           <button type="submit" class="px-4 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium shadow-md shadow-primary-500/20 transition-all flex items-center justify-center">Update Product</button>
        </div>

      </div>
    </form>

    <!-- Media Library Component -->
    <AppMediaLibrary 
      :show="showMediaModal"
      :multiple="mediaModalMode === 'gallery'"
      :type-label="mediaModalMode === 'gallery' ? 'Gallery' : 'Selection'"
      @close="showMediaModal = false"
      @select="handleMediaSelection"
    />
  </div>
</template>

<script setup>
import { 
  Plus, 
  Minus, 
  ChevronDown, 
  UploadCloud, 
  HelpCircle, 
  Box, 
  PlayCircle,
  X,
  ChevronLeft, 
  Search, 
  Trash2, 
  RefreshCw,
  Image,
  Pencil,
  ExternalLink,
  ListChecks,
  GripVertical,
  Library
} from 'lucide-vue-next'
import AppRichEditor from '~/components/AppRichEditor.vue'
import AppSearchSelect from '~/components/AppSearchSelect.vue'

definePageMeta({
  middleware: 'auth',
  permissions: 'products.edit',
  validate: async (route) => {
    // Check if id is numeric OR one of the allowed statuses
    return /^\d+$/.test(route.params.id) || ['published', 'draft', 'pending'].includes(route.params.id)
  }
})

const route = useRoute()
const config = useRuntimeConfig()
const auth = useAuthStore()
const { getAll, createItem, getById, deleteItem, updateItem } = useCrud()
import { toast } from 'vue-sonner';
// Determine if we are in List View (Status) or Edit View (ID)
const isListView = computed(() => ['published', 'draft', 'pending'].includes(route.params.id))

// ==========================================
// LIST VIEW LOGIC
// ==========================================
const products = ref([])
const listCategories = ref([])
const searchQuery = ref('')
const selectedCategory = ref('')
// Get status from route or default to null
const activeStatus = computed(() => isListView.value ? route.params.id : null)
const selectedItems = ref([])

// Variant State
const productAttributesList = ref([])
const selectedAttributesConfig = ref([
    { attribute_id: '', value_ids: [] }
])
const generatedVariants = ref([])

// Media Modal State
const showMediaModal = ref(false)
const mediaModalMode = ref('thumbnail') // 'thumbnail', 'gallery', 'variant'
const targetVariantIndex = ref(null)

const openMediaLibrary = (mode, index = null) => {
    mediaModalMode.value = mode
    targetVariantIndex.value = index
    showMediaModal.value = true
}

const handleMediaSelection = (selection) => {
    // Helper: normalise a single file object's URL
    const getUrl = (file) => file.file_url || file.preview || file.path || ''

    if (mediaModalMode.value === 'thumbnail') {
        // Single selection for thumbnail
        const file = Array.isArray(selection) ? selection[0] : selection
        if (file) {
            form.value.image = getUrl(file)
            thumbnailPreview.value = getUrl(file)
        }
    } else if (mediaModalMode.value === 'gallery') {
        // Multiple selections for gallery
        const selections = Array.isArray(selection) ? selection : [selection]
        selections.forEach(file => {
            const url = getUrl(file)
            if (url && !galleryItems.value.find(item => item.value === url)) {
                galleryItems.value.push({
                    id: file.id,
                    source: 'existing',
                    value: url,
                    preview: url
                })
            }
        })
    } else if (mediaModalMode.value === 'variant' && targetVariantIndex.value !== null) {
        // Single selection for variant image
        const file = Array.isArray(selection) ? selection[0] : selection
        const variant = generatedVariants.value[targetVariantIndex.value]
        if (variant && file) {
            variant.imagePath = getUrl(file)
            variant.imagePreview = getUrl(file)
        }
    }
}

const removeVariantImage = (vIdx) => {
    const variant = generatedVariants.value[vIdx]
    if (variant) {
        variant.imagePath = null
        variant.imagePreview = null
        variant.imageFile = null
    }
}

const generateRandomSku = () => {
    const prefix = form.value.name ? form.value.name.substring(0, 3).toUpperCase() : 'PROD'
    const random = Math.random().toString(36).substring(2, 7).toUpperCase()
    const datePart = new Date().toISOString().slice(2, 4) + new Date().toISOString().slice(5, 7)
    form.value.sku = `${prefix}-${datePart}-${random}`
}

const addAttributeLine = () => {
    selectedAttributesConfig.value.push({
        attribute_id: '',
        value_ids: []
    })
}

const removeAttributeLine = (index) => {
    selectedAttributesConfig.value.splice(index, 1)
    generateCombinations()
}

const generateCombinations = () => {
    // Get valid sets of values
    const validGroupings = selectedAttributesConfig.value
        .filter(conf => conf.attribute_id && conf.value_ids.length > 0)
        .map(conf => {
            const attrObj = productAttributesList.value.find(a => a.id === conf.attribute_id);
            return conf.value_ids.map(valId => {
                const valObj = attrObj.values.find(v => v.id === valId);
                return {
                    id: valId,
                    value: valObj ? valObj.value : '',
                    meta: valObj ? valObj.meta : '',
                    attribute_name: attrObj.name,
                    attribute_id: attrObj.id
                };
            });
        });

    if (validGroupings.length === 0) {
        generatedVariants.value = [];
        return;
    }

    const combinations = validGroupings.reduce((acc, current) => {
        const temp = [];
        acc.forEach(a => {
            current.forEach(c => {
                temp.push([...a, c]);
            });
        });
        return temp;
    }, [[]]);

    // Map to variants keeping old data if possible
    generatedVariants.value = combinations.map(combo => {
        const comboKey = combo.map(c => c.id).sort().join('-');
        
        // Match by comboKey (attributes)
        const existing = generatedVariants.value.find(v => v._key === comboKey);
        
        return {
            _key: comboKey,
            id: existing ? existing.id : null,
            attributes: combo,
            sku: existing ? existing.sku : form.value.sku ? `${form.value.sku}-${combo.map(c => c.value).join('-')}` : null,
            price: existing ? existing.price : form.value.sale_price,
            stock_qty: existing ? existing.stock_qty : form.value.stock_qty || 0,
            imagePath: existing ? existing.imagePath : null,
            imagePreview: existing ? existing.imagePreview : null,
            is_active: existing ? existing.is_active : true
        };
    });
}

// Watch config structure to regenerate smartly
watch(() => selectedAttributesConfig.value, () => generateCombinations(), { deep: true })

const tabs = [
  { label: 'All Products', value: 'all' },
  { label: 'Published', value: 'published' },
  { label: 'Draft', value: 'draft' },
  { label: 'Pending', value: 'pending' }
]

const isAllSelected = computed(() => {
  return products.value.length > 0 && selectedItems.value.length === products.value.length
})

const toggleAll = () => {
  if (isAllSelected.value) {
    selectedItems.value = []
  } else {
    selectedItems.value = products.value.map(p => p.id)
  }
}

const clearFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = ''
  fetchProducts()
}

const fetchProducts = async () => {
  if (!isListView.value) return // Don't fetch products if in edit mode

  const queryParams = new URLSearchParams()
  if (searchQuery.value) queryParams.append('search', searchQuery.value)
  if (selectedCategory.value) queryParams.append('category_id', selectedCategory.value)
  if (activeStatus.value) queryParams.append('status', activeStatus.value)
  
  const [productsRes] = await Promise.all([
    getAll(`/vendor/products?${queryParams.toString()}`),
  ])

  if (productsRes) {
    products.value = productsRes
  }
}

const fetchListCategories = async () => {
  if (!isListView.value) return

  const res = await getAll('/vendor/attributes/categories')
  if (res) {
    listCategories.value = res
  }
}

const deleteProduct = async (id) => {
  if (!confirm('Are you sure you want to delete this product?')) return

  try {
    await deleteItem(id, '/vendor/products')
    await fetchProducts()
  } catch (error) {
    console.error('Failed to delete product', error)
  }
}

const bulkDelete = async () => {
  if (!confirm(`Are you sure you want to delete ${selectedItems.value.length} selected products?`)) return
  alert('Bulk delete functionality to be implemented with API support.')
}

// ==========================================
// EDIT VIEW LOGIC
// ==========================================

// Accordion State
const sections = reactive({
  images: true,
  seo: false,
  faq: false,
  specifications: false,
  dropshipping: false
})

const toggleAccordion = (key) => {
  sections[key] = !sections[key]
}

// Form Data
const form = ref({
  name: '',
  slug: '',
  short_description: '',
  sku: '',
  product_code: '',
  category_ids: [],
  brand_id: '',
  unit_id: '',
  purchase_price: '',
  sale_price: '',
  discount_price: '',
  stock_qty: '',
  weight: '',
  has_variants: false,
  is_featured: false,
  is_special: false,
  is_trending: false,
  is_buy_one_get_one: false,
  is_preorder: false,
  is_dropshipping: false,
  dropshipping_url: '',
  dropshipping_sku: '',
  discount_value: '',
  discount_type: '',
  note: '',
  status: 'published',
  video_url: '',
  description: '',
  seo_title: '',
  seo_description: '',
  seo_keywords: '',
  faqs: [],
  key_features: [],
  specifications: [],
  is_active: false,
  stock_visibility_state: 'quantity',
  low_stock_warning_qty: 1
})

// File state
const thumbnailFile = ref(null)

// Local state for image previews and FAQ UI
const thumbnailPreview = ref(null)
const galleryItems = ref([]) // array of { id, source, value/file, preview }
const localFaqs = ref([])
const localSpecificationGroups = ref([])
const isLoading = ref(true)

// Lookups for Edit Form
const formCategories = ref([])
const brands = ref([])
const units = ref([])

const getCategoryPath = (category, allCategories) => {
  if (!category.parent_id) return category.name
  const parent = allCategories.find(c => c.id === category.parent_id)
  if (!parent) return category.name
  return `${getCategoryPath(parent, allCategories)} > ${category.name}`
}

const fetchData = async () => {
    if (isListView.value) return // Don't fetch edit data if in list mode

    try {
        isLoading.value = true
        const [categoriesRes, brandsRes, unitsRes, attributesRes, productRes] = await Promise.all([
             getAll('/vendor/attributes/categories'),
             getAll('/vendor/attributes/brands'),
             getAll('/vendor/attributes/units'),
             getAll('/vendor/product-attributes'),
             getById('/vendor/products', route.params.id)
        ])

        // Options
        if (categoriesRes && categoriesRes.data) {
          const rawCategories = categoriesRes.data
          formCategories.value = rawCategories.map(cat => ({
            ...cat,
            name: getCategoryPath(cat, rawCategories)
          }))
        }
        if (brandsRes && brandsRes.data) brands.value = brandsRes.data
        if (unitsRes && unitsRes.data) units.value = unitsRes.data
        if (attributesRes && attributesRes.data) productAttributesList.value = attributesRes.data
        else if (Array.isArray(attributesRes)) productAttributesList.value = attributesRes

        // Product Hydration
        if (productRes) {
           Object.keys(form.value).forEach(key => {
              if (productRes.hasOwnProperty(key)) {
                 if (key === 'categories') return // Handled by category_ids
                 
                 // Handle booleans
                 if (typeof form.value[key] === 'boolean') {
                    form.value[key] = !!productRes[key]
                 } else {
                    form.value[key] = productRes[key]
                 }
              }
           })

           // Specialized hydration
           form.value.category_ids = productRes.categories ? productRes.categories.map(c => (c.id || c)) : []
           form.value.is_active = !!productRes.is_active

           // FAQs
           if (typeof productRes.faqs === 'string' && productRes.faqs !== 'null') {
              localFaqs.value = JSON.parse(productRes.faqs)
           } else if (Array.isArray(productRes.faqs)) {
              localFaqs.value = productRes.faqs
           }
           
            if (!localFaqs.value.length) {
               localFaqs.value = [{ question: '', answer: '' }]
            }

            // Key Features Logic (Migrate to groups if exists)
            let migratedFeatures = []
            if (typeof productRes.key_features === 'string' && productRes.key_features !== 'null' && productRes.key_features !== '') {
               try {
                  const parsed = JSON.parse(productRes.key_features)
                  migratedFeatures = Array.isArray(parsed) ? parsed.map(item => {
                     return typeof item === 'string' ? { label: '', value: item } : item
                  }) : []
               } catch (e) {}
            } else if (Array.isArray(productRes.key_features)) {
               migratedFeatures = productRes.key_features.map(item => {
                  return typeof item === 'string' ? { label: '', value: item } : item
               })
            }

            // Specifications Logic (Grouped Structure)
            let specGroups = []
            if (typeof productRes.specifications === 'string' && productRes.specifications !== 'null' && productRes.specifications !== '') {
               try {
                  specGroups = JSON.parse(productRes.specifications)
               } catch (e) {}
            } else if (Array.isArray(productRes.specifications)) {
               specGroups = productRes.specifications
            }

            // Migration / Normalization
            if (specGroups.length > 0) {
               // Check if it's old flat format: [{label, value}]
               if (specGroups[0] && !specGroups[0].items) {
                  localSpecificationGroups.value = [{ 
                     title: 'Specifications', 
                     items: specGroups.map(s => ({ label: s.label || '', value: s.value || '' }))
                  }]
               } else {
                  localSpecificationGroups.value = specGroups
               }
            } else {
               localSpecificationGroups.value = []
            }

            // Add migrated key features as a group if they exist
            if (migratedFeatures.length > 0) {
               localSpecificationGroups.value.unshift({
                  title: 'Key Features',
                  items: migratedFeatures
               })
            }
            
            if (!localSpecificationGroups.value.length) {
               localSpecificationGroups.value = [{ title: 'Key Features', items: [{ label: '', value: '' }] }]
            }

            // Previews
            if (productRes.image) thumbnailPreview.value = productRes.image
            
            // Hydrate Variants Configuration
            if (productRes.variants && productRes.variants.length > 0) {
                const usedAttributeIds = new Set();
                productRes.variants.forEach(v => {
                    v.attributes.forEach(a => usedAttributeIds.add(a.attribute.id));
                });

                selectedAttributesConfig.value = Array.from(usedAttributeIds).map(attrId => {
                    const values = new Set();
                    productRes.variants.forEach(v => {
                        v.attributes.forEach(a => {
                            if (a.attribute.id === attrId) values.add(a.id);
                        });
                    });
                    return {
                        attribute_id: attrId,
                        value_ids: Array.from(values)
                    };
                });

                // Hydrate Generated Variants
                generatedVariants.value = productRes.variants.map(v => {
                    const combo = v.attributes.map(a => ({
                        id: a.id,
                        value: a.value,
                        meta: a.meta,
                        attribute_name: a.attribute.name,
                        attribute_id: a.attribute.id
                    }));
                    const comboKey = combo.map(c => c.id).sort().join('-');
                    return {
                        _key: comboKey,
                        id: v.id,
                        attributes: combo,
                        sku: v.sku,
                        price: v.price,
                        stock_qty: v.stock_qty,
                        imagePath: v.image,
                        imagePreview: v.image,
                        is_active: !!v.is_active
                    };
                });
            }

            // Hydrate new fields
            form.value.stock_visibility_state = productRes.stock_visibility_state || 'quantity'
            form.value.low_stock_warning_qty = productRes.low_stock_warning_qty || 1

            if (productRes.gallery) {
              const gallery = Array.isArray(productRes.gallery) ? productRes.gallery : []
              galleryItems.value = gallery.map(url => ({
                 id: Math.random().toString(36).substr(2, 9),
                 source: 'existing',
                 value: url,
                 preview: url
              }))
           }
        }
    } catch (e) {
        console.error('Failed to fetch product data', e)
    } finally {
        isLoading.value = false
    }
}

// Handlers
const generateSlug = () => {
  form.value.slug = form.value.name
    .toLowerCase()
    .replace(/[^\w ]+/g, '')
    .replace(/ +/g, '-')
}

const handleThumbnailUpload = (event) => {
   const file = event.target.files[0]
   if (file) {
      thumbnailFile.value = file
      thumbnailPreview.value = URL.createObjectURL(file)
   }
}

const handleGalleryUpload = (event) => {
   // Legacy - handled by Media Library
}

const removeGalleryImage = (index) => {
   galleryItems.value.splice(index, 1)
}

// Drag and Drop Handlers
const dragIndex = ref(null)
const onDragStart = (index) => {
   dragIndex.value = index
}
const onDragOver = (event, index) => {
   // preventDefault is enough
}
const onDrop = (index) => {
   const item = galleryItems.value.splice(dragIndex.value, 1)[0]
   galleryItems.value.splice(index, 0, item)
   dragIndex.value = null
}

const addFaq = () => {
   localFaqs.value.push({ question: '', answer: '' })
}

const removeFaq = (index) => {
   localFaqs.value.splice(index, 1)
}

// Specification Group Handlers
const addSpecGroup = () => {
   localSpecificationGroups.value.push({ 
      title: '', 
      items: [{ label: '', value: '' }] 
   })
}

const removeSpecGroup = (index) => {
   localSpecificationGroups.value.splice(index, 1)
}

const addSpecItem = (groupIndex) => {
   localSpecificationGroups.value[groupIndex].items.push({ label: '', value: '' })
}

const removeSpecItem = (groupIndex, itemIndex) => {
   if (localSpecificationGroups.value[groupIndex].items.length > 1) {
      localSpecificationGroups.value[groupIndex].items.splice(itemIndex, 1)
   }
}

// Hierarchical Category Logic
const selectParents = (categoryId) => {
  const category = formCategories.value.find(c => c.id === categoryId)
  if (category && category.parent_id) {
    if (!form.value.category_ids.includes(category.parent_id)) {
      form.value.category_ids.push(category.parent_id)
      selectParents(category.parent_id)
    }
  }
}

const deselectChildren = (parentId) => {
  const children = formCategories.value.filter(c => c.parent_id === parentId)
  children.forEach(child => {
    const index = form.value.category_ids.indexOf(child.id)
    if (index > -1) {
      form.value.category_ids.splice(index, 1)
      deselectChildren(child.id)
    }
  })
}

watch(() => [...form.value.category_ids], (newVal, oldVal) => {
  if (newVal.length > oldVal.length) {
    const addedId = newVal.find(id => !oldVal.includes(id))
    if (addedId) selectParents(addedId)
  }
  if (newVal.length < oldVal.length) {
    const removedId = oldVal.find(id => !newVal.includes(id))
    if (removedId) deselectChildren(removedId)
  }
}, { deep: true })

const attributeToggles = {
  is_featured: 'Featured Product',
  is_special: 'Special Product',
  is_trending: 'Trending Product',
  is_buy_one_get_one: 'Buy 1 Get 1',
  is_preorder: 'PreOrder Product',
  is_active: 'Status (Active/Inactive)'
}

// Creation Handlers (Brands, Units, Categories)
const handleCategoryCreate = async (name) => {
  try {
    const formData = new FormData()
    formData.append('name', name)
    formData.append('is_active', '1')
    const newCategory = await createItem('/vendor/attributes/categories', formData, null, false)
    if (newCategory) {
      const resp = await getAll('/vendor/attributes/categories')
      if (resp?.data) formCategories.value = resp.data.map(cat => ({
         ...cat,
         name: getCategoryPath(cat, resp.data)
      }))
      const id = newCategory.id || newCategory.data?.id
      if (id && !form.value.category_ids.includes(id)) form.value.category_ids.push(id)
    }
  } catch (e) {}
}

const handleBrandCreate = async (name) => {
   try {
     const formData = new FormData()
     formData.append('name', name)
     formData.append('is_active', '1')
     const res = await createItem('/vendor/attributes/brands', formData, null, false)
     if (res) {
       const resp = await getAll('/vendor/attributes/brands')
       if (resp?.data) brands.value = resp.data
       form.value.brand_id = res.id || res.data?.id
     }
   } catch (e) {}
}

const handleUnitCreate = async (name) => {
   try {
     const formData = new FormData()
     formData.append('name', name)
     formData.append('is_active', '1')
     const res = await createItem('/vendor/attributes/units', formData, null, false)
     if (res) {
       const resp = await getAll('/vendor/attributes/units')
       if (resp?.data) units.value = resp.data
       form.value.unit_id = res.id || res.data?.id
     }
   } catch (e) {}
}

const updateProduct = async () => {
  if (!form.value.name || !form.value.category_ids?.length || !form.value.sale_price) {
      toast.error('Please fill in required fields!')
      return
  }

  const formData = new FormData()
  
  // Append basic fields
  Object.keys(form.value).forEach(key => {
      if (key === 'faqs' || key === 'category_ids' || key === 'specifications' || key === 'key_features') return
      let value = form.value[key]
      
      // Handle SKU nullability
      if (key === 'sku' && !value) value = null
      
      if (typeof value === 'boolean') value = value ? 1 : 0
      if (value !== null && value !== undefined) formData.append(key, value)
  })

  // Append Categories
  form.value.category_ids.forEach(id => formData.append('category_ids[]', id))

  // Append Files
  if (thumbnailFile.value) formData.append('image', thumbnailFile.value)
  
  // Handle Gallery Uploads and Ordering
  const galleryMetadata = []
  let uploadIndex = 0
  galleryItems.value.forEach((item) => {
      if (item.source === 'existing') {
          galleryMetadata.push({ source: 'existing', value: item.value })
      } else {
          formData.append(`gallery[${uploadIndex}]`, item.file)
          galleryMetadata.push({ source: 'upload', index: uploadIndex })
          uploadIndex++
      }
  })
  formData.append('gallery_items', JSON.stringify(galleryMetadata))

  // FAQs
  const validFaqs = localFaqs.value.filter(f => f.question && f.answer)
  formData.append('faqs', JSON.stringify(validFaqs))

  // Specifications (Nested Grouped Structure)
  const validGroups = localSpecificationGroups.value
     .map(group => ({
        ...group,
        items: group.items.filter(i => i.label || i.value)
     }))
     .filter(group => group.title || group.items.length > 0)
     
  formData.append('specifications', JSON.stringify(validGroups))

  // Clear key_features since we moved them to specifications
  formData.append('key_features', JSON.stringify([]))

  try {
    useUtilityStore.isEdit = true
    
    // Prepare Variants for submission
    const cleanVariants = generatedVariants.value.map((v, index) => {
        // Handle images (same logic as create.vue)
        if (v.imageFile) {
            formData.append(`variants_image_${index}`, v.imageFile)
        }
        return {
            id: v.id, // Include ID for existing variants
            sku: v.sku || null,
            price: v.price,
            stock_qty: v.stock_qty,
            is_active: v.is_active,
            image: v.imagePath || null,
            attributes: v.attributes.map(attr => attr.id)
        }
    })
    formData.append('variants', JSON.stringify(cleanVariants))

    await updateItem(`/vendor/products/${route.params.id}`, formData)
    useUtilityStore.isEdit = false
    navigateTo('/vendor/products')
  } catch (error) {
     console.error('Error updating product:', error)
  }
}

// Watch for route changes to update view and fetch data
watch(() => route.params.id, (newId) => {
  if (['published', 'draft', 'pending'].includes(newId)) {
    // Switched to list view
    fetchProducts()
    fetchListCategories()
  } else if (/^\d+$/.test(newId)) {
    // Switched to edit view
    fetchData()
  }
}, { immediate: true })

// onMounted is handled by immediate watch, but to be safe/explicit if needed:
// onMounted(() => {
//   if (isListView.value) {
//     fetchProducts()
//     fetchListCategories()
//   } else {
//     fetchData()
//   }
// })
</script>

<style scoped>
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
  border-radius: 10px;
}

.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 #f1f5f9;
}
</style>
