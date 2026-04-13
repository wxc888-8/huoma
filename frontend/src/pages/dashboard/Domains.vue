<script setup lang="ts">
import { computed, onMounted, reactive, ref } from 'vue'
import { ElMessage } from 'element-plus'
import { Icon } from '@iconify/vue'
import { api } from '@/lib/api'

type DomainRow = { id: number; domain: string; price?: number; remark?: string; addtime?: string; type?: 'entry' | 'landing' }

const activeTab = ref<'entry' | 'landing'>('entry')
const loading = ref(false)
const storeLoading = ref(false)

const list = reactive({
  free: [] as DomainRow[],
  paid: [] as DomainRow[],
})

const store = ref<DomainRow[]>([])
const mine = ref<DomainRow[]>([])

const loadList = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/domains/list', { params: { type: activeTab.value } })
    if (data?.code !== 200) throw new Error(data?.msg ?? '加载失败')
    list.free = data?.data?.free ?? []
    list.paid = data?.data?.paid ?? []
  } catch (e) {
    ElMessage.error(e instanceof Error ? e.message : '加载失败')
  } finally {
    loading.value = false
  }
}

const loadStore = async () => {
  storeLoading.value = true
  try {
    const { data } = await api.get('/domains/store', { params: { type: activeTab.value } })
    if (data?.code !== 200) throw new Error(data?.msg ?? '加载失败')
    store.value = data?.data?.domains ?? []
  } catch (e) {
    ElMessage.error(e instanceof Error ? e.message : '加载失败')
  } finally {
    storeLoading.value = false
  }
}

const loadMine = async () => {
  try {
    const { data } = await api.get('/domains/mine')
    if (data?.code !== 200) return
    mine.value = data?.data?.domains ?? []
  } catch {
  }
}

const currentMine = computed(() => mine.value.filter((d) => d.type === activeTab.value))

const buyDomain = async (id: number) => {
  try {
    const { data } = await api.post('/domains/buy', { domain_id: id, domain_type: activeTab.value })
    if (data?.code !== 200) throw new Error(data?.msg ?? '购买失败')
    ElMessage.success('购买成功')
    await Promise.all([loadList(), loadStore(), loadMine()])
  } catch (e) {
    ElMessage.error(e instanceof Error ? e.message : '购买失败')
  }
}

const releaseDomain = async (id: number) => {
  try {
    const { data } = await api.post('/domains/release', { domain_id: id, domain_type: activeTab.value })
    if (data?.code !== 200) throw new Error(data?.msg ?? '释放失败')
    ElMessage.success('已释放')
    await Promise.all([loadList(), loadStore(), loadMine()])
  } catch (e) {
    ElMessage.error(e instanceof Error ? e.message : '释放失败')
  }
}

const reloadAll = async () => {
  await Promise.all([loadList(), loadStore(), loadMine()])
}

onMounted(reloadAll)
</script>

<template>
  <div class="space-y-5">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">域名池</h1>
        <p class="text-sm text-slate-500 mt-1">免费域名 + 已购域名 + 商店域名统一管理</p>
      </div>

      <button
        class="px-4 py-2 rounded-lg border border-slate-200 text-slate-700 font-medium hover:bg-slate-50 transition-colors flex items-center"
        :disabled="loading || storeLoading"
        @click="reloadAll"
      >
        <Icon icon="lucide:refresh-cw" class="w-4 h-4 mr-2" />
        刷新
      </button>
    </div>

    <div class="bg-white border border-slate-100 rounded-xl shadow-sm p-4">
      <el-segmented
        v-model="activeTab"
        :options="[
          { label: '入口域名', value: 'entry' },
          { label: '落地域名', value: 'landing' },
        ]"
        @change="reloadAll"
      />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
      <div class="bg-white border border-slate-100 rounded-xl shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
          <div class="font-bold text-slate-800">免费域名</div>
          <div class="text-sm text-slate-500">{{ list.free.length }} 条</div>
        </div>
        <div v-loading="loading" class="space-y-2">
          <div v-for="d in list.free" :key="d.id" class="px-3 py-2 rounded-lg bg-slate-50 border border-slate-100">
            <div class="text-slate-800 font-medium truncate">{{ d.domain }}</div>
          </div>
          <div v-if="!loading && list.free.length === 0" class="text-sm text-slate-500">暂无</div>
        </div>
      </div>

      <div class="bg-white border border-slate-100 rounded-xl shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
          <div class="font-bold text-slate-800">我已购</div>
          <div class="text-sm text-slate-500">{{ currentMine.length }} 条</div>
        </div>
        <div class="space-y-2">
          <div v-for="d in currentMine" :key="d.id" class="px-3 py-2 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-between gap-3">
            <div class="min-w-0">
              <div class="text-slate-800 font-medium truncate">{{ d.domain }}</div>
              <div v-if="d.addtime" class="text-xs text-slate-500">{{ d.addtime }}</div>
            </div>
            <button class="shrink-0 px-3 py-1.5 rounded-lg text-sm font-medium border border-slate-200 hover:bg-white transition-colors" @click="releaseDomain(d.id)">
              释放
            </button>
          </div>
          <div v-if="currentMine.length === 0" class="text-sm text-slate-500">暂无</div>
        </div>
      </div>

      <div class="bg-white border border-slate-100 rounded-xl shadow-sm p-5">
        <div class="flex items-center justify-between mb-4">
          <div class="font-bold text-slate-800">域名商店</div>
          <div class="text-sm text-slate-500">{{ store.length }} 条</div>
        </div>
        <div v-loading="storeLoading" class="space-y-2">
          <div v-for="d in store" :key="d.id" class="px-3 py-2 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-between gap-3">
            <div class="min-w-0">
              <div class="text-slate-800 font-medium truncate">{{ d.domain }}</div>
              <div class="text-xs text-slate-500 truncate">
                <span v-if="d.price">价格 {{ d.price }} 积分</span>
                <span v-if="d.remark"> · {{ d.remark }}</span>
              </div>
            </div>
            <button class="shrink-0 bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors" @click="buyDomain(d.id)">
              购买
            </button>
          </div>
          <div v-if="!storeLoading && store.length === 0" class="text-sm text-slate-500">暂无</div>
        </div>
      </div>
    </div>
  </div>
</template>

