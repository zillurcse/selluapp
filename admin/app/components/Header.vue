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

        <!-- Notification Bell -->
        <div class="notif-wrap" ref="notifRef">
          <button
            class="hdr-icon-btn hdr-icon-btn--bell"
            title="Notifications"
            @click="toggleNotif"
          >
            <Bell :size="15" />
            <span v-if="notifStore.unreadCount > 0" class="notif-badge">
              {{ notifStore.unreadCount > 99 ? '99+' : notifStore.unreadCount }}
            </span>
          </button>

          <!-- Notification Dropdown -->
          <transition name="notif">
            <div v-if="notifStore.isOpen" class="notif-panel">
              <!-- Header -->
              <div class="notif-panel-head">
                <span class="notif-panel-title">Notifications</span>
                <button
                  v-if="notifStore.unreadCount > 0"
                  class="notif-mark-all"
                  @click="notifStore.markAllRead()"
                >
                  Mark all read
                </button>
              </div>

              <!-- Body: list -->
              <div class="notif-list">
                <div v-if="notifStore.notifications.length === 0" class="notif-empty">
                  <BellOff :size="28" class="notif-empty-icon" />
                  <span>No notifications yet</span>
                </div>

                <div
                  v-for="n in notifStore.notifications"
                  :key="n.id"
                  class="notif-item"
                  :class="{ 'notif-item--unread': !n.is_read }"
                  @click="handleNotifClick(n)"
                >
                  <div class="notif-icon-wrap" :class="n.type === 'new_order' ? 'notif-icon--order' : 'notif-icon--customer'">
                    <ShoppingBag v-if="n.type === 'new_order'" :size="14" />
                    <UserPlus v-else :size="14" />
                  </div>
                  <div class="notif-content">
                    <p class="notif-title">{{ n.title }}</p>
                    <p class="notif-msg">{{ n.message }}</p>
                    <span class="notif-time">{{ timeAgo(n.created_at) }}</span>
                  </div>
                  <div v-if="!n.is_read" class="notif-unread-dot"></div>
                </div>
              </div>

              <!-- Footer -->
              <div class="notif-panel-foot">
                <NuxtLink to="/vendor/orders" class="notif-foot-link" @click="notifStore.isOpen = false">
                  View all orders
                </NuxtLink>
              </div>
            </div>
          </transition>
        </div>
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
  BellOff,
  Menu,
  LogOut,
  User,
  ChevronRight,
  ShoppingBag,
  UserPlus,
} from 'lucide-vue-next'
import { ref, computed, onMounted, onUnmounted } from 'vue'

const auth = useAuthStore()
const { can } = usePermissions()
const router = useRouter()
const route = useRoute()
const config = useRuntimeConfig()
const sidebar = useSidebar()
const notifStore = useNotificationStore()

const isDropdownOpen = ref(false)
const dropdownRef = ref(null)
const notifRef = ref(null)

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
  if (isDropdownOpen.value) notifStore.isOpen = false
}

const toggleNotif = () => {
  notifStore.toggleOpen()
  if (notifStore.isOpen) isDropdownOpen.value = false
}

const closeDropdown = (e) => {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    isDropdownOpen.value = false
  }
  if (notifRef.value && !notifRef.value.contains(e.target)) {
    notifStore.isOpen = false
  }
}

const handleLogout = async () => {
  isDropdownOpen.value = false
  await auth.logout()
}

const handleNotifClick = async (n) => {
  if (!n.is_read) await notifStore.markRead(n.id)
  const link = n.data?.link
  if (link) {
    notifStore.isOpen = false
    router.push(link)
  }
}

/**
 * Simple "time ago" helper.
 * Returns e.g. "2m ago", "3h ago", "1d ago"
 */
const timeAgo = (dateStr) => {
  if (!dateStr) return ''
  const diff = Date.now() - new Date(dateStr).getTime()
  const mins = Math.floor(diff / 60000)
  if (mins < 1) return 'just now'
  if (mins < 60) return `${mins}m ago`
  const hrs = Math.floor(mins / 60)
  if (hrs < 24) return `${hrs}h ago`
  const days = Math.floor(hrs / 24)
  return `${days}d ago`
}

onMounted(() => {
  document.addEventListener('click', closeDropdown)
  notifStore.startPolling(30000)
})

onUnmounted(() => {
  document.removeEventListener('click', closeDropdown)
  notifStore.stopPolling()
})
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

/* ─── Notification Badge ─────────────────────────────────────────────────────── */
.notif-badge {
  position: absolute;
  top: 4px;
  right: 4px;
  min-width: 16px;
  height: 16px;
  padding: 0 3px;
  border-radius: 8px;
  background-color: #ef4444;
  border: 1.5px solid #fff;
  color: #fff;
  font-size: 9px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
}
.dark .notif-badge { border-color: #0f1117; }

/* ─── Notification Wrap ──────────────────────────────────────────────────────── */
.notif-wrap { position: relative; }

/* ─── Notification Panel ─────────────────────────────────────────────────────── */
.notif-panel {
  position: absolute;
  right: 0;
  top: calc(100% + 8px);
  width: 340px;
  background-color: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  box-shadow: 0 8px 24px -4px rgba(0, 0, 0, 0.12), 0 2px 8px -2px rgba(0, 0, 0, 0.06);
  z-index: 50;
  overflow: hidden;
}
.dark .notif-panel {
  background-color: #161b27;
  border-color: #1e2330;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
}

.notif-panel-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 13px 14px 10px;
  border-bottom: 1px solid #f3f4f6;
}
.dark .notif-panel-head { border-bottom-color: #1e2330; }

.notif-panel-title {
  font-size: 13px;
  font-weight: 700;
  color: #111827;
}
.dark .notif-panel-title { color: #f3f4f6; }

.notif-mark-all {
  font-size: 11px;
  font-weight: 500;
  color: #4f46e5;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  transition: color 0.15s;
}
.notif-mark-all:hover { color: #3730a3; }
.dark .notif-mark-all { color: #818cf8; }

/* ─── Notification List ──────────────────────────────────────────────────────── */
.notif-list {
  max-height: 320px;
  overflow-y: auto;
  scrollbar-width: thin;
}

.notif-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  padding: 32px 16px;
  color: #9ca3af;
  font-size: 13px;
}
.notif-empty-icon { color: #d1d5db; }
.dark .notif-empty-icon { color: #374151; }

/* ─── Notification Item ──────────────────────────────────────────────────────── */
.notif-item {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 10px 14px;
  cursor: pointer;
  border-bottom: 1px solid #f9fafb;
  transition: background-color 0.15s;
  position: relative;
}
.notif-item:last-child { border-bottom: none; }
.notif-item:hover { background-color: #f9fafb; }
.dark .notif-item { border-bottom-color: #1a1f2e; }
.dark .notif-item:hover { background-color: #1a1f2e; }

.notif-item--unread { background-color: #fafafe; }
.dark .notif-item--unread { background-color: #13182199; }

.notif-icon-wrap {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  margin-top: 1px;
}

.notif-icon--order {
  background-color: #ede9fe;
  color: #7c3aed;
}
.dark .notif-icon--order { background-color: rgba(124, 58, 237, 0.15); color: #a78bfa; }

.notif-icon--customer {
  background-color: #ecfdf5;
  color: #059669;
}
.dark .notif-icon--customer { background-color: rgba(5, 150, 105, 0.15); color: #34d399; }

.notif-content { flex: 1; min-width: 0; }

.notif-title {
  font-size: 12.5px;
  font-weight: 600;
  color: #111827;
  margin: 0 0 2px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.dark .notif-title { color: #f3f4f6; }

.notif-msg {
  font-size: 11.5px;
  color: #6b7280;
  margin: 0 0 3px;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.dark .notif-msg { color: #9ca3af; }

.notif-time {
  font-size: 10.5px;
  color: #9ca3af;
}

.notif-unread-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background-color: #4f46e5;
  flex-shrink: 0;
  margin-top: 6px;
}
.dark .notif-unread-dot { background-color: #818cf8; }

/* ─── Panel Footer ───────────────────────────────────────────────────────────── */
.notif-panel-foot {
  padding: 8px 14px;
  border-top: 1px solid #f3f4f6;
  text-align: center;
}
.dark .notif-panel-foot { border-top-color: #1e2330; }

.notif-foot-link {
  font-size: 12px;
  font-weight: 500;
  color: #4f46e5;
  text-decoration: none;
  transition: color 0.15s;
}
.notif-foot-link:hover { color: #3730a3; }
.dark .notif-foot-link { color: #818cf8; }

/* ─── Notification transition ────────────────────────────────────────────────── */
.notif-enter-active, .notif-leave-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}
.notif-enter-from, .notif-leave-to {
  opacity: 0;
  transform: translateY(-6px) scale(0.97);
}

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
