import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

import HomeView from '../views/HomeView.vue'
import DaftarKosView from '../views/DaftarKosView.vue'
import KosDetailView from '../views/KosDetailView.vue'
import PetaLokasiView from '../views/PetaLokasiView.vue'
import PengajuanPemesananView from '../views/PengajuanPemesananView.vue'
import ProfilPenggunaView from '../views/ProfilPenggunaView.vue'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import PencariDashboard from '../views/PencariDashboard.vue'
import PemilikDashboard from '../views/PemilikDashboard.vue'
import AdminDashboard from '../views/AdminDashboard.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView,
  },
  {
    path: '/kos',
    name: 'daftar-kos',
    component: DaftarKosView,
  },
  {
    path: '/kos/:id',
    name: 'kos-detail',
    component: KosDetailView,
    props: true,
  },
  {
    path: '/peta',
    name: 'peta-lokasi',
    component: PetaLokasiView,
  },
  {
    path: '/pesan/:kosId/:kamarId',
    name: 'pengajuan-pemesanan',
    component: PengajuanPemesananView,
    meta: { requiresAuth: true, roles: ['pencari'] },
    props: true,
  },
  {
    path: '/pesanan',
    name: 'riwayat-pemesanan',
    component: PencariDashboard,
    meta: { requiresAuth: true, roles: ['pencari', 'admin'] },
  },
  {
    path: '/profil',
    name: 'profil-pengguna',
    component: ProfilPenggunaView,
    meta: { requiresAuth: true },
  },
  {
    path: '/login',
    name: 'login',
    component: LoginView,
    meta: { guestOnly: true },
  },
  {
    path: '/register',
    name: 'register',
    component: RegisterView,
    meta: { guestOnly: true },
  },
  {
    path: '/dashboard/pencari',
    name: 'pencari-dashboard',
    component: PencariDashboard,
    meta: { requiresAuth: true, roles: ['pencari'] },
  },
  {
    path: '/dashboard/pemilik',
    name: 'pemilik-dashboard',
    component: PemilikDashboard,
    meta: { requiresAuth: true, roles: ['pemilik'] },
  },
  {
    path: '/dashboard/admin',
    name: 'admin-dashboard',
    component: AdminDashboard,
    meta: { requiresAuth: true, roles: ['admin'] },
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 }
  },
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  if (to.meta.guestOnly && authStore.isAuthenticated) {
    if (authStore.isPemilik) return next({ name: 'pemilik-dashboard' })
    if (authStore.isAdmin) return next({ name: 'admin-dashboard' })
    return next({ name: 'home' })
  }

  if (to.meta.requiresAuth) {
    if (!authStore.isAuthenticated) {
      return next({ name: 'login', query: { redirect: to.fullPath } })
    }

    if (to.meta.roles && !to.meta.roles.includes(authStore.role)) {
      alert('Akses ditolak untuk peran akun Anda.')
      return next({ name: 'home' })
    }
  }

  next()
})

export default router
