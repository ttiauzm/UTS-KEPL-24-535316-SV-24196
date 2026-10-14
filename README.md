Nama: Mutia Umniati Z
NIM: 24/535316/SV/24196

# Notes App — Laravel API + Vue 3 + Docker

Aplikasi catatan (CRUD) untuk UTS Evolusi Perangkat Lunak:
**backend Laravel 12 (REST API + token Sanctum)**, **frontend Vue 3 (Axios)**, **MySQL**, dijalankan dengan
**Docker Compose**, dites dan di-deploy memakai **GitHub Actions** ke **GitHub Container Registry (GHCR)**.

## Arsitektur

```
Browser ──> frontend (Nginx :8080, Vue build statis)
   │
   └── Axios + Bearer token ──> backend (FrankenPHP + Laravel :8000) ──> db (MySQL 8)
```

## REST API

Base URL: `http://localhost:8000/api` — semua respons JSON.
Koleksi Postman: `postman/notes-api.postman_collection.json` (11 request + test otomatis).

| Method | Endpoint | Auth | Keterangan |
|---|---|---|---|
| POST | `/register` | – | Daftar, mengembalikan token (201 / 422) |
| POST | `/login` | – | Login, mengembalikan token Sanctum (200 / 401) |
| GET | `/me` | Bearer | Data user yang login |
| POST | `/logout` | Bearer | Hapus token saat ini |
| GET | `/notes` | Bearer | Daftar catatan |
| POST | `/notes` | Bearer | Buat catatan (201 / 422) |
| GET | `/notes/{id}` | Bearer | Detail catatan (200 / 404) |
| PUT/PATCH | `/notes/{id}` | Bearer | Ubah catatan |
| DELETE | `/notes/{id}` | Bearer | Hapus catatan |

Tanpa token, endpoint terproteksi menjawab `401 {"message":"Unauthenticated."}`.
Akun demo (seeder): `test@example.com` / `password`.

## Menjalankan dengan Docker Compose

```bash
docker compose up --build
```

- Frontend: http://localhost:8080 · Backend: http://localhost:8000/api (health check: `/up`)
- Urutan start: `db` (MySQL, menunggu sehat) → `backend` (migrate + seed otomatis) → `frontend`.
- Kredensial DB bisa diubah lewat env `MYSQL_DATABASE`, `MYSQL_USER`, `MYSQL_PASSWORD`, `MYSQL_ROOT_PASSWORD`.
- Hentikan: `docker compose down` (tambah `-v` untuk menghapus data DB).

Dockerfile backend dan frontend sama-sama **multi-stage**, dengan `.dockerignore` di root dan di `frontend/`.
Backend berjalan di **FrankenPHP** (Caddy + PHP) sebagai user non-root.

## Development tanpa Docker

Butuh PHP 8.3 + Composer, dan Node 20.

```bash
# Backend
composer install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite && php artisan migrate --seed
php artisan serve                                   # http://127.0.0.1:8000

# Frontend (terminal lain)
cd frontend && cp .env.example .env && npm ci && npm run dev     # http://localhost:5173
```

## Test

```bash
php artisan test                           # backend: feature test auth + CRUD
cd frontend && npm run lint && npm test    # frontend
```

## CI/CD (GitHub Actions)

- `.github/workflows/ci.yml` — **2 job**: `Backend (Laravel test)` dan `Frontend (Vue lint, test, build)`.
  Jalan pada push ke `main`/`dev`/`feature/**` dan pada Pull Request ke `main`/`dev`.
- `.github/workflows/deploy.yml` — job `Deploy`: build image backend & frontend, push ke GHCR
  (`ghcr.io/<owner>/<repo>-backend|frontend`, tag `latest` + SHA commit). Login memakai **GitHub Secrets**
  (`GITHUB_TOKEN`; opsional secret `VITE_API_URL`). Hanya jalan di `main`.

## Alur Git

- `main` — stabil & dilindungi (branch protection: wajib Pull Request + CI hijau).
- `dev` — integrasi. `feature/*` — pengembangan, digabung lewat Pull Request.
