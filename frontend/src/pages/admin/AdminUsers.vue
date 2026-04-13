<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue'
import { ElMessage } from 'element-plus'
import { Icon } from '@iconify/vue'
import { api, withAdminAuth } from '@/lib/api'
import { useAdminAuthStore } from '@/stores/adminAuth'

type UserRow = {
  id: number
  user: string
  name: string
  mail: string
  qq: string
  points: number
  vip: string | null
  state: number
  addtime: string | null
  lasttime: string | null
}

const adminAuth = useAdminAuthStore()
const loading = ref(false)
const dialogOpen = ref(false)
const saving = ref(false)

const query = reactive({
  kw: '',
  page: 1,
  limit: 15,
})

const total = ref(0)
const rows = ref<UserRow[]>([])
const offset = computed(() => (query.page - 1) * query.limit)

const editForm = reactive({
  id: 0,
  name: '',
  mail: '',
  qq: '',
  points: 0,
  vip: '',
  state: 1,
  pwd: '',
})

const fetchList = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/admin/users/list', {
      params: { kw: query.kw || undefined, limit: query.limit, offset: offset.value },
      ...withAdminAuth(adminAuth.token),
    })
    if (data?.code !== 200) throw new Error(data?.msg ?? '加载失败')
    total.value = data?.data?.total ?? 0
    rows.value = data?.data?.rows ?? []
  } catch (e) {
    ElMessage.error(e instanceof Error ? e.message : '加载失败')
  } finally {
    loading.value = false
  }
}

const openEdit = (row: UserRow) => {
  dialogOpen.value = true
  editForm.id = row.id
  editForm.name = row.name ?? ''
  editForm.mail = row.mail ?? ''
  editForm.qq = row.qq ?? ''
  editForm.points = row.points ?? 0
  editForm.vip = row.vip ?? ''
  editForm.state = row.state ?? 1
  editForm.pwd = ''
}

const save = async () => {
  saving.value = true
  try {
    const payload: any = {
      id: editForm.id,
      name: editForm.name,
      mail: editForm.mail,
      qq: editForm.qq,
      points: editForm.points,
      vip: editForm.vip,
      state: editForm.state,
    }
    if (editForm.pwd) payload.pwd = editForm.pwd
    const { data } = await api.post('/admin/users/update', payload, withAdminAuth(adminAuth.token))
    if (data?.code !== 200) throw new Error(data?.msg ?? '保存失败')
    ElMessage.success('保存成功')
    dialogOpen.value = false
    await fetchList()
  } catch (e) {
    ElMessage.error(e instanceof Error ? e.message : '保存失败')
  } finally {
    saving.value = false
  }
}

onMounted(fetchList)
</script>

<template>
  <div class="space-y-5">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">用户管理</h1>
        <p class="text-sm text-slate-300 mt-1">用户状态、积分、VIP、资料与密码</p>
      </div>
    </div>

    <div class="rounded-2xl border border-white/10 bg-white/5 shadow-sm">
      <div class="p-4 flex flex-wrap items-center gap-3 border-b border-white/10">
        <div class="relative flex-1 min-w-[220px]">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <Icon icon="lucide:search" class="w-4 h-4 text-white/40" />
          </div>
          <input
            v-model="query.kw"
            class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-white/10 bg-white/5 text-white placeholder:text-white/30 focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition-shadow"
            placeholder="搜索用户名/邮箱/QQ/昵称"
            @keyup.enter="() => { query.page = 1; fetchList() }"
          />
        </div>
        <button
          class="px-4 py-2.5 rounded-lg border border-white/10 text-slate-200 font-medium hover:bg-white/5 transition-colors flex items-center"
          :disabled="loading"
          @click="() => { query.page = 1; fetchList() }"
        >
          <Icon icon="lucide:refresh-cw" class="w-4 h-4 mr-2" />
          刷新
        </button>
      </div>

      <div class="p-4">
        <el-table :data="rows" v-loading="loading" row-key="id" class="rounded-lg overflow-hidden !bg-transparent">
          <el-table-column prop="id" label="ID" width="80" />
          <el-table-column prop="user" label="用户名" width="160" />
          <el-table-column prop="name" label="昵称" width="160" />
          <el-table-column prop="mail" label="邮箱" min-width="220" />
          <el-table-column prop="points" label="积分" width="110" />
          <el-table-column prop="vip" label="VIP到期" width="180" />
          <el-table-column label="状态" width="100">
            <template #default="{ row }">
              <el-tag :type="row.state === 1 ? 'success' : 'danger'">{{ row.state === 1 ? '正常' : '封禁' }}</el-tag>
            </template>
          </el-table-column>
          <el-table-column label="操作" width="120" fixed="right">
            <template #default="{ row }">
              <button class="px-3 py-1.5 rounded-lg border border-white/10 hover:bg-white/5 text-slate-200 transition-colors" @click="openEdit(row)">
                编辑
              </button>
            </template>
          </el-table-column>
        </el-table>

        <div class="mt-4 flex items-center justify-between">
          <div class="text-sm text-slate-300">共 {{ total }} 条</div>
          <el-pagination
            background
            layout="prev, pager, next, sizes"
            :total="total"
            :page-size="query.limit"
            :current-page="query.page"
            :page-sizes="[10, 15, 20, 30, 50]"
            @update:current-page="(p: number) => { query.page = p; fetchList() }"
            @update:page-size="(s: number) => { query.limit = s; query.page = 1; fetchList() }"
          />
        </div>
      </div>
    </div>

    <el-dialog v-model="dialogOpen" title="编辑用户" width="620px" align-center>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">昵称</div>
          <input v-model="editForm.name" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow" />
        </div>
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">邮箱</div>
          <input v-model="editForm.mail" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow" />
        </div>
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">QQ</div>
          <input v-model="editForm.qq" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow" />
        </div>
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">积分</div>
          <el-input-number v-model="editForm.points" :min="0" :max="100000000" class="w-full" />
        </div>
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">VIP到期</div>
          <input v-model="editForm.vip" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow" placeholder="YYYY-MM-DD HH:mm:ss" />
        </div>
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">状态</div>
          <el-select v-model="editForm.state" class="w-full">
            <el-option :value="1" label="正常" />
            <el-option :value="0" label="封禁" />
          </el-select>
        </div>
        <div class="md:col-span-2">
          <div class="text-sm font-medium text-slate-700 mb-1">重置密码(可选)</div>
          <input v-model="editForm.pwd" type="password" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow" />
        </div>
      </div>

      <template #footer>
        <div class="flex items-center justify-end gap-2">
          <el-button @click="dialogOpen = false">取消</el-button>
          <el-button type="primary" :loading="saving" @click="save">保存</el-button>
        </div>
      </template>
    </el-dialog>
  </div>
</template>

