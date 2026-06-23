# NHÓM 05 - HỆ THỐNG NHẬN DIỆN VÀ PHÂN LOẠI RÁC THẢI

## 1. Giới thiệu đề tài

Đề tài: **Xây dựng hệ thống Web nhận diện và phân loại rác thải sinh hoạt ứng dụng Học sâu (Deep Learning)**.

Dự án xây dựng một hệ thống Web hỗ trợ người dùng nhận diện và phân loại rác thải sinh hoạt thông qua hình ảnh. Người dùng có thể tải ảnh rác thải từ thiết bị hoặc sử dụng camera để chụp ảnh trực tiếp. Hệ thống sẽ gửi hình ảnh đến API AI để phân loại và hiển thị kết quả lên giao diện Web.

Hệ thống phân loại rác thành 3 nhóm chính:

- Rác hữu cơ
- Rác tái chế
- Rác vô cơ / nguy hại

## 2. Thành viên thực hiện

| STT | Họ và tên | Vai trò |
|---|---|---|
| 1 | Phương | Backend & Frontend Web Laravel |
| 2 | Ngọc | AI, xử lý dữ liệu, huấn luyện mô hình và API Python |

## 3. Phân công công việc

### Phương - Backend & Frontend Web

- Khởi tạo dự án Web bằng Laravel.
- Thiết kế giao diện trang chủ, trang tải ảnh và trang camera.
- Viết Controller Laravel để nhận ảnh từ người dùng.
- Gửi ảnh từ Laravel sang API AI Python.
- Nhận kết quả JSON từ API và hiển thị lên giao diện.

### Ngọc - AI & Data

- Tải và xử lý bộ dữ liệu Garbage Classification.
- Gộp dữ liệu từ 12 lớp ban đầu thành 3 nhóm rác.
- Huấn luyện mô hình MobileNetV2.
- Đóng gói mô hình thành API bằng FastAPI hoặc Flask.
- Trả kết quả phân loại về cho Laravel.

## 4. Công nghệ sử dụng

### Web

- Laravel
- PHP
- Blade Template
- HTML5
- CSS3
- JavaScript
- Bootstrap

### AI

- Python
- TensorFlow / Keras
- MobileNetV2
- OpenCV
- FastAPI hoặc Flask

### Quản lý mã nguồn

- Git
- GitHub

## 5. Chức năng chính

### 5.1. Nhận diện qua ảnh tải lên

Người dùng chọn một ảnh rác thải từ thiết bị, sau đó bấm nút nhận diện. Laravel nhận ảnh và gửi sang API AI để phân loại.

### 5.2. Nhận diện qua camera

Người dùng mở camera trên trình duyệt, đưa rác vào khung hình và bấm nút **Chụp & Nhận diện**. JavaScript sẽ chụp một khung hình, mã hóa ảnh và gửi về Laravel để xử lý.

### 5.3. Hiển thị kết quả

Hệ thống hiển thị:

- Loại rác
- Độ tin cậy
- Gợi ý bỏ rác đúng thùng

Ví dụ định dạng JSON API trả về:

```json
{
  "class": "Tai_che",
  "confidence": 0.96
}
## 6. Quy trình xử lý hệ thống

```text
Người dùng chọn ảnh hoặc chụp ảnh từ camera
        ↓
Laravel nhận dữ liệu ảnh
        ↓
Laravel gửi ảnh sang API AI Python
        ↓
Mô hình AI phân loại ảnh
        ↓
API trả về JSON kết quả
        ↓
Laravel hiển thị kết quả lên giao diện
```

## 7. Cấu trúc thư mục chính

```text
waste-classification-web/
├── app/
│   └── Http/
│       └── Controllers/
├── resources/
│   └── views/
├── routes/
│   └── web.php
├── public/
├── database/
├── .env.example
└── README.md
```
