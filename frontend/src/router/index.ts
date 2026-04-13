import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Login from '@/pages/auth/Login.vue'
import DashboardLayout from '@/components/layouts/DashboardLayout.vue'
import Overview from '@/pages/dashboard/Overview.vue'
import Links from '@/pages/dashboard/Links.vue'
import QRCodes from '@/pages/dashboard/QRCodes.vue'
import Domains from '@/pages/dashboard/Domains.vue'
import Billing from '@/pages/dashboard/Billing.vue'
import Settings from '@/pages/dashboard/Settings.vue'
import AdminLogin from '@/pages/admin/AdminLogin.vue'
import AdminLayout from '@/components/layouts/AdminLayout.vue'
import AdminOverview from '@/pages/admin/AdminOverview.vue'
import AdminUsers from '@/pages/admin/AdminUsers.vue'
import AdminDomains from '@/pages/admin/AdminDomains.vue'
import AdminDomainPool from '@/pages/admin/AdminDomainPool.vue'
import AdminBlacklist from '@/pages/admin/AdminBlacklist.vue'
import AdminWithdraw from '@/pages/admin/AdminWithdraw.vue'
import AdminPointsPackages from '@/pages/admin/AdminPointsPackages.vue'
import { useAdminAuthStore } from '@/stores/adminAuth'

export const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'Login',
      component: Login
    },
    {
      path: '/admin/login',
      name: 'AdminLogin',
      component: AdminLogin
    },
    {
      path: '/',
      redirect: '/login'
    },
    {
      path: '/dashboard',
      component: DashboardLayout,
      children: [
        {
          path: '',
          name: 'DashboardOverview',
          component: Overview
        },
        {
          path: 'links',
          name: 'DashboardLinks',
          component: Links
        },
        {
          path: 'qrcodes',
          name: 'DashboardQRCodes',
          component: QRCodes
        },
        {
          path: 'domains',
          name: 'DashboardDomains',
          component: Domains
        },
        {
          path: 'billing',
          name: 'DashboardBilling',
          component: Billing
        },
        {
          path: 'settings',
          name: 'DashboardSettings',
          component: Settings
        }
      ]
    },
    {
      path: '/admin',
      component: AdminLayout,
      children: [
        {
          path: '',
          name: 'AdminOverview',
          component: AdminOverview
        },
        {
          path: 'users',
          name: 'AdminUsers',
          component: AdminUsers
        },
        {
          path: 'domains',
          name: 'AdminDomains',
          component: AdminDomains
        },
        {
          path: 'domain-pool',
          name: 'AdminDomainPool',
          component: AdminDomainPool
        },
        {
          path: 'blacklist',
          name: 'AdminBlacklist',
          component: AdminBlacklist
        },
        {
          path: 'packages',
          name: 'AdminPointsPackages',
          component: AdminPointsPackages
        },
        {
          path: 'withdraw',
          name: 'AdminWithdraw',
          component: AdminWithdraw
        }
      ]
    }
  ]
})

router.beforeEach((to, from, next) => {
  const auth = useAuthStore()
  const token = auth.token || localStorage.getItem('token')
  const adminAuth = useAdminAuthStore()
  const adminToken = adminAuth.token || localStorage.getItem('admin_token')
  if (to.path.startsWith('/admin')) {
    if (to.path === '/admin/login') {
      if (adminToken) next('/admin')
      else next()
      return
    }
    if (!adminToken) {
      next('/admin/login')
      return
    }
    next()
    return
  }
  if (to.path !== '/login' && !token) {
    next('/login')
  } else if (to.path === '/login' && token) {
    next('/dashboard')
  } else {
    next()
  }
})
