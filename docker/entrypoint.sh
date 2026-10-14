#!/bin/sh
set -e
cd /app

# Buat APP_KEY sementara kalau belum diberikan lewat environment
if [ -z "$APP_KEY" ]; then
  APP_KEY="$(php artisan key:generate --show)"
  export APP_KEY
fi

# Tunggu database siap lalu jalankan migrasi (coba ulang maksimal 20x)
tries=0
until php artisan migrate --force; do
  tries=$((tries + 1))
  if [ "$tries" -ge 20 ]; then
    echo "Migrasi gagal setelah $tries percobaan, database tidak bisa dijangkau." >&2
    exit 1
  fi
  echo "Database belum siap, coba lagi dalam 3 detik... ($tries/20)"
  sleep 3
done

# Seed akun demo + contoh catatan (idempoten, aman dijalankan berulang)
if [ "${DB_SEED:-false}" = "true" ]; then
  php artisan db:seed --force
fi

exec "$@"
