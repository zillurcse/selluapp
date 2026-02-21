<template>
  <div class="p-10 space-y-8 animate-in fade-in slide-in-from-bottom-5 duration-700">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-[1000] text-slate-900 dark:text-white tracking-tight">Staff Management</h1>
        <p class="text-slate-500 dark:text-slate-400 mt-1 font-medium italic">Manage your team members and their roles</p>
      </div>
      <NuxtLink 
        to="/vendor/staff/create"
        class="flex items-center gap-2 px-6 py-3 bg-slate-900 dark:bg-indigo-600 text-white rounded-2xl font-bold hover:scale-105 active:scale-95 transition-all shadow-xl shadow-slate-900/10 dark:shadow-indigo-600/20 group"
      >
        <UserPlus class="w-5 h-5 group-hover:rotate-12 transition-transform" />
        Add Staff Member
      </NuxtLink>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl p-6 rounded-[24px] border border-slate-200/50 dark:border-slate-800/50 shadow-sm group hover:border-indigo-500/30 transition-all duration-500">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-500/10 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
            <Users class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
          </div>
          <div>
            <p class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Staff</p>
            <h3 class="text-2xl font-[1000] text-slate-900 dark:text-white leading-none mt-1">{{ staff.length }}</h3>
          </div>
        </div>
      </div>
      
      <!-- Placeholder stats -->
      <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl p-6 rounded-[24px] border border-slate-200/50 dark:border-slate-800/50 shadow-sm group hover:border-emerald-500/30 transition-all duration-500">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
            <UserCheck class="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
          </div>
          <div>
            <p class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Active Today</p>
            <h3 class="text-2xl font-[1000] text-slate-900 dark:text-white leading-none mt-1">{{ staff.length }}</h3>
          </div>
        </div>
      </div>

      <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl p-6 rounded-[24px] border border-slate-200/50 dark:border-slate-800/50 shadow-sm group hover:border-amber-500/30 transition-all duration-500">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-amber-50 dark:bg-amber-500/10 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
            <ShieldCheck class="w-6 h-6 text-amber-600 dark:text-amber-400" />
          </div>
          <div>
            <p class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Custom Roles</p>
            <h3 class="text-2xl font-[1000] text-slate-900 dark:text-white leading-none mt-1">Managed</h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Staff List -->
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-[32px] border border-slate-200/50 dark:border-slate-800/50 shadow-sm overflow-hidden">
      <div v-if="loading" class="p-20 flex flex-col items-center justify-center space-y-4">
        <div class="relative w-16 h-16">
          <div class="absolute inset-0 border-4 border-indigo-500/20 rounded-full"></div>
          <div class="absolute inset-0 border-4 border-indigo-500 rounded-full border-t-transparent animate-spin"></div>
        </div>
        <p class="text-slate-500 font-bold animate-pulse">Scanning staff records...</p>
      </div>

      <div v-else-if="staff.length === 0" class="p-20 flex flex-col items-center justify-center text-center space-y-6">
        <div class="w-24 h-24 bg-slate-50 dark:bg-slate-800/50 rounded-full flex items-center justify-center">
          <Users class="w-12 h-12 text-slate-300 dark:text-slate-600" />
        </div>
        <div class="max-w-xs">
          <h3 class="text-xl font-bold text-slate-900 dark:text-white">No Staff Members Yet</h3>
          <p class="text-slate-500 dark:text-slate-400 mt-2">Start building your team by adding your first staff member.</p>
        </div>
        <NuxtLink 
          to="/vendor/staff/create"
          class="flex items-center gap-2 px-6 py-3 bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white rounded-2xl font-bold hover:bg-indigo-600 hover:text-white transition-all"
        >
          Add First Member
        </NuxtLink>
      </div>

      <div v-else class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="border-b border-slate-200/50 dark:border-slate-800/50 bg-slate-50/50 dark:bg-slate-800/30">
              <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Name & Email</th>
              <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Role</th>
              <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Joined Date</th>
              <th class="px-8 py-5 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="member in staff" :key="member.id" class="group hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors border-b border-slate-100/50 dark:border-slate-800/50 last:border-0">
              <td class="px-8 py-5">
                <div class="flex items-center gap-4">
                  <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center font-black text-indigo-600 dark:text-indigo-400 border border-slate-200/50 dark:border-slate-700/50 shadow-sm group-hover:scale-110 transition-transform uppercase">
                    {{ member.name.charAt(0) }}
                  </div>
                  <div>
                    <h4 class="font-bold text-slate-900 dark:text-white">{{ member.name }}</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-500">{{ member.email }}</p>
                  </div>
                </div>
              </td>
              <td class="px-8 py-5">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 ring-1 ring-indigo-500/20">
                  {{ member.roles?.[0]?.name || 'No Role' }}
                </span>
              </td>
              <td class="px-8 py-5">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ formatDate(member.created_at) }}</p>
              </td>
              <td class="px-8 py-5 text-right space-x-2">
                <div class="flex items-center justify-end gap-2">
                  <NuxtLink 
                    :to="`/vendor/staff/${member.id}`"
                    class="p-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-600/30 hover:shadow-lg hover:shadow-indigo-500/10 transition-all group"
                  >
                    <Edit3 class="w-4 h-4 transition-transform group-hover:scale-110" />
                  </NuxtLink>
                  <button 
                    @click="handleDeleteItem(member.id)"
                    class="p-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-400 hover:text-rose-600 hover:border-rose-600/30 hover:shadow-lg hover:shadow-rose-500/10 transition-all group"
                  >
                    <Trash2 class="w-4 h-4 transition-transform group-hover:scale-110" />
                  </button>
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
  Users, 
  UserPlus, 
  UserCheck, 
  ShieldCheck, 
  Edit3, 
  Trash2, 
  Mail, 
  Search 
} from 'lucide-vue-next'

const { getAll, deleteItem } = useCrud()
const { $toast } = useNuxtApp()

const staff = ref([])
const loading = ref(true)

const fetchStaff = async () => {
  loading.value = true
  try {
    const response = await getAll('/vendor/staff')
    staff.value = response
  } catch (error) {
    console.error('Error fetching staff:', error)
    $toast.error('Failed to load staff records')
  } finally {
    loading.value = false
  }
}

const handleDeleteItem = async (id) => {
  if (confirm('Are you sure you want to remove this staff member?')) {
    try {
      await deleteItem(id, '/vendor/staff')
      $toast.success('Staff member removed successfully')
      await fetchStaff()
    } catch (error) {
      $toast.error('Failed to remove staff member')
    }
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

onMounted(fetchStaff)
</script>
