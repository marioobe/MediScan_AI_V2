# TODO1 — Migrasi MySQL & Fix Bug

## Fase 1: Fix Bug Prediction
- [x] PredictionController: tambah `ai_model_id` dari local model lookup
- [x] Migration 054009: bikin `ai_model_id` nullable (`->nullable()`)

## Fase 2: Pindah ke MySQL
- [x] Backup `database.sqlite` → `database.sqlite.backup`
- [x] Update `.env`: `DB_CONNECTION=mysql`, aktifkan DB_HOST/DB_PORT/DATABASE/USERNAME/PASSWORD
- [x] `php artisan db:wipe` (bersihkan tabel lama)
- [x] `php artisan migrate`
- [x] Buat admin user via tinker

## Fase 3: Testing Fungsional
- [x] Login admin di `/admin/login`
- [x] Dashboard muncul (models count, predictions, training)
- [x] Upload gambar prediksi di `/tes`
- [ ] Lihat hasil prediksi di admin panel
- [ ] Upload dataset ZIP training
- [ ] Polling progress training
- [ ] Lihat model & confusion matrix

## Fase 4: Final
- [x] Update TODO.md progres
- [x] Catat perubahan di catatanTerakhir.md
