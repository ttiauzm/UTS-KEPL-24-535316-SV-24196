<script setup>
import { ref, onMounted } from 'vue'
import { ringkasIsi, formatTanggal } from '../utils/format'
import { getNotes, createNote, updateNote, deleteNote, pesanError } from '../api/http'

const daftarCatatan = ref([])
const status = ref('loading') // loading | ok | error
const pesan = ref('')

const form = ref({ title: '', content: '' })
const editId = ref(null)

async function ambilData() {
  status.value = 'loading'
  try {
    daftarCatatan.value = await getNotes()
    status.value = 'ok'
  } catch (err) {
    pesan.value = pesanError(err)
    status.value = 'error'
  }
}

async function simpan() {
  pesan.value = ''
  try {
    if (editId.value) {
      await updateNote(editId.value, form.value)
    } else {
      await createNote(form.value)
    }
    batal()
    await ambilData()
  } catch (err) {
    pesan.value = pesanError(err)
  }
}

function mulaiEdit(item) {
  editId.value = item.id
  form.value = { title: item.title, content: item.content ?? '' }
}

function batal() {
  editId.value = null
  form.value = { title: '', content: '' }
}

async function hapus(item) {
  if (!window.confirm(`Hapus catatan "${item.title}"?`)) return
  try {
    await deleteNote(item.id)
    await ambilData()
  } catch (err) {
    pesan.value = pesanError(err)
  }
}

onMounted(ambilData)
</script>

<template>
  <section>
    <h2>Daftar Catatan</h2>

    <form @submit.prevent="simpan">
      <h3>{{ editId ? 'Ubah Catatan' : 'Tambah Catatan' }}</h3>
      <p><input v-model="form.title" placeholder="Judul" required /></p>
      <p><textarea v-model="form.content" placeholder="Isi catatan"></textarea></p>
      <button type="submit">{{ editId ? 'Simpan Perubahan' : 'Tambah' }}</button>
      <button v-if="editId" type="button" @click="batal">Batal</button>
    </form>

    <p v-if="pesan" role="alert">{{ pesan }}</p>
    <p v-if="status === 'loading'">Memuat data...</p>
    <p v-else-if="status === 'error'">Gagal mengambil data dari Laravel.</p>

    <ul v-else>
      <li v-for="item in daftarCatatan" :key="item.id">
        <strong>{{ item.title }}</strong>
        — {{ ringkasIsi(item.content) }}
        <br />
        <small>{{ formatTanggal(item.created_at) }}</small>
        <button type="button" @click="mulaiEdit(item)">Ubah</button>
        <button type="button" @click="hapus(item)">Hapus</button>
      </li>
      <li v-if="daftarCatatan.length === 0">Belum ada catatan.</li>
    </ul>

    <button type="button" @click="ambilData">Muat Ulang</button>
  </section>
</template>
