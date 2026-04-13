<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { ElMessage } from 'element-plus'
import { Icon } from '@iconify/vue'
import { useAdminAuthStore } from '@/stores/adminAuth'

const router = useRouter()
const adminAuth = useAdminAuthStore()
const loading = ref(false)

const form = reactive({
  user: '',
  password: '',
})

const submit = async () => {
  if (!form.user || !form.password) {
    ElMessage.warning('请填写管理员账号和密码')
    return
  }
  loading.value = true
  try {
    await adminAuth.login({ user: form.user, password: form.password })
    ElMessage.success('登录成功')
    router.push('/admin')
  } catch (e) {
    ElMessage.error(e instanceof Error ? e.message : '登录失败')
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 p-4">
    <div class="absolute inset-0 z-0">
      <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-indigo-500/20 blur-3xl"></div>
      <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-blue-500/20 blur-3xl"></div>
    </div>

    <div class="w-full max-w-md bg-white/10 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/10 p-8 relative z-10 text-white">
      <div class="flex flex-col items-center mb-8">
        <div class="w-12 h-12 bg-gradient-to-tr from-indigo-500 to-blue-500 rounded-xl flex items-center justify-center shadow-lg mb-4">
          <Icon icon="lucide:shield" class="w-7 h-7 text-white" />
        </div>
        <h2 class="text-2xl font-bold tracking-tight">管理员登录</h2>
        <p class="text-white/70 text-sm mt-2">系统控制台・高权限操作</p>
      </div>

      <form @submit.prevent="submit" class="space-y-5">
        <div>
          <label class="block text-sm font-medium text-white/80 mb-1">账号</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <Icon icon="lucide:user" class="h-5 w-5 text-white/40" />
            </div>
            <input
              v-model="form.user"
              class="block w-full pl-10 pr-3 py-2.5 border border-white/10 rounded-lg focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 bg-white/10 text-white placeholder:text-white/40 transition-shadow"
              placeholder="admin"
            />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-white/80 mb-1">密码</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <Icon icon="lucide:lock" class="h-5 w-5 text-white/40" />
            </div>
            <input
              v-model="form.password"
              type="password"
              class="block w-full pl-10 pr-3 py-2.5 border border-white/10 rounded-lg focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 bg-white/10 text-white placeholder:text-white/40 transition-shadow"
              placeholder="••••••••"
            />
          </div>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full flex justify-center items-center py-2.5 px-4 rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-500 hover:bg-indigo-600 transition-colors disabled:opacity-70"
        >
          <Icon v-if="loading" icon="lucide:loader-2" class="animate-spin -ml-1 mr-2 h-5 w-5" />
          登录控制台
        </button>
      </form>

      <div class="mt-6 text-center">
        <router-link to="/login" class="text-sm text-white/70 hover:text-white">返回用户登录</router-link>
      </div>
    </div>
  </div>
</template>

