<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nhận diện và phân loại rác thải</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
            Nhận diện rác thải AI
        </a>
    </div>
</nav>

<div class="container py-5">
    <div class="text-center mb-4">
        <h1 class="fw-bold text-success">Hệ thống nhận diện và phân loại rác thải</h1>
        <p class="text-muted">
            Ứng dụng Web hỗ trợ phân loại rác thải sinh hoạt thành 3 nhóm:
            rác hữu cơ, rác tái chế và rác vô cơ/nguy hại.
        </p>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <ul class="nav nav-tabs mb-4" id="wasteTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="upload-tab" data-bs-toggle="tab"
                            data-bs-target="#upload" type="button" role="tab">
                        Tải ảnh
                    </button>
                </li>

                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="camera-tab" data-bs-toggle="tab"
                            data-bs-target="#camera" type="button" role="tab">
                        Camera
                    </button>
                </li>
            </ul>

            <div class="tab-content">

                <!-- TAB TẢI ẢNH -->
                <div class="tab-pane fade show active" id="upload" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="fw-bold mb-3">Nhận diện qua ảnh tải lên</h4>

                            <form action="{{ route('detect.upload') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Chọn ảnh rác thải</label>
                                    <input type="file" name="image" class="form-control" accept="image/*" required>
                                </div>

                                <button type="submit" class="btn btn-success w-100">
                                    Nhận diện
                                </button>
                            </form>

                            @error('image')
                                <div class="alert alert-danger mt-3">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mt-4 mt-md-0">
                            <h4 class="fw-bold mb-3">Kết quả</h4>

                            @if(session('display_class'))
                                <div class="alert alert-success">
                                    <h5 class="fw-bold">{{ session('display_class') }}</h5>
                                    <p class="mb-1">
                                        Độ tin cậy:
                                        <strong>{{ session('confidence') }}%</strong>
                                    </p>
                                    <p class="mb-0">
                                        {{ session('suggestion') }}
                                    </p>
                                </div>
                            @else
                                <div class="alert alert-secondary">
                                    Chưa có kết quả. Vui lòng chọn ảnh để nhận diện.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- TAB CAMERA -->
                <div class="tab-pane fade" id="camera" role="tabpanel">
                    <div class="row">
                        <div class="col-md-7">
                            <h4 class="fw-bold mb-3">Nhận diện bằng camera</h4>

                            <video id="video" class="w-100 rounded bg-dark" autoplay playsinline></video>
                            <canvas id="canvas" style="display:none;"></canvas>

                            <div class="mt-3 d-flex gap-2">
                                <button id="btnStart" class="btn btn-success">
                                    Mở camera
                                </button>

                                <button id="btnDetect" class="btn btn-primary">
                                    📸 Chụp & Nhận diện
                                </button>

                                <button id="btnStop" class="btn btn-danger">
                                    Tắt camera
                                </button>
                            </div>
                        </div>

                        <div class="col-md-5 mt-4 mt-md-0">
                            <h4 class="fw-bold mb-3">Kết quả camera</h4>

                            <div id="cameraResult" class="alert alert-secondary">
                                Chưa có kết quả. Vui lòng mở camera và bấm chụp.
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const btnStart = document.getElementById('btnStart');
    const btnDetect = document.getElementById('btnDetect');
    const btnStop = document.getElementById('btnStop');
    const cameraResult = document.getElementById('cameraResult');

    let stream = null;

    btnStart.addEventListener('click', async function () {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = stream;
        } catch (error) {
            alert('Không thể mở camera. Vui lòng kiểm tra quyền truy cập camera.');
        }
    });

    btnStop.addEventListener('click', function () {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            video.srcObject = null;
            stream = null;
        }
    });

    btnDetect.addEventListener('click', async function () {
        if (!stream) {
            alert('Vui lòng mở camera trước.');
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        const imageData = canvas.toDataURL('image/png');

        cameraResult.className = 'alert alert-warning';
        cameraResult.innerHTML = 'Đang nhận diện...';

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
                cameraResult.className = 'alert alert-success';
                cameraResult.innerHTML = `
                    <h5 class="fw-bold">${data.display_class}</h5>
                    <p class="mb-1">Độ tin cậy: <strong>${data.confidence}%</strong></p>
                    <p class="mb-0">${data.suggestion}</p>
                `;
            } else {
                cameraResult.className = 'alert alert-danger';
                cameraResult.innerHTML = 'Có lỗi xảy ra khi nhận diện.';
            }
        } catch (error) {
            cameraResult.className = 'alert alert-danger';
            cameraResult.innerHTML = 'Không thể gửi ảnh từ camera về hệ thống.';
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>