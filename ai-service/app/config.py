import os

STORAGE_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", "..", "storage"))
MODELS_DIR = os.path.join(STORAGE_DIR, "models")
DATASETS_DIR = os.path.join(STORAGE_DIR, "datasets")
PREDICTIONS_DIR = os.path.join(STORAGE_DIR, "predictions")
RUNS_DIR = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", "runs"))

MAX_DATASET_SIZE_MB = 500
VALID_EXTENSIONS = {".jpg", ".jpeg", ".png"}
MASK_KEYWORDS = ["mask", "label", "annotation", "gt"]
MIN_IMAGES_PER_CLASS = 20
WARNING_IMAGES_PER_CLASS = 50
IMG_SIZE = (224, 224)
RANDOM_SEED = 42

for d in [MODELS_DIR, DATASETS_DIR, PREDICTIONS_DIR, RUNS_DIR]:
    os.makedirs(d, exist_ok=True)
