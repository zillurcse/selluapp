<template>
  <div class="p-4 sm:p-6 lg:p-10 bg-slate-50 dark:bg-slate-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
      <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <NuxtLink to="/vendor/role-permissions" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 flex items-center mb-2 transition-colors">
            <ArrowLeftIcon class="w-4 h-4 mr-1" />
            Back to Roles
          </NuxtLink>
          <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Create New Role</h1>
          <p class="text-slate-500 dark:text-slate-400 mt-1">Define a new role and assign specific permissions to your team members.</p>
        </div>
        
        <div class="flex items-center gap-3">
          <button 
            @click="toggleAllPermissions"
            class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 px-3 py-1.5 rounded-lg border border-indigo-200 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-900/30 transition-all"
          >
            {{ allSelected ? 'Deselect All' : 'Select All Permissions' }}
          </button>
        </div>
      </div>

      <form @submit.prevent="saveRole" class="space-y-8 pb-20">
        <!-- Role Info Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 md:p-8 transition-all">
          <div class="max-w-2xl">
            <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2 text-indigo-100">Role Name</label>
            <div class="relative group">
              <input
                v-model="form.name"
                type="text"
                id="name"
                required
                placeholder="e.g. Inventory Manager, Sales Lead"
                class="block w-full px-4 py-3 rounded-xl border-slate-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all sm:text-sm placeholder-slate-400 dark:placeholder-slate-500"
              />
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                <ShieldCheckIcon class="w-5 h-5" />
              </div>
            </div>
            <p v-if="errors.name" class="mt-2 text-sm text-red-600 dark:text-red-400">{{ errors.name[0] }}</p>
          </div>
        </div>

        <!-- Permissions Section -->
        <div class="space-y-4">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
              <LockIcon class="w-5 h-5 text-indigo-500" />
              Permissions Management
            </h2>
            <div class="relative w-64">
              <input 
                v-model="searchQuery"
                type="text"
                placeholder="Search permissions..."
                class="w-full pl-9 pr-4 py-2 text-sm rounded-lg border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 transition-all"
              />
              <SearchIcon class="absolute left-3 top-2.5 w-4 h-4 text-slate-400" />
            </div>
          </div>

          <div v-if="loadingPermissions" class="flex flex-col items-center justify-center py-20 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mb-4"></div>
            <p class="text-slate-500 dark:text-slate-400 animate-pulse font-medium">Loading available permissions...</p>
          </div>
          
          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div 
              v-for="(perms, group) in filteredPermissions" 
              :key="group" 
              class="group relative bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md transition-all duration-300"
            >
              <div class="p-5 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between bg-slate-50/50 dark:bg-slate-900/50 rounded-t-2xl">
                <h3 class="text-sm font-black text-slate-800 dark:text-slate-200 uppercase tracking-wider flex items-center gap-2">
                  <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                  {{ group }}
                </h3>
                <button 
                  type="button"
                  @click="toggleGroup(group, perms)"
                  class="text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200 uppercase"
                >
                  {{ isGroupSelected(perms) ? 'Deselect All' : 'Select All' }}
                </button>
              </div>
              
              <div class="p-5 space-y-3">
                <label 
                  v-for="permission in perms" 
                  :key="permission.id" 
                  :for="`permission-${permission.id}`"
                  class="flex items-center p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/50 cursor-pointer transition-colors group/item"
                >
                  <div class="relative flex items-center">
                    <input
                      :id="`permission-${permission.id}`"
                      type="checkbox"
                      :value="permission.name"
                      v-model="form.permissions"
                      class="peer h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-slate-300 dark:border-slate-600 rounded-md transition-all cursor-pointer bg-white dark:bg-slate-900"
                    />
                    <CheckIcon class="absolute inset-0 w-3.5 h-3.5 m-auto text-white opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity" />
                  </div>
                  <span class="ml-3 text-sm font-medium text-slate-700 dark:text-slate-300 capitalize group-hover/item:text-indigo-600 dark:group-hover/item:text-indigo-400 transition-colors">
                    {{ formatPermissionName(permission.name) }}
                  </span>
                </label>
              </div>
            </div>
          </div>
          
          <div v-if="!loadingPermissions && Object.keys(filteredPermissions).length === 0" class="py-20 text-center bg-white dark:bg-slate-800 rounded-2xl border border-dashed border-slate-300 dark:border-slate-700">
            <SearchIcon class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-4" />
            <p class="text-slate-500 dark:text-slate-400 font-medium">No permissions found matching your search.</p>
            <button @click="searchQuery = ''" class="mt-4 text-indigo-600 font-semibold hover:underline">Clear search</button>
          </div>
        </div>

        <!-- Sticky Submit Bar -->
        <div class="fixed bottom-0 left-0 lg:left-72 right-0 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-t border-slate-200 dark:border-slate-700 p-4 z-10 transition-all duration-500">
          <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="hidden md:block">
              <span class="text-sm font-medium text-slate-500 dark:text-slate-400">
                {{ form.permissions.length }} permissions selected
              </span>
            </div>
            <div class="flex items-center gap-4 w-full md:w-auto">
              <NuxtLink
                to="/vendor/role-permissions"
                class="flex-1 md:flex-none text-center px-6 py-3 border border-slate-300 dark:border-slate-600 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all"
              >
                Cancel
              </NuxtLink>
              <button
                type="submit"
                :disabled="submitting || !form.name"
                class="flex-2 md:flex-none inline-flex items-center justify-center px-8 py-3 border border-transparent rounded-xl shadow-lg shadow-indigo-200 dark:shadow-indigo-900/20 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all transform active:scale-[0.98]"
              >
                <Loader2Icon v-if="submitting" class="w-4 h-4 mr-2 animate-spin" />
                <PlusIcon v-else class="w-4 h-4 mr-2" />
                {{ submitting ? 'Creating Role...' : 'Save Role' }}
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { 
  ArrowLeftIcon, 
  SearchIcon, 
  LockIcon, 
  ShieldCheckIcon, 
  CheckIcon, 
  PlusIcon,
  Loader2Icon 
} from 'lucide-vue-next'

definePageMeta({
  middleware: 'auth'
})

const { createItem, getAll } = useCrud()
const router = useRouter()
const { $toast } = useNuxtApp()

const form = reactive({
  name: '',
  permissions: []
})

const errors = ref({})
const groupedPermissions = ref({})
const loadingPermissions = ref(true)
const submitting = ref(false)
const searchQuery = ref('')

// Computed properties
const filteredPermissions = computed(() => {
  if (!searchQuery.value) return groupedPermissions.value
  
  const query = searchQuery.value.toLowerCase()
  const filtered = {}
  
  Object.entries(groupedPermissions.value).forEach(([group, perms]) => {
    const matchedPerms = perms.filter(p => 
      p.name.toLowerCase().includes(query) || 
      group.toLowerCase().includes(query)
    )
    
    if (matchedPerms.length > 0) {
      filtered[group] = matchedPerms
    }
  })
  
  return filtered
})

const allSelected = computed(() => {
  const totalPerms = Object.values(groupedPermissions.value).flat().length
  return totalPerms > 0 && form.permissions.length === totalPerms
})

// Methods
const fetchPermissions = async () => {
  try {
    loadingPermissions.value = true
    const res = await getAll('/vendor/permissions')
    if (res) {
      // Sort groups alphabetically
      const sorted = {}
      Object.keys(res).sort().forEach(key => {
        sorted[key] = res[key]
      })
      groupedPermissions.value = sorted
    }
  } catch (error) {
    console.error('Error fetching permissions:', error)
    $toast?.error('Failed to load permissions')
  } finally {
    loadingPermissions.value = false
  }
}

const formatPermissionName = (name) => {
  const parts = name.split('.')
  return parts.slice(1).join(' ') || name
}

const isGroupSelected = (perms) => {
  return perms.every(p => form.permissions.includes(p.name))
}

const toggleGroup = (group, perms) => {
  const allInGroupSelected = isGroupSelected(perms)
  
  if (allInGroupSelected) {
    // Remove all permissions of this group
    const permNames = perms.map(p => p.name)
    form.permissions = form.permissions.filter(p => !permNames.includes(p))
  } else {
    // Add all permissions of this group (avoid duplicates)
    const currentPerms = new Set(form.permissions)
    perms.forEach(p => currentPerms.add(p.name))
    form.permissions = Array.from(currentPerms)
  }
}

const toggleAllPermissions = () => {
  if (allSelected.value) {
    form.permissions = []
  } else {
    const allNames = Object.values(groupedPermissions.value).flat().map(p => p.name)
    form.permissions = allNames
  }
}

const saveRole = async () => {
  try {
    submitting.value = true
    errors.value = {}
    
    const res = await createItem('/vendor/roles', form)
    if (res) {
      $toast?.success('Role created successfully')
      router.push('/vendor/role-permissions')
    }
  } catch (error) {
    console.error('Error saving role:', error)
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    }
    $toast?.error(error.response?.data?.message || 'Error saving role')
  } finally {
    submitting.value = false
  }
}

onMounted(fetchPermissions)
</script>

