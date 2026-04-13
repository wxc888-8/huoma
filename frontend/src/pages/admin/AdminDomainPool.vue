<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Icon } from '@iconify/vue'
import { api, withAdminAuth } from '@/lib/api'
import { useAdminAuthStore } from '@/stores/adminAuth'

type Row = {
  id: number
  domain: string
  state: number
  qqsafe: number
  wxsafe: number
  is_paid: number
  price: number
  remark: string
  addtime: string
  uid: number
}

const adminAuth = useAdminAuthStore()
const tab = ref<'entry' | 'landing'>('entry')
const loading = ref(false)
const dialogOpen = ref(false)
const saving = ref(false)

const rows = ref<Row[]>([])

const form = reactive({
  id: 0,
  domain: '',
  state: 1,
  qqsafe: 1,
  wxsafe: 1,
  is_paid: 0,
  price: 0,
  remark: '',
})

const load = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/admin/domains/pool/list', {
      params: { type: tab.value },
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

const openCreate = () => {
  dialogOpen.value = true
  form.id = 0
  form.domain = ''
  form.state = 1
  form.qqsafe = 1
  form.wxsafe = 1
  form.is_paid = 0
  form.price = 0
  form.remark = ''
}

const openEdit = (row: Row) => {
  dialogOpen.value = true
  form.id = row.id
  form.domain = row.domain
  form.state = row.state
  form.qqsafe = row.qqsafe
  form.wxsafe = row.wxsafe
  form.is_paid = row.is_paid
  form.price = row.price
  form.remark = row.remark ?? ''
}

const save = async () => {
  if (!form.domain) {
    ElMessage.warning('域名不能为空')
    return
  }
  saving.value = true
  try {
    const payload = { type: tab.value, ...form }
    const { data } = await api.post('/admin/domains/pool/save', payload, withAdminAuth(adminAuth.token))
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
    const { data } = await api.post('/admin/domains/pool/delete', { type: tab.value, id }, withAdminAuth(adminAuth.token))
    if (data?.code !== 200) throw new Error(data?.msg ?? '删除失败')
    ElMessage.success('已删除')
    await load()
  } catch (e) {
    ElMessage.error(e instanceof Error ? e.message : '删除失败')
  }
}

const title = computed(() => (tab.value === 'entry' ? '入口域名池' : '落地域名池'))

onMounted(load)
</script>

<template>
  <div class="space-y-5">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">{{ title }}</h1>
        <p class="text-sm text-slate-300 mt-1">dwz_entry_domain / dwz_landing_domain</p>
      </div>
      <div class="flex items-center gap-2">
        <el-segmented
          v-model="tab"
          :options="[
            { label: '入口域名', value: 'entry' },
            { label: '落地域名', value: 'landing' },
          ]"
          @change="load"
        />
        <button
          class="shrink-0 bg-indigo-500 text-white px-4 py-2 rounded-lg font-medium shadow-sm hover:bg-indigo-600 transition-colors flex items-center"
          @click="openCreate"
        >
          <Icon icon="lucide:plus" class="w-5 h-5 mr-1" />
          新增
        </button>
      </div>
    </div>

    <div class="rounded-2xl border border-white/10 bg-white/5 shadow-sm">
      <div class="p-4">
        <el-table :data="rows" v-loading="loading" row-key="id" class="rounded-lg overflow-hidden !bg-transparent">
          <el-table-column prop="id" label="ID" width="90" />
          <el-table-column prop="domain" label="域名" min-width="260" />
          <el-table-column label="付费" width="90">
            <template #default="{ row }">
              <el-tag :type="row.is_paid === 1 ? 'warning' : 'info'">{{ row.is_paid === 1 ? '付费' : '免费' }}</el-tag>
            </template>
          </el-table-column>
          <el-table-column prop="price" label="价格" width="110" />
          <el-table-column label="状态" width="100">
            <template #default="{ row }">
              <el-tag :type="row.state === 1 ? 'success' : 'info'">{{ row.state === 1 ? '启用' : '停用' }}</el-tag>
            </template>
          </el-table-column>
          <el-table-column prop="uid" label="归属UID" width="110" />
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

    <el-dialog v-model="dialogOpen" :title="form.id ? '编辑域名' : '新增域名'" width="720px" align-center>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
          <div class="text-sm font-medium text-slate-700 mb-1">域名</div>
          <input v-model="form.domain" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow" />
        </div>
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">启用</div>
          <el-select v-model="form.state" class="w-full">
            <el-option :value="1" label="启用" />
            <el-option :value="0" label="停用" />
          </el-select>
        </div>
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">付费域名</div>
          <el-select v-model="form.is_paid" class="w-full">
            <el-option :value="0" label="免费" />
            <el-option :value="1" label="付费" />
          </el-select>
        </div>
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">价格(积分)</div>
          <el-input-number v-model="form.price" :min="0" :max="100000000" class="w-full" />
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
        <div class="md:col-span-2">
          <div class="text-sm font-medium text-slate-700 mb-1">备注</div>
          <input v-model="form.remark" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow" />
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

