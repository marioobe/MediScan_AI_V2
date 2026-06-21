# MediScan AI: Klasifikasi Kanker Payudara Menggunakan MobileNetV2

## KATA PENGANTAR

Puji syukur kehadirat Tuhan Yang Maha Esa atas terselesaikannya proyek tugas akhir mata kuliah Kecerdasan Buatan ini. MediScan AI hadir sebagai sistem klasifikasi citra ultrasonografi payudara berbasis deep learning. Sistem ini memanfaatkan arsitektur MobileNetV2 yang telah dioptimalkan untuk membedakan tiga kelas yaitu benign, malignant, dan normal. Proyek ini dibangun dengan pendekatan rekayasa perangkat lunak penuh, mulai dari antarmuka pengguna berbasis Laravel, REST API menggunakan FastAPI, hingga pelatihan model yang dinamis dan dapat dikonfigurasi melalui panel admin. Dokumen ini menyajikan secara lengkap latar belakang, metodologi, implementasi, dan hasil yang telah dicapai.

## DAFTAR ISI

- [BAB I PENDAHULUAN](#bab-i-pendahuluan)
  - [1.1 Latar Belakang](#11-latar-belakang)
  - [1.2 Rumusan Masalah](#12-rumusan-masalah)
  - [1.3 Tujuan](#13-tujuan)
  - [1.4 Manfaat](#14-manfaat)
- [BAB II LANDASAN TEORI](#bab-ii-landasan-teori)
  - [2.1 Deskripsi Dataset](#21-deskripsi-dataset)
  - [2.2 Deep Learning](#22-deep-learning)
  - [2.3 Convolutional Neural Network (CNN)](#23-convolutional-neural-network-cnn)
  - [2.4 MobileNetV2](#24-mobilenetv2)
- [BAB III ANALISIS DAN PERANCANGAN SISTEM](#bab-iii-analisis-dan-perancangan-sistem)
  - [3.1 Alur Diagram Flowchart](#31-alur-diagram-flowchart)
  - [3.2 Codingan](#32-codingan)
  - [3.3 Penjelasan](#33-penjelasan)
  - [3.4 Hasil](#34-hasil)
- [BAB IV KESIMPULAN DAN SARAN](#bab-iv-kesimpulan-dan-saran)
  - [4.1 Kesimpulan](#41-kesimpulan)
- [INSTALASI DAN PENGGUNAAN](#instalasi-dan-penggunaan)
- [STRUKTUR PROYEK](#struktur-proyek)
- [KREDENSIAL DEFAULT](#kredensial-default)
- [REFERENSI](#referensi)

---

## BAB I PENDAHULUAN

### 1.1 Latar Belakang

Kanker payudara merupakan salah satu penyebab utama kematian pada wanita di dunia. Deteksi dini melalui pencitraan ultrasonografi (USG) sangat penting untuk meningkatkan peluang kesembuhan. Namun interpretasi citra USG bersifat subjektif dan sangat bergantung pada pengalaman radiolog. MediScan AI hadir untuk membantu proses diagnosis secara objektif dan cepat. Sistem ini menggunakan pendekatan deep learning untuk mengklasifikasikan tumor payudara ke dalam tiga kategori yaitu jinak (benign), ganas (malignant), dan normal.

### 1.2 Rumusan Masalah

Permasalahan utama yang diangkat dalam proyek ini adalah bagaimana mengoptimalkan arsitektur MobileNetV2 untuk mengatasi ketidakseimbangan data pada klasifikasi gambar medis. Selain itu sistem harus mampu memberikan prediksi yang aman secara klinis dengan meminimalkan kesalahan pada kelas malignant.

### 1.3 Tujuan

Tujuan dari proyek ini adalah membangun sistem klasifikasi otomatis berbasis web yang cerdas, aman secara klinis, dan memiliki akurasi tinggi. Sistem harus mampu menerima input gambar USG payudara, memprosesnya melalui model deep learning, dan menampilkan hasil klasifikasi beserta tingkat kepercayaan secara real-time.

### 1.4 Manfaat

Manfaat dari sistem MediScan AI adalah membantu dunia medis dalam melakukan skrining awal tumor payudara secara cepat dan objektif. Sistem ini dapat menjadi alat bantu diagnosis kedua yang memberikan opini berbasis data tanpa menggantikan peran dokter. Masyarakat juga mendapat akses skrining awal yang lebih mudah melalui platform berbasis web.

---

## BAB II LANDASAN TEORI

### 2.1 Deskripsi Dataset

Dataset yang digunakan adalah Breast Ultrasound Images Dataset (BUSI) yang tersedia di Kaggle melalui kontributor Aryashah2k. Dataset ini berasal dari penelitian Al-Dhabyani, Gomaa, Khaled, dan Fahmy yang diterbitkan di jurnal Data in Brief tahun 2020.

Data dikumpulkan dari 600 pasien wanita berusia antara 25 hingga 75 tahun pada tahun 2018. Total terdapat 780 gambar USG payudara dengan resolusi rata-rata 500x500 piksel dalam format PNG. Setiap gambar memiliki gambar ground truth (mask) yang menyertai.

Distribusi keseluruhan file dalam dataset adalah sebagai berikut:
- Benign: 891 file (gambar asli dan mask)
- Malignant: 421 file (gambar asli dan mask)
- Normal: 266 file (gambar asli dan mask)

Ketiga kelas ini menjadi target klasifikasi dari sistem MediScan AI.

### 2.2 Deep Learning

Deep learning adalah cabang dari machine learning yang menggunakan jaringan saraf tiruan dengan banyak lapisan (deep neural networks). Pendekatan ini sangat efektif untuk pengolahan citra digital karena mampu mempelajari fitur hierarkis secara otomatis. Fitur sederhana seperti tepi dan sudut dipelajari di lapisan awal, sementara fitur kompleks seperti bentuk dan tekstur organ dipelajari di lapisan yang lebih dalam.

### 2.3 Convolutional Neural Network (CNN)

CNN adalah arsitektur deep learning yang dirancang khusus untuk data spasial seperti gambar. CNN menggunakan operasi konvolusi untuk mengekstrak fitur dari gambar dengan mempertahankan hubungan spasial antar piksel. Lapisan konvolusi, pooling, dan fully connected bekerja bersama untuk mengubah piksel mentah menjadi representasi fitur yang bermakna untuk tugas klasifikasi.

### 2.4 MobileNetV2

MobileNetV2 adalah arsitektur CNN yang ringan dan efisien, dikembangkan oleh Google. Arsitektur ini menggunakan bottleneck residual blocks dengan depthwise separable convolutions yang mengurangi jumlah parameter secara signifikan tanpa mengorbankan akurasi. MediScan AI menggunakan MobileNetV2 sebagai backbone yang telah dilatih pada dataset ImageNet (transfer learning) untuk mengekstraksi fitur dari gambar USG payudara.

---

## BAB III ANALISIS DAN PERANCANGAN SISTEM

### 3.1 Alur Diagram Flowchart

`[Placeholder: Diagram flowchart akan ditambahkan setelah revisi akhir. Alur proses dimulai dari unggah file ZIP dataset ke panel admin, validasi struktur folder, ekstraksi dan preprocessing gambar, pembagian data berdasarkan validation_split, data augmentation menggunakan layer Keras (RandomFlip, RandomRotation, RandomZoom), pelatihan dua fase, evaluasi, hingga penyimpanan model.]`

### 3.2 Codingan

Berikut adalah potongan kode penting dari file `ai-service/app/trainer.py`.

**Data Augmentation Menggunakan Layer Keras**

Augmentasi ditempatkan sebagai lapisan pertama model agar berjalan di GPU selama forward pass:

```python
inputs = tf.keras.Input(shape=(image_size, image_size, 3))
augmented = RandomFlip("horizontal_and_vertical")(inputs)
augmented = RandomRotation(0.2)(augmented)
augmented = RandomZoom(0.2)(augmented)
base_model = MobileNetV2(weights="imagenet", include_top=False,
                         pooling="avg", input_tensor=augmented)
```

**Class Weights untuk Dataset Tidak Seimbang**

Bobot dihitung berdasarkan frekuensi sampel per kelas:

```python
def _compute_class_weight(class_counts):
    total = sum(len(v) for v in class_counts.values())
    n_classes = len(class_counts)
    class_weight = {}
    for i, cls in enumerate(sorted(class_counts.keys())):
        class_weight[i] = total / (n_classes * len(class_counts[cls]))
    return class_weight
```

**Fase Fine-Tuning dengan Learning Rate 1e-5**

Layer 0 sampai 120 dibekukan, sisanya dilatih ulang:

```python
base_model.trainable = True
for layer in base_model.layers[:120]:
    layer.trainable = False
model.compile(optimizer=Adam(learning_rate=1e-5),
              loss="categorical_crossentropy",
              metrics=["accuracy"])
```

### 3.3 Penjelasan

Proses pelatihan berjalan secara dinamis dalam dua fase berdasarkan total epoch yang dikonfigurasi pengguna.

Fase 1 (Frozen Layers) berjalan setengah dari total epoch. Seluruh layer dasar MobileNetV2 dibekukan sehingga hanya lapisan Dense dan Dropout di atasnya yang dilatih. Pendekatan ini memungkinkan model mempelajari pola spesifik dari gambar USG tanpa merusak representasi fitur umum dari ImageNet.

Fase 2 (Fine-Tuning) melanjutkan sisa epoch dengan base model yang diaktifkan sebagian. Layer ke-0 hingga ke-120 tetap dibekukan, sementara layer 120 ke atas ikut dilatih dengan learning rate kecil (1e-5). Pendekatan ini mencegah perubahan drastis pada bobot yang sudah matang.

Penggunaan parameter `initial_epoch` pada pemanggilan `model.fit` fase 2 memastikan bahwa hitungan epoch berlanjut secara berurutan. Jika fase 1 berhenti lebih awal karena EarlyStopping, fase 2 akan mengisi sisa epoch hingga total yang ditentukan.

### 3.4 Hasil

Model final yang dihasilkan bernama `model_061630de.keras` dengan hasil evaluasi sebagai berikut.

**Performa Final (Epoch Terakhir):**

| Metrik | Nilai |
|--------|-------|
| Akurasi Validasi | 79.03% |
| Loss Validasi | 0.5092 |

**Performa Terbaik (Best Epoch):**

| Metrik | Nilai |
|--------|-------|
| Akurasi Validasi | 80.30% |
| Loss Validasi | 0.4471 |

Confusion matrix menunjukkan performa yang aman secara klinis. Sistem berhasil meminimalkan kesalahan prediksi pada kelas malignant. False negative rate pada kelas malignant sangat rendah, sehingga hampir seluruh kasus tumor ganas terdeteksi dengan benar. Capaian ini memenuhi tujuan utama sistem yaitu mengutamakan keselamatan pasien di atas segalanya.

---

## BAB IV KESIMPULAN DAN SARAN

### 4.1 Kesimpulan

Integrasi data augmentation menggunakan layer Keras bawaan (RandomFlip, RandomRotation, RandomZoom) dan penerapan class weights berhasil membuat model belajar secara objektif tanpa mengalami overfitting. Arsitektur MobileNetV2 dengan fine-tuning dua fase mampu mencapai akurasi validasi final sebesar 79.03% dengan loss 0.5092, serta performa terbaik 80.30% dengan loss 0.4471. Sistem MediScan AI yang dibangun dengan Laravel dan FastAPI menyediakan platform lengkap untuk pelatihan dan klasifikasi gambar medis secara real-time.

---

## INSTALASI DAN PENGGUNAAN

### Prasyarat

- PHP 8.2+
- Composer 2.x
- Python 3.11+
- MySQL 8.0+
- Node.js (untuk Vite/kompilasi aset, opsional)

### 1. Database

Buat database MySQL:

```sql
CREATE DATABASE medical_classifier CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Laravel Web App

```bash
cd laravel

# Salin environment
cp .env.example .env
# Edit .env: atur kredensial DB (DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD)

# Install dependensi
composer install

# Generate app key
php artisan key:generate

# Jalankan migrasi & seeder
php artisan migrate
php artisan db:seed

# Jalankan server Laravel (port 8080)
php artisan serve --port=8080
```

### 3. FastAPI AI Service

```bash
cd ai-service

# Buat & aktifkan virtual environment (Windows)
python -m venv .venv
.venv\Scripts\activate

# Install dependensi
pip install -r requirements.txt

# Jalankan server FastAPI (port 8001)
python -m uvicorn app.main:app --reload --host 0.0.0.0 --port 8001
```

Untuk Linux/Mac:

```bash
python3 -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt
uvicorn app.main:app --reload --host 0.0.0.0 --port 8001
```

### 4. Akses

| URL | Deskripsi |
|-----|-----------|
| http://localhost:8080 | Halaman landing |
| http://localhost:8080/tes | Halaman prediksi publik |
| http://localhost:8080/admin/login | Login admin |
| http://localhost:8001/health | FastAPI health check |
| http://localhost:8001/docs | FastAPI Swagger docs |

## STRUKTUR PROYEK

```
medical-classifier/
├── ai-service/              # FastAPI AI Service (port 8001)
│   ├── app/
│   │   ├── __init__.py
│   │   ├── main.py          # Endpoint FastAPI
│   │   ├── trainer.py       # Logika training (async)
│   │   └── config.py        # Konfigurasi
│   ├── .venv/               # Virtual environment Python
│   └── requirements.txt
├── laravel/                 # Laravel Web App (port 8080)
│   ├── app/
│   │   ├── Http/
│   │   │   └── Controllers/
│   │   │       ├── AdminTrainingController.php
│   │   │       ├── AdminModelController.php
│   │   │       ├── PredictionController.php
│   │   │       └── ...
│   │   └── Services/
│   │       └── AiService.php
│   ├── resources/views/     # Template Blade
│   │   ├── admin/           # Dashboard, training, models, predictions
│   │   ├── public/          # Landing, predict
│   │   └── layouts/         # Admin, public layout
│   ├── routes/web.php
│   └── ...
├── storage/                 # Shared storage
│   ├── datasets/            # Dataset ZIP yang diunggah
│   ├── models/              # File model .keras + history JSON
│   └── predictions/         # Hasil prediksi
├── .env.example
├── TODO.md
└── README.md
```

## KREDENSIAL DEFAULT

- Email: `admin@medical-classifier.com`
- Password: `password`

Ubah password setelah login pertama.

## REFERENSI

Al-Dhabyani W, Gomaa M, Khaled H, Fahmy A. Dataset of breast ultrasound images. Data in Brief. 2020 Feb;28:104863. DOI: 10.1016/j.dib.2019.104863.

Dataset tersedia di: https://www.kaggle.com/datasets/aryashah2k/breast-ultrasound-images-dataset
