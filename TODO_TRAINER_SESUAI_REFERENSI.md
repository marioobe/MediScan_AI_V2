# TODO: Penyesuaian Trainer agar Sesuai Referensi Pak Budi (abaikan SVM)

## Prinsip: MobileNetV2 sebagai Fixed Feature Extractor (fully frozen)

### Perubahan pada `ai-service/app/trainer.py`:

- [x] **1. Hapus 2 fase training** (frozen + fine-tune) → ganti dengan 1 fase saja
- [x] **2. MobileNetV2 fully frozen** — tidak boleh ada `base_model.trainable = True`
- [x] **3. Tidak ada augmentasi data** — hapus `RandomFlip`, `RandomRotation`, `RandomZoom`
- [x] **4. Ubah `class_mode` dari `'categorical'` → `'sparse'`** (integer labels)
- [x] **5. Ubah `shuffle` training dari `True` → `False`**
- [x] **6. Model baru: cukup `GlobalAveragePooling2D` → `Dense(n_classes, activation='softmax')`**
- [x] **7. Compile dengan `loss='sparse_categorical_crossentropy'`** (karena sparse labels)
- [x] **8. Hapus `class_weight`** (atau sesuaikan untuk sparse)
- [x] **9. Hapus `ProgressCallback` 2 fase → cukup 1 callback**
- [x] **10. Hapus `EarlyStopping` dan `ReduceLROnPlateau`** (ikuti referensi)
- [x] **11. Evaluasi akhir seperti referensi:** accuracy, precision, recall, f1-score
- [x] **12. Simpan metrik evaluasi ke file JSON**

### Sinkronisasi Metrik ke Laravel (Webhook):
- [x] **13. Kirim precision, recall, f1_score ke webhook Laravel** (`AdminTrainingController@webhook`)
- [x] **14. Migration DB**: tambah kolom `precision_result`, `recall_result`, `f1_score_result` ke `training_jobs`
- [x] **15. Tampilkan 3 metrik baru** di halaman `training-progress.blade.php`

### Tidak perlu diubah:
- [x] ~`pooling='avg'`~ tetap sama
- [x] ~`input_shape`~ tetap configurable via parameter
- [x] ~Confusion matrix & classification report~ sudah ada
- [x] ~Struktur validasi dataset~ sudah sesuai

---

**Mulai eksekusi setelah ada perintah dari user.**
