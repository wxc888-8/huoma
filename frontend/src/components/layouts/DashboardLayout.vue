<script setup lang="ts">
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { Icon } from '@iconify/vue'

const router = useRouter()
const route = useRoute()
const isCollapsed = ref(false)

const menuItems = [
  { path: '/dashboard', label: '总览看板', icon: 'lucide:layout-dashboard' },
  { path: '/dashboard/links', label: '活码管理', icon: 'lucide:qr-code' },
  { path: '/dashboard/billing', label: '充值提现', icon: 'lucide:wallet' },
  { path: '/admin', label: '系统管理', icon: 'lucide:settings', adminOnly: true }
]

const handleLogout = () => {
  localStorage.removeItem('token')
  router.push('/login')
}
</script>

<template>
  <div class="flex h-screen w-full bg-slate-50 text-slate-900">
    <!-- Sidebar -->
    <aside
      :class="[
        'flex flex-col bg-white border-r border-slate-200 transition-all duration-300 ease-in-out',
        isCollapsed ? 'w-20' : 'w-64'
      ]"
    >
      <div class="h-16 flex items-center justify-center border-b border-slate-100">
        <Icon icon="lucide:shield-check" class="w-8 h-8 text-blue-600" />
        <span v-if="!isCollapsed" class="ml-3 font-bold text-lg text-slate-800 tracking-tight">活码管家</span>
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
                ? 'bg-blue-50 text-blue-700 font-medium' 
                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
            ]"
          >
            <Icon :icon="item.icon" :class="['w-5 h-5', route.path === item.path ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600']" />
            <span v-if="!isCollapsed" class="ml-3">{{ item.label }}</span>
          </router-link>
        </nav>
      </div>

      <div class="p-4 border-t border-slate-100">
        <button
          @click="handleLogout"
          class="flex items-center w-full px-3 py-2.5 text-slate-600 rounded-lg hover:bg-red-50 hover:text-red-600 transition-colors group"
        >
          <Icon icon="lucide:log-out" class="w-5 h-5 text-slate-400 group-hover:text-red-500" />
          <span v-if="!isCollapsed" class="ml-3 font-medium">退出登录</span>
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <!-- Header -->
      <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 z-10">
        <button 
          @click="isCollapsed = !isCollapsed"
          class="p-2 rounded-md hover:bg-slate-100 text-slate-500 transition-colors"
        >
          <Icon :icon="isCollapsed ? 'lucide:menu' : 'lucide:menu-square'" class="w-5 h-5" />
        </button>

        <div class="flex items-center space-x-4">
          <div class="text-sm font-medium bg-blue-50 text-blue-700 px-3 py-1 rounded-full">
            余额: <span class="font-bold">1280</span> 积分
          </div>
          <div class="h-8 w-8 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-500 text-white flex items-center justify-center font-bold text-sm shadow-md">
            U
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="flex-1 overflow-y-auto p-6 bg-slate-50/50">
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
