<template>
  <div class="min-h-screen bg-slate-900 text-slate-100 flex flex-col font-sans select-text">
    <!-- Top Global Header / Navigation -->
    <header class="bg-slate-950/90 border-b border-slate-800 sticky top-0 z-40 backdrop-blur-md px-4 lg:px-6 py-3">
      <div class="max-w-7xl mx-auto flex flex-col md:flex-row md:items-center justify-between gap-3">
        <!-- Logo & Title -->
        <div class="flex items-center gap-3">
          <router-link to="/" class="flex items-center gap-2 group">
            <img src="../assets/logo.png" alt="Kostara Logo" class="w-8 h-8 object-contain group-hover:scale-105 transition-transform" />
            <div class="flex items-center text-lg font-black tracking-tight">
              <span class="text-[#017E91]">Kos</span>
              <span class="text-[#E68202]">tara</span>
            </div>
          </router-link>
          <div class="h-5 w-px bg-slate-700 hidden sm:block"></div>
          <div class="flex items-center gap-2">
            <span class="px-2 py-0.5 text-xs font-bold bg-[#017E91]/20 text-[#38bdf8] border border-[#017E91]/40 rounded-md">
              API TEST SUITE v1
            </span>
            <span class="text-xs text-slate-400 hidden lg:inline">
              Interactive REST API Playground & Tester
            </span>
          </div>
        </div>

        <!-- Quick Environment & Auth State Bar -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 text-xs">
          <!-- Base URL Configuration -->
          <div class="flex items-center bg-slate-900 border border-slate-700/80 rounded-lg px-2.5 py-1.5 gap-2">
            <span class="text-slate-400 font-semibold flex items-center gap-1">
              <PhGlobe :size="14" class="text-primary" /> Base:
            </span>
            <input
              v-model="baseUrl"
              type="text"
              class="bg-transparent text-slate-200 outline-none w-44 sm:w-56 text-xs font-mono"
              placeholder="http://127.0.0.1:8000/api/v1"
            />
            <button
              @click="resetBaseUrl"
              class="text-slate-500 hover:text-slate-300 transition"
              title="Reset ke default"
            >
              <PhArrowClockwise :size="13" />
            </button>
          </div>

          <!-- Active User Badge / Quick Logins -->
          <div class="flex items-center gap-2">
            <div
              v-if="authToken"
              class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-400"
            >
              <PhShieldCheck :size="14" weight="bold" />
              <span class="font-bold capitalize">{{ currentRoleDisplay }}</span>
              <button
                @click="clearAuthToken"
                class="ml-1 text-slate-400 hover:text-rose-400 transition"
                title="Hapus token autentikasi"
              >
                <PhX :size="13" />
              </button>
            </div>
            <div v-else class="px-2.5 py-1.5 rounded-lg bg-slate-800 text-slate-400 border border-slate-700">
              <span class="flex items-center gap-1"><PhUser :size="13" /> Guest (No Auth)</span>
            </div>

            <!-- Quick Login Dropdown or Buttons -->
            <div class="flex items-center gap-1">
              <button
                @click="quickLogin('admin@kos.test', 'password')"
                :disabled="isLoggingIn"
                class="px-2.5 py-1.5 bg-primary/20 text-[#38bdf8] hover:bg-primary hover:text-white active:scale-95 border border-primary/40 rounded-lg font-bold transition cursor-pointer disabled:opacity-50 text-[11px]"
                title="Login cepat sebagai Admin (admin@kos.test)"
              >
                Admin
              </button>
              <button
                @click="quickLogin('pemilik@kos.test', 'password')"
                :disabled="isLoggingIn"
                class="px-2.5 py-1.5 bg-amber-500/20 text-amber-300 hover:bg-amber-500 hover:text-white active:scale-95 border border-amber-500/40 rounded-lg font-bold transition cursor-pointer disabled:opacity-50 text-[11px]"
                title="Login cepat sebagai Mitra Pemilik (pemilik@kos.test)"
              >
                Pemilik
              </button>
              <button
                @click="quickLogin('pencari@kos.test', 'password')"
                :disabled="isLoggingIn"
                class="px-2.5 py-1.5 bg-emerald-500/20 text-emerald-300 hover:bg-emerald-500 hover:text-white active:scale-95 border border-emerald-500/40 rounded-lg font-bold transition cursor-pointer disabled:opacity-50 text-[11px]"
                title="Login cepat sebagai Pencari Kos (pencari@kos.test)"
              >
                Pencari
              </button>
              <button
                @click="showTokenModal = true"
                class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 rounded-md transition cursor-pointer"
                title="Atur Kustom Bearer Token"
              >
                <PhKey :size="15" />
              </button>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Playground Workspace -->
    <div class="flex-1 max-w-7xl w-full mx-auto p-4 lg:p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 min-h-0">
      <!-- Left Column: Endpoints Directory (lg:col-span-4) -->
      <aside class="lg:col-span-4 flex flex-col bg-slate-950/70 border border-slate-800 rounded-2xl overflow-hidden max-h-[88vh]">
        <!-- Search & Filter Controls -->
        <div class="p-3.5 border-b border-slate-800 space-y-2.5 bg-slate-950/90">
          <div class="relative">
            <PhMagnifyingGlass :size="15" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari endpoint, URL, atau kata kunci..."
              class="w-full bg-slate-900 border border-slate-700/80 rounded-xl pl-8 pr-3 py-1.5 text-xs text-slate-200 placeholder:text-slate-500 outline-none focus:border-primary transition"
            />
            <button
              v-if="searchQuery"
              @click="searchQuery = ''"
              class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-300"
            >
              <PhX :size="13" />
            </button>
          </div>

          <!-- Method Pills Filter -->
          <div class="flex flex-wrap items-center gap-1">
            <button
              v-for="m in ['ALL', 'GET', 'POST', 'PUT', 'PATCH', 'DELETE']"
              :key="m"
              @click="selectedMethodFilter = m"
              :class="[
                'px-2 py-0.5 rounded text-[10px] font-bold tracking-wider transition cursor-pointer',
                selectedMethodFilter === m
                  ? 'bg-primary text-white shadow-xs'
                  : 'bg-slate-900 text-slate-400 hover:bg-slate-800 hover:text-slate-200'
              ]"
            >
              {{ m }}
            </button>
          </div>

          <!-- Category Selector -->
          <select
            v-model="selectedCategory"
            class="w-full bg-slate-900 border border-slate-700/80 rounded-xl px-2.5 py-1.5 text-xs text-slate-300 outline-none focus:border-primary"
          >
            <option v-for="cat in apiCategories" :key="cat" :value="cat">
              {{ cat === 'Semua' ? 'Semua Kategori (' + apiEndpoints.length + ')' : cat }}
            </option>
          </select>
        </div>

        <!-- Endpoints List -->
        <div class="flex-1 overflow-y-auto divide-y divide-slate-800/60 p-1.5 space-y-1">
          <div
            v-if="filteredEndpoints.length === 0"
            class="p-6 text-center text-slate-500 text-xs"
          >
            Tidak ada endpoint yang cocok dengan filter.
          </div>

          <button
            v-for="ep in filteredEndpoints"
            :key="ep.id"
            @click="selectEndpoint(ep)"
            :class="[
              'w-full text-left p-2.5 rounded-xl transition flex flex-col gap-1 cursor-pointer select-none',
              selectedEndpoint?.id === ep.id
                ? 'bg-slate-800/90 border border-primary/50 shadow-sm'
                : 'hover:bg-slate-900/80 border border-transparent'
            ]"
          >
            <div class="flex items-center justify-between gap-2">
              <span :class="['px-1.5 py-0.5 rounded text-[10px] font-extrabold tracking-wide uppercase', methodBadgeClass(ep.method)]">
                {{ ep.method }}
              </span>
              <span class="text-[10px] text-slate-400 truncate max-w-[150px] text-right font-medium">
                {{ ep.category }}
              </span>
            </div>

            <div class="text-xs font-semibold text-slate-200 truncate">
              {{ ep.name }}
            </div>

            <div class="flex items-center justify-between text-[11px] font-mono text-slate-400 truncate">
              <span class="truncate">{{ ep.path }}</span>
              <span
                v-if="ep.auth !== 'public'"
                class="shrink-0 text-[10px] text-amber-400/90 flex items-center gap-0.5"
                :title="'Membutuhkan hak akses: ' + ep.auth"
              >
                <PhLock :size="10" weight="bold" />
                <span class="capitalize">{{ ep.auth }}</span>
              </span>
            </div>
          </button>
        </div>

        <!-- Footer Stats -->
        <div class="p-2.5 border-t border-slate-800 bg-slate-950 text-[11px] text-slate-500 flex items-center justify-between">
          <span>Menampilkan {{ filteredEndpoints.length }} dari {{ apiEndpoints.length }} endpoint</span>
          <router-link to="/about" class="text-primary hover:underline">Tentang API</router-link>
        </div>
      </aside>

      <!-- Right Column: Interactive Tester & Playground (lg:col-span-8) -->
      <main class="lg:col-span-8 flex flex-col gap-5 min-h-0">
        <div v-if="!selectedEndpoint" class="bg-slate-950/70 border border-slate-800 rounded-2xl p-12 text-center text-slate-500">
          <PhCode :size="48" class="mx-auto mb-3 text-slate-600" />
          <h3 class="text-base font-bold text-slate-300">Pilih Endpoint untuk Mulai Menguji</h3>
          <p class="text-xs mt-1 text-slate-500 max-w-md mx-auto">
            Pilih salah satu endpoint pada panel sebelah kiri untuk memuat parameter bawaan, payload JSON, dan mengujinya secara interaktif.
          </p>
        </div>

        <template v-else>
          <!-- Request Builder Panel -->
          <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-4 sm:p-5 flex flex-col gap-4">
            <!-- Endpoint Header & Title -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-800">
              <div>
                <div class="flex items-center gap-2">
                  <span :class="['px-2 py-0.5 rounded text-xs font-black uppercase', methodBadgeClass(currentMethod)]">
                    {{ currentMethod }}
                  </span>
                  <h2 class="text-base font-bold text-slate-100">
                    {{ selectedEndpoint.name }}
                  </h2>
                </div>
                <p class="text-xs text-slate-400 mt-1">
                  {{ selectedEndpoint.description }}
                </p>
              </div>

              <!-- Required Permission Badge -->
              <div class="shrink-0">
                <span
                  v-if="selectedEndpoint.auth === 'public'"
                  class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30"
                >
                  <PhCheckCircle :size="13" /> Publik (Tanpa Token)
                </span>
                <span
                  v-else
                  class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30"
                >
                  <PhLock :size="13" /> Auth: {{ selectedEndpoint.auth }}
                </span>
              </div>
            </div>

            <!-- URL Bar & Action Buttons -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
              <div class="flex-1 flex items-center bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs font-mono overflow-hidden focus-within:border-primary transition">
                <span class="text-slate-500 select-none mr-1">{{ baseUrl }}</span>
                <input
                  v-model="computedPath"
                  type="text"
                  class="flex-1 bg-transparent text-slate-100 outline-none font-mono"
                  placeholder="/endpoint"
                />
              </div>

              <!-- Send Request Button -->
              <button
                @click="sendRequest"
                :disabled="isLoading"
                class="px-5 py-2 bg-[#017E91] hover:bg-[#016979] active:scale-95 text-white text-xs font-bold rounded-xl flex items-center justify-center gap-2 transition cursor-pointer shadow-md disabled:opacity-50"
              >
                <PhCircleNotch v-if="isLoading" :size="16" class="animate-spin" />
                <PhPlay v-else :size="16" weight="fill" />
                <span>{{ isLoading ? 'Mengirim...' : 'Kirim Request' }}</span>
              </button>

              <!-- Copy cURL -->
              <button
                @click="copyCurlCommand"
                class="px-3 py-2 bg-slate-900 hover:bg-slate-800 active:scale-95 text-slate-300 border border-slate-700 text-xs font-semibold rounded-xl flex items-center justify-center gap-1.5 transition cursor-pointer"
                title="Salin sebagai perintah cURL"
              >
                <PhTerminal :size="15" />
                <span class="hidden sm:inline">cURL</span>
              </button>
            </div>

            <!-- Request Configuration Tabs (Params, Body, Headers, Docs) -->
            <div>
              <div class="flex items-center gap-2 border-b border-slate-800 text-xs font-semibold">
                <button
                  @click="activeReqTab = 'params'"
                  :class="[
                    'pb-2 px-3 transition cursor-pointer relative',
                    activeReqTab === 'params' ? 'text-primary font-bold' : 'text-slate-400 hover:text-slate-200'
                  ]"
                >
                  Params ({{ pathParamsList.length + queryParamsList.length }})
                  <span v-if="activeReqTab === 'params'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary rounded-full"></span>
                </button>

                <button
                  v-if="['POST', 'PUT', 'PATCH'].includes(currentMethod)"
                  @click="activeReqTab = 'body'"
                  :class="[
                    'pb-2 px-3 transition cursor-pointer relative',
                    activeReqTab === 'body' ? 'text-primary font-bold' : 'text-slate-400 hover:text-slate-200'
                  ]"
                >
                  Body ({{ selectedEndpoint.bodyType || 'json' }})
                  <span v-if="activeReqTab === 'body'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary rounded-full"></span>
                </button>

                <button
                  @click="activeReqTab = 'headers'"
                  :class="[
                    'pb-2 px-3 transition cursor-pointer relative',
                    activeReqTab === 'headers' ? 'text-primary font-bold' : 'text-slate-400 hover:text-slate-200'
                  ]"
                >
                  Headers ({{ headersList.length }})
                  <span v-if="activeReqTab === 'headers'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary rounded-full"></span>
                </button>

                <button
                  @click="activeReqTab = 'docs'"
                  :class="[
                    'pb-2 px-3 transition cursor-pointer relative',
                    activeReqTab === 'docs' ? 'text-primary font-bold' : 'text-slate-400 hover:text-slate-200'
                  ]"
                >
                  Dokumentasi
                  <span v-if="activeReqTab === 'docs'" class="absolute bottom-0 left-0 right-0 h-0.5 bg-primary rounded-full"></span>
                </button>
              </div>

              <!-- Tab Contents -->
              <div class="pt-3 min-h-[140px]">
                <!-- Tab: Params -->
                <div v-if="activeReqTab === 'params'" class="space-y-4">
                  <!-- Path Parameters -->
                  <div v-if="pathParamsList.length > 0">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">
                      Path Parameters ({...})
                    </span>
                    <div class="space-y-2">
                      <div
                        v-for="p in pathParamsList"
                        :key="p.key"
                        class="grid grid-cols-12 gap-2 items-center text-xs"
                      >
                        <div class="col-span-4 font-mono font-bold text-primary truncate">
                          {{ p.key }}
                        </div>
                        <input
                          v-model="p.value"
                          type="text"
                          :placeholder="p.description || 'Nilai parameter'"
                          class="col-span-8 bg-slate-900 border border-slate-700/80 rounded-lg px-2.5 py-1.5 text-xs text-slate-200 outline-none focus:border-primary font-mono"
                        />
                      </div>
                    </div>
                  </div>

                  <!-- Query Parameters -->
                  <div>
                    <div class="flex items-center justify-between mb-2">
                      <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        Query Parameters (?key=value)
                      </span>
                      <button
                        @click="addQueryParam"
                        class="text-[11px] text-primary hover:underline flex items-center gap-1 cursor-pointer"
                      >
                        <PhPlus :size="12" /> Tambah Param
                      </button>
                    </div>

                    <div v-if="queryParamsList.length === 0" class="text-xs text-slate-500 italic">
                      Tidak ada query parameter default. Klik "+ Tambah Param" jika ingin menambahkan.
                    </div>

                    <div v-else class="space-y-2">
                      <div
                        v-for="(q, idx) in queryParamsList"
                        :key="idx"
                        class="flex items-center gap-2 text-xs"
                      >
                        <input
                          v-model="q.enabled"
                          type="checkbox"
                          class="rounded bg-slate-900 border-slate-700 text-primary focus:ring-0 cursor-pointer"
                        />
                        <input
                          v-model="q.key"
                          type="text"
                          placeholder="Key (mis: page)"
                          class="w-1/3 bg-slate-900 border border-slate-700/80 rounded-lg px-2.5 py-1.5 text-xs font-mono text-slate-200 outline-none focus:border-primary"
                        />
                        <input
                          v-model="q.value"
                          type="text"
                          placeholder="Value"
                          class="flex-1 bg-slate-900 border border-slate-700/80 rounded-lg px-2.5 py-1.5 text-xs font-mono text-slate-200 outline-none focus:border-primary"
                        />
                        <button
                          @click="removeQueryParam(idx)"
                          class="p-1.5 text-slate-500 hover:text-rose-400 transition cursor-pointer"
                        >
                          <PhTrash :size="14" />
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Tab: Body -->
                <div v-else-if="activeReqTab === 'body'" class="space-y-3">
                  <div class="flex items-center justify-between">
                    <span class="text-[11px] text-slate-400 font-medium">
                      Format: JSON Payload (application/json)
                    </span>
                    <button
                      @click="formatJsonBody"
                      class="text-[11px] text-primary hover:underline flex items-center gap-1 cursor-pointer font-medium"
                    >
                      <PhCode :size="13" /> Rapikan JSON
                    </button>
                  </div>

                  <div v-if="selectedEndpoint.bodyType === 'form-data'" class="p-3 bg-slate-900/90 border border-slate-700/80 rounded-xl space-y-2">
                    <span class="text-xs text-amber-400 font-semibold block">
                      Endpoint ini mendukung berkas form-data (File Upload):
                    </span>
                    <input
                      type="file"
                      ref="fileInputRef"
                      @change="handleFileSelected"
                      multiple
                      class="text-xs text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-hover cursor-pointer"
                    />
                    <p class="text-[11px] text-slate-400">
                      Pilih satu atau beberapa berkas gambar (JPG, PNG, WEBP max 2MB) untuk diunggah langsung ke server.
                    </p>
                  </div>

                  <textarea
                    v-model="requestBodyText"
                    rows="8"
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-xs font-mono text-slate-200 outline-none focus:border-primary transition resize-y"
                    placeholder='{"key": "value"}'
                  ></textarea>
                </div>

                <!-- Tab: Headers -->
                <div v-else-if="activeReqTab === 'headers'" class="space-y-3">
                  <div class="flex items-center justify-between mb-1">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                      Request Headers
                    </span>
                    <button
                      @click="addHeader"
                      class="text-[11px] text-primary hover:underline flex items-center gap-1 cursor-pointer"
                    >
                      <PhPlus :size="12" /> Tambah Header
                    </button>
                  </div>

                  <div class="space-y-2">
                    <div
                      v-for="(h, idx) in headersList"
                      :key="idx"
                      class="flex items-center gap-2 text-xs"
                    >
                      <input
                        v-model="h.enabled"
                        type="checkbox"
                        class="rounded bg-slate-900 border-slate-700 text-primary focus:ring-0 cursor-pointer"
                      />
                      <input
                        v-model="h.key"
                        type="text"
                        placeholder="Header Key"
                        class="w-1/3 bg-slate-900 border border-slate-700/80 rounded-lg px-2.5 py-1.5 text-xs font-mono text-slate-200 outline-none focus:border-primary"
                      />
                      <input
                        v-model="h.value"
                        type="text"
                        placeholder="Header Value"
                        class="flex-1 bg-slate-900 border border-slate-700/80 rounded-lg px-2.5 py-1.5 text-xs font-mono text-slate-200 outline-none focus:border-primary"
                      />
                      <button
                        @click="removeHeader(idx)"
                        class="p-1.5 text-slate-500 hover:text-rose-400 transition cursor-pointer"
                      >
                        <PhTrash :size="14" />
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Tab: Documentation -->
                <div v-else-if="activeReqTab === 'docs'" class="space-y-3 text-xs text-slate-300">
                  <div class="p-3 bg-slate-900/60 rounded-xl border border-slate-800 space-y-2">
                    <div class="font-bold text-slate-200">Catatan & Ketentuan Endpoint:</div>
                    <p class="text-slate-400">{{ selectedEndpoint.docs?.notes || 'Tidak ada catatan khusus untuk endpoint ini.' }}</p>
                  </div>

                  <div v-if="selectedEndpoint.docs?.responseExample" class="p-3 bg-slate-900/60 rounded-xl border border-slate-800 space-y-1.5">
                    <div class="font-bold text-slate-200">Contoh Respons Sukses (Expected Response):</div>
                    <pre class="bg-slate-950 p-2.5 rounded-lg text-[11px] font-mono text-emerald-400 overflow-x-auto">{{ selectedEndpoint.docs.responseExample }}</pre>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Response Inspector Panel -->
          <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-4 sm:p-5 flex flex-col gap-3">
            <!-- Response Meta Bar -->
            <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-800">
              <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-slate-400">Response:</span>
                <span
                  v-if="responseStatus !== null"
                  :class="['px-2.5 py-0.5 rounded-full text-xs font-black tracking-wide', responseStatusBadgeClass(responseStatus)]"
                >
                  {{ responseStatus }} {{ responseStatusText }}
                </span>
                <span v-else class="text-xs text-slate-500 italic">Belum ada request yang dikirim</span>
              </div>

              <!-- Metrics -->
              <div v-if="responseStatus !== null" class="flex items-center gap-3 text-xs text-slate-400 font-mono">
                <span class="flex items-center gap-1">
                  <PhClock :size="13" class="text-slate-500" /> {{ responseTime }} ms
                </span>
                <span class="flex items-center gap-1">
                  <PhHardDrive :size="13" class="text-slate-500" /> {{ responseSize }}
                </span>
                <button
                  @click="copyResponseJson"
                  class="p-1 text-slate-400 hover:text-slate-200 transition"
                  title="Salin JSON Respons"
                >
                  <PhCopy :size="15" />
                </button>
              </div>
            </div>

            <!-- Response Tabs (Body, Headers) -->
            <div v-if="responseStatus !== null">
              <div class="flex items-center gap-4 text-xs font-semibold mb-2">
                <button
                  @click="activeResTab = 'body'"
                  :class="activeResTab === 'body' ? 'text-primary border-b-2 border-primary pb-1' : 'text-slate-400 hover:text-slate-200 pb-1'"
                >
                  Response Body
                </button>
                <button
                  @click="activeResTab = 'headers'"
                  :class="activeResTab === 'headers' ? 'text-primary border-b-2 border-primary pb-1' : 'text-slate-400 hover:text-slate-200 pb-1'"
                >
                  Headers ({{ Object.keys(responseHeaders).length }})
                </button>
              </div>

              <!-- Response Body Tab -->
              <div v-if="activeResTab === 'body'">
                <div v-if="responseErrorHelper" class="mb-3 p-3 bg-rose-500/10 border border-rose-500/30 rounded-xl text-xs text-rose-300">
                  <span class="font-bold">Informasi Status {{ responseStatus }}:</span> {{ responseErrorHelper }}
                </div>

                <div class="relative bg-slate-900 border border-slate-800 rounded-xl p-3 max-h-[380px] overflow-y-auto font-mono text-xs text-slate-200">
                  <pre class="whitespace-pre-wrap leading-relaxed">{{ responseBodyPretty }}</pre>
                </div>
              </div>

              <!-- Response Headers Tab -->
              <div v-else-if="activeResTab === 'headers'" class="bg-slate-900 border border-slate-800 rounded-xl p-3 max-h-[300px] overflow-y-auto font-mono text-xs space-y-1.5">
                <div v-for="(val, key) in responseHeaders" :key="key" class="flex gap-2">
                  <span class="text-slate-400 font-bold w-1/3 truncate">{{ key }}:</span>
                  <span class="text-slate-200 flex-1 break-all">{{ val }}</span>
                </div>
              </div>
            </div>

            <div v-else class="py-8 text-center text-slate-600 text-xs italic">
              Tekan tombol "Kirim Request" atau tekan <kbd class="px-1.5 py-0.5 bg-slate-900 border border-slate-700 rounded text-slate-400 font-mono">Ctrl + Enter</kbd> untuk mengeksekusi endpoint.
            </div>
          </div>

          <!-- Request History Log -->
          <div v-if="requestHistory.length > 0" class="bg-slate-950/70 border border-slate-800 rounded-2xl p-4 flex flex-col gap-2">
            <div class="flex items-center justify-between text-xs font-bold text-slate-400">
              <span>Riwayat Pengujian Sesi Ini ({{ requestHistory.length }})</span>
              <button @click="requestHistory = []" class="text-slate-500 hover:text-rose-400 text-[11px]">Bersihkan</button>
            </div>
            <div class="flex flex-col divide-y divide-slate-800/60 max-h-40 overflow-y-auto">
              <div
                v-for="(hist, idx) in requestHistory"
                :key="idx"
                @click="loadFromHistory(hist)"
                class="py-2 px-2 hover:bg-slate-900 rounded-lg flex items-center justify-between text-xs cursor-pointer transition font-mono"
              >
                <div class="flex items-center gap-2 truncate">
                  <span :class="['px-1.5 py-0.5 rounded text-[10px] font-bold uppercase', methodBadgeClass(hist.method)]">
                    {{ hist.method }}
                  </span>
                  <span class="text-slate-300 truncate">{{ hist.path }}</span>
                </div>
                <div class="flex items-center gap-3 text-slate-500 text-[11px] shrink-0">
                  <span :class="responseStatusBadgeTextClass(hist.status)">{{ hist.status }}</span>
                  <span>{{ hist.time }}ms</span>
                  <span>{{ hist.timestamp }}</span>
                </div>
              </div>
            </div>
          </div>
        </template>
      </main>
    </div>

    <!-- Custom Token Modal -->
    <div
      v-if="showTokenModal"
      class="fixed inset-0 bg-slate-950/80 backdrop-blur-xs flex items-center justify-center z-50 p-4"
    >
      <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-lg p-5 space-y-4 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
          <h3 class="text-sm font-bold text-slate-100 flex items-center gap-2">
            <PhKey :size="18" class="text-primary" /> Pengaturan Bearer Token Kustom
          </h3>
          <button @click="showTokenModal = false" class="text-slate-400 hover:text-white">
            <PhX :size="18" />
          </button>
        </div>

        <div class="space-y-2 text-xs">
          <label class="block text-slate-300 font-semibold">
            Sanctum Bearer Token:
          </label>
          <textarea
            v-model="customTokenInput"
            rows="4"
            placeholder="Tempelkan token Sanctum di sini (contoh: 1|xYzAbC...)"
            class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-xs font-mono text-slate-200 outline-none focus:border-primary"
          ></textarea>
          <p class="text-[11px] text-slate-500">
            Token ini akan disuntikkan secara otomatis ke header <code class="text-primary font-mono">Authorization: Bearer &lt;token&gt;</code> untuk setiap panggilan API.
          </p>
        </div>

        <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-800">
          <button
            @click="showTokenModal = false"
            class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold cursor-pointer"
          >
            Batal
          </button>
          <button
            @click="applyCustomToken"
            class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-xl text-xs font-bold cursor-pointer"
          >
            Terapkan Token
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import { apiCategories, apiEndpoints } from '../data/apiEndpoints'
import { useAuthStore } from '../stores/auth'
import { useUiStore } from '../stores/ui'
import {
  PhMagnifyingGlass,
  PhGlobe,
  PhUser,
  PhShieldCheck,
  PhCheckCircle,
  PhLock,
  PhKey,
  PhPlay,
  PhTerminal,
  PhPlus,
  PhTrash,
  PhCopy,
  PhClock,
  PhHardDrive,
  PhCode,
  PhX,
  PhCircleNotch,
  PhArrowClockwise
} from '@phosphor-icons/vue'

const auth = useAuthStore()
const ui = useUiStore()

// State: Environment & Base URL
const defaultBaseUrl = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000/api/v1'
const baseUrl = ref(defaultBaseUrl)

const resetBaseUrl = () => {
  baseUrl.value = defaultBaseUrl
}

// State: Auth Token
const authToken = ref(localStorage.getItem('token') || '')
const currentRoleDisplay = computed(() => {
  if (auth.user?.role) return `${auth.user.nama || 'User'} (${auth.user.role})`
  if (authToken.value) return 'Authenticated Token'
  return 'Guest'
})

const showTokenModal = ref(false)
const customTokenInput = ref('')
const isLoggingIn = ref(false)

const applyCustomToken = () => {
  const token = customTokenInput.value.trim()
  authToken.value = token
  if (token) {
    localStorage.setItem('token', token)
    ui.success('Token kustom berhasil diterapkan.')
  } else {
    localStorage.removeItem('token')
    ui.info('Token kustom dihapus.')
  }
  showTokenModal.value = false
  syncHeadersWithAuth()
}

const clearAuthToken = () => {
  authToken.value = ''
  localStorage.removeItem('token')
  auth.token = null
  auth.user = null
  syncHeadersWithAuth()
  ui.info('Token autentikasi telah dibersihkan.')
}

const quickLogin = async (email, password) => {
  isLoggingIn.value = true
  try {
    const res = await axios.post(`${baseUrl.value}/auth/login`, { email, password }, {
      headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' }
    })
    if (res.data?.token) {
      authToken.value = res.data.token
      localStorage.setItem('token', res.data.token)
      if (res.data.user) {
        localStorage.setItem('user', JSON.stringify(res.data.user))
        auth.user = res.data.user
      }
      auth.token = res.data.token
      syncHeadersWithAuth()
      ui.success(`Berhasil login sebagai ${res.data.user?.nama || email}`)
    }
  } catch (err) {
    console.error('Quick login error:', err)
    ui.error(err.response?.data?.message || 'Login gagal. Pastikan server backend sedang berjalan.')
  } finally {
    isLoggingIn.value = false
  }
}

// State: Filter Directory
const searchQuery = ref('')
const selectedMethodFilter = ref('ALL')
const selectedCategory = ref('Semua')

const filteredEndpoints = computed(() => {
  return apiEndpoints.filter((ep) => {
    // Filter Method
    if (selectedMethodFilter.value !== 'ALL' && ep.method !== selectedMethodFilter.value) {
      return false
    }
    // Filter Category
    if (selectedCategory.value !== 'Semua' && ep.category !== selectedCategory.value) {
      return false
    }
    // Filter Search Keyword
    if (searchQuery.value.trim()) {
      const q = searchQuery.value.toLowerCase().trim()
      const matchName = ep.name.toLowerCase().includes(q)
      const matchPath = ep.path.toLowerCase().includes(q)
      const matchDesc = ep.description.toLowerCase().includes(q)
      const matchCategory = ep.category.toLowerCase().includes(q)
      if (!matchName && !matchPath && !matchDesc && !matchCategory) return false
    }
    return true
  })
})

// State: Selected Endpoint & Playground
const selectedEndpoint = ref(apiEndpoints[0] || null)
const currentMethod = ref('POST')
const pathParamsList = ref([])
const queryParamsList = ref([])
const headersList = ref([])
const requestBodyText = ref('')
const selectedFiles = ref([])
const fileInputRef = ref(null)

const activeReqTab = ref('params')
const activeResTab = ref('body')

// Computed Dynamic Path with replaced {params}
const computedPath = computed({
  get() {
    if (!selectedEndpoint.value) return ''
    let p = selectedEndpoint.value.path
    pathParamsList.value.forEach((param) => {
      if (param.key && param.value !== undefined) {
        p = p.replace(`{${param.key}}`, param.value || `{${param.key}}`)
      }
    })
    return p
  },
  set(val) {
    // allow manual editing if needed
  }
})

// Sync Headers with current Auth token
const syncHeadersWithAuth = () => {
  const existingAuth = headersList.value.find((h) => h.key.toLowerCase() === 'authorization')
  if (authToken.value) {
    if (existingAuth) {
      existingAuth.value = `Bearer ${authToken.value}`
      existingAuth.enabled = true
    } else {
      headersList.value.push({ key: 'Authorization', value: `Bearer ${authToken.value}`, enabled: true })
    }
  } else {
    if (existingAuth) {
      headersList.value = headersList.value.filter((h) => h.key.toLowerCase() !== 'authorization')
    }
  }
}

// Select Endpoint Handler
const selectEndpoint = (ep) => {
  selectedEndpoint.value = ep
  currentMethod.value = ep.method
  pathParamsList.value = (ep.pathParams || []).map((p) => ({ ...p }))
  queryParamsList.value = (ep.queryParams || []).map((q) => ({ ...q }))
  headersList.value = (ep.headers || []).map((h) => ({ ...h }))
  requestBodyText.value = ep.defaultBody || ''
  selectedFiles.value = []

  // Ensure default headers
  if (!headersList.value.some((h) => h.key.toLowerCase() === 'accept')) {
    headersList.value.unshift({ key: 'Accept', value: 'application/json', enabled: true })
  }
  if (['POST', 'PUT', 'PATCH'].includes(ep.method) && ep.bodyType === 'json') {
    if (!headersList.value.some((h) => h.key.toLowerCase() === 'content-type')) {
      headersList.value.push({ key: 'Content-Type', value: 'application/json', enabled: true })
    }
  }

  syncHeadersWithAuth()

  // Default tab
  if (['POST', 'PUT', 'PATCH'].includes(ep.method)) {
    activeReqTab.value = 'body'
  } else {
    activeReqTab.value = 'params'
  }
}

const addQueryParam = () => {
  queryParamsList.value.push({ key: '', value: '', enabled: true })
}

const removeQueryParam = (idx) => {
  queryParamsList.value.splice(idx, 1)
}

const addHeader = () => {
  headersList.value.push({ key: '', value: '', enabled: true })
}

const removeHeader = (idx) => {
  headersList.value.splice(idx, 1)
}

const formatJsonBody = () => {
  try {
    if (!requestBodyText.value.trim()) return
    const parsed = JSON.parse(requestBodyText.value)
    requestBodyText.value = JSON.stringify(parsed, null, 2)
    ui.success('JSON berhasil dirapikan.')
  } catch (err) {
    ui.error('JSON tidak valid: ' + err.message)
  }
}

const handleFileSelected = (e) => {
  selectedFiles.value = Array.from(e.target.files)
}

// Request Execution State
const isLoading = ref(false)
const responseStatus = ref(null)
const responseStatusText = ref('')
const responseTime = ref(0)
const responseSize = ref('0 B')
const responseHeaders = ref({})
const responseBodyPretty = ref('')
const responseErrorHelper = ref('')
const requestHistory = ref([])

// Execute Request
const sendRequest = async () => {
  if (!selectedEndpoint.value || isLoading.value) return
  isLoading.value = true
  responseStatus.value = null
  responseBodyPretty.value = 'Memuat respons dari server...'
  responseErrorHelper.value = ''

  // Build Query String
  const activeParams = queryParamsList.value.filter((q) => q.enabled && q.key.trim() !== '')
  const searchParams = new URLSearchParams()
  activeParams.forEach((q) => searchParams.append(q.key.trim(), q.value))
  const queryString = searchParams.toString() ? `?${searchParams.toString()}` : ''

  const finalUrl = `${baseUrl.value.replace(/\/$/, '')}${computedPath.value}${queryString}`

  // Build Headers Object
  const reqHeaders = {}
  headersList.value.filter((h) => h.enabled && h.key.trim() !== '').forEach((h) => {
    reqHeaders[h.key.trim()] = h.value
  })

  // Build Payload
  let reqData = undefined
  if (['POST', 'PUT', 'PATCH'].includes(currentMethod.value)) {
    if (selectedEndpoint.value.bodyType === 'form-data' && selectedFiles.value.length > 0) {
      const formData = new FormData()
      if (selectedEndpoint.value.id === 'uploads-images') {
        selectedFiles.value.forEach((file) => {
          formData.append('images[]', file)
        })
      } else if (selectedEndpoint.value.id === 'auth-update-avatar') {
        formData.append('avatar', selectedFiles.value[0])
      } else {
        formData.append('file', selectedFiles.value[0])
      }
      reqData = formData
      delete reqHeaders['Content-Type']
    } else if (requestBodyText.value.trim()) {
      try {
        reqData = JSON.parse(requestBodyText.value)
      } catch (e) {
        reqData = requestBodyText.value
      }
    }
  }

  const startTime = performance.now()
  try {
    const res = await axios({
      method: currentMethod.value.toLowerCase(),
      url: finalUrl,
      headers: reqHeaders,
      data: reqData,
      responseType: selectedEndpoint.value.id === 'bookings-export' ? 'blob' : 'json',
      validateStatus: () => true // handle all status codes manually
    })

    const duration = Math.round(performance.now() - startTime)
    responseTime.value = duration
    responseStatus.value = res.status
    responseStatusText.value = res.statusText || getStatusText(res.status)
    responseHeaders.value = res.headers || {}

    // Calculate Size
    const rawDataStr = typeof res.data === 'object' ? JSON.stringify(res.data) : String(res.data)
    responseSize.value = formatBytes(new Blob([rawDataStr]).size)

    if (res.status >= 200 && res.status < 300) {
      if (selectedEndpoint.value.id === 'bookings-export') {
        responseBodyPretty.value = '[File CSV Stream diterima berhasil]'
        // Trigger auto-download
        const blob = new Blob([res.data], { type: 'text/csv' })
        const downloadUrl = window.URL.createObjectURL(blob)
        const a = document.createElement('a')
        a.href = downloadUrl
        a.download = `rekap-pemesanan-${new Date().toISOString().slice(0, 10)}.csv`
        a.click()
        window.URL.revokeObjectURL(downloadUrl)
      } else {
        responseBodyPretty.value = JSON.stringify(res.data, null, 2)
      }
    } else {
      responseBodyPretty.value = typeof res.data === 'object' ? JSON.stringify(res.data, null, 2) : String(res.data)
      setResponseErrorHelper(res.status)
    }

    // Add to History
    requestHistory.value.unshift({
      method: currentMethod.value,
      path: computedPath.value,
      url: finalUrl,
      status: res.status,
      time: duration,
      timestamp: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }),
      endpoint: selectedEndpoint.value
    })
    if (requestHistory.value.length > 20) requestHistory.value.pop()

  } catch (err) {
    const duration = Math.round(performance.now() - startTime)
    responseTime.value = duration
    responseStatus.value = err.response?.status || 0
    responseStatusText.value = 'Network Error / Blocked'
    responseBodyPretty.value = JSON.stringify({
      error: 'Network Error',
      message: err.message,
      detail: 'Pastikan server backend Laravel berjalan di port yang sesuai (mis. http://127.0.0.1:8000) dan tidak diblokir CORS.'
    }, null, 2)
    responseErrorHelper.value = 'Gagal terhubung ke host API. Silakan periksa URL base dan konektivitas server backend.'
  } finally {
    isLoading.value = false
  }
}

// Helpers
const getStatusText = (status) => {
  const map = {
    200: 'OK',
    201: 'Created',
    204: 'No Content',
    400: 'Bad Request',
    401: 'Unauthorized',
    403: 'Forbidden',
    404: 'Not Found',
    422: 'Unprocessable Content',
    500: 'Internal Server Error',
  }
  return map[status] || 'Response'
}

const setResponseErrorHelper = (status) => {
  if (status === 401) {
    responseErrorHelper.value = 'Token tidak valid atau belum diautentikasi. Silakan lakukan Login terlebih dahulu menggunakan tombol Quick Login di atas.'
  } else if (status === 403) {
    responseErrorHelper.value = 'Akses ditolak. Akun Anda tidak memiliki hak akses (role) atau status verifikasi yang dipersyaratkan untuk rute ini.'
  } else if (status === 404) {
    responseErrorHelper.value = 'Data ID atau resource tidak ditemukan di server basis data.'
  } else if (status === 422) {
    responseErrorHelper.value = 'Terdapat validasi input yang belum sesuai. Periksa field errors pada JSON di atas.'
  }
}

const formatBytes = (bytes) => {
  if (!bytes || bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i]
}

const methodBadgeClass = (method) => {
  switch (method?.toUpperCase()) {
    case 'GET': return 'bg-sky-500/20 text-sky-400 border border-sky-500/30'
    case 'POST': return 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30'
    case 'PUT': return 'bg-amber-500/20 text-amber-400 border border-amber-500/30'
    case 'PATCH': return 'bg-purple-500/20 text-purple-400 border border-purple-500/30'
    case 'DELETE': return 'bg-rose-500/20 text-rose-400 border border-rose-500/30'
    default: return 'bg-slate-700 text-slate-300'
  }
}

const responseStatusBadgeClass = (status) => {
  if (status >= 200 && status < 300) return 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30'
  if (status >= 300 && status < 400) return 'bg-sky-500/20 text-sky-400 border border-sky-500/30'
  if (status >= 400 && status < 500) return 'bg-amber-500/20 text-amber-400 border border-amber-500/30'
  return 'bg-rose-500/20 text-rose-400 border border-rose-500/30'
}

const responseStatusBadgeTextClass = (status) => {
  if (status >= 200 && status < 300) return 'text-emerald-400 font-bold'
  if (status >= 300 && status < 400) return 'text-sky-400 font-bold'
  if (status >= 400 && status < 500) return 'text-amber-400 font-bold'
  return 'text-rose-400 font-bold'
}

const copyCurlCommand = () => {
  const activeParams = queryParamsList.value.filter((q) => q.enabled && q.key.trim() !== '')
  const searchParams = new URLSearchParams()
  activeParams.forEach((q) => searchParams.append(q.key.trim(), q.value))
  const queryString = searchParams.toString() ? `?${searchParams.toString()}` : ''

  const finalUrl = `${baseUrl.value.replace(/\/$/, '')}${computedPath.value}${queryString}`

  let curl = `curl -X ${currentMethod.value} "${finalUrl}"`
  headersList.value.filter((h) => h.enabled && h.key.trim() !== '').forEach((h) => {
    curl += ` \\\n  -H "${h.key.trim()}: ${h.value}"`
  })

  if (['POST', 'PUT', 'PATCH'].includes(currentMethod.value) && requestBodyText.value.trim()) {
    curl += ` \\\n  -d '${requestBodyText.value.replace(/'/g, `'\\''`)}'`
  }

  navigator.clipboard.writeText(curl)
  ui.success('Perintah cURL berhasil disalin ke clipboard.')
}

const copyResponseJson = () => {
  if (!responseBodyPretty.value) return
  navigator.clipboard.writeText(responseBodyPretty.value)
  ui.success('Respons JSON disalin.')
}

const loadFromHistory = (hist) => {
  if (hist.endpoint) {
    selectEndpoint(hist.endpoint)
  }
}

// Keyboard Shortcut: Ctrl + Enter to Send Request
const handleKeydown = (e) => {
  if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
    e.preventDefault()
    sendRequest()
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeydown)
  if (apiEndpoints.length > 0) {
    selectEndpoint(apiEndpoints[0])
  }
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown)
})
</script>
