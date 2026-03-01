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
              <label class="text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5 flex justify-between items-center">
                 <span>SKU <span class="text-red-500">*</span></span>
                 <button @click="generateSKU" type="button" class="text-xs font-bold text-primary-600 dark:text-primary-400 hover:text-primary-700 hover:underline transition-all flex items-center">
                   <RefreshCw class="w-3 h-3 mr-1" /> Generate SKU
                 </button>
              </label>
              <input v-model="form.sku" type="text" placeholder="Ex: UORLA-14464" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-slate-500 bg-white dark:bg-slate-900 text-gray-900 dark:text-white uppercase" required />
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
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Weight (kg)</label>
                <div class="relative">
                    <input v-model="form.weight" type="number" step="0.01" min="0" placeholder="0.00" class="w-full px-4 py-2.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 dark:focus:border-primary-400 outline-none transition-all placeholder:text-gray-400 dark:placeholder:text-slate-500 bg-white dark:bg-slate-900 text-gray-900 dark:text-white pr-10" />
                    <span class="absolute right-3 top-2.5 text-gray-400 dark:text-slate-500 text-xs font-semibold">kg</span>
                </div>
              </div>
           </div>
           
           <div class="mt-6 flex items-center p-4 bg-gray-50 dark:bg-slate-800/50 rounded-lg border border-gray-100 dark:border-slate-700 transition-colors">
             <label class="relative inline-flex items-center cursor-pointer">
               <input type="checkbox" v-model="form.has_variants" class="sr-only peer">
               <div class="w-11 h-6 bg-gray-200 dark:bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 dark:after:border-slate-600 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600 transition-colors"></div>
               <span class="ml-3 text-sm font-medium text-gray-700 dark:text-slate-300">Enable Product Variants</span>
             </label>
           </div>

           <!-- Variant Config UI -->
           <div v-if="form.has_variants" class="mt-6 border-t border-gray-100 dark:border-slate-800 pt-6 animate-in fade-in slide-in-from-top-4 duration-500">
              <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-4">Product Variants Manager</h3>
              
              <div class="space-y-4 mb-6">
                 <div v-for="(config, bgIndex) in selectedAttributesConfig" :key="bgIndex" class="p-4 bg-gray-50/50 dark:bg-slate-800/50 rounded-xl border border-gray-200 dark:border-slate-700 relative">
                    <button @click="removeAttributeLine(bgIndex)" type="button" class="absolute top-4 right-4 text-red-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 p-1.5 rounded-lg transition-all">
                        <X class="w-4 h-4" />
                    </button>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                       <div>
                           <div class="flex items-center justify-between mb-1.5 border-b border-transparent">
                               <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Select Attribute</label>
                               <button @click="showQuickAddAttributeModal = true" type="button" class="text-xs text-primary-600 dark:text-primary-400 font-bold hover:text-primary-700 dark:hover:text-primary-300 transition-colors flex items-center pr-1">
                                    <Plus class="w-3.5 h-3.5 mr-0.5" />
                                    New
                               </button>
                           </div>
                           <select v-model="config.attribute_id" @change="generateCombinations" class="w-full px-4 py-2 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none bg-white dark:bg-slate-900 text-gray-900 dark:text-white">
                               <option value="" disabled>Select Attribute</option>
                               <option v-for="attr in productAttributesList" :key="attr.id" :value="attr.id">{{ attr.name }}</option>
                           </select>
                       </div>
                       <div v-if="config.attribute_id">
                           <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Select Values</label>
                           <div class="flex flex-wrap gap-2">
                               <label v-for="val in productAttributesList.find(a => a.id === config.attribute_id)?.values" :key="val.id" class="inline-flex items-center p-2 rounded-lg border border-gray-100 dark:border-slate-800 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors cursor-pointer ring-1 ring-transparent peer-checked:ring-primary-500">
                                   <input type="checkbox" :value="val.id" v-model="config.value_ids" @change="generateCombinations" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600 dark:ring-offset-slate-800 focus:ring-2 dark:bg-slate-700 dark:border-slate-600 peer">
                                   <div v-if="productAttributesList.find(a => a.id === config.attribute_id)?.type === 'color'" 
                                        class="w-4 h-4 rounded-full ml-2 shadow-sm border border-gray-200" 
                                        :style="{ backgroundColor: val.meta }"></div>
                                   <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ val.value }}</span>
                               </label>
                           </div>
                       </div>
                    </div>
                 </div>
                 <button @click="addAttributeLine" type="button" class="w-full py-3 bg-white dark:bg-slate-900 border border-dashed border-gray-300 dark:border-slate-700 text-gray-600 dark:text-slate-400 font-medium rounded-xl hover:border-primary-500 hover:text-primary-600 transition-all flex items-center justify-center gap-2">
                    <Plus class="w-4 h-4" />
                    Add Another Attribute
                 </button>
              </div>

              <!-- Matrix UI -->
              <div v-if="generatedVariants.length > 0" class="overflow-x-auto rounded-lg border border-gray-200 dark:border-slate-700">
                  <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                      <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-slate-800 dark:text-gray-400">
                          <tr>
                              <th scope="col" class="px-4 py-3 border-b dark:border-slate-700">Variant</th>
                              <th scope="col" class="px-4 py-3 border-b dark:border-slate-700 w-32">Price</th>
                              <th scope="col" class="px-4 py-3 border-b dark:border-slate-700 w-24">Stock</th>
                              <th scope="col" class="px-4 py-3 border-b dark:border-slate-700 w-32">SKU</th>
                              <th scope="col" class="px-4 py-3 border-b dark:border-slate-700 w-24">Image</th>
                              <th scope="col" class="px-4 py-3 border-b dark:border-slate-700 w-16 text-center">Active</th>
                          </tr>
                      </thead>
                      <tbody>
                          <tr v-for="(variant, idx) in generatedVariants" :key="variant._key" class="bg-white border-b dark:bg-slate-900 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">
                              <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                  <div class="flex items-center gap-2 flex-wrap">
                                      <template v-for="(attr, aIdx) in variant.attributes" :key="aIdx">
                                           <div class="flex items-center gap-1.5 bg-gray-50 dark:bg-slate-800 px-2 py-0.5 rounded border border-gray-100 dark:border-slate-700">
                                               <div v-if="productAttributesList.find(pa => pa.id === attr.attribute_id)?.type === 'color'"
                                                    class="w-3 h-3 rounded-full border border-gray-200"
                                                    :style="{ backgroundColor: attr.meta }"></div>
                                               <span class="text-xs">{{ attr.value }}</span>
                                           </div>
                                      </template>
                                  </div>
                              </td>
                              <td class="px-4 py-3">
                                  <input type="number" v-model="variant.price" class="w-full px-2 py-1.5 border border-gray-200 dark:border-slate-600 rounded bg-transparent focus:ring-1 focus:ring-primary-500 outline-none" min="0" step="0.01">
                              </td>
                              <td class="px-4 py-3">
                                  <input type="number" v-model="variant.stock_qty" class="w-full px-2 py-1.5 border border-gray-200 dark:border-slate-600 rounded bg-transparent focus:ring-1 focus:ring-primary-500 outline-none" min="0">
                              </td>
                              <td class="px-4 py-3">
                                  <input type="text" v-model="variant.sku" class="w-full px-2 py-1.5 border border-gray-200 dark:border-slate-600 rounded bg-transparent focus:ring-1 focus:ring-primary-500 outline-none" placeholder="SKU">
                              </td>
                              <td class="px-4 py-3">
                                   <div class="flex items-center space-x-2">
                                      <div v-if="variant.imagePreview" class="relative group w-8 h-8 rounded border border-gray-200 dark:border-slate-600 overflow-hidden shadow-sm">
                                          <img :src="variant.imagePreview" class="w-full h-full object-cover">
                                      </div>
                                      <button @click="openMediaLibrary('variant', idx)" type="button" class="text-gray-400 hover:text-primary-500 transition-colors p-1.5 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-lg">
                                          <UploadCloud class="w-5 h-5"/>
                                      </button>
                                   </div>
                              </td>
                              <td class="px-4 py-3 text-center">
                                  <input type="checkbox" v-model="variant.is_active" class="w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500 dark:focus:ring-primary-600">
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
                        <div v-if="galleryItems.length > 0" class="mt-6">
                            <h4 class="text-xs font-semibold text-gray-500 dark:text-slate-400 uppercase tracking-wider mb-3">Selected Images (Drag to reorder)</h4>
                            <div class="flex flex-wrap gap-4">
                               <div 
                                 v-for="(item, idx) in galleryItems" 
                                 :key="item.id" 
                                 draggable="true"
                                 @dragstart="onDragStart(idx)"
                                 @dragover.prevent="onDragOver($event, idx)"
                                 @drop="onDrop(idx)"
                                 class="w-24 h-24 border-2 border-transparent hover:border-primary-500 dark:hover:border-primary-400 rounded-lg overflow-hidden relative group shadow-sm bg-white dark:bg-slate-800 cursor-move transition-all active:scale-95 active:shadow-lg"
                               >
                                  <img :src="item.preview" class="w-full h-full object-cover pointer-events-none" />
                                  <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none">
                                     <GripVertical class="w-6 h-6 text-white" />
                                  </div>
                                  <button @click.stop="removeGalleryImage(idx)" type="button" class="absolute top-1 right-1 bg-white/90 dark:bg-slate-900/90 text-red-500 dark:text-red-400 rounded-full p-1 opacity-100 sm:opacity-0 group-hover:opacity-100 transition-all hover:bg-red-50 dark:hover:bg-red-900/50 shadow-sm z-10">
                                     <X class="w-3.5 h-3.5" />
                                  </button>
                                  <div class="absolute bottom-1 left-1 bg-black/50 text-white text-[8px] px-1.5 py-0.5 rounded font-bold uppercase transition-opacity opacity-0 group-hover:opacity-100">
                                     Item #{{ idx + 1 }}
                                  </div>
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

            <div class="mt-6 pt-5 border-t border-gray-100 dark:border-slate-800">
               <h4 class="text-xs font-black text-gray-400 dark:text-slate-500 uppercase tracking-widest mb-4">Stock Visibility</h4>
               <div class="space-y-2">
                  <label v-for="option in [
                    { value: 'quantity', label: 'Show Stock Quantity', desc: 'Display exact numbers' },
                    { value: 'text_only', label: 'Text Only', desc: 'Display In-Stock/Out-of-Stock' },
                    { value: 'hide', label: 'Hide Stock', desc: 'Hide stock level from customers' }
                  ]" :key="option.value" 
                  class="flex items-center p-3 rounded-xl border border-gray-100 dark:border-slate-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-all group"
                  :class="{'border-primary-500 bg-primary-50/30 dark:bg-primary-900/10': form.stock_visibility_state === option.value}">
                     <input type="radio" v-model="form.stock_visibility_state" :value="option.value" class="sr-only">
                     <div class="w-4 h-4 rounded-full border-2 border-gray-300 dark:border-slate-600 flex items-center justify-center mr-3 group-hover:border-primary-400"
                     :class="{'border-primary-500 bg-primary-500': form.stock_visibility_state === option.value}">
                        <div v-if="form.stock_visibility_state === option.value" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                     </div>
                     <div>
                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ option.label }}</div>
                        <div class="text-[10px] text-gray-400 dark:text-slate-500">{{ option.desc }}</div>
                     </div>
                  </label>
               </div>

               <div class="mt-4">
                  <label class="block text-xs font-bold text-gray-500 dark:text-slate-500 uppercase tracking-widest mb-1.5 ml-1">Low Stock Warning Qty</label>
                  <input v-model="form.low_stock_warning_qty" type="number" min="0" placeholder="e.g. 5" class="w-full px-4 py-2 bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 outline-none text-sm font-bold text-gray-900 dark:text-white transition-all shadow-sm" />
                  <p class="mt-1 text-[10px] text-gray-400 dark:text-slate-500">Alert me when stock falls below this level.</p>
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

    <!-- Quick Add Attribute Modal -->
    <div v-if="showQuickAddAttributeModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 transition-opacity">
       <div class="bg-white dark:bg-slate-900 rounded-xl max-w-md w-full p-6 shadow-xl animate-in zoom-in-95 duration-200">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Add Quick Attribute</h3>
          <div class="space-y-4">
             <div>
                 <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Attribute Name</label>
                 <input type="text" v-model="quickAttributeForm.name" placeholder="e.g. Color" class="w-full px-4 py-2 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-primary-500 outline-none bg-white dark:bg-slate-900 text-gray-900 dark:text-white">
             </div>
             
             <div>
                 <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Attribute Type</label>
                 <select v-model="quickAttributeForm.type" class="w-full px-4 py-2 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-primary-500 outline-none bg-white dark:bg-slate-900 text-gray-900 dark:text-white">
                     <option value="button">Standard Button / Label</option>
                     <option value="color">Color Swatch</option>
                 </select>
             </div>

             <div v-if="quickAttributeForm.type !== 'color'">
                 <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Values (Comma separated)</label>
                 <input type="text" v-model="quickAttributeForm.values" placeholder="e.g. Cotton, Polyester, Silk" class="w-full px-4 py-2 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-primary-500 outline-none bg-white dark:bg-slate-900 text-gray-900 dark:text-white">
             </div>

             <div v-if="quickAttributeForm.type === 'color'" class="space-y-3">
                 <div class="flex items-center justify-between">
                     <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Add Colors</label>
                     <button @click="addColorToQuickForm" type="button" class="text-xs flex items-center text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">
                         <Plus class="w-3 h-3 mr-1" /> Add Color
                     </button>
                 </div>
                 <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                     <div v-for="(colItem, idx) in quickAttributeForm.colorValues" :key="idx" class="flex gap-2 items-center">
                         <input type="text" v-model="colItem.value" placeholder="Color Name (e.g. Red)" class="flex-1 px-3 py-1.5 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-primary-500 outline-none bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-sm">
                         <input type="color" v-model="colItem.meta" class="w-10 h-8 rounded shrink-0 cursor-pointer border border-gray-200 dark:border-slate-700">
                         <button @click="quickAttributeForm.colorValues.splice(idx, 1)" type="button" class="p-1.5 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors shrink-0">
                             <X class="w-4 h-4"/>
                         </button>
                     </div>
                 </div>
             </div>

             <div class="pt-4 border-t border-gray-100 dark:border-slate-800 space-y-4">
                 <div>
                     <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Guide Description (Optional)</label>
                     <textarea v-model="quickAttributeForm.description" rows="2" placeholder="e.g. Size guide for international shirts" class="w-full px-4 py-2 border border-gray-200 dark:border-slate-700 rounded-lg focus:ring-primary-500 outline-none bg-white dark:bg-slate-900 text-gray-900 dark:text-white text-sm"></textarea>
                 </div>
                 <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Guide Image (Optional)</label>
                    <div class="flex items-center gap-4">
                        <div v-if="quickAttributeForm.guidePreview" class="w-16 h-16 rounded border overflow-hidden shrink-0 bg-gray-50">
                            <img :src="quickAttributeForm.guidePreview" class="w-full h-full object-cover">
                        </div>
                        <label class="flex-1 border-2 border-dashed border-gray-200 dark:border-slate-700 rounded-lg px-4 py-3 text-center cursor-pointer hover:border-primary-500 transition-all">
                            <input type="file" @change="handleGuideImageUpload" class="hidden" accept="image/*">
                            <span class="text-xs text-gray-500 dark:text-slate-400">Apply Guide Image / Size Chart</span>
                        </label>
                    </div>
                 </div>
             </div>
          </div>
          <div class="mt-6 flex justify-end gap-3">
             <button @click="showQuickAddAttributeModal = false" type="button" class="px-4 py-2 text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-slate-800 hover:bg-gray-200 dark:hover:bg-slate-700 rounded-lg transition-colors text-sm font-medium">Cancel</button>
             <button @click="submitQuickAttribute" type="button" :disabled="isSubmittingAttribute" class="px-4 py-2 text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors text-sm font-medium flex items-center disabled:opacity-50">
                <span v-if="isSubmittingAttribute" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin mr-2"></span>
                Save Attribute
             </button>
          </div>
       </div>
    </div>

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
  ListChecks,
  GripVertical,
  Trash2,
  RefreshCw
} from 'lucide-vue-next'
import AppRichEditor from '~/components/AppRichEditor.vue'
import AppSearchSelect from '~/components/AppSearchSelect.vue'
import AppMediaLibrary from '~/components/AppMediaLibrary.vue'

definePageMeta({
  middleware: 'auth',
  permissions: 'products.create'
})

const config = useRuntimeConfig()
const auth = useAuthStore()
const { getAll, createItem } = useCrud()
const router = useRoute()
import { toast } from 'vue-sonner';

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

// Media Library State
const showMediaModal = ref(false)
const mediaModalMode = ref('thumbnail') // thumbnail, gallery, variant
const targetVariantIndex = ref(null)

const openMediaLibrary = (mode, index = null) => {
    mediaModalMode.value = mode
    targetVariantIndex.value = index
    showMediaModal.value = true
}

const handleMediaSelection = (selected) => {
    if (mediaModalMode.value === 'thumbnail') {
        thumbnailPreview.value = selected.file_url
        form.value.image = selected.file_path
    } else if (mediaModalMode.value === 'gallery') {
        const newImages = Array.isArray(selected) ? selected : [selected]
        newImages.forEach(img => {
            if (!galleryItems.value.some(item => item.path === img.file_path)) {
                galleryItems.value.push({
                    id: img.id,
                    path: img.file_path,
                    preview: img.file_url,
                    source: 'existing'
                })
            }
        })
    } else if (mediaModalMode.value === 'variant') {
        if (targetVariantIndex.value !== null) {
            generatedVariants.value[targetVariantIndex.value].imagePreview = selected.file_url
            generatedVariants.value[targetVariantIndex.value].imagePath = selected.file_path
        }
    }
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
  weight: '',
  has_variants: false,
  is_featured: false,
  is_special: false,
  is_trending: false,
  is_buy_one_get_one: false,
  is_preorder: false,
  is_dropshipping: false,
  stock_visibility_state: 'quantity',
  low_stock_warning_qty: 1,
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
  specifications: []
})

// File state
const thumbnailFile = ref(null)

// Local state for image previews and FAQ UI
const thumbnailPreview = ref(null)
const galleryItems = ref([]) // array of { id, file, preview }
const localFaqs = ref([
   { question: '', answer: '' }
])
const localSpecificationGroups = ref([
   { 
     title: 'Key Features', 
     items: [{ label: '', value: '' }] 
   }
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
   const newFiles = files.slice(0, 10 - galleryItems.value.length) 
   
   newFiles.forEach(file => {
      galleryItems.value.push({
         id: Math.random().toString(36).substr(2, 9),
         file: file,
         preview: URL.createObjectURL(file)
      })
   })
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
   // Optional: Add visual feedback for targeted slot
}
const onDrop = (index) => {
   const item = galleryItems.value.splice(dragIndex.value, 1)[0]
   galleryItems.value.splice(index, 0, item)
   dragIndex.value = null
}

// FAQ Handlers
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

const generateSKU = () => {
    let prefix = '';
    if (form.value.name) {
        prefix = form.value.name
            .split(' ')
            .map(word => word[0])
            .join('')
            .toUpperCase()
            .slice(0, 3);
    } else {
        prefix = 'SKU';
    }
    
    const random = Math.floor(1000 + Math.random() * 9000);
    const datePart = new Date().toISOString().slice(2, 4) + new Date().toISOString().slice(5, 7);
    form.value.sku = `${prefix}-${datePart}-${random}`;
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
        const [categoriesRes, brandsRes, unitsRes, attributesRes] = await Promise.all([
             getAll('/vendor/attributes/categories'),
             getAll('/vendor/attributes/brands'),
             getAll('/vendor/attributes/units'),
             getAll('/vendor/product-attributes')
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
        if (attributesRes && attributesRes.data) productAttributesList.value = attributesRes.data
    } catch (e) {
        console.error('Failed to fetch options')
    }
}

// Variant Logic
const productAttributesList = ref([])
const selectedAttributesConfig = ref([]) // [{ attr_index: 0, selected_values: []}]
const generatedVariants = ref([])

// Quick Attribute Modal State
const showQuickAddAttributeModal = ref(false)
const isSubmittingAttribute = ref(false)
const quickAttributeForm = ref({
   name: '',
   type: 'button',
   values: '',
   colorValues: [],
   description: '',
   guideImage: null,
   guidePreview: null
})

const handleGuideImageUpload = (event) => {
    const file = event.target.files[0]
    if (file) {
        quickAttributeForm.value.guideImage = file
        quickAttributeForm.value.guidePreview = URL.createObjectURL(file)
    }
}

const addColorToQuickForm = () => {
    quickAttributeForm.value.colorValues.push({ value: '', meta: '#000000' })
}

const submitQuickAttribute = async () => {
    if (!quickAttributeForm.value.name) {
        toast.error('Please provide an attribute name.')
        return
    }

    let finalValues = []
    
    if (quickAttributeForm.value.type === 'color') {
        finalValues = quickAttributeForm.value.colorValues
            .filter(v => v.value)
            .map(v => ({ value: v.value, meta: v.meta }))
        
        if (finalValues.length === 0) {
            toast.error('Please provide at least one color value.')
            return
        }
    } else {
        const valueArray = quickAttributeForm.value.values.split(',').map(v => v.trim()).filter(v => v)
        if (valueArray.length === 0) {
            toast.error('Please provide at least one value for the attribute.')
            return
        }
        finalValues = valueArray.map(val => ({ value: val }))
    }
    
    isSubmittingAttribute.value = true
    try {
        const formData = new FormData()
        formData.append('name', quickAttributeForm.value.name)
        formData.append('type', quickAttributeForm.value.type)
        formData.append('values', JSON.stringify(finalValues))
        
        if (quickAttributeForm.value.description) {
            formData.append('description', quickAttributeForm.value.description)
        }
        
        if (quickAttributeForm.value.guideImage) {
            formData.append('guide_image', quickAttributeForm.value.guideImage)
        }

        const res = await createItem('/vendor/product-attributes', formData);
        
        if (res) {
            productAttributesList.value.push(res)
            showQuickAddAttributeModal.value = false
            quickAttributeForm.value = { name: '', type: 'button', values: '', colorValues: [], description: '', guideImage: null, guidePreview: null }
            toast.success('Attribute created successfully')
        }
    } catch (e) {
        toast.error('Failed to create attribute: ' + (e.response?._data?.message || e.message))
    } finally {
        isSubmittingAttribute.value = false
    }
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
        const existing = generatedVariants.value.find(v => v._key === comboKey);
        
        return {
            _key: comboKey,
            attributes: combo,
            sku: existing ? existing.sku : form.value.sku ? `${form.value.sku}-${combo.map(c => c.value).join('-')}` : null,
            price: existing ? existing.price : form.value.sale_price,
            stock_qty: existing ? existing.stock_qty : form.value.stock_qty || 0,
            imageFile: existing ? existing.imageFile : null,
            imagePreview: existing ? existing.imagePreview : null,
            is_active: existing ? existing.is_active : true
        };
    });
}

// Watch config structure to regenerate smartly
watch(() => selectedAttributesConfig.value, () => generateCombinations(), { deep: true })

const handleVariantImage = (event, index) => {
   const file = event.target.files[0]
   if (file) {
      generatedVariants.value[index].imageFile = file
      generatedVariants.value[index].imagePreview = URL.createObjectURL(file)
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
  
  // Construct gallery items metadata for ordering
  const galleryMetadata = []
  galleryItems.value.forEach((item, index) => {
      if (item.source === 'existing') {
          galleryMetadata.push({
              source: 'existing',
              value: item.path,
              index: index
          })
      } else {
          formData.append(`gallery[${index}]`, item.file)
          galleryMetadata.push({
              source: 'upload',
              index: index
          })
      }
  })
  formData.append('gallery_items', JSON.stringify(galleryMetadata))

  // Append FAQs
  // localFaqs contains empty ones potentially, filter and stringify
  const validFaqs = localFaqs.value.filter(f => f.question && f.answer)
  formData.append('faqs', JSON.stringify(validFaqs))

  // Append Specifications (Nested Grouped Structure)
  const validGroups = localSpecificationGroups.value
     .map(group => ({
        ...group,
        items: group.items.filter(i => i.label || i.value)
     }))
     .filter(group => group.title || group.items.length > 0)
     
  formData.append('specifications', JSON.stringify(validGroups))

  // Append Variants Data
  if (form.value.has_variants) {
      const cleanVariants = generatedVariants.value.map((v, index) => {
          if (v.imageFile) {
              formData.append(`variants_image_${index}`, v.imageFile)
          }
          return {
              sku: v.sku || null,
              price: v.price,
              stock_qty: v.stock_qty,
              is_active: v.is_active,
              image: v.imagePath || null,
              attributes: v.attributes.map(attr => attr.id)
          }
      })
      formData.append('variants', JSON.stringify(cleanVariants))
  }

  await createItem('/vendor/products', formData, form)
  navigateTo('/vendor/products')
}
</script>
