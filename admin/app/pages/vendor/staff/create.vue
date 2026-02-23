<template>
  <div class="p-8 pb-20 max-w-4xl mx-auto animate-in fade-in slide-in-from-bottom-5 duration-700">
    <!-- Header -->
    <div class="flex items-center justify-between mb-10">
      <div class="space-y-1">
        <NuxtLink to="/vendor/staff" class="group flex items-center gap-2 text-slate-500 hover:text-indigo-600 transition-colors mb-4">
          <ArrowLeft class="w-4 h-4 group-hover:-translate-x-1 transition-transform" />
          <span class="text-sm font-bold uppercase tracking-widest">Back to Staff</span>
        </NuxtLink>
        <h1 class="text-3xl font-[1000] text-slate-900 dark:text-white tracking-tight">Add New Staff</h1>
        <p class="text-slate-500 dark:text-slate-400 font-medium italic">Create a new access account for your team</p>
      </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-[32px] border border-slate-200/50 dark:border-slate-800/50 shadow-2xl shadow-slate-200/20 dark:shadow-none overflow-hidden h-fit">
      <form @submit.prevent="handleSubmit" class="p-10 space-y-10">
        <!-- Basic Info Section -->
        <div class="space-y-8">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-500/10 rounded-xl flex items-center justify-center">
              <User class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
            </div>
            <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight text-center">Identity Details</h3>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-3">
              <label class="text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest ml-1">Full Name</label>
              <div class="relative group">
                <input 
                  v-model="form.name"
                  type="text" 
                  placeholder="e.g. John Doe"
                  class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800 rounded-2xl outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-medium text-slate-900 dark:text-white"
                  required
                />
                <User class="absolute right-5 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors" />
              </div>
            </div>

            <div class="space-y-3">
              <label class="text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest ml-1">Email Address</label>
              <div class="relative group">
                <input 
                  v-model="form.email"
                  type="email" 
                  placeholder="e.g. john@yourstore.com"
                  class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800 rounded-2xl outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-medium text-slate-900 dark:text-white"
                  required
                />
                <Mail class="absolute right-5 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 group-focus-within:text-indigo-500 transition-colors" />
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
             <div class="space-y-3">
              <label class="text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest ml-1">Login Password</label>
              <div class="relative group">
                <input 
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'" 
                  placeholder="••••••••"
                  class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800 rounded-2xl outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-medium text-slate-900 dark:text-white font-mono"
                  required
                />
                <button type="button" @click="showPassword = !showPassword" class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-indigo-500 transition-colors">
                  <component :is="showPassword ? EyeOff : Eye" class="w-5 h-5" />
                </button>
              </div>
            </div>

            <div class="space-y-3 text-center">
              <label class="text-sm font-black text-slate-700 dark:text-slate-300 uppercase tracking-widest ml-1">Assign Security Role</label>
              <div class="relative group">
                <select 
                  v-model="form.role"
                  class="w-full px-6 py-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800 rounded-2xl outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold text-slate-900 dark:text-white appearance-none cursor-pointer"
                  required
                >
                  <option value="" disabled>Select a role</option>
                  <option v-for="role in roles" :key="role.id" :value="role.name">
                    {{ role.name }}
                  </option>
                </select>
                <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none">
                  <ChevronDown class="w-5 h-5 text-slate-400 group-focus-within:text-indigo-500" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Role Permissions Preview (Optional but nice) -->
        <div v-if="selectedRole" class="p-6 rounded-2xl bg-indigo-500/5 border border-indigo-500/10 space-y-4">
           <div class="flex items-center gap-2">
              <ShieldAlert class="w-4 h-4 text-indigo-500" />
              <span class="text-xs font-black text-indigo-600 dark:text-indigo-400 uppercase tracking-[0.1em]">Privileges for "{{ selectedRole.name }}"</span>
           </div>
           <div class="flex flex-wrap gap-2">
              <span v-for="p in selectedRole.permissions" :key="p.id" class="px-2 py-1 bg-white dark:bg-slate-800 text-[9px] font-black text-slate-500 dark:text-slate-400 rounded-md border border-slate-200 dark:border-slate-700 uppercase tracking-tighter">
                {{ p.name }}
              </span>
              <span v-if="selectedRole.permissions.length === 0" class="text-xs text-slate-400 italic font-medium">No special permissions defined for this role.</span>
           </div>
        </div>

        <!-- Submit Bar -->
        <div class="pt-10 flex flex-col md:flex-row gap-4">
          <button 
            type="submit"
            :disabled="submitting"
            class="flex-1 flex items-center justify-center gap-3 px-8 py-5 bg-slate-900 dark:bg-indigo-600 text-white rounded-[20px] font-black uppercase tracking-widest hover:scale-[1.02] active:scale-95 transition-all shadow-xl shadow-slate-900/20 dark:shadow-indigo-600/30 disabled:opacity-50 disabled:scale-100"
          >
            <span v-if="submitting" class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            <span v-else>Register Staff Account</span>
          </button>
          
          <NuxtLink 
            to="/vendor/staff"
            class="px-8 py-5 bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white rounded-[20px] font-black uppercase tracking-widest text-center hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors"
          >
            Cancel
          </NuxtLink>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { 
  ArrowLeft, 
  User, 
  Mail, 
  Lock, 
  ShieldCheck, 
  Eye, 
  EyeOff, 
  ChevronDown,
  ShieldAlert
} from 'lucide-vue-next'

definePageMeta({
  middleware: 'auth',
  permissions: 'staff.create'
})

const { getAll, createItem } = useCrud()
const router = useRouter()
const { $toast } = useNuxtApp()

const roles = ref([])
const submitting = ref(false)
const showPassword = ref(false)

const form = ref({
  name: '',
  email: '',
  password: '',
  role: ''
})

const selectedRole = computed(() => {
  return roles.value.find(r => r.name === form.value.role)
})

const fetchRoles = async () => {
  try {
    // We already scoped RoleController::index to only return vendor's roles
    const response = await getAll('/vendor/roles')
    roles.value = response
  } catch (error) {
    console.error('Error fetching roles:', error)
  }
}

const handleSubmit = async () => {
  submitting.value = true
  try {
    await createItem('/vendor/staff',form.value)
    
    router.push('/vendor/staff')
  } catch (error) {
    console.error('Error creating staff:', error)
    $toast.error(error.response?.data?.message || 'Failed to create staff member')
  } finally {
    submitting.value = false
  }
}

onMounted(fetchRoles)
</script>
