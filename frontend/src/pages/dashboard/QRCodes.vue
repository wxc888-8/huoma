<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Icon } from '@iconify/vue'
import { api } from '@/lib/api'

type QrRow = {
  id: number
  name: string
  qr_url: string
  entry_domain: string
  landing_domain: string
  views: number
  ip_count: number
  wechat_status: number
  state: number
  addtime: string | null
}

const loading = ref(false)
const saving = ref(false)
const dialogOpen = ref(false)

const query = reactive({
  kw: '',
  page: 1,
  limit: 15,
})

const total = ref(0)
const rows = ref<QrRow[]>([])
const offset = computed(() => (query.page - 1) * query.limit)

const form = reactive({
  id: 0,
  name: '',
  entry_domain: '',
  landing_domain: '',
  remarks: '',
  jump_time: 0,
  items: [{ jump_url: '', threshold: 100, image_url: '' }],
})

const fetchList = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/qrcodes/list', {
      params: {
        kw: query.kw || undefined,
        limit: query.limit,
        offset: offset.value,
      },
    })
    if (data?.code !== 200) throw new Error(data?.msg ?? '加载失败')
    total.value = data?.data?.total ?? 0
    rows.value = data?.data?.rows ?? []
  } catch (e) {
    const msg = e instanceof Error ? e.message : '加载失败'
    ElMessage.error(msg)
  } finally {
    loading.value = false
  }
}

const openCreate = () => {
  dialogOpen.value = true
  form.id = 0
  form.name = ''
  form.entry_domain = ''
  form.landing_domain = ''
  form.remarks = ''
  form.jump_time = 0
  form.items = [{ jump_url: '', threshold: 100, image_url: '' }]
}

const openEdit = async (id: number) => {
  dialogOpen.value = true
  saving.value = true
  try {
    const { data } = await api.get('/qrcodes/info', { params: { id } })
    if (data?.code !== 200) throw new Error(data?.msg ?? '加载失败')
    const qr = data?.data?.qr ?? {}
    const items = data?.data?.items ?? []
    form.id = qr.id ?? id
    form.name = qr.name ?? ''
    form.entry_domain = qr.entry_domain ?? ''
    form.landing_domain = qr.landing_domain ?? ''
    form.remarks = qr.remarks ?? ''
    form.jump_time = qr.jump_time ?? 0
    form.items = items.length
      ? items.map((it: any) => ({
          jump_url: it.jump_url ?? '',
          threshold: it.threshold ?? 100,
          image_url: it.image_url ?? '',
        }))
      : [{ jump_url: '', threshold: 100, image_url: '' }]
  } catch (e) {
    const msg = e instanceof Error ? e.message : '加载失败'
    ElMessage.error(msg)
  } finally {
    saving.value = false
  }
}

const addItem = () => {
  form.items.push({ jump_url: '', threshold: 100, image_url: '' })
}

const removeItem = (index: number) => {
  if (form.items.length <= 1) return
  form.items.splice(index, 1)
}

const handleSave = async () => {
  if (!form.name || !form.entry_domain || !form.landing_domain) {
    ElMessage.warning('请填写活码名称、入口域名、落地域名')
    return
  }
  if (!form.items.some((i) => i.jump_url)) {
    ElMessage.warning('至少填写一个跳转地址')
    return
  }
  saving.value = true
  try {
    const payload = {
      id: form.id || undefined,
      name: form.name,
      entry_domain: form.entry_domain,
      landing_domain: form.landing_domain,
      remarks: form.remarks || undefined,
      jump_time: form.jump_time,
      items: form.items.filter((i) => i.jump_url),
    }
    const { data } = await api.post('/qrcodes/save', payload)
    if (data?.code !== 200) throw new Error(data?.msg ?? '保存失败')
    ElMessage.success('保存成功')
    dialogOpen.value = false
    await fetchList()
  } catch (e) {
    const msg = e instanceof Error ? e.message : '保存失败'
    ElMessage.error(msg)
  } finally {
    saving.value = false
  }
}

const handleDelete = async (id: number) => {
  try {
    await ElMessageBox.confirm('确认删除该活码吗？', '删除确认', {
      confirmButtonText: '删除',
      cancelButtonText: '取消',
      type: 'warning',
    })
  } catch {
    return
  }
  try {
    const { data } = await api.post('/qrcodes/delete', { id })
    if (data?.code !== 200) throw new Error(data?.msg ?? '删除失败')
    ElMessage.success('已删除')
    await fetchList()
  } catch (e) {
    const msg = e instanceof Error ? e.message : '删除失败'
    ElMessage.error(msg)
  }
}

const toggleStatus = async (row: QrRow, field: 'state' | 'wechat_status', value: number) => {
  try {
    const { data } = await api.post('/qrcodes/update-status', { id: row.id, field, value })
    if (data?.code !== 200) throw new Error(data?.msg ?? '更新失败')
    if (field === 'state') row.state = value
    else row.wechat_status = value
  } catch (e) {
    const msg = e instanceof Error ? e.message : '更新失败'
    ElMessage.error(msg)
  }
}

onMounted(fetchList)
</script>

<template>
  <div class="space-y-5">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">活码管理</h1>
        <p class="text-sm text-slate-500 mt-1">创建多跳转二维码，分流与抗封配置统一管理</p>
      </div>

      <button
        class="shrink-0 bg-blue-600 text-white px-4 py-2 rounded-lg font-medium shadow-sm hover:bg-blue-700 transition-colors flex items-center"
        @click="openCreate"
      >
        <Icon icon="lucide:plus" class="w-5 h-5 mr-1" />
        新建活码
      </button>
    </div>

    <div class="bg-white border border-slate-100 rounded-xl shadow-sm">
      <div class="p-4 flex flex-wrap items-center gap-3 border-b border-slate-100">
        <div class="relative flex-1 min-w-[220px]">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <Icon icon="lucide:search" class="w-4 h-4 text-slate-400" />
          </div>
          <input
            v-model="query.kw"
            class="w-full pl-9 pr-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow"
            placeholder="搜索名称/域名/备注"
            @keyup.enter="() => { query.page = 1; fetchList() }"
          />
        </div>
        <button
          class="px-4 py-2.5 rounded-lg border border-slate-200 text-slate-700 font-medium hover:bg-slate-50 transition-colors flex items-center"
          :disabled="loading"
          @click="() => { query.page = 1; fetchList() }"
        >
          <Icon icon="lucide:refresh-cw" class="w-4 h-4 mr-2" />
          刷新
        </button>
      </div>

      <div class="p-4">
        <el-table :data="rows" v-loading="loading" row-key="id" class="rounded-lg overflow-hidden">
          <el-table-column label="二维码" width="88">
            <template #default="{ row }">
              <div class="w-14 h-14 rounded-lg bg-slate-50 border border-slate-100 overflow-hidden flex items-center justify-center">
                <img v-if="row.qr_url" :src="`/user/${row.qr_url}`" class="w-full h-full object-cover" />
                <Icon v-else icon="lucide:qr-code" class="w-6 h-6 text-slate-400" />
              </div>
            </template>
          </el-table-column>
          <el-table-column prop="name" label="名称" min-width="180" />
          <el-table-column label="域名" min-width="260">
            <template #default="{ row }">
              <div class="text-sm">
                <div class="text-slate-800 font-medium truncate">{{ row.entry_domain }}</div>
                <div class="text-slate-500 truncate">{{ row.landing_domain }}</div>
              </div>
            </template>
          </el-table-column>
          <el-table-column label="数据" width="140">
            <template #default="{ row }">
              <div class="text-sm text-slate-600">
                <div>PV {{ row.views }}</div>
                <div>IP {{ row.ip_count }}</div>
              </div>
            </template>
          </el-table-column>
          <el-table-column label="状态" width="160">
            <template #default="{ row }">
              <div class="flex items-center gap-3">
                <el-switch
                  :model-value="row.state === 1"
                  @update:model-value="(v: boolean) => toggleStatus(row, 'state', v ? 1 : 0)"
                />
                <el-tag :type="row.wechat_status === 1 ? 'success' : 'danger'">
                  {{ row.wechat_status === 1 ? '微信正常' : '疑似拦截' }}
                </el-tag>
              </div>
            </template>
          </el-table-column>
          <el-table-column label="操作" width="140" fixed="right">
            <template #default="{ row }">
              <div class="flex items-center gap-2">
                <button class="p-2 rounded-md hover:bg-slate-100 text-slate-600 transition-colors" @click="openEdit(row.id)">
                  <Icon icon="lucide:pencil" class="w-4 h-4" />
                </button>
                <button class="p-2 rounded-md hover:bg-red-50 text-slate-600 hover:text-red-600 transition-colors" @click="handleDelete(row.id)">
                  <Icon icon="lucide:trash-2" class="w-4 h-4" />
                </button>
              </div>
            </template>
          </el-table-column>
        </el-table>

        <div class="mt-4 flex items-center justify-between">
          <div class="text-sm text-slate-500">共 {{ total }} 条</div>
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

    <el-dialog v-model="dialogOpen" :title="form.id ? '编辑活码' : '新建活码'" width="760px" align-center>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">活码名称</div>
          <input v-model="form.name" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow" />
        </div>
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">跳转延时(秒)</div>
          <el-input-number v-model="form.jump_time" :min="0" :max="60" class="w-full" />
        </div>
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">入口域名</div>
          <input v-model="form.entry_domain" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow" placeholder="example.com" />
        </div>
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">落地域名</div>
          <input v-model="form.landing_domain" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow" placeholder="landing.com" />
        </div>
        <div class="col-span-2">
          <div class="text-sm font-medium text-slate-700 mb-1">备注</div>
          <input v-model="form.remarks" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow" />
        </div>
      </div>

      <div class="mt-6">
        <div class="flex items-center justify-between">
          <div class="text-sm font-bold text-slate-800">跳转地址</div>
          <el-button type="primary" plain @click="addItem">新增一条</el-button>
        </div>

        <div class="mt-3 space-y-3">
          <div v-for="(it, idx) in form.items" :key="idx" class="p-4 rounded-xl border border-slate-100 bg-slate-50/40">
            <div class="grid grid-cols-12 gap-3 items-start">
              <div class="col-span-7">
                <div class="text-xs font-medium text-slate-600 mb-1">跳转链接</div>
                <input v-model="it.jump_url" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow" placeholder="https://..." />
              </div>
              <div class="col-span-3">
                <div class="text-xs font-medium text-slate-600 mb-1">阈值</div>
                <el-input-number v-model="it.threshold" :min="1" :max="1000000" class="w-full" />
              </div>
              <div class="col-span-2 flex justify-end">
                <button class="p-2 rounded-md hover:bg-white text-slate-500 hover:text-red-600 transition-colors mt-5" @click="removeItem(idx)">
                  <Icon icon="lucide:x" class="w-4 h-4" />
                </button>
              </div>
              <div class="col-span-12">
                <div class="text-xs font-medium text-slate-600 mb-1">图片/素材(可选)</div>
                <input v-model="it.image_url" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow" placeholder="图片URL或Base64" />
              </div>
            </div>
          </div>
        </div>
      </div>

      <template #footer>
        <div class="flex items-center justify-end gap-2">
          <el-button @click="dialogOpen = false">取消</el-button>
          <el-button type="primary" :loading="saving" @click="handleSave">保存</el-button>
        </div>
      </template>
    </el-dialog>
  </div>
</template>

