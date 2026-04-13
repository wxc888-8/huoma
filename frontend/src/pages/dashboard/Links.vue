<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Icon } from '@iconify/vue'
import { api } from '@/lib/api'

type LinkRow = {
  id: string
  dwz: string
  url: string
  title: string
  remarks: string
  view: number
  addtime: string | null
  pattern: number
}

const loading = ref(false)
const creating = ref(false)
const dialogOpen = ref(false)

const query = reactive({
  kw: '',
  page: 1,
  limit: 15,
})

const total = ref(0)
const rows = ref<LinkRow[]>([])

const offset = computed(() => (query.page - 1) * query.limit)

const createForm = reactive({
  url: '',
  type: '',
  pattern: 1,
  id: '',
  remarks: '',
  title: '',
})

const apiTypes = ref<{ id: number; name: string; keyname: string }[]>([])

const loadTypes = async () => {
  try {
    const { data } = await api.get('/links/types')
    if (data?.code !== 200) return
    apiTypes.value = data?.data?.apis ?? []
  } catch {
  }
}

const fetchList = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/links/list', {
      params: {
        kw: query.kw || undefined,
        limit: query.limit,
        offset: offset.value,
        sort: 'addtime',
        order: 'DESC',
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
  createForm.url = ''
  createForm.type = ''
  createForm.pattern = 1
  createForm.id = ''
  createForm.remarks = ''
  createForm.title = ''
}

const handleCreate = async () => {
  if (!createForm.url || !createForm.type) {
    ElMessage.warning('请填写跳转网址和短链类型')
    return
  }
  creating.value = true
  try {
    const { data } = await api.post('/links/create', {
      url: createForm.url,
      type: createForm.type,
      pattern: createForm.pattern,
      id: createForm.id || undefined,
      remarks: createForm.remarks || undefined,
      title: createForm.title || undefined,
    })
    if (data?.code !== 200) throw new Error(data?.msg ?? '创建失败')
    ElMessage.success('创建成功')
    dialogOpen.value = false
    query.page = 1
    await fetchList()
  } catch (e) {
    const msg = e instanceof Error ? e.message : '创建失败'
    ElMessage.error(msg)
  } finally {
    creating.value = false
  }
}

const handleDelete = async (id: string) => {
  try {
    await ElMessageBox.confirm('确认删除该短链吗？删除后会进入回收站。', '删除确认', {
      confirmButtonText: '删除',
      cancelButtonText: '取消',
      type: 'warning',
    })
  } catch {
    return
  }

  try {
    const { data } = await api.post('/links/delete', { id })
    if (data?.code !== 200) throw new Error(data?.msg ?? '删除失败')
    ElMessage.success('已删除')
    await fetchList()
  } catch (e) {
    const msg = e instanceof Error ? e.message : '删除失败'
    ElMessage.error(msg)
  }
}

const copyDwz = async (text: string) => {
  try {
    await navigator.clipboard.writeText(text)
    ElMessage.success('已复制')
  } catch {
    ElMessage.warning('复制失败，请手动复制')
  }
}

onMounted(fetchList)
onMounted(loadTypes)
</script>

<template>
  <div class="space-y-5">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">活码 / 短链管理</h1>
        <p class="text-sm text-slate-500 mt-1">创建、管理并追踪你的短链访问数据</p>
      </div>

      <button
        class="shrink-0 bg-blue-600 text-white px-4 py-2 rounded-lg font-medium shadow-sm hover:bg-blue-700 transition-colors flex items-center"
        @click="openCreate"
      >
        <Icon icon="lucide:plus" class="w-5 h-5 mr-1" />
        新建短链
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
            placeholder="搜索短链后缀 / 备注 / 短链地址"
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
        <el-table
          :data="rows"
          v-loading="loading"
          row-key="id"
          class="rounded-lg overflow-hidden"
        >
          <el-table-column prop="id" label="后缀" width="140" />
          <el-table-column label="短链地址" min-width="240">
            <template #default="{ row }">
              <div class="flex items-center gap-2">
                <a class="text-blue-600 hover:text-blue-700 font-medium truncate max-w-[380px]" :href="row.dwz" target="_blank">
                  {{ row.dwz }}
                </a>
              </div>
            </template>
          </el-table-column>
          <el-table-column label="目标链接" min-width="260">
            <template #default="{ row }">
              <span class="text-slate-600 truncate block max-w-[520px]">{{ row.url }}</span>
            </template>
          </el-table-column>
          <el-table-column prop="view" label="访问" width="90" />
          <el-table-column prop="addtime" label="创建时间" width="180" />
          <el-table-column label="操作" width="120" fixed="right">
            <template #default="{ row }">
              <div class="flex items-center gap-2">
                <button
                  class="p-2 rounded-md hover:bg-slate-100 text-slate-600 transition-colors"
                  @click="copyDwz(row.dwz)"
                >
                  <Icon icon="lucide:copy" class="w-4 h-4" />
                </button>
                <button
                  class="p-2 rounded-md hover:bg-red-50 text-slate-600 hover:text-red-600 transition-colors"
                  @click="handleDelete(row.id)"
                >
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

    <el-dialog v-model="dialogOpen" title="新建短链" width="520px" align-center>
      <div class="space-y-4">
        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">跳转网址</div>
          <input
            v-model="createForm.url"
            class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow"
            placeholder="https://example.com"
          />
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <div class="text-sm font-medium text-slate-700 mb-1">短链类型</div>
            <el-select
              v-model="createForm.type"
              class="w-full"
              filterable
              allow-create
              default-first-option
              placeholder="选择或输入"
            >
              <el-option
                v-for="t in apiTypes"
                :key="t.keyname"
                :label="t.name ? `${t.name} (${t.keyname})` : t.keyname"
                :value="t.keyname"
              />
            </el-select>
          </div>
          <div>
            <div class="text-sm font-medium text-slate-700 mb-1">模式</div>
            <el-select v-model="createForm.pattern" class="w-full" placeholder="选择模式">
              <el-option :value="1" label="模式1" />
              <el-option :value="2" label="模式2" />
              <el-option :value="3" label="模式3" />
              <el-option :value="4" label="直链模式" />
            </el-select>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <div class="text-sm font-medium text-slate-700 mb-1">自定义后缀(可选)</div>
            <input
              v-model="createForm.id"
              class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow"
              placeholder="1-8 位字母数字"
            />
          </div>
          <div>
            <div class="text-sm font-medium text-slate-700 mb-1">标题(可选)</div>
            <input
              v-model="createForm.title"
              class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow"
              placeholder="便于识别"
            />
          </div>
        </div>

        <div>
          <div class="text-sm font-medium text-slate-700 mb-1">备注(可选)</div>
          <input
            v-model="createForm.remarks"
            class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow"
            placeholder="例如：投放A计划"
          />
        </div>
      </div>

      <template #footer>
        <div class="flex items-center justify-end gap-2">
          <el-button @click="dialogOpen = false">取消</el-button>
          <el-button type="primary" :loading="creating" @click="handleCreate">创建</el-button>
        </div>
      </template>
    </el-dialog>
  </div>
</template>
