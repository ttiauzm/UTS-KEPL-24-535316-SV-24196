import { describe, it, expect } from 'vitest'
import { ringkasIsi, formatTanggal } from './format'

describe('ringkasIsi', () => {
  it('mengembalikan "(kosong)" kalau isi null/undefined/kosong', () => {
    expect(ringkasIsi(null)).toBe('(kosong)')
    expect(ringkasIsi('')).toBe('(kosong)')
  })

  it('tidak memotong teks yang pendek', () => {
    expect(ringkasIsi('Halo dunia', 40)).toBe('Halo dunia')
  })

  it('memotong teks yang panjang dan menambahkan "..."', () => {
    const teksPanjang = 'a'.repeat(50)
    const hasil = ringkasIsi(teksPanjang, 10)
    expect(hasil).toBe('aaaaaaaaaa...')
    expect(hasil.length).toBeLessThanOrEqual(13)
  })
})

describe('formatTanggal', () => {
  it('mengembalikan "-" kalau tanggal kosong atau tidak valid', () => {
    expect(formatTanggal(null)).toBe('-')
    expect(formatTanggal('bukan-tanggal')).toBe('-')
  })

  it('memformat tanggal ISO menjadi format id-ID', () => {
    const hasil = formatTanggal('2026-01-15T10:00:00.000000Z')
    expect(hasil).toContain('2026')
    expect(hasil).toContain('Januari')
  })
})
