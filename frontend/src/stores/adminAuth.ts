import { defineStore } from 'pinia'
import { api } from '@/lib/api'

export const useAdminAuthStore = defineStore('adminAuth', {
  state: () => ({
    token: localStorage.getItem('admin_token') ?? '',
    adminUser: localStorage.getItem('admin_user') ?? '',
  }),
  actions: {
    setToken(token: string, user: string) {
      this.token = token
      this.adminUser = user
      localStorage.setItem('admin_token', token)
      localStorage.setItem('admin_user', user)
    },
    clear() {
      this.token = ''
      this.adminUser = ''
      localStorage.removeItem('admin_token')
      localStorage.removeItem('admin_user')
    },
    async login(payload: { user: string; password: string }) {
      const { data } = await api.post('/admin/login', payload, {
        headers: { Authorization: undefined as any },
      })
      if (data?.code !== 200) throw new Error(data?.msg ?? '登录失败')
      const token = data?.data?.token as string
      const user = data?.data?.admin?.user as string
      if (!token || !user) throw new Error('登录失败')
      this.setToken(token, user)
    },
    async fetchMe() {
      const { data } = await api.get('/admin/me', {
        headers: { Authorization: `Bearer ${this.token}` },
      })
      if (data?.code !== 200) throw new Error(data?.msg ?? '获取管理员信息失败')
      this.adminUser = data?.data?.user ?? this.adminUser
      localStorage.setItem('admin_user', this.adminUser)
    },
  },
})

