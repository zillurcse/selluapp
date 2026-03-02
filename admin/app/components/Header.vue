<template>
  <header class="app-header">

    <!-- Left: Mobile toggle + Breadcrumb -->
    <div class="header-left">
      <button class="mobile-menu-btn" @click="sidebar.toggle()" aria-label="Toggle menu">
        <Menu :size="20" />
      </button>

      <nav class="breadcrumb">
        <span class="breadcrumb-root">Admin</span>
        <ChevronRight :size="12" class="breadcrumb-sep" />
        <span class="breadcrumb-current">{{ currentPageTitle }}</span>
      </nav>
    </div>

    <!-- Right: Actions -->
    <div class="header-right">

      <!-- Visit Shop -->
      <a
        v-if="can('settings.view')"
        :href="config.public.webBase"
        target="_blank"
        class="visit-btn"
      >
        <ExternalLink :size="13" />
        Visit Shop
      </a>

      <!-- Divider -->
      <div class="header-divider"></div>

      <!-- Icon Actions -->
      <div class="icon-row">
        <button class="hdr-icon-btn" title="Refresh" @click="$router.go(0)">
          <RotateCcw :size="15" />
        </button>

        <NuxtLink
          v-if="can(['pos.view', 'pos.manage'])"
          to="/vendor/pos"
          target="_blank"
          class="hdr-icon-btn"
          title="Point of Sale"
        >
          <Monitor :size="15" />
        </NuxtLink>

        <button class="hdr-icon-btn hdr-icon-btn--bell" title="Notifications">
          <Bell :size="15" />
          <span class="notif-dot"></span>
        </button>
      </div>

      <!-- Divider -->
      <div class="header-divider"></div>

      <!-- User Menu -->
      <div class="user-menu-wrap" ref="dropdownRef">
        <button class="user-btn" @click="toggleDropdown">
          <div class="user-btn-avatar">
            {{ userInitials }}
          </div>
          <div class="user-btn-info">
            <span class="user-btn-name">{{ auth.user?.name || 'User' }}</span>
            <span class="user-btn-role">{{ userRoleLabel }}</span>
          </div>
          <ChevronRight
            :size="13"
            class="user-btn-chevron"
            :class="{ 'is-open': isDropdownOpen }"
          />
        </button>

        <transition name="dd">
          <div v-if="isDropdownOpen" class="dd-panel">
            <!-- Header -->
            <div class="dd-head">
              <div class="dd-avatar">{{ userInitials }}</div>
              <div class="dd-meta">
                <p class="dd-name">{{ auth.user?.name || 'User' }}</p>
                <p class="dd-email">{{ auth.user?.email || '' }}</p>
              </div>
            </div>

            <!-- Items -->
            <div class="dd-body">
              <NuxtLink
                v-if="can('settings.view')"
                to="/vendor/profile-settings"
                class="dd-item"
                @click="isDropdownOpen = false"
              >
                <span class="dd-item-icon"><User :size="14" /></span>
                Account Settings
              </NuxtLink>
            </div>

            <div class="dd-sep"></div>

            <div class="dd-footer">
              <button class="dd-item dd-item--danger" @click="handleLogout">
                <span class="dd-item-icon"><LogOut :size="14" /></span>
                Sign Out
              </button>
            </div>
          </div>
        </transition>
      </div>

    </div>
  </header>
</template>

<script setup>
import {
  ExternalLink,
  RotateCcw,
  Monitor,
  Bell,
  Menu,
  LogOut,
  User,
  ChevronRight,
} from 'lucide-vue-next'
import { ref, computed, onMounted, onUnmounted } from 'vue'

const auth = useAuthStore()
const { can } = usePermissions()
const router = useRouter()
const route = useRoute()
const config = useRuntimeConfig()
const sidebar = useSidebar()
const isDropdownOpen = ref(false)
const dropdownRef = ref(null)

const currentPageTitle = computed(() => {
  const path = route.path
  if (path === '/') return 'Dashboard'
  const parts = path.split('/')
  const lastPart = parts[parts.length - 1]
  return lastPart.charAt(0).toUpperCase() + lastPart.slice(1).replace(/-/g, ' ')
})

const userInitials = computed(() => {
  const name = auth.user?.name || 'U'
  return name.substring(0, 1).toUpperCase()
})

const userRoleLabel = computed(() => {
  const roles = auth.user?.roles
  if (!roles?.length) return 'User'
  const roleNames = roles.map(r => r.name)
  if (roleNames.includes('super-admin')) return 'Super Admin'
  if (roleNames.includes('admin')) return 'Admin'
  if (roleNames.includes('vendor')) return 'Vendor'
  return 'User'
})

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value
}

const closeDropdown = (e) => {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    isDropdownOpen.value = false
  }
}

const handleLogout = async () => {
  isDropdownOpen.value = false
  await auth.logout()
}

onMounted(() => document.addEventListener('click', closeDropdown))
onUnmounted(() => document.removeEventListener('click', closeDropdown))
</script>

<style scoped>
/* ─── Shell ──────────────────────────────────────────────────────────────────── */
.app-header {
  position: sticky;
  top: 0;
  z-index: 40;
  height: 56px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  background-color: #fff;
  border-bottom: 1px solid #e5e7eb;
  gap: 12px;
  flex-shrink: 0;
}

.dark .app-header {
  background-color: #0f1117;
  border-bottom-color: #1e2330;
}

/* ─── Left ───────────────────────────────────────────────────────────────────── */
.header-left {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 0;
}

/* Mobile menu button */
.mobile-menu-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 7px;
  background: transparent;
  color: #6b7280;
  cursor: pointer;
  flex-shrink: 0;
  transition: background-color 0.15s, color 0.15s;
}
.mobile-menu-btn:hover { background-color: #f3f4f6; color: #111827; }
.dark .mobile-menu-btn:hover { background-color: #1a1f2e; color: #f9fafb; }

@media (min-width: 1024px) { .mobile-menu-btn { display: none; } }

/* Breadcrumb */
.breadcrumb {
  display: flex;
  align-items: center;
  gap: 5px;
}

.breadcrumb-root {
  font-size: 12.5px;
  font-weight: 500;
  color: #9ca3af;
}
.dark .breadcrumb-root { color: #4b5563; }

.breadcrumb-sep { color: #d1d5db; flex-shrink: 0; }
.dark .breadcrumb-sep { color: #374151; }

.breadcrumb-current {
  font-size: 13px;
  font-weight: 600;
  color: #111827;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.dark .breadcrumb-current { color: #f9fafb; }

/* ─── Right ──────────────────────────────────────────────────────────────────── */
.header-right {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}

/* Divider */
.header-divider {
  width: 1px;
  height: 20px;
  background-color: #e5e7eb;
  flex-shrink: 0;
}
.dark .header-divider { background-color: #1e2330; }

/* Visit Shop */
.visit-btn {
  display: none;
  align-items: center;
  gap: 5px;
  padding: 6px 12px;
  font-size: 12px;
  font-weight: 500;
  color: #374151;
  background-color: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 7px;
  text-decoration: none;
  white-space: nowrap;
  transition: background-color 0.15s, border-color 0.15s, color 0.15s;
}
.visit-btn:hover { background-color: #f3f4f6; border-color: #d1d5db; color: #111827; }

.dark .visit-btn { color: #9ca3af; background-color: #1a1f2e; border-color: #1e2330; }
.dark .visit-btn:hover { background-color: #252b3b; color: #f9fafb; }

@media (min-width: 768px) { .visit-btn { display: flex; } }

/* ─── Icon Row ───────────────────────────────────────────────────────────────── */
.icon-row {
  display: flex;
  align-items: center;
  gap: 2px;
}

.hdr-icon-btn {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 7px;
  background: transparent;
  color: #9ca3af;
  cursor: pointer;
  text-decoration: none;
  transition: background-color 0.15s, color 0.15s;
}
.hdr-icon-btn:hover { background-color: #f3f4f6; color: #374151; }
.dark .hdr-icon-btn { color: #4b5563; }
.dark .hdr-icon-btn:hover { background-color: #1a1f2e; color: #9ca3af; }

/* Bell with dot */
.notif-dot {
  position: absolute;
  top: 7px;
  right: 7px;
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background-color: #4f46e5;
  border: 1.5px solid #fff;
}
.dark .notif-dot { border-color: #0f1117; }

/* ─── User Button ────────────────────────────────────────────────────────────── */
.user-menu-wrap { position: relative; }

.user-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  height: 36px;
  padding: 0 10px 0 4px;
  border: 1px solid #e5e7eb;
  border-radius: 9px;
  background-color: #f9fafb;
  cursor: pointer;
  transition: background-color 0.15s, border-color 0.15s;
}
.user-btn:hover { background-color: #f3f4f6; border-color: #d1d5db; }
.dark .user-btn { background-color: #1a1f2e; border-color: #1e2330; }
.dark .user-btn:hover { background-color: #252b3b; border-color: #2d3548; }

.user-btn-avatar {
  width: 28px;
  height: 28px;
  border-radius: 6px;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  color: #fff;
  font-size: 12px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.user-btn-info {
  display: none;
  flex-direction: column;
  align-items: flex-start;
}
@media (min-width: 640px) { .user-btn-info { display: flex; } }

.user-btn-name {
  font-size: 12px;
  font-weight: 600;
  color: #111827;
  white-space: nowrap;
  line-height: 1.2;
}
.dark .user-btn-name { color: #f9fafb; }

.user-btn-role {
  font-size: 10px;
  font-weight: 500;
  color: #4f46e5;
  line-height: 1.2;
}
.dark .user-btn-role { color: #818cf8; }

.user-btn-chevron {
  color: #9ca3af;
  transition: transform 0.2s ease;
  flex-shrink: 0;
}
.user-btn-chevron.is-open { transform: rotate(90deg); }

/* ─── Dropdown Panel ─────────────────────────────────────────────────────────── */
.dd-panel {
  position: absolute;
  right: 0;
  top: calc(100% + 6px);
  width: 230px;
  background-color: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 10px;
  box-shadow: 0 4px 16px -2px rgba(0,0,0,0.1), 0 1px 4px -1px rgba(0,0,0,0.06);
  overflow: hidden;
  z-index: 50;
}
.dark .dd-panel {
  background-color: #161b27;
  border-color: #1e2330;
  box-shadow: 0 8px 24px rgba(0,0,0,0.4);
}

/* Dropdown header */
.dd-head {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 14px;
  background-color: #f9fafb;
  border-bottom: 1px solid #e5e7eb;
}
.dark .dd-head { background-color: #1a1f2e; border-bottom-color: #1e2330; }

.dd-avatar {
  width: 32px;
  height: 32px;
  border-radius: 7px;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  color: #fff;
  font-size: 13px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.dd-meta { min-width: 0; }

.dd-name {
  font-size: 13px;
  font-weight: 600;
  color: #111827;
  margin: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.dark .dd-name { color: #f9fafb; }

.dd-email {
  font-size: 11px;
  color: #9ca3af;
  margin: 1px 0 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Dropdown body/footer */
.dd-body, .dd-footer { padding: 5px; }

.dd-sep { height: 1px; background-color: #f3f4f6; margin: 0 5px; }
.dark .dd-sep { background-color: #1e2330; }

/* Dropdown items */
.dd-item {
  display: flex;
  align-items: center;
  gap: 9px;
  width: 100%;
  padding: 8px 10px;
  font-size: 13px;
  font-weight: 500;
  color: #374151;
  border-radius: 7px;
  text-decoration: none;
  border: none;
  background: transparent;
  cursor: pointer;
  transition: background-color 0.15s, color 0.15s;
  text-align: left;
}
.dd-item:hover { background-color: #f3f4f6; color: #111827; }
.dark .dd-item { color: #9ca3af; }
.dark .dd-item:hover { background-color: #1e2330; color: #f9fafb; }

.dd-item--danger { color: #dc2626; }
.dark .dd-item--danger { color: #f87171; }
.dd-item--danger:hover { background-color: #fef2f2; color: #dc2626; }
.dark .dd-item--danger:hover { background-color: rgba(220,38,38,0.1); color: #f87171; }

.dd-item-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 26px;
  height: 26px;
  border-radius: 6px;
  background-color: #f3f4f6;
  color: #6b7280;
  flex-shrink: 0;
}
.dark .dd-item-icon { background-color: #252b3b; color: #6b7280; }
.dd-item:hover .dd-item-icon { background-color: #e5e7eb; }
.dark .dd-item:hover .dd-item-icon { background-color: #2d3548; color: #d1d5db; }
.dd-item--danger .dd-item-icon { background-color: #fef2f2; color: #dc2626; }
.dark .dd-item--danger .dd-item-icon { background-color: rgba(220,38,38,0.1); color: #f87171; }

/* ─── Dropdown transition ────────────────────────────────────────────────────── */
.dd-enter-active, .dd-leave-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}
.dd-enter-from, .dd-leave-to {
  opacity: 0;
  transform: translateY(-4px) scale(0.98);
}
</style>
