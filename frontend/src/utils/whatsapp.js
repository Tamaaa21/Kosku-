/**
 * Helper utility for WhatsApp communication in Kostara
 */

export function formatWhatsAppNumber(phone) {
  if (!phone) return null

  // Remove all non-numeric characters
  let clean = String(phone).replace(/\D/g, '')

  if (!clean) return null

  // Convert Indonesian national prefix 08xx to international 628xx
  if (clean.startsWith('0')) {
    clean = '62' + clean.slice(1)
  } else if (clean.startsWith('8')) {
    clean = '62' + clean
  }

  return clean
}

export function getWhatsAppUrl(phone, message = '') {
  const cleanNumber = formatWhatsAppNumber(phone)
  if (!cleanNumber) return null

  const encodedMessage = encodeURIComponent(message)
  return `https://wa.me/${cleanNumber}${encodedMessage ? '?text=' + encodedMessage : ''}`
}

export function openWhatsAppChat(phone, message = '') {
  const url = getWhatsAppUrl(phone, message)
  if (!url) {
    alert('Nomor WhatsApp belum tersedia atau belum dilengkapi oleh pengguna.')
    return false
  }

  window.open(url, '_blank', 'noopener,noreferrer')
  return true
}

export function buildInquiryTemplate({ ownerName = 'Pemilik Kos', kosName = '', roomName = '', kosUrl = '' }) {
  const lines = [
    `Halo Bapak/Ibu ${ownerName},`,
    '',
    `Saya tertarik dengan kos *${kosName}*${roomName ? ` (Kamar: ${roomName})` : ''} yang saya temukan melalui aplikasi Kostara.`,
    'Apakah unit kamar tersebut saat ini masih tersedia untuk disurvei atau disewa?',
  ]

  if (kosUrl) {
    lines.push('', `Tautan: ${kosUrl}`)
  }

  lines.push('', 'Terima kasih!')
  return lines.join('\n')
}

export function buildTenantInquiryTemplate({ ownerName = 'Pemilik Kos', kosName = '', roomName = '', bookingId = '' }) {
  return [
    `Halo Bapak/Ibu ${ownerName},`,
    '',
    `Saya ingin menanyakan perihal pengajuan sewa kos *${kosName}*${roomName ? ` (Kamar: ${roomName})` : ''}${bookingId ? ` [ID Pesanan: #KOS-${bookingId}]` : ''} di Kostara.`,
    'Mohon informasinya, terima kasih!',
  ].join('\n')
}

export function buildOwnerContactTenantTemplate({ tenantName = 'Calon Penyewa', kosName = '', roomName = '', bookingId = '' }) {
  return [
    `Halo kak ${tenantName},`,
    '',
    `Saya pengelola/pemilik dari *${kosName}*. Menindaklanjuti pengajuan sewa Anda untuk kamar *${roomName}*${bookingId ? ` (#KOS-${bookingId})` : ''} melalui Kostara.`,
    'Apakah ada hal atau informasi yang ingin ditanyakan lebih lanjut?',
  ].join('\n')
}

/**
 * Generate Google Maps navigation link from coordinates or address
 */
export function getGoogleMapsUrl(location) {
  if (!location) return 'https://www.google.com/maps'

  if (location.latitude && location.longitude) {
    const lat = encodeURIComponent(location.latitude)
    const lng = encodeURIComponent(location.longitude)
    return `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`
  }

  if (location.alamat) {
    return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(location.alamat)}`
  }

  if (location.nama) {
    return `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(location.nama)}`
  }

  return 'https://www.google.com/maps'
}

/**
 * Build pre-filled text for Kostara WhatsApp Customer Service
 */
export function buildCustomerServiceTemplate(category = 'umum', userName = '') {
  const prefix = userName ? `Saya ${userName}. ` : ''
  switch (category) {
    case 'pencari':
      return `Halo Tim Customer Service Kostara,\n\n${prefix}Saya membutuhkan bantuan terkait pencarian kos, ketersediaan kamar, atau tata cara pemesanan di Kostara.\n\nMohon bantuannya, terima kasih!`
    case 'pemilik':
      return `Halo Tim Customer Service Kostara,\n\n${prefix}Saya mitra pemilik kos dan membutuhkan bantuan terkait pendaftaran properti kos, verifikasi kamar, atau pengelolaan pesanan di Kostara.\n\nTerima kasih!`
    case 'kendala':
      return `Halo Tim Customer Service Kostara,\n\n${prefix}Saya ingin melaporkan kendala teknis atau memiliki pertanyaan terkait aplikasi Kostara.\n\nRincian kendala:\n\nTerima kasih!`
    default:
      return `Halo Tim Customer Service Kostara,\n\n${prefix}Saya pengguna Kostara dan ingin bertanya atau berkonsultasi seputar layanan Kostara.\n\nTerima kasih!`
  }
}

