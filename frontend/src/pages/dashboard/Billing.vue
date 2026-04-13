<script setup lang="ts">
import { onMounted, reactive, ref } from 'vue'
import { ElMessage } from 'element-plus'
import { api } from '@/lib/api'

type PackageRow = { id: number; name: string; price: number; points_num: number; status: number }

const packages = ref<PackageRow[]>([])
const loading = ref(false)
const ordering = ref(false)
const withdrawing = ref(false)

const form = reactive({
  packageId: 0,
  payType: 'alipay',
})

const withdrawForm = reactive({
  amount: 10,
  alipay_account: '',
  alipay_name: '',
})

const loadPackages = async () => {
  loading.value = true
  try {
    const { data } = await api.get('/billing/points-packages')
    if (data?.code !== 200) throw new Error(data?.msg ?? '加载失败')
    packages.value = data?.data?.packages ?? []
    if (!form.packageId && packages.value.length) form.packageId = packages.value[0].id
  } catch (e) {
    ElMessage.error(e instanceof Error ? e.message : '加载失败')
  } finally {
    loading.value = false
  }
}

const createOrder = async () => {
  if (!form.packageId) {
    ElMessage.warning('请选择套餐')
    return
  }
  ordering.value = true
  try {
    const { data } = await api.post('/billing/recharge-points', { package_id: form.packageId, type: form.payType })
    if (data?.code !== 200) throw new Error(data?.msg ?? '下单失败')
    ElMessage.success(`订单创建成功：${data?.data?.trade_no}`)
  } catch (e) {
    ElMessage.error(e instanceof Error ? e.message : '下单失败')
  } finally {
    ordering.value = false
  }
}

const submitWithdraw = async () => {
  if (!withdrawForm.alipay_account || !withdrawForm.alipay_name) {
    ElMessage.warning('请填写完整的支付宝信息')
    return
  }
  withdrawing.value = true
  try {
    const { data } = await api.post('/billing/withdraw', withdrawForm)
    if (data?.code !== 200) throw new Error(data?.msg ?? '提交失败')
    ElMessage.success('提现申请已提交')
    withdrawForm.amount = 10
    withdrawForm.alipay_account = ''
    withdrawForm.alipay_name = ''
  } catch (e) {
    ElMessage.error(e instanceof Error ? e.message : '提交失败')
  } finally {
    withdrawing.value = false
  }
}

onMounted(loadPackages)
</script>

<template>
  <div class="space-y-5">
    <div>
      <h1 class="text-2xl font-bold text-slate-800 tracking-tight">充值与提现</h1>
      <p class="text-sm text-slate-500 mt-1">积分充值、订单创建、提现申请</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
      <div class="bg-white border border-slate-100 rounded-xl shadow-sm p-5">
        <div class="font-bold text-slate-800 mb-4">积分充值</div>
        <div v-loading="loading" class="space-y-4">
          <div>
            <div class="text-sm font-medium text-slate-700 mb-1">选择套餐</div>
            <el-select v-model="form.packageId" class="w-full" placeholder="选择套餐">
              <el-option v-for="p in packages" :key="p.id" :label="`${p.name} - ${p.price}元 / ${p.points_num}积分`" :value="p.id" />
            </el-select>
          </div>
          <div>
            <div class="text-sm font-medium text-slate-700 mb-1">支付方式</div>
            <el-select v-model="form.payType" class="w-full">
              <el-option label="支付宝" value="alipay" />
              <el-option label="微信" value="wxpay" />
              <el-option label="QQ" value="qqpay" />
            </el-select>
          </div>
          <el-button type="primary" class="w-full" :loading="ordering" @click="createOrder">创建订单</el-button>
          <div class="text-xs text-slate-500">订单创建后，请对接原系统的支付页面/回调完成支付流程。</div>
        </div>
      </div>

      <div class="bg-white border border-slate-100 rounded-xl shadow-sm p-5">
        <div class="font-bold text-slate-800 mb-4">提现申请</div>
        <div class="space-y-4">
          <div>
            <div class="text-sm font-medium text-slate-700 mb-1">提现积分</div>
            <el-input-number v-model="withdrawForm.amount" :min="1" :max="1000000" class="w-full" />
          </div>
          <div>
            <div class="text-sm font-medium text-slate-700 mb-1">支付宝账号</div>
            <input v-model="withdrawForm.alipay_account" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow" />
          </div>
          <div>
            <div class="text-sm font-medium text-slate-700 mb-1">收款人姓名</div>
            <input v-model="withdrawForm.alipay_name" class="w-full px-3 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-shadow" />
          </div>
          <el-button type="primary" class="w-full" :loading="withdrawing" @click="submitWithdraw">提交提现</el-button>
        </div>
      </div>
    </div>
  </div>
</template>

