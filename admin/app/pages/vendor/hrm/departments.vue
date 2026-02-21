<template>
  <div class="p-10 bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Departments</h1>
        <p class="text-gray-500 dark:text-slate-400 mt-1">Organize your team into departments.</p>
      </div>
      <button @click="openDrawer()" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-transparent rounded-xl font-semibold text-sm text-white hover:bg-gray-800 transition-all shadow-sm">
        <Plus class="w-4 h-4 mr-2" />Add Department
      </button>
    </div>

    <!-- Loading skeletons -->
    <div v-if="loading" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div v-for="i in 3" :key="i" class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm animate-pulse">
        <div class="w-12 h-12 rounded-xl bg-gray-200 mb-4"></div>
        <div class="h-4 bg-gray-200 rounded w-2/3 mb-2"></div>
        <div class="h-3 bg-gray-200 rounded w-full mb-6"></div>
        <div class="h-3 bg-gray-200 rounded w-1/3"></div>
      </div>
    </div>

    <!-- Empty state -->
    <div v-else-if="!departments.length" class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm p-12 text-center">
      <Building2 class="w-12 h-12 mx-auto mb-3 text-gray-300" />
      <p class="text-gray-500">No departments yet. Create your first department!</p>
    </div>

    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div v-for="dept in departments" :key="dept.id" class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-all group">
        <div class="flex items-start justify-between mb-4">
          <div class="p-3 rounded-xl bg-indigo-50 text-indigo-600">
            <Building2 class="w-6 h-6" />
          </div>
          <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
            <button @click="openDrawer(dept)" class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all">
              <Pencil class="w-4 h-4" />
            </button>
            <button @click="confirmDelete(dept)" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
              <Trash2 class="w-4 h-4" />
            </button>
          </div>
        </div>
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ dept.name }}</h3>
        <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">{{ dept.description || 'No description.' }}</p>
        <div class="mt-6 pt-6 border-t border-gray-50 dark:border-slate-800 flex items-center justify-between text-sm">
          <div class="flex items-center gap-2 text-gray-500 dark:text-slate-400">
            <Users class="w-4 h-4" />
            <span>{{ dept.employees_count ?? 0 }} Employees</span>
          </div>
          <span :class="dept.status === 'active' ? 'text-green-600 bg-green-50 px-2 py-0.5 rounded text-xs font-bold uppercase' : 'text-gray-400 bg-gray-50 px-2 py-0.5 rounded text-xs font-bold uppercase'">
            {{ dept.status }}
          </span>
        </div>
      </div>
    </div>

    <!-- Drawer -->
    <div v-if="drawerOpen" class="fixed inset-0 z-[60] overflow-hidden">
      <div class="absolute inset-0 bg-black/20 backdrop-blur-sm" @click="drawerOpen = false"></div>
      <div class="absolute inset-y-0 right-0 max-w-md w-full flex">
        <div class="relative w-full bg-white dark:bg-slate-900 shadow-2xl flex flex-col h-full animate-slide-left">
          <div class="px-6 py-4 border-b border-gray-100 dark:border-slate-800 flex items-center justify-between bg-gray-50/50 dark:bg-slate-800/50">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ editing ? 'Edit Department' : 'Add Department' }}</h2>
            <button @click="drawerOpen = false" class="p-2 hover:bg-gray-200 rounded-full transition-colors text-gray-400">
              <X class="w-5 h-5" />
            </button>
          </div>
          <div class="flex-1 p-6 space-y-5">
            <div v-if="formError" class="bg-red-50 border border-red-100 text-red-600 text-sm px-4 py-3 rounded-xl">{{ formError }}</div>
            <div>
              <label class="block text-sm font-bold text-gray-900 mb-2">Department Name *</label>
              <input v-model="form.name" type="text" placeholder="e.g. Engineering" class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm transition-all" />
            </div>
            <div>
              <label class="block text-sm font-bold text-gray-900 mb-2">Description</label>
              <textarea v-model="form.description" rows="3" placeholder="Briefly describe the department's role..." class="w-full px-4 py-2.5 bg-gray-50 border-transparent focus:bg-white focus:border-gray-200 focus:ring-0 rounded-xl text-sm transition-all resize-none"></textarea>
            </div>
            <div>
              <label class="block text-sm font-bold text-gray-900 mb-2">Status</label>
              <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input v-model="form.status" type="radio" value="active" class="w-4 h-4 accent-indigo-600" /><span class="text-sm text-gray-700">Active</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input v-model="form.status" type="radio" value="inactive" class="w-4 h-4 accent-indigo-600" /><span class="text-sm text-gray-700">Inactive</span>
                </label>
              </div>
            </div>
          </div>
          <div class="p-10 border-t border-gray-100 dark:border-slate-800 bg-gray-50/50 dark:bg-slate-800/50 flex items-center gap-3">
            <button @click="drawerOpen = false" class="flex-1 px-4 py-2.5 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl font-semibold text-sm text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 transition-all">Cancel</button>
            <button @click="save" :disabled="saving" class="flex-1 px-4 py-2.5 bg-gray-900 rounded-xl font-semibold text-sm text-white hover:bg-gray-800 transition-all shadow-lg active:scale-95 disabled:opacity-60">
              {{ saving ? 'Saving...' : (editing ? 'Update' : 'Create Department') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Modal -->
    <div v-if="deleteTarget" class="fixed inset-0 z-[70] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" @click="deleteTarget = null"></div>
      <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl p-6 w-full max-w-sm">
        <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-4">
          <Trash2 class="w-6 h-6 text-red-500" />
        </div>
        <h3 class="text-lg font-bold text-gray-900 dark:text-white text-center mb-2">Delete Department</h3>
        <p class="text-sm text-gray-500 dark:text-slate-400 text-center mb-6">Are you sure you want to delete <strong>{{ deleteTarget?.name }}</strong>?</p>
        <div class="flex gap-3">
          <button @click="deleteTarget = null" class="flex-1 px-4 py-2.5 bg-gray-100 rounded-xl font-semibold text-sm text-gray-700">Cancel</button>
          <button @click="doDelete" :disabled="deleting" class="flex-1 px-4 py-2.5 bg-red-600 rounded-xl font-semibold text-sm text-white disabled:opacity-60">
            {{ deleting ? 'Deleting...' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Building2, Users, Plus, Pencil, Trash2, X } from 'lucide-vue-next'

const { fetchDepartments, createDepartment, updateDepartment, deleteDepartment } = useHrm()

const departments = ref([])
const loading = ref(true)
const drawerOpen = ref(false)
const saving = ref(false)
const deleting = ref(false)
const editing = ref(null)
const deleteTarget = ref(null)
const formError = ref('')
const form = reactive({ name: '', description: '', status: 'active' })

const openDrawer = (dept = null) => {
  editing.value = dept
  form.name = dept?.name ?? ''
  form.description = dept?.description ?? ''
  form.status = dept?.status ?? 'active'
  formError.value = ''
  drawerOpen.value = true
}

const confirmDelete = (dept) => { deleteTarget.value = dept }

const load = async () => {
  loading.value = true
  try { departments.value = await fetchDepartments() } catch (e) { console.error(e) } finally { loading.value = false }
}

const save = async () => {
  saving.value = true
  formError.value = ''
  try {
    if (editing.value) {
      await updateDepartment(editing.value.id, form)
    } else {
      await createDepartment(form)
    }
    drawerOpen.value = false
    await load()
  } catch (e) {
    formError.value = e?.data?.message || 'An error occurred.'
  } finally {
    saving.value = false
  }
}

const doDelete = async () => {
  deleting.value = true
  try {
    await deleteDepartment(deleteTarget.value.id)
    deleteTarget.value = null
    await load()
  } catch (e) { console.error(e) } finally { deleting.value = false }
}

onMounted(load)
</script>

<style scoped>
.animate-slide-left { animation: slideLeft 0.3s ease-out; }
@keyframes slideLeft { from { transform: translateX(100%); } to { transform: translateX(0); } }
</style>
