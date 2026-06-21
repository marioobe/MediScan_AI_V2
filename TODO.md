# TODO List - Medical Image Classifier Web App

## 1. Setup Project
- [x] 1. Setup project structure (Laravel + FastAPI dalam 1 repo)

## 2. Laravel: Database & Auth
- [x] 2. Laravel: Install & konfigurasi project (Laravel 12, MySQL, env)
- [x] 2a. Buat migration tabel: admins
- [x] 2b. Buat migration tabel: training_jobs
- [x] 2c. Buat migration tabel: ai_models
- [x] 2d. Buat migration tabel: predictions
- [x] 2e. Seeder admin awal

## 3. FastAPI: AI Service
- [x] 3. FastAPI: Setup project & dependencies (Python, TensorFlow, Uvicorn)
- [x] 4. FastAPI: Implementasi POST /train (validasi ZIP, ekstrak, background training)
- [x] 4a. Validasi struktur dataset (folder kelas, ekstensi, filter mask/annotation files)
- [x] 4b. Implementasi training pipeline (MobileNetV2, 2 fase, class_weight, augmentasi)
- [x] 4c. Simpan model (.keras), history, confusion matrix, classification report
- [x] 5. FastAPI: Implementasi GET /train/{job_id}/status (polling progress)
- [x] 6. FastAPI: Implementasi POST /predict (prediksi gambar dengan model aktif)
- [x] 7. FastAPI: Implementasi GET /health

## 4. Laravel: Backend Logic
- [x] 8. Laravel: Model Eloquent (Admin, AiModel, TrainingJob, Prediction)
- [x] 9. Laravel: Auth admin (login, middleware)
- [x] 10. Laravel: Controller & route public (/ landing, /tes prediksi)
- [x] 11. Laravel: Controller & route admin (/admin/dashboard, models, training, predictions)
- [x] 12. Laravel: Implementasi HTTP Client ke FastAPI (Guzzle)

## 5. Laravel: Frontend (Blade Views)
- [x] 13. Laravel: Halaman training progress real-time (JS polling tiap 3-5 detik)
- [x] 14. Laravel: Halaman hasil prediksi + riwayat + pagination
- [x] 15. Laravel: Blade views (landing, dashboard, models, training, predictions, login)
  - 🎨 Tailwind CSS, gradasi Indigo→Sky (#6366f1→#0ea5e9)

## 6. Validasi & Keamanan
- [x] 16. Validasi & error handling (ukuran file, belum ada model aktif, job gagal)
- [x] 17. Konfigurasi CORS Laravel <-> FastAPI

## 7. Dokumentasi & Finalisasi
- [x] 18. File .env.example & README.md (cara install & jalankan)
- [ ] 19. Testing & verifikasi akhir (lint, typecheck, jalankan semua service)
  - [x] Login admin
  - [x] Dashboard
  - [x] Upload prediksi gambar
  - [ ] Training ZIP upload
  - [ ] Confusion Matrix modal
- [ ] 20. Dokumentasi bagian yang perlu disesuaikan manual (DB, path model, dll)

---

**Progress: 25/27 tasks completed ✅**
**Sisa: Testing & final verification**
