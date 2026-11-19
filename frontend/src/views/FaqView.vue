<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 transition-colors duration-200 py-6 sm:py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
      <!-- Breadcrumb & Back Navigation -->
      <div class="mb-6 flex items-center justify-between">
        <router-link
          to="/"
          class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-primary hover:text-primary-focus transition cursor-pointer"
        >
          <PhArrowLeft :size="16" weight="bold" />
          <span>Kembali ke Beranda</span>
        </router-link>

        <span class="text-xs text-slate-400 dark:text-slate-500">
          Pusat Bantuan &bull; v2.0
        </span>
      </div>

      <!-- Hero Header & Search -->
      <div class="text-center mb-8 sm:mb-12">
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary/10 text-primary dark:bg-primary/20 dark:text-primary-container text-xs font-bold mb-3">
          <PhSparkle :size="14" weight="fill" />
          <span>Pusat Bantuan & Tanya Jawab</span>
        </div>
        <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-3">
          Ada yang bisa kami bantu?
        </h1>
        <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 max-w-xl mx-auto mb-6">
          Temukan jawaban instan seputar pemesanan kos, pembayaran DP 20%, pelunasan, kemitraan pemilik, hingga keamanan akun.
        </p>

        <!-- Search Input -->
        <div class="max-w-xl mx-auto relative">
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
            <PhMagnifyingGlass :size="20" weight="bold" />
          </div>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari pertanyaan... (contoh: DP, pelunasan, verifikasi, dompet)"
            class="w-full pl-11 pr-10 py-3.5 text-xs sm:text-sm bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xs focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
          />
          <button
            v-if="searchQuery"
            @click="searchQuery = ''"
            type="button"
            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer"
            title="Hapus pencarian"
          >
            <PhXCircle :size="18" weight="fill" />
          </button>
        </div>
      </div>

      <!-- Category Filter Pills -->
      <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-6 sm:mb-8 scrollbar-none no-scrollbar">
        <button
          v-for="cat in categories"
          :key="cat.id"
          @click="selectedCategory = cat.id"
          type="button"
          :class="[
            'px-4 py-2 rounded-xl text-xs sm:text-sm font-semibold transition shrink-0 cursor-pointer flex items-center gap-2 border',
            selectedCategory === cat.id
              ? 'bg-primary text-white border-primary shadow-xs'
              : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:bg-slate-100 dark:hover:bg-slate-800/80'
          ]"
        >
          <component :is="cat.icon" :size="16" weight="bold" />
          <span>{{ cat.label }}</span>
          <span
            :class="[
              'text-[10px] px-1.5 py-0.5 rounded-full font-bold',
              selectedCategory === cat.id ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500'
            ]"
          >
            {{ getCategoryCount(cat.id) }}
          </span>
        </button>
      </div>

      <!-- FAQ Accordion List -->
      <div v-if="filteredFaqs.length > 0" class="space-y-3.5 mb-10">
        <div
          v-for="(item, idx) in filteredFaqs"
          :key="item.id"
          class="bg-white dark:bg-slate-900 border border-slate-200/90 dark:border-slate-800 rounded-2xl overflow-hidden shadow-2xs transition hover:border-slate-300 dark:hover:border-slate-700"
        >
          <!-- Accordion Header -->
          <button
            @click="toggleFaq(item.id)"
            type="button"
            class="w-full text-left px-5 py-4 flex items-center justify-between gap-4 cursor-pointer focus:outline-none"
            :aria-expanded="openFaqIds.includes(item.id)"
          >
            <div class="flex items-start gap-3 min-w-0">
              <span class="mt-0.5 p-1 rounded-lg bg-primary/10 text-primary dark:bg-primary/20 shrink-0">
                <PhQuestion :size="16" weight="bold" />
              </span>
              <div>
                <span class="text-xs sm:text-sm font-bold text-slate-900 dark:text-slate-100 block">
                  {{ item.question }}
                </span>
                <span class="inline-block mt-1 text-[10px] font-semibold uppercase tracking-wider text-primary/90 dark:text-primary-container">
                  {{ getCategoryLabel(item.category) }}
                </span>
              </div>
            </div>
            <div
              :class="[
                'p-1.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-500 transition-transform duration-200 shrink-0',
                openFaqIds.includes(item.id) ? 'rotate-180 bg-primary/10 text-primary dark:bg-primary/20' : ''
              ]"
            >
              <PhCaretDown :size="16" weight="bold" />
            </div>
          </button>

          <!-- Accordion Body -->
          <div
            v-show="openFaqIds.includes(item.id)"
            class="px-5 pb-5 pt-1 text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed border-t border-slate-100 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-900/50"
          >
            <div class="prose dark:prose-invert max-w-none text-xs sm:text-sm" v-html="item.answerHtml"></div>

            <!-- Helpful feedback widget -->
            <div class="mt-4 pt-3 border-t border-slate-200/60 dark:border-slate-800 flex items-center justify-between text-[11px] text-slate-400">
              <span>Apakah jawaban ini membantu?</span>
              <div class="flex items-center gap-2">
                <button
                  @click="voteHelpful(item.id, true)"
                  type="button"
                  :disabled="item.voted"
                  :class="[
                    'px-2.5 py-1 rounded-lg flex items-center gap-1.5 transition cursor-pointer',
                    item.voted === 'yes'
                      ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold'
                      : 'hover:bg-slate-200/60 dark:hover:bg-slate-800 text-slate-500'
                  ]"
                >
                  <PhThumbsUp :size="14" :weight="item.voted === 'yes' ? 'fill' : 'regular'" />
                  <span>Ya</span>
                </button>
                <button
                  @click="voteHelpful(item.id, false)"
                  type="button"
                  :disabled="item.voted"
                  :class="[
                    'px-2.5 py-1 rounded-lg flex items-center gap-1.5 transition cursor-pointer',
                    item.voted === 'no'
                      ? 'bg-rose-500/10 text-rose-600 dark:text-rose-400 font-bold'
                      : 'hover:bg-slate-200/60 dark:hover:bg-slate-800 text-slate-500'
                  ]"
                >
                  <PhThumbsDown :size="14" :weight="item.voted === 'no' ? 'fill' : 'regular'" />
                  <span>Tidak</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State when search returns nothing -->
      <div
        v-else
        class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-8 sm:p-12 text-center mb-10 shadow-2xs"
      >
        <div class="w-14 h-14 mx-auto rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mb-3">
          <PhMagnifyingGlass :size="26" weight="bold" />
        </div>
        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">
          Tidak ada pertanyaan yang sesuai
        </h3>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto mb-4">
          Kami tidak dapat menemukan jawaban untuk kata kunci "{{ searchQuery }}". Anda dapat bertanya langsung kepada tim dukungan kami.
        </p>
        <button
          @click="searchQuery = ''; selectedCategory = 'semua'"
          type="button"
          class="px-4 py-2 text-xs font-semibold rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 cursor-pointer transition"
        >
          Reset Pencarian
        </button>
      </div>

      <!-- Contact Support Help Card -->
      <div class="bg-gradient-to-br from-primary/10 via-primary-fixed/30 to-amber-500/10 dark:from-slate-900 dark:via-primary/10 dark:to-slate-900 border border-primary/20 dark:border-primary/30 rounded-2xl p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-6 shadow-sm">
        <div class="text-center sm:text-left">
          <span class="inline-flex items-center gap-1 text-[11px] font-bold uppercase tracking-wider text-primary mb-1">
            <PhHeadset :size="14" weight="bold" />
            Layanan Pelanggan 24/7
          </span>
          <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white">
            Masih butuh bantuan atau ada kendala?
          </h3>
          <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-400 mt-1 max-w-md">
            Hubungi tim Customer Support Kostara langsung via WhatsApp. Kami siap mendampingi kebutuhan sewa dan transaksi Anda.
          </p>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 shrink-0 w-full sm:w-auto">
          <a
            :href="whatsappUrl"
            target="_blank"
            rel="noopener noreferrer"
            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-md hover:shadow-lg transition cursor-pointer"
          >
            <PhWhatsappLogo :size="18" weight="bold" />
            <span>Chat WhatsApp</span>
          </a>

          <a
            href="mailto:support@kostara.id"
            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-100 border border-slate-200 dark:border-slate-700 font-semibold text-xs sm:text-sm shadow-2xs transition cursor-pointer"
          >
            <PhEnvelopeSimple :size="18" weight="bold" />
            <span>Kirim Email</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import {
  PhArrowLeft,
  PhSparkle,
  PhMagnifyingGlass,
  PhXCircle,
  PhQuestion,
  PhCaretDown,
  PhThumbsUp,
  PhThumbsDown,
  PhWhatsappLogo,
  PhEnvelopeSimple,
  PhHeadset,
  PhSquaresFour,
  PhCreditCard,
  PhKey,
  PhBuildings,
  PhShieldCheck
} from '@phosphor-icons/vue'

const searchQuery = ref('')
const selectedCategory = ref('semua')
const openFaqIds = ref(['faq-1']) // default expand first item

const categories = [
  { id: 'semua', label: 'Semua', icon: PhSquaresFour },
  { id: 'sewa', label: 'Sewa & DP 20%', icon: PhCreditCard },
  { id: 'pelunasan', label: 'Pelunasan & Serah Kunci', icon: PhKey },
  { id: 'pemilik', label: 'Mitra Pemilik & Dompet', icon: PhBuildings },
  { id: 'keamanan', label: 'Akun & Keamanan', icon: PhShieldCheck }
]

const faqs = ref([
  // Kategori: Sewa & DP 20%
  {
    id: 'faq-1',
    category: 'sewa',
    question: 'Bagaimana alur penyewaan kos di platform Kostara?',
    answerHtml: `
      <ol class="list-decimal pl-5 space-y-1.5">
        <li><strong>Cari Kos:</strong> Telusuri kamar kos sesuai kampus, kota, fasilitas, atau budget Anda melalui peta atau daftar katalog.</li>
        <li><strong>Ajukan Sewa:</strong> Tentukan tanggal mulai sewa serta durasi sewa yang Anda inginkan (bulanan, 3 bulan, atau tahunan).</li>
        <li><strong>Bayar Uang Muka (DP 20%):</strong> Lakukan pembayaran DP 20% melalui metode pembayaran Midtrans (QRIS atau Virtual Account) untuk mengunci kamar tersebut secara resmi.</li>
        <li><strong>Konfirmasi Pemilik:</strong> Pemilik kos akan memeriksa dan menyetujui pengajuan sewa Anda.</li>
      </ol>
    `,
    voted: null
  },
  {
    id: 'faq-2',
    category: 'sewa',
    question: 'Berapa besaran DP (Uang Muka) yang harus saya bayarkan?',
    answerHtml: `
      <p>Besaran uang muka (DP) di Kostara ditetapkan sebesar <strong>minimal 20% dari total nilai sewa periode pertama</strong>. Pembayaran DP ini bertujuan mengikat kamar secara instan agar pemilik tidak menyewakannya kepada calon penyewa lain.</p>
      <p class="mt-2 text-slate-500 dark:text-slate-400">Sisa tagihan sewa sebesar 80% dapat Anda lunasi sebelum tanggal check-in atau saat proses serah terima kunci di lokasi kos.</p>
    `,
    voted: null
  },
  {
    id: 'faq-3',
    category: 'sewa',
    question: 'Metode pembayaran apa saja yang didukung oleh Kostara?',
    answerHtml: `
      <p>Kostara bekerja sama dengan <strong>Midtrans Payment Gateway</strong> resmi untuk menyediakan pembayaran otomatis, aman, dan tanpa biaya tersembunyi:</p>
      <ul class="list-disc pl-5 mt-2 space-y-1">
        <li><strong>QRIS Instan:</strong> GoPay, OVO, Dana, LinkAja, ShopeePay, serta aplikasi Mobile Banking mana pun.</li>
        <li><strong>Virtual Account (VA):</strong> Bank BCA, Mandiri, BNI, BRI, dan Permata (verifikasi otomatis real-time 24 jam).</li>
      </ul>
    `,
    voted: null
  },

  // Kategori: Pelunasan & Serah Kunci
  {
    id: 'faq-4',
    category: 'pelunasan',
    question: 'Kapan dan bagaimana cara melunasi sisa tagihan sewa (80%)?',
    answerHtml: `
      <p>Sisa tagihan 80% dapat dilunasi melalui dashboard <strong>Riwayat Pesanan</strong> akun Anda:</p>
      <ol class="list-decimal pl-5 mt-2 space-y-1">
        <li>Masuk ke menu <strong>Pesanan Saya</strong>.</li>
        <li>Pilih pesanan kos Anda yang berstatus <em>Telah Bayar DP</em>.</li>
        <li>Klik tombol <strong>Pelunasan Sewa</strong> untuk membayar sisa 80% via Midtrans (QRIS/VA).</li>
        <li>Pelunasan harus diselesaikan paling lambat saat jadwal serah kunci kamar di lokasi bersama Mitra Pemilik.</li>
      </ol>
    `,
    voted: null
  },
  {
    id: 'faq-5',
    category: 'pelunasan',
    question: 'Bagaimana prosedur serah kunci kamar kos dan konfirmasi check-in?',
    answerHtml: `
      <p>Setelah pelunasan terverifikasi otomatis oleh sistem:</p>
      <ul class="list-disc pl-5 mt-2 space-y-1">
        <li>Anda dan pemilik kos bertemu di lokasi kos pada tanggal check-in yang dijadwalkan.</li>
        <li>Pemilik kos melakukan serah terima fisik kunci kamar dan mengecek fasilitas bersama Anda.</li>
        <li>Pemilik akan mengonfirmasi tombol <strong>Konfirmasi Serah Kunci & Check-in</strong> di panel dashboard pemiliknya.</li>
        <li>Status pesanan Anda akan berubah menjadi <strong>Aktif / Sedang Dihuni</strong>.</li>
      </ul>
    `,
    voted: null
  },
  {
    id: 'faq-6',
    category: 'pelunasan',
    question: 'Apakah uang DP bisa dikembalikan (refund) jika saya membatalkan pesanan?',
    answerHtml: `
      <p>Kebijakan pengembalian dana DP diatur dengan adil untuk melindungi pencari kos dan pemilik:</p>
      <ul class="list-disc pl-5 mt-2 space-y-1">
        <li>Jika pengajuan sewa <strong>ditolak oleh pemilik</strong> atau pemilik tidak merespons hingga batas waktu kadaluarsa, DP akan <strong>dikembalikan 100%</strong> ke rekening penyewa.</li>
        <li>Jika penyewa membatalkan sepihak setelah disetujui, pengembalian dana tunduk pada syarat & ketentuan pembatalan serta kesepakatan tertulis pemilik kos.</li>
      </ul>
    `,
    voted: null
  },

  // Kategori: Mitra Pemilik & Dompet
  {
    id: 'faq-7',
    category: 'pemilik',
    question: 'Bagaimana cara mendaftar dan memverifikasi properti sebagai Mitra Pemilik Kos?',
    answerHtml: `
      <p>Langkah bergabung menjadi Mitra Pemilik Kostara:</p>
      <ol class="list-decimal pl-5 mt-2 space-y-1">
        <li>Daftar akun di Kostara dan pilih peran sebagai <strong>Pemilik Kos</strong>.</li>
        <li>Buka menu <strong>Verifikasi Kepemilikan Properti</strong> di dashboard Anda.</li>
        <li>Unggah foto KTP pemilik dan dokumen bukti kepemilikan sah (PBB, Sertifikat Properti, atau Surat Kuasa).</li>
        <li>Tim verifikasi Kostara akan meninjau dan menyetujui akun Anda dalam <strong>1x24 jam kerja</strong>.</li>
        <li>Setelah disetujui, Anda dapat langsung menambahkan unit kos, mengelola kamar, dan menerima pesanan sewa.</li>
      </ol>
    `,
    voted: null
  },
  {
    id: 'faq-8',
    category: 'pemilik',
    question: 'Kapan dana hasil sewa masuk ke Saldo Dompet Pemilik?',
    answerHtml: `
      <p>Untuk keamanan bersama, dana pembayaran penyewa (DP & Pelunasan) disimpan aman dalam sistem Kostara, dan <strong>otomatis diteruskan ke Saldo Dompet Pemilik</strong> begitu penyewa resmi melakukan serah kunci (check-in).</p>
      <p class="mt-2 text-slate-500 dark:text-slate-400">Hal ini menjamin penyewa mendapatkan kamar yang sesuai dan pemilik menerima pembayaran utuh tanpa potongan sepihak.</p>
    `,
    voted: null
  },
  {
    id: 'faq-9',
    category: 'pemilik',
    question: 'Bagaimana cara melakukan penarikan dana (Withdrawal) ke rekening bank pemilik?',
    answerHtml: `
      <p>Pencairan saldo dompet mitra sangat cepat dan transparan:</p>
      <ol class="list-decimal pl-5 mt-2 space-y-1">
        <li>Buka menu <strong>Dompet Saya</strong> di Dashboard Pemilik.</li>
        <li>Klik tombol <strong>Tarik Saldo</strong>.</li>
        <li>Pilih rekening bank tujuan yang telah didaftarkan dan masukkan nominal penarikan (minimal Rp 50.000).</li>
        <li>Admin keuangan kami akan memverifikasi dan mentransfer dana ke rekening bank Anda dalam 1-2 jam kerja.</li>
      </ol>
    `,
    voted: null
  },

  // Kategori: Akun & Keamanan
  {
    id: 'faq-10',
    category: 'keamanan',
    question: 'Apakah data identitas KTP dan dokumen yang saya unggah aman?',
    answerHtml: `
      <p><strong>Ya, 100% aman dan terlindungi.</strong> Kostara menerapkan enkripsi tingkat tinggi (SSL 256-bit) dan standar perlindungan data pribadi. Dokumen identitas KTP Anda hanya disimpan di server khusus terisolasi dan hanya dapat diakses oleh auditor verifikasi resmi kami untuk validasi kepemilikan dan pencegahan penipuan sewa kos.</p>
    `,
    voted: null
  },
  {
    id: 'faq-11',
    category: 'keamanan',
    question: 'Apa yang harus dilakukan jika saya lupa kata sandi akun?',
    answerHtml: `
      <p>Jangan khawatir! Klik tautan <strong>Lupa Kata Sandi</strong> pada halaman Login, masukkan alamat email akun Anda, dan sistem akan secara otomatis mengirimkan tautan pemulihan kata sandi ke kotak masuk email Anda dalam hitungan detik.</p>
    `,
    voted: null
  },
  {
    id: 'faq-12',
    category: 'keamanan',
    question: 'Bagaimana jika terjadi perselisihan atau ketidaksesuaian fasilitas kos?',
    answerHtml: `
      <p>Kostara menyediakan tim Customer Support & Penengah yang dapat Anda hubungi via WhatsApp Layanan Bantuan kapan pun. Kami akan membantu memediasi antara penyewa dan pemilik, memeriksa bukti foto/video, serta memberlakukan garansi sewa jika properti tidak sesuai dengan informasi yang tertera di aplikasi.</p>
    `,
    voted: null
  }
])

const toggleFaq = (id) => {
  const index = openFaqIds.value.indexOf(id)
  if (index > -1) {
    openFaqIds.value.splice(index, 1)
  } else {
    openFaqIds.value.push(id)
  }
}

const getCategoryCount = (categoryId) => {
  if (categoryId === 'semua') return faqs.value.length
  return faqs.value.filter((item) => item.category === categoryId).length
}

const getCategoryLabel = (categoryId) => {
  const cat = categories.find((c) => c.id === categoryId)
  return cat ? cat.label : categoryId
}

const filteredFaqs = computed(() => {
  let list = faqs.value

  if (selectedCategory.value !== 'semua') {
    list = list.filter((item) => item.category === selectedCategory.value)
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase().trim()
    list = list.filter((item) => {
      const inQuestion = item.question.toLowerCase().includes(q)
      const inAnswer = item.answerHtml.toLowerCase().includes(q)
      const inCategory = getCategoryLabel(item.category).toLowerCase().includes(q)
      return inQuestion || inAnswer || inCategory
    })
  }

  return list
})

const voteHelpful = (id, isHelpful) => {
  const item = faqs.value.find((f) => f.id === id)
  if (item) {
    item.voted = isHelpful ? 'yes' : 'no'
  }
}

const whatsappUrl = computed(() => {
  const message = encodeURIComponent('Halo Tim Support Kostara, saya memerlukan bantuan terkait layanan sewa/kemitraan kos di Kostara.')
  return `https://wa.me/6281234567890?text=${message}`
})
</script>
