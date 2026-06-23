<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WasteController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function detectUpload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'image.required' => 'Vui lòng chọn ảnh cần nhận diện.',
            'image.image' => 'Tệp tải lên phải là hình ảnh.',
            'image.mimes' => 'Ảnh phải có định dạng jpg, jpeg, png hoặc webp.',
            'image.max' => 'Dung lượng ảnh không được vượt quá 5MB.',
        ]);

        // Khi chưa có API AI thật thì dùng kết quả giả
        if (config('services.ai_api.use_fake')) {
            $result = [
                'class' => 'Tai_che',
                'confidence' => 0.96,
            ];
        } else {
            try {
                $image = $request->file('image');

                $response = Http::attach(
                    'file',
                    file_get_contents($image->getRealPath()),
                    $image->getClientOriginalName()
                )->post(config('services.ai_api.url') . '/predict');

                if (!$response->successful()) {
                    return back()->with('error', 'API AI chưa phản hồi thành công. Vui lòng kiểm tra lại API của Ngọc.');
                }

                $result = $response->json();
            } catch (\Exception $e) {
                return back()->with('error', 'Không thể kết nối đến API AI: ' . $e->getMessage());
            }
        }

        $displayData = $this->formatResult($result);

        return back()->with([
            'class' => $displayData['class'],
            'display_class' => $displayData['display_class'],
            'confidence' => $displayData['confidence_percent'],
            'suggestion' => $displayData['suggestion'],
        ]);
    }

    public function detectCamera(Request $request)
    {
        $request->validate([
            'image' => 'required|string',
        ]);

        // Khi chưa có API AI thật thì dùng kết quả giả
        if (config('services.ai_api.use_fake')) {
            $result = [
                'class' => 'Tai_che',
                'confidence' => 0.96,
            ];
        } else {
            try {
                $imageData = $request->input('image');

                // Xóa phần mở đầu dạng: data:image/png;base64,
                $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
                $imageBinary = base64_decode($imageData);

                $response = Http::attach(
                    'file',
                    $imageBinary,
                    'camera-frame.png'
                )->post(config('services.ai_api.url') . '/predict');

                if (!$response->successful()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'API AI chưa phản hồi thành công.',
                    ], 500);
                }

                $result = $response->json();
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể kết nối đến API AI: ' . $e->getMessage(),
                ], 500);
            }
        }

        $displayData = $this->formatResult($result);

        return response()->json([
            'success' => true,
            'class' => $displayData['class'],
            'display_class' => $displayData['display_class'],
            'confidence' => $displayData['confidence_percent'],
            'suggestion' => $displayData['suggestion'],
        ]);
    }

    private function formatResult(array $result)
    {
        $class = $result['class'] ?? 'Khong_xac_dinh';
        $confidence = $result['confidence'] ?? 0;

        // Nếu API trả confidence dạng 0.96 thì đổi thành 96
        // Nếu API trả sẵn 96 thì giữ nguyên
        $confidencePercent = $confidence <= 1
            ? round($confidence * 100, 2)
            : round($confidence, 2);

        $displayClass = 'Không xác định';
        $suggestion = 'Cần kiểm tra lại kết quả phân loại.';

        if ($class === 'Huu_co') {
            $displayClass = 'Rác Hữu Cơ';
            $suggestion = 'Vui lòng bỏ vào thùng rác hữu cơ hoặc thùng rác màu xanh lá.';
        } elseif ($class === 'Tai_che') {
            $displayClass = 'Rác Tái Chế';
            $suggestion = 'Vui lòng bỏ vào thùng rác tái chế.';
        } elseif ($class === 'Vo_co') {
            $displayClass = 'Rác Vô Cơ / Nguy Hại';
            $suggestion = 'Vui lòng bỏ vào thùng rác vô cơ hoặc khu vực xử lý rác nguy hại.';
        }

        return [
            'class' => $class,
            'display_class' => $displayClass,
            'confidence_percent' => $confidencePercent,
            'suggestion' => $suggestion,
        ];
    }
}