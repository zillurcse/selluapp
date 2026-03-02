<template>
  <div class="page-wrapper">
    <div class="form-container">

      <!-- Page Header -->
      <div class="page-header">
        <button class="back-btn" @click="$router.push('/vendor/attributes/categories')">
          <ChevronLeft :size="16" />
        </button>
        <div>
          <h1 class="page-title">Create Category</h1>
          <p class="page-subtitle">Add a new category to organize your products.</p>
        </div>
      </div>

      <!-- Form Card -->
      <form @submit.prevent="createCategory" class="form-card">

        <!-- Section: Basic Info -->
        <div class="form-section">
          <div class="section-head">
            <span class="section-icon section-icon--indigo"><LayoutGrid :size="14" /></span>
            <h3 class="section-title">Basic Information</h3>
          </div>
          <div class="form-grid">
            <div class="field">
              <label class="field-label">Category Name <span class="required">*</span></label>
              <input v-model="form.name" type="text" placeholder="e.g. Electronics" class="field-input" required />
            </div>
            <div class="field">
              <label class="field-label">Parent Category</label>
              <div class="select-wrap">
                <select v-model="form.parent_id" class="field-input field-select">
                  <option :value="null">— None (Top Level) —</option>
                  <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
                <ChevronDown :size="14" class="select-arrow" />
              </div>
            </div>
          </div>
          <div class="field">
            <label class="field-label">Description</label>
            <textarea v-model="form.description" rows="3" placeholder="Brief description for SEO..." class="field-input field-textarea"></textarea>
          </div>
        </div>

        <!-- Section: Media -->
        <div class="form-section">
          <div class="section-head">
            <span class="section-icon section-icon--amber"><ImageIcon :size="14" /></span>
            <h3 class="section-title">Media Assets</h3>
          </div>
          <div class="form-grid">
            <div class="field">
              <label class="field-label">Category Icon <span class="field-hint">80×80px recommended</span></label>
              <div class="upload-box upload-box--square" @click="openMediaLibrary('icon')">
                <img v-if="iconPreview" :src="iconPreview" class="upload-preview" />
                <div v-else class="upload-placeholder">
                  <Upload :size="18" class="upload-icon" />
                  <span>Choose Icon</span>
                </div>
                <div v-if="iconPreview" class="upload-change-overlay">
                  <ImageIcon :size="16" />
                  <span>Change</span>
                </div>
              </div>
            </div>
            <div class="field">
              <label class="field-label">Category Banner <span class="field-hint">300×180px recommended</span></label>
              <div class="upload-box upload-box--wide" @click="openMediaLibrary('banner')">
                <img v-if="imagePreview" :src="imagePreview" class="upload-preview" />
                <div v-else class="upload-placeholder">
                  <ImageIcon :size="18" class="upload-icon" />
                  <span>Choose Banner</span>
                </div>
                <div v-if="imagePreview" class="upload-change-overlay">
                  <ImageIcon :size="16" />
                  <span>Change</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Section: Settings -->
        <div class="form-section">
          <div class="toggle-row">
            <div class="toggle-info">
              <p class="toggle-label">Active</p>
              <p class="toggle-desc">Show this category in the storefront.</p>
            </div>
            <button type="button" @click="form.is_active = !form.is_active" class="toggle" :class="{ 'toggle--on': form.is_active }">
              <span class="toggle-thumb"></span>
            </button>
          </div>
        </div>

        <!-- Actions -->
        <div class="form-actions">
          <button type="button" @click="$router.push('/vendor/attributes/categories')" class="btn-cancel">Cancel</button>
          <button type="submit" :disabled="isLoading" class="btn-submit">
            <Loader2 v-if="isLoading" :size="15" class="spin" />
            <Save v-else :size="15" />
            Create Category
          </button>
        </div>
      </form>
    </div>

    <!-- Media Library -->
    <AppMediaLibrary
      :show="showMediaModal"
      :multiple="false"
      type-label="Image"
      @close="showMediaModal = false"
      @select="handleMediaSelection"
    />
  </div>
</template>

<script setup>
import { ChevronLeft, ChevronDown, LayoutGrid, Image as ImageIcon, Upload, Save, Loader2 } from 'lucide-vue-next'
import AppMediaLibrary from '~/components/AppMediaLibrary.vue'

definePageMeta({ middleware: 'auth', permissions: 'categories.create' })

const auth = useAuthStore()
const router = useRouter()
const config = useRuntimeConfig()
const { createItem, getAll } = useCrud()

const isLoading = ref(false)
const form = reactive({ name: '', description: '', parent_id: null, is_active: true, icon: null, image: null })
const iconPreview = ref(null)
const imagePreview = ref(null)
const categories = ref([])

// Media Library
const showMediaModal = ref(false)
const mediaModalMode = ref('icon') // 'icon' | 'banner'

const openMediaLibrary = (mode) => {
  mediaModalMode.value = mode
  showMediaModal.value = true
}

const handleMediaSelection = (selected) => {
  if (mediaModalMode.value === 'icon') {
    form.icon = selected.file_url
    iconPreview.value = selected.file_url
  } else if (mediaModalMode.value === 'banner') {
    form.image = selected.file_url
    imagePreview.value = selected.file_url
  }
}

const fetchCategories = async () => {
  const res = await getAll('/vendor/attributes/categories').catch(() => null)
  categories.value = res?.data || res || []
}
fetchCategories()

const createCategory = async () => {
  isLoading.value = true
  try {
    const fd = new FormData()
    fd.append('name', form.name)
    if (form.description) fd.append('description', form.description)
    if (form.parent_id) fd.append('parent_id', form.parent_id)
    fd.append('is_active', form.is_active ? '1' : '0')
    if (form.icon) fd.append('icon', form.icon)
    if (form.image) fd.append('image', form.image)
    await createItem('/vendor/attributes/categories', fd)
    router.push('/vendor/attributes/categories')
  } catch (e) { console.error(e) } finally { isLoading.value = false }
}
</script>

<style scoped>
.page-wrapper { padding: 28px 28px 60px; min-height: 100vh; background-color: #f8fafc; }
.dark .page-wrapper { background-color: #090d14; }
.form-container { max-width: 800px; }
.page-header { display: flex; align-items: center; gap: 14px; margin-bottom: 20px; }
.back-btn { display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border: 1px solid #e5e7eb; border-radius: 7px; background: #fff; color: #6b7280; cursor: pointer; flex-shrink: 0; transition: background-color .15s, color .15s; }
.back-btn:hover { background-color: #f3f4f6; color: #111827; }
.dark .back-btn { background-color: #1a1f2e; border-color: #1e2330; color: #6b7280; }
.dark .back-btn:hover { background-color: #252b3b; color: #d1d5db; }
.page-title { font-size: 18px; font-weight: 700; color: #111827; margin: 0; }
.dark .page-title { color: #f9fafb; }
.page-subtitle { font-size: 12.5px; color: #9ca3af; margin: 2px 0 0; }
.form-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
.dark .form-card { background-color: #161b27; border-color: #1e2330; }
.form-section { padding: 20px 24px; border-bottom: 1px solid #f3f4f6; }
.dark .form-section { border-bottom-color: #1e2330; }
.section-head { display: flex; align-items: center; gap: 8px; margin-bottom: 16px; }
.section-icon { display: flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 6px; flex-shrink: 0; }
.section-icon--indigo { background-color: #eff6ff; color: #4f46e5; }
.section-icon--amber { background-color: #fffbeb; color: #d97706; }
.dark .section-icon--indigo { background-color: rgba(79,70,229,.12); color: #818cf8; }
.dark .section-icon--amber { background-color: rgba(217,119,6,.12); color: #fbbf24; }
.section-title { font-size: 13.5px; font-weight: 600; color: #111827; margin: 0; }
.dark .section-title { color: #f9fafb; }
.form-grid { display: grid; grid-template-columns: 1fr; gap: 14px; margin-bottom: 14px; }
@media (min-width: 640px) { .form-grid { grid-template-columns: 1fr 1fr; } }
.field { display: flex; flex-direction: column; gap: 5px; }
.field-label { font-size: 12.5px; font-weight: 600; color: #374151; }
.dark .field-label { color: #d1d5db; }
.required { color: #ef4444; }
.field-hint { font-size: 11px; font-weight: 400; color: #9ca3af; margin-left: 4px; }
.field-input { width: 100%; padding: 9px 12px; font-size: 13.5px; color: #111827; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; outline: none; transition: border-color .15s, box-shadow .15s; box-sizing: border-box; }
.field-input:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,.08); background-color: #fff; }
.dark .field-input { background-color: #1a1f2e; border-color: #1e2330; color: #f9fafb; }
.dark .field-input:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79,70,229,.12); background-color: #1e2330; }
.field-textarea { resize: vertical; min-height: 80px; }
.select-wrap { position: relative; }
.field-select { appearance: none; padding-right: 36px; cursor: pointer; }
.select-arrow { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; pointer-events: none; }
.upload-box { position: relative; border: 1.5px dashed #e5e7eb; border-radius: 8px; background-color: #f9fafb; display: flex; align-items: center; justify-content: center; overflow: hidden; cursor: pointer; transition: border-color .15s, background-color .15s; }
.upload-box:hover { border-color: #4f46e5; background-color: #f5f3ff; }
.dark .upload-box { background-color: #1a1f2e; border-color: #1e2330; }
.dark .upload-box:hover { border-color: #4f46e5; }
.upload-box--square { height: 140px; max-width: 140px; }
.upload-box--wide { height: 140px; }
.upload-preview { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: 1; }
.upload-placeholder { display: flex; flex-direction: column; align-items: center; gap: 6px; color: #9ca3af; font-size: 12px; font-weight: 500; }
.upload-icon { color: #9ca3af; }
.upload-change-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.45); display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px; color: #fff; font-size: 11px; font-weight: 600; opacity: 0; transition: opacity .15s; z-index: 2; }
.upload-box:hover .upload-change-overlay { opacity: 1; }
.toggle-row { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
.toggle-info { flex: 1; }
.toggle-label { font-size: 13.5px; font-weight: 600; color: #111827; margin: 0; }
.dark .toggle-label { color: #f9fafb; }
.toggle-desc { font-size: 12px; color: #9ca3af; margin: 2px 0 0; }
.toggle { position: relative; width: 40px; height: 22px; border-radius: 11px; border: none; background-color: #d1d5db; cursor: pointer; transition: background-color .2s; flex-shrink: 0; }
.toggle--on { background-color: #4f46e5; }
.dark .toggle { background-color: #374151; }
.toggle-thumb { position: absolute; top: 3px; left: 3px; width: 16px; height: 16px; border-radius: 50%; background-color: #fff; transition: transform .2s; display: block; box-shadow: 0 1px 3px rgba(0,0,0,.2); }
.toggle--on .toggle-thumb { transform: translateX(18px); }
.form-actions { display: flex; align-items: center; justify-content: flex-end; gap: 8px; padding: 14px 24px; background-color: #f9fafb; border-top: 1px solid #f3f4f6; }
.dark .form-actions { background-color: #1a1f2e; border-top-color: #1e2330; }
.btn-cancel { padding: 8px 16px; font-size: 13px; font-weight: 500; color: #6b7280; background-color: #fff; border: 1px solid #e5e7eb; border-radius: 7px; cursor: pointer; transition: background-color .15s; }
.btn-cancel:hover { background-color: #f3f4f6; color: #374151; }
.dark .btn-cancel { background-color: #1e2330; border-color: #252b3b; color: #9ca3af; }
.dark .btn-cancel:hover { background-color: #252b3b; color: #d1d5db; }
.btn-submit { display: flex; align-items: center; gap: 6px; padding: 8px 18px; font-size: 13px; font-weight: 600; color: #fff; background-color: #4f46e5; border: none; border-radius: 7px; cursor: pointer; transition: background-color .15s; }
.btn-submit:hover { background-color: #4338ca; }
.btn-submit:disabled { opacity: .6; cursor: not-allowed; }
.spin { animation: spin 1s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
</style>
