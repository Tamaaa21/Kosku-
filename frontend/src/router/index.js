import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

import HomeView from '../views/HomeView.vue'
import KosListView from '../views/KosListView.vue'
import KosDetailView from '../views/KosDetailView.vue'
import FavoritesView from '../views/FavoritesView.vue'
import LocationMapView from '../views/LocationMapView.vue'
import BookingFormView from '../views/BookingFormView.vue'
import UserProfileView from '../views/UserProfileView.vue'
import LoginView from '../views/LoginView.vue'
import RegisterView from '../views/RegisterView.vue'
import TenantDashboard from '../views/TenantDashboard.vue'
import AdminDashboard from '../views/AdminDashboard.vue'
import OwnerDashboard from '../views/OwnerDashboard.vue'
import ManageKosView from '../views/ManageKosView.vue'
import ManageRoomsView from '../views/ManageRoomsView.vue'
import BookingRequestsView from '../views/BookingRequestsView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView,
  },
  {
    path: '/kos',
    name: 'daftar-kos',
    component: KosListView,
  },
  {
    path: '/kos/:id',
    name: 'kos-detail',
    component: KosDetailView,
    props: true,
  },
  {
    path: '/favorit',
    alias: '/tersimpan',
    name: 'favorit',
    component: FavoritesView,
    meta: { requiresAuth: true },
  },
  {
    path: '/peta',
    name: 'peta-lokasi',
    component: LocationMapView,
  },
  {
    path: '/pesan/:kosId/:kamarId',
    name: 'pengajuan-pemesanan',
    component: BookingFormView,
    meta: { requiresAuth: true, roles: ['pencari'] },
    props: true,
  },
  {
    path: '/pesanan',
    name: 'riwayat-pemesanan',
    component: TenantDashboard,
    meta: { requiresAuth: true, roles: ['pencari', 'pemilik', 'admin'] },
  },
  {
    path: '/profil',
    name: 'profil-pengguna',
    component: UserProfileView,
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
    redirect: '/pesanan',
  },
  {
    path: '/dashboard/pemilik',
    name: 'pemilik-dashboard',
    component: OwnerDashboard,
    meta: { requiresAuth: true, roles: ['pemilik', 'admin'] },
  },
  {
    path: '/dashboard/pemilik/kos',
    name: 'kelola-kos',
    component: ManageKosView,
    meta: { requiresAuth: true, roles: ['pemilik', 'admin'] },
  },
  {
    path: '/dashboard/pemilik/kamar',
    name: 'kelola-kamar',
    component: ManageRoomsView,
    meta: { requiresAuth: true, roles: ['pemilik', 'admin'] },
  },
  {
    path: '/dashboard/pemilik/pemesanan',
    name: 'kelola-pemesanan',
    component: BookingRequestsView,
    meta: { requiresAuth: true, roles: ['pemilik', 'admin'] },
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
    if (authStore.isAdmin) return next({ name: 'admin-dashboard' })
    if (authStore.isPemilik) return next({ name: 'pemilik-dashboard' })
    return next({ name: 'riwayat-pemesanan' })
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
