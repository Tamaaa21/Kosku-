<template>
  <!-- Dynamic Navigation Switcher:
       - Pemilik -> SidebarPemilik (Sidebar Kiri + Top Bar Header)
       - Admin   -> SidebarAdmin   (Sidebar Kiri + Top Bar Header)
       - Pencari / Tamu -> NavbarPencari (Top Horizontal Navbar)
  -->
  <component :is="activeNavigationComponent" />
</template>

<script setup>
import { computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import TenantNavbar from './TenantNavbar.vue'
import OwnerSidebar from './OwnerSidebar.vue'
import SidebarAdmin from './SidebarAdmin.vue'

const auth = useAuthStore()

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
