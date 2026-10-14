import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  test: {
    // 'node' cukup karena kita hanya test fungsi logika murni (utils/format.js),
    // bukan komponen Vue -> tidak butuh Laravel atau browser sama sekali.
    environment: 'node',
    globals: true,
  },
})
