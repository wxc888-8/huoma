<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Icon } from '@iconify/vue'
import { api, withAdminAuth } from '@/lib/api'
import { useAdminAuthStore } from '@/stores/adminAuth'

type DomainRow = {
  id: number
  domain: string
  type: number
  state: number
  is_https: number
  qqsafe: number
  wxsafe: number
  dysafe: number
  addtime: string
}

const adminAuth = useAdminAuthStore()
const loading = ref(false)
const dialogOpen = ref(false)
const saving = ref(false)

const rows = ref<DomainRow[]>([])

const form = reactive({
  id: 0,
  domain: '',
  type: 0,
  state: 1,
  is_https: 0,
  qqsafe: 1,
  wxsafe: 1,
  dysafe: 1,
})

const load = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/admin/domains/system/list', withAdminAuth(adminAuth.token))
    if (data?.code !== 200) throw new Error(data?.msg ?? '加载失败')
    rows.value = data?.data?.rows ?? []
  } catch (e) {
    ElMessage.error(e instanceof Error ? e.message : '加载失败')
  } finally {
    loading.value = false
  }
}

const openCreate = () => {
  dialogOpen.value = true
  form.id = 0
  form.domain = ''
  form.type = 0
  form.state = 1
  form.is_https = 0
  form.qqsafe = 1
  form.wxsafe = 1
  form.dysafe = 1
}

const openEdit = (row: DomainRow) => {
  dialogOpen.value = true
  form.id = row.id
  form.domain = row.domain
  form.type = row.type
  form.state = row.state
  form.is_https = row.is_https
  form.qqsafe = row.qqsafe
  form.wxsafe = row.wxsafe
  form.dysafe = row.dysafe
}

const save = async () => {
  if (!form.domain) {
    ElMessage.warning('域名不能为空')
    return
  }
  saving.value = true
  try {
    const { data } = await api.post('/admin/domains/system/save', form, withAdminAuth(adminAuth.token))
    if (data?.code !== 200) throw new Error(data?.msg ?? '保存失败')
    ElMessage.success('保存成功')
    dialogOpen.value = false
    await load()
  } catch (e) {
    ElMessage.error(e instanceof Error ? e.message : '保存失败')
  } finally {
    saving.value = false
  }
}

const remove = async (id: number) => {
  try {
    await ElMessageBox.confirm('确认删除该域名吗？', '删除确认', {
      confirmButtonText: '删除',
      cancelButtonText: '取消',
      type: 'warning',
    })
  } catch {
    return
  }
  try {
    const { data } = await api.post('/admin/domains/system/delete', { id }, withAdminAuth(adminAuth.token))
    if (data?.code !== 200) throw new Error(data?.msg ?? '删除失败')
    ElMessage.success('已删除')
    await load()
  } catch (e) {
    ElMessage.error(e instanceof Error ? e.message : '删除失败')
  }
}

const typeLabel = (t: number) => {
  const m: Record<number, string> = {
    0: '默认',
    2: '跳转域名(模式1)',
    4: '跳转域名(模式2)',
    6: '跳转域名(模式3)',
    8: '其他',
  }
  return m[t] ?? `type=${t}`
}

const activeRows = computed(() => rows.value)

onMounted(load)
</script>

<template>
  <div class="space-y-5">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">系统域名池</h1>
        <p class="text-sm text-slate-300 mt-1">dwz_domain：短链生成与跳转域名池</p>
      </div>

      <button
        class="shrink-0 bg-indigo-500 text-white px-4 py-2 rounded-lg font-medium shadow-sm hover:bg-indigo-600 transition-colors flex items-center"
        @click="openCreate"
      >
        <Icon icon="lucide:plus" class="w-5 h-5 mr-1" />
        新增域名
      </button>
    </div>

    <div class="rounded-2xl border border-white/10 bg-white/5 shadow-sm">
      <div class="p-4">
        <el-table :data="activeRows" v-loading="loading" row-key="id" class="rounded-lg overflow-hidden !bg-transparent">
          <el-table-column prop="id" label="ID" width="90" />
          <el-table-column prop="domain" label="域名" min-width="260" />
          <el-table-column label="类型" width="160">
            <template #default="{ row }">
              <span class="text-slate-200">{{ typeLabel(row.type) }}</span>
            </template>
          </el-table-column>
          <el-table-column label="状态" width="100">
            <template #default="{ row }">
              <el-tag :type="row.state === 1 ? 'success' : 'info'">{{ row.state === 1 ? '启用' : '停用' }}</el-tag>
            </template>
          </el-table-column>
          <el-table-column label="HTTPS" width="100">
            <template #default="{ row }">
              <el-tag :type="row.is_https === 1 ? 'success' : 'warning'">{{ row.is_https === 1 ? '是' : '否' }}</el-tag>
            </template>
          </el-table-column>
          <el-table-column prop="addtime" label="添加时间" width="180" />
          <el-table-column label="操作" width="160" fixed="right">
            <template #default="{ row }">
              <div class="flex items-center gap-2">
                <button class="px-3 py-1.5 rounded-lg border border-white/10 hover:bg-white/5 text-slate-200 transition-colors" @click="openEdit(row)">
                  编辑
                </button>
                <button class="px-3 py-1.5 rounded-lg border border-red-500/20 hover:bg-red-500/10 text-red-200 transition-colors" @click="remove(row.id)">
                  删除
                </button>
              </div>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </div>

    <el-dialog v-model="dialogOpen" :title="form.id ? '编辑域名' : '新增域名'" width="680px" align-center>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
          <div class="text-sm font-medium text-slate-700 mb-1">域名</div>
          <input v-model="form.domain" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow" placeholder="example.com 或 *.example.com" />
        </div>
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">类型</div>
          <el-input-number v-model="form.type" :min="0" :max="99" class="w-full" />
        </div>
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">启用</div>
          <el-select v-model="form.state" class="w-full">
            <el-option :value="1" label="启用" />
            <el-option :value="0" label="停用" />
          </el-select>
        </div>
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">HTTPS</div>
          <el-select v-model="form.is_https" class="w-full">
            <el-option :value="1" label="是" />
            <el-option :value="0" label="否" />
          </el-select>
        </div>
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">QQ安全</div>
          <el-select v-model="form.qqsafe" class="w-full">
            <el-option :value="1" label="正常" />
            <el-option :value="0" label="风险" />
          </el-select>
        </div>
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">微信安全</div>
          <el-select v-model="form.wxsafe" class="w-full">
            <el-option :value="1" label="正常" />
            <el-option :value="0" label="风险" />
          </el-select>
        </div>
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">抖音安全</div>
          <el-select v-model="form.dysafe" class="w-full">
            <el-option :value="1" label="正常" />
            <el-option :value="0" label="风险" />
          </el-select>
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

