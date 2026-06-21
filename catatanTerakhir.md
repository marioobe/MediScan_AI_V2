# Catatan Terakhir — MediScan AI

> Status: Semua fitur inti selesai. Tinggal testing akhir.

---

## Cara Menjalankan

```bash
# Terminal 1 — FastAPI (Python)
cd ai-service
python -m uvicorn app.main:app --host 0.0.0.0 --port 8001 --reload

# Terminal 2 — Laravel (PHP)
cd laravel
php -d upload_max_filesize=500M -d post_max_size=500M artisan serve --port=8080
```

**Akses:**
- Landing: `http://localhost:8080/`
- Prediksi publik: `http://localhost:8080/tes`
- Admin login: `http://localhost:8080/admin/login`
- Admin dashboard: `http://localhost:8080/admin/dashboard`

**Kredensial Admin:**
- Email: `admin@medical-classifier.com`
- Password: `password`

---

## Ringkasan Perubahan Berdasarkan Sesi

### 🔄 Sesi 1 — Setup Awal + Core Backend
| No | Apa yang dilakukan | File |
|----|-------------------|------|
| 1 | Project structure: `ai-service/`, `laravel/`, `storage/` | — |
| 2 | Laravel 12 install + .env (MySQL, AI_SERVICE_URL) | `laravel/.env` |
| 3 | FastAPI dependencies + endpoints: POST /train, GET /status, POST /predict, GET /models/active | `ai-service/requirements.txt`, `main.py` |
| 4 | Trainer 2-phase MobileNetV2 (5 frozen + 5 fine-tune), class_weight, ProgressCallback | `ai-service/app/trainer.py` |
| 5 | Migrations: admins, training_jobs, ai_models, predictions | `laravel/database/migrations/` |
| 6 | Auth admin guard, model Eloquent, AiService HTTP Client | `laravel/app/` |
| 7 | Routes + Blade views: landing, predict, login, dashboard, models, training, training-progress, predictions | `laravel/routes/web.php`, `resources/views/` |
| 8 | AJAX upload + progress bar + beforeunload protection | `training.blade.php` |

### 🖼️ Sesi 2 — Prediksi CRUD + Timezone Fix
| No | Apa yang dilakukan | File |
|----|-------------------|------|
| 9 | Timezone: UTC → Asia/Jakarta | `laravel/config/app.php` |
| 10 | Migration: +prediction_id, original_name, model_label, nullable ai_model_id | `2026_06_21_155950_*.php` |
| 11 | PredictionController simpan image ke storage + DB record | `PredictionController.php` |
| 12 | AdminPredictionController: baca dari DB + show + destroy | `AdminPredictionController.php` |
| 13 | Predictions view: Aksi kolom (lihat modal + delete), detail modal, AJAX delete | `admin/predictions.blade.php` |
| 14 | Storage link created (`public/storage` → `storage/app/public`) | — |
| 15 | Session driver change: database → file (fix CSRF 419) | `laravel/.env` |

### ⬇️ Sesi 3 — Download Button di Modal Detail
| No | Apa yang dilakukan | File |
|----|-------------------|------|
| 16 | Ikon download (SVG panah bawah) di samping nama file di modal detail | `admin/predictions.blade.php:154-158` |
| 17 | `<a download="original_name">` — download langsung, bukan buka tab baru | `admin/predictions.blade.php:155` |

### 🗄️ Sesi 4 — CRUD Model Management + CM Modal
| No | Apa yang dilakukan | File |
|----|-------------------|------|
| 18 | FastAPI: +`DELETE /models/{model_id}` hapus file .keras + CM + history + class_names | `ai-service/app/main.py:287-309` |
| 19 | FastAPI: +`POST /models/register` upload .keras/.h5 eksternal | `ai-service/app/main.py:311-333` |
| 20 | Migration: +model_id (unique), +notes ke tabel ai_models | `2026_06_21_161700_*.php` |
| 21 | AiModel: +model_id, notes di fillable; +scopeActive() | `app/Models/AiModel.php` |
| 22 | AiService: +deleteModel(), +registerModel() | `app/Services/AiService.php` |
| 23 | AdminModelController: index() merge FastAPI + DB, update(), destroy(), store() | `app/Http/Controllers/AdminModelController.php` |
| 24 | Routes: POST register, POST update, DELETE destroy | `routes/web.php` |
| 25 | Models view: **4 modal** (CM popup, Edit, Delete, Create) + enhanced table + semua JS | `admin/models.blade.php` |

### 🗄️ Sesi 5 — Admin Panel UI Redesign
| No | Apa yang dilakukan | File |
|----|-------------------|------|
| 27 | **Layout**: mobile hamburger nav, breadcrumbs, toast system (ganti alert), confirm modal (ganti confirm()) | `layouts/admin.blade.php` |
| 28 | **Dashboard**: 4 stat cards (baru: training sessions), recent predictions list, recent training list | `admin/dashboard.blade.php`, `AdminDashboardController.php` |
| 29 | **Models**: search bar filter, pagination 10/page, toast/confirm, activate button disable | `admin/models.blade.php` |
| 30 | **Predictions**: search bar, pagination, confirm modal ganti confirm(), probability bars | `admin/predictions.blade.php` |
| 31 | **Training**: drag-drop visual feedback, status icons history, cancel pakai confirm modal | `admin/training.blade.php` |
| 32 | **Training Progress**: circular progress ring (SVG), breadcrumb link back | `admin/training-progress.blade.php` |

### 🐘 Sesi 6 — MySQL Migration + Bug Fixes
| No | Apa yang dilakukan | File |
|----|-------------------|------|
| 33 | Fixed `listModels()` & `getPredictions()` — extract `value` key dari FastAPI response | `app/Services/AiService.php` |
| 34 | Fixed migration 075709 — skip MODIFY COLUMN untuk SQLite | `database/migrations/2026_06_21_075709_*.php` |
| 35 | Fixed migration 155950 — skip MODIFY COLUMN untuk SQLite | `database/migrations/2026_06_21_155950_*.php` |
| 36 | Fixed migration 054009 — `ai_model_id` nullable | `database/migrations/2026_06_21_054009_*.php` |
| 37 | Fixed `PredictionController` — lookup local model + set `ai_model_id` | `app/Http/Controllers/PredictionController.php` |
| 38 | Migrated database: SQLite → MySQL (`medical_classifier`) | `laravel/.env` |
| 39 | Admin user seeded | — |

### 🎨 Sesi 7 — Landing Page Redesign (Dark Futuristic)
| No | Apa yang dilakukan | File |
|----|-------------------|------|
| 40 | **Navbar**: fixed, transparan → navbar-blur saat scroll, menu Beranda+Predict+Admin | `layouts/public.blade.php` |
| 41 | **Landing**: bg-slate-950 + 3 glowing orbs (indigo, cyan, purple) animasi pulse | `public/landing.blade.php` |
| 42 | **Hero headline**: 7xl gradient text + badge "Powered by MobileNetV2" | `public/landing.blade.php` |
| 43 | **CTA button**: glow-shadow (neon) indigo→cyan, hover translateY(-2px) | `public/landing.blade.php` |
| 44 | **Mockup frame**: terminal-style dengan dots, drop zone + hasil prediksi dummy (97.3%) | `public/landing.blade.php` |
| 45 | **Feature cards**: `bg-white/[0.03] border-white/[0.06]` glassmorphism, glow hover | `public/landing.blade.php` |
| 46 | **How It Works**: 3 step dengan angka gradient, step-number border glow, CTA bawah | `public/landing.blade.php` |
| 47 | **Footer**: dark border-white/5, logo kecil, copyright | `layouts/public.blade.php` |
| 48 | Custom CSS: `glow-orb`, `glow-card`, `glow-btn`, `navbar-blur`, `animate-float`, `animate-pulse-glow` | `layouts/public.blade.php` (inline `<style>`) |

---

## Arsitektur

```
┌─────────────────┐     HTTP (port 8080)     ┌──────────────┐
│   Browser        │ ◄──────────────────────► │   Laravel    │
│  (Tailwind CSS)  │                          │  (PHP 8.2)   │
└─────────────────┘                          └──────┬───────┘
                                                    │ HTTP Client
                                                    ▼
                                           ┌─────────────────┐
                                           │    FastAPI       │
                                           │  (port 8001)     │
                                           │  Python 3.12     │
                                           │  TensorFlow/     │
                                           │  MobileNetV2     │
                                           └─────────────────┘
```

---

## Key Files Reference

| File | Fungsi |
|------|--------|
| `ai-service/app/main.py` | FastAPI: semua endpoint + model management |
| `ai-service/app/trainer.py` | ProgressCallback, training pipeline, cancel |
| `ai-service/app/config.py` | Path constants, konfigurasi |
| `laravel/app/Http/Controllers/PredictionController.php` | Upload + predict + save image+DB |
| `laravel/app/Http/Controllers/AdminPredictionController.php` | CRUD prediksi dari DB |
| `laravel/app/Http/Controllers/AdminTrainingController.php` | Upload dataset + cancel training |
| `laravel/app/Http/Controllers/AdminModelController.php` | CRUD model (merge FastAPI + DB) |
| `laravel/app/Services/AiService.php` | HTTP Client ke FastAPI |
| `laravel/resources/views/admin/models.blade.php` | 4 modal: CM, Edit, Delete, Create |
| `laravel/resources/views/admin/predictions.blade.php` | Modal detail + download + delete |
| `laravel/resources/views/admin/training.blade.php` | AJAX upload + progress bar |
| `laravel/resources/views/admin/training-progress.blade.php` | Real-time epoch table polling |
| `laravel/resources/views/public/landing.blade.php` | Landing page dark futuristic |
| `laravel/resources/views/layouts/public.blade.php` | Navbar fixed blur + footer |

---

## Hal yang Perlu Di-test / Diverifikasi

- [ ] Upload dataset ZIP nyata di `/admin/training`
- [ ] Polling real-time epoch table
- [ ] Cancel training
- [ ] Prediksi gambar di `/tes`
- [ ] Lihat detail prediksi + download gambar
- [ ] Hapus prediksi
- [ ] Edit model name + notes
- [ ] Hapus model (verify file .keras juga kehapus)
- [ ] Register model eksternal (.keras file)
- [ ] Activate model + deactivate otomatis yang lain
- [ ] Confusion Matrix modal
- [ ] Responsive landing page di mobile
- [ ] Scroll navbar blur effect
