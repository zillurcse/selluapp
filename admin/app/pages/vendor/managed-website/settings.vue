<template>
  <div class="p-10 bg-[#f8fafc] min-h-screen">
    <div class="max-w-[1200px] mx-auto mb-8">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
          <NuxtLink 
            to="/vendor/managed-website" 
            class="w-10 h-10 bg-slate-900 rounded-full flex items-center justify-center text-white hover:bg-slate-800 transition-all active:scale-95 shadow-lg shadow-slate-900/20"
          >
            <ChevronLeft class="w-6 h-6" />
          </NuxtLink>
          <h1 class="text-2xl font-black text-slate-900 tracking-tight leading-none">Webpage Settings</h1>
        </div>
        <button 
          @click="saveSettings" 
          :disabled="saving || pending"
          class="px-8 py-3 bg-rose-600 hover:bg-rose-700 text-white font-black rounded-xl shadow-lg shadow-rose-200 transition-all active:scale-95 disabled:opacity-50 flex items-center gap-2"
        >
          <Save v-if="!saving" class="w-5 h-5" />
          <span>{{ saving ? 'Saving...' : 'Save Settings' }}</span>
        </button>
      </div>
    </div>
    
    <div v-if="pending" class="flex justify-center items-center py-20">
      <div class="w-8 h-8 border-4 border-rose-600 border-t-transparent rounded-full animate-spin"></div>
    </div>
    
    <div v-else class="max-w-[1200px] mx-auto space-y-8 pb-20">
      
      <!-- SEO & Webpage Identity -->
      <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex items-center gap-4 bg-indigo-50/30">
          <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center">
            <Globe class="w-6 h-6" />
          </div>
          <div>
            <h2 class="text-xl font-black text-slate-900">SEO & Webpage Identity</h2>
            <p class="text-sm font-bold text-slate-500">Configure how your site appears in search engines and browser tabs</p>
          </div>
        </div>
        
        <div class="p-8 space-y-8">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-6">
              <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">SEO Title (Browser Tab Title)</label>
                <input 
                  v-model="form.seoTitle" 
                  type="text" 
                  placeholder="e.g. My Premium Store | Best Electronics"
                  class="w-full h-14 px-6 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700"
                >
              </div>
              <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Meta Description</label>
                <textarea 
                  v-model="form.seoDescription" 
                  rows="3" 
                  placeholder="A short description of your store for search engine results..."
                  class="w-full p-6 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 resize-none"
                ></textarea>
              </div>
            </div>

            <div class="space-y-2">
              <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Meta Icon (Favicon / Browser Icon)</label>
              <div @click="openMediaLibrary('favicon')" class="w-full aspect-[2/1] bg-slate-50 border-2 border-dashed border-slate-200 rounded-[32px] flex items-center justify-center p-8 group cursor-pointer hover:border-indigo-300 hover:bg-slate-100/50 transition-all relative overflow-hidden">
                <img v-if="form.favicon" :src="form.favicon" alt="Favicon preview" class="w-20 h-20 object-contain drop-shadow-md">
                <div v-else class="text-center">
                  <div class="w-12 h-12 bg-white rounded-2xl shadow-sm border border-slate-100 flex items-center justify-center mx-auto mb-3">
                    <Upload class="w-6 h-6 text-slate-400" />
                  </div>
                  <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-tight">Recommended Size<br>32x32 or 64x64</p>
                </div>
                
                <div v-if="form.favicon" class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm">
                  <p class="text-white text-xs font-black uppercase tracking-widest bg-slate-900/50 px-4 py-2 rounded-full">Change Icon</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Lookbook Settings -->
      <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex items-center gap-4 bg-indigo-50/30">
          <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center">
            <Layout class="w-6 h-6" />
          </div>
          <div>
            <h2 class="text-xl font-black text-slate-900">Lookbook Section</h2>
            <p class="text-sm font-bold text-slate-500">Customize the featured lookbook section on your homepage</p>
          </div>
          <div class="ml-auto flex items-center gap-3">
             <span class="text-xs font-black uppercase text-slate-400">Show Section</span>
             <button 
                @click="lookbookForm.lookbook_status = lookbookForm.lookbook_status == '1' ? '0' : '1'"
                :class="['w-12 h-6 rounded-full transition-all duration-300 relative', lookbookForm.lookbook_status == '1' ? 'bg-indigo-600' : 'bg-slate-200']"
              >
                <div :class="['w-4 h-4 bg-white rounded-full absolute top-1 transition-all duration-300 shadow-sm', lookbookForm.lookbook_status == '1' ? 'left-7' : 'left-1']"></div>
              </button>
          </div>
        </div>
        
        <div class="p-8 space-y-8" v-if="lookbookForm.lookbook_status == '1'">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left: Image Selection -->
            <div class="space-y-2">
              <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Lookbook Main Image</label>
              <div @click="openMediaLibrary('lookbook_image')" class="w-full aspect-square md:aspect-[4/3] bg-slate-50 border-2 border-dashed border-slate-200 rounded-[32px] flex items-center justify-center p-8 group cursor-pointer hover:border-indigo-300 hover:bg-slate-100/50 transition-all relative overflow-hidden">
                <img v-if="lookbookForm.lookbook_image" :src="lookbookForm.lookbook_image" alt="Lookbook preview" class="w-full h-full object-cover">
                <div v-else class="text-center">
                  <div class="w-12 h-12 bg-white rounded-2xl shadow-sm border border-slate-100 flex items-center justify-center mx-auto mb-3">
                    <Upload class="w-6 h-6 text-slate-400" />
                  </div>
                  <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest font-heading leading-tight underline underline-offset-4">Click to upload your<br>Masterpiece Image</p>
                  <p class="text-[10px] font-bold text-slate-300 mt-2 uppercase tracking-widest leading-tight italic">Recommended Size: 1200x1200px</p>
                </div>
                
                <div v-if="lookbookForm.lookbook_image" class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm">
                  <p class="text-white text-xs font-black uppercase tracking-widest bg-slate-900/50 px-4 py-2 rounded-full">Change Image</p>
                </div>
              </div>
            </div>

            <!-- Right: Content Fields -->
            <div class="space-y-6">
              <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Lookbook Badge (Subtitle)</label>
                <input 
                  v-model="lookbookForm.lookbook_subtitle" 
                  type="text" 
                  placeholder="e.g. Concept Lookbook"
                  class="w-full h-14 px-6 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700"
                >
              </div>
              <div class="space-y-2">
                <label class="text-xs font-black uppercase tracking-wider ml-1 text-indigo-500">Main Heading (HTML Support)</label>
                <input 
                  v-model="lookbookForm.lookbook_title" 
                  type="text" 
                  placeholder="e.g. Sculpting Space <br/>with Light"
                  class="w-full h-14 px-6 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-bold text-slate-900"
                >
              </div>
              <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Description Text</label>
                <textarea 
                  v-model="lookbookForm.lookbook_description" 
                  rows="4" 
                  placeholder="A compelling description about your lookbook concept..."
                  class="w-full p-6 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 resize-none leading-relaxed"
                ></textarea>
              </div>
            </div>
          </div>

          <hr class="border-slate-50">

          <!-- Features Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="p-6 bg-slate-50/50 rounded-3xl space-y-4 border border-slate-100">
               <h4 class="text-xs font-black text-indigo-600 uppercase tracking-[0.2em] mb-4 italic">Feature Asset 01</h4>
               <div class="flex gap-4">
                  <div class="w-20 space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider pl-1">Icon</label>
                    <input v-model="lookbookForm.feature_1_icon" type="text" placeholder="🌿" class="w-full h-12 text-center text-xl bg-white border border-slate-200 rounded-xl outline-none shadow-sm capitalize">
                  </div>
                  <div class="flex-grow space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider pl-1">Feature Title</label>
                    <input v-model="lookbookForm.feature_1_title" type="text" placeholder="Eco Architecture" class="w-full h-12 px-4 bg-white border border-slate-200 rounded-xl outline-none shadow-sm font-bold">
                  </div>
               </div>
               <div class="space-y-2">
                  <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider pl-1">Short Description</label>
                  <input v-model="lookbookForm.feature_1_desc" type="text" placeholder="100% sustainable materials." class="w-full h-12 px-4 bg-white border border-slate-200 rounded-xl outline-none shadow-sm font-medium text-sm">
               </div>
            </div>

            <div class="p-6 bg-slate-50/50 rounded-3xl space-y-4 border border-slate-100">
               <h4 class="text-xs font-black text-indigo-600 uppercase tracking-[0.2em] mb-4 italic">Feature Asset 02</h4>
               <div class="flex gap-4">
                  <div class="w-20 space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider pl-1">Icon</label>
                    <input v-model="lookbookForm.feature_2_icon" type="text" placeholder="🖐️" class="w-full h-12 text-center text-xl bg-white border border-slate-200 rounded-xl outline-none shadow-sm capitalize">
                  </div>
                  <div class="flex-grow space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider pl-1">Feature Title</label>
                    <input v-model="lookbookForm.feature_2_title" type="text" placeholder="Master Crafted" class="w-full h-12 px-4 bg-white border border-slate-200 rounded-xl outline-none shadow-sm font-bold">
                  </div>
               </div>
               <div class="space-y-2">
                  <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider pl-1">Short Description</label>
                  <input v-model="lookbookForm.feature_2_desc" type="text" placeholder="Expert artisanal finishes." class="w-full h-12 px-4 bg-white border border-slate-200 rounded-xl outline-none shadow-sm font-medium text-sm">
               </div>
            </div>
          </div>

          <hr class="border-slate-50">

          <!-- Hotspot & Button -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-6">
              <h4 class="text-xs font-black text-rose-500 uppercase tracking-[0.2em] mb-4 flex items-center gap-2 italic underline underline-offset-4 decoration-rose-200"><div class="w-2 h-2 bg-rose-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(244,63,94,0.5)]"></div> Image Hotspot Detail</h4>
              <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                  <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider pl-1">Hotspot Badge</label>
                  <input v-model="lookbookForm.hotspot_badge" type="text" placeholder="Minimalist" class="w-full h-12 px-4 bg-slate-50 border border-slate-100 rounded-xl outline-none font-semibold uppercase tracking-widest text-[10px] text-indigo-600">
                </div>
                <div class="space-y-2">
                  <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider pl-1">Hotspot Title</label>
                  <input v-model="lookbookForm.hotspot_title" type="text" placeholder="Velvet Sofa" class="w-full h-12 px-4 bg-slate-50 border border-slate-100 rounded-xl outline-none font-bold">
                </div>
                <div class="space-y-2">
                  <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider pl-1">Hotspot Price</label>
                  <input v-model="lookbookForm.hotspot_price" type="text" placeholder="$899" class="w-full h-12 px-4 bg-slate-50 border border-slate-100 rounded-xl outline-none font-bold text-rose-500">
                </div>
                <div class="space-y-2">
                  <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider pl-1">Stock Status</label>
                  <input v-model="lookbookForm.hotspot_status" type="text" placeholder="In Stock" class="w-full h-12 px-4 bg-slate-50 border border-slate-100 rounded-xl outline-none font-black text-emerald-500 uppercase tracking-widest text-[10px]">
                </div>
              </div>
            </div>

            <div class="space-y-6">
              <h4 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em] mb-4 italic">Action Button</h4>
              <div class="space-y-4">
                <div class="space-y-2">
                  <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider pl-1">Button Text</label>
                  <input v-model="lookbookForm.button_text" type="text" placeholder="Shop The Concept" class="w-full h-12 px-4 bg-slate-50 border border-slate-100 rounded-xl outline-none font-black uppercase tracking-widest text-xs">
                </div>
                <div class="space-y-2">
                  <label class="text-[10px] font-black text-slate-400 uppercase tracking-wider pl-1">Button Redirect Link</label>
                  <input v-model="lookbookForm.shop_link" type="text" placeholder="/shop" class="w-full h-12 px-4 bg-slate-50 border border-slate-100 rounded-xl outline-none font-semibold text-slate-500">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Newsletter Settings -->
      <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex items-center gap-4 bg-indigo-50/30">
          <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center">
            <Megaphone class="w-6 h-6" />
          </div>
          <div>
            <h2 class="text-xl font-black text-slate-900">Newsletter Section</h2>
            <p class="text-sm font-bold text-slate-500">Customize the newsletter subscription section on your homepage</p>
          </div>
          <div class="ml-auto flex items-center gap-3">
             <span class="text-xs font-black uppercase text-slate-400">Show Section</span>
             <button 
                @click="newsletterForm.newsletter_status = newsletterForm.newsletter_status == '1' ? '0' : '1'"
                :class="['w-12 h-6 rounded-full transition-all duration-300 relative', newsletterForm.newsletter_status == '1' ? 'bg-indigo-600' : 'bg-slate-200']"
              >
                <div :class="['w-4 h-4 bg-white rounded-full absolute top-1 transition-all duration-300 shadow-sm', newsletterForm.newsletter_status == '1' ? 'left-7' : 'left-1']"></div>
              </button>
          </div>
        </div>
        
        <div class="p-8 space-y-8" v-if="newsletterForm.newsletter_status == '1'">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-6">
              <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Newsletter Title</label>
                <input 
                  v-model="newsletterForm.newsletter_title" 
                  type="text" 
                  placeholder="e.g. Join the Inner Circle"
                  class="w-full h-14 px-6 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700"
                >
              </div>
              <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Newsletter Description</label>
                <textarea 
                  v-model="newsletterForm.newsletter_description" 
                  rows="3" 
                  placeholder="Receive curated design inspiration, early product access..."
                  class="w-full p-6 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700 resize-none"
                ></textarea>
              </div>
            </div>

            <div class="space-y-6">
              <div class="space-y-2">
                <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Input Placeholder</label>
                <input 
                  v-model="newsletterForm.newsletter_placeholder" 
                  type="text" 
                  placeholder="e.g. Enter your email address"
                  class="w-full h-14 px-6 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700"
                >
              </div>
              <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                  <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Button Text</label>
                  <input 
                    v-model="newsletterForm.newsletter_button_text" 
                    type="text" 
                    placeholder="e.g. Subscribe"
                    class="w-full h-14 px-6 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700"
                  >
                </div>
                <div class="space-y-2">
                  <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Footer Text</label>
                  <input 
                    v-model="newsletterForm.newsletter_footer_text" 
                    type="text" 
                    placeholder="e.g. Zero Spam. Only Inspiration."
                    class="w-full h-14 px-6 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 outline-none transition-all font-semibold text-slate-700"
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Top Announcement Bar Details -->
      <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex items-center gap-4 bg-rose-50/30">
          <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center">
            <Megaphone class="w-6 h-6" />
          </div>
          <div>
            <h2 class="text-xl font-black text-slate-900">Announcement Bar</h2>
            <p class="text-sm font-bold text-slate-500">Display a prominent message at the top of your website</p>
          </div>
          <div class="ml-auto flex items-center gap-3">
             <span class="text-xs font-black uppercase text-slate-400">Show Bar</span>
             <button 
                @click="form.showAnnouncement = !form.showAnnouncement"
                :class="['w-12 h-6 rounded-full transition-all duration-300 relative', form.showAnnouncement ? 'bg-rose-500' : 'bg-slate-200']"
              >
                <div :class="['w-4 h-4 bg-white rounded-full absolute top-1 transition-all duration-300 shadow-sm', form.showAnnouncement ? 'left-7' : 'left-1']"></div>
              </button>
          </div>
        </div>
        
        <div class="p-8 space-y-6" v-if="form.showAnnouncement">
          <div class="space-y-2">
            <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Announcement Text</label>
            <input 
              v-model="form.announcementText" 
              type="text" 
              placeholder="e.g. Free shipping on all orders over $100!"
              class="w-full h-14 px-6 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all font-semibold text-slate-700"
            >
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
              <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Background Color</label>
              <div class="flex items-center gap-4">
                <input type="color" v-model="form.announcementBgColor" class="w-14 h-14 rounded-xl cursor-pointer border-0 p-1 bg-slate-50">
                <input type="text" v-model="form.announcementBgColor" class="flex-grow h-14 px-6 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all font-semibold text-slate-700 uppercase">
              </div>
            </div>
            <div class="space-y-2">
              <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Text Color</label>
              <div class="flex items-center gap-4">
                <input type="color" v-model="form.announcementTextColor" class="w-14 h-14 rounded-xl cursor-pointer border-0 p-1 bg-slate-50">
                <input type="text" v-model="form.announcementTextColor" class="flex-grow h-14 px-6 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all font-semibold text-slate-700 uppercase">
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Footer Settings -->
      <div class="bg-white rounded-[32px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex items-center gap-4">
          <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center">
            <LayoutBottombar class="w-6 h-6" />
          </div>
          <div>
            <h2 class="text-xl font-black text-slate-900">Footer Settings</h2>
            <p class="text-sm font-bold text-slate-500">Configure your website footer text</p>
          </div>
        </div>
        
        <div class="p-8 space-y-6">
          <div class="space-y-2">
            <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">About Shop (Short Description)</label>
            <textarea 
              v-model="form.footerAbout" 
              rows="4" 
              placeholder="We are a premium brand offering the best products in the market..."
              class="w-full p-6 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all font-semibold text-slate-700 resize-none"
            ></textarea>
          </div>
          <div class="space-y-2">
            <label class="text-xs font-black text-slate-400 uppercase tracking-wider ml-1">Copyright Text</label>
            <input 
              v-model="form.footerCopyright" 
              type="text" 
              placeholder="© 2026 YourStore. All rights reserved."
              class="w-full h-14 px-6 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all font-semibold text-slate-700"
            >
          </div>
        </div>
      </div>
      
    </div>

    <!-- Media Library Modal -->
    <AppMediaLibrary
      :show="showMediaModal"
      :multiple="false"
      :type-label="mediaTarget === 'favicon' ? 'Favicon' : 'Lookbook Image'"
      @close="showMediaModal = false"
      @select="handleMediaSelection"
    />
  </div>
</template>

<script setup>
import { ChevronLeft, Save, Megaphone, LayoutList as LayoutBottombar, Globe, Upload, Layout } from 'lucide-vue-next'
import { ref, reactive, onMounted } from 'vue'
import AppMediaLibrary from '~/components/AppMediaLibrary.vue'

definePageMeta({
  middleware: 'auth',
  permissions: 'settings.view'
})

const { $toast } = useNuxtApp()
const { getAll, createItem } = useCrud()
const router = useRouter()

const pending = ref(true)
const saving = ref(false)

const form = reactive({
  seoTitle: '',
  seoDescription: '',
  favicon: null,
  showAnnouncement: true,
  announcementText: 'Free shipping on all orders over $100!',
  announcementBgColor: '#000000',
  announcementTextColor: '#ffffff',
  footerAbout: '',
  footerCopyright: '© 2026 Sellull. All rights reserved.'
})

const lookbookForm = reactive({
  lookbook_status: '1',
  lookbook_image: null,
  lookbook_subtitle: 'Concept Lookbook',
  lookbook_title: 'Sculpting Space <br/>with Light',
  lookbook_description: 'Create a narrative of peace in your home. Our new minimalist series explores the dialogue between industrial precision and organic warmth.',
  feature_1_icon: '🌿',
  feature_1_title: 'Eco Architecture',
  feature_1_desc: '100% sustainable materials.',
  feature_2_icon: '🖐️',
  feature_2_title: 'Master Crafted',
  feature_2_desc: 'Expert artisanal finishes.',
  hotspot_badge: 'Minimalist',
  hotspot_title: 'Velvet Sofa',
  hotspot_price: '$899',
  hotspot_status: 'In Stock',
  button_text: 'Shop The Concept',
  shop_link: '/shop'
})

const newsletterForm = reactive({
  newsletter_status: '1',
  newsletter_title: 'Join the Inner Circle',
  newsletter_description: 'Receive curated design inspiration, early product access, and member-only pricing delivered to your inbox.',
  newsletter_placeholder: 'Enter your email address',
  newsletter_button_text: 'Subscribe',
  newsletter_footer_text: 'Zero Spam. Only Inspiration.'
})

const loadSettings = async () => {
  try {
    pending.value = true
    
    // Load all settings groups in a single batch request
    const response = await getAll('/vendor/settings?groups[]=website_general&groups[]=website_lookbook&groups[]=website_newsletter')
    
    if (response?.data) {
      const allData = response.data

      // Populate General Settings
      if (allData.website_general) {
        const loaded = allData.website_general
        form.seoTitle = loaded.seoTitle || ''
        form.seoDescription = loaded.seoDescription || ''
        form.favicon = loaded.favicon || null
        form.showAnnouncement = loaded.showAnnouncement === 'true' || loaded.showAnnouncement === true || loaded.showAnnouncement === '1' || loaded.showAnnouncement === 1
        form.announcementText = loaded.announcementText || ''
        form.announcementBgColor = loaded.announcementBgColor || '#000000'
        form.announcementTextColor = loaded.announcementTextColor || '#ffffff'
        form.footerAbout = loaded.footerAbout || ''
        form.footerCopyright = loaded.footerCopyright || `© ${new Date().getFullYear()} My Store. All rights reserved.`
      }

      // Populate Lookbook Settings
      if (allData.website_lookbook) {
        const loaded = allData.website_lookbook
        Object.keys(lookbookForm).forEach(key => {
          if (loaded[key] !== undefined) {
            lookbookForm[key] = loaded[key]
          }
        })
      }

      // Populate Newsletter Settings
      if (allData.website_newsletter) {
        const loaded = allData.website_newsletter
        Object.keys(newsletterForm).forEach(key => {
          if (loaded[key] !== undefined) {
            newsletterForm[key] = loaded[key]
          }
        })
      }
    }

  } catch (error) {
    if (error.response?.status !== 404) {
      $toast.error('Failed to load settings')
    }
  } finally {
    pending.value = false
  }
}

const saveSettings = async () => {
  try {
    saving.value = true
    
    // Save all settings groups in a single batch request
    await createItem('/vendor/settings', {
      groups: [
        {
          group: 'website_general',
          settings: form
        },
        {
          group: 'website_lookbook',
          settings: lookbookForm
        },
        {
          group: 'website_newsletter',
          settings: newsletterForm
        }
      ]
    })

    // $toast.success('Settings saved successfully')
    router.push('/vendor/managed-website')
  } catch (error) {
    console.error(error)
    $toast.error('Failed to save settings')
  } finally {
    saving.value = false
  }
}

// Media Library
const showMediaModal = ref(false)
const mediaTarget = ref('favicon')

const openMediaLibrary = (target) => {
  mediaTarget.value = target
  showMediaModal.value = true
}

const handleMediaSelection = (selected) => {
  if (mediaTarget.value === 'favicon') {
    form.favicon = selected.file_url
  } else if (mediaTarget.value === 'lookbook_image') {
    lookbookForm.lookbook_image = selected.file_url
  }
}

onMounted(() => {
  loadSettings()
})
</script>

<style scoped>
/* Any additional custom styles if needed */
</style>
