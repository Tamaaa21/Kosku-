<template>
  <div class="w-full">
    <!-- Hero Section with 5-Second HD Background Slider -->
    <section
      class="relative w-full overflow-hidden group select-none"
      @mouseenter="isHovered = true"
      @mouseleave="isHovered = false"
    >
      <div class="relative w-full min-h-[540px] sm:min-h-[580px] lg:min-h-[640px] flex items-center justify-center">
        <!-- Background Slider Layer (Smooth Crossfade Transitions) -->
        <div
          v-for="(banner, idx) in activeBanners"
          :key="banner.id || idx"
          class="absolute inset-0 bg-cover bg-center transition-opacity duration-1000 ease-in-out transform will-change-transform"
          :class="idx === currentSlideIndex ? 'opacity-100 z-0' : 'opacity-0 -z-10 pointer-events-none'"
          :style="{ backgroundImage: `url('${banner.gambar_url}')` }"
        ></div>

        <!-- High contrast scrim gradient overlay matching UI in image.png:
             Left is dark for crisp text legibility, top has subtle vignette so transparent navbar is crystal clear -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/85 via-slate-950/50 to-slate-950/20 z-0 pointer-events-none"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950/70 via-transparent to-slate-950/50 z-0 pointer-events-none"></div>

        <!-- Manual Navigation Arrows (Desktop hover) -->
        <button
          v-if="activeBanners.length > 1"
          @click.stop="prevSlide"
          class="hidden lg:flex absolute left-6 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-slate-900/40 hover:bg-slate-900/80 backdrop-blur-md text-white items-center justify-center border border-white/20 transition-all opacity-0 group-hover:opacity-100 hover:scale-105 active:scale-95 shadow-lg"
          title="Slide Sebelumnya"
          type="button"
        >
          <PhCaretLeft :size="20" weight="bold" />
        </button>
        <button
          v-if="activeBanners.length > 1"
          @click.stop="nextSlide"
          class="hidden lg:flex absolute right-6 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-slate-900/40 hover:bg-slate-900/80 backdrop-blur-md text-white items-center justify-center border border-white/20 transition-all opacity-0 group-hover:opacity-100 hover:scale-105 active:scale-95 shadow-lg"
          title="Slide Selanjutnya"
          type="button"
        >
          <PhCaretRight :size="20" weight="bold" />
        </button>

        <!-- Main Hero Content -->
        <div class="relative z-10 max-w-7xl mx-auto w-full px-margin pt-24 sm:pt-28 lg:pt-30 pb-6 sm:pb-8 lg:pb-10 flex flex-col justify-between min-h-[520px] sm:min-h-[560px] lg:min-h-[620px]">
          <!-- Top Left Content: Badge, H1, Subtitle -->
          <div class="max-w-3xl flex flex-col items-start text-left">
            <!-- Badge: Location Pill -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-900/60 backdrop-blur-md border border-white/15 text-white/95 text-xs sm:text-sm font-medium mb-4 sm:mb-5 shadow-sm">
              <PhMapPin :size="16" weight="fill" class="text-amber-400 select-none shrink-0" />
              <span>{{ currentBanner.badge_text || 'Cari kos di seluruh Indonesia' }}</span>
            </div>

            <!-- Headline: Kos Nyaman, Hidup Lebih Mudah -->
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-[1.15] drop-shadow-sm">
              <span>{{ formattedTitle.part1 }}</span>
              <br v-if="formattedTitle.part2" />
              <span v-if="formattedTitle.part2" class="text-amber-400">{{ formattedTitle.part2 }}</span>
            </h1>

            <!-- Subtitle -->
            <p class="text-sm sm:text-base lg:text-lg text-slate-200/95 max-w-xl mt-3 sm:mt-3.5 leading-relaxed font-normal drop-shadow-xs whitespace-pre-line">
              {{ currentBanner.subjudul || 'Temukan kos terbaik sesuai kebutuhanmu.\nLokasi strategis, fasilitas lengkap, harga transparan.' }}
            </p>

            <!-- 5s Slider Indicators (Dots & Progress) -->
            <div v-if="activeBanners.length > 1" class="flex items-center gap-2 mt-4 sm:mt-5">
              <button
                v-for="(b, idx) in activeBanners"
                :key="idx"
                @click="goToSlide(idx)"
                class="h-2 rounded-full transition-all duration-300 cursor-pointer focus:outline-none"
                :class="idx === currentSlideIndex ? 'w-8 bg-amber-400' : 'w-2 bg-white/40 hover:bg-white/80'"
                :title="`Pindah ke slide ${idx + 1}`"
                type="button"
              ></button>
              <span class="text-[11px] font-semibold text-white/70 ml-2 hidden sm:inline-flex items-center gap-1.5 bg-black/40 backdrop-blur-xs px-2.5 py-0.5 rounded-full border border-white/10">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Auto Slide 5s</span>
              </span>
            </div>
          </div>

          <!-- Bottom: Floating Search Bar Card (Desktop Pill & Mobile 1-Line) -->
          <div class="w-full max-w-[740px] mr-auto mt-6 sm:mt-8 -translate-y-4 sm:-translate-y-6 lg:-translate-y-8">
            <!-- 1. Mobile 1-Line Search Bar (cukup satu baris, klik untuk pop up detail) -->
            <div
              @click="openMobileSearchModal"
              class="flex lg:hidden w-full items-center justify-between gap-3 bg-white/95 backdrop-blur-md rounded-full shadow-[0_12px_36px_rgba(0,0,0,0.18)] p-2 pl-4 pr-2 border border-white/60 cursor-pointer active:scale-[0.98] transition-all hover:bg-white"
            >
              <div class="flex items-center gap-3 min-w-0 flex-1">
                <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0">
                  <PhMagnifyingGlass :size="18" weight="bold" />
                </div>
                <div class="min-w-0 flex-1 text-left">
                  <template v-if="activeSearchSummary">
                    <p class="text-xs font-bold text-slate-800 truncate">{{ activeSearchSummary.title }}</p>
                    <p class="text-[10px] text-slate-500 font-medium truncate">{{ activeSearchSummary.subtitle }}</p>
                  </template>
                  <template v-else>
                    <p class="text-xs sm:text-sm font-semibold text-slate-700 truncate">Mau cari kos di mana?</p>
                    <p class="text-[10px] text-slate-400 font-medium">Lokasi &bull; Tipe &bull; Budget</p>
                  </template>
                </div>
              </div>

              <!-- Filter / Action button indicator -->
              <div class="w-9 h-9 rounded-full bg-gradient-to-r from-primary to-[#095296] text-white flex items-center justify-center shadow-sm shrink-0">
                <PhSlidersHorizontal :size="16" weight="bold" />
              </div>
            </div>

            <!-- 2. Desktop Full Pill Search Bar (Layar Besar lg+) -->
            <div class="hidden lg:flex w-full bg-white/95 backdrop-blur-md rounded-full shadow-[0_20px_50px_rgba(0,0,0,0.22)] p-2 sm:p-2.5 border border-white/40 items-center gap-2 transition-all">
              <!-- Location Search -->
              <div class="flex-1 flex items-center gap-2.5 px-3.5 py-2 sm:py-2.5 bg-slate-50/90 hover:bg-slate-100/80 rounded-full focus-within:bg-white focus-within:ring-2 focus-within:ring-primary/20 transition-all group/loc">
                <PhMapPin :size="20" weight="fill" class="text-primary/70 group-focus-within/loc:text-primary transition-colors select-none shrink-0" />
                <div class="flex-1 flex flex-col justify-center min-w-0">
                  <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block leading-none mb-0.5">Lokasi / Area</span>
                  <input
                    v-model="searchQuery"
                    type="text"
                    class="w-full bg-transparent text-sm font-semibold text-slate-800 placeholder:text-slate-400 placeholder:font-normal border-none focus:outline-none focus:ring-0 p-0 leading-tight"
                    placeholder="Kota, area, atau kampus..."
                    @keyup.enter="handleSearch"
                  />
                </div>
                <button
                  v-if="searchQuery"
                  @click="searchQuery = ''"
                  class="p-1 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 transition shrink-0 cursor-pointer"
                  title="Hapus pencarian"
                  type="button"
                >
                  <PhXCircle :size="16" weight="fill" />
                </button>
              </div>

              <!-- Divider Desktop -->
              <div class="w-px h-8 bg-slate-200/90 shrink-0"></div>

              <!-- Jenis Kos Dropdown (Bed Icon) -->
              <div class="flex items-center gap-2.5 px-3.5 py-2 sm:py-2.5 bg-slate-50/90 hover:bg-slate-100/80 rounded-full min-w-[135px] relative group/type cursor-pointer focus-within:ring-2 focus-within:ring-primary/20 focus-within:bg-white transition-all">
                <PhBed :size="19" weight="bold" class="text-slate-500 group-focus-within/type:text-primary transition-colors select-none shrink-0" />
                <div class="flex-1 flex flex-col justify-center min-w-0 pr-4">
                  <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block leading-none mb-0.5">Tipe Hunian</span>
                  <select
                    v-model="selectedJenis"
                    style="background-image: none !important;"
                    class="w-full bg-transparent bg-none [background-image:none] text-xs sm:text-sm font-semibold text-slate-700 border-none focus:outline-none focus:ring-0 p-0 cursor-pointer appearance-none truncate leading-tight"
                  >
                    <option value="">Semua Tipe</option>
                    <option value="putra">Putra</option>
                    <option value="putri">Putri</option>
                    <option value="campur">Campur</option>
                  </select>
                </div>
                <PhCaretDown :size="13" weight="bold" class="absolute right-3.5 text-slate-400 pointer-events-none" />
              </div>

              <!-- Divider Desktop -->
              <div class="w-px h-8 bg-slate-200/90 shrink-0"></div>

              <!-- Rentang Harga Dropdown (Coin / Dollar Icon) -->
              <div class="flex items-center gap-2.5 px-3.5 py-2 sm:py-2.5 bg-slate-50/90 hover:bg-slate-100/80 rounded-full min-w-[145px] relative group/price cursor-pointer focus-within:ring-2 focus-within:ring-primary/20 focus-within:bg-white transition-all">
                <PhCurrencyCircleDollar :size="19" weight="bold" class="text-slate-500 group-focus-within/price:text-primary transition-colors select-none shrink-0" />
                <div class="flex-1 flex flex-col justify-center min-w-0 pr-4">
                  <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block leading-none mb-0.5">Budget</span>
                  <select
                    v-model="selectedPriceRange"
                    style="background-image: none !important;"
                    class="w-full bg-transparent bg-none [background-image:none] text-xs sm:text-sm font-semibold text-slate-700 border-none focus:outline-none focus:ring-0 p-0 cursor-pointer appearance-none truncate leading-tight"
                  >
                    <option value="">Semua Harga</option>
                    <option value="under500">&lt; Rp 500rb</option>
                    <option value="500-1000">Rp 500rb - 1jt</option>
                    <option value="above1000">&gt; Rp 1jt</option>
                  </select>
                </div>
                <PhCaretDown :size="13" weight="bold" class="absolute right-3.5 text-slate-400 pointer-events-none" />
              </div>

              <!-- Search Button -->
              <button
                @click="handleSearch"
                class="bg-gradient-to-r from-primary to-[#095296] hover:from-[#095296] hover:to-primary text-white px-6 sm:px-7 py-3 rounded-full text-sm font-bold flex items-center justify-center gap-2 transition-all shadow-md hover:shadow-lg hover:shadow-primary/30 active:scale-95 whitespace-nowrap cursor-pointer shrink-0"
                type="button"
              >
                <PhMagnifyingGlass :size="18" weight="bold" class="select-none" />
                <span>Cari</span>
              </button>
            </div>

            <!-- Popular Search Quick Chips (Single line horizontal scroll on mobile) -->
            <div class="flex items-center gap-2 mt-3 pt-0.5 overflow-x-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden flex-nowrap lg:flex-wrap max-w-full pb-1">
              <span class="text-xs font-semibold text-white/85 hidden sm:inline-block shrink-0">Pencarian Populer:</span>
              <button
                v-for="(tag, tIdx) in quickTags"
                :key="tIdx"
                @click="applyQuickTag(tag)"
                class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-medium transition shadow-xs cursor-pointer active:scale-95 border shrink-0 whitespace-nowrap"
                :class="isTagActive(tag) ? 'bg-amber-400 text-slate-900 border-amber-300 font-bold shadow-md' : 'bg-black/40 hover:bg-black/60 text-white/95 border-white/20 backdrop-blur-xs'"
                type="button"
              >
                <span>{{ tag.label }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Platform Advantages Band -->
    <section id="tentang" class="max-w-7xl mx-auto w-full px-margin py-space-xl">
      <div class="bg-surface-container-lowest rounded-2xl p-space-lg sm:p-space-xl border border-slate-200/80 shadow-xs grid grid-cols-1 md:grid-cols-3 gap-space-lg">
        <!-- Item 1 -->
        <div class="flex items-start gap-space-md">
          <div class="w-11 h-11 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
            <PhMapPin :size="24" weight="duotone" />
          </div>
          <div class="space-y-1">
            <h3 class="text-sm sm:text-base font-bold text-on-surface">Peta Lokasi Presisi</h3>
            <p class="text-xs text-on-surface-variant leading-relaxed">
              Titik lokasi terintegrasi Leaflet & OpenStreetMap untuk kemudahan survei rute kos tanpa tersesat.
            </p>
          </div>
        </div>

        <!-- Item 2 -->
        <div class="flex items-start gap-space-md">
          <div class="w-11 h-11 rounded-xl bg-tertiary/10 text-tertiary flex items-center justify-center shrink-0">
            <PhShieldCheck :size="24" weight="duotone" />
          </div>
          <div class="space-y-1">
            <h3 class="text-sm sm:text-base font-bold text-on-surface">Fasilitas & Harga Transparan</h3>
            <p class="text-xs text-on-surface-variant leading-relaxed">
              Cek tipe kamar mandi, fasilitas WiFi, parkir, dan ketersediaan unit sewa secara riil tanpa biaya tersembunyi.
            </p>
          </div>
        </div>

        <!-- Item 3 -->
        <div class="flex items-start gap-space-md">
          <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
            <PhWhatsappLogo :size="24" weight="duotone" />
          </div>
          <div class="space-y-1">
            <h3 class="text-sm sm:text-base font-bold text-on-surface">Hubungi Langsung Pemilik</h3>
            <p class="text-xs text-on-surface-variant leading-relaxed">
              Ajukan sewa transparan atau koordinasi jadwal survei langsung via WhatsApp ke pemilik properti resmi.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Popular Kos Section -->
    <section class="max-w-7xl mx-auto w-full px-margin pb-space-xl">
      <!-- Section Header -->
      <div class="flex items-center justify-between gap-space-md mb-space-lg">
        <div class="space-y-1">
          <h2 class="text-2xl font-bold text-on-surface tracking-tight">Kos Populer</h2>
          <p class="text-xs text-on-surface-variant">Paling banyak diminati dan berlokasi strategis di berbagai kota di Indonesia</p>
        </div>
        <router-link
          to="/kos"
          class="text-xs font-semibold text-primary hover:text-primary-container flex items-center gap-1 group"
        >
          <span>Lihat Semua</span>
          <PhArrowRight :size="16" weight="bold" class="group-hover:translate-x-1 transition-transform select-none" />
        </router-link>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-lg">
        <div v-for="i in 6" :key="i" class="h-80 bg-white rounded-xl border border-slate-100 animate-pulse"></div>
      </div>

      <!-- Cards Grid -->
      <div v-else-if="kosList.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-lg">
        <article
          v-for="kos in kosList"
          :key="kos.id"
          class="bg-surface-container-lowest rounded-xl shadow-sm hover:shadow-md transition-all flex flex-col overflow-hidden group border border-slate-100"
        >
          <!-- Thumbnail Image -->
          <div class="relative w-full aspect-[4/3] overflow-hidden bg-surface-container">
            <img
              :src="getImageForKos(kos.id)"
              :alt="kos.nama"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            />
            <div class="absolute top-3 left-3 flex items-center gap-1.5">
              <span
                v-if="kos.rating_rata_rata && Number(kos.rating_rata_rata) >= 4.5"
                class="bg-accent text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-xs tracking-wide"
              >
                Top Rating
              </span>
              <GenderBadge :jenis="kos.jenis" />
            </div>
            <button
              @click.stop.prevent="handleToggleFavorite(kos)"
              class="absolute top-3 right-3 min-w-[36px] min-h-[36px] sm:min-w-[40px] sm:min-h-[40px] rounded-full bg-surface-container-lowest/90 backdrop-blur-sm flex items-center justify-center shadow-sm transition-all"
              :class="favoritesStore.isFavorite(kos.id) ? 'text-error hover:scale-110' : 'text-secondary hover:text-error hover:scale-105'"
              :title="favoritesStore.isFavorite(kos.id) ? 'Hapus dari Favorit' : 'Simpan Kos ke Favorit'"
              type="button"
            >
              <PhHeart
                :size="18"
                :weight="favoritesStore.isFavorite(kos.id) ? 'fill' : 'bold'"
                class="select-none"
              />
            </button>
          </div>

          <!-- Card Body -->
          <div class="p-space-md flex-1 flex flex-col justify-between space-y-space-sm">
            <div>
              <div class="flex items-center justify-between gap-space-xs mb-1">
                <router-link :to="`/kos/${kos.id}`" class="text-base font-bold text-on-surface group-hover:text-primary transition-colors truncate">
                  {{ kos.nama }}
                </router-link>
                <div class="flex items-center gap-1 text-on-surface text-xs">
                  <PhStar :size="14" weight="fill" class="text-amber-500 select-none" />
                  <span class="font-semibold">{{ kos.rating_rata_rata ? Number(kos.rating_rata_rata).toFixed(1) : 'Baru' }}</span>
                  <span v-if="kos.ulasan_count" class="text-[10px] text-slate-400 font-normal">({{ kos.ulasan_count }})</span>
                </div>
              </div>

              <div class="flex items-center gap-1 text-on-surface-variant text-xs mb-space-sm">
                <PhMapPin :size="14" weight="bold" class="text-primary select-none shrink-0" />
                <span class="truncate">{{ kos.alamat }}</span>
              </div>

              <!-- Facilities Badges -->
              <div class="flex flex-wrap items-center gap-1.5 pt-1">
                <span
                  v-for="fas in (kos.fasilitas || []).slice(0, 3)"
                  :key="fas.id"
                  class="inline-flex items-center gap-1 bg-surface-container-low text-primary text-[11px] font-semibold px-2.5 py-1 rounded-full"
                >
                  <PhCheckCircle :size="13" weight="bold" class="select-none shrink-0" />
                  <span>{{ fas.nama }}</span>
                </span>
                <span v-if="(kos.fasilitas || []).length > 3" class="text-[11px] text-on-surface-variant">
                  +{{ kos.fasilitas.length - 3 }} lainnya
                </span>
              </div>
            </div>

            <!-- Price & Action -->
            <div class="pt-space-sm border-t border-slate-100 flex items-center justify-between">
              <div>
                <span class="text-base font-bold text-primary">{{ formatRupiah(kos.harga) }}</span>
                <span class="text-xs text-on-surface-variant font-normal"> / bln</span>
              </div>
              <router-link
                :to="`/kos/${kos.id}`"
                class="px-3 py-1.5 bg-surface-container-low hover:bg-surface-container text-primary text-xs font-semibold rounded-lg transition"
              >
                Lihat Detail
              </router-link>
            </div>
          </div>
        </article>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12 bg-white rounded-xl border border-slate-100">
        <p class="text-sm text-on-surface-variant">Belum ada properti kos yang tersedia.</p>
      </div>
    </section>

    <!-- Mobile Search Detail Pop-up Modal -->
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isMobileSearchOpen"
        class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-950/65 backdrop-blur-xs p-0 sm:p-4 select-auto"
        @click.self="closeMobileSearchModal"
      >
        <div
          class="w-full sm:max-w-md bg-white rounded-t-3xl sm:rounded-2xl shadow-2xl border border-slate-100 max-h-[88vh] flex flex-col overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-200"
        >
          <!-- Modal Header -->
          <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
              <h3 class="text-base font-bold text-slate-900">Cari Properti Kos</h3>
              <p class="text-xs text-slate-500 mt-0.5">Tentukan filter pencarian lebih detail</p>
            </div>
            <button
              @click="closeMobileSearchModal"
              type="button"
              class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 transition cursor-pointer"
              title="Tutup"
            >
              <PhX :size="18" weight="bold" />
            </button>
          </div>

          <!-- Modal Body -->
          <div class="p-5 space-y-4 overflow-y-auto">
            <!-- 1. Lokasi / Area -->
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Lokasi / Area / Kampus</label>
              <div class="flex items-center gap-2.5 px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus-within:bg-white focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/15 transition">
                <PhMapPin :size="20" weight="fill" class="text-primary/70 shrink-0" />
                <input
                  v-model="searchQuery"
                  type="text"
                  class="w-full bg-transparent text-sm font-semibold text-slate-800 placeholder:text-slate-400 border-none focus:outline-none focus:ring-0 p-0 leading-tight"
                  placeholder="Nama kota, jalan, area, atau kampus..."
                  @keyup.enter="handleMobileSearchSubmit"
                />
                <button
                  v-if="searchQuery"
                  @click="searchQuery = ''"
                  type="button"
                  class="p-1 rounded-full text-slate-400 hover:text-slate-600 cursor-pointer"
                >
                  <PhXCircle :size="16" weight="fill" />
                </button>
              </div>
            </div>

            <!-- 2. Tipe Hunian (Segmented Buttons) -->
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Tipe Hunian</label>
              <div class="grid grid-cols-4 gap-2">
                <button
                  v-for="opt in jenisOptions"
                  :key="opt.val"
                  @click="selectedJenis = opt.val"
                  type="button"
                  class="py-2.5 px-2 rounded-xl text-xs font-semibold text-center transition border cursor-pointer"
                  :class="selectedJenis === opt.val ? 'bg-primary text-white border-primary shadow-xs' : 'bg-slate-50 text-slate-700 border-slate-200/80 hover:bg-slate-100'"
                >
                  {{ opt.label }}
                </button>
              </div>
            </div>

            <!-- 3. Rentang Harga (Budget Cards) -->
            <div class="space-y-1.5">
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Rentang Budget</label>
              <div class="grid grid-cols-2 gap-2">
                <button
                  v-for="pOpt in priceOptions"
                  :key="pOpt.val"
                  @click="selectedPriceRange = pOpt.val"
                  type="button"
                  class="py-2.5 px-3 rounded-xl text-xs font-semibold text-left transition border cursor-pointer flex items-center justify-between"
                  :class="selectedPriceRange === pOpt.val ? 'bg-primary text-white border-primary shadow-xs' : 'bg-slate-50 text-slate-700 border-slate-200/80 hover:bg-slate-100'"
                >
                  <span>{{ pOpt.label }}</span>
                  <span v-if="selectedPriceRange === pOpt.val" class="w-2 h-2 rounded-full bg-amber-400 shrink-0"></span>
                </button>
              </div>
            </div>

            <!-- 4. Pencarian Populer (1 Baris Scrollable) -->
            <div class="space-y-1.5 pt-1">
              <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider">Pilihan Cepat Populer</label>
              <div class="flex items-center gap-1.5 overflow-x-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden flex-nowrap pb-1">
                <button
                  v-for="(tag, tIdx) in quickTags"
                  :key="tIdx"
                  @click="applyQuickTag(tag)"
                  type="button"
                  class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium transition cursor-pointer border shrink-0 whitespace-nowrap"
                  :class="isTagActive(tag) ? 'bg-amber-400 text-slate-900 border-amber-300 font-bold shadow-xs' : 'bg-slate-100 hover:bg-slate-200/70 text-slate-700 border-slate-200/70'"
                >
                  {{ tag.label }}
                </button>
              </div>
            </div>
          </div>

          <!-- Modal Footer CTA -->
          <div class="p-4 border-t border-slate-100 bg-slate-50 flex items-center gap-3">
            <button
              @click="resetSearchFilters"
              type="button"
              class="px-4 py-3 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200/60 transition cursor-pointer"
            >
              Reset
            </button>
            <button
              @click="handleMobileSearchSubmit"
              type="button"
              class="flex-1 py-3 px-5 rounded-xl bg-gradient-to-r from-primary to-[#095296] text-white text-sm font-bold shadow-md hover:shadow-lg active:scale-95 transition flex items-center justify-center gap-2 cursor-pointer"
            >
              <PhMagnifyingGlass :size="18" weight="bold" />
              <span>Tampilkan Kos</span>
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useFavoritesStore } from '../stores/favorites'
import { useUiStore } from '../stores/ui'
import api from '../services/api'
import GenderBadge from '../components/GenderBadge.vue'
import {
  PhMagnifyingGlass,
  PhBed,
  PhCurrencyCircleDollar,
  PhMapPin,
  PhShieldCheck,
  PhWhatsappLogo,
  PhArrowRight,
  PhHeart,
  PhStar,
  PhCheckCircle,
  PhCaretDown,
  PhCaretLeft,
  PhCaretRight,
  PhXCircle,
  PhSlidersHorizontal,
  PhX
} from '@phosphor-icons/vue'

const router = useRouter()
const route = useRoute()
const auth = useAuthStore()
const favoritesStore = useFavoritesStore()
const ui = useUiStore()

const searchQuery = ref('')
const selectedJenis = ref('')
const selectedPriceRange = ref('')
const kosList = ref([])
const loading = ref(false)

// Mobile Search Modal State
const isMobileSearchOpen = ref(false)

const openMobileSearchModal = () => {
  isMobileSearchOpen.value = true
}

const closeMobileSearchModal = () => {
  isMobileSearchOpen.value = false
}

const jenisOptions = [
  { label: 'Semua', val: '' },
  { label: 'Putra', val: 'putra' },
  { label: 'Putri', val: 'putri' },
  { label: 'Campur', val: 'campur' }
]

const priceOptions = [
  { label: 'Semua Harga', val: '' },
  { label: '< Rp 500rb', val: 'under500' },
  { label: 'Rp 500rb - 1jt', val: '500-1000' },
  { label: '> Rp 1jt', val: 'above1000' }
]

const activeSearchSummary = computed(() => {
  const parts = []
  if (searchQuery.value) parts.push(searchQuery.value)
  if (selectedJenis.value) {
    const formatted = selectedJenis.value.charAt(0).toUpperCase() + selectedJenis.value.slice(1)
    parts.push(`Kos ${formatted}`)
  }
  if (selectedPriceRange.value === 'under500') parts.push('< Rp 500rb')
  else if (selectedPriceRange.value === '500-1000') parts.push('Rp 500rb - 1jt')
  else if (selectedPriceRange.value === 'above1000') parts.push('> Rp 1jt')

  if (parts.length > 0) {
    return {
      title: parts[0],
      subtitle: parts.slice(1).join(' • ') || 'Filter aktif'
    }
  }
  return null
})

const resetSearchFilters = () => {
  searchQuery.value = ''
  selectedJenis.value = ''
  selectedPriceRange.value = ''
}

const handleMobileSearchSubmit = () => {
  closeMobileSearchModal()
  handleSearch()
}

// Popular Quick Tags
const quickTags = [
  { label: 'Jakarta', type: 'city', val: 'Jakarta' },
  { label: 'Yogyakarta', type: 'city', val: 'Yogyakarta' },
  { label: 'Bandung', type: 'city', val: 'Bandung' },
  { label: 'Kos Putri', type: 'jenis', val: 'putri' },
  { label: 'Kos Putra', type: 'jenis', val: 'putra' },
  { label: '< Rp 1 Juta', type: 'price', val: '500-1000' }
]

const isTagActive = (tag) => {
  if (tag.type === 'city') return searchQuery.value.trim().toLowerCase() === tag.val.toLowerCase()
  if (tag.type === 'jenis') return selectedJenis.value === tag.val
  if (tag.type === 'price') return selectedPriceRange.value === tag.val
  return false
}

const applyQuickTag = (tag) => {
  if (tag.type === 'city') {
    searchQuery.value = searchQuery.value === tag.val ? '' : tag.val
  } else if (tag.type === 'jenis') {
    selectedJenis.value = selectedJenis.value === tag.val ? '' : tag.val
  } else if (tag.type === 'price') {
    selectedPriceRange.value = selectedPriceRange.value === tag.val ? '' : tag.val
  }
  if (!isMobileSearchOpen.value) {
    handleSearch()
  }
}

// Hero Banner Slider State
const defaultSlides = [
  {
    id: 1,
    judul: 'Kos Nyaman, Hidup Lebih Mudah',
    subjudul: 'Temukan kos terbaik sesuai kebutuhanmu.\nLokasi strategis, fasilitas lengkap, harga transparan.',
    badge_text: 'Cari kos di seluruh Indonesia',
    gambar_url: 'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?q=80&w=2070&auto=format&fit=crop'
  },
  {
    id: 2,
    judul: 'Hunian Idaman, Dekat Kampus & Kantor',
    subjudul: 'Fasilitas lengkap dengan akses mudah, WiFi super cepat, dan lingkungan nyaman.',
    badge_text: 'Pilihan Hunian Eksklusif & Nyaman',
    gambar_url: 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?q=80&w=2071&auto=format&fit=crop'
  },
  {
    id: 3,
    judul: 'Pesan Kamar Kos, Langsung Huni',
    subjudul: 'Kemudahan reservasi langsung ke pemilik properti tanpa biaya tersembunyi.',
    badge_text: 'Sewa Transparan Bebas Cemas',
    gambar_url: 'https://images.unsplash.com/photo-1540518614846-7ede433c4ef7?q=80&w=2071&auto=format&fit=crop'
  },
  {
    id: 4,
    judul: 'Kenyamanan Maksimal, Harga Bersahabat',
    subjudul: 'Temukan kamar sewa dengan ulasan riil dan foto kondisi asli yang terverifikasi.',
    badge_text: 'Ribuan Pilihan Kos Terverifikasi',
    gambar_url: 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?q=80&w=2070&auto=format&fit=crop'
  }
]

const banners = ref([])
const currentSlideIndex = ref(0)
let sliderTimer = null
const isHovered = ref(false)

const activeBanners = computed(() => {
  if (banners.value && banners.value.length > 0) {
    return banners.value
  }
  return defaultSlides
})

const currentBanner = computed(() => {
  const list = activeBanners.value
  if (!list || list.length === 0) return defaultSlides[0]
  return list[currentSlideIndex.value % list.length]
})

// Splits title to highlight second part with golden amber
const formattedTitle = computed(() => {
  const full = currentBanner.value?.judul || 'Kos Nyaman, Hidup Lebih Mudah'
  if (full.includes(',')) {
    const parts = full.split(',')
    return {
      part1: parts[0].trim() + ',',
      part2: parts.slice(1).join(',').trim()
    }
  }
  return {
    part1: full,
    part2: ''
  }
})

const nextSlide = () => {
  if (activeBanners.value.length <= 1) return
  currentSlideIndex.value = (currentSlideIndex.value + 1) % activeBanners.value.length
}

const prevSlide = () => {
  if (activeBanners.value.length <= 1) return
  currentSlideIndex.value = (currentSlideIndex.value - 1 + activeBanners.value.length) % activeBanners.value.length
}

const goToSlide = (idx) => {
  currentSlideIndex.value = idx
  startSlider()
}

const startSlider = () => {
  stopSlider()
  sliderTimer = setInterval(() => {
    if (!isHovered.value && activeBanners.value.length > 1) {
      nextSlide()
    }
  }, 5000)
}

const stopSlider = () => {
  if (sliderTimer) {
    clearInterval(sliderTimer)
    sliderTimer = null
  }
}

const fetchHeroBanners = async () => {
  try {
    const res = await api.get('/hero-banners')
    if (res.data?.data && res.data.data.length > 0) {
      banners.value = res.data.data
      // Preload next images
      res.data.data.forEach((b) => {
        if (b.gambar_url) {
          const img = new Image()
          img.src = b.gambar_url
        }
      })
    }
  } catch (err) {
    // Graceful fallback to defaultSlides
  }
}

const handleToggleFavorite = (kos) => {
  if (!auth.isAuthenticated) {
    ui.warning('Silakan masuk terlebih dahulu untuk menyimpan kos favorit.')
    router.push({ name: 'login', query: { redirect: route.fullPath } })
    return
  }
  const isAdded = favoritesStore.toggleFavorite(kos)
  if (isAdded) {
    ui.success(`Kos "${kos.nama}" disimpan ke favorit!`)
  } else {
    ui.info(`Kos "${kos.nama}" dihapus dari favorit.`)
  }
}

// Sample default cover photos from Stitch design assets
const samplePhotos = [
  'https://lh3.googleusercontent.com/aida-public/AB6AXuBtAMGcNImL28Wkcksd1pB136MBTXtxAeUZZdLwny2E0TfcGGRkdrMA_JR6pVnktaHUu8Ffua13FdN3HdgZd29vhkeFDWM4WJtQJFMuZndi8o1HB2GXyn8ikhIgK45eAlZmqqiEVYkjMe7_m-Cvw0nxPY6cKLGKCrWp2Ch2dtBIlIqQb5pKjNVS1WaRhbnQPFyDftN7_uvVGXkv3Z_JUnsJpPxReDktVkZYw0eJ1YRW7caBLYvFdhslDw',
  'https://lh3.googleusercontent.com/aida-public/AB6AXuCOitClHgLDKT01wozYNVTlNFfj9k7oJNfa4q2kNrDk86lv3RxjjBUoPS6XVwSRz1fJt2Njq_q5ULmoPtKJ6tl88vke1gmGCs0RASAks5YEI7qsJO_F2vpf0H79xaVOLcj6daJIZt4_vYQD98RpawFS-fn8uc4Gg8SAZ6U1phc_LSoddYK5bxVMFELhOTo3bzl4rS6xJsZs6VtkRf6E9koZW5_wbWiwXvorevgASvvlZRP5P-63L62c2Q',
  'https://lh3.googleusercontent.com/aida-public/AB6AXuCq7lWrhImOr_FOsjNzINLkTN7b_5naoC4qeVNpyv6ugH4fnh9cc_IfxyVcenUHoVsykW_bUzYT96tFdwcpt9ghlRd6ji-wQjUfun67OQOq3T1S6XB2tOmVgvzykn_ra-KV7mRpZLQ09KBeHgpQj_rkik9wD8A8tT5piCUPOvWmwULPLmec2WUaeEHWcK2rVuT-D0jdYE8LleRBoUP8Vwlz6LcasWJw3DEPRxzciDC7dsnsgoobvasOfw',
  'https://lh3.googleusercontent.com/aida-public/AB6AXuBTwXv4Xzishf0BXTnGXeJGpf3six-aec8DUjhun4L1wAl49mDNbV4PPm9M7tACxaznDsA8efSHLFZXYg2Y2tqKbz4LG-PnJ_Izg77boMl0DFoZjIyMPJsP7f1m1TOy7WRSf9P9KWaENTrx0Q8oiX4WyyJUG--tFfpvnEr55wG2VWex3vG3bk3Oe7VB2DDfVDL3Hrqc01BGrDlFUw8nmuMIrr03zc8cscfCplekhP_8NAdixMSnIyhjFQ',
]

const getImageForKos = (id) => {
  return samplePhotos[(id - 1) % samplePhotos.length]
}

const formatRupiah = (val) => {
  if (!val) return 'Rp 0'
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val)
}

const handleSearch = () => {
  const query = {}
  if (searchQuery.value) query.q = searchQuery.value
  if (selectedJenis.value) query.jenis = selectedJenis.value

  if (selectedPriceRange.value === 'under500') {
    query.harga_max = 500000
  } else if (selectedPriceRange.value === '500-1000') {
    query.harga_min = 500000
    query.harga_max = 1000000
  } else if (selectedPriceRange.value === 'above1000') {
    query.harga_min = 1000000
  }

  router.push({ path: '/kos', query })
}

const fetchPopularKos = async () => {
  loading.value = true
  try {
    const res = await api.get('/kos', { params: { per_page: 6 } })
    kosList.value = res.data.data || []
  } catch (err) {
    // Abaikan error saat fetch
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchPopularKos()
  fetchHeroBanners()
  startSlider()
})

onUnmounted(() => {
  stopSlider()
})
</script>
