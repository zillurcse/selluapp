<template>
  <div class="flex flex-col h-full bg-gray-50/50 dark:bg-slate-950 min-h-screen transition-colors duration-300 p-10">
    <!-- Header -->
    <div class="flex items-center justify-between px-8 py-6 bg-white dark:bg-slate-900 border-b border-gray-100 dark:border-slate-800 transition-colors duration-300 rounded-2xl mb-8 shadow-sm">
      <div class="flex items-center">
        <button @click="$router.back()" class="p-2 bg-black text-white rounded-full hover:bg-gray-800 transition-colors mr-6 shadow-sm">
          <ArrowLeft class="w-5 h-5" />
        </button>
        <div>
          <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
            {{ store.isEdit ? 'Edit Landing Page' : 'Create Landing Page' }}
          </h1>
          <p class="text-gray-500 dark:text-slate-400 mt-1">
            {{ store.isEdit ? 'Update your landing page settings below.' : 'Choose a type, select products, and go live.' }}
          </p>
        </div>
      </div>

      <!-- Step indicator -->
      <div class="hidden md:flex items-center gap-2">
        <div v-for="(step, idx) in steps" :key="idx"
          class="flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold transition-all"
          :class="currentStep === idx ? 'bg-black text-white dark:bg-white dark:text-black' : currentStep > idx ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400 dark:bg-slate-800 dark:text-slate-500'"
        >
          <CheckCircle2 v-if="currentStep > idx" class="w-4 h-4" />
          <span v-else class="w-4 h-4 rounded-full bg-current flex items-center justify-center text-[10px] text-white dark:text-black font-black">{{ idx + 1 }}</span>
          {{ step }}
        </div>
      </div>
    </div>

    <div class="max-w-5xl mx-auto w-full space-y-8">

      <!-- ===== STEP 0: Type Selection ===== -->
      <div v-if="currentStep === 0" class="space-y-6 animate-fade-in">
        <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-wide">Choose Page Type</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Single -->
          <button @click="selectType('single')"
            class="relative flex flex-col items-center p-8 rounded-3xl border-2 transition-all duration-300 group text-center"
            :class="formData.landing_page_type === 'single' ? 'border-purple-500 bg-purple-50/50 dark:bg-purple-900/10 shadow-lg scale-[1.02]' : 'border-gray-100 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-gray-200 hover:shadow-md'"
          >
            <div class="w-16 h-16 rounded-2xl bg-purple-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
              <Box class="w-8 h-8 text-white" />
            </div>
            <h3 class="font-black text-gray-900 dark:text-white text-lg mb-2">Single Product</h3>
            <p class="text-sm text-gray-500 dark:text-slate-400">Focused hero page for one product. Maximum conversion.</p>
            <div v-if="formData.landing_page_type === 'single'" class="absolute top-4 right-4 w-6 h-6 bg-purple-500 rounded-full flex items-center justify-center">
              <Check class="w-4 h-4 text-white" />
            </div>
          </button>

          <!-- Multiple -->
          <button @click="selectType('multiple')"
            class="relative flex flex-col items-center p-8 rounded-3xl border-2 transition-all duration-300 group text-center"
            :class="formData.landing_page_type === 'multiple' ? 'border-orange-500 bg-orange-50/50 dark:bg-orange-900/10 shadow-lg scale-[1.02]' : 'border-gray-100 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-gray-200 hover:shadow-md'"
          >
            <div class="absolute -top-3 left-1/2 -translate-x-1/2">
              <span class="bg-orange-500 text-white text-[10px] font-black px-2.5 py-0.5 rounded-full tracking-widest uppercase shadow">PRO</span>
            </div>
            <div class="w-16 h-16 rounded-2xl bg-orange-500 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
              <Layers class="w-8 h-8 text-white" />
            </div>
            <h3 class="font-black text-gray-900 dark:text-white text-lg mb-2">Multi Product</h3>
            <p class="text-sm text-gray-500 dark:text-slate-400">Showcase 2-4 products together. Great for bundles.</p>
            <div v-if="formData.landing_page_type === 'multiple'" class="absolute top-4 right-4 w-6 h-6 bg-orange-500 rounded-full flex items-center justify-center">
              <Check class="w-4 h-4 text-white" />
            </div>
          </button>

          <!-- Common -->
          <button @click="selectType('common')"
            class="relative flex flex-col items-center p-8 rounded-3xl border-2 transition-all duration-300 group text-center"
            :class="formData.landing_page_type === 'common' ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-900/10 shadow-lg scale-[1.02]' : 'border-gray-100 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-gray-200 hover:shadow-md'"
          >
            <div class="w-16 h-16 rounded-2xl bg-emerald-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
              <Globe class="w-8 h-8 text-white" />
            </div>
            <h3 class="font-black text-gray-900 dark:text-white text-lg mb-2">Common Page</h3>
            <p class="text-sm text-gray-500 dark:text-slate-400">Default store page shown when no specific page is set.</p>
            <div v-if="formData.landing_page_type === 'common'" class="absolute top-4 right-4 w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center">
              <Check class="w-4 h-4 text-white" />
            </div>
          </button>
        </div>
      </div>

      <!-- ===== STEP 1: Template ===== -->
      <div v-if="currentStep === 1" class="space-y-6 animate-fade-in">
        <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-wide">Choose Template</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div v-for="tmpl in availableTemplates" :key="tmpl.value"
            @click="formData.template_name = tmpl.value"
            class="relative group cursor-pointer transition-all duration-300"
          >
            <div class="aspect-[4/3] rounded-2xl overflow-hidden border-4 transition-all duration-300 shadow-md"
              :class="formData.template_name === tmpl.value ? 'border-blue-500 ring-4 ring-blue-100 dark:ring-blue-900/40 scale-[1.02]' : 'border-white dark:border-slate-800 hover:border-gray-200'"
            >
              <img :src="tmpl.preview" :alt="tmpl.label" class="w-full h-full object-cover grayscale-[0.2] group-hover:grayscale-0 transition-all duration-500" />
              <div v-if="formData.template_name === tmpl.value" class="absolute inset-0 bg-blue-500/10 flex items-center justify-center">
                <div class="bg-blue-500 text-white rounded-full p-2 shadow-lg">
                  <Check class="w-6 h-6" />
                </div>
              </div>
            </div>
            <div class="mt-3 flex items-center justify-between">
              <h3 class="font-bold text-gray-900 dark:text-white">{{ tmpl.label }}</h3>
              <span v-if="tmpl.free" class="text-xs font-bold px-2 py-0.5 bg-green-100 text-green-700 rounded">FREE</span>
              <span v-else class="text-xs font-bold px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded">PRO</span>
            </div>
          </div>
        </div>
      </div>

      <!-- ===== STEP 2: Products ===== -->
      <div v-if="currentStep === 2" class="space-y-6 animate-fade-in">
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-wide">
            {{ formData.landing_page_type === 'single' ? 'Select Product' : formData.landing_page_type === 'multiple' ? 'Select Products (Max 4)' : 'Select Products (Optional)' }}
          </h2>
          <!-- Search -->
          <div class="relative w-72">
            <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
            <input v-model="searchQuery" type="text" placeholder="Search products..." @input="handleSearch"
              class="w-full h-11 pl-12 pr-4 bg-gray-50 dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-2xl focus:border-blue-500 transition-all outline-none font-medium dark:text-slate-200 text-sm"
            />
          </div>
        </div>

        <!-- Selected products reorder (multi) -->
        <div v-if="formData.landing_page_type === 'multiple' && selectedProductObjects.length > 0" class="p-5 bg-blue-50/50 dark:bg-blue-900/10 border-2 border-blue-100 dark:border-blue-900/30 rounded-2xl">
          <p class="text-xs font-black text-blue-600 uppercase tracking-widest mb-3">Selected Order (Drag to reorder)</p>
          <div class="flex flex-wrap gap-3">
            <div
              v-for="(prod, idx) in selectedProductObjects"
              :key="prod.id"
              draggable="true"
              @dragstart="dragStart(idx)"
              @dragover.prevent="dragOver(idx)"
              @drop="dragDrop"
              class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-slate-800 rounded-xl border border-blue-200 dark:border-blue-800 cursor-grab active:cursor-grabbing shadow-sm"
            >
              <GripVertical class="w-4 h-4 text-gray-400" />
              <span class="w-5 h-5 rounded-full bg-blue-500 text-white text-[10px] font-black flex items-center justify-center">{{ idx + 1 }}</span>
              <span class="text-sm font-bold text-gray-800 dark:text-white max-w-[120px] truncate">{{ prod.name }}</span>
              <button @click="removeSelectedProduct(prod.id)" class="text-gray-300 hover:text-red-500 transition-colors ml-1">
                <X class="w-3 h-3" />
              </button>
            </div>
          </div>
        </div>

        <!-- Product grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
          <div v-for="product in products" :key="product.id"
            @click="toggleProduct(product)"
            class="relative flex flex-col p-4 rounded-2xl border-2 transition-all cursor-pointer group hover:shadow-lg"
            :class="isProductSelected(product.id) ? 'border-blue-500 bg-blue-50/30 dark:bg-blue-900/10' : 'border-gray-100 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-gray-200'"
          >
            <div class="w-full aspect-square rounded-xl bg-gray-50 dark:bg-slate-800 mb-3 flex items-center justify-center overflow-hidden">
              <img v-if="product.image" :src="product.image" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy" />
              <Package v-else class="w-10 h-10 text-gray-200" />
            </div>
            <div class="font-bold text-gray-900 dark:text-slate-100 leading-tight mb-1 text-sm line-clamp-2">{{ product.name }}</div>
            <div class="text-sm font-black text-blue-600 dark:text-blue-400">৳{{ product.sale_price }}</div>
            <div v-if="isProductSelected(product.id)" class="absolute top-3 right-3 bg-blue-500 text-white rounded-full p-1 shadow-md">
              <Check class="w-4 h-4" />
            </div>
          </div>
        </div>

        <!-- Load more -->
        <div class="flex flex-col items-center justify-center pt-4 gap-3">
          <div v-if="loadingProducts" class="flex items-center gap-2 text-gray-500">
            <Loader2 class="w-5 h-5 animate-spin" />
            <span class="text-sm">Loading products...</span>
          </div>
          <button v-if="hasMore && !loadingProducts" @click="loadMore"
            class="px-8 py-3 bg-gray-100 dark:bg-slate-800 text-gray-600 dark:text-slate-400 rounded-xl text-sm font-bold hover:bg-gray-200 transition-all active:scale-95">
            Load More
          </button>
          <p v-if="formData.landing_page_type === 'multiple'" class="text-xs text-gray-400">
            Selected: {{ formData.selectedProducts.length }} / 4 products
          </p>
        </div>
      </div>

      <!-- ===== STEP 3: Content ===== -->
      <div v-if="currentStep === 3" class="space-y-6 animate-fade-in">
        <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-wide">Content & Customization</h2>
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-slate-800 space-y-6">

          <!-- Title -->
          <div class="space-y-2">
            <label class="text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Page Title *</label>
            <input v-model="formData.title" type="text" placeholder="e.g. Premium Wireless Headphones – The Future of Sound"
              class="w-full h-14 px-6 bg-gray-50 dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-2xl focus:border-blue-500 transition-all outline-none font-medium dark:text-slate-200"
            />
          </div>

          <!-- Hero Subtitle -->
          <div class="space-y-2">
            <label class="text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Hero Subtitle (Optional)</label>
            <input v-model="formData.settings.hero_subtitle" type="text" placeholder="e.g. New Release · Limited Stock"
              class="w-full h-12 px-5 bg-gray-50 dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-xl focus:border-blue-500 transition-all outline-none font-medium dark:text-slate-200 text-sm"
            />
          </div>

          <!-- Hero Description (for multi & common) -->
          <div v-if="formData.landing_page_type !== 'single'" class="space-y-2">
            <label class="text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Hero Description</label>
            <textarea v-model="formData.settings.hero_desc" rows="3" placeholder="Campaign tagline or description..."
              class="w-full px-5 py-4 bg-gray-50 dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-xl focus:border-blue-500 transition-all outline-none font-medium text-sm dark:text-slate-200"
            ></textarea>
          </div>

          <!-- CTA Text -->
          <div class="space-y-2">
            <label class="text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">CTA Button Text</label>
            <input v-model="formData.settings.cta_text" type="text" placeholder="e.g. Add to Collection · Order Now · Explore All"
              class="w-full h-12 px-5 bg-gray-50 dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-xl focus:border-blue-500 transition-all outline-none font-medium dark:text-slate-200 text-sm"
            />
          </div>

          <!-- Narrative (single) -->
          <div v-if="formData.landing_page_type === 'single'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Narrative Title</label>
              <input v-model="formData.settings.narrative_title" placeholder="e.g. Immersive Experience"
                class="w-full h-12 px-5 bg-gray-50 dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-xl focus:border-blue-500 transition-all outline-none font-medium text-sm dark:text-slate-200"
              />
            </div>
            <div class="space-y-2">
              <label class="text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Narrative Description</label>
              <textarea v-model="formData.settings.narrative_desc" rows="2" placeholder="Story behind the product..."
                class="w-full px-5 py-3 bg-gray-50 dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-xl focus:border-blue-500 transition-all outline-none font-medium text-sm dark:text-slate-200"
              ></textarea>
            </div>
          </div>

          <!-- Key Features -->
          <div class="space-y-4">
            <label class="text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Key Features (Max 3)</label>
            <div v-for="(feature, index) in formData.settings.features" :key="index"
              class="p-5 bg-gray-50 dark:bg-slate-800/50 rounded-2xl border-2 border-dashed border-gray-200 dark:border-slate-700 space-y-3">
              <div class="flex justify-between items-center">
                <span class="text-xs font-black text-blue-500 uppercase">Feature #{{ index + 1 }}</span>
                <button @click="removeFeature(index)" class="text-red-500 hover:text-red-600 transition-colors">
                  <Trash2 class="w-4 h-4" />
                </button>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <input v-model="feature.title" placeholder="Feature Title" class="w-full h-10 px-4 bg-white dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-xl focus:border-blue-500 outline-none text-sm font-medium" />
                <input v-model="feature.desc" placeholder="Brief Description" class="w-full h-10 px-4 bg-white dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-xl focus:border-blue-500 outline-none text-sm font-medium" />
              </div>
            </div>
            <button v-if="formData.settings.features.length < 3" @click="addFeature"
              class="w-full py-4 border-2 border-dashed border-gray-200 dark:border-slate-700 rounded-2xl text-gray-400 hover:border-blue-500 hover:text-blue-500 transition-all flex items-center justify-center gap-2 text-sm font-bold">
              <Plus class="w-4 h-4" /> Add Feature
            </button>
          </div>

          <!-- Floating Points (single only) -->
          <div v-if="formData.landing_page_type === 'single'" class="space-y-4">
            <label class="text-sm font-bold text-gray-700 dark:text-slate-300 uppercase tracking-wider">Floating Info Points (On Product Image)</label>
            <div v-for="(point, index) in formData.settings.floating_points" :key="index"
              class="p-5 bg-gray-50 dark:bg-slate-800/50 rounded-2xl border-2 border-dashed border-amber-200 dark:border-amber-800/30 space-y-3">
              <div class="flex justify-between items-center">
                <span class="text-xs font-black text-amber-500 uppercase">Point #{{ index + 1 }}</span>
                <button @click="removePoint(index)" class="text-red-500 hover:text-red-600"><Trash2 class="w-4 h-4" /></button>
              </div>
              <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <input v-model="point.label" placeholder="Label" class="w-full h-10 px-3 bg-white dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-xl focus:border-blue-500 outline-none text-sm font-medium" />
                <input v-model="point.value" placeholder="Value" class="w-full h-10 px-3 bg-white dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-xl focus:border-blue-500 outline-none text-sm font-medium" />
                <input v-model.number="point.x" type="number" placeholder="X%" class="w-full h-10 px-3 bg-white dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-xl focus:border-blue-500 outline-none text-sm font-medium" />
                <input v-model.number="point.y" type="number" placeholder="Y%" class="w-full h-10 px-3 bg-white dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-xl focus:border-blue-500 outline-none text-sm font-medium" />
              </div>
            </div>
            <button v-if="formData.settings.floating_points.length < 3" @click="addPoint"
              class="w-full py-4 border-2 border-dashed border-amber-200 dark:border-amber-800/30 rounded-2xl text-gray-400 hover:border-amber-500 hover:text-amber-500 transition-all flex items-center justify-center gap-2 text-sm font-bold">
              <Plus class="w-4 h-4" /> Add Info Point
            </button>
          </div>
        </div>
      </div>

      <!-- ===== STEP 4: Schedule & Settings ===== -->
      <div v-if="currentStep === 4" class="space-y-6 animate-fade-in">
        <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-wide">Schedule & Settings</h2>
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 shadow-sm border border-gray-100 dark:border-slate-800 space-y-8">

          <!-- Campaign Scheduling -->
          <div class="space-y-4">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-indigo-500 text-white flex items-center justify-center">
                <Calendar class="w-5 h-5" />
              </div>
              <h3 class="text-base font-black text-gray-900 dark:text-white uppercase tracking-wide">Campaign Scheduling</h3>
            </div>
            <div class="p-4 bg-indigo-50/50 dark:bg-indigo-900/10 rounded-2xl border border-indigo-100 dark:border-indigo-900/20">
              <p class="text-xs text-indigo-700 dark:text-indigo-400 font-medium mb-4">Optional: Set start and end dates for your campaign. Outside these dates the page will automatically become inactive.</p>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                  <label class="text-sm font-bold text-gray-700 dark:text-slate-300">Campaign Starts</label>
                  <input v-model="formData.campaign_start_at" type="datetime-local"
                    class="w-full h-12 px-4 bg-white dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-xl focus:border-indigo-500 transition-all outline-none font-medium text-sm dark:text-slate-200"
                  />
                </div>
                <div class="space-y-2">
                  <label class="text-sm font-bold text-gray-700 dark:text-slate-300">Campaign Ends</label>
                  <input v-model="formData.campaign_end_at" type="datetime-local"
                    class="w-full h-12 px-4 bg-white dark:bg-slate-800 border-2 border-gray-100 dark:border-slate-700 rounded-xl focus:border-indigo-500 transition-all outline-none font-medium text-sm dark:text-slate-200"
                  />
                </div>
              </div>
              <!-- Countdown preview -->
              <div v-if="countdownText" class="mt-4 flex items-center gap-2 text-sm font-bold text-amber-600 dark:text-amber-400">
                <Clock class="w-4 h-4" />
                Campaign ends in: {{ countdownText }}
              </div>
            </div>
          </div>

          <!-- Status -->
          <div class="space-y-4">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-green-500 text-white flex items-center justify-center">
                <ToggleRight class="w-5 h-5" />
              </div>
              <h3 class="text-base font-black text-gray-900 dark:text-white uppercase tracking-wide">Page Status</h3>
            </div>
            <div class="flex items-center gap-4 p-5 bg-gray-50 dark:bg-slate-800/50 rounded-2xl border border-gray-100 dark:border-slate-700">
              <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" :checked="formData.status === 'active'" @change="formData.status = $event.target.checked ? 'active' : 'draft'" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
              </label>
              <div>
                <span class="font-bold text-gray-900 dark:text-white">{{ formData.status === 'active' ? 'Active (Live)' : 'Draft (Hidden)' }}</span>
                <p class="text-xs text-gray-500 dark:text-slate-400">{{ formData.status === 'active' ? 'This page is visible to visitors.' : 'This page is hidden from visitors.' }}</p>
              </div>
              <span class="ml-auto px-3 py-1 rounded-full text-xs font-black"
                :class="formData.status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-500 dark:bg-slate-700 dark:text-slate-400'"
              >{{ formData.status === 'active' ? '● LIVE' : '○ DRAFT' }}</span>
            </div>
          </div>

          <!-- Set as Home -->
          <div class="flex items-center gap-4 p-5 rounded-2xl border-2 bg-blue-50/50 dark:bg-blue-900/10 border-blue-100 dark:border-blue-900/30">
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" v-model="formData.is_home" class="sr-only peer">
              <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
            </label>
            <div>
              <span class="font-bold text-gray-900 dark:text-white uppercase tracking-wide text-sm">Set as Store Home Page</span>
              <p class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">Visitors going to your store root will see this landing page instead of the regular shop.</p>
            </div>
            <div v-if="formData.is_home" class="ml-auto flex items-center text-xs font-black text-blue-600 dark:text-blue-400 gap-1">
              <CheckCircle2 class="w-4 h-4" /> ACTIVE
            </div>
          </div>
        </div>
      </div>

      <!-- Navigation Buttons -->
      <div class="flex items-center justify-between pt-4 pb-10">
        <button v-if="currentStep > 0" @click="currentStep--"
          class="flex items-center gap-2 px-6 py-3 border-2 border-gray-200 dark:border-slate-700 rounded-2xl font-bold text-gray-600 dark:text-slate-400 hover:border-gray-400 transition-all active:scale-95">
          <ChevronLeft class="w-5 h-5" /> Back
        </button>
        <div v-else></div>

        <div class="flex items-center gap-3">
          <!-- Skip products for common type -->
          <button v-if="currentStep === 2 && formData.landing_page_type === 'common'" @click="currentStep++"
            class="px-6 py-3 border-2 border-gray-200 dark:border-slate-700 rounded-2xl font-bold text-gray-500 hover:border-gray-400 transition-all text-sm">
            Skip (Optional)
          </button>

          <button v-if="currentStep < steps.length - 1" @click="nextStep"
            :disabled="!canProceed"
            class="flex items-center gap-2 px-8 py-3 bg-black dark:bg-white text-white dark:text-black rounded-2xl font-black hover:bg-gray-800 dark:hover:bg-gray-100 transition-all active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed shadow-lg">
            Next <ChevronRight class="w-5 h-5" />
          </button>

          <button v-else @click="submitLandingPage"
            :disabled="!isValid || store.isLoading"
            class="flex items-center gap-3 px-10 py-4 bg-black dark:bg-white text-white dark:text-black rounded-2xl font-black text-lg hover:bg-gray-800 transition-all active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed shadow-xl group">
            {{ store.isEdit ? 'Update Page' : "Create Page" }}
            <ArrowRight v-if="!store.isLoading" class="w-6 h-6 group-hover:translate-x-1 transition-transform" />
            <Loader2 v-else class="w-6 h-6 animate-spin" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {
  ArrowLeft, ArrowRight, ChevronLeft, ChevronRight,
  Check, CheckCircle2, Box, Layers, Globe,
  Search, Package, Plus, Trash2, Loader2,
  GripVertical, X, Calendar, Clock, ToggleRight
} from 'lucide-vue-next'
import { toast } from 'vue-sonner'

definePageMeta({
  layout: 'default',
  middleware: 'auth',
  permissions: 'landing_pages.create'
})

const { getAll, createItem, getById, updateItem } = useCrud()
const route = useRoute()
const utilityStore = useUtilityStore()
utilityStore.pageBackLink = '/vendor/landing-page/all'

const store = utilityStore
const currentStep = ref(0)
const steps = ['Type', 'Template', 'Products', 'Content', 'Schedule']

// ---- Form Data ----
const formData = ref({
  landing_page_type: 'single',
  template_name: 'modern',
  title: '',
  product_id: null,
  selectedProducts: [], // for multi
  status: 'active',
  is_home: false,
  campaign_start_at: '',
  campaign_end_at: '',
  settings: {
    hero_subtitle: '',
    hero_desc: '',
    narrative_title: '',
    narrative_desc: '',
    cta_text: '',
    features: [],
    floating_points: []
  }
})

// ---- Template Definitions ----
const templatesByType = {
  single: [
    { value: 'modern', label: 'Modern Dark', preview: 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&q=80&w=800', free: true },
    { value: 'premium_1', label: 'Vibrant Pink', preview: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&q=80&w=800', free: false },
    { value: 'premium_2', label: 'Ocean Breeze', preview: 'https://images.unsplash.com/photo-1491553895911-0055eca6402d?auto=format&fit=crop&q=80&w=800', free: false },
    { value: 'white_modern', label: 'White Modern', preview: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&q=80&w=800', free: false },
  ],
  multiple: [
    { value: 'bundle_grid', label: 'Bundle Grid', preview: 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&q=80&w=800', free: true },
    { value: 'showcase_slider', label: 'Showcase Slider', preview: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&q=80&w=800', free: false },
    { value: 'vertical_stack', label: 'Vertical Stack', preview: 'https://images.unsplash.com/photo-1481437156560-3205f6a55735?auto=format&fit=crop&q=80&w=800', free: false },
  ],
  common: [
    { value: 'store_grid', label: 'Store Grid', preview: 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&q=80&w=800', free: true },
    { value: 'store_hero', label: 'Store Hero', preview: 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?auto=format&fit=crop&q=80&w=800', free: false },
  ]
}

const availableTemplates = computed(() => templatesByType[formData.value.landing_page_type] || [])

// ---- Product Search & Pagination ----
const products = ref([])
const loadingProducts = ref(false)
const searchQuery = ref('')
const currentPage = ref(1)
const hasMore = ref(true)
const perPage = 8
let searchTimeout = null

const fetchProducts = async (page = 1, append = false) => {
  loadingProducts.value = true
  try {
    const res = await getAll('/vendor/products', { search: searchQuery.value, per_page: perPage, page })
    const newProducts = res.data || res || []
    products.value = append ? [...products.value, ...newProducts] : newProducts
    hasMore.value = newProducts.length === perPage
  } catch (e) {
    console.error(e)
  } finally {
    loadingProducts.value = false
  }
}

const handleSearch = () => {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => { currentPage.value = 1; fetchProducts(1, false) }, 400)
}
const loadMore = () => { if (!loadingProducts.value && hasMore.value) { currentPage.value++; fetchProducts(currentPage.value, true) } }

// Watch step 2 to load products
watch(currentStep, (step) => { if (step === 2) fetchProducts() })

// ---- Product Selection ----
const isProductSelected = (id) => {
  if (formData.value.landing_page_type === 'single') return formData.value.product_id === id
  return formData.value.selectedProducts.includes(id)
}

const toggleProduct = (product) => {
  if (formData.value.landing_page_type === 'single') {
    formData.value.product_id = product.id
  } else {
    const idx = formData.value.selectedProducts.indexOf(product.id)
    if (idx > -1) {
      formData.value.selectedProducts.splice(idx, 1)
    } else if (formData.value.selectedProducts.length < 4) {
      formData.value.selectedProducts.push(product.id)
    } else {
      toast.warning('Maximum 4 products allowed')
    }
  }
}

const removeSelectedProduct = (id) => {
  formData.value.selectedProducts = formData.value.selectedProducts.filter(p => p !== id)
}

// For drag-to-reorder
const selectedProductObjects = computed(() => {
  return formData.value.selectedProducts.map(id => products.value.find(p => p.id === id)).filter(Boolean)
})

let dragFromIndex = null
const dragStart = (idx) => { dragFromIndex = idx }
const dragOver = (idx) => { /* handled in drop */ }
const dragDrop = (evt, toIdx) => {
  if (dragFromIndex === null) return
  const ids = [...formData.value.selectedProducts]
  const [moved] = ids.splice(dragFromIndex, 1)
  // find toIndex relative to the drop target
  const el = evt.currentTarget
  const container = el.parentElement
  const siblings = [...container.children]
  const toIndex = siblings.indexOf(el)
  ids.splice(toIndex, 0, moved)
  formData.value.selectedProducts = ids
  dragFromIndex = null
}

// ---- Features & Points ----
const addFeature = () => formData.value.settings.features.push({ title: '', desc: '' })
const removeFeature = (i) => formData.value.settings.features.splice(i, 1)
const addPoint = () => formData.value.settings.floating_points.push({ x: 50, y: 50, label: '', value: '' })
const removePoint = (i) => formData.value.settings.floating_points.splice(i, 1)

// ---- Type Selection ----
const selectType = (type) => {
  formData.value.landing_page_type = type
  // set default template for type
  const templates = templatesByType[type]
  formData.value.template_name = templates?.[0]?.value || 'modern'
  // reset products
  formData.value.product_id = null
  formData.value.selectedProducts = []
}

// ---- Navigation ----
const canProceed = computed(() => {
  if (currentStep.value === 0) return !!formData.value.landing_page_type
  if (currentStep.value === 1) return !!formData.value.template_name
  if (currentStep.value === 2) {
    if (formData.value.landing_page_type === 'single') return !!formData.value.product_id
    if (formData.value.landing_page_type === 'multiple') return formData.value.selectedProducts.length > 0
    return true // common: optional
  }
  if (currentStep.value === 3) return !!formData.value.title.trim()
  return true
})

const nextStep = () => {
  if (canProceed.value) currentStep.value++
}

// ---- Countdown ----
const countdownText = computed(() => {
  if (!formData.value.campaign_end_at) return ''
  const diff = new Date(formData.value.campaign_end_at) - new Date()
  if (diff <= 0) return 'Campaign Ended'
  const d = Math.floor(diff / 86400000)
  const h = Math.floor((diff % 86400000) / 3600000)
  const m = Math.floor((diff % 3600000) / 60000)
  return `${d}d ${h}h ${m}m`
})

// ---- Validation ----
const isValid = computed(() => {
  if (!formData.value.title.trim()) return false
  if (formData.value.landing_page_type === 'single' && !formData.value.product_id) return false
  if (formData.value.landing_page_type === 'multiple' && formData.value.selectedProducts.length === 0) return false
  return true
})

// ---- Edit Mode ----
onMounted(async () => {
  if (route.query.id) {
    utilityStore.isEdit = true
    await fetchPageData(route.query.id)
  } else {
    utilityStore.isEdit = false
    // Pre-select type from query param (?type=single|multiple|common)
    const t = route.query.type
    if (t && ['single', 'multiple', 'common'].includes(t)) {
      selectType(t)
    }
  }
})

const fetchPageData = async (id) => {
  try {
    const res = await getById('/vendor/landing-pages', id)
    if (res) {
      const type = res.landing_page_type || 'single'
      formData.value = {
        landing_page_type: type,
        template_name: res.template_name || 'modern',
        title: res.title || '',
        product_id: res.product_id || null,
        selectedProducts: res.settings?.product_ids || (res.product_id ? [res.product_id] : []),
        status: res.status || 'active',
        is_home: !!(res.is_home === 1 || res.is_home === true),
        campaign_start_at: res.campaign_start_at ? res.campaign_start_at.slice(0, 16) : '',
        campaign_end_at: res.campaign_end_at ? res.campaign_end_at.slice(0, 16) : '',
        settings: {
          hero_subtitle: res.settings?.hero_subtitle || '',
          hero_desc: res.settings?.hero_desc || '',
          narrative_title: res.settings?.narrative_title || '',
          narrative_desc: res.settings?.narrative_desc || '',
          cta_text: res.settings?.cta_text || '',
          features: res.settings?.features || [],
          floating_points: res.settings?.floating_points || []
        }
      }
      // Move to last step for edit
      currentStep.value = 4
    }
  } catch (e) {
    console.error('Failed to load landing page:', e)
  }
}

// ---- Submit ----
const submitLandingPage = async () => {
  if (!isValid.value) return

  const type = formData.value.landing_page_type
  const payload = {
    landing_page_type: type,
    template_name: formData.value.template_name,
    title: formData.value.title,
    status: formData.value.status,
    is_home: formData.value.is_home,
    campaign_start_at: formData.value.campaign_start_at || null,
    campaign_end_at: formData.value.campaign_end_at || null,
    settings: {
      ...formData.value.settings,
      ...(type === 'multiple' ? { product_ids: formData.value.selectedProducts } : {})
    }
  }

  // product_id
  if (type === 'single') payload.product_id = formData.value.product_id
  else if (type === 'multiple') payload.product_id = formData.value.selectedProducts[0] || null
  // common: omit product_id

  try {
    if (store.isEdit) {
      await updateItem(`/vendor/landing-pages/${route.query.id}`, payload)
      toast.success('Landing page updated!')
      navigateTo('/vendor/landing-page/all')
    } else {
      await createItem('/vendor/landing-pages', payload)
      toast.success('Landing page created!')
      navigateTo('/vendor/landing-page/all')
    }
  } catch (e) {
    console.error('Submit error:', e)
    toast.error('Something went wrong. Please try again.')
  }
}
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(12px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
  animation: fadeIn 0.35s ease forwards;
}
</style>
