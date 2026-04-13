<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { Icon } from '@iconify/vue'
import { useAdminAuthStore } from '@/stores/adminAuth'

const router = useRouter()
const route = useRoute()
const adminAuth = useAdminAuthStore()
const isCollapsed = ref(false)

const menuItems = [
  { path: '/admin', label: '系统总览', icon: 'lucide:layout-dashboard' },
  { path: '/admin/users', label: '用户管理', icon: 'lucide:users' },
  { path: '/admin/domains', label: '系统域名池', icon: 'lucide:globe' },
  { path: '/admin/domain-pool', label: '入口/落地域名', icon: 'lucide:route' },
  { path: '/admin/blacklist', label: '黑名单', icon: 'lucide:ban' },
  { path: '/admin/packages', label: '积分套餐', icon: 'lucide:package' },
  { path: '/admin/withdraw', label: '提现审核', icon: 'lucide:hand-coins' },
]

const handleLogout = () => {
  adminAuth.clear()
  router.push('/admin/login')
}

const adminUser = computed(() => adminAuth.adminUser || 'admin')

onMounted(async () => {
  if (adminAuth.token) {
    try {
      await adminAuth.fetchMe()
    } catch {
      adminAuth.clear()
      router.push('/admin/login')
    }
  }
})
</script>

<template>
  <div class="flex h-screen w-full bg-slate-950 text-slate-100">
    <aside
      :class="[
        'flex flex-col bg-slate-950/80 border-r border-white/10 transition-all duration-300 ease-in-out backdrop-blur',
        isCollapsed ? 'w-20' : 'w-64'
      ]"
    >
      <div class="h-16 flex items-center justify-center border-b border-white/10">
        <Icon icon="lucide:shield" class="w-8 h-8 text-indigo-400" />
        <span v-if="!isCollapsed" class="ml-3 font-bold text-lg text-white tracking-tight">控制台</span>
      </div>

      <div class="flex-1 overflow-y-auto py-4">
        <nav class="space-y-1 px-3">
          <router-link
            v-for="item in menuItems"
            :key="item.path"
            :to="item.path"
            class="flex items-center px-3 py-2.5 rounded-lg transition-colors group relative"
            :class="[
              route.path === item.path
                ? 'bg-indigo-500/15 text-indigo-200 font-medium'
                : 'text-slate-300 hover:bg-white/5 hover:text-white'
            ]"
          >
            <Icon :icon="item.icon" :class="['w-5 h-5', route.path === item.path ? 'text-indigo-300' : 'text-slate-400 group-hover:text-slate-200']" />
            <span v-if="!isCollapsed" class="ml-3">{{ item.label }}</span>
          </router-link>
        </nav>
      </div>

      <div class="p-4 border-t border-white/10">
        <button
          @click="handleLogout"
          class="flex items-center w-full px-3 py-2.5 text-slate-300 rounded-lg hover:bg-red-500/10 hover:text-red-200 transition-colors group"
        >
          <Icon icon="lucide:log-out" class="w-5 h-5 text-slate-400 group-hover:text-red-300" />
          <span v-if="!isCollapsed" class="ml-3 font-medium">退出登录</span>
        </button>
      </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <header class="h-16 bg-slate-950/70 border-b border-white/10 flex items-center justify-between px-6 z-10 backdrop-blur">
        <button
          @click="isCollapsed = !isCollapsed"
          class="p-2 rounded-md hover:bg-white/5 text-slate-300 transition-colors"
        >
          <Icon :icon="isCollapsed ? 'lucide:menu' : 'lucide:menu-square'" class="w-5 h-5" />
        </button>

        <div class="flex items-center space-x-4">
          <div class="text-sm font-medium bg-white/5 text-slate-200 px-3 py-1 rounded-full border border-white/10">
            {{ adminUser }}
          </div>
          <div class="h-8 w-8 rounded-full bg-gradient-to-tr from-indigo-500 to-blue-500 text-white flex items-center justify-center font-bold text-sm shadow-md">
            A
          </div>
        </div>
      </header>

      <main class="flex-1 overflow-y-auto p-6 bg-slate-950">
        <div class="max-w-7xl mx-auto w-full">
          <router-view v-slot="{ Component }">
            <transition name="fade" mode="out-in">
              <component :is="Component" />
            </transition>
          </router-view>
        </div>
      </main>
    </div>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
