# syntax=docker/dockerfile:1

# =====================================================================
# Stage 1 (build): install dependensi + build Vue (Vite) jadi file statis
# =====================================================================
FROM node:20-alpine AS build
WORKDIR /app

# Layer dependensi: hanya berubah kalau package*.json berubah
COPY package.json package-lock.json ./
RUN npm ci

COPY . .

# Alamat API backend dipasang saat build (dibaca Vite sebagai VITE_API_URL).
# Dipanggil dari BROWSER, jadi harus alamat yang bisa dijangkau browser (bukan nama service Docker).
ARG VITE_API_URL=http://localhost:8000/api
ENV VITE_API_URL=$VITE_API_URL
RUN npm run build

# =====================================================================
# Stage 2 (runtime): Nginx non-root, hanya berisi hasil build statis
# =====================================================================
FROM nginxinc/nginx-unprivileged:1.27-alpine AS runtime

COPY nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=build /app/dist /usr/share/nginx/html

EXPOSE 8080

HEALTHCHECK --interval=15s --timeout=5s --start-period=10s --retries=5 \
    CMD wget -qO- http://127.0.0.1:8080/ > /dev/null || exit 1
