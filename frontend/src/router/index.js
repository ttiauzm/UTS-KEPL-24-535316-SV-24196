import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/HomeView.vue'
import Catatan from '../views/CatatanView.vue'
import Login from '../views/LoginView.vue'
import { apakahSudahLogin } from '../api/http'

const routes = [
  { path: '/', name: 'home', component: Home },
  { path: '/login', name: 'login', component: Login },
  { path: '/catatan', name: 'catatan', component: Catatan, meta: { butuhLogin: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Halaman catatan hanya bisa dibuka setelah login (punya token)
router.beforeEach((to) => {
  if (to.meta.butuhLogin && !apakahSudahLogin()) return { name: 'login' }
})

export default router
