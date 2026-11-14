<template>
  <div class="w-full">
    <!-- Hero Section with Background Slider & Stable Visual Anchor -->
    <section
      class="relative w-full overflow-hidden group select-none"
      @mouseenter="isHovered = true"
      @mouseleave="isHovered = false"
      @touchstart="onTouchStart"
      @touchend="onTouchEnd"
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
          class="hidden lg:flex absolute left-6 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-slate-900/40 hover:bg-slate-900/80 backdrop-blur-md text-white items-center justify-center border border-white/20 transition-all opacity-0 group-hover:opacity-100 hover:scale-105 active:scale-95 shadow-lg cursor-pointer"
          title="Slide Sebelumnya"
          type="button"
        >
          <PhCaretLeft :size="20" weight="bold" />
        </button>
        <button
          v-if="activeBanners.length > 1"
          @click.stop="nextSlide"
          class="hidden lg:flex absolute right-6 top-1/2 -translate-y-1/2 z-20 w-11 h-11 rounded-full bg-slate-900/40 hover:bg-slate-900/80 backdrop-blur-md text-white items-center justify-center border border-white/20 transition-all opacity-0 group-hover:opacity-100 hover:scale-105 active:scale-95 shadow-lg cursor-pointer"
          title="Slide Selanjutnya"
          type="button"
        >
          <PhCaretRight :size="20" weight="bold" />
        </button>

        <!-- Main Hero Content -->
        <div class="relative z-10 max-w-7xl mx-auto w-full px-margin pt-24 sm:pt-28 lg:pt-30 pb-6 sm:pb-8 lg:pb-10 flex flex-col justify-between min-h-[520px] sm:min-h-[560px] lg:min-h-[620px]">
          <!-- Top Left Content: Badge, H1, Subtitle (Dynamic from Admin Hero Banner) -->
          <div class="max-w-3xl flex flex-col items-start text-left min-h-[200px] sm:min-h-[230px]">
            <!-- Badge: Location Pill / Custom Badge Text from Admin -->
            <transition
              enter-active-class="transition duration-500 ease-out"
              enter-from-class="opacity-0 -translate-y-2"
              enter-to-class="opacity-100 translate-y-0"
              leave-active-class="transition duration-200 ease-in"
              leave-from-class="opacity-100"
              leave-to-class="opacity-0"
              mode="out-in"
            >
              <div
                :key="currentSlideIndex + '-badge'"
                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-slate-900/60 backdrop-blur-md border border-white/15 text-white/95 text-xs sm:text-sm font-medium mb-4 sm:mb-5 shadow-sm"
              >
                <PhMapPin :size="16" weight="fill" class="text-amber-400 select-none shrink-0" />
                <span>{{ currentBanner?.badge_text || 'Cari kos di seluruh Indonesia' }}</span>
              </div>
            </transition>

            <!-- Headline: Dynamic with Admin Judul & Amber accent highlight -->
            <transition
              enter-active-class="transition duration-500 ease-out"
              enter-from-class="opacity-0 -translate-y-2"
              enter-to-class="opacity-100 translate-y-0"
              leave-active-class="transition duration-200 ease-in"
              leave-from-class="opacity-100"
              leave-to-class="opacity-0"
              mode="out-in"
            >
              <h1
                :key="currentSlideIndex + '-title'"
                class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-[1.15] drop-shadow-sm"
              >
                <span>{{ formattedTitle.part1 }}</span>
                <template v-if="formattedTitle.part2">
                  <br />
                  <span class="text-amber-400">{{ formattedTitle.part2 }}</span>
                </template>
              </h1>
            </transition>

            <!-- Subtitle (Dynamic Copy from Admin) -->
            <transition
              enter-active-class="transition duration-500 ease-out"
              enter-from-class="opacity-0 -translate-y-1"
              enter-to-class="opacity-100 translate-y-0"
              leave-active-class="transition duration-200 ease-in"
              leave-from-class="opacity-100"
              leave-to-class="opacity-0"
              mode="out-in"
            >
              <p
                :key="currentSlideIndex + '-sub'"
                class="text-sm sm:text-base lg:text-lg text-slate-200/95 max-w-xl mt-3 sm:mt-3.5 leading-relaxed font-normal drop-shadow-xs"
              >
                {{ currentBanner?.subjudul || 'Temukan kos terbaik sesuai kebutuhanmu di seluruh Indonesia. Lokasi strategis, fasilitas lengkap, dan harga transparan langsung dari pemilik.' }}
              </p>
            </transition>

            <!-- Slider Indicators & Accessible Controls -->
            <div v-if="activeBanners.length > 1" class="flex items-center gap-2.5 mt-4 sm:mt-5">
              <div class="flex items-center gap-1.5 p-1 bg-slate-900/50 backdrop-blur-md rounded-full border border-white/10">
                <button
                  v-for="(b, idx) in activeBanners"
                  :key="idx"
                  @click="goToSlide(idx)"
                  class="h-7 px-1 flex items-center justify-center cursor-pointer focus:outline-none"
                  :title="`Pindah ke foto ${idx + 1}`"
                  type="button"
                >
                  <span
                    class="h-2 rounded-full transition-all duration-300 block"
                    :class="idx === currentSlideIndex ? 'w-7 bg-amber-400' : 'w-2 bg-white/40 hover:bg-white/80'"
                  ></span>
                </button>
              </div>

              <!-- Pause / Play Toggle Button -->
        
            </div>
          </div>

          <!-- Bottom: Floating Search Bar Card (Desktop Segmented Pill & Mobile 1-Line - Clean & Spacious) -->
          <div class="w-full max-w-4xl mx-auto mt-6 sm:mt-8 -translate-y-4 sm:-translate-y-6 lg:-translate-y-8">
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
                    <p class="text-[11px] text-slate-500 font-medium truncate">{{ activeSearchSummary.subtitle }}</p>
                  </template>
                  <template v-else>
                    <p class="text-xs sm:text-sm font-semibold text-slate-700 truncate">Mau cari kos di mana?</p>
                    <p class="text-[11px] text-slate-500 font-medium">Lokasi &bull; Tipe &bull; Budget</p>
                  </template>
                </div>
              </div>

              <!-- Filter / Action button indicator -->
              <div class="w-9 h-9 rounded-full bg-gradient-to-r from-primary to-[#095296] text-white flex items-center justify-center shadow-sm shrink-0">
                <PhSlidersHorizontal :size="16" weight="bold" />
              </div>
            </div>

            <!-- 2. Desktop Segmented Search Bar (Pristine, Clean & Premium) -->
            <div
              ref="searchBarContainerRef"
              class="hidden lg:flex relative w-full bg-white rounded-full shadow-[0_14px_38px_-6px_rgba(15,23,42,0.14),0_4px_12px_-2px_rgba(15,23,42,0.06)] border border-slate-200/90 p-1.5 items-center transition-all"
            >
              <!-- Segment 1: Lokasi / Area -->
              <div
                @click="focusLocationInput"
                class="flex-1 flex items-center gap-3 px-4 py-2 rounded-full cursor-text transition-colors group/loc relative"
                :class="activeDropdown === 'location' ? 'bg-slate-50' : 'hover:bg-slate-50/80'"
              >
                <div
                  class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 transition-colors"
                  :class="searchQuery ? 'bg-primary/10 text-primary' : 'bg-slate-100 text-slate-500 group-hover/loc:text-primary'"
                >
                  <PhMapPin :size="16" weight="bold" />
                </div>
                <div class="flex-1 flex flex-col justify-center min-w-0">
                  <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block leading-none mb-1">
                    Lokasi / Area
                  </span>
                  <input
                    ref="locationInputRef"
                    v-model="searchQuery"
                    type="text"
                    class="w-full bg-transparent text-sm font-semibold text-slate-800 placeholder:text-slate-400 placeholder:font-normal border-none focus:outline-none focus:ring-0 p-0 leading-tight"
                    placeholder="Kota, area, atau kampus..."
                    @focus="activeDropdown = 'location'"
                    @keyup.enter="handleSearch"
                  />
                </div>
                <button
                  v-if="searchQuery"
                  @click.stop="searchQuery = ''"
                  class="p-1 rounded-full text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 transition shrink-0 cursor-pointer"
                  title="Hapus lokasi"
                  type="button"
                >
                  <PhXCircle :size="16" weight="fill" />
                </button>

                <!-- Location Suggestions Popover -->
                <transition
                  enter-active-class="transition duration-150 ease-out"
                  enter-from-class="transform scale-95 opacity-0 -translate-y-1"
                  enter-to-class="transform scale-100 opacity-100 translate-y-0"
                  leave-active-class="transition duration-100 ease-in"
                  leave-from-class="transform scale-100 opacity-100 translate-y-0"
                  leave-to-class="transform scale-95 opacity-0 -translate-y-1"
                >
                  <div
                    v-if="activeDropdown === 'location'"
                    class="absolute top-full left-0 mt-3 w-80 bg-white rounded-2xl shadow-xl border border-slate-100 p-4 z-40 text-left select-none"
                    @click.stop
                  >
                    <div class="flex items-center justify-between mb-2.5">
                      <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Kota Populer</span>
                      <span class="text-[11px] text-slate-500">Pilih cepat</span>
                    </div>
                    <div class="grid grid-cols-2 gap-1.5">
                      <button
                        v-for="city in popularCities"
                        :key="city"
                        type="button"
                        @click.stop="selectCity(city)"
                        class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold transition cursor-pointer text-left"
                        :class="searchQuery.toLowerCase() === city.toLowerCase()
                          ? 'bg-primary/10 text-primary font-bold'
                          : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900'"
                      >
                        <PhMapPin :size="13" weight="fill" class="text-primary/70 shrink-0" />
                        <span class="truncate">{{ city }}</span>
                      </button>
                    </div>
                  </div>
                </transition>
              </div>

              <!-- Divider 1 -->
              <div
                class="w-px h-7 bg-slate-200/80 shrink-0 transition-opacity"
                :class="{ 'opacity-0': activeDropdown === 'location' || activeDropdown === 'jenis' }"
              ></div>

              <!-- Segment 2: Tipe Hunian (Custom Clean Dropdown) -->
              <div class="relative min-w-[160px]">
                <button
                  type="button"
                  @click.stop="toggleDropdown('jenis')"
                  class="w-full flex items-center justify-between gap-2 px-4 py-2 rounded-full text-left transition-colors cursor-pointer group/type"
                  :class="activeDropdown === 'jenis' ? 'bg-slate-50' : 'hover:bg-slate-50/80'"
                >
                  <div class="flex items-center gap-2.5 min-w-0">
                    <div
                      class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 transition-colors"
                      :class="selectedJenis ? 'bg-primary/10 text-primary' : 'bg-slate-100 text-slate-500 group-hover/type:text-primary'"
                    >
                      <PhBed :size="16" weight="bold" />
                    </div>
                    <div class="flex flex-col justify-center min-w-0">
                      <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block leading-none mb-1">
                        Tipe Hunian
                      </span>
                      <span
                        class="text-sm font-semibold truncate leading-tight"
                        :class="selectedJenis ? 'text-primary' : 'text-slate-700'"
                      >
                        {{ getJenisLabel(selectedJenis) }}
                      </span>
                    </div>
                  </div>
                  <PhCaretDown
                    :size="12"
                    weight="bold"
                    class="text-slate-400 transition-transform duration-200 shrink-0"
                    :class="{ 'rotate-180 text-primary': activeDropdown === 'jenis' }"
                  />
                </button>

                <!-- Tipe Hunian Dropdown Menu -->
                <transition
                  enter-active-class="transition duration-150 ease-out"
                  enter-from-class="transform scale-95 opacity-0 -translate-y-1"
                  enter-to-class="transform scale-100 opacity-100 translate-y-0"
                  leave-active-class="transition duration-100 ease-in"
                  leave-from-class="transform scale-100 opacity-100 translate-y-0"
                  leave-to-class="transform scale-95 opacity-0 -translate-y-1"
                >
                  <div
                    v-if="activeDropdown === 'jenis'"
                    class="absolute top-full left-0 mt-3 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 p-2 z-40 select-none"
                    @click.stop
                  >
                    <div class="px-3 py-1.5 mb-1 border-b border-slate-100 flex items-center justify-between">
                      <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Filter Tipe Kos</span>
                      <button
                        v-if="selectedJenis"
                        @click.stop="selectJenis('')"
                        class="text-[11px] font-semibold text-primary hover:underline cursor-pointer"
                        type="button"
                      >
                        Reset
                      </button>
                    </div>
                    <button
                      v-for="opt in jenisOptions"
                      :key="opt.val"
                      type="button"
                      @click.stop="selectJenis(opt.val)"
                      class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition cursor-pointer text-left"
                      :class="selectedJenis === opt.val
                        ? 'bg-primary/10 text-primary font-bold'
                        : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900'"
                    >
                      <div>
                        <p class="leading-snug">{{ opt.val ? `Kos ${opt.label}` : 'Semua Tipe' }}</p>
                        <p class="text-[11px] font-normal text-slate-500">{{ opt.desc }}</p>
                      </div>
                      <PhCheck v-if="selectedJenis === opt.val" :size="14" weight="bold" class="text-primary shrink-0" />
                    </button>
                  </div>
                </transition>
              </div>

              <!-- Divider 2 -->
              <div
                class="w-px h-7 bg-slate-200/80 shrink-0 transition-opacity"
                :class="{ 'opacity-0': activeDropdown === 'jenis' || activeDropdown === 'price' }"
              ></div>

              <!-- Segment 3: Rentang Budget (Custom Clean Dropdown) -->
              <div class="relative min-w-[170px]">
                <button
                  type="button"
                  @click.stop="toggleDropdown('price')"
                  class="w-full flex items-center justify-between gap-2 px-4 py-2 rounded-full text-left transition-colors cursor-pointer group/price"
                  :class="activeDropdown === 'price' ? 'bg-slate-50' : 'hover:bg-slate-50/80'"
                >
                  <div class="flex items-center gap-2.5 min-w-0">
                    <div
                      class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 transition-colors"
                      :class="selectedPriceRange ? 'bg-primary/10 text-primary' : 'bg-slate-100 text-slate-500 group-hover/price:text-primary'"
                    >
                      <PhCurrencyCircleDollar :size="16" weight="bold" />
                    </div>
                    <div class="flex flex-col justify-center min-w-0">
                      <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block leading-none mb-1">
                        Budget
                      </span>
                      <span
                        class="text-sm font-semibold truncate leading-tight"
                        :class="selectedPriceRange ? 'text-primary' : 'text-slate-700'"
                      >
                        {{ getPriceLabel(selectedPriceRange) }}
                      </span>
                    </div>
                  </div>
                  <PhCaretDown
                    :size="12"
                    weight="bold"
                    class="text-slate-400 transition-transform duration-200 shrink-0"
                    :class="{ 'rotate-180 text-primary': activeDropdown === 'price' }"
                  />
                </button>

                <!-- Budget Dropdown Menu -->
                <transition
                  enter-active-class="transition duration-150 ease-out"
                  enter-from-class="transform scale-95 opacity-0 -translate-y-1"
                  enter-to-class="transform scale-100 opacity-100 translate-y-0"
                  leave-active-class="transition duration-100 ease-in"
                  leave-from-class="transform scale-100 opacity-100 translate-y-0"
                  leave-to-class="transform scale-95 opacity-0 -translate-y-1"
                >
                  <div
                    v-if="activeDropdown === 'price'"
                    class="absolute top-full left-0 mt-3 w-60 bg-white rounded-2xl shadow-xl border border-slate-100 p-2 z-40 select-none"
                    @click.stop
                  >
                    <div class="px-3 py-1.5 mb-1 border-b border-slate-100 flex items-center justify-between">
                      <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Rentang Budget</span>
                      <button
                        v-if="selectedPriceRange"
                        @click.stop="selectPrice('')"
                        class="text-[11px] font-semibold text-primary hover:underline cursor-pointer"
                        type="button"
                      >
                        Reset
                      </button>
                    </div>
                    <button
                      v-for="pOpt in priceOptions"
                      :key="pOpt.val"
                      type="button"
                      @click.stop="selectPrice(pOpt.val)"
                      class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-xs font-semibold transition cursor-pointer text-left"
                      :class="selectedPriceRange === pOpt.val
                        ? 'bg-primary/10 text-primary font-bold'
                        : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900'"
                    >
                      <div>
                        <p class="leading-snug">{{ pOpt.label }}</p>
                        <p class="text-[11px] font-normal text-slate-500">{{ pOpt.desc }}</p>
                      </div>
                      <PhCheck v-if="selectedPriceRange === pOpt.val" :size="14" weight="bold" class="text-primary shrink-0" />
                    </button>
                  </div>
                </transition>
              </div>

              <!-- Search CTA Button -->
              <button
                @click="handleSearch"
                class="bg-primary hover:bg-[#083569] text-white px-7 py-3 rounded-full text-sm font-bold flex items-center justify-center gap-2 transition-all shadow-sm hover:shadow-md hover:shadow-primary/20 active:scale-95 whitespace-nowrap cursor-pointer shrink-0 ml-1.5"
                type="button"
              >
                <PhMagnifyingGlass :size="17" weight="bold" class="select-none" />
                <span>Cari Kos</span>
              </button>
            </div>

            <!-- Popular Search Quick Chips (Clean Glass Minimal) -->
            <div class="flex items-center gap-2 mt-3 pt-0.5 overflow-x-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden flex-nowrap lg:flex-wrap max-w-full pb-1 justify-start lg:justify-center">
              <span class="text-xs font-medium text-slate-200/90 hidden sm:inline-flex items-center gap-1.5 shrink-0 drop-shadow-xs">
                <PhTrendUp :size="14" weight="bold" class="text-amber-400 select-none" />
                <span>Pencarian Populer:</span>
              </span>
              <button
                v-for="(tag, tIdx) in quickTags"
                :key="tIdx"
                @click="applyQuickTag(tag)"
                class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-medium transition cursor-pointer active:scale-95 border shrink-0 whitespace-nowrap backdrop-blur-md"
                :class="isTagActive(tag)
                  ? 'bg-amber-400 text-slate-900 border-amber-300 font-bold shadow-sm ring-1 ring-amber-400/40'
                  : 'bg-slate-900/40 hover:bg-slate-900/65 text-slate-200 hover:text-white border-white/10 shadow-2xs'"
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
      <div class="text-center max-w-xl mx-auto mb-space-lg">
        <h2 class="text-xl sm:text-2xl font-bold text-on-surface tracking-tight">Keunggulan Kostara</h2>
        <p class="text-xs text-on-surface-variant mt-1">Standar layanan hunian sewa yang jernih, transparan, dan tepercaya di Indonesia</p>
      </div>
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
        <div v-for="i in 6" :key="i" class="h-80 bg-white rounded-2xl border border-slate-100 animate-pulse"></div>
      </div>

      <!-- Cards Grid -->
      <div v-else-if="kosList.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-lg">
        <article
          v-for="kos in kosList"
          :key="kos.id"
          class="bg-surface-container-lowest rounded-2xl shadow-sm hover:shadow-md transition-all flex flex-col overflow-hidden group border border-slate-100"
        >
          <!-- Thumbnail Image (16:9 Cinematic Aspect Video) -->
          <div class="relative w-full aspect-video overflow-hidden bg-surface-container">
            <img
              :src="getImageForKos(kos.id)"
              :alt="kos.nama"
              class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            />
            <div class="absolute top-3 left-3 flex items-center gap-1.5 flex-wrap">
              <span
                v-if="kos.rating_rata_rata && Number(kos.rating_rata_rata) >= 4.5"
                class="bg-accent text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-xs tracking-wide"
              >
                Top Rating
              </span>
              <GenderBadge :jenis="kos.jenis" />
              <span
                class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500 text-white shadow-xs backdrop-blur-xs select-none"
              >
                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                Aktif
              </span>
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
            <div class="pt-space-sm border-t border-slate-100 flex items-center justify-between gap-2">
              <div class="min-w-0">
                <span class="text-base font-bold text-primary">{{ formatRupiah(kos.harga) }}</span>
                <span class="text-xs text-on-surface-variant font-normal"> / bln</span>
              </div>
              <div class="flex items-center gap-1.5 shrink-0">
                <button
                  @click.stop.prevent="handleWhatsAppInquiry(kos)"
                  class="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 hover:text-emerald-800 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1 border border-emerald-200/80 cursor-pointer active:scale-95 shadow-2xs"
                  title="Hubungi Pemilik via WhatsApp"
                  type="button"
                >
                  <PhWhatsappLogo :size="15" weight="fill" class="shrink-0 text-emerald-600" />
                  <span>WA</span>
                </button>
                <router-link
                  :to="`/kos/${kos.id}`"
                  class="px-3.5 py-1.5 bg-primary hover:bg-primary-hover text-white text-xs font-bold rounded-xl transition shadow-xs active:scale-95"
                >
                  Lihat Detail
                </router-link>
              </div>
            </div>
          </div>
        </article>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12 bg-white rounded-2xl border border-slate-100">
        <p class="text-sm text-on-surface-variant">Belum ada properti kos yang tersedia.</p>
      </div>
    </section>

    <!-- Campus & City Quick Shortcuts -->
    <section class="max-w-7xl mx-auto w-full px-margin pb-space-xl">
      <div class="bg-surface-container-low/60 rounded-2xl p-space-lg sm:p-space-xl border border-slate-200/70">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-space-md">
          <div>
            <h2 class="text-lg sm:text-xl font-bold text-on-surface tracking-tight">Cari Kos Dekat Kampus Populer</h2>
            <p class="text-xs text-on-surface-variant mt-0.5">Temukan hunian kos terdekat dengan kampus impian Anda di berbagai kota</p>
          </div>
        </div>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="campus in popularCampuses"
            :key="campus.name"
            @click="searchByCampus(campus.query)"
            type="button"
            class="px-3.5 py-2 rounded-xl bg-white hover:bg-primary hover:text-white border border-slate-200/90 text-slate-700 text-xs font-semibold shadow-2xs transition-all flex items-center gap-1.5 cursor-pointer active:scale-95 group"
          >
            <PhGraduationCap :size="16" weight="bold" class="text-primary group-hover:text-white transition-colors" />
            <span>{{ campus.name }}</span>
            <span class="text-[10px] text-slate-400 group-hover:text-white/80 font-normal">({{ campus.city }})</span>
          </button>
        </div>
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
              class="w-10 h-10 min-w-[40px] rounded-full flex items-center justify-center text-slate-500 hover:text-slate-800 hover:bg-slate-200/70 transition cursor-pointer active:scale-95"
              aria-label="Tutup pencarian"
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
                  <div class="min-w-0">
                    <p class="truncate leading-tight">{{ pOpt.label }}</p>
                    <p class="text-[10px] opacity-75 font-normal truncate">{{ pOpt.desc }}</p>
                  </div>
                  <span v-if="selectedPriceRange === pOpt.val" class="w-2 h-2 rounded-full bg-amber-400 shrink-0 ml-1"></span>
                </button>
              </div>
            </div>

            <!-- 4. Pencarian Populer (1 Baris Scrollable) -->
            <div class="space-y-1.5 pt-1">
              <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Pilihan Cepat Populer</label>
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
              class="flex-1 py-3 px-5 rounded-xl bg-primary hover:bg-[#083569] text-white text-sm font-bold shadow-md hover:shadow-lg active:scale-95 transition flex items-center justify-center gap-2 cursor-pointer"
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
import { openWhatsAppChat, buildInquiryTemplate } from '../utils/whatsapp'
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
  PhCheck,
  PhTrendUp,
  PhCaretDown,
  PhCaretLeft,
  PhCaretRight,
  PhXCircle,
  PhSlidersHorizontal,
  PhX,
  PhPause,
  PhPlay,
  PhGraduationCap
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

// Desktop Dropdowns & Popovers
const searchBarContainerRef = ref(null)
const locationInputRef = ref(null)
const activeDropdown = ref(null) // 'location' | 'jenis' | 'price' | null

const popularCities = ['Yogyakarta', 'Jakarta', 'Bandung', 'Surabaya', 'Malang', 'Semarang']

const toggleDropdown = (name) => {
  if (activeDropdown.value === name) {
    activeDropdown.value = null
  } else {
    activeDropdown.value = name
  }
}

const selectJenis = (val) => {
  selectedJenis.value = val
  activeDropdown.value = null
}

const selectPrice = (val) => {
  selectedPriceRange.value = val
  activeDropdown.value = null
}

const selectCity = (city) => {
  searchQuery.value = city
  activeDropdown.value = null
}

const focusLocationInput = () => {
  activeDropdown.value = 'location'
  locationInputRef.value?.focus()
}

const getJenisLabel = (val) => {
  if (!val) return 'Semua Tipe'
  const found = jenisOptions.find((o) => o.val === val)
  return found ? (found.val ? `Kos ${found.label}` : 'Semua Tipe') : 'Semua Tipe'
}

const getPriceLabel = (val) => {
  if (!val) return 'Semua Harga'
  const found = priceOptions.find((o) => o.val === val)
  return found ? found.label : 'Semua Harga'
}

const handleClickOutside = (e) => {
  if (searchBarContainerRef.value && !searchBarContainerRef.value.contains(e.target)) {
    activeDropdown.value = null
  }
}

// Mobile Search Modal State
const isMobileSearchOpen = ref(false)

const openMobileSearchModal = () => {
  isMobileSearchOpen.value = true
}

const closeMobileSearchModal = () => {
  isMobileSearchOpen.value = false
}

const jenisOptions = [
  { label: 'Semua', val: '', desc: 'Semua tipe hunian' },
  { label: 'Putra', val: 'putra', desc: 'Khusus pria' },
  { label: 'Putri', val: 'putri', desc: 'Khusus wanita' },
  { label: 'Campur', val: 'campur', desc: 'Pria & wanita' }
]

const priceOptions = [
  { label: 'Semua Harga', val: '', desc: 'Tanpa batas budget' },
  { label: '< Rp 1 Juta', val: 'under1000', desc: 'Hemat & ekonomis' },
  { label: 'Rp 1 - 2 Juta', val: '1000-2000', desc: 'Standar paling populer' },
  { label: 'Rp 2 - 3.5 Juta', val: '2000-3500', desc: 'Kamar mandi dalam & AC' },
  { label: '> Rp 3.5 Juta', val: 'above3500', desc: 'Eksklusif & premium' }
]

const activeSearchSummary = computed(() => {
  const parts = []
  if (searchQuery.value) parts.push(searchQuery.value)
  if (selectedJenis.value) {
    const formatted = selectedJenis.value.charAt(0).toUpperCase() + selectedJenis.value.slice(1)
    parts.push(`Kos ${formatted}`)
  }
  if (selectedPriceRange.value === 'under1000') parts.push('< Rp 1jt')
  else if (selectedPriceRange.value === '1000-2000') parts.push('Rp 1 - 2jt')
  else if (selectedPriceRange.value === '2000-3500') parts.push('Rp 2 - 3.5jt')
  else if (selectedPriceRange.value === 'above3500') parts.push('> Rp 3.5jt')

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
  { label: 'Yogyakarta', type: 'city', val: 'Yogyakarta' },
  { label: 'Jakarta', type: 'city', val: 'Jakarta' },
  { label: 'Bandung', type: 'city', val: 'Bandung' },
  { label: 'Kos Putri', type: 'jenis', val: 'putri' },
  { label: 'Kos Putra', type: 'jenis', val: 'putra' },
  { label: '< Rp 1 Juta', type: 'price', val: 'under1000' }
]

// Popular Campus Directory
const popularCampuses = [
  { name: 'UGM', city: 'Yogyakarta', query: 'UGM' },
  { name: 'UI', city: 'Depok', query: 'UI' },
  { name: 'ITB', city: 'Bandung', query: 'ITB' },
  { name: 'UNDIP', city: 'Semarang', query: 'UNDIP' },
  { name: 'UNAIR', city: 'Surabaya', query: 'UNAIR' },
  { name: 'Univ. Brawijaya', city: 'Malang', query: 'Brawijaya' },
  { name: 'UNS', city: 'Surakarta', query: 'UNS' },
  { name: 'UNY', city: 'Yogyakarta', query: 'UNY' }
]

const searchByCampus = (query) => {
  searchQuery.value = query
  handleSearch()
}

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
  const words = full.split(' ')
  if (words.length > 2) {
    const midpoint = Math.ceil(words.length / 2)
    return {
      part1: words.slice(0, midpoint).join(' '),
      part2: words.slice(midpoint).join(' ')
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

const isAutoPlayPaused = ref(false)

const toggleAutoPlay = () => {
  isAutoPlayPaused.value = !isAutoPlayPaused.value
}

let touchStartX = 0
let touchEndX = 0

const onTouchStart = (e) => {
  isHovered.value = true
  if (e.changedTouches && e.changedTouches[0]) {
    touchStartX = e.changedTouches[0].screenX
  }
}

const onTouchEnd = (e) => {
  isHovered.value = false
  if (e.changedTouches && e.changedTouches[0]) {
    touchEndX = e.changedTouches[0].screenX
    if (touchStartX - touchEndX > 50) {
      nextSlide()
    } else if (touchEndX - touchStartX > 50) {
      prevSlide()
    }
  }
}

const goToSlide = (idx) => {
  currentSlideIndex.value = idx
  startSlider()
}

const startSlider = () => {
  stopSlider()
  sliderTimer = setInterval(() => {
    if (
      !isHovered.value &&
      !isAutoPlayPaused.value &&
      activeDropdown.value === null &&
      !isMobileSearchOpen.value &&
      activeBanners.value.length > 1
    ) {
      nextSlide()
    }
  }, 6000)
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

const handleWhatsAppInquiry = (kos) => {
  const phone = kos.pemilik?.telepon || kos.pemilik?.no_hp
  const ownerName = kos.pemilik?.nama || 'Pemilik Kos'
  const kosUrl = `${window.location.origin}/kos/${kos.id}`
  const message = buildInquiryTemplate({
    ownerName,
    kosName: kos.nama || 'Kos',
    kosUrl,
  })

  if (!phone) {
    ui.info(`Nomor WhatsApp pemilik ${kos.nama} belum tersedia. Silakan periksa halaman detail properti.`)
    router.push(`/kos/${kos.id}`)
    return
  }

  openWhatsAppChat(phone, message)
}

const handleToggleFavorite = (kos) => {
  if (!auth.isAuthenticated) {
    ui.warning('Silakan masuk (login) terlebih dahulu untuk menyimpan kos ke favorit.')
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
  activeDropdown.value = null
  const query = {}
  if (searchQuery.value) query.q = searchQuery.value
  if (selectedJenis.value) query.jenis = selectedJenis.value

  if (selectedPriceRange.value === 'under1000') {
    query.harga_max = 1000000
  } else if (selectedPriceRange.value === '1000-2000') {
    query.harga_min = 1000000
    query.harga_max = 2000000
  } else if (selectedPriceRange.value === '2000-3500') {
    query.harga_min = 2000000
    query.harga_max = 3500000
  } else if (selectedPriceRange.value === 'above3500') {
    query.harga_min = 3500000
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
  window.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  stopSlider()
  window.removeEventListener('click', handleClickOutside)
})
</script>
