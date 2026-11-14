import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const HomeView = () => import('../views/HomeView.vue')
const KosListView = () => import('../views/KosListView.vue')
const KosDetailView = () => import('../views/KosDetailView.vue')
const FavoritesView = () => import('../views/FavoritesView.vue')
const LocationMapView = () => import('../views/LocationMapView.vue')
const BookingFormView = () => import('../views/BookingFormView.vue')
const UserProfileView = () => import('../views/UserProfileView.vue')
const LoginView = () => import('../views/LoginView.vue')
const RegisterView = () => import('../views/RegisterView.vue')
const ForgotPasswordView = () => import('../views/ForgotPasswordView.vue')
const ResetPasswordView = () => import('../views/ResetPasswordView.vue')
const TenantDashboard = () => import('../views/TenantDashboard.vue')
const AdminDashboard = () => import('../views/AdminDashboard.vue')
const OwnerDashboard = () => import('../views/OwnerDashboard.vue')
const ManageKosView = () => import('../views/ManageKosView.vue')
const ManageRoomsView = () => import('../views/ManageRoomsView.vue')
const BookingRequestsView = () => import('../views/BookingRequestsView.vue')
const PropertyVerificationView = () => import('../views/PropertyVerificationView.vue')
const AboutView = () => import('../views/AboutView.vue')
const NotFoundView = () => import('../views/NotFoundView.vue')
const ForbiddenView = () => import('../views/ForbiddenView.vue')
const ApiTestView = () => import('../views/ApiTestView.vue')

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView,
  },
  {
    path: '/about',
    alias: '/tentang',
    name: 'about',
    component: AboutView,
  },
  {
    path: '/api-test',
    alias: ['/api-tester', '/api-docs', '/docs/api'],
    name: 'api-test',
    component: ApiTestView,
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
    path: '/forgot-password',
    name: 'forgot-password',
    component: ForgotPasswordView,
    meta: { guestOnly: true },
  },
  {
    path: '/reset-password',
    name: 'reset-password',
    component: ResetPasswordView,
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
    path: '/dashboard/pemilik/verifikasi',
    name: 'pemilik-verifikasi',
    component: PropertyVerificationView,
    meta: { requiresAuth: true, roles: ['pemilik', 'admin'] },
  },
  {
    path: '/dashboard/pemilik/kos',
    name: 'kelola-kos',
    component: ManageKosView,
    meta: { requiresAuth: true, roles: ['pemilik', 'admin'], requiresVerified: true },
  },
  {
    path: '/dashboard/pemilik/kamar',
    name: 'kelola-kamar',
    component: ManageRoomsView,
    meta: { requiresAuth: true, roles: ['pemilik', 'admin'], requiresVerified: true },
  },
  {
    path: '/dashboard/pemilik/pemesanan',
    name: 'kelola-pemesanan',
    component: BookingRequestsView,
    meta: { requiresAuth: true, roles: ['pemilik', 'admin'], requiresVerified: true },
  },
  {
    path: '/dashboard/admin',
    name: 'admin-dashboard',
    component: AdminDashboard,
    meta: { requiresAuth: true, roles: ['admin'] },
  },
  {
    path: '/403',
    name: 'forbidden',
    component: ForbiddenView,
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: NotFoundView,
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
      return next({ name: 'forbidden' })
    }

    if (to.meta.requiresVerified && authStore.isPemilik && !authStore.isVerified) {
      return next({
        name: 'pemilik-verifikasi',
        query: { locked: '1', from: to.path }
      })
    }
  }

  next()
})

export default router
