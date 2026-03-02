<template>
  <div class="page-wrapper">
    <div class="form-container">
      <div class="page-header">
        <button class="back-btn" @click="$router.back()"><ChevronLeft :size="16" /></button>
        <div>
          <h1 class="page-title">Sorting Attributes</h1>
          <p class="page-subtitle">Drag and drop to reorder categories and brands.</p>
        </div>
      </div>

      <div class="form-card">
        <!-- Tabs -->
        <div class="tabs">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            class="tab-btn"
            :class="{ 'tab-btn--active': activeTab === tab.id }"
          >{{ tab.name }}</button>
        </div>

        <div class="sort-body">
          <!-- Info Banner -->
          <div class="info-banner">
            <Info :size="14" class="info-icon" />
            <p>Drag items to reorder. Click <strong>Save Order</strong> when done.</p>
          </div>

          <!-- Categories List -->
          <div v-show="activeTab === 'categories'" class="sort-list">
            <div
              v-for="(category, index) in categories"
              :key="category.id"
              class="sort-item"
              draggable="true"
              @dragstart="startDrag($event, index, 'categories')"
              @drop="onDrop($event, index, 'categories')"
              @dragover.prevent
              @dragenter.prevent
            >
              <div class="sort-item-left">
                <GripVertical :size="16" class="grip-icon" />
                <div class="sort-thumb">
                  <img v-if="category.image" :src="category.image" class="sort-thumb-img" />
                  <ImageIcon v-else :size="13" />
                </div>
                <div>
                  <p class="sort-name">{{ category.name }}</p>
                  <p class="sort-type">Category</p>
                </div>
              </div>
              <span class="sort-pos">{{ index + 1 }}</span>
            </div>
            <div v-if="categories.length === 0" class="sort-empty">
              <LayoutGrid :size="22" />
              <p>No categories to sort</p>
            </div>
          </div>

          <!-- Brands List -->
          <div v-show="activeTab === 'brands'" class="sort-list">
            <div
              v-for="(brand, index) in brands"
              :key="brand.id"
              class="sort-item"
              draggable="true"
              @dragstart="startDrag($event, index, 'brands')"
              @drop="onDrop($event, index, 'brands')"
              @dragover.prevent
              @dragenter.prevent
            >
              <div class="sort-item-left">
                <GripVertical :size="16" class="grip-icon" />
                <div class="sort-thumb">
                  <img v-if="brand.image" :src="brand.image" class="sort-thumb-img" />
                  <ImageIcon v-else :size="13" />
                </div>
                <div>
                  <p class="sort-name">{{ brand.name }}</p>
                  <p class="sort-type">Brand</p>
                </div>
              </div>
              <span class="sort-pos">{{ index + 1 }}</span>
            </div>
            <div v-if="brands.length === 0" class="sort-empty">
              <Award :size="22" />
              <p>No brands to sort</p>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="form-actions">
          <button @click="saveOrder(activeTab)" :disabled="saving" class="btn-submit">
            <Loader2 v-if="saving" :size="15" class="spin" /><Save v-else :size="15" />
            {{ saving ? 'Saving...' : `Save ${activeTab === 'categories' ? 'Category' : 'Brand'} Order` }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { GripVertical, Info, ChevronLeft, LayoutGrid, Award, Image as ImageIcon, Save, Loader2 } from 'lucide-vue-next'
import { toast } from 'vue-sonner'

definePageMeta({ middleware: 'auth', permissions: 'attributes.view' })

const config = useRuntimeConfig(); const auth = useAuthStore()
const { getAll, createItem } = useCrud(); const router = useRouter()
const tabs = [{ id: 'categories', name: 'Categories' }, { id: 'brands', name: 'Brands' }]
const activeTab = ref('categories'); const categories = ref([]); const brands = ref([]); const saving = ref(false)

const startDrag = (event, index, type) => {
  event.dataTransfer.dropEffect = 'move'; event.dataTransfer.effectAllowed = 'move'
  event.dataTransfer.setData('draggedIndex', index); event.dataTransfer.setData('type', type)
}
const onDrop = (event, dropIndex, type) => {
  const draggedIndex = event.dataTransfer.getData('draggedIndex'); const draggedType = event.dataTransfer.getData('type')
  if (draggedType !== type) return
  const oldIndex = parseInt(draggedIndex)
  if (oldIndex === dropIndex) return
  const items = type === 'categories' ? categories : brands
  const itemToMove = items.value[oldIndex]
  items.value.splice(oldIndex, 1); items.value.splice(dropIndex, 0, itemToMove)
}
const fetchData = async () => {
  try {
    const [cats, brnds] = await Promise.all([getAll('/vendor/attributes/categories'), getAll('/vendor/attributes/brands')])
    categories.value = cats?.data || cats || []; brands.value = brnds?.data || brnds || []
  } catch (e) { console.error(e) }
}
fetchData()
const saveOrder = async (type) => {
  saving.value = true
  const list = type === 'categories' ? categories.value : brands.value
  const items = list.map((item, index) => ({ id: item.id, sort_order: index + 1 }))
  try {
    await createItem(`/vendor/attributes/${type}/sort`, items)
    toast.success(`${type.charAt(0).toUpperCase() + type.slice(1)} order saved`)
    router.push('/vendor/attributes/brands')
  } catch (e) { console.error(e); toast.error('Failed to save order') } finally { saving.value = false }
}
</script>

<style scoped>
.page-wrapper{padding:28px 28px 60px;min-height:100vh;background-color:#f8fafc}.dark .page-wrapper{background-color:#090d14}.form-container{max-width:700px}.page-header{display:flex;align-items:center;gap:14px;margin-bottom:20px}.back-btn{display:flex;align-items:center;justify-content:center;width:32px;height:32px;border:1px solid #e5e7eb;border-radius:7px;background:#fff;color:#6b7280;cursor:pointer;flex-shrink:0;transition:background-color .15s}.back-btn:hover{background-color:#f3f4f6;color:#111827}.dark .back-btn{background-color:#1a1f2e;border-color:#1e2330;color:#6b7280}.page-title{font-size:18px;font-weight:700;color:#111827;margin:0}.dark .page-title{color:#f9fafb}.page-subtitle{font-size:12.5px;color:#9ca3af;margin:2px 0 0}.form-card{background:#fff;border:1px solid #e5e7eb;border-radius:10px;overflow:hidden}.dark .form-card{background-color:#161b27;border-color:#1e2330}

/* Tabs */
.tabs{display:flex;gap:4px;padding:8px 10px;border-bottom:1px solid #e5e7eb;background-color:#f9fafb}.dark .tabs{border-bottom-color:#1e2330;background-color:#1a1f2e}
.tab-btn{padding:7px 16px;font-size:13px;font-weight:500;color:#6b7280;border:1px solid transparent;border-radius:7px;background:transparent;cursor:pointer;transition:background-color .15s,color .15s,border-color .15s}.tab-btn:hover{color:#374151;background-color:#f3f4f6}.dark .tab-btn:hover{color:#d1d5db;background-color:#252b3b}
.tab-btn--active{background-color:#fff;color:#4f46e5;border-color:#e5e7eb;font-weight:600}.dark .tab-btn--active{background-color:#252b3b;color:#818cf8;border-color:#1e2330}

/* Sort Body */
.sort-body{padding:16px 20px}

/* Info Banner */
.info-banner{display:flex;align-items:center;gap:8px;padding:10px 14px;background-color:#eff6ff;border:1px solid #c7d2fe;border-radius:7px;font-size:12.5px;color:#4338ca;margin-bottom:16px}.dark .info-banner{background-color:rgba(79,70,229,.1);border-color:rgba(79,70,229,.2);color:#818cf8}
.info-icon{color:#4f46e5;flex-shrink:0}.dark .info-icon{color:#818cf8}

/* Sort List */
.sort-list{display:flex;flex-direction:column;gap:6px}
.sort-item{display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background-color:#fff;border:1px solid #e5e7eb;border-radius:8px;cursor:grab;transition:box-shadow .15s,border-color .15s;user-select:none}.sort-item:hover{border-color:#c7d2fe;box-shadow:0 2px 8px rgba(79,70,229,.08)}.sort-item:active{cursor:grabbing}.dark .sort-item{background-color:#1e2330;border-color:#252b3b}.dark .sort-item:hover{border-color:#4f46e5}
.sort-item-left{display:flex;align-items:center;gap:10px}
.grip-icon{color:#d1d5db;flex-shrink:0}.dark .grip-icon{color:#374151}
.sort-thumb{width:30px;height:30px;border-radius:6px;background-color:#f3f4f6;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;color:#9ca3af;overflow:hidden;flex-shrink:0}.dark .sort-thumb{background-color:#252b3b;border-color:#1e2330}
.sort-thumb-img{width:100%;height:100%;object-fit:cover}
.sort-name{font-size:13px;font-weight:600;color:#111827;margin:0}.dark .sort-name{color:#f9fafb}
.sort-type{font-size:11px;color:#9ca3af;margin:1px 0 0}
.sort-pos{font-size:11px;font-weight:600;color:#9ca3af;background-color:#f3f4f6;border:1px solid #e5e7eb;border-radius:5px;padding:2px 7px}.dark .sort-pos{background-color:#252b3b;border-color:#1e2330}

/* Empty */
.sort-empty{display:flex;flex-direction:column;align-items:center;gap:8px;padding:40px 20px;color:#9ca3af;font-size:13px;text-align:center}
.sort-empty p{margin:0}

/* Actions */
.form-actions{display:flex;align-items:center;justify-content:flex-end;gap:8px;padding:14px 20px;background-color:#f9fafb;border-top:1px solid #f3f4f6}.dark .form-actions{background-color:#1a1f2e;border-top-color:#1e2330}
.btn-submit{display:flex;align-items:center;gap:6px;padding:8px 18px;font-size:13px;font-weight:600;color:#fff;background-color:#4f46e5;border:none;border-radius:7px;cursor:pointer;transition:background-color .15s}.btn-submit:hover{background-color:#4338ca}.btn-submit:disabled{opacity:.6;cursor:not-allowed}
.spin{animation:spin 1s linear infinite}@keyframes spin{to{transform:rotate(360deg)}}
</style>
