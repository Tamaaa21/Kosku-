/**
 * Utility for Midtrans Snap JS SDK
 */

let snapLoadedPromise = null

export function loadMidtransSnap() {
  if (window.snap) {
    return Promise.resolve(window.snap)
  }

  if (snapLoadedPromise) {
    return snapLoadedPromise
  }

  snapLoadedPromise = new Promise((resolve, reject) => {
    const isProduction = import.meta.env.VITE_MIDTRANS_IS_PRODUCTION === 'true'
    const clientKey = import.meta.env.VITE_MIDTRANS_CLIENT_KEY || 'SB-Mid-client-test-Kostara123'
    const snapUrl = isProduction
      ? 'https://app.midtrans.com/snap/snap.js'
      : 'https://app.sandbox.midtrans.com/snap/snap.js'

    const script = document.createElement('script')
    script.src = snapUrl
    script.setAttribute('data-client-key', clientKey)
    script.async = true

    script.onload = () => {
      resolve(window.snap)
    }

    script.onerror = (err) => {
      snapLoadedPromise = null
      reject(new Error('Gagal memuat sistem pembayaran Midtrans. Periksa koneksi internet Anda.'))
    }

    document.head.appendChild(script)
  })

  return snapLoadedPromise
}

/**
 * Open Midtrans Snap Popup
 *
 * @param {string} snapToken
 * @param {object} callbacks
 * @returns {Promise}
 */
export async function payWithSnap(snapToken, callbacks = {}) {
  await loadMidtransSnap()

  if (!window.snap) {
    throw new Error('Midtrans Snap tidak tersedia.')
  }

  return new Promise((resolve, reject) => {
    window.snap.pay(snapToken, {
      onSuccess: (result) => {
        if (callbacks.onSuccess) callbacks.onSuccess(result)
        resolve(result)
      },
      onPending: (result) => {
        if (callbacks.onPending) callbacks.onPending(result)
        resolve(result)
      },
      onError: (result) => {
        if (callbacks.onError) callbacks.onError(result)
        reject(result)
      },
      onClose: () => {
        if (callbacks.onClose) callbacks.onClose()
        resolve({ status: 'closed' })
      },
    })
  })
}
