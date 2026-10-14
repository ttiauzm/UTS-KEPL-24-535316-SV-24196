<script setup>
import { useRouter, useRoute } from 'vue-router'
import { logout, apakahSudahLogin } from './api/http'

const router = useRouter()
const route = useRoute()

async function keluar() {
  await logout().catch(() => {})
  router.push('/login')
}
</script>

<template>
  <header>
    <h1>Notes App (Vue 3)</h1>
    <nav>
      <router-link to="/">Beranda</router-link>
      |
      <router-link to="/catatan">Daftar Catatan</router-link>
      |
      <!-- route.fullPath dibaca supaya tombol ikut diperbarui saat berpindah halaman -->
      <a v-if="route.fullPath && apakahSudahLogin()" href="#" @click.prevent="keluar">Logout</a>
      <router-link v-else to="/login">Login</router-link>
    </nav>
  </header>

  <main>
    <router-view />
  </main>
</template>
