import axios from 'axios'

export const api = axios.create({
  baseURL: '/api/v1',
  timeout: 15000,
})

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) {
    ;(config.headers as any) = config.headers ?? {}
    ;(config.headers as any).Authorization = `Bearer ${token}`
  }
  return config
})

export const withAdminAuth = (token: string) => ({
  headers: {
    Authorization: `Bearer ${token}`,
  },
})
