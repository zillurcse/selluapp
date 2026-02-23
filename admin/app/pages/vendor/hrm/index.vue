<template>
  <div class="p-10 max-w-7xl mx-auto bg-[#f8fafc] dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Human Resource Management</h1>
      <p class="text-gray-500 dark:text-slate-400 mt-1">Manage your team, organization structure, and payroll from one place.</p>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div v-if="statsLoading" v-for="i in 4" :key="i" class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm animate-pulse">
        <div class="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
        <div class="h-8 bg-gray-200 rounded w-1/2 mb-2"></div>
        <div class="h-3 bg-gray-200 rounded w-full"></div>
      </div>
      <div v-else v-for="stat in statItems" :key="stat.label" class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between mb-4">
          <div :class="`p-2 rounded-lg ${stat.bg}`">
            <component :is="stat.icon" :class="`w-5 h-5 ${stat.color}`" />
          </div>
          <span class="text-xs font-medium text-gray-400 dark:text-slate-500">{{ stat.trend }}</span>
        </div>
        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ stat.value }}</div>
        <div class="text-sm text-gray-500 dark:text-slate-400 mt-1">{{ stat.label }}</div>
      </div>
    </div>

    <!-- Navigation Hub -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <NuxtLink
        v-for="item in navItems"
        :key="item.title"
        :to="item.link"
        class="group bg-white dark:bg-slate-900 p-6 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-all duration-300 hover:-translate-y-1"
      >
        <div class="flex items-start justify-between">
          <div :class="`p-3 rounded-xl ${item.bg} group-hover:scale-110 transition-transform duration-300`">
            <component :is="item.icon" :class="`w-6 h-6 ${item.color}`" />
          </div>
          <ArrowRight class="w-5 h-5 text-gray-300 dark:text-slate-600 group-hover:text-gray-900 dark:group-hover:text-white transition-colors" />
        </div>
        <div class="mt-4">
          <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ item.title }}</h3>
          <p class="text-sm text-gray-500 dark:text-slate-400 mt-1">{{ item.description }}</p>
        </div>
        <div v-if="item.isPro" class="mt-4">
          <span class="bg-orange-100 text-orange-600 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider">Pro Feature</span>
        </div>
      </NuxtLink>
    </div>

    <!-- Recent Activities -->
    <div class="mt-12">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white">Recent Activities</h2>
      </div>
      <div class="bg-white dark:bg-slate-900 rounded-2xl border border-gray-100 dark:border-slate-800 shadow-sm divide-y divide-gray-50 dark:divide-slate-800">
        <!-- Loading skeleton -->
        <div v-if="activitiesLoading" v-for="i in 4" :key="i" class="p-4 flex items-center gap-4 animate-pulse">
          <div class="w-10 h-10 rounded-full bg-gray-200"></div>
          <div class="flex-1 space-y-2">
            <div class="h-3 bg-gray-200 rounded w-3/4"></div>
            <div class="h-2 bg-gray-200 rounded w-1/2"></div>
          </div>
        </div>
        <!-- Empty state -->
        <div v-else-if="!activities.length" class="p-8 text-center text-gray-400">
          <Users class="w-10 h-10 mx-auto mb-2 opacity-40" />
          <p class="text-sm">No recent activities yet.</p>
        </div>
        <!-- Activity rows -->
        <div v-else v-for="act in activities" :key="act.text + act.time" class="p-4 flex items-center gap-4">
          <div class="w-10 h-10 rounded-full flex items-center justify-center"
            :class="act.type === 'new_employee' ? 'bg-blue-50' : 'bg-orange-50'">
            <UserIcon v-if="act.type === 'new_employee'" class="w-5 h-5 text-blue-500" />
            <FileClock v-else class="w-5 h-5 text-orange-500" />
          </div>
          <div>
            <p class="text-sm font-medium text-gray-900 dark:text-slate-100">{{ act.text }}</p>
            <p class="text-xs text-gray-500 dark:text-slate-400">{{ act.sub }}</p>
          </div>
          <span class="ml-auto text-xs text-gray-400 dark:text-slate-500">{{ act.time }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {
  Users,
  Building2,
  UserSquare2,
  CalendarCheck2,
  Banknote,
  FileClock,
  ArrowRight,
  User as UserIcon,
  TrendingUp,
  Clock,
  CheckCircle2
} from 'lucide-vue-next'

definePageMeta({
  middleware: 'auth',
  permissions: 'hrm.dashboard.view'
})

const { fetchStats, fetchRecentActivities } = useHrm()

const stats = ref(null)
const activities = ref([])
const statsLoading = ref(true)
const activitiesLoading = ref(true)

onMounted(async () => {
  try {
    const [s, a] = await Promise.all([fetchStats(), fetchRecentActivities()])
    stats.value = s
    activities.value = a
  } catch (e) {
    console.error('HRM dashboard error:', e)
  } finally {
    statsLoading.value = false
    activitiesLoading.value = false
  }
})

const statItems = computed(() => {
  if (!stats.value) return []
  return [
    { label: 'Total Employees', value: stats.value.total_employees ?? 0, icon: Users, bg: 'bg-blue-50', color: 'text-blue-600', trend: `${stats.value.active_employees ?? 0} active` },
    { label: 'On Leave', value: stats.value.on_leave ?? 0, icon: Clock, bg: 'bg-orange-50', color: 'text-orange-600', trend: `${stats.value.pending_leaves ?? 0} pending approval` },
    { label: 'Attendance', value: stats.value.attendance_rate ?? '0%', icon: CheckCircle2, bg: 'bg-green-50', color: 'text-green-600', trend: 'Rate today' },
    { label: 'New Hires', value: stats.value.new_hires ?? 0, icon: TrendingUp, bg: 'bg-purple-50', color: 'text-purple-600', trend: 'Last 30 days' },
  ]
})

const navItems = [
  { title: 'Employees', description: 'Manage all employee records, profiles and details.', icon: Users, link: '/vendor/hrm/employees', bg: 'bg-blue-50', color: 'text-blue-600', isPro: false },
  { title: 'Departments', description: 'View and manage company departments and teams.', icon: Building2, link: '/vendor/hrm/departments', bg: 'bg-indigo-50', color: 'text-indigo-600', isPro: false },
  { title: 'Designations', description: 'Define and manage employee roles and titles.', icon: UserSquare2, link: '/vendor/hrm/designations', bg: 'bg-purple-50', color: 'text-purple-600', isPro: false },
  { title: 'Attendance', description: 'Track daily check-ins, check-outs and working hours.', icon: CalendarCheck2, link: '/vendor/hrm/attendance', bg: 'bg-green-50', color: 'text-green-600', isPro: true },
  { title: 'Payroll', description: 'Process salaries, bonuses and manage tax records.', icon: Banknote, link: '/vendor/hrm/payroll', bg: 'bg-emerald-50', color: 'text-emerald-600', isPro: true },
  { title: 'Leaves', description: 'Manage leave requests, balances and holidays.', icon: FileClock, link: '/vendor/hrm/leaves', bg: 'bg-orange-50', color: 'text-orange-600', isPro: true },
]


</script>
