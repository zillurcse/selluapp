<template>
  <div class="max-w-[1600px] mx-auto p-10 bg-[#f8fafc] dark:bg-slate-950 transition-colors duration-300">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Add Product</h1>
      <NuxtLink to="/vendor/products" class="px-4 py-2 bg-white dark:bg-slate-900 border border-gray-300 dark:border-slate-700 text-gray-700 dark:text-slate-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-800 text-sm font-medium transition-colors shadow-sm">
        Back to List
      </NuxtLink>
    </div>

    <form @submit.prevent="submitProduct" class="grid grid-cols-12 gap-8">
      <!-- Left Column (Main Content) -->
      <div class="col-span-12 lg:col-span-9 space-y-8">
        
        <!-- Basic Info -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 p-8 transition-colors">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="col-span-1 md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Product Name <span class="text-red-500">*</span></label>
                <input v-model="form.name" type="text" placeholder="e.g. Premium Cotton T-Shirt" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-slate-500 bg-white dark:bg-slate-900 text-gray-900 dark:text-white" required @input="generateSlug" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Product Slug <span class="text-red-500">*</span></label>
              <div class="relative">
                  <span class="absolute left-4 top-2.5 text-gray-400 dark:text-slate-500 text-sm">/products/</span>
                  <input v-model="form.slug" type="text" class="w-full pl-24 pr-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all bg-gray-50/50 dark:bg-slate-800/50 text-gray-600 dark:text-slate-300" required />
              </div>
            </div>
            <div class="col-span-1 md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Short Description</label>
              <textarea v-model="form.short_description" rows="3" placeholder="Brief summary of the product..." class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-slate-500 bg-white dark:bg-slate-900 text-gray-900 dark:text-white"></textarea>
            </div>
          </div>
        </div>

        <!-- Identifiers -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 p-8 transition-colors">
          <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Inventory Codes</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">SKU <span class="text-red-500">*</span></label>
              <input v-model="form.sku" type="text" placeholder="Ex: UORLA-14464" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-slate-500 bg-white dark:bg-slate-900 text-gray-900 dark:text-white" required />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Product Code <span class="text-red-500">*</span></label>
              <input v-model="form.product_code" type="text" placeholder="Ex: PRD-260219010119" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-slate-500 bg-white dark:bg-slate-900 text-gray-900 dark:text-white" required />
            </div>
          </div>
        </div>

        <!-- Relations -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 p-8 transition-colors">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Organization</h2>
           <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
             <div>
                <AppSearchSelect
                  v-model="form.category_ids"
                  :items="categories"
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
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 p-8 transition-colors">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-6">Pricing & Inventory</h2>
           <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Purchase Price</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-500 dark:text-slate-400">৳</span>
                    <input v-model="form.purchase_price" type="number" step="0.01" placeholder="0.00" class="w-full pl-8 pr-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-slate-500 bg-white dark:bg-slate-900 text-gray-900 dark:text-white" />
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Sale Price <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-500 dark:text-slate-400">৳</span>
                    <input v-model="form.sale_price" type="number" step="0.01" placeholder="0.00" class="w-full pl-8 pr-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-slate-500 bg-white dark:bg-slate-900 text-gray-900 dark:text-white" required />
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Discount Price</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-500 dark:text-slate-400">৳</span>
                    <input v-model="form.discount_price" type="number" step="0.01" placeholder="0.00" class="w-full pl-8 pr-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-slate-500 bg-white dark:bg-slate-900 text-gray-900 dark:text-white" />
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Stock Qty <span class="text-red-500">*</span></label>
                <input v-model="form.stock_qty" type="number" placeholder="0" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-slate-500 bg-white dark:bg-slate-900 text-gray-900 dark:text-white" required />
              </div>
           </div>
           
           <div class="mt-6 flex items-center p-4 bg-gray-50 dark:bg-slate-800/50 rounded-lg border border-gray-100 dark:border-slate-700 transition-colors">
             <label class="relative inline-flex items-center cursor-pointer">
               <input type="checkbox" v-model="form.has_variants" class="sr-only peer">
               <div class="w-11 h-6 bg-gray-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 dark:after:border-slate-600 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600 transition-colors"></div>
               <span class="ml-3 text-sm font-medium text-gray-700 dark:text-slate-300">Enable Product Variants</span>
             </label>
           </div>
        </div>

        <!-- Accordions -->
        <div class="space-y-4">
           <!-- Product Images -->
           <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 overflow-hidden transition-colors">
               <button type="button" @click="toggleAccordion('images')" class="w-full px-8 py-5 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-slate-800/50 bg-white dark:bg-slate-900 transition-colors">
                  <div class="flex items-center font-medium text-gray-900 dark:text-white group">
                     <span class="mr-3 p-1 rounded-md bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400 group-hover:bg-primary-50 dark:group-hover:bg-primary-900/30 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                        <Plus v-if="!sections.images" class="w-4 h-4" />
                        <Minus v-else class="w-4 h-4" />
                     </span>
                     Product Images
                  </div>
                  <ChevronDown class="w-5 h-5 text-gray-400 dark:text-slate-500 transition-transform duration-300" :class="{'rotate-180': sections.images}" />
               </button>
               <div v-show="sections.images" class="p-8 border-t border-gray-100 dark:border-slate-800">
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                     <!-- Thumbnail -->
                     <div class="col-span-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Thumbnail (Max 2MB | 400x400)</label>
                        <div class="border-2 border-dashed border-gray-200 dark:border-slate-700 hover:border-primary-400 dark:hover:border-primary-500 bg-gray-50/50 dark:bg-slate-800/50 hover:bg-primary-50/10 dark:hover:bg-primary-900/10 rounded-xl h-64 flex flex-col items-center justify-center transition-all cursor-pointer relative overflow-hidden group">
                           <input type="file" @change="handleThumbnailUpload" class="absolute inset-0 opacity-0 cursor-pointer z-10" accept="image/*" />
                           <div v-if="thumbnailPreview" class="absolute inset-0 w-full h-full p-2">
                              <img :src="thumbnailPreview" class="w-full h-full object-cover rounded-lg shadow-sm" />
                              <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-lg m-2">
                                <span class="text-white text-sm font-medium">Change Image</span>
                              </div>
                           </div>
                           <div v-else class="text-center p-6">
                              <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-3 group-hover:bg-primary-100 dark:group-hover:bg-primary-900/50 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                <UploadCloud class="w-6 h-6 text-gray-400 dark:text-slate-500 group-hover:text-primary-500 dark:group-hover:text-primary-400" />
                              </div>
                              <span class="text-sm font-medium text-gray-600 dark:text-slate-400 block mb-1">Click to Upload</span>
                              <span class="text-xs text-gray-400 dark:text-slate-500">SVG, PNG, JPG or GIF</span>
                           </div>
                        </div>
                     </div>
                     <!-- Gallery -->
                     <div class="col-span-1 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Gallery Images (Max 2MB per image)</label>
                         <div class="border-2 border-dashed border-gray-200 dark:border-slate-700 hover:border-primary-400 dark:hover:border-primary-500 bg-gray-50/50 dark:bg-slate-800/50 hover:bg-primary-50/10 dark:hover:bg-primary-900/10 rounded-xl h-64 flex flex-col items-center justify-center transition-all cursor-pointer relative overflow-hidden group">
                           <input type="file" @change="handleGalleryUpload" multiple class="absolute inset-0 opacity-0 cursor-pointer z-10" accept="image/*" />
                           <div class="text-center p-6">
                              <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-3 group-hover:bg-primary-100 dark:group-hover:bg-primary-900/50 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                <UploadCloud class="w-6 h-6 text-gray-400 dark:text-slate-500 group-hover:text-primary-500 dark:group-hover:text-primary-400" />
                              </div>
                              <span class="text-sm font-medium text-gray-600 dark:text-slate-400 block mb-1">Click here to select images</span>
                              <span class="text-xs text-gray-400 dark:text-slate-500">Max 2MB per image, Limit: 10 images</span>
                           </div>
                        </div>
                        <div v-if="galleryPreviews.length > 0" class="mt-6">
                            <h4 class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-3">Selected Images</h4>
                            <div class="flex flex-wrap gap-4">
                               <div v-for="(img, idx) in galleryPreviews" :key="idx" class="w-24 h-24 border border-gray-200 dark:border-slate-700 rounded-lg overflow-hidden relative group shadow-sm bg-white dark:bg-slate-800">
                                  <img :src="img" class="w-full h-full object-cover" />
                                  <button @click="removeGalleryImage(idx)" type="button" class="absolute top-1 right-1 bg-white/90 dark:bg-slate-900/90 text-red-500 dark:text-red-400 rounded-full p-1 opacity-0 group-hover:opacity-100 transition-all hover:bg-red-50 dark:hover:bg-red-900/50 shadow-sm">
                                     <X class="w-3.5 h-3.5" />
                                  </button>
                               </div>
                            </div>
                        </div>
                     </div>
                  </div>
               </div>
           </div>

           <!-- Product Description & SEO -->
           <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 overflow-hidden transition-colors">
               <button type="button" @click="toggleAccordion('seo')" class="w-full px-8 py-5 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-slate-800/50 bg-white dark:bg-slate-900 transition-colors">
                  <div class="flex items-center font-medium text-gray-900 dark:text-white group">
                     <span class="mr-3 p-1 rounded-md bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400 group-hover:bg-primary-50 dark:group-hover:bg-primary-900/30 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                        <Plus v-if="!sections.seo" class="w-4 h-4" />
                        <Minus v-else class="w-4 h-4" />
                     </span>
                     Product Description & SEO Settings
                  </div>
                  <ChevronDown class="w-5 h-5 text-gray-400 dark:text-slate-500 transition-transform duration-300" :class="{'rotate-180': sections.seo}" />
               </button>
               <div v-show="sections.seo" class="p-8 border-t border-gray-100 dark:border-slate-800 space-y-8">
                  <!-- Description with Toolbar Dummy -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Product Description <span class="text-red-500">*</span></label>
                     <div class="prose dark:prose-invert max-w-none">
                        <AppRichEditor v-model="form.description" placeholder="Enter detailed product description..." />
                     </div>
                  </div>

                  <!-- SEO Fields -->
                  <div class="space-y-6 pt-6 border-t border-gray-100 dark:border-slate-800">
                      <h3 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wider">Search Engine Optimization</h3>
                     <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Meta Title (optional)</label>
                        <input v-model="form.seo_title" type="text" placeholder="Best Product Name | Brand" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-slate-500 bg-white dark:bg-slate-900 text-gray-900 dark:text-white" />
                        <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">Recommended length: 50-60 characters</p>
                     </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Meta Description (optional)</label>
                        <textarea v-model="form.seo_description" rows="3" placeholder="Short description for SEO (max 160 characters)" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-slate-500 bg-white dark:bg-slate-900 text-gray-900 dark:text-white"></textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-slate-400">Recommended length: 150-160 characters</p>
                     </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Meta Keywords (optional)</label>
                        <input v-model="form.seo_keywords" type="text" placeholder="keyword1, keyword2, keyword3" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-slate-500 bg-white dark:bg-slate-900 text-gray-900 dark:text-white" />
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
        
        <!-- Actions -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 p-6 sticky top-6 z-20 transition-colors">
           <h3 class="font-medium text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-100 dark:border-slate-800">Publish</h3>
           <div class="space-y-4">
              <div class="grid grid-cols-2 gap-3">
                 <button type="button" class="col-span-1 px-4 py-2 border border-gray-300 dark:border-slate-700 rounded-lg text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 font-medium text-sm transition-colors shadow-sm">Save Draft</button>
                 <button type="button" class="col-span-1 px-4 py-2 border border-gray-300 dark:border-slate-700 rounded-lg text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 font-medium text-sm transition-colors shadow-sm">Preview</button>
              </div>
              <button type="submit" class="w-full px-4 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 font-medium shadow-md shadow-primary-500/20 transition-all flex items-center justify-center">
                 Save Changes
              </button>
           </div>
        </div>

        <!-- Product Status -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 p-6 transition-colors">
           <h3 class="font-medium text-gray-900 dark:text-white mb-4">Status & Visibility</h3>
           <select v-model="form.status" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all bg-gray-50/50 dark:bg-slate-800/50 text-gray-900 dark:text-white">
              <option value="published">Published</option>
              <option value="draft">Draft</option>
              <option value="pending">Pending</option>
           </select>
        </div>

        <!-- Product Attributes -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 p-6 transition-colors">
           <h3 class="font-medium text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-100 dark:border-slate-800">Product Attributes</h3>
           <div class="space-y-3">
              <div v-for="(label, key) in attributeToggles" :key="key" class="flex items-center justify-between py-1">
                 <span class="text-sm text-gray-600 dark:text-slate-300 hover:text-gray-900 dark:hover:text-white transition-colors">{{ label }}</span>
                 <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" v-model="form[key]" class="sr-only peer">
                    <div class="w-9 h-5 bg-gray-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 dark:after:border-slate-600 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary-600"></div>
                 </label>
              </div>
           </div>
        </div>

        <!-- Product Discount -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 p-6 transition-colors">
           <h3 class="font-medium text-gray-900 dark:text-white mb-4">Product Discount</h3>
           <div class="space-y-4">
              <div class="relative">
                 <span class="absolute left-3 top-2.5 text-gray-500 dark:text-slate-400 text-sm font-semibold">%</span>
                 <input v-model="form.discount_value" type="number" placeholder="Discount Value" class="w-full pl-8 pr-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-slate-500 bg-white dark:bg-slate-900 text-gray-900 dark:text-white" />
              </div>
              <select v-model="form.discount_type" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all bg-gray-50/50 dark:bg-slate-800/50 text-gray-900 dark:text-white">
                 <option value="" disabled>Select Type</option>
                 <option value="percent">Percent (%)</option>
                 <option value="fixed">Fixed Amount</option>
              </select>
           </div>
        </div>

        <!-- Note -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 p-6 transition-colors">
           <h3 class="font-medium text-gray-900 dark:text-white mb-4">Internal Note</h3>
           <textarea v-model="form.note" rows="3" placeholder="Add a private note..." class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-slate-500 bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-sm"></textarea>
        </div>


        <!-- Product Video -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm ring-1 ring-gray-200/50 dark:ring-slate-800 p-6 transition-colors">
           <h3 class="font-medium text-gray-900 dark:text-white mb-4">Product Video</h3>
           <div class="relative">
              <input v-model="form.video_url" type="text" placeholder="YouTube / Vimeo link" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all pr-10 placeholder:text-gray-400 dark:placeholder:text-slate-500 bg-white dark:bg-slate-900 text-gray-900 dark:text-white" />
              <div class="absolute right-3 top-2.5 text-gray-400 dark:text-slate-500">
                 <PlayCircle class="w-5 h-5" />
              </div>
           </div>
        </div>

      </div>
    </form>
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
  X
} from 'lucide-vue-next'
import AppRichEditor from '~/components/AppRichEditor.vue'
import AppSearchSelect from '~/components/AppSearchSelect.vue'

const config = useRuntimeConfig()
const auth = useAuthStore()
const { getAll, createItem } = useCrud()

// Accordion State
const sections = reactive({
  images: true,
  seo: false,
  faq: false,
  dropshipping: false
})

const toggleAccordion = (key) => {
  sections[key] = !sections[key]
}

// Form Data - Using refs for files, reactive for others
const form = ref({
  name: '',
  slug: '',
  short_description: '',
  sku: '',
  product_code: 'PRD-' + Date.now().toString().slice(-8), 
  category_ids: [],
  brand_id: '',
  unit_id: '',
  purchase_price: '',
  sale_price: '',
  discount_price: '',
  stock_qty: '',
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
  faqs: []
})

// File state
const thumbnailFile = ref(null)
const galleryFiles = ref([])

// Local state for image previews and FAQ UI
const thumbnailPreview = ref(null)
const galleryPreviews = ref([])
const localFaqs = ref([
   { question: '', answer: '' }
])

// Image Handlers
const handleThumbnailUpload = (event) => {
   const file = event.target.files[0]
   if (file) {
      thumbnailFile.value = file
      thumbnailPreview.value = URL.createObjectURL(file)
   }
}

const handleGalleryUpload = (event) => {
   const files = Array.from(event.target.files)
   const newFiles = files.slice(0, 10 - galleryFiles.value.length) 
   
   newFiles.forEach(file => {
      galleryFiles.value.push(file)
      galleryPreviews.value.push(URL.createObjectURL(file))
   })
}

const removeGalleryImage = (index) => {
   galleryFiles.value.splice(index, 1)
   galleryPreviews.value.splice(index, 1)
}

// FAQ Handlers
const addFaq = () => {
   localFaqs.value.push({ question: '', answer: '' })
}

const removeFaq = (index) => {
   localFaqs.value.splice(index, 1)
}

const attributeToggles = {
  is_featured: 'Featured Product',
  is_special: 'Special Product',
  is_trending: 'Trending Product',
  is_buy_one_get_one: 'Buy 1 Get 1',
  is_preorder: 'PreOrder Product'
}

const categories = ref([])
const brands = ref([])
const units = ref([])

// Generate Slug from Name
const generateSlug = () => {
  form.value.slug = form.value.name
    .toLowerCase()
    .replace(/[^\w ]+/g, '')
    .replace(/ +/g, '-')
}

// Generate Breadcrumb Path
const getCategoryPath = (category, allCategories) => {
  if (!category.parent_id) return category.name
  const parent = allCategories.find(c => c.id === category.parent_id)
  if (!parent) return category.name
  return `${getCategoryPath(parent, allCategories)} > ${category.name}`
}

// Fetch Data
const fetchOptions = async () => {
    try {
        const [categoriesRes, brandsRes, unitsRes] = await Promise.all([
             getAll('/vendor/attributes/categories'),
             getAll('/vendor/attributes/brands'),
             getAll('/vendor/attributes/units')
        ])
        if (categoriesRes && categoriesRes.data) {
          // Format categories with breadcrumbs
          const rawCategories = categoriesRes.data
          categories.value = rawCategories.map(cat => ({
            ...cat,
            name: getCategoryPath(cat, rawCategories)
          }))
        }
        if (brandsRes && brandsRes.data) brands.value = brandsRes.data
        if (unitsRes && unitsRes.data) units.value = unitsRes.data
    } catch (e) {
        console.error('Failed to fetch options')
    }
}

fetchOptions()

// Hierarchical Category Selection Logic
const selectParents = (categoryId) => {
  const category = categories.value.find(c => c.id === categoryId)
  if (category && category.parent_id) {
    if (!form.value.category_ids.includes(category.parent_id)) {
      form.value.category_ids.push(category.parent_id)
      selectParents(category.parent_id)
    }
  }
}

const deselectChildren = (parentId) => {
  const children = categories.value.filter(c => c.parent_id === parentId)
  children.forEach(child => {
    const index = form.value.category_ids.indexOf(child.id)
    if (index > -1) {
      form.value.category_ids.splice(index, 1)
      deselectChildren(child.id)
    }
  })
}

watch(() => [...form.value.category_ids], (newVal, oldVal) => {
  // Check if a new category was added
  if (newVal.length > oldVal.length) {
    const addedId = newVal.find(id => !oldVal.includes(id))
    if (addedId) {
      selectParents(addedId)
    }
  }
  // Check if a category was removed
  if (newVal.length < oldVal.length) {
    const removedId = oldVal.find(id => !newVal.includes(id))
    if (removedId) {
      deselectChildren(removedId)
    }
  }
}, { deep: true })

const handleCategoryCreate = async (name) => {
  try {
    const formData = new FormData()
    formData.append('name', name)
    formData.append('is_active', '1')

    const newCategory = await createItem('/vendor/attributes/categories', formData, null, false)

    if (newCategory) {
      // Refresh categories list
      const categoriesRes = await getAll('/vendor/attributes/categories')
      if (categoriesRes && categoriesRes.data) categories.value = categoriesRes.data
      
      // Select the newly created category
      const createdId = newCategory.id || (newCategory.data ? newCategory.data.id : null)
      if (createdId) {
        if (!form.value.category_ids.includes(createdId)) {
          form.value.category_ids.push(createdId)
        }
      }
    }
  } catch (error) {
    console.error('Failed to create category:', error)
    alert(error.data?.message || 'Failed to create category')
  }
}

const handleBrandCreate = async (name) => {
  try {
    const formData = new FormData()
    formData.append('name', name)
    formData.append('is_active', '1')

    const newBrand = await createItem('/vendor/attributes/brands', formData, null, false)

    if (newBrand) {
      const brandsRes = await getAll('/vendor/attributes/brands')
      if (brandsRes && brandsRes.data) brands.value = brandsRes.data
      const createdId = newBrand.id || (newBrand.data ? newBrand.data.id : null)
      if (createdId) form.value.brand_id = createdId
    }
  } catch (error) {
    console.error('Failed to create brand:', error)
    alert(error.data?.message || 'Failed to create brand')
  }
}

const handleUnitCreate = async (name) => {
  try {
    const formData = new FormData()
    formData.append('name', name)
    formData.append('is_active', '1')

    const newUnit = await createItem('/vendor/attributes/units', formData, null, false)

    if (newUnit) {
      const unitsRes = await getAll('/vendor/attributes/units')
      if (unitsRes && unitsRes.data) units.value = unitsRes.data
      const createdId = newUnit.id || (newUnit.data ? newUnit.data.id : null)
      if (createdId) form.value.unit_id = createdId
    }
  } catch (error) {
    console.error('Failed to create unit:', error)
    alert(error.data?.message || 'Failed to create unit')
  }
}

const submitProduct = async () => {
  // Validate required fields
  if (!form.value.name || !form.value.category_ids?.length || !form.value.sale_price) {
      alert('Please fill in required fields')
      return
  }

  const formData = new FormData()
  
  // Append basic fields
  Object.keys(form.value).forEach(key => {
      if (key === 'faqs' || key === 'category_ids') return // Handle separately
      if (form.value[key] !== null && form.value[key] !== undefined) {
          let value = form.value[key]
          if (typeof value === 'boolean') {
              value = value ? 1 : 0
          }
          formData.append(key, value)
      }
  })

  // Append category_ids correctly as an array for Laravel
  if (form.value.category_ids && Array.isArray(form.value.category_ids)) {
      form.value.category_ids.forEach(id => {
          formData.append('category_ids[]', id)
      })
  }

  // Append variants boolean (converts to string 'true'/'false' or 1/0 usually handled by Laravel)
  // Checkbox bindings in Vue are booleans, FormData sends string "true"/"false"
  // Laravel boolean validation accepts "true", "false", 1, 0, "1", "0"

  // Append Files
  if (thumbnailFile.value) {
      formData.append('image', thumbnailFile.value) // Assuming 'image' is the main image/thumbnail field
  }
  
  galleryFiles.value.forEach((file, index) => {
      formData.append(`gallery[${index}]`, file)
  })

  // Append FAQs
  // localFaqs contains empty ones potentially, filter and stringify
  const validFaqs = localFaqs.value.filter(f => f.question && f.answer)
  formData.append('faqs', JSON.stringify(validFaqs))

  try {
     await createItem('/vendor/products', formData, form)
     navigateTo('/vendor/products')
  } catch (error) {
     console.error('Error creating product:', error)
     // Error message is handled by toast in useCrud
  }
}
</script>
