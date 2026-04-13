import { defineStore } from 'pinia'
import { api } from '@/lib/api'

export interface UserInfo {
  id: number
  user: string
  name: string
  mail: string
  points: number
  vip: number
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('token') ?? '',
    user: null as UserInfo | null,
  }),
  actions: {
    setToken(token: string) {
      this.token = token
      localStorage.setItem('token', token)
    },
    clear() {
      this.token = ''
      this.user = null
      localStorage.removeItem('token')
    },
    async login(payload: { email?: string; user?: string; password?: string; pwd?: string }) {
      const { data } = await api.post('/auth/login', payload)
      if (data?.code !== 200) throw new Error(data?.msg ?? '登录失败')
      const token = data?.data?.token as string
      if (!token) throw new Error('登录失败')
      this.setToken(token)
      this.user = data?.data?.user ?? null
      return this.user
    },
    async fetchMe() {
      const { data } = await api.get('/user/me')
      if (data?.code !== 200) throw new Error(data?.msg ?? '获取用户信息失败')
      this.user = data?.data ?? null
      return this.user
    },
  },
})

