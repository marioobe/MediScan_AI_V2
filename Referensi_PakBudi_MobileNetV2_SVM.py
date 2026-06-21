import numpy as np
import tensorflow as tf
from tensorflow.keras.preprocessing.image import ImageDataGenerator
from tensorflow.keras.applications import MobileNetV2
from tensorflow.keras.applications.mobilenet_v2 import preprocess_input
from sklearn.svm import SVC
from sklearn.metrics import (
    confusion_matrix, classification_report, precision_score,
    recall_score, f1_score, accuracy_score, ConfusionMatrixDisplay
)
import matplotlib.pyplot as plt
import seaborn as sns
import time
import joblib
import os


# Start measuring time
start = time.time()

# Set paths
dataset_path = "Data1" 
img_height, img_width = 224, 224  # Adjusting for MobileNetV2's input size
batch_size = 32

# Load and preprocess images
datagen = ImageDataGenerator(preprocessing_function=preprocess_input, validation_split=0.3)

train_gen = datagen.flow_from_directory(
    dataset_path,
    target_size=(img_height, img_width),
    batch_size=batch_size,
    class_mode='sparse',
    subset='training',
    shuffle=False
)

val_gen = datagen.flow_from_directory(
    dataset_path,
    target_size=(img_height, img_width),
    batch_size=batch_size,
    class_mode='sparse',
    subset='validation',
    shuffle=False
)

# Load pretrained MobileNetV2 (without top layer)
base_model = MobileNetV2(input_shape=(img_height, img_width, 3),
                         include_top=False,
                         weights='imagenet',
                         pooling='avg')  # Global Average Pooling

# Extract features using MobileNetV2
X_train_features = base_model.predict(train_gen, verbose=0)
y_train = train_gen.classes

X_val_features = base_model.predict(val_gen, verbose=0)
y_val = val_gen.classes

# Train SVM classifier
# Pilih fungsi  One-vs-Rest (OVR) dan One-vs-One (OVO) Fungsinya : 'ovr', 'ovo'
# Pilih fungsi kernel di sini: 'linear', 'poly', 'rbf', 'sigmoid'
svm = SVC(kernel='rbf', C=10, gamma='scale', probability=True)  # Using RBF kernel for better performance
# svm = SVC(kernel='linear', C=10, gamma='scale', probability=True, decision_function_shape='ovr', random_state=42) # Contoh menggunakan kernel RBF
svm.fit(X_train_features, y_train)
# === Evaluasi pada data train ===
y_train_pred = svm.predict(X_train_features)
train_accuracy = accuracy_score(y_train, y_train_pred) * 100
print(f"Train Accuracy: {train_accuracy:.2f}%")  # <<--- Tambahkan baris ini

# === Prediksi dan Evaluasi ===
y_pred = svm.predict(X_val_features)
accuracy = accuracy_score(y_val, y_pred) * 100
precision = precision_score(y_val, y_pred, average='macro', zero_division=0) * 100
recall = recall_score(y_val, y_pred, average='macro', zero_division=0) * 100
f1 = f1_score(y_val, y_pred, average='macro', zero_division=0) * 100

# === Step 6: Report === Menampilkan Hasil ===
class_names = list(dict(sorted(val_gen.class_indices.items(), key=lambda item: item[1])).keys())
print(f"\nValidation Accuracy: {accuracy:.2f}%")
print("\n=== Classification Report ===")
print(classification_report(y_val, y_pred, target_names=class_names, zero_division=0))

# === Visualisasi Metrik ===
metrics = {'Accuracy': accuracy, 'Precision': precision, 'Recall': recall, 'F1-Score': f1}

plt.figure(figsize=(8, 6))
plt.bar(metrics.keys(), metrics.values(), color=['skyblue', 'orange', 'green', 'red'])
plt.ylim(0, 100)
plt.ylabel('Percentage')
plt.title('Model Evaluation Metrics')
for i, (key, value) in enumerate(metrics.items()):
    plt.text(i, value + 1, f'{value:.2f}%', ha='center')
plt.grid(axis='y', linestyle='--', alpha=0.7)
plt.tight_layout()
plt.show()

# === Confusion Matrix ===
cm = confusion_matrix(y_val, y_pred)
plt.figure(figsize=(10, 8))
sns.heatmap(cm, annot=True, fmt='d', cmap='Blues',
            xticklabels=class_names, yticklabels=class_names)
plt.xlabel('Predicted label')
plt.ylabel('True label')
plt.title('Confusion Matrix')
plt.xticks(rotation=45, ha='right')  # Rotasi agar tidak tumpang tindih
plt.yticks(rotation=0)               # Supaya label Y tetap horizontal
plt.tight_layout()
plt.show()


# Save the trained model and class names
joblib.dump(svm, 'svm_model_mobilenetv2.pkl')  # Save SVM model
joblib.dump(val_gen.class_indices, 'class_names_mobilenetv2.pkl')  # Save class names
print("Model and class names have been saved.")


# End measuring time
end = time.time()
# Measure total time taken
print(f"Total time: {end - start:.2f} seconds")




