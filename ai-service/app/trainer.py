import os
import json
import shutil
import zipfile
import threading
import uuid
from datetime import datetime

import requests

import numpy as np
import matplotlib
matplotlib.use("Agg")
import matplotlib.pyplot as plt
from sklearn.metrics import (
    confusion_matrix, classification_report,
    precision_score, recall_score, f1_score, accuracy_score
)
from sklearn.utils.class_weight import compute_class_weight
import seaborn as sns

import tensorflow as tf
from tensorflow.keras.applications import MobileNetV2
from tensorflow.keras.applications.mobilenet_v2 import preprocess_input
from tensorflow.keras.preprocessing.image import ImageDataGenerator
from tensorflow.keras.models import Model
from tensorflow.keras.layers import Dense
from tensorflow.keras.optimizers import Adam

from app.config import (
    DATASETS_DIR, MODELS_DIR, VALID_EXTENSIONS, MASK_KEYWORDS,
    MIN_IMAGES_PER_CLASS, WARNING_IMAGES_PER_CLASS, IMG_SIZE, RANDOM_SEED, MAX_DATASET_SIZE_MB
)


class SparseFocalLoss(tf.keras.losses.Loss):
    def __init__(self, gamma=2.0, alpha=0.25, from_logits=False, **kwargs):
        super().__init__(**kwargs)
        self.gamma = gamma
        self.alpha = alpha
        self.from_logits = from_logits

    def call(self, y_true, y_pred):
        epsilon = tf.keras.backend.epsilon()
        if self.from_logits:
            y_pred = tf.nn.softmax(y_pred, axis=-1)
        y_pred = tf.clip_by_value(y_pred, epsilon, 1.0 - epsilon)
        y_true = tf.cast(y_true, tf.int32)
        y_true_one_hot = tf.one_hot(y_true, tf.shape(y_pred)[-1])
        cross_entropy = -y_true_one_hot * tf.math.log(y_pred)
        focal_weight = tf.pow(1.0 - y_pred, self.gamma) * y_true_one_hot
        focal_loss = self.alpha * focal_weight * cross_entropy
        return tf.reduce_sum(focal_loss, axis=-1)

    def get_config(self):
        config = super().get_config()
        config.update({"gamma": self.gamma, "alpha": self.alpha, "from_logits": self.from_logits})
        return config

jobs = {}

def get_job(job_id):
    return jobs.get(job_id)

def _update_job(job_id, **kwargs):
    if job_id in jobs:
        jobs[job_id].update(kwargs)

def cancel_training(job_id):
    if job_id not in jobs:
        return False
    job = jobs[job_id]
    if job.get("status") in ("training", "pending", "validating", "extracting"):
        job["cancel_requested"] = True
        _log(job_id, "Cancel requested by user.")
        return True
    return False

def _log(job_id, message):
    if job_id in jobs:
        jobs[job_id]["log"] = jobs[job_id].get("log", "") + f"[{datetime.now().isoformat()}] {message}\n"

def _validate_zip(zip_path):
    total_size = 0
    with zipfile.ZipFile(zip_path, "r") as zf:
        for info in zf.infolist():
            total_size += info.file_size
            if ".." in info.filename or info.filename.startswith("/"):
                raise ValueError(f"Path traversal detected: {info.filename}")
    total_size_mb = total_size / (1024 * 1024)
    if total_size_mb > MAX_DATASET_SIZE_MB:
        raise ValueError(f"Dataset too large: {total_size_mb:.1f}MB (max {MAX_DATASET_SIZE_MB}MB)")

def _extract_zip(zip_path, extract_to):
    if os.path.exists(extract_to):
        shutil.rmtree(extract_to)
    os.makedirs(extract_to, exist_ok=True)
    with zipfile.ZipFile(zip_path, "r") as zf:
        zf.extractall(extract_to)
    items = os.listdir(extract_to)
    if len(items) == 1 and os.path.isdir(os.path.join(extract_to, items[0])):
        inner = os.path.join(extract_to, items[0])
        for f in os.listdir(inner):
            shutil.move(os.path.join(inner, f), os.path.join(extract_to, f))
        os.rmdir(inner)

def _validate_dataset(job_id, dataset_path):
    _update_job(job_id, status="validating")
    classes = sorted([d for d in os.listdir(dataset_path) if os.path.isdir(os.path.join(dataset_path, d))])
    if len(classes) < 2:
        raise ValueError(f"Minimal 2 folder kelas, ditemukan {len(classes)}")
    _log(job_id, f"Ditemukan {len(classes)} kelas: {classes}")
    class_counts = {}
    skipped_counts = {}
    for cls in classes:
        cls_path = os.path.join(dataset_path, cls)
        all_files = sorted(os.listdir(cls_path))
        valid_files = []
        skipped = 0
        for f in all_files:
            ext = os.path.splitext(f)[1].lower()
            if ext not in VALID_EXTENSIONS:
                skipped += 1
                continue
            f_lower = f.lower()
            if any(kw in f_lower for kw in MASK_KEYWORDS):
                skipped += 1
                continue
            valid_files.append(f)
        skipped_counts[cls] = skipped
        class_counts[cls] = valid_files
        count = len(valid_files)
        if count < MIN_IMAGES_PER_CLASS:
            raise ValueError(f"Kelas '{cls}' hanya punya {count} gambar (min {MIN_IMAGES_PER_CLASS})")
        warning = f" (WARNING: <{WARNING_IMAGES_PER_CLASS})" if count < WARNING_IMAGES_PER_CLASS else ""
        _log(job_id, f"  Kelas '{cls}': {count} gambar{warning}")
        if skipped > 0:
            _log(job_id, f"    -> {skipped} file di-skip (ekstensi invalid / nama mask/label/annotation/gt)")
        for f in valid_files:
            src = os.path.join(cls_path, f)
            dst = os.path.join(cls_path, f)
    total_skipped = sum(skipped_counts.values())
    if total_skipped > 0:
        _log(job_id, f"Total file di-skip: {total_skipped}")
    total = sum(len(v) for v in class_counts.values())
    _log(job_id, f"Total gambar valid: {total}")
    return classes, class_counts

class ProgressCallback(tf.keras.callbacks.Callback):
    def __init__(self, job_id, epoch_offset=0):
        super().__init__()
        self.job_id = job_id
        self.epoch_offset = epoch_offset

    def on_epoch_end(self, epoch, logs=None):
        job = jobs.get(self.job_id)
        if job and job.get("cancel_requested"):
            self.model.stop_training = True
            _log(self.job_id, "Training cancelled by user.")
            return
        logs = logs or {}
        display_epoch = epoch + 1 + self.epoch_offset
        _update_job(self.job_id, current_epoch=display_epoch)
        ep = {
            "epoch": display_epoch,
            "accuracy": float(logs.get("accuracy", 0)),
            "loss": float(logs.get("loss", 0)),
            "val_accuracy": float(logs.get("val_accuracy", 0)),
            "val_loss": float(logs.get("val_loss", 0)),
        }
        if self.job_id in jobs:
            jobs[self.job_id].setdefault("epochs", []).append(ep)
        _log(self.job_id,
             f"Epoch {ep['epoch']}: "
             f"acc={ep['accuracy']:.4f} | val_acc={ep['val_accuracy']:.4f} | "
             f"loss={ep['loss']:.4f} | val_loss={ep['val_loss']:.4f}")

def _evaluate_metrics(model, val_gen, class_names):
    val_gen.reset()
    y_true = val_gen.classes
    y_pred = model.predict(val_gen, verbose=0)
    y_pred_classes = np.argmax(y_pred, axis=1)
    acc = accuracy_score(y_true, y_pred_classes)
    prec = precision_score(y_true, y_pred_classes, average='macro', zero_division=0)
    rec = recall_score(y_true, y_pred_classes, average='macro', zero_division=0)
    f1 = f1_score(y_true, y_pred_classes, average='macro', zero_division=0)
    return {
        "accuracy": float(acc),
        "precision": float(prec),
        "recall": float(rec),
        "f1_score": float(f1),
    }, y_pred_classes

def _compute_class_weights(train_gen, class_names):
    class_counts = np.bincount(train_gen.classes)
    total = len(train_gen.classes)
    n_classes = len(class_names)
    weights = total / (n_classes * class_counts.astype(float))
    class_weight_dict = {i: float(w) for i, w in enumerate(weights)}
    return class_weight_dict

def _run_training(job_id, dataset_path, classes, cfg):
    try:
        epochs = cfg.get("epochs", 10)
        val_split = cfg.get("validation_split", 0.3)
        batch_size = cfg.get("batch_size", 32)
        image_size = cfg.get("image_size", 224)
        lr = cfg.get("learning_rate", 0.0001)
        img_size = (image_size, image_size)

        _update_job(job_id, status="training", current_epoch=0, total_epoch=epochs, progress_percent=0)
        _log(job_id, "Memulai training...")
        class_names = sorted(classes)
        n_classes = len(class_names)
        _log(job_id, f"Kelas: {class_names}")
        for cls in classes:
            cls_path = os.path.join(dataset_path, cls)
            files = [f for f in os.listdir(cls_path) if f.endswith((".jpg", ".jpeg", ".png"))]
            _log(job_id, f"  Kelas '{cls}': {len(files)} gambar")

        train_datagen = ImageDataGenerator(
            preprocessing_function=preprocess_input,
            rotation_range=20,
            width_shift_range=0.15,
            height_shift_range=0.15,
            shear_range=0.15,
            zoom_range=0.15,
            brightness_range=(0.8, 1.2),
            horizontal_flip=True,
            fill_mode="nearest",
            validation_split=val_split
        )
        val_datagen = ImageDataGenerator(
            preprocessing_function=preprocess_input,
            validation_split=val_split
        )
        train_gen = train_datagen.flow_from_directory(
            dataset_path, target_size=img_size, batch_size=batch_size,
            class_mode="sparse", classes=class_names,
            subset="training", seed=RANDOM_SEED, shuffle=True
        )
        val_gen = val_datagen.flow_from_directory(
            dataset_path, target_size=img_size, batch_size=batch_size,
            class_mode="sparse", classes=class_names,
            subset="validation", seed=RANDOM_SEED, shuffle=False
        )
        n_train = train_gen.samples
        n_val = val_gen.samples
        ft_epochs = max(epochs // 2, 5)
        total_epochs_combined = epochs + ft_epochs
        _log(job_id, f"Train samples: {n_train}, Validation samples: {n_val}")
        _update_job(job_id, total_epoch=total_epochs_combined)

        class_weight_dict = _compute_class_weights(train_gen, class_names)
        _log(job_id, f"Class weights: {class_weight_dict}")
        _log(job_id, " Focal Loss aktif: gamma=2.0, alpha=0.25 — fokus pada kelas malignant.")

        base_model = MobileNetV2(
            weights="imagenet", include_top=False, pooling="avg",
            input_shape=(image_size, image_size, 3)
        )
        base_model.trainable = False
        x = base_model.output
        x = Dense(256, activation="relu")(x)
        x = tf.keras.layers.Dropout(0.5)(x)
        x = Dense(128, activation="relu")(x)
        x = tf.keras.layers.Dropout(0.3)(x)
        outputs = Dense(n_classes, activation="softmax")(x)
        model = Model(inputs=base_model.input, outputs=outputs)
        model.compile(
            optimizer=Adam(learning_rate=lr),
            loss=SparseFocalLoss(gamma=2.0, alpha=0.25),
            metrics=["accuracy"]
        )
        _log(job_id, "Model: MobileNetV2 (frozen) + Dense(256,ReLU)+Drop(0.5)+Dense(128,ReLU)+Drop(0.3)+Dense(n,softmax)")
        _log(job_id, "Fase 1: training classifier head (base frozen)...")
        prog_cb = ProgressCallback(job_id)
        history = model.fit(
            train_gen, epochs=epochs, validation_data=val_gen,
            class_weight=class_weight_dict,
            callbacks=[prog_cb], verbose=0
        )
        total_epochs_done = len(history.history["loss"])

        if not jobs.get(job_id, {}).get("cancel_requested"):
            _log(job_id, "Fase 2: fine-tuning (unfreeze layer 120+, lr={:.6f})...".format(lr * 0.1))
            base_model.trainable = True
            for layer in base_model.layers[:120]:
                layer.trainable = False
            model.compile(
                optimizer=Adam(learning_rate=lr * 0.1),
                loss=SparseFocalLoss(gamma=2.0, alpha=0.25),
                metrics=["accuracy"]
            )
            ft_prog_cb = ProgressCallback(job_id, epoch_offset=total_epochs_done)
            history_ft = model.fit(
                train_gen, epochs=ft_epochs, validation_data=val_gen,
                class_weight=class_weight_dict,
                callbacks=[ft_prog_cb], verbose=0
            )
            for k, v in history_ft.history.items():
                history.history.setdefault(k, []).extend(v)
            total_epochs_done += len(history_ft.history["loss"])
            _log(job_id, f"Fine-tuning selesai: +{len(history_ft.history['loss'])} epoch")
        if jobs.get(job_id, {}).get("cancel_requested"):
            _update_job(job_id, status="cancelled", progress_percent=0)
            _log(job_id, "Training dibatalkan.")
            return
        val_acc = history.history["val_accuracy"][-1]
        val_loss = history.history["val_loss"][-1]
        _log(job_id, f"Training selesai: {total_epochs_done} epoch, val_acc={val_acc:.4f}, val_loss={val_loss:.4f}")

        metrics_dict, y_pred_classes = _evaluate_metrics(model, val_gen, class_names)
        _log(job_id, f"Evaluasi: accuracy={metrics_dict['accuracy']*100:.2f}%, precision={metrics_dict['precision']*100:.2f}%, recall={metrics_dict['recall']*100:.2f}%, f1={metrics_dict['f1_score']*100:.2f}%")

        model_id = str(uuid.uuid4())[:8]
        model_filename = f"model_{model_id}.keras"
        model_path = os.path.join(MODELS_DIR, model_filename)
        model.save(model_path)
        _log(job_id, f"Model saved: {model_path}")

        history_path = os.path.join(MODELS_DIR, f"history_{model_id}.json")
        history_serializable = {k: [float(v) for v in vals] for k, vals in history.history.items()}
        with open(history_path, "w") as f:
            json.dump(history_serializable, f)
        class_names_path = os.path.join(MODELS_DIR, f"class_names_{model_id}.json")
        with open(class_names_path, "w") as f:
            json.dump(class_names, f)
        metrics_path = os.path.join(MODELS_DIR, f"metrics_{model_id}.json")
        with open(metrics_path, "w") as f:
            json.dump(metrics_dict, f, indent=2)

        config_path = os.path.join(MODELS_DIR, f"config_{model_id}.json")
        with open(config_path, "w") as f:
            json.dump(cfg, f, indent=2)

        cm_filename = f"confusion_matrix_{model_id}.png"
        cm_path = os.path.join(MODELS_DIR, cm_filename)
        _generate_confusion_matrix(model, val_gen, class_names, cm_path)
        report_filename = f"classification_report_{model_id}.json"
        report_path = os.path.join(MODELS_DIR, report_filename)
        _generate_classification_report(model, val_gen, class_names, report_path)
        _log(job_id, "Confusion matrix & classification report & metrics saved.")

        _update_job(
            job_id,
            status="completed",
            progress_percent=100,
            current_epoch=total_epochs_done,
            total_epoch=total_epochs_done,
            accuracy_result=float(metrics_dict["accuracy"]),
            loss_result=float(val_loss),
            precision_result=float(metrics_dict["precision"]),
            recall_result=float(metrics_dict["recall"]),
            f1_score_result=float(metrics_dict["f1_score"]),
            model_id=model_id,
            model_path=model_filename,
            class_names=class_names,
            history_path=history_path,
            confusion_matrix_path=cm_filename,
            classification_report_path=report_filename,
            metrics_path=metrics_path,
            config_path=config_path,
        )
        _log(job_id, "Training selesai!")
        try:
            laravel_url = os.environ.get(
                "LARAVEL_WEBHOOK_URL",
                "http://localhost:8080/api/training/webhook"
            )
            job_data = jobs.get(job_id, {})
            epoch_history = job_data.get("epochs", [])
            current_epoch = job_data.get("current_epoch", 0)
            total_epoch = job_data.get("total_epoch", 0)
            resp = requests.post(laravel_url, json={
                "job_id": job_id,
                "status": "Completed",
                "accuracy": float(metrics_dict["accuracy"]),
                "loss": float(val_loss),
                "precision": float(metrics_dict["precision"]),
                "recall": float(metrics_dict["recall"]),
                "f1_score": float(metrics_dict["f1_score"]),
                "epoch_history": epoch_history,
                "current_epoch": current_epoch,
                "total_epoch": total_epoch,
                "model_id": job_data.get("model_id", ""),
            }, timeout=5)
            if resp.ok:
                _log(job_id, f"Notifikasi ke Laravel: HTTP {resp.status_code} OK")
            else:
                _log(job_id, f"Notifikasi ke Laravel: HTTP {resp.status_code} — {resp.text[:200]}")
        except Exception as webhook_err:
            _log(job_id, f"Gagal mengirim notifikasi ke Laravel: {webhook_err}")
            _log(job_id, f"Laravel webhook URL: {laravel_url}")
    except Exception as e:
        _update_job(job_id, status="failed", error_message=str(e))
        _log(job_id, f"ERROR: {str(e)}")

def _generate_confusion_matrix(model, val_gen, class_names, save_path):
    val_gen.reset()
    y_true = val_gen.classes
    y_pred = model.predict(val_gen, verbose=0)
    y_pred_classes = np.argmax(y_pred, axis=1)
    cm = confusion_matrix(y_true, y_pred_classes)
    plt.figure(figsize=(10, 8))
    sns.heatmap(cm, annot=True, fmt="d", cmap="Blues", xticklabels=class_names, yticklabels=class_names)
    plt.title("Confusion Matrix")
    plt.ylabel("True Label")
    plt.xlabel("Predicted Label")
    plt.xticks(rotation=45, ha='right')
    plt.yticks(rotation=0)
    plt.tight_layout()
    plt.savefig(save_path)
    plt.close()

    cm_data_path = save_path.replace(".png", ".json")
    with open(cm_data_path, "w") as f:
        json.dump({
            "matrix": cm.tolist(),
            "class_names": class_names,
        }, f, indent=2)

def _generate_classification_report(model, val_gen, class_names, save_path):
    val_gen.reset()
    y_true = val_gen.classes
    y_pred = model.predict(val_gen, verbose=0)
    y_pred_classes = np.argmax(y_pred, axis=1)
    report = classification_report(y_true, y_pred_classes, target_names=class_names, output_dict=True)
    with open(save_path, "w") as f:
        json.dump(report, f, indent=2)

def start_training(zip_path, epochs=10, validation_split=0.3, batch_size=32, image_size=224, learning_rate=0.0001, dataset_filename=None):
    job_id = str(uuid.uuid4())
    dataset_dir = os.path.join(DATASETS_DIR, job_id)
    if not dataset_filename:
        zip_basename = os.path.basename(zip_path)
        dataset_filename = zip_basename.split('_', 1)[1] if '_' in zip_basename else zip_basename
    jobs[job_id] = {
        "id": job_id,
        "status": "pending",
        "current_epoch": 0,
        "total_epoch": epochs,
        "progress_percent": 0,
        "accuracy_result": None,
        "loss_result": None,
        "precision_result": None,
        "recall_result": None,
        "f1_score_result": None,
        "error_message": None,
        "log": "",
        "model_id": None,
        "model_path": None,
        "class_names": None,
        "history_path": None,
        "confusion_matrix_path": None,
        "classification_report_path": None,
        "metrics_path": None,
        "config_path": None,
        "epochs": [],
        "cancel_requested": False,
        "config": {
            "dataset_filename": dataset_filename,
            "epochs": epochs,
            "validation_split": validation_split,
            "batch_size": batch_size,
            "image_size": image_size,
            "learning_rate": learning_rate,
        },
    }
    thread = threading.Thread(target=_training_workflow, args=(job_id, zip_path, dataset_dir), daemon=True)
    thread.start()
    return job_id

def _training_workflow(job_id, zip_path, dataset_dir):
    try:
        _log(job_id, "Validating ZIP file...")
        _validate_zip(zip_path)
        _log(job_id, "Extracting dataset...")
        _update_job(job_id, status="extracting")
        _extract_zip(zip_path, dataset_dir)
        _log(job_id, f"Dataset extracted to {dataset_dir}")
        classes, class_counts = _validate_dataset(job_id, dataset_dir)
        _log(job_id, f"Validasi dataset berhasil: {classes}")
        cfg = jobs.get(job_id, {}).get("config", {})
        _run_training(job_id, dataset_dir, classes, cfg)
    except Exception as e:
        _update_job(job_id, status="failed", error_message=str(e))
        _log(job_id, f"FATAL ERROR: {str(e)}")
