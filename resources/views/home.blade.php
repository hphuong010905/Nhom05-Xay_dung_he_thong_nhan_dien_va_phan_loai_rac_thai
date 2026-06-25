<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nhận diện và phân loại rác thải</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --green-main: #159957;
            --green-dark: #0f6f43;
            --green-soft: #e8f7ef;
            --yellow-main: #ffc83d;
            --gray-main: #6b7280;
            --dark-text: #123026;
            --card-border: rgba(21, 153, 87, 0.18);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", Arial, Helvetica, sans-serif;
            color: var(--dark-text);
            background:
                radial-gradient(circle at top left, rgba(35, 190, 113, 0.22), transparent 32%),
                radial-gradient(circle at bottom right, rgba(255, 200, 61, 0.18), transparent 28%),
                linear-gradient(135deg, #f4fbf7 0%, #eef7f2 45%, #f7faf8 100%);
        }

        .page-shell {
            width: min(94vw, 1120px);
            margin: 0 auto;
            padding: clamp(12px, 2vh, 22px) 0;
        }

        .hero {
            display: grid;
            grid-template-columns: 57% 43%;
            gap: clamp(12px, 1.8vw, 20px);
            align-items: stretch;
            margin-bottom: clamp(12px, 1.8vh, 18px);
        }

        .hero-card,
        .result-card,
        .action-card {
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid var(--card-border);
            border-radius: clamp(18px, 2vw, 24px);
            box-shadow: 0 14px 36px rgba(15, 111, 67, 0.11);
            backdrop-filter: blur(10px);
        }

        .hero-card {
            padding: clamp(18px, 2.5vw, 28px);
            position: relative;
            overflow: hidden;
        }

        .hero-card::after {
            content: "";
            position: absolute;
            width: 18%;
            aspect-ratio: 1 / 1;
            border-radius: 50%;
            background: rgba(21, 153, 87, 0.09);
            right: -5%;
            top: -12%;
        }

        .app-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: clamp(6px, 0.9vw, 8px) clamp(10px, 1.3vw, 14px);
            border-radius: 999px;
            background: var(--green-soft);
            color: var(--green-dark);
            font-size: clamp(12px, 1vw, 14px);
            font-weight: 700;
            margin-bottom: clamp(10px, 1.4vh, 14px);
        }

        .hero-title {
            margin: 0;
            font-size: clamp(28px, 4vw, 46px);
            line-height: 1.12;
            font-weight: 900;
            color: var(--green-dark);
            letter-spacing: -0.8px;
        }

        .hero-title span {
            color: var(--green-main);
        }

        .hero-desc {
            margin-top: clamp(8px, 1.2vh, 12px);
            margin-bottom: 0;
            color: #4b6359;
            font-size: clamp(14px, 1.2vw, 16px);
            line-height: 1.55;
        }

        .feature-row {
            display: flex;
            gap: clamp(7px, 1vw, 10px);
            flex-wrap: wrap;
            margin-top: clamp(12px, 1.8vh, 18px);
        }

        .feature-pill {
            padding: clamp(6px, 0.9vw, 8px) clamp(9px, 1.1vw, 12px);
            border-radius: 12px;
            background: #ffffff;
            border: 1px solid rgba(21, 153, 87, 0.15);
            color: #315f4a;
            font-size: clamp(12px, 1vw, 14px);
            font-weight: 650;
        }

        .result-card {
            padding: clamp(18px, 2.1vw, 22px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: clamp(190px, 26vh, 240px);
            border: 2px solid rgba(21, 153, 87, 0.2);
        }

        .result-heading {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--green-dark);
            font-size: clamp(18px, 1.7vw, 22px);
            font-weight: 850;
            margin-bottom: clamp(12px, 1.6vh, 16px);
        }

        .result-icon {
            width: clamp(36px, 4vw, 44px);
            aspect-ratio: 1 / 1;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, #dff7ea, #ffffff);
            border: 1px solid rgba(21, 153, 87, 0.18);
            font-size: clamp(18px, 2vw, 22px);
        }

        .result-main {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: clamp(8px, 1.2vh, 12px);
        }

        .result-label {
            color: #4b6359;
            font-size: clamp(14px, 1.2vw, 16px);
            font-weight: 650;
        }

        .result-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: clamp(7px, 0.9vw, 9px) clamp(11px, 1.3vw, 14px);
            border-radius: 13px;
            background: var(--yellow-main);
            color: #2e2700;
            font-weight: 850;
            font-size: clamp(14px, 1.2vw, 16px);
            box-shadow: inset 0 -2px 0 rgba(0,0,0,0.08);
        }

        .confidence-box {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: clamp(10px, 1.2vw, 13px) clamp(12px, 1.5vw, 15px);
            border-radius: 16px;
            background: #f8fbf9;
            border: 1px solid rgba(21, 153, 87, 0.12);
            margin-top: 8px;
            font-size: clamp(14px, 1.1vw, 16px);
        }

        .confidence-value {
            font-weight: 900;
            color: var(--green-dark);
            font-size: clamp(16px, 1.5vw, 19px);
        }

        .suggestion {
            margin-top: clamp(9px, 1.2vh, 12px);
            color: #566b62;
            line-height: 1.5;
            font-size: clamp(13px, 1.05vw, 14.5px);
        }

        .main-grid {
            display: grid;
            grid-template-columns: 58% 42%;
            gap: clamp(12px, 1.8vw, 20px);
            align-items: start;
        }

        .action-card {
            padding: clamp(18px, 2.2vw, 24px);
        }

        .card-title-custom {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: clamp(12px, 1.6vh, 16px);
            font-size: clamp(17px, 1.5vw, 21px);
            font-weight: 850;
            color: var(--dark-text);
        }

        .mini-icon {
            width: clamp(32px, 3.5vw, 38px);
            aspect-ratio: 1 / 1;
            border-radius: 12px;
            display: grid;
            place-items: center;
            background: var(--green-soft);
            color: var(--green-dark);
        }

        .upload-zone {
            border: 2px dashed rgba(21, 153, 87, 0.28);
            border-radius: 18px;
            padding: clamp(14px, 1.8vw, 18px);
            background: #fbfffd;
            transition: 0.2s ease;
        }

        .upload-zone:hover {
            border-color: rgba(21, 153, 87, 0.55);
            background: #f7fff9;
        }

        .form-label {
            font-size: clamp(13px, 1vw, 15px);
        }

        .form-control {
            border-radius: 14px;
            padding: clamp(9px, 1vw, 11px) clamp(10px, 1.2vw, 12px);
            border: 1px solid #d8e3dd;
            font-size: clamp(13px, 1vw, 15px);
        }

        .form-control:focus {
            border-color: var(--green-main);
            box-shadow: 0 0 0 0.18rem rgba(21, 153, 87, 0.15);
        }

        .preview-box {
            display: none;
            margin-top: 14px;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #dce9e2;
            background: #f7faf8;
        }

        .preview-box img {
            width: 100%;
            max-height: clamp(180px, 32vh, 280px);
            object-fit: contain;
            display: block;
            background: #f2f5f3;
        }

        .or-line {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: clamp(12px, 1.7vh, 16px) 0;
            color: #7c8d85;
            font-size: clamp(12px, 0.95vw, 13px);
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .or-line::before,
        .or-line::after {
            content: "";
            height: 1px;
            flex: 1;
            background: #dce7e1;
        }

        .btn-camera,
        .btn-submit {
            width: 100%;
            border: none;
            border-radius: 15px;
            padding: clamp(10px, 1.2vw, 13px) clamp(12px, 1.4vw, 15px);
            font-weight: 850;
            font-size: clamp(14px, 1.1vw, 16px);
            transition: 0.18s ease;
        }

        .btn-camera {
            background: #64748b;
            color: #ffffff;
        }

        .btn-camera:hover {
            background: #475569;
            color: #ffffff;
            transform: translateY(-1px);
        }

        .btn-submit {
            margin-top: clamp(12px, 1.5vh, 15px);
            color: #ffffff;
            background: linear-gradient(135deg, #159957, #0f7b49);
            box-shadow: 0 10px 24px rgba(21, 153, 87, 0.23);
        }

        .btn-submit:hover {
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 14px 30px rgba(21, 153, 87, 0.28);
        }

        .camera-area {
            display: none;
            margin-top: 14px;
            padding: clamp(12px, 1.4vw, 15px);
            background: #f8fbf9;
            border: 1px solid #dce9e2;
            border-radius: 18px;
        }

        .camera-video {
            width: 100%;
            max-height: clamp(200px, 32vh, 300px);
            object-fit: cover;
            border-radius: 16px;
            background: #0f172a;
        }

        .camera-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 10px;
        }

        .camera-actions .btn {
            border-radius: 13px;
            font-weight: 750;
            padding: clamp(8px, 1vw, 10px);
            font-size: clamp(13px, 1vw, 15px);
        }

        .info-card {
            padding: clamp(18px, 2.2vw, 24px);
        }

        .waste-list {
            display: grid;
            gap: clamp(10px, 1.3vh, 13px);
            margin-top: 12px;
        }

        .waste-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: clamp(12px, 1.4vw, 15px);
            border-radius: 17px;
            background: #ffffff;
            border: 1px solid rgba(21, 153, 87, 0.12);
        }

        .waste-symbol {
            width: clamp(34px, 3.6vw, 40px);
            aspect-ratio: 1 / 1;
            flex: 0 0 auto;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: clamp(18px, 1.8vw, 21px);
        }

        .organic {
            background: #e9f8d9;
        }

        .recycle {
            background: #e3f2ff;
        }

        .inorganic {
            background: #fff2d8;
        }

        .waste-item strong {
            display: block;
            margin-bottom: 3px;
            font-size: clamp(14px, 1.1vw, 15px);
            color: var(--dark-text);
        }

        .waste-item span {
            color: #66756f;
            font-size: clamp(12.5px, 1vw, 13.5px);
            line-height: 1.45;
        }

        .alert {
            border-radius: 15px;
            font-size: clamp(13px, 1vw, 14px);
            padding: 11px 13px;
        }

        @media (max-width: 900px) {
            .hero,
            .main-grid {
                grid-template-columns: 1fr;
            }

            .hero-title {
                font-size: clamp(28px, 7vw, 38px);
            }

            .result-card {
                min-height: unset;
            }
        }

        @media (max-width: 576px) {
            .page-shell {
                width: 94vw;
                padding: 12px 0;
            }

            .hero-card,
            .result-card,
            .action-card,
            .info-card {
                border-radius: 20px;
            }

            .feature-row {
                gap: 8px;
            }

            .camera-actions {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

@php
    $displayClass = session('display_class');
    $confidence = session('confidence');
    $suggestion = session('suggestion');

    if (!$displayClass) {
        $displayClass = 'Chưa có kết quả';
        $confidence = '--';
        $suggestion = 'Hãy tải ảnh rác thải hoặc sử dụng camera để hệ thống bắt đầu phân loại.';
    }

    $confidenceText = $confidence === '--' ? '--' : $confidence . '%';
@endphp

<div class="page-shell">

    <section class="hero">
        <div class="hero-card">
            <div class="app-badge">🌱 AI Waste Classification</div>

            <h1 class="hero-title">
                Nhận diện & phân loại <span>rác thải</span> thông minh
            </h1>

            <p class="hero-desc">
                Hệ thống hỗ trợ phân loại rác thải sinh hoạt dựa trên hình ảnh,
                giúp người dùng nhận biết nhóm rác và bỏ rác đúng nơi quy định.
            </p>

            <div class="feature-row">
                <div class="feature-pill">♻️ Rác tái chế</div>
                <div class="feature-pill">🍃 Rác hữu cơ</div>
                <div class="feature-pill">⚠️ Rác vô cơ / nguy hại</div>
            </div>
        </div>

        <div class="result-card">
            <div class="result-heading">
                <div class="result-icon">🤖</div>
                <div>Kết quả phân tích</div>
            </div>

            <div class="result-main">
                <span class="result-label">Loại rác:</span>
                <span class="result-badge">{{ $displayClass }}</span>
            </div>

            <div class="confidence-box">
                <span>Độ tin cậy</span>
                <span class="confidence-value">{{ $confidenceText }}</span>
            </div>

            <div class="suggestion">
                {{ $suggestion }}
            </div>
        </div>
    </section>

    <section class="main-grid">
        <div class="action-card">
            <div class="card-title-custom">
                <div class="mini-icon">📤</div>
                <div>Tải ảnh hoặc chụp ảnh rác thải</div>
            </div>

            <form action="{{ route('detect.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="upload-zone">
                    <label class="form-label fw-semibold">Chọn ảnh từ thiết bị</label>
                    <input id="imageInput" type="file" name="image" class="form-control" accept="image/*">

                    <div id="previewBox" class="preview-box">
                        <img id="previewImage" src="#" alt="Ảnh rác thải đã chọn">
                    </div>

                    <div class="or-line">HOẶC</div>

                    <button type="button" id="btnOpenCamera" class="btn btn-camera">
                        📷 Bật camera
                    </button>

                    <div id="cameraArea" class="camera-area">
                        <video id="video" class="camera-video" autoplay playsinline></video>
                        <canvas id="canvas" style="display:none;"></canvas>

                        <div class="camera-actions">
                            <button type="button" id="btnCapture" class="btn btn-success">
                                📸 Chụp & nhận diện
                            </button>

                            <button type="button" id="btnCloseCamera" class="btn btn-outline-danger">
                                Tắt camera
                            </button>
                        </div>

                        <div id="cameraResult" class="mt-3"></div>
                    </div>
                </div>

                <button type="submit" class="btn btn-submit">
                    Phân tích ảnh ngay
                </button>
            </form>

            @error('image')
                <div class="alert alert-danger mt-3 mb-0">
                    {{ $message }}
                </div>
            @enderror

            @if(session('error'))
                <div class="alert alert-danger mt-3 mb-0">
                    {{ session('error') }}
                </div>
            @endif
        </div>

        <div class="action-card info-card">
            <div class="card-title-custom">
                <div class="mini-icon">🗂️</div>
                <div>Nhóm rác hệ thống hỗ trợ</div>
            </div>

            <div class="waste-list">
                <div class="waste-item">
                    <div class="waste-symbol organic">🍃</div>
                    <div>
                        <strong>Rác hữu cơ</strong>
                        <span>Thức ăn thừa, rau củ, lá cây và các loại rác dễ phân hủy.</span>
                    </div>
                </div>

                <div class="waste-item">
                    <div class="waste-symbol recycle">♻️</div>
                    <div>
                        <strong>Rác tái chế</strong>
                        <span>Chai nhựa, lon kim loại, giấy, bìa carton và vật liệu có thể tái sử dụng.</span>
                    </div>
                </div>

                <div class="waste-item">
                    <div class="waste-symbol inorganic">⚠️</div>
                    <div>
                        <strong>Rác vô cơ / nguy hại</strong>
                        <span>Túi nilon bẩn, gốm sứ vỡ, pin, hóa chất hoặc rác khó phân hủy.</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

<script>
    const imageInput = document.getElementById('imageInput');
    const previewBox = document.getElementById('previewBox');
    const previewImage = document.getElementById('previewImage');

    const btnOpenCamera = document.getElementById('btnOpenCamera');
    const btnCloseCamera = document.getElementById('btnCloseCamera');
    const btnCapture = document.getElementById('btnCapture');
    const cameraArea = document.getElementById('cameraArea');
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const cameraResult = document.getElementById('cameraResult');

    let stream = null;

    imageInput.addEventListener('change', function () {
        const file = this.files[0];

        if (!file) {
            previewBox.style.display = 'none';
            previewImage.src = '#';
            return;
        }

        previewImage.src = URL.createObjectURL(file);
        previewBox.style.display = 'block';
    });

    btnOpenCamera.addEventListener('click', async function () {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = stream;
            cameraArea.style.display = 'block';
        } catch (error) {
            alert('Không thể mở camera. Vui lòng kiểm tra quyền truy cập camera.');
        }
    });

    btnCloseCamera.addEventListener('click', function () {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            video.srcObject = null;
            stream = null;
        }

        cameraArea.style.display = 'none';
        cameraResult.innerHTML = '';
    });

    btnCapture.addEventListener('click', async function () {
        if (!stream) {
            alert('Vui lòng bật camera trước.');
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        const imageData = canvas.toDataURL('image/png');

        cameraResult.innerHTML = `
            <div class="alert alert-warning mb-0">
                Đang gửi ảnh camera để hệ thống phân tích...
            </div>
        `;

        try {
            const response = await fetch("{{ route('detect.camera') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    image: imageData
                })
            });

            const data = await response.json();

            if (data.success) {
                cameraResult.innerHTML = `
                    <div class="alert alert-success mb-0">
                        <strong>Kết quả camera:</strong><br>
                        Loại rác: <strong>${data.display_class}</strong><br>
                        Độ tin cậy: <strong>${data.confidence}%</strong><br>
                        ${data.suggestion}
                    </div>
                `;
            } else {
                cameraResult.innerHTML = `
                    <div class="alert alert-danger mb-0">
                        ${data.message ?? 'Có lỗi xảy ra khi nhận diện ảnh từ camera.'}
                    </div>
                `;
            }
        } catch (error) {
            cameraResult.innerHTML = `
                <div class="alert alert-danger mb-0">
                    Không thể gửi ảnh từ camera đến hệ thống.
                </div>
            `;
        }
    });
</script>

</body>
</html>