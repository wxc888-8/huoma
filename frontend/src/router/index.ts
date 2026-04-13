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

export const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/login',
      name: 'Login',
      component: Login
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
    }
  ]
})

router.beforeEach((to, from, next) => {
  const auth = useAuthStore()
  const token = auth.token || localStorage.getItem('token')
  if (to.path !== '/login' && !token) {
    next('/login')
  } else if (to.path === '/login' && token) {
    next('/dashboard')
  } else {
    next()
  }
})
