<template>
  <!-- Sembunyikan navigasi umum jika berada di portal internal administrator -->
  <component v-if="!isAdminLoginRoute" :is="activeNavigationComponent" />
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import TenantNavbar from './TenantNavbar.vue'
import OwnerSidebar from './OwnerSidebar.vue'
import SidebarAdmin from './SidebarAdmin.vue'

const auth = useAuthStore()
const route = useRoute()

const isAdminLoginRoute = computed(() => {
  return route.name === 'admin-login' || route.path === '/admin/login'
})

const activeNavigationComponent = computed(() => {
  if (auth.isAdmin) {
    return SidebarAdmin
  }
  if (auth.isPemilik) {
    return OwnerSidebar
  }
  return TenantNavbar
})
</script>
