# PROMPT: Bangun Web Klasifikasi Citra Medis dengan Admin Training Panel

Kamu adalah AI coding agent. Bangun sebuah web application untuk klasifikasi citra medis menggunakan MobileNetV2 + Transfer Learning, dari nol (belum ada kode sama sekali). Kerjakan SEMUA spesifikasi di bawah ini secara end-to-end: setup project, buat skema database, buat backend AI service, buat web app (admin + user), dan pastikan semuanya terhubung dan bisa dijalankan.

## 1. Konsep Sistem

Ada 2 sisi:

- **Sisi Admin (perlu login):** Admin upload dataset gambar (file ZIP, terstruktur per folder kelas), sistem melatih (training) model MobileNetV2 dari dataset itu, admin bisa lihat progress training, lihat hasil metrik (akurasi, loss, confusion matrix), lalu mengaktifkan model yang dianggap terbaik. Hanya satu model yang aktif dalam satu waktu.
- **Sisi User (TANPA login):** User upload satu gambar, sistem memprediksi pakai model yang sedang aktif, dan user langsung lihat hasil (kelas prediksi + confidence score per kelas).

## 2. Tech Stack (wajib, jangan diganti)

- **Backend AI Service:** Python 3.11+, FastAPI, TensorFlow/Keras, Uvicorn
- **Web App (frontend + backend bisnis):** Laravel 12 (PHP 8.2+), Blade templating, MySQL
- **Komunikasi antar service:** Laravel memanggil FastAPI lewat HTTP (Laravel HTTP Client/Guzzle)
- **Job/queue:** Laravel Queue (database driver cukup) ATAU background task di FastAPI — pilih salah satu, tapi WAJIB asynchronous (lihat poin 5)

## 3. Arsitektur

```
Browser (User & Admin)
        |
        v
Laravel App (port 8080)
  - Routing, auth admin, view, database
  - Kirim HTTP request ke FastAPI utk training & prediksi
        |
        v
FastAPI AI Service (port 8001)
  - Load/train model TensorFlow
  - Endpoint training (async/background)
  - Endpoint prediksi
  - Simpan file model ke disk
        |
        v
MySQL Database
```

## 4. Skema Database (Laravel migration)

Buat tabel-tabel berikut:

- `admins`: id, name, email, password, timestamps
- `ai_models`: id, name, version, class_names (JSON, contoh: ["benign","malignant","normal"]), file_path, accuracy, loss, is_active (boolean), training_job_id (nullable, foreign key), created_at
- `training_jobs`: id, dataset_path, status (enum: pending, validating, extracting, training, completed, failed), current_epoch, total_epoch, progress_percent, accuracy_result, loss_result, error_message, log (text, log progress), started_at, finished_at, created_by (admin_id)
- `predictions`: id, ai_model_id (foreign key), image_path, predicted_class, confidence, probabilities (JSON, semua kelas+confidence-nya), created_at

## 5. ATURAN WAJIB untuk fitur Training (JANGAN DILANGGAR)

Ini bagian paling kritis. Pelanggaran terhadap aturan ini akan membuat model gagal total atau server crash.

**5.1 Training TIDAK BOLEH berjalan di dalam request HTTP yang sama (blocking).**
Training bisa makan waktu menit sampai jam. Endpoint upload dataset harus:
1. Terima file, validasi, simpan
2. Buat record `training_jobs` dengan status `pending`
3. Langsung balas response `{job_id, status: "pending"}` ke client
4. Jalankan proses training di background (worker/queue/background task — bukan di thread utama request)
5. Selama training berjalan, update kolom `training_jobs` (current_epoch, progress_percent, status) secara periodik agar bisa dipoll
6. Admin panel poll endpoint status setiap beberapa detik untuk update progress bar real-time

**5.2 Validasi struktur dataset SEBELUM training dimulai.**
Setelah ZIP diekstrak, dataset harus berbentuk:
```
dataset/
  kelas_a/
    gambar1.jpg
    gambar2.jpg
  kelas_b/
    ...
```
Validasi:
- Minimal 2 folder kelas
- Tiap folder minimal punya N gambar (tentukan threshold wajar, misal minimal 20 gambar/kelas, kasih warning kalau kurang dari 50)
- Hanya terima ekstensi gambar valid (.jpg, .jpeg, .png)
- **PENTING:** Jika ada file dengan pola nama mengandung kata "mask", "label", "annotation", "gt" (ground truth) di SETIAP folder kelas — ini kemungkinan file anotasi/mask, BUKAN gambar untuk diklasifikasi. WAJIB filter/exclude file seperti ini sebelum training, dan tampilkan warning ke admin berapa file yang di-skip. (Contoh nyata: dataset BUSI breast ultrasound punya file `nama_mask.png` di setiap folder kelas yang harus difilter.)
- Tolak ZIP dengan path traversal (`../`) atau ukuran di atas limit yang wajar (misal 500MB)

**5.3 Preprocessing HANYA boleh diterapkan SATU KALI.**
`preprocess_input` dari `tensorflow.keras.applications.mobilenet_v2` mengubah piksel dari range [0,255] ke [-1,1]. Jika dipanggil dua kali (misal sekali di `ImageDataGenerator(preprocessing_function=preprocess_input)` DAN sekali lagi di dalam arsitektur model), nilai piksel akan rusak (mendekati -1 semua) dan model gagal belajar (val_accuracy macet di angka rendah/medioker, training look "noisy"/naik-turun tanpa konvergen). Pastikan `preprocess_input` HANYA dipanggil di satu tempat — paling aman di `ImageDataGenerator`.

**5.4 Tangani class imbalance dengan `class_weight`.**
Hitung jumlah sampel per kelas dari training set, hitung `class_weight` (formula: `total_samples / (n_classes * count_per_class)`), dan masukkan ke parameter `class_weight` saat `model.fit(...)`. Jangan training tanpa ini jika dataset tidak balance.

**5.5 Arsitektur model:**
```
Input (224x224x3)
  -> MobileNetV2(weights='imagenet', include_top=False, pooling='avg')  [freeze dulu]
  -> Dense(256, relu) -> Dropout(0.5)
  -> Dense(128, relu) -> Dropout(0.3)
  -> Dense(n_classes, softmax)
```
Training 2 fase:
- Fase 1: base model frozen (`trainable=False`), train dense layers saja, learning rate ~1e-3, beberapa epoch (gunakan EarlyStopping + ReduceLROnPlateau)
- Fase 2 (fine-tuning): unfreeze base model dari layer ke-120 ke atas, learning rate jauh lebih rendah (~1e-5), lanjutkan training dengan EarlyStopping

**5.6 Augmentasi data yang aman untuk citra medis:**
Gunakan rotation, width/height shift, zoom, shear. JANGAN gunakan `horizontal_flip`/`vertical_flip` jika orientasi anatomis penting untuk diagnosis (misal X-ray yang membedakan sisi kiri/kanan tubuh) — tapi BOLEH dipakai untuk citra yang tidak punya orientasi baku (misal USG tanpa makna arah). Tentukan ini berdasarkan konteks dataset yang diupload; jika ragu, defaultkan `horizontal_flip=False` untuk keamanan.

**5.7 Split data:** train/validation/test, stratified per kelas (jangan random split polos yang bisa bikin satu kelas minoritas hilang sama sekali dari validation/test set).

**5.8 Setelah training selesai, simpan:** file model (`.keras`), `class_names.json`, `history.json` (riwayat akurasi/loss tiap epoch), confusion matrix (image), classification report (precision/recall/f1 per kelas). Update `training_jobs` jadi status `completed` dengan hasil akurasi & loss final. Jika ada error di proses manapun, set status `failed` dan simpan pesan error ke kolom `error_message` (jangan biarkan job menggantung tanpa status jelas).

## 6. Endpoint FastAPI (AI Service) yang harus dibuat

```
POST /train
  - Body: multipart file (zip dataset)
  - Proses: validasi, ekstrak, training di background, return job_id segera
  - Response: {"job_id": "...", "status": "pending"}

GET /train/{job_id}/status
  - Response: {"status": "...", "current_epoch": N, "progress_percent": N, "accuracy": N, "loss": N}

POST /predict
  - Body: multipart image + model_id aktif (atau service ambil otomatis model yang is_active)
  - Response: {"predicted_class": "...", "confidence": N, "probabilities": {"kelas_a": N, "kelas_b": N, ...}}

GET /health
  - Response: {"status": "ok"}
```

## 7. Halaman & Fitur Web App (Laravel)

**Public (tanpa login):**
- `/` — landing page, jelaskan cara kerja sistem
- `/tes` — form upload gambar, tombol "Analisis", tampilkan hasil prediksi (kelas + confidence bar per kelas) setelah submit. Jika tidak ada model aktif, tampilkan pesan jelas "belum ada model aktif" — jangan biarkan user upload ke sistem yang belum siap.

**Admin (perlu login, route di-protect middleware auth):**
- `/admin/login` — form login
- `/admin/dashboard` — ringkasan: jumlah model, model yang aktif sekarang, jumlah prediksi yang sudah dilakukan user
- `/admin/models` — list semua model (nama, akurasi, status aktif/non-aktif), tombol "Aktifkan" per model
- `/admin/training` — form upload dataset ZIP baru + tombol "Mulai Training"
- `/admin/training/{job_id}` — halaman progress training real-time (polling status setiap 3-5 detik via JavaScript fetch, tampilkan progress bar + epoch saat ini), setelah selesai tampilkan tombol "Aktifkan Model Ini" + lihat confusion matrix & classification report
- `/admin/predictions` — riwayat semua prediksi yang dilakukan user (pagination)

## 8. Hal yang harus divalidasi/dihandle (non-functional)

- Validasi ukuran file upload (gambar maupun dataset ZIP) di sisi Laravel SEBELUM dikirim ke FastAPI
- Tangani kondisi: belum ada model aktif sama sekali (saat web pertama kali dijalankan)
- Tangani kondisi: training gagal di tengah jalan (jangan database/job menggantung status "training" selamanya)
- CORS dikonfigurasi benar antara Laravel <-> FastAPI
- Buat file `.env.example` dengan semua variable yang dibutuhkan (DB credentials, AI_SERVICE_URL, dll)
- Buat `README.md` berisi cara install & jalankan dari nol (migrate, seed, jalankan FastAPI, jalankan Laravel)

## 9. Output yang diharapkan dari kamu (AI agent)

1. Struktur folder project lengkap (Laravel + FastAPI sebagai 2 folder terpisah dalam 1 repo)
2. Semua migration, model, controller, route, view Laravel sesuai poin 7
3. Kode FastAPI lengkap sesuai poin 5 dan 6, termasuk logic training async-nya
4. README cara menjalankan
5. Setelah selesai, jelaskan secara singkat bagian mana yang masih perlu disesuaikan manual oleh saya (misal kredensial database, path penyimpanan model)

Kerjakan semua poin di atas secara berurutan dan menyeluruh. Jangan minta konfirmasi di tengah jalan kecuali ada keputusan yang benar-benar ambigu dan berdampak besar pada arsitektur.
