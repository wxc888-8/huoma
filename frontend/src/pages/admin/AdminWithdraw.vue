<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Icon } from '@iconify/vue'
import { api, withAdminAuth } from '@/lib/api'
import { useAdminAuthStore } from '@/stores/adminAuth'

type Row = {
  id: number
  uid: number
  username?: string
  amount: number
  alipay_account: string
  alipay_name: string
  create_time: string
  status: number
  remark?: string
}

const adminAuth = useAdminAuthStore()
const loading = ref(false)
const rows = ref<Row[]>([])

const filter = reactive({
  status: 0,
})

const load = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/admin/withdraw/list', {
      params: { status: filter.status },
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

const process = async (row: Row, status: 1 | 2) => {
  const actionText = status === 1 ? '通过' : '拒绝'
  let remark = ''
  if (status === 2) {
    try {
      const r = await ElMessageBox.prompt('请输入拒绝原因', '拒绝提现', {
        confirmButtonText: '提交',
        cancelButtonText: '取消',
      })
      remark = r.value ?? ''
    } catch {
      return
    }
  } else {
    try {
      await ElMessageBox.confirm(`确认${actionText}提现吗？`, '审核确认', {
        confirmButtonText: actionText,
        cancelButtonText: '取消',
        type: 'warning',
      })
    } catch {
      return
    }
  }

  try {
    const { data } = await api.post('/admin/withdraw/process', { id: row.id, status, remark }, withAdminAuth(adminAuth.token))
    if (data?.code !== 200) throw new Error(data?.msg ?? '处理失败')
    ElMessage.success('已处理')
    await load()
  } catch (e) {
    ElMessage.error(e instanceof Error ? e.message : '处理失败')
  }
}

onMounted(load)
</script>

<template>
  <div class="space-y-5">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">提现审核</h1>
        <p class="text-sm text-slate-300 mt-1">审核用户提现申请</p>
      </div>
      <div class="flex items-center gap-2">
        <el-select v-model="filter.status" class="w-44" @change="load">
          <el-option :value="0" label="待审核" />
          <el-option :value="1" label="已通过" />
          <el-option :value="2" label="已拒绝" />
        </el-select>
        <button
          class="px-4 py-2 rounded-lg border border-white/10 text-slate-200 font-medium hover:bg-white/5 transition-colors flex items-center"
          :disabled="loading"
          @click="load"
        >
          <Icon icon="lucide:refresh-cw" class="w-4 h-4 mr-2" />
          刷新
        </button>
      </div>
    </div>

    <div class="rounded-2xl border border-white/10 bg-white/5 shadow-sm">
      <div class="p-4">
        <el-table :data="rows" v-loading="loading" row-key="id" class="rounded-lg overflow-hidden !bg-transparent">
          <el-table-column prop="id" label="ID" width="90" />
          <el-table-column label="用户" width="180">
            <template #default="{ row }">
              <div class="text-slate-200 font-medium">{{ row.username || `UID ${row.uid}` }}</div>
              <div class="text-xs text-slate-400">UID {{ row.uid }}</div>
            </template>
          </el-table-column>
          <el-table-column prop="amount" label="积分" width="120" />
          <el-table-column label="支付宝" min-width="260">
            <template #default="{ row }">
              <div class="text-slate-200">{{ row.alipay_account }}</div>
              <div class="text-xs text-slate-400">{{ row.alipay_name }}</div>
            </template>
          </el-table-column>
          <el-table-column prop="create_time" label="申请时间" width="180" />
          <el-table-column label="状态" width="120">
            <template #default="{ row }">
              <el-tag :type="row.status === 0 ? 'warning' : row.status === 1 ? 'success' : 'danger'">
                {{ row.status === 0 ? '待审核' : row.status === 1 ? '通过' : '拒绝' }}
              </el-tag>
            </template>
          </el-table-column>
          <el-table-column label="操作" width="200" fixed="right">
            <template #default="{ row }">
              <div class="flex items-center gap-2">
                <button
                  v-if="row.status === 0"
                  class="px-3 py-1.5 rounded-lg bg-emerald-500/20 text-emerald-200 hover:bg-emerald-500/30 transition-colors"
                  @click="process(row, 1)"
                >
                  通过
                </button>
                <button
                  v-if="row.status === 0"
                  class="px-3 py-1.5 rounded-lg bg-red-500/20 text-red-200 hover:bg-red-500/30 transition-colors"
                  @click="process(row, 2)"
                >
                  拒绝
                </button>
                <span v-if="row.status !== 0" class="text-sm text-slate-400">已处理</span>
              </div>
            </template>
          </el-table-column>
        </el-table>
      </div>
    </div>
  </div>
</template>

