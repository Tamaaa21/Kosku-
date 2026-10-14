<template>
  <div class="space-y-3">
    <div class="flex items-center justify-between">
      <label class="text-xs font-semibold text-on-surface flex items-center gap-1.5">
        <span>{{ label }}</span>
        <span class="text-[11px] font-normal text-on-surface-variant">
          ({{ images.length }}/{{ maxFiles }} Foto, maks. {{ maxSizeMb }}MB/foto)
        </span>
      </label>
      <span v-if="images.length >= maxFiles" class="text-[11px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">
        Batas Maksimal Tercapai
      </span>
    </div>

    <!-- Error Toast / Validation Message -->
    <div
      v-if="validationError"
      class="p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-center justify-between gap-2"
    >
      <div class="flex items-center gap-1.5 min-w-0">
        <PhWarning :size="16" weight="bold" class="text-rose-600 shrink-0" />
        <span class="truncate">{{ validationError }}</span>
      </div>
      <button
        type="button"
        @click="validationError = ''"
        class="text-rose-600 hover:text-rose-900 font-bold shrink-0 text-sm"
      >
        &times;
      </button>
    </div>

    <!-- Upload Dropzone / Button -->
    <div
      v-if="images.length < maxFiles"
      @dragover.prevent="isDragging = true"
      @dragleave.prevent="isDragging = false"
      @drop.prevent="handleDrop"
      :class="[
        'border-2 border-dashed rounded-xl p-4 sm:p-5 text-center cursor-pointer transition flex flex-col items-center justify-center gap-1.5',
        isDragging
          ? 'border-primary bg-primary/5'
          : 'border-slate-200 hover:border-primary/50 bg-surface-container-low/30 hover:bg-surface-container-low/60'
      ]"
      @click="triggerFileInput"
    >
      <input
        ref="fileInputRef"
        type="file"
        multiple
        accept="image/jpeg,image/png,image/jpg,image/webp"
        class="hidden"
        @change="handleFileChange"
      />

      <div v-if="uploading" class="flex flex-col items-center gap-2 py-2">
        <PhCircleNotch :size="28" weight="bold" class="animate-spin text-primary" />
        <span class="text-xs font-semibold text-primary">Mengunggah gambar...</span>
      </div>

      <template v-else>
        <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center mb-1">
          <PhImageSquare :size="22" weight="bold" />
        </div>
        <p class="text-xs font-semibold text-on-surface">
          Klik atau seret foto ke sini untuk mengunggah
        </p>
        <p class="text-[11px] text-on-surface-variant">
          Format didukung: JPG, PNG, WEBP &bull; Maks. {{ maxSizeMb }}MB per berkas &bull; Tersisa {{ maxFiles - images.length }} slot
        </p>
      </template>
    </div>

    <!-- Image Preview Grid -->
    <div v-if="images.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 pt-1">
      <div
        v-for="(imgUrl, index) in images"
        :key="index"
        class="group relative aspect-square rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shadow-xs"
      >
        <img
          :src="resolveImageUrl(imgUrl)"
          :alt="`Foto ${index + 1}`"
          class="w-full h-full object-cover transition duration-300 group-hover:scale-105"
        />

        <!-- Foto Utama Badge -->
        <div
          v-if="index === 0"
          class="absolute top-1.5 left-1.5 bg-primary text-white text-[9px] font-bold px-1.5 py-0.5 rounded shadow-xs flex items-center gap-0.5"
        >
          <PhStar :size="11" weight="fill" />
          <span>Utama</span>
        </div>

        <!-- Action Overlay -->
        <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-1.5 p-2">
          <!-- Jadikan Utama button if not first -->
          <button
            v-if="index > 0"
            type="button"
            @click.stop="setAsPrimary(index)"
            title="Jadikan Foto Utama"
            class="w-8 h-8 rounded-lg bg-white/90 text-primary hover:bg-white flex items-center justify-center shadow-sm transition text-xs"
          >
            <PhStar :size="16" weight="fill" />
          </button>

          <!-- Hapus Foto button -->
          <button
            type="button"
            @click.stop="removeImage(index)"
            title="Hapus Foto"
            class="w-8 h-8 rounded-lg bg-white/90 text-rose-600 hover:bg-white flex items-center justify-center shadow-sm transition text-xs"
          >
            <PhTrash :size="16" weight="bold" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import api from '../services/api'
import {
  PhWarning,
  PhCircleNotch,
  PhImageSquare,
  PhStar,
  PhTrash
} from '@phosphor-icons/vue'

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => [],
  },
  maxFiles: {
    type: Number,
    default: 5,
  },
  maxSizeMb: {
    type: Number,
    default: 2,
  },
  label: {
    type: String,
    default: 'Galeri Foto',
  },
})

const emit = defineEmits(['update:modelValue'])

const fileInputRef = ref(null)
const isDragging = ref(false)
const uploading = ref(false)
const validationError = ref('')

const images = computed(() => props.modelValue || [])

const resolveImageUrl = (url) => {
  if (!url) return ''
  if (url.startsWith('http://') || url.startsWith('https://')) return url
  // Handle relative storage url
  const baseBackend = import.meta.env.VITE_API_URL
    ? import.meta.env.VITE_API_URL.replace(/\/api\/v1\/?$/, '')
    : 'https://backend-kos.ddev.site'
  return `${baseBackend}${url.startsWith('/') ? '' : '/'}${url}`
}

const triggerFileInput = () => {
  if (uploading.value || images.value.length >= props.maxFiles) return
  validationError.value = ''
  fileInputRef.value?.click()
}

const handleFileChange = (e) => {
  const files = Array.from(e.target.files || [])
  processFiles(files)
  if (fileInputRef.value) fileInputRef.value.value = ''
}

const handleDrop = (e) => {
  isDragging.value = false
  const files = Array.from(e.dataTransfer?.files || [])
  processFiles(files)
}

const processFiles = async (files) => {
  if (!files || files.length === 0) return
  validationError.value = ''

  // 1. Check max total files
  const availableSlots = props.maxFiles - images.value.length
  if (files.length > availableSlots) {
    validationError.value = `Maksimal ${props.maxFiles} foto. Anda hanya dapat menambahkan ${availableSlots} foto lagi.`
    return
  }

  // 2. Validate format and size for each file
  const maxBytes = props.maxSizeMb * 1024 * 1024
  const validMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp']

  for (const file of files) {
    if (!validMimes.includes(file.type.toLowerCase())) {
      validationError.value = `Berkas "${file.name}" tidak valid. Format harus JPG, PNG, atau WEBP.`
      return
    }

    if (file.size > maxBytes) {
      validationError.value = `Ukuran berkas "${file.name}" (${(file.size / (1024 * 1024)).toFixed(2)}MB) melebihi batas maksimal ${props.maxSizeMb}MB!`
      return
    }
  }

  // 3. Upload to backend
  uploading.value = true
  try {
    const formData = new FormData()
    files.forEach((file) => {
      formData.append('images[]', file)
    })

    const res = await api.post('/upload/images', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    })

    const uploadedUrls = res.data?.urls || []
    const updatedImages = [...images.value, ...uploadedUrls]
    emit('update:modelValue', updatedImages)
  } catch (err) {
    validationError.value =
      err.response?.data?.message || 'Gagal mengunggah foto. Pastikan ukuran berkas maks 2MB dan format sesuai.'
  } finally {
    uploading.value = false
  }
}

const removeImage = (index) => {
  const updated = [...images.value]
  updated.splice(index, 1)
  emit('update:modelValue', updated)
}

const setAsPrimary = (index) => {
  if (index <= 0 || index >= images.value.length) return
  const updated = [...images.value]
  const [target] = updated.splice(index, 1)
  updated.unshift(target)
  emit('update:modelValue', updated)
}
</script>
