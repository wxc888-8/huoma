<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { Icon } from '@iconify/vue'
import { ElMessage } from 'element-plus'
import { api, withAdminAuth } from '@/lib/api'
import { useAdminAuthStore } from '@/stores/adminAuth'

const adminAuth = useAdminAuthStore()
const loading = ref(false)

const stats = ref([
  { title: '用户数', value: '0', icon: 'lucide:users', color: 'text-indigo-300', bg: 'bg-indigo-500/10' },
  { title: '短链数', value: '0', icon: 'lucide:link', color: 'text-blue-300', bg: 'bg-blue-500/10' },
  { title: '已支付订单', value: '0', icon: 'lucide:receipt', color: 'text-emerald-300', bg: 'bg-emerald-500/10' },
  { title: '待审核提现', value: '0', icon: 'lucide:hand-coins', color: 'text-amber-300', bg: 'bg-amber-500/10' },
])

const load = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/admin/stats', withAdminAuth(adminAuth.token))
    if (data?.code !== 200) throw new Error(data?.msg ?? '加载失败')
    const d = data?.data ?? {}
    stats.value = [
      { title: '用户数', value: String(d.users ?? 0), icon: 'lucide:users', color: 'text-indigo-300', bg: 'bg-indigo-500/10' },
      { title: '短链数', value: String(d.urls ?? 0), icon: 'lucide:link', color: 'text-blue-300', bg: 'bg-blue-500/10' },
      { title: '已支付订单', value: String(d.orders_paid ?? 0), icon: 'lucide:receipt', color: 'text-emerald-300', bg: 'bg-emerald-500/10' },
      { title: '待审核提现', value: String(d.withdraw_pending ?? 0), icon: 'lucide:hand-coins', color: 'text-amber-300', bg: 'bg-amber-500/10' },
    ]
  } catch (e) {
    ElMessage.error(e instanceof Error ? e.message : '加载失败')
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-white tracking-tight">系统总览</h1>
      <button
        class="px-4 py-2 rounded-lg border border-white/10 text-slate-200 font-medium hover:bg-white/5 transition-colors flex items-center"
        :disabled="loading"
        @click="load"
      >
        <Icon icon="lucide:refresh-cw" class="w-4 h-4 mr-2" />
        刷新
      </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
      <div
        v-for="(s, idx) in stats"
        :key="idx"
        class="rounded-2xl border border-white/10 bg-white/5 p-5 shadow-sm hover:bg-white/7 transition-colors relative overflow-hidden"
      >
        <div class="absolute -right-6 -top-6 w-24 h-24 rounded-full opacity-20" :class="s.bg"></div>
        <div class="relative z-10 flex items-center justify-between">
          <div>
            <div class="text-sm text-slate-300 font-medium">{{ s.title }}</div>
            <div class="mt-2 text-3xl font-bold text-white">{{ s.value }}</div>
          </div>
          <div :class="['w-12 h-12 rounded-xl flex items-center justify-center border border-white/10', s.bg]">
            <Icon :icon="s.icon" :class="['w-6 h-6', s.color]" />
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
      <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
        <div class="font-bold text-white mb-2">说明</div>
        <div class="text-sm text-slate-300 leading-6">
          <div>管理员后台已接入新 API，可用于用户管理、域名池管理、黑名单与提现审核。</div>
          <div>旧版 /admin 仍保留备用，建议先灰度并行一段时间再切换入口。</div>
        </div>
      </div>

      <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
        <div class="font-bold text-white mb-2">安全提示</div>
        <div class="text-sm text-slate-300 leading-6">
          <div>管理员 token 使用原有 authcode 机制，务必在宝塔/Nginx 配置 HTTPS。</div>
          <div>建议后续将 admin_pwd 升级为哈希存储，并增加二次验证。</div>
        </div>
      </div>
    </div>
  </div>
</template>

