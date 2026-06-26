from flask import Flask, request, jsonify
from tensorflow.keras.models import load_model
from tensorflow.keras.preprocessing import image
from tensorflow.keras.applications.mobilenet_v2 import preprocess_input
import numpy as np
import io
from PIL import Image

app = Flask(__name__)

# Nạp mô hình đã được Fine-tuned cùng đối tượng tùy chỉnh
MODEL_PATH = 'garbage_classification_model_finetuned.h5'
model = load_model(MODEL_PATH, custom_objects={'preprocess_input': preprocess_input})

class_names = ['battery', 'biological', 'brown-glass', 'cardboard', 'clothes', 
               'green-glass', 'metal', 'paper', 'plastic', 'shoes', 'trash', 'white-glass']

category_mapping = {
    'biological': 'Rác Hữu cơ', 'cardboard': 'Rác Tái chế', 'paper': 'Rác Tái chế',
    'plastic': 'Rác Tái chế', 'metal': 'Rác Tái chế', 'brown-glass': 'Rác Tái chế',
    'green-glass': 'Rác Tái chế', 'white-glass': 'Rác Tái chế', 'battery': 'Rác Vô cơ',
    'trash': 'Rác Vô cơ', 'clothes': 'Rác Vô cơ', 'shoes': 'Rác Vô cơ'
}

@app.route('/api/predict', methods=['POST'])
def predict():
    if 'file' not in request.files:
        return jsonify({'error': 'Không tìm thấy tệp tin'}), 400
    
    file = request.files['file']
    try:
        # Chuyển đổi luồng byte thành hình ảnh và chuẩn hóa kích thước
        img = Image.open(io.BytesIO(file.read())).convert('RGB')
        img = img.resize((224, 224))
        
        img_array = image.img_to_array(img)
        img_array = np.expand_dims(img_array, axis=0)
        
        predictions = model.predict(img_array)
        predicted_index = np.argmax(predictions)
        
        confidence = float(predictions[0][predicted_index]) * 100
        predicted_class = class_names[predicted_index]
        garbage_type = category_mapping[predicted_class]
        
        return jsonify({
            'success': True,
            'label': predicted_class.upper(),
            'category': garbage_type,
            'confidence': round(confidence, 2)
        })
    except Exception as e:
        return jsonify({'success': False, 'error': str(e)}), 500

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)