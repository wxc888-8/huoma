import { createRouter, createWebHistory } from 'vue-router'

export const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/login',
      name: 'Login',
      component: () => import('../pages/auth/Login.vue')
    },
    {
      path: '/',
      redirect: '/login'
    },
    {
      path: '/dashboard',
      component: () => import('../components/layouts/DashboardLayout.vue'),
      children: [
        {
          path: '',
          name: 'DashboardOverview',
          component: () => import('../pages/dashboard/Overview.vue')
        }
      ]
    }
  ]
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  if (to.path !== '/login' && !token) {
    next('/login')
  } else if (to.path === '/login' && token) {
    next('/dashboard')
  } else {
    next()
  }
})
