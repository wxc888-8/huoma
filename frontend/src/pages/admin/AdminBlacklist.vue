<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Icon } from '@iconify/vue'
import { api, withAdminAuth } from '@/lib/api'
import { useAdminAuthStore } from '@/stores/adminAuth'

type Row = { id: number; content: string; type: number; addtime: string }

const adminAuth = useAdminAuthStore()
const loading = ref(false)
const rows = ref<Row[]>([])

const form = reactive({
  content: '',
  type: 1,
})

const typeOptions = [
  { value: 0, label: 'IP' },
  { value: 1, label: 'URL(编码后)' },
  { value: 2, label: '域名' },
]

const load = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/admin/blacklist/list', {
      params: { type: form.type },
      ...withAdminAuth(adminAuth.token),
    })
    if (data?.code !== 200) throw new Error(data?.msg ?? '加载失败')
    rows.value = data?.data?.rows ?? []
  } catch (e) {
    ElMessage.error(e instanceof Error ? e.message : '加载失败')
  } finally {
    loading.value = false
  }
}

const add = async () => {
  if (!form.content) {
    ElMessage.warning('内容不能为空')
    return
  }
  try {
    const { data } = await api.post('/admin/blacklist/add', form, withAdminAuth(adminAuth.token))
    if (data?.code !== 200) throw new Error(data?.msg ?? '添加失败')
    ElMessage.success('添加成功')
    form.content = ''
    await load()
  } catch (e) {
    ElMessage.error(e instanceof Error ? e.message : '添加失败')
  }
}

const remove = async (id: number) => {
  try {
    await ElMessageBox.confirm('确认删除该条目吗？', '删除确认', {
      confirmButtonText: '删除',
      cancelButtonText: '取消',
      type: 'warning',
    })
  } catch {
    return
  }
  try {
    const { data } = await api.post('/admin/blacklist/delete', { id }, withAdminAuth(adminAuth.token))
    if (data?.code !== 200) throw new Error(data?.msg ?? '删除失败')
    ElMessage.success('已删除')
    await load()
  } catch (e) {
    ElMessage.error(e instanceof Error ? e.message : '删除失败')
  }
}

const typeLabel = computed(() => typeOptions.find((t) => t.value === form.type)?.label ?? '')

onMounted(load)
</script>

<template>
  <div class="space-y-5">
    <div>
      <h1 class="text-2xl font-bold text-white tracking-tight">黑名单</h1>
      <p class="text-sm text-slate-300 mt-1">封禁 IP / URL / 域名（与短链/活码生成逻辑联动）</p>
    </div>

    <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
      <div class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
        <div class="md:col-span-1">
          <div class="text-sm font-medium text-slate-200 mb-1">类型</div>
          <el-select v-model="form.type" class="w-full" @change="load">
            <el-option v-for="t in typeOptions" :key="t.value" :label="t.label" :value="t.value" />
          </el-select>
        </div>
        <div class="md:col-span-4">
          <div class="text-sm font-medium text-slate-200 mb-1">内容</div>
          <input
            v-model="form.content"
            class="w-full px-3 py-2.5 rounded-lg border border-white/10 bg-white/5 text-white placeholder:text-white/30 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition-shadow"
            :placeholder="`输入要拉黑的${typeLabel}`"
          />
        </div>
        <div class="md:col-span-1">
          <button
            class="w-full bg-indigo-500 text-white px-4 py-2.5 rounded-lg font-medium hover:bg-indigo-600 transition-colors flex items-center justify-center"
            @click="add"
          >
            <Icon icon="lucide:plus" class="w-4 h-4 mr-2" />
            添加
          </button>
        </div>
      </div>
    </div>

    <div class="rounded-2xl border border-white/10 bg-white/5 shadow-sm">
      <div class="p-4">
        <el-table :data="rows" v-loading="loading" row-key="id" class="rounded-lg overflow-hidden !bg-transparent">
          <el-table-column prop="id" label="ID" width="90" />
          <el-table-column prop="content" label="内容" min-width="420" />
          <el-table-column prop="addtime" label="添加时间" width="180" />
          <el-table-column label="操作" width="120" fixed="right">
            <template #default="{ row }">
              <button class="px-3 py-1.5 rounded-lg border border-red-500/20 hover:bg-red-500/10 text-red-200 transition-colors" @click="remove(row.id)">
                删除
              </button>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </div>
  </div>
</template>

