/**
 * Meringkas isi catatan supaya tidak terlalu panjang di tampilan list.
 * @param {string|null|undefined} isi
 * @param {number} maxPanjang
 * @returns {string}
 */
export function ringkasIsi(isi, maxPanjang = 40) {
  if (!isi) return '(kosong)'
  const teks = isi.trim()
  if (teks.length <= maxPanjang) return teks
  return teks.slice(0, maxPanjang).trimEnd() + '...'
}

/**
 * Format tanggal ISO dari Laravel (created_at) menjadi format id-ID yang mudah dibaca.
 * @param {string|null|undefined} tanggalIso
 * @returns {string}
 */
export function formatTanggal(tanggalIso) {
  if (!tanggalIso) return '-'
  const tanggal = new Date(tanggalIso)
  if (Number.isNaN(tanggal.getTime())) return '-'
  return tanggal.toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
  })
}
