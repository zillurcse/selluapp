<template>
  <div class="page-wrapper">
    <div class="form-container">
      <div class="page-header">
        <button class="back-btn" @click="$router.push('/vendor/attributes/units')"><ChevronLeft :size="16" /></button>
        <div>
          <h1 class="page-title">Create Unit</h1>
          <p class="page-subtitle">Add a new unit of measurement.</p>
        </div>
      </div>

      <form @submit.prevent="createUnit" class="form-card">
        <div class="form-section">
          <div class="section-head">
            <span class="section-icon section-icon--indigo"><LayoutGrid :size="14" /></span>
            <h3 class="section-title">Unit Details</h3>
          </div>
          <div class="form-grid">
            <div class="field">
              <label class="field-label">Unit Name <span class="required">*</span></label>
              <input v-model="form.name" type="text" placeholder="e.g. Kilogram, Litre" class="field-input" required />
            </div>
            <div class="field">
              <label class="field-label">Symbol <span class="required">*</span></label>
              <input v-model="form.symbol" type="text" placeholder="e.g. kg, L" class="field-input" required />
            </div>
          </div>
          <div class="field">
            <label class="field-label">Description</label>
            <textarea v-model="form.description" rows="3" placeholder="Brief unit description..." class="field-input field-textarea"></textarea>
          </div>
        </div>

        <div class="form-section">
          <div class="toggle-row">
            <div class="toggle-info">
              <p class="toggle-label">Active</p>
              <p class="toggle-desc">Determine if this unit is active and selectable.</p>
            </div>
            <button type="button" @click="form.is_active = !form.is_active" class="toggle" :class="{ 'toggle--on': form.is_active }">
              <span class="toggle-thumb"></span>
            </button>
          </div>
        </div>

        <div class="form-actions">
          <button type="button" @click="$router.push('/vendor/attributes/units')" class="btn-cancel">Cancel</button>
          <button type="submit" :disabled="isLoading" class="btn-submit">
            <Loader2 v-if="isLoading" :size="15" class="spin" /><Save v-else :size="15" />
            Create Unit
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ChevronLeft, LayoutGrid, Save, Loader2 } from 'lucide-vue-next'

definePageMeta({ middleware: 'auth', permissions: 'units.create' })

const router = useRouter(); const { createItem } = useCrud()
const isLoading = ref(false)
const form = reactive({ name: '', symbol: '', description: '', is_active: true })

const createUnit = async () => {
  isLoading.value = true
  try { await createItem('/vendor/attributes/units', form); router.push('/vendor/attributes/units') }
  catch (e) { console.error(e) } finally { isLoading.value = false }
}
</script>

<style scoped>
.page-wrapper{padding:28px 28px 60px;min-height:100vh;background-color:#f8fafc}.dark .page-wrapper{background-color:#090d14}.form-container{max-width:800px}.page-header{display:flex;align-items:center;gap:14px;margin-bottom:20px}.back-btn{display:flex;align-items:center;justify-content:center;width:32px;height:32px;border:1px solid #e5e7eb;border-radius:7px;background:#fff;color:#6b7280;cursor:pointer;flex-shrink:0;transition:background-color .15s}.back-btn:hover{background-color:#f3f4f6;color:#111827}.dark .back-btn{background-color:#1a1f2e;border-color:#1e2330;color:#6b7280}.page-title{font-size:18px;font-weight:700;color:#111827;margin:0}.dark .page-title{color:#f9fafb}.page-subtitle{font-size:12.5px;color:#9ca3af;margin:2px 0 0}.form-card{background:#fff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden}.dark .form-card{background-color:#161b27;border-color:#1e2330}.form-section{padding:20px 24px;border-bottom:1px solid #f3f4f6}.dark .form-section{border-bottom-color:#1e2330}.section-head{display:flex;align-items:center;gap:8px;margin-bottom:16px}.section-icon{display:flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:6px;flex-shrink:0}.section-icon--indigo{background-color:#eff6ff;color:#4f46e5}.dark .section-icon--indigo{background-color:rgba(79,70,229,.12);color:#818cf8}.section-title{font-size:13.5px;font-weight:600;color:#111827;margin:0}.dark .section-title{color:#f9fafb}.form-grid{display:grid;grid-template-columns:1fr;gap:14px;margin-bottom:14px}@media(min-width:640px){.form-grid{grid-template-columns:1fr 1fr}}.field{display:flex;flex-direction:column;gap:5px}.field-label{font-size:12.5px;font-weight:600;color:#374151}.dark .field-label{color:#d1d5db}.required{color:#ef4444}.field-input{width:100%;padding:9px 12px;font-size:13.5px;color:#111827;background-color:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;outline:none;transition:border-color .15s,box-shadow .15s;box-sizing:border-box}.field-input:focus{border-color:#4f46e5;box-shadow:0 0 0 3px rgba(79,70,229,.08);background-color:#fff}.dark .field-input{background-color:#1a1f2e;border-color:#1e2330;color:#f9fafb}.dark .field-input:focus{border-color:#4f46e5;background-color:#1e2330}.field-textarea{resize:vertical;min-height:80px}.toggle-row{display:flex;align-items:center;justify-content:space-between;gap:16px}.toggle-info{flex:1}.toggle-label{font-size:13.5px;font-weight:600;color:#111827;margin:0}.dark .toggle-label{color:#f9fafb}.toggle-desc{font-size:12px;color:#9ca3af;margin:2px 0 0}.toggle{position:relative;width:40px;height:22px;border-radius:11px;border:none;background-color:#d1d5db;cursor:pointer;transition:background-color .2s;flex-shrink:0}.toggle--on{background-color:#4f46e5}.dark .toggle{background-color:#374151}.toggle-thumb{position:absolute;top:3px;left:3px;width:16px;height:16px;border-radius:50%;background-color:#fff;transition:transform .2s;display:block;box-shadow:0 1px 3px rgba(0,0,0,.2)}.toggle--on .toggle-thumb{transform:translateX(18px)}.form-actions{display:flex;align-items:center;justify-content:flex-end;gap:8px;padding:14px 24px;background-color:#f9fafb;border-top:1px solid #f3f4f6}.dark .form-actions{background-color:#1a1f2e;border-top-color:#1e2330}.btn-cancel{padding:8px 16px;font-size:13px;font-weight:500;color:#6b7280;background-color:#fff;border:1px solid #e5e7eb;border-radius:7px;cursor:pointer}.btn-cancel:hover{background-color:#f3f4f6}.dark .btn-cancel{background-color:#1e2330;border-color:#252b3b;color:#9ca3af}.btn-submit{display:flex;align-items:center;gap:6px;padding:8px 18px;font-size:13px;font-weight:600;color:#fff;background-color:#4f46e5;border:none;border-radius:7px;cursor:pointer;transition:background-color .15s}.btn-submit:hover{background-color:#4338ca}.btn-submit:disabled{opacity:.6;cursor:not-allowed}.spin{animation:spin 1s linear infinite}@keyframes spin{to{transform:rotate(360deg)}}
</style>
