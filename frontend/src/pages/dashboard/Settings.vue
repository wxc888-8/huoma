<script setup lang="ts">
import { computed, reactive, ref } from 'vue'
import { ElMessage } from 'element-plus'
import { useAuthStore } from '@/stores/auth'
import { api } from '@/lib/api'

const auth = useAuthStore()

const saving = ref(false)
const form = reactive({
  name: '',
  mail: '',
  qq: '',
  pwd: '',
})

const user = computed(() => auth.user)

const init = () => {
  form.name = user.value?.name ?? ''
  form.mail = user.value?.mail ?? ''
  form.qq = ''
  form.pwd = ''
}

const save = async () => {
  saving.value = true
  try {
    const payload: any = {
      name: form.name,
      mail: form.mail,
      qq: form.qq,
    }
    if (form.pwd) payload.pwd = form.pwd
    const { data } = await api.post('/user/update-profile', payload)
    if (data?.code !== 200) throw new Error(data?.msg ?? '保存失败')
    if (data?.data?.token) {
      auth.setToken(data.data.token)
    }
    await auth.fetchMe()
    init()
    ElMessage.success('保存成功')
  } catch (e) {
    ElMessage.error(e instanceof Error ? e.message : '保存失败')
  } finally {
    saving.value = false
  }
}

init()
</script>

<template>
  <div class="space-y-5">
    <div>
      <h1 class="text-2xl font-bold text-slate-800 tracking-tight">账号设置</h1>
      <p class="text-sm text-slate-500 mt-1">修改昵称/邮箱/密码</p>
    </div>

    <div class="bg-white border border-slate-100 rounded-xl shadow-sm p-6 max-w-2xl">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">昵称</div>
          <input v-model="form.name" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow" />
        </div>
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">邮箱</div>
          <input v-model="form.mail" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow" />
        </div>
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">QQ(可选)</div>
          <input v-model="form.qq" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow" />
        </div>
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">新密码(可选)</div>
          <input v-model="form.pwd" type="password" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow" />
        </div>
      </div>
      <div class="mt-6 flex justify-end">
        <el-button type="primary" :loading="saving" @click="save">保存</el-button>
      </div>
    </div>
  </div>
</template>
