<template>
  <div class="page-wrapper">

    <!-- Page Header -->
    <div class="page-header">
      <div class="page-header-left">
        <button class="back-btn" @click="$router.back()">
          <ChevronLeft :size="16" />
        </button>
        <div>
          <h1 class="page-title">Categories</h1>
          <p class="page-subtitle">Organize your products into a structured hierarchy.</p>
        </div>
      </div>
      <NuxtLink to="/vendor/attributes/categories/create" class="btn-primary">
        <Plus :size="15" />
        Add Category
      </NuxtLink>
    </div>

    <!-- Table Card -->
    <div class="table-card">
      <div class="table-scroll">
        <table class="data-table">
          <thead>
            <tr>
              <th>Category Name</th>
              <th>Slug</th>
              <th>Status</th>
              <th class="col-actions">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="category in hierarchicalCategories" :key="category.id">
              <td>
                <div class="cell-entity">
                  <!-- Hierarchy indent -->
                  <div v-if="category.depth > 0" class="depth-indent">
                    <span v-for="i in category.depth" :key="i" class="depth-dash"></span>
                    <CornerDownRight :size="13" class="depth-icon" />
                  </div>
                  <div class="entity-thumb">
                    <img v-if="category.image" :src="category.image" alt="" />
                    <ImageIcon v-else :size="14" />
                  </div>
                  <div>
                    <div class="entity-name">{{ category.name }}</div>
                    <div v-if="category.description" class="entity-sub">{{ category.description }}</div>
                  </div>
                </div>
              </td>
              <td><span class="badge-slug">{{ category.slug }}</span></td>
              <td>
                <span class="status-badge" :class="category.is_active ? 'status-active' : 'status-inactive'">
                  <span class="status-dot"></span>
                  {{ category.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="col-actions">
                <div class="row-actions">
                  <NuxtLink :to="`/vendor/attributes/categories/${category.id}`" class="action-btn action-btn--edit" title="Edit">
                    <Pencil :size="14" />
                  </NuxtLink>
                  <button @click="deleteCategory(category.id)" class="action-btn action-btn--delete" title="Delete">
                    <Trash2 :size="14" />
                  </button>
                </div>
              </td>
            </tr>

            <!-- Empty state -->
            <tr v-if="categories.length === 0">
              <td colspan="4">
                <div class="empty-state">
                  <div class="empty-icon"><FolderOpen :size="24" /></div>
                  <p class="empty-label">No categories found.</p>
                  <NuxtLink to="/vendor/attributes/categories/create" class="btn-primary btn-sm">
                    <Plus :size="13" /> Add your first category
                  </NuxtLink>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</template>

<script setup>
import {
  ChevronLeft,
  Plus,
  Pencil,
  Trash2,
  Image as ImageIcon,
  FolderOpen,
  CornerDownRight,
} from 'lucide-vue-next'

definePageMeta({
  middleware: 'auth',
  permissions: 'categories.view',
})

const config = useRuntimeConfig()
const auth = useAuthStore()
const categories = ref([])
const { deleteItem, getAll } = useCrud()

const hierarchicalCategories = computed(() => {
  const items = []
  const format = (parentId = null, depth = 0) => {
    const children = categories.value.filter(c => c.parent_id === parentId)
    children.sort((a, b) => (a.sort_order || 0) - (b.sort_order || 0))
    children.forEach(cat => {
      items.push({ ...cat, depth, displayName: '-'.repeat(depth + 1) + ' ' + cat.name })
      format(cat.id, depth + 1)
    })
  }
  format(null, 0)
  return items
})

const fetchCategories = async () => {
  try {
    const res = await getAll('/vendor/attributes/categories')
    categories.value = res?.data || res || []
  } catch (e) {
    console.error('Failed to fetch categories')
  }
}

await fetchCategories()

const deleteCategory = async (id) => {
  if (!confirm('Are you sure you want to delete this category?')) return
  try {
    await $fetch(`${config.public.apiBase}/admin/categories/${id}`, {
      method: 'DELETE',
      headers: { Authorization: `Bearer ${auth.token}` },
    })
    await fetchCategories()
  } catch (error) {
    console.error('Failed to delete category', error)
    alert('Failed to delete category')
  }
}
</script>

<style scoped>
/* ─── Layout ─────────────────────────────────────────────────────────────────── */
.page-wrapper {
  padding: 28px 28px 40px;
  min-height: 100vh;
  background-color: #f8fafc;
}
.dark .page-wrapper { background-color: #090d14; }

/* ─── Page Header ────────────────────────────────────────────────────────────── */
.page-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 14px;
  margin-bottom: 20px;
}

.page-header-left {
  display: flex;
  align-items: center;
  gap: 14px;
}

.back-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border: 1px solid #e5e7eb;
  border-radius: 7px;
  background-color: #fff;
  color: #6b7280;
  cursor: pointer;
  flex-shrink: 0;
  transition: background-color 0.15s, color 0.15s;
}
.back-btn:hover { background-color: #f3f4f6; color: #111827; }
.dark .back-btn { background-color: #1a1f2e; border-color: #1e2330; color: #6b7280; }
.dark .back-btn:hover { background-color: #252b3b; color: #d1d5db; }

.page-title {
  font-size: 18px;
  font-weight: 700;
  color: #111827;
  margin: 0;
  line-height: 1.2;
}
.dark .page-title { color: #f9fafb; }

.page-subtitle {
  font-size: 12.5px;
  color: #9ca3af;
  margin: 2px 0 0;
}

/* ─── Buttons ────────────────────────────────────────────────────────────────── */
.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  font-size: 13px;
  font-weight: 600;
  color: #fff;
  background-color: #4f46e5;
  border: none;
  border-radius: 8px;
  text-decoration: none;
  cursor: pointer;
  transition: background-color 0.15s;
  white-space: nowrap;
}
.btn-primary:hover { background-color: #4338ca; }
.btn-sm { padding: 6px 12px; font-size: 12px; }

/* ─── Table Card ─────────────────────────────────────────────────────────────── */
.table-card {
  background-color: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  overflow: hidden;
}
.dark .table-card { background-color: #161b27; border-color: #1e2330; }

.table-scroll { overflow-x: auto; }

/* ─── Table ──────────────────────────────────────────────────────────────────── */
.data-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

.data-table thead tr {
  background-color: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
}
.dark .data-table thead tr { background-color: #1a1f2e; border-bottom-color: #1e2330; }

.data-table th {
  padding: 11px 16px;
  font-size: 11px;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  white-space: nowrap;
}
.dark .data-table th { color: #4b5563; }

.data-table tbody tr {
  border-bottom: 1px solid #f3f4f6;
  transition: background-color 0.12s;
}
.dark .data-table tbody tr { border-bottom-color: #1e2330; }
.data-table tbody tr:last-child { border-bottom: none; }
.data-table tbody tr:hover { background-color: #f9fafb; }
.dark .data-table tbody tr:hover { background-color: #1a1f2e; }

.data-table td {
  padding: 12px 16px;
  font-size: 13px;
  color: #374151;
  white-space: nowrap;
}
.dark .data-table td { color: #9ca3af; }

.col-actions { text-align: right; }

/* ─── Cell: Entity ───────────────────────────────────────────────────────────── */
.cell-entity {
  display: flex;
  align-items: center;
  gap: 10px;
}

.depth-indent {
  display: flex;
  align-items: center;
  gap: 2px;
}
.depth-dash {
  display: inline-block;
  width: 10px;
  height: 1px;
  background-color: #d1d5db;
}
.dark .depth-dash { background-color: #374151; }
.depth-icon { color: #9ca3af; }

.entity-thumb {
  width: 32px;
  height: 32px;
  border-radius: 7px;
  background-color: #f3f4f6;
  border: 1px solid #e5e7eb;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
  flex-shrink: 0;
}
.dark .entity-thumb { background-color: #1e2330; border-color: #252b3b; }
.entity-thumb img { width: 100%; height: 100%; object-fit: cover; }

.entity-name {
  font-size: 13px;
  font-weight: 600;
  color: #111827;
}
.dark .entity-name { color: #f9fafb; }

.entity-sub {
  font-size: 11px;
  color: #9ca3af;
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* ─── Badges ─────────────────────────────────────────────────────────────────── */
.badge-slug {
  display: inline-block;
  padding: 2px 8px;
  font-size: 11.5px;
  font-weight: 500;
  color: #6b7280;
  background-color: #f3f4f6;
  border-radius: 5px;
  font-family: ui-monospace, monospace;
}
.dark .badge-slug { background-color: #1e2330; color: #6b7280; }

.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  padding: 3px 9px;
  font-size: 11px;
  font-weight: 600;
  border-radius: 5px;
  border: 1px solid transparent;
}
.status-active {
  background-color: #ecfdf5;
  color: #059669;
  border-color: #a7f3d0;
}
.status-inactive {
  background-color: #fef2f2;
  color: #dc2626;
  border-color: #fecaca;
}
.dark .status-active { background-color: rgba(5,150,105,0.1); border-color: rgba(5,150,105,0.2); color: #34d399; }
.dark .status-inactive { background-color: rgba(220,38,38,0.1); border-color: rgba(220,38,38,0.2); color: #f87171; }

.status-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background-color: currentColor;
  flex-shrink: 0;
}

/* ─── Row Actions ────────────────────────────────────────────────────────────── */
.row-actions {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 4px;
  opacity: 0;
  transition: opacity 0.2s ease-in-out;
}

.data-table tbody tr:hover .row-actions {
  opacity: 1;
}

.action-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
  border-radius: 6px;
  border: 1px solid transparent;
  cursor: pointer;
  transition: background-color 0.15s, color 0.15s;
  text-decoration: none;
}

.action-btn--edit {
  background-color: #eff6ff;
  color: #3b82f6;
  border-color: #bfdbfe;
}
.action-btn--edit:hover { background-color: #3b82f6; color: #fff; border-color: transparent; }
.dark .action-btn--edit { background-color: rgba(59,130,246,0.1); border-color: rgba(59,130,246,0.2); color: #60a5fa; }
.dark .action-btn--edit:hover { background-color: #3b82f6; color: #fff; border-color: transparent; }

.action-btn--delete {
  background-color: #fef2f2;
  color: #ef4444;
  border-color: #fecaca;
}
.action-btn--delete:hover { background-color: #ef4444; color: #fff; border-color: transparent; }
.dark .action-btn--delete { background-color: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.2); color: #f87171; }
.dark .action-btn--delete:hover { background-color: #ef4444; color: #fff; border-color: transparent; }

/* ─── Empty State ────────────────────────────────────────────────────────────── */
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 12px;
  padding: 60px 20px;
  text-align: center;
}

.empty-icon {
  width: 48px;
  height: 48px;
  border-radius: 10px;
  background-color: #f3f4f6;
  border: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
}
.dark .empty-icon { background-color: #1e2330; border-color: #252b3b; }

.empty-label {
  font-size: 13px;
  font-weight: 500;
  color: #6b7280;
  margin: 0;
}
</style>
