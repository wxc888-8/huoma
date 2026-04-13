<script setup lang="ts">
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { Icon } from '@iconify/vue'
import { ElMessage } from 'element-plus'

const router = useRouter()
const isLogin = ref(true)
const loading = ref(false)

const form = reactive({
  email: '',
  password: '',
  confirmPassword: ''
})

const handleSubmit = async () => {
  if (!form.email || !form.password) {
    ElMessage.warning('请填写邮箱和密码')
    return
  }
  
  if (!isLogin.value && form.password !== form.confirmPassword) {
    ElMessage.error('两次密码不一致')
    return
  }

  loading.value = true
  // Mock API call
  setTimeout(() => {
    loading.value = false
    localStorage.setItem('token', 'mock_token')
    ElMessage.success(isLogin.value ? '登录成功' : '注册成功')
    router.push('/dashboard')
  }, 1000)
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 p-4">
    <div class="absolute inset-0 z-0">
      <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-blue-300/30 blur-3xl"></div>
      <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] rounded-full bg-indigo-300/30 blur-3xl"></div>
    </div>

    <div class="w-full max-w-md bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/50 p-8 relative z-10">
      <div class="flex flex-col items-center mb-8">
        <div class="w-12 h-12 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg mb-4">
          <Icon icon="lucide:shield-check" class="w-7 h-7 text-white" />
        </div>
        <h2 class="text-2xl font-bold text-slate-800">{{ isLogin ? '登录管理后台' : '注册新账号' }}</h2>
        <p class="text-slate-500 text-sm mt-2">防封活码系统・稳定安全</p>
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-5">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">邮箱账号</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <Icon icon="lucide:mail" class="h-5 w-5 text-slate-400" />
            </div>
            <input 
              v-model="form.email"
              type="email" 
              class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/50 transition-shadow"
              placeholder="admin@example.com"
            />
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between mb-1">
            <label class="block text-sm font-medium text-slate-700">密码</label>
            <a v-if="isLogin" href="#" class="text-sm font-medium text-blue-600 hover:text-blue-500">忘记密码?</a>
          </div>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <Icon icon="lucide:lock" class="h-5 w-5 text-slate-400" />
            </div>
            <input 
              v-model="form.password"
              type="password" 
              class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/50 transition-shadow"
              placeholder="••••••••"
            />
          </div>
        </div>

        <div v-if="!isLogin">
          <label class="block text-sm font-medium text-slate-700 mb-1">确认密码</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <Icon icon="lucide:lock-keyhole" class="h-5 w-5 text-slate-400" />
            </div>
            <input 
              v-model="form.confirmPassword"
              type="password" 
              class="block w-full pl-10 pr-3 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white/50 transition-shadow"
              placeholder="••••••••"
            />
          </div>
        </div>

        <button 
          type="submit"
          :disabled="loading"
          class="w-full flex justify-center items-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-70"
        >
          <Icon v-if="loading" icon="lucide:loader-2" class="animate-spin -ml-1 mr-2 h-5 w-5" />
          {{ isLogin ? '立即登录' : '注册账号' }}
        </button>
      </form>

      <div class="mt-6 text-center">
        <p class="text-sm text-slate-600">
          {{ isLogin ? '没有账号?' : '已有账号?' }}
          <button 
            @click="isLogin = !isLogin"
            class="font-medium text-blue-600 hover:text-blue-500 ml-1 focus:outline-none"
          >
            {{ isLogin ? '免费注册' : '返回登录' }}
          </button>
        </p>
      </div>
    </div>
  </div>
</template>
