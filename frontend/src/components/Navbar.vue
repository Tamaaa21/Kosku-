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
import NavbarPencari from './NavbarPencari.vue'
import SidebarPemilik from './SidebarPemilik.vue'
import SidebarAdmin from './SidebarAdmin.vue'

const auth = useAuthStore()

const activeNavigationComponent = computed(() => {
  if (auth.isAdmin) {
    return SidebarAdmin
  }
  if (auth.isPemilik) {
    return SidebarPemilik
  }
  return NavbarPencari
})
</script>
