import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('../pages/HomePage.vue')
  },
  {
    path: '/backup/configure',
    name: 'Configure',
    component: () => import('../pages/ConfigurePage.vue')
  },
  {
    path: '/backup/:uuid/progress',
    name: 'Progress',
    component: () => import('../pages/ProgressPage.vue')
  },
  {
    path: '/backup/:uuid/complete',
    name: 'Complete',
    component: () => import('../pages/CompletePage.vue')
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
