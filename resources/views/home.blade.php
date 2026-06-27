<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phân loại rác thải bằng AI</title>

    <style>
        * {
            box-sizing: border-box;
        }

        :root {
            --green: #159456;
            --green-dark: #0f6f41;
            --green-soft: #eaf8f1;
            --green-border: #bfe7d0;
            --blue: #2f80ed;
            --blue-soft: #eef6ff;
            --yellow: #f5a400;
            --yellow-soft: #fff8e6;
            --red-soft: #fff0f0;
            --bg: #f5f8f6;
            --white: #ffffff;
            --text: #172033;
            --muted: #667085;
            --border: #dde7e1;
            --shadow: 0 10px 28px rgba(15, 23, 42, 0.07);
            --radius-lg: 22px;
            --radius-md: 16px;
            --radius-sm: 12px;
        }

        body {
            margin: 0;
            background: linear-gradient(180deg, #f8fbf9 0%, #eef5f1 100%);
            color: var(--text);
            font-family: "Segoe UI", Arial, Helvetica, sans-serif;
            font-size: 16px;
        }

        .page {
            width: min(94%, 1180px);
            margin: 22px auto 28px;
        }

        .top-header {
            background: var(--white);
            border: 1px solid #eef2ef;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            padding: 20px 3%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 22px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 18px;
            min-width: 0;
        }

        .brand-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            background: var(--green-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            flex-shrink: 0;
        }

        .brand-text h1 {
            margin: 0;
            color: var(--green-dark);
            font-size: clamp(26px, 3.1vw, 42px);
            line-height: 1.15;
            font-weight: 800;
            letter-spacing: 0.1px;
        }

        .brand-text p {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: clamp(14px, 1.2vw, 17px);
            line-height: 1.5;
        }

        .eco-badge {
            min-width: 210px;
            padding: 14px 18px;
            border-radius: 16px;
            background: #f6fbf8;
            border: 1px solid var(--green-border);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .eco-badge .leaf {
            font-size: 28px;
        }

        .eco-badge strong {
            display: block;
            color: var(--green-dark);
            font-size: 15px;
            margin-bottom: 3px;
        }

        .eco-badge span {
            color: var(--muted);
            font-size: 13px;
        }

        .main-grid {
            display: grid;
            grid-template-columns: 31.5% 33% 31.5%;
            gap: 2%;
            margin-bottom: 22px;
        }

        .card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            padding: 22px;
        }

        .step-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .step-number {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--green);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            flex-shrink: 0;
        }

        .step-title h2 {
            margin: 0;
            font-size: clamp(18px, 1.5vw, 22px);
            font-weight: 800;
            color: var(--text);
        }

        .step-desc {
            margin: -4px 0 18px 46px;
            color: var(--muted);
            font-size: 14.5px;
            line-height: 1.5;
        }

        .upload-zone {
            border: 2px dashed #9bd9b8;
            border-radius: var(--radius-md);
            background: #fbfffd;
            padding: 28px 18px;
            text-align: center;
            transition: 0.2s ease;
        }

        .upload-zone.drag-over {
            background: var(--green-soft);
            border-color: var(--green);
        }

        .upload-icon {
            width: 62px;
            height: 62px;
            border-radius: 50%;
            background: var(--green-soft);
            border: 1px solid var(--green-border);
            margin: 0 auto 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }

        .upload-zone h3 {
            margin: 0 0 7px;
            font-size: 17px;
            font-weight: 800;
        }

        .upload-zone p {
            margin: 0 0 16px;
            color: var(--muted);
            font-size: 14px;
        }

        .file-input {
            display: none;
        }

        .btn {
            border: none;
            outline: none;
            cursor: pointer;
            border-radius: 13px;
            padding: 12px 16px;
            font-size: 15px;
            font-weight: 750;
            transition: 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            user-select: none;
        }

        .btn:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .btn-primary {
            background: var(--green);
            color: #fff;
            box-shadow: 0 8px 17px rgba(21, 148, 86, 0.22);
        }

        .btn-primary:hover:not(:disabled) {
            background: var(--green-dark);
            transform: translateY(-1px);
        }

        .btn-outline {
            background: var(--white);
            color: var(--green-dark);
            border: 1px solid var(--green);
        }

        .btn-outline:hover:not(:disabled) {
            background: var(--green-soft);
        }

        .btn-soft {
            background: var(--green-soft);
            color: var(--green-dark);
            border: 1px solid var(--green-border);
        }

        .btn-gray {
            background: #eef2f6;
            color: #344054;
        }

        .btn-full {
            width: 100%;
        }

        .or-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 18px 0;
            color: #98a2b3;
            font-size: 13px;
            font-weight: 700;
        }

        .or-divider::before,
        .or-divider::after {
            content: "";
            height: 1px;
            background: #e4e7ec;
            flex: 1;
        }

        .helper-text {
            margin-top: 16px;
            color: var(--muted);
            font-size: 13.5px;
            line-height: 1.55;
        }

        .submit-area {
            margin-top: 18px;
        }

        .camera-section {
            display: none;
            margin-top: 18px;
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 12px;
            background: #fbfdfc;
        }

        .camera-section.active {
            display: block;
        }

        .camera-frame {
            width: 100%;
            aspect-ratio: 4 / 3;
            border-radius: 13px;
            overflow: hidden;
            background: #111827;
            border: 1px solid #111827;
        }

        .camera-frame video,
        .camera-frame canvas {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .camera-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 12px;
        }

        .preview-card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .preview-box {
            width: 100%;
            aspect-ratio: 4 / 3;
            border-radius: var(--radius-md);
            background: #f7faf8;
            border: 1px solid var(--border);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
        }

        .preview-box img,
        .preview-box video,
        .preview-box canvas {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
            background: #f8faf9;
        }

        .preview-placeholder {
            text-align: center;
            padding: 22px;
            color: #98a2b3;
        }

        .preview-placeholder .icon {
            font-size: 52px;
            display: block;
            margin-bottom: 12px;
        }

        .preview-placeholder strong {
            display: block;
            color: #475467;
            font-size: 16px;
            margin-bottom: 5px;
        }

        .preview-placeholder span {
            font-size: 13.5px;
            line-height: 1.5;
        }

        .analyzing-status {
            margin-top: auto;
            background: var(--green-soft);
            color: var(--green-dark);
            border-radius: 14px;
            padding: 13px 14px;
            text-align: center;
            font-weight: 750;
            font-size: 14px;
        }

        .result-panel {
            height: 100%;
        }

        .result-empty {
            border: 1px dashed var(--border);
            border-radius: var(--radius-md);
            background: #fbfdfc;
            padding: 28px 18px;
            text-align: center;
            color: var(--muted);
            line-height: 1.6;
        }

        .result-badge {
            background: linear-gradient(135deg, #ffda58, #ffc533);
            color: #111827;
            border-radius: 14px;
            padding: 16px;
            font-size: clamp(18px, 1.8vw, 23px);
            font-weight: 850;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
        }

        .result-badge .left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .result-badge .icon {
            font-size: 28px;
        }

        .confidence-label {
            color: var(--muted);
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 7px;
        }

        .confidence-value {
            color: var(--green);
            font-size: clamp(24px, 2.4vw, 34px);
            font-weight: 900;
            margin-bottom: 8px;
        }

        .confidence-bar {
            width: 100%;
            height: 11px;
            background: #e5e7eb;
            border-radius: 999px;
            overflow: hidden;
            margin-bottom: 18px;
        }

        .confidence-fill {
            height: 100%;
            background: linear-gradient(90deg, #21b66f, var(--green));
            border-radius: 999px;
        }

        .quick-tip {
            border-radius: var(--radius-md);
            background: #f6fbf8;
            border: 1px solid var(--green-border);
            padding: 16px;
        }

        .quick-tip h3 {
            margin: 0 0 12px;
            font-size: 16px;
            font-weight: 850;
        }

        .quick-tip ul {
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .quick-tip li {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            color: #475467;
            line-height: 1.5;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .quick-tip li::before {
            content: "✓";
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--green);
            color: #fff;
            font-size: 12px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .guide-section {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            padding: 22px;
            margin-bottom: 20px;
        }

        .guide-title {
            margin: 0 0 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text);
            font-size: clamp(19px, 1.7vw, 25px);
            font-weight: 850;
        }

        .guide-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 18px;
        }

        .guide-card {
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            padding: 20px;
            min-height: 220px;
        }

        .guide-card.recycle {
            background: #f2fbf6;
            border-color: var(--green-border);
        }

        .guide-card.process {
            background: var(--blue-soft);
            border-color: #c7defb;
        }

        .guide-card.note {
            background: var(--yellow-soft);
            border-color: #f6d58b;
        }

        .guide-card h3 {
            margin: 0 0 8px;
            font-size: 19px;
            font-weight: 850;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .guide-card p {
            margin: 0 0 16px;
            color: #475467;
            font-size: 14.5px;
            line-height: 1.55;
        }

        .mini-icons {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 12px;
        }

        .mini-item {
            text-align: center;
            color: #475467;
            font-size: 13px;
        }

        .mini-item .mini-icon {
            display: block;
            font-size: 22px;
            margin-bottom: 6px;
        }

        .guide-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .guide-list li {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            color: #475467;
            line-height: 1.5;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .guide-list li::before {
            content: "✓";
            color: var(--green);
            font-weight: 900;
            flex-shrink: 0;
        }

        .info-note {
            background: #f6fbf8;
            border: 1px solid var(--green-border);
            border-radius: var(--radius-md);
            padding: 14px 18px;
            color: #475467;
            font-size: 14.5px;
            line-height: 1.55;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-note strong {
            color: var(--green-dark);
        }

        .error-box {
            background: var(--red-soft);
            border: 1px solid #f3c7c7;
            color: #7f1d1d;
            border-radius: var(--radius-md);
            padding: 14px 16px;
            margin-bottom: 18px;
            line-height: 1.55;
            font-size: 14.5px;
        }

        .hidden {
            display: none !important;
        }
        
        .official-source-box {
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px dashed rgba(31, 143, 95, 0.25);
        }

        .source-title {
            font-size: 13px;
            font-weight: 800;
            color: var(--green-dark);
            margin-bottom: 8px;
        }

        .source-link {
            display: block;
            width: 100%;
            padding: 9px 11px;
            margin-bottom: 8px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(31, 143, 95, 0.18);
            color: #146c47;
            text-decoration: none;
            font-size: 13.5px;
            line-height: 1.45;
            font-weight: 650;
            transition: 0.2s ease;
        }

        .source-link:hover {
            background: #eaf7f0;
            border-color: #1f8f5f;
            color: #0f6f41;
            transform: translateY(-1px);
        }

        .source-link::after {
            content: " ↗";
            font-weight: 800;
        }

        @media (max-width: 1050px) {
            .main-grid {
                grid-template-columns: 1fr 1fr;
                gap: 18px;
            }

            .result-panel {
                grid-column: span 2;
            }

            .guide-grid {
                grid-template-columns: 1fr;
            }

            .eco-badge {
                display: none;
            }
        }

        @media (max-width: 760px) {
            .page {
                width: 94%;
                margin: 14px auto 24px;
            }

            .top-header {
                align-items: flex-start;
                padding: 18px;
            }

            .brand {
                align-items: flex-start;
            }

            .brand-icon {
                width: 48px;
                height: 48px;
                font-size: 28px;
            }

            .main-grid {
                grid-template-columns: 1fr;
            }

            .result-panel {
                grid-column: auto;
            }

            .camera-actions {
                grid-template-columns: 1fr;
            }

            .mini-icons {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>

<body>
@php
    $resultData = session('result', $result ?? null);
    $errorMessage = session('error');
    $previewFromServer = session('preview_image', $previewImage ?? null);

    $groupLabelMap = [
        'Huu_co' => 'Rác hữu cơ',
        'Tai_che' => 'Rác tái chế',
        'Vo_co' => 'Rác vô cơ / nguy hại',
    ];

    $specificLabelMap = [
        'biological' => 'Rác hữu cơ',
        'plastic' => 'Nhựa',
        'paper' => 'Giấy',
        'cardboard' => 'Bìa carton',
        'metal' => 'Kim loại',
        'battery' => 'Pin',
        'trash' => 'Rác hỗn hợp',
        'brown-glass' => 'Thủy tinh nâu',
        'green-glass' => 'Thủy tinh xanh',
        'white-glass' => 'Thủy tinh trắng',
        'clothes' => 'Quần áo',
        'shoes' => 'Giày dép',
    ];

    $group = $resultData['class'] ?? null;
    $specific = strtolower($resultData['specific_class'] ?? '');
    $confidence = isset($resultData['confidence']) ? (float) $resultData['confidence'] : null;

    $groupDisplay = $groupLabelMap[$group] ?? 'Chưa xác định';
    $specificDisplay = $specificLabelMap[$specific] ?? ($resultData['specific_class'] ?? 'Chưa xác định');

    $mainGuide = [
        'Huu_co' => [
            'title' => 'Hướng xử lý rác hữu cơ',
            'tips' => [
                'Có thể ủ làm phân compost nếu là thức ăn thừa, rau củ hoặc lá cây.',
                'Bỏ vào thùng rác hữu cơ để tránh lẫn với rác tái chế.',
                'Nên xử lý sớm để hạn chế mùi hôi và côn trùng.',
            ],
        ],
        'Tai_che' => [
            'title' => 'Hướng tái chế',
            'tips' => [
                'Làm sạch và để khô trước khi bỏ vào nhóm rác tái chế.',
                'Phân loại riêng nhựa, giấy, kim loại, thủy tinh nếu có điều kiện.',
                'Không để lẫn thức ăn thừa hoặc dầu mỡ vào rác tái chế.',
            ],
        ],
        'Vo_co' => [
            'title' => 'Hướng xử lý',
            'tips' => [
                'Nếu là pin hoặc thiết bị điện tử nhỏ, cần thu gom riêng.',
                'Không bỏ rác nguy hại chung với rác sinh hoạt.',
                'Không tự ý đốt hoặc chôn lấp vì có thể gây ô nhiễm.',
            ],
        ],
    ];

    $guideByGroup = $group ? ($mainGuide[$group] ?? null) : null;

    $resultIcon = match($group) {
        'Huu_co' => '🌿',
        'Tai_che' => '♻️',
        'Vo_co' => '⚠️',
        default => '♻️',
    };
@endphp

<div class="page">
    <header class="top-header">
        <div class="brand">
            <div class="brand-icon">♻️</div>
            <div class="brand-text">
                <h1>Phân loại rác thải bằng AI</h1>
                <p>Nhận diện thông minh – Phân loại chính xác – Bảo vệ môi trường 🌿</p>
            </div>
        </div>

        <div class="eco-badge">
            <div class="leaf">🌱</div>
            <div>
                <strong>Vì một hành tinh xanh</strong>
                <span>Hành động từ hôm nay</span>
            </div>
        </div>
    </header>

    @if ($errorMessage)
        <div class="error-box">
            <strong>Lỗi:</strong> {{ $errorMessage }}
        </div>
    @endif

    <main class="main-grid">
        <section class="card">
            <div class="step-title">
                <div class="step-number">1</div>
                <h2>Tải ảnh rác lên</h2>
            </div>
            <p class="step-desc">Chọn ảnh từ máy hoặc chụp ảnh trực tiếp.</p>

            <form method="POST" action="{{ route('detect.upload') }}" enctype="multipart/form-data" id="uploadForm">
                @csrf

                <div class="upload-zone" id="uploadZone">
                    <div class="upload-icon">☁️</div>
                    <h3>Kéo thả ảnh vào đây</h3>
                    <p>hoặc chọn ảnh từ thiết bị</p>

                    <label for="imageInput" class="btn btn-primary">
                        ⬆️ Chọn ảnh từ máy
                    </label>

                    <input class="file-input" type="file" name="image" id="imageInput" accept="image/*" required>
                </div>

                <div class="or-divider">HOẶC</div>

                <button type="button" id="btnOpenCamera" class="btn btn-outline btn-full">
                    📷 Mở camera để chụp ảnh
                </button>

                <div id="cameraSection" class="camera-section">
                    <div class="camera-frame">
                        <video id="cameraVideo" autoplay playsinline></video>
                        <canvas id="cameraCanvas" class="hidden"></canvas>
                    </div>

                    <div class="camera-actions">
                        <button type="button" id="btnCapture" class="btn btn-primary">Chụp ảnh</button>
                        <button type="button" id="btnRetake" class="btn btn-gray hidden">Chụp lại</button>
                        <button type="button" id="btnCloseCamera" class="btn btn-gray">Đóng camera</button>
                        <button type="submit" id="btnSubmitCamera" class="btn btn-primary hidden">Gửi nhận diện</button>
                    </div>
                </div>

                <input type="hidden" name="captured_image" id="capturedImage">

                <div class="helper-text" id="uploadStatus">
                    Hỗ trợ: JPG, PNG, JPEG, WEBP. Ảnh rõ nét, đủ sáng giúp AI nhận diện chính xác hơn.
                </div>

                <div class="submit-area">
                    <button type="submit" id="btnUploadSubmit" class="btn btn-primary btn-full">
                        ✨ Nhận diện & Phân loại
                    </button>
                </div>
            </form>
        </section>

        <section class="card preview-card">
            <div class="step-title">
                <div class="step-number">2</div>
                <h2>Ảnh đang phân tích</h2>
            </div>

            <div class="preview-box">
                @if ($previewFromServer)
                    <img id="previewImage" src="{{ $previewFromServer }}" alt="Ảnh xem trước">
                    <div id="previewPlaceholder" class="preview-placeholder hidden">
                        <span class="icon">🖼️</span>
                        <strong>Ảnh của bạn sẽ hiển thị ở đây</strong>
                        <span>Sau khi tải lên hoặc chụp ảnh thành công.</span>
                    </div>
                @else
                    <img id="previewImage" class="hidden" alt="Ảnh xem trước">
                    <div id="previewPlaceholder" class="preview-placeholder">
                        <span class="icon">🖼️</span>
                        <strong>Ảnh của bạn sẽ hiển thị ở đây</strong>
                        <span>Sau khi tải lên hoặc chụp ảnh thành công.</span>
                    </div>
                @endif
            </div>

            <div class="analyzing-status" id="analysisStatus">
                {{ $resultData ? 'AI đã hoàn tất phân tích.' : 'AI sẵn sàng phân tích ảnh rác thải.' }}
            </div>
        </section>

        <section class="card result-panel">
            <div class="step-title">
                <div class="step-number">3</div>
                <h2>Kết quả phân loại</h2>
            </div>

            @if (!$resultData)
                <div class="result-empty">
                    Chưa có kết quả phân loại.<br>
                    Vui lòng chọn ảnh hoặc chụp ảnh để hệ thống nhận diện.
                </div>
            @else
                <div class="result-badge">
                    <div class="left">
                        <span class="icon">{{ $resultIcon }}</span>
                        <span>{{ $groupDisplay }}</span>
                    </div>
                    <span>🧾</span>
                </div>

                <div class="confidence-label">Loại rác cụ thể</div>
                <div style="font-size: 20px; font-weight: 850; margin-bottom: 16px;">
                    {{ $specificDisplay }}
                </div>

                <div class="confidence-label">Độ tin cậy</div>
                <div class="confidence-value">{{ number_format($confidence ?? 0, 2) }}%</div>
                <div class="confidence-bar">
                    <div class="confidence-fill" style="width: {{ min(100, max(0, $confidence ?? 0)) }}%;"></div>
                </div>

                <div class="quick-tip">
                    <h3>💡 Gợi ý nhanh</h3>

                    <ul>
                        @if (!empty($guideByGroup['tips']))
                            @foreach ($guideByGroup['tips'] as $tip)
                                <li>{{ $tip }}</li>
                            @endforeach
                        @else
                            <li>Kết quả AI chỉ mang tính hỗ trợ.</li>
                            <li>Nên kiểm tra lại nếu vật thể khó nhận diện.</li>
                            <li>Phân loại theo quy định tại địa phương.</li>
                        @endif
                    </ul>
                </div>
            @endif
        </section>
    </main>

    <section class="guide-section">
        <h2 class="guide-title">🌿 Hướng dẫn xử lý rác thải</h2>

        <div class="guide-grid">
            <div class="guide-card recycle">
                <h3>♻️ Tái chế</h3>
                <p>
                    Các loại rác có khả năng tái chế nên được làm sạch sơ bộ, giữ khô và phân loại riêng
                    để thuận tiện cho quá trình thu gom, tái chế.
                </p>

                <div class="mini-icons">
                    <div class="mini-item">
                        <span class="mini-icon">🧴</span>
                        Nhựa
                    </div>
                    <div class="mini-item">
                        <span class="mini-icon">📄</span>
                        Giấy
                    </div>
                    <div class="mini-item">
                        <span class="mini-icon">🥫</span>
                        Kim loại
                    </div>
                    <div class="mini-item">
                        <span class="mini-icon">🍾</span>
                        Thủy tinh
                    </div>
                </div>

                <div class="official-source-box">
                    <div class="source-title">Nguồn tham khảo chính thống</div>

                    <a class="source-link" href="https://vea.mae.gov.vn/chat-thai-ran-sinh-hoat/5389/mot-so-noi-dung-chinh-cua-du-thao-so-tay-huong-dan-phan-loai-chat-thai-ran-sinh-hoat-tai-nguon-theo-" target="_blank" rel="noopener noreferrer">
                        Cục Môi trường - Phân loại chất thải rắn sinh hoạt tại nguồn
                    </a>

                    <a class="source-link" href="https://www.epa.gov/recycle/how-do-i-recycle-common-recyclables" target="_blank" rel="noopener noreferrer">
                        US EPA - Hướng dẫn tái chế các vật liệu phổ biến
                    </a>
                </div>
            </div>

            <div class="guide-card process">
                <h3>🗑️ Xử lý</h3>
                <p>
                    Rác không thể tái chế hoặc rác sinh hoạt khác cần được xử lý đúng cách,
                    không trộn lẫn với rác có khả năng tái chế hoặc rác nguy hại.
                </p>

                <div class="mini-icons">
                    <div class="mini-item">
                        <span class="mini-icon">🧺</span>
                        Rác khác
                    </div>
                    <div class="mini-item">
                        <span class="mini-icon">🍃</span>
                        Hữu cơ
                    </div>
                    <div class="mini-item">
                        <span class="mini-icon">⚠️</span>
                        Nguy hại
                    </div>
                    <div class="mini-item">
                        <span class="mini-icon">🔋</span>
                        Pin
                    </div>
                </div>

                <div class="official-source-box">
                    <div class="source-title">Nguồn tham khảo chính thống</div>

                    <a class="source-link" href="https://vea.mae.gov.vn/tin-tuc-su-kien/10738/ha-noi-chuan-bi-nguon-luc-de-ap-dung-phan-loai-rac-tai-nguon" target="_blank" rel="noopener noreferrer">
                        Cục Môi trường - Quy định phân loại rác tại nguồn
                    </a>

                    <a class="source-link" href="https://www.epa.gov/recycle/how-do-i-recycle-common-recyclables" target="_blank" rel="noopener noreferrer">
                        US EPA - Hướng dẫn xử lý pin và vật liệu phổ biến
                    </a>
                </div>
            </div>

            <div class="guide-card note">
                <h3>⚠️ Lưu ý</h3>
                <p>
                    Kết quả AI chỉ mang tính hỗ trợ. Người dùng nên kiểm tra lại loại rác
                    và tuân theo quy định phân loại tại địa phương.
                </p>

                <ul class="guide-list">
                    <li>Phân loại rác tại nguồn trước khi bỏ vào thùng.</li>
                    <li>Giữ giấy sạch, khô; nhựa, kim loại, thủy tinh nên làm rỗng và rửa sơ bộ.</li>
                    <li>Không trộn pin, bóng đèn, thiết bị điện tử nhỏ với rác sinh hoạt thông thường.</li>
                </ul>

                <div class="official-source-box">
                    <div class="source-title">Nguồn tham khảo chính thống</div>

                    <a class="source-link" href="https://www.epa.gov/recycle/frequent-questions-recycling" target="_blank" rel="noopener noreferrer">
                        US EPA - Câu hỏi thường gặp về tái chế
                    </a>

                    <a class="source-link" href="https://www.unep.org/topics/waste" target="_blank" rel="noopener noreferrer">
                        UNEP - Thông tin tổng quan về quản lý chất thải
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="info-note">
        <span>ℹ️</span>
        <span>
            <strong>Lưu ý:</strong> Kết quả nhận diện từ AI chỉ mang tính chất hỗ trợ.
            Vui lòng kiểm tra và phân loại theo quy định địa phương.
        </span>
    </div>
</div>

<script>
    const imageInput = document.getElementById('imageInput');
    const uploadZone = document.getElementById('uploadZone');
    const previewImage = document.getElementById('previewImage');
    const previewPlaceholder = document.getElementById('previewPlaceholder');
    const analysisStatus = document.getElementById('analysisStatus');
    const uploadStatus = document.getElementById('uploadStatus');

    const uploadForm = document.getElementById('uploadForm');
    const btnUploadSubmit = document.getElementById('btnUploadSubmit');

    const btnOpenCamera = document.getElementById('btnOpenCamera');
    const btnCloseCamera = document.getElementById('btnCloseCamera');
    const cameraSection = document.getElementById('cameraSection');
    const cameraVideo = document.getElementById('cameraVideo');
    const cameraCanvas = document.getElementById('cameraCanvas');
    const btnCapture = document.getElementById('btnCapture');
    const btnRetake = document.getElementById('btnRetake');
    const btnSubmitCamera = document.getElementById('btnSubmitCamera');
    const capturedImage = document.getElementById('capturedImage');

    let stream = null;
    let cameraReady = false;
    let usingCamera = false;

    function showPreview(src) {
        previewImage.src = src;
        previewImage.classList.remove('hidden');

        if (previewPlaceholder) {
            previewPlaceholder.classList.add('hidden');
        }

        analysisStatus.innerText = 'Ảnh đã sẵn sàng. Bấm nhận diện để phân loại.';
    }

    function setLoading(button, text) {
        button.dataset.originalText = button.innerHTML;
        button.innerHTML = text;
        button.disabled = true;
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }

        cameraReady = false;
    }

    function resetCameraButtons() {
        cameraVideo.classList.remove('hidden');
        cameraCanvas.classList.add('hidden');

        btnCapture.classList.remove('hidden');
        btnRetake.classList.add('hidden');
        btnSubmitCamera.classList.add('hidden');
    }

    function closeCamera() {
        stopCamera();
        usingCamera = false;
        capturedImage.value = '';
        cameraSection.classList.remove('active');
        btnOpenCamera.classList.remove('hidden');
        resetCameraButtons();
        uploadStatus.innerText = 'Camera đã đóng. Có thể tải ảnh lên hoặc mở lại camera.';
    }

    async function openCamera() {
        try {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                alert('Trình duyệt không hỗ trợ camera.');
                return;
            }

            stopCamera();
            usingCamera = true;

            cameraSection.classList.add('active');
            btnOpenCamera.classList.add('hidden');
            resetCameraButtons();

            uploadStatus.innerText = 'Đang mở camera...';

            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: { ideal: 'environment' },
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            });

            cameraVideo.srcObject = stream;

            cameraVideo.onloadedmetadata = function () {
                cameraVideo.play();
                cameraReady = true;
                uploadStatus.innerText = 'Camera đã sẵn sàng. Đặt rác vào giữa khung và bấm chụp ảnh.';
            };
        } catch (error) {
            console.error(error);
            alert('Không thể mở camera. Vui lòng kiểm tra quyền truy cập camera.');
            closeCamera();
        }
    }

    function captureImage() {
        if (!cameraReady || !cameraVideo.videoWidth) {
            alert('Camera chưa sẵn sàng. Vui lòng đợi vài giây rồi thử lại.');
            return;
        }

        const context = cameraCanvas.getContext('2d');

        cameraCanvas.width = cameraVideo.videoWidth;
        cameraCanvas.height = cameraVideo.videoHeight;

        context.drawImage(cameraVideo, 0, 0, cameraCanvas.width, cameraCanvas.height);

        const imageData = cameraCanvas.toDataURL('image/png');
        capturedImage.value = imageData;

        cameraVideo.classList.add('hidden');
        cameraCanvas.classList.remove('hidden');

        btnCapture.classList.add('hidden');
        btnRetake.classList.remove('hidden');
        btnSubmitCamera.classList.remove('hidden');

        showPreview(imageData);
        stopCamera();

        uploadStatus.innerText = 'Đã chụp ảnh. Có thể gửi nhận diện hoặc chụp lại.';
    }

    function retakeImage() {
        capturedImage.value = '';
        openCamera();
    }

    function handleFile(file) {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

        if (!allowedTypes.includes(file.type)) {
            alert('Vui lòng chọn ảnh JPG, PNG, JPEG hoặc WEBP.');
            imageInput.value = '';
            return;
        }

        const maxSize = 10 * 1024 * 1024;

        if (file.size > maxSize) {
            alert('Ảnh quá lớn. Vui lòng chọn ảnh tối đa 10MB.');
            imageInput.value = '';
            return;
        }

        usingCamera = false;
        capturedImage.value = '';

        const reader = new FileReader();

        reader.onload = function (e) {
            showPreview(e.target.result);
            uploadStatus.innerText = 'Đã chọn ảnh. Bấm “Nhận diện & Phân loại” để gửi ảnh sang API AI.';
        };

        reader.readAsDataURL(file);
    }

    imageInput.addEventListener('change', function (event) {
        const file = event.target.files[0];

        if (!file) return;

        handleFile(file);
    });

    uploadZone.addEventListener('dragover', function (event) {
        event.preventDefault();
        uploadZone.classList.add('drag-over');
    });

    uploadZone.addEventListener('dragleave', function () {
        uploadZone.classList.remove('drag-over');
    });

    uploadZone.addEventListener('drop', function (event) {
        event.preventDefault();
        uploadZone.classList.remove('drag-over');

        const file = event.dataTransfer.files[0];

        if (!file) return;

        imageInput.files = event.dataTransfer.files;
        handleFile(file);
    });

    btnOpenCamera.addEventListener('click', openCamera);
    btnCloseCamera.addEventListener('click', closeCamera);
    btnCapture.addEventListener('click', captureImage);
    btnRetake.addEventListener('click', retakeImage);

    btnSubmitCamera.addEventListener('click', function () {
        if (!capturedImage.value) {
            alert('Vui lòng chụp ảnh trước khi gửi nhận diện.');
            return;
        }

        setLoading(btnSubmitCamera, 'Đang nhận diện...');
        analysisStatus.innerText = 'AI đang phân tích ảnh từ camera...';

        uploadForm.action = "{{ route('detect.camera') }}";
        uploadForm.submit();
    });

    uploadForm.addEventListener('submit', function (event) {
        if (usingCamera) {
            return;
        }

        if (!imageInput.files.length) {
            event.preventDefault();
            alert('Vui lòng chọn ảnh trước khi nhận diện.');
            return;
        }

        uploadForm.action = "{{ route('detect.upload') }}";
        setLoading(btnUploadSubmit, 'Đang nhận diện...');
        analysisStatus.innerText = 'AI đang phân tích ảnh đã tải lên...';
        uploadStatus.innerText = 'Đang gửi ảnh sang API AI, vui lòng đợi...';
    });

    window.addEventListener('beforeunload', stopCamera);
</script>
</body>
</html>