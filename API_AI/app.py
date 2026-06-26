from flask import Flask, request, jsonify
from flask_cors import CORS

import os
import io
import numpy as np
import tensorflow as tf
from PIL import Image
from tensorflow.keras.applications.mobilenet_v2 import preprocess_input

app = Flask(__name__)
CORS(app)

print("Đang đánh thức AI, vui lòng đợi...")

model = tf.keras.models.load_model(
    "model_rac_thai_fixed.h5",
    custom_objects={"preprocess_input": preprocess_input},
    compile=False
)

print("AI đã sẵn sàng nhận ảnh!")

# Model trong Colab được huấn luyện với 12 lớp chi tiết
class_names = [
    "battery",
    "biological",
    "brown-glass",
    "cardboard",
    "clothes",
    "green-glass",
    "metal",
    "paper",
    "plastic",
    "shoes",
    "trash",
    "white-glass"
]

# Gộp 12 lớp chi tiết thành 3 nhóm rác theo đề tài
category_mapping = {
    "biological": "Huu_co",

    "cardboard": "Tai_che",
    "paper": "Tai_che",
    "plastic": "Tai_che",
    "metal": "Tai_che",
    "brown-glass": "Tai_che",
    "green-glass": "Tai_che",
    "white-glass": "Tai_che",

    "battery": "Vo_co",
    "trash": "Vo_co",
    "clothes": "Vo_co",
    "shoes": "Vo_co"
}

def preprocess_image(image_bytes):
    img = Image.open(io.BytesIO(image_bytes)).convert("RGB")
    img = img.resize((224, 224))

    img_array = np.array(img)
    img_array = np.expand_dims(img_array, axis=0)

    # Không chia /255.0
    # Không gọi preprocess_input ở đây
    # Vì model đã có tầng Lambda preprocess_input bên trong

    return img_array

@app.route("/", methods=["GET"])
def home():
    return jsonify({
        "success": True,
        "message": "API AI phân loại rác thải đang chạy"
    })

@app.route("/predict", methods=["POST"])
def predict():
    if "file" not in request.files:
        return jsonify({
            "success": False,
            "message": "Không tìm thấy file ảnh. Field gửi lên phải tên là file."
        }), 400

    try:
        file = request.files["file"]
        image_bytes = file.read()

        processed_image = preprocess_image(image_bytes)

        predictions = model.predict(processed_image)

        predicted_class_index = int(np.argmax(predictions[0]))
        confidence = float(np.max(predictions[0]) * 100)

        specific_class = class_names[predicted_class_index]
        final_class = category_mapping[specific_class]

        print("Predictions:", predictions[0])
        print("Predicted index:", predicted_class_index)
        print("Specific class:", specific_class)
        print("Final class:", final_class)
        print("Confidence:", round(confidence, 2))

        return jsonify({
            "success": True,
            "class": final_class,
            "specific_class": specific_class,
            "confidence": round(confidence, 2)
        })

    except Exception as e:
        return jsonify({
            "success": False,
            "message": str(e)
        }), 500

if __name__ == "__main__":
    app.run(host="127.0.0.1", port=8000, debug=False)