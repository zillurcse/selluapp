<template>
  <div class="p-4 sm:p-6 lg:p-10 bg-slate-50 dark:bg-slate-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Roles & Permissions</h1>
          <p class="text-slate-500 dark:text-slate-400 mt-1">Manage user roles and their associated permissions for your staff.</p>
        </div>
        <NuxtLink
          to="/vendor/role-permissions/create"
          class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-200 dark:shadow-indigo-900/20 transition-all transform active:scale-[0.98]"
        >
          <PlusIcon class="w-5 h-5 mr-2" />
          Create New Role
        </NuxtLink>
      </div>

      <!-- Roles Grid/Table -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden transition-all">
        <div v-if="loading" class="p-20 flex flex-col items-center justify-center">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mb-4"></div>
          <p class="text-slate-500 dark:text-slate-400 font-medium animate-pulse">Fetching roles...</p>
        </div>
        
        <div v-else-if="roles.length === 0" class="p-20 text-center">
          <div class="w-20 h-20 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-slate-800">
            <ShieldIcon class="w-10 h-10 text-slate-300 dark:text-slate-600" />
          </div>
          <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">No roles found</h3>
          <p class="text-slate-500 dark:text-slate-400 mb-6">Start by creating your first team role.</p>
          <NuxtLink
            to="/vendor/role-permissions/create"
            class="inline-flex items-center px-4 py-2 text-sm font-bold text-indigo-600 dark:text-indigo-400 hover:underline"
          >
            Create Role now
          </NuxtLink>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
            <thead class="bg-slate-50 dark:bg-slate-900/50">
              <tr>
                <th class="px-6 py-4 text-left text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Role Name</th>
                <th class="px-6 py-4 text-left text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Permissions</th>
                <th class="px-6 py-4 text-left text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Created At</th>
                <th class="px-6 py-4 text-right text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
              <tr v-for="role in roles" :key="role.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors group">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform">
                      <ShieldCheckIcon class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div>
                      <div class="text-sm font-bold text-slate-900 dark:text-white capitalize">{{ role.name }}</div>
                      <div class="text-xs text-slate-500 dark:text-slate-500">{{ role.permissions?.length || 0 }} permissions assigned</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="flex flex-wrap gap-1.5 max-w-md">
                    <span 
                      v-for="permission in role.permissions?.slice(0, 3)" 
                      :key="permission.id"
                      class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 uppercase tracking-tight"
                    >
                      {{ formatPermission(permission.name) }}
                    </span>
                    <span v-if="role.permissions?.length > 3" class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                      +{{ role.permissions.length - 3 }} MORE
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-slate-500 dark:text-slate-400 tracking-tight">
                    {{ new Date(role.created_at).toLocaleDateString() }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right">
                  <div class="flex justify-end gap-2">
                    <NuxtLink 
                      :to="`/vendor/role-permissions/${role.id}`"
                      class="p-2 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/40 rounded-xl transition-all"
                      title="Edit Role"
                    >
                      <PencilIcon class="w-5 h-5" />
                    </NuxtLink>
                    <button 
                      v-if="!['super-admin', 'vendor'].includes(role.name)"
                      @click="handleDelete(role.id, role.name)"
                      class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/40 rounded-xl transition-all"
                      title="Delete Role"
                    >
                      <TrashIcon class="w-5 h-5" />
                    </button>
                    <button 
                      v-else
                      disabled
                      class="p-2 text-slate-300 dark:text-slate-600 cursor-not-allowed"
                      title="System Role (Locked)"
                    >
                      <LockIcon class="w-5 h-5" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { PlusIcon, PencilIcon, TrashIcon, ShieldCheckIcon, ShieldIcon, LockIcon } from 'lucide-vue-next'

definePageMeta({
  middleware: 'auth',
  permissions: 'roles.view'
})

const roles = ref([])
const loading = ref(false)


const { getAll, deleteItem } = useCrud()
const { $toast } = useNuxtApp()

const fetchRoles = async () => {
    try {
        loading.value = true
        const res = await getAll('/vendor/roles')
        if (res) {
            roles.value = res
        }
    } catch (e) {
        console.error('Failed to fetch roles', e)
        $toast?.error('Failed to load roles')
    } finally {
        loading.value = false
    }
}

const formatPermission = (name) => {
  return name.split('.').pop().replace(/_/g, ' ')
}

const handleDelete = async (id, name) => {
    if (!confirm(`Are you sure you want to delete the "${name}" role? This action cannot be undone.`)) return
    
    try {
      await deleteItem(id, '/vendor/roles')
      $toast?.success('Role deleted successfully')
      fetchRoles()
    } catch (error) {
      console.error('Delete failed', error)
      $toast?.error(error.response?.data?.message || 'Error deleting role')
    }
}

fetchRoles()
</script>

