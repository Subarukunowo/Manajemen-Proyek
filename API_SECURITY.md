# API Security — Manajemen Proyek

## Ringkasan

API REST dilindungi oleh **Laravel Sanctum** (token-based authentication).
Tidak ada endpoint yang dapat diakses tanpa token. API tidak ditampilkan di UI manapun
dan hanya bisa digunakan oleh tim yang memiliki token yang valid.

---

## Cara Mendapatkan Token API

### 1. Login via API untuk mendapatkan token

```bash
curl -X POST http://localhost/dashboard_manajemen_proyek/public/api/auth/token \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "risethunder7@gmail.com",
    "password": "admin@2026!",
    "device_name": "my-api-client"
  }'
```

Response:
```json
{
  "token": "1|abc123xyz...",
  "user": { "id": 1, "name": "Admin", "email": "risethunder7@gmail.com", "role": "admin" }
}
```

### 2. Gunakan token di setiap request

```bash
# Header Authorization wajib ada di semua request API
curl -X GET http://localhost/dashboard_manajemen_proyek/public/api/projects \
  -H "Authorization: Bearer 1|abc123xyz..." \
  -H "Accept: application/json"
```

---

## Endpoint Tersedia

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| `POST` | `/api/auth/token` | Login & dapatkan token |
| `DELETE` | `/api/auth/token` | Revoke token (logout API) |
| `GET` | `/api/projects` | Daftar semua proyek |
| `POST` | `/api/projects` | Buat proyek baru |
| `PUT` | `/api/projects/{id}` | Update proyek |
| `DELETE` | `/api/projects/{id}` | Hapus proyek |
| `PATCH` | `/api/projects/{id}/status` | Update status proyek |
| `GET` | `/api/projects/{id}/phases` | Fase proyek |
| `GET` | `/api/projects/{id}/tasks` | Task proyek |
| `GET` | `/api/projects/{id}/risks` | Risiko proyek |
| `GET` | `/api/projects/{id}/changes` | Change request |
| `GET` | `/api/projects/{id}/milestones` | Milestone |
| `GET` | `/api/projects/{id}/budget` | Anggaran |
| `GET` | `/api/projects/{id}/kurva-s` | Data Kurva-S |
| `GET` | `/api/projects/{id}/kurva-s/chart` | Chart data |

---

## Rate Limiting

- **60 request/menit** per token untuk semua endpoint
- **10 request/menit** untuk endpoint auth (anti-brute force)
- Jika melewati batas: `HTTP 429 Too Many Requests`

---

## Keamanan

| Layer | Mekanisme |
|-------|-----------|
| Autentikasi | Laravel Sanctum Bearer Token |
| Rate Limiting | 60 req/min (auth: 10 req/min) |
| Audit Log | Semua write operation dicatat di `audit_logs` |
| CORS | Hanya origin yang terdaftar di `config/cors.php` |
| No UI Exposure | Tidak ada link/button ke API di tampilan web |
| Token Expiry | Token dapat di-revoke kapan saja via `DELETE /api/auth/token` |

---

## Cara Revoke Token (Logout API)

```bash
curl -X DELETE http://localhost/dashboard_manajemen_proyek/public/api/auth/token \
  -H "Authorization: Bearer 1|abc123xyz..." \
  -H "Accept: application/json"
```

---

## Contoh CRUD via API

### Buat Proyek Baru
```bash
curl -X POST http://localhost/dashboard_manajemen_proyek/public/api/projects \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "nama": "Proyek Baru",
    "status": "Draft",
    "tanggal_mulai": "2026-01-01",
    "tanggal_selesai": "2026-12-31",
    "anggaran": 5000000000
  }'
```

### Tambah Task ke Proyek
```bash
curl -X POST http://localhost/dashboard_manajemen_proyek/public/api/projects/1/tasks \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "nama": "Setup Server",
    "prioritas": "High",
    "status": "Todo",
    "tanggal_mulai": "2026-01-01",
    "tanggal_selesai": "2026-01-15"
  }'
```

### Update Progress Task
```bash
curl -X PATCH http://localhost/dashboard_manajemen_proyek/public/api/projects/1/tasks/1/progress \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"persen_selesai": 75}'
```

---

> **Penting:** Jangan bagikan token ke siapapun di luar tim.
> Token tersimpan di tabel `personal_access_tokens` dan dapat di-revoke via database
> atau endpoint `DELETE /api/auth/token`.
