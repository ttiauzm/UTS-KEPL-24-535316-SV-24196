import axios from 'axios'

export const TOKEN_KEY = 'token'

// Satu instance Axios untuk semua request ke Laravel.
// VITE_API_URL contoh: http://127.0.0.1:8000/api
export const http = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: { Accept: 'application/json' },
})

// Otomatis kirim token Sanctum: Authorization: Bearer <token>
http.interceptors.request.use((config) => {
  const token = localStorage.getItem(TOKEN_KEY)
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

// Token kedaluwarsa/tidak valid -> bersihkan dan arahkan ke halaman login
http.interceptors.response.use(
  (res) => res,
  (err) => {
    const sudahLogin = err.config?.url?.includes('/login')
    if (err.response?.status === 401 && !sudahLogin) {
      localStorage.removeItem(TOKEN_KEY)
      if (window.location.pathname !== '/login') window.location.assign('/login')
    }
    return Promise.reject(err)
  },
)

export function apakahSudahLogin() {
  return Boolean(localStorage.getItem(TOKEN_KEY))
}

export async function login(email, password) {
  const { data } = await http.post('/login', { email, password })
  localStorage.setItem(TOKEN_KEY, data.token)
  return data
}

export async function logout() {
  try {
    await http.post('/logout')
  } finally {
    localStorage.removeItem(TOKEN_KEY)
  }
}

export const getNotes = () => http.get('/notes').then((r) => r.data)
export const createNote = (payload) => http.post('/notes', payload).then((r) => r.data)
export const updateNote = (id, payload) => http.put(`/notes/${id}`, payload).then((r) => r.data)
export const deleteNote = (id) => http.delete(`/notes/${id}`).then((r) => r.data)

// Ambil pesan error yang ramah dari respons Laravel (422/401/dll)
export function pesanError(err) {
  const data = err.response?.data
  if (data?.errors) return Object.values(data.errors).flat().join(' ')
  return data?.message || err.message
}
