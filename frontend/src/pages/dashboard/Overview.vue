<script setup lang="ts">
import { ref } from 'vue'
import { Icon } from '@iconify/vue'
import { ElCard, ElRow, ElCol } from 'element-plus'

const stats = ref([
  { title: '总访问量', value: '12,849', icon: 'lucide:bar-chart-3', color: 'text-blue-500', bg: 'bg-blue-50' },
  { title: '今日新增', value: '+342', icon: 'lucide:activity', color: 'text-green-500', bg: 'bg-green-50' },
  { title: '活跃活码', value: '45', icon: 'lucide:qr-code', color: 'text-indigo-500', bg: 'bg-indigo-50' },
  { title: '可用积分', value: '1,280', icon: 'lucide:coins', color: 'text-amber-500', bg: 'bg-amber-50' }
])

const recentActivities = ref([
  { id: 1, action: '生成了新的防封活码', target: '活动推广A', time: '10分钟前', icon: 'lucide:plus-circle', color: 'text-blue-500' },
  { id: 2, action: '充值成功', target: '500 积分', time: '2小时前', icon: 'lucide:check-circle-2', color: 'text-green-500' },
  { id: 3, action: '删除了活码', target: '过期测试链接', time: '昨天 15:30', icon: 'lucide:trash-2', color: 'text-red-500' },
])
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold text-slate-800 tracking-tight">数据看板</h1>
      <button class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium shadow-sm hover:bg-blue-700 transition-colors flex items-center">
        <Icon icon="lucide:plus" class="w-5 h-5 mr-1" />
        新建活码
      </button>
    </div>

    <!-- Stats Row -->
    <el-row :gutter="24">
      <el-col :span="6" :xs="12" :sm="12" :md="6" v-for="(stat, index) in stats" :key="index">
        <div class="bg-white rounded-xl p-5 border border-slate-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
          <div class="absolute -right-4 -top-4 w-24 h-24 rounded-full opacity-10 bg-current transition-transform group-hover:scale-150" :class="stat.color"></div>
          <div class="flex items-center justify-between relative z-10">
            <div>
              <p class="text-sm font-medium text-slate-500 mb-1">{{ stat.title }}</p>
              <h3 class="text-3xl font-bold text-slate-800">{{ stat.value }}</h3>
            </div>
            <div :class="['w-12 h-12 rounded-xl flex items-center justify-center', stat.bg]">
              <Icon :icon="stat.icon" :class="['w-6 h-6', stat.color]" />
            </div>
          </div>
        </div>
      </el-col>
    </el-row>

    <!-- Charts & Activity -->
    <el-row :gutter="24" class="mt-6">
      <el-col :span="16" :xs="24" :md="16">
        <el-card class="rounded-xl border-slate-100 shadow-sm !border-0" shadow="hover">
          <template #header>
            <div class="flex items-center justify-between">
              <span class="font-bold text-slate-800">访问趋势 (近7天)</span>
              <div class="text-sm text-slate-500 flex items-center cursor-pointer hover:text-blue-600">
                查看更多 <Icon icon="lucide:chevron-right" class="w-4 h-4 ml-1" />
              </div>
            </div>
          </template>
          <div class="h-64 flex items-end justify-between px-2 pb-2 gap-2">
            <!-- Mock Chart Bars -->
            <div v-for="i in 7" :key="i" class="w-full flex flex-col items-center group cursor-pointer">
              <div class="w-full bg-blue-100 rounded-t-md relative transition-all group-hover:bg-blue-200" 
                   :style="{ height: `${Math.random() * 60 + 20}%` }">
                <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10">
                  {{ Math.floor(Math.random() * 500) + 100 }} 次
                </div>
              </div>
              <div class="text-xs text-slate-400 mt-2 font-medium">10/{{ i }}</div>
            </div>
          </div>
        </el-card>
      </el-col>

      <el-col :span="8" :xs="24" :md="8">
        <el-card class="rounded-xl border-slate-100 shadow-sm !border-0 h-full" shadow="hover">
          <template #header>
            <span class="font-bold text-slate-800">最新动态</span>
          </template>
          <div class="space-y-6 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-200 before:to-transparent">
            <div v-for="activity in recentActivities" :key="activity.id" class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
              <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-white bg-slate-50 text-slate-500 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10">
                <Icon :icon="activity.icon" :class="['w-5 h-5', activity.color]" />
              </div>
              <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-4 rounded-xl border border-slate-100 bg-white shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between space-x-2 mb-1">
                  <div class="font-bold text-slate-800 text-sm">{{ activity.action }}</div>
                  <time class="text-xs font-medium text-slate-400">{{ activity.time }}</time>
                </div>
                <div class="text-slate-500 text-sm font-medium">{{ activity.target }}</div>
              </div>
            </div>
          </div>
        </el-card>
      </el-col>
    </el-row>
  </div>
</template>

<style scoped>
.el-card__header {
  border-bottom: 1px solid #f1f5f9;
  padding: 16px 20px;
}
</style>
