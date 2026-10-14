<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { login, pesanError } from '../api/http'

const router = useRouter()
const email = ref('test@example.com')
const password = ref('')
const error = ref('')
const loading = ref(false)

async function submit() {
  error.value = ''
  loading.value = true
  try {
    await login(email.value, password.value)
    router.push('/catatan')
  } catch (err) {
    error.value = pesanError(err)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <section>
    <h2>Login</h2>
    <form @submit.prevent="submit">
      <p>
        <label>Email<br /><input v-model="email" type="email" required /></label>
      </p>
      <p>
        <label>Password<br /><input v-model="password" type="password" required /></label>
      </p>
      <p v-if="error" role="alert">{{ error }}</p>
      <button type="submit" :disabled="loading">{{ loading ? 'Memproses...' : 'Masuk' }}</button>
    </form>
  </section>
</template>
