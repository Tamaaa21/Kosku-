<template>
  <span :class="['inline-flex items-center font-extrabold tracking-tight select-none', customClass]">
    <span :class="kosClass || 'text-[#017E91]'">Kos</span><span :class="taraClass || 'text-[#E68202]'">tara</span>
  </span>
</template>

<script setup>
defineProps({
  customClass: {
    type: String,
    default: '',
  },
  kosClass: {
    type: String,
    default: '',
  },
  taraClass: {
    type: String,
    default: '',
  },
})
</script>
