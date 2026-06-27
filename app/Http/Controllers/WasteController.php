<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WasteController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function detectUpload(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
            ]);

            $image = $request->file('image');

            $previewImage = 'data:' . $image->getMimeType() . ';base64,' . base64_encode(
                file_get_contents($image->getRealPath())
            );

            $aiResult = $this->sendImageToAiApi(
                $image->getRealPath(),
                $image->getClientOriginalName()
            );

            if (!$aiResult['success']) {
                return redirect()
                    ->route('home')
                    ->with('error', $aiResult['message'])
                    ->with('preview_image', $previewImage);
            }

            return redirect()
                ->route('home')
                ->with('result', $aiResult['data'])
                ->with('preview_image', $previewImage);

        } catch (\Throwable $e) {
            Log::error('Lỗi nhận diện ảnh tải lên: ' . $e->getMessage());

            return redirect()
                ->route('home')
                ->with('error', 'Lỗi khi xử lý ảnh tải lên: ' . $e->getMessage());
        }
    }

    public function detectCamera(Request $request)
    {
        try {
            $request->validate([
                'captured_image' => 'required|string',
            ]);

            $base64Image = $request->input('captured_image');

            if (!str_contains($base64Image, ',')) {
                return redirect()
                    ->route('home')
                    ->with('error', 'Dữ liệu ảnh từ camera không hợp lệ.');
            }

            [$meta, $imageData] = explode(',', $base64Image, 2);

            $binaryImage = base64_decode($imageData);

            if ($binaryImage === false) {
                return redirect()
                    ->route('home')
                    ->with('error', 'Không thể giải mã ảnh từ camera.');
            }

            $tempDir = storage_path('app/temp');

            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0777, true);
            }

            $tempPath = $tempDir . '/camera_' . time() . '.png';

            file_put_contents($tempPath, $binaryImage);

            $aiResult = $this->sendImageToAiApi($tempPath, 'camera_image.png');

            if (file_exists($tempPath)) {
                unlink($tempPath);
            }

            if (!$aiResult['success']) {
                return redirect()
                    ->route('home')
                    ->with('error', $aiResult['message'])
                    ->with('preview_image', $base64Image);
            }

            return redirect()
                ->route('home')
                ->with('result', $aiResult['data'])
                ->with('preview_image', $base64Image);

        } catch (\Throwable $e) {
            Log::error('Lỗi nhận diện ảnh camera: ' . $e->getMessage());

            return redirect()
                ->route('home')
                ->with('error', 'Lỗi khi xử lý ảnh camera: ' . $e->getMessage());
        }
    }

    private function sendImageToAiApi(string $imagePath, string $fileName): array
    {
        try {
            $apiBaseUrl = rtrim(env('AI_API_URL', 'http://127.0.0.1:8000'), '/');
            $apiUrl = $apiBaseUrl . '/predict';

            if (!file_exists($imagePath)) {
                return [
                    'success' => false,
                    'message' => 'Không tìm thấy file ảnh để gửi sang API AI.',
                ];
            }

            $response = Http::timeout(60)
                ->attach(
                    'file',
                    fopen($imagePath, 'r'),
                    $fileName
                )
                ->post($apiUrl);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => 'API AI trả lỗi HTTP ' . $response->status() . ': ' . $response->body(),
                ];
            }

            $json = $response->json();

            if (!is_array($json)) {
                return [
                    'success' => false,
                    'message' => 'API AI không trả về dữ liệu JSON hợp lệ. Nội dung trả về: ' . $response->body(),
                ];
            }

            if (isset($json['success']) && $json['success'] === false) {
                return [
                    'success' => false,
                    'message' => $json['message'] ?? 'API AI báo lỗi nhưng không có nội dung chi tiết.',
                ];
            }

            $class = $json['class'] ?? null;
            $specificClass = $json['specific_class'] ?? null;
            $confidence = $json['confidence'] ?? null;

            if ($class === null) {
                return [
                    'success' => false,
                    'message' => 'API AI chưa trả về trường class. Dữ liệu nhận được: ' . json_encode($json, JSON_UNESCAPED_UNICODE),
                ];
            }

            if ($confidence === null) {
                $confidence = 0;
            }

            $confidence = (float) $confidence;

            if ($confidence <= 1) {
                $confidence = $confidence * 100;
            }

            return [
                'success' => true,
                'data' => [
                    'class' => $class,
                    'specific_class' => $specificClass,
                    'confidence' => round($confidence, 2),
                ],
            ];

        } catch (\Throwable $e) {
            Log::error('Lỗi gọi API AI: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Không gọi được API AI. Kiểm tra API Flask đã chạy ở http://127.0.0.1:8000 chưa. Chi tiết lỗi: ' . $e->getMessage(),
            ];
        }
    }
}