<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Icon } from '@iconify/vue'
import { api, withAdminAuth } from '@/lib/api'
import { useAdminAuthStore } from '@/stores/adminAuth'

type Row = {
  id: number
  name: string
  points_num: number
  price: number
  status: number
  sort: number
  addtime: string
  remarks: string
}

const adminAuth = useAdminAuthStore()
const loading = ref(false)
const dialogOpen = ref(false)
const saving = ref(false)
const rows = ref<Row[]>([])

const form = reactive({
  id: 0,
  name: '',
  points_num: 100,
  price: 1,
  status: 1,
  sort: 0,
  remarks: '',
})

const load = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/admin/points-packages/list', withAdminAuth(adminAuth.token))
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
  form.name = ''
  form.points_num = 100
  form.price = 1
  form.status = 1
  form.sort = 0
  form.remarks = ''
}

const openEdit = (row: Row) => {
  dialogOpen.value = true
  form.id = row.id
  form.name = row.name
  form.points_num = row.points_num
  form.price = row.price
  form.status = row.status
  form.sort = row.sort
  form.remarks = row.remarks ?? ''
}

const save = async () => {
  if (!form.name) {
    ElMessage.warning('套餐名称不能为空')
    return
  }
  saving.value = true
  try {
    const { data } = await api.post('/admin/points-packages/save', form, withAdminAuth(adminAuth.token))
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
    await ElMessageBox.confirm('确认删除该套餐吗？', '删除确认', {
      confirmButtonText: '删除',
      cancelButtonText: '取消',
      type: 'warning',
    })
  } catch {
    return
  }
  try {
    const { data } = await api.post('/admin/points-packages/delete', { id }, withAdminAuth(adminAuth.token))
    if (data?.code !== 200) throw new Error(data?.msg ?? '删除失败')
    ElMessage.success('已删除')
    await load()
  } catch (e) {
    ElMessage.error(e instanceof Error ? e.message : '删除失败')
  }
}

onMounted(load)
</script>

<template>
  <div class="space-y-5">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">积分套餐</h1>
        <p class="text-sm text-slate-300 mt-1">dwz_points_package：充值套餐配置</p>
      </div>
      <button
        class="shrink-0 bg-indigo-500 text-white px-4 py-2 rounded-lg font-medium shadow-sm hover:bg-indigo-600 transition-colors flex items-center"
        @click="openCreate"
      >
        <Icon icon="lucide:plus" class="w-5 h-5 mr-1" />
        新增套餐
      </button>
    </div>

    <div class="rounded-2xl border border-white/10 bg-white/5 shadow-sm">
      <div class="p-4">
        <el-table :data="rows" v-loading="loading" row-key="id" class="rounded-lg overflow-hidden !bg-transparent">
          <el-table-column prop="id" label="ID" width="90" />
          <el-table-column prop="name" label="名称" min-width="220" />
          <el-table-column prop="points_num" label="积分" width="110" />
          <el-table-column prop="price" label="价格(元)" width="110" />
          <el-table-column label="状态" width="100">
            <template #default="{ row }">
              <el-tag :type="row.status === 1 ? 'success' : 'info'">{{ row.status === 1 ? '上架' : '下架' }}</el-tag>
            </template>
          </el-table-column>
          <el-table-column prop="sort" label="排序" width="90" />
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

    <el-dialog v-model="dialogOpen" :title="form.id ? '编辑套餐' : '新增套餐'" width="720px" align-center>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
          <div class="text-sm font-medium text-slate-700 mb-1">套餐名称</div>
          <input v-model="form.name" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow" />
        </div>
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">积分数量</div>
          <el-input-number v-model="form.points_num" :min="1" :max="100000000" class="w-full" />
        </div>
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">价格(元)</div>
          <el-input-number v-model="form.price" :min="0" :max="100000000" class="w-full" />
        </div>
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">状态</div>
          <el-select v-model="form.status" class="w-full">
            <el-option :value="1" label="上架" />
            <el-option :value="0" label="下架" />
          </el-select>
        </div>
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">排序</div>
          <el-input-number v-model="form.sort" :min="0" :max="100000" class="w-full" />
        </div>
        <div class="md:col-span-2">
          <div class="text-sm font-medium text-slate-700 mb-1">备注</div>
          <input v-model="form.remarks" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow" />
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

