<?php

namespace App\Http\Controllers;
# import thư viện. Request dùng để lấy dữ liệu người dùng gửi lên
# ví dụ ảnh upload hoặc ảnh camera. HTTP dùng để Laravel gửi request
# sang API python của Ngọc
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WasteController extends Controller
{
    public function index() # Hàm dùng để hiển thị giao diện chính
    {
        return view('home');
    }

    # hàm xử lý ảnh do người dùng gửi lên từ tab "Tải ảnh"
    public function detectUpload(Request $request)
    {
        $request->validate([
            # bắt buộc chọn phải là ảnh | file tải lên phải là hình ảnh | chỉ cho phép các định dạng ảnh này | Dung lượng ảnh tối đa là 5MB
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',# Kiểm tra ảnh có hợp lệ không
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
                # Lấy file ảnh do người dùng tải lên
                $image = $request->file('image');
                
                # Gửi ảnh sang API AI
                $response = Http::attach(
                    'file',
                    file_get_contents($image->getRealPath()), # Đọc nội dung thật của file ảnh
                    $image->getClientOriginalName()# Lấy tên file ảnh gốc
                )->post(config('services.ai_api.url') . '/predict');# Gửi ảnh bằng phương thức POST đến API

                # Kiểm tra API có phản hồi thành công không
                if (!$response->successful()) {
                    return back()->with('error', 'API AI chưa phản hồi thành công. Vui lòng kiểm tra lại API của Ngọc.');
                }

                # Nhận JSON từ API
                $result = $response->json();
            } catch (\Exception $e) {
                return back()->with('error', 'Không thể kết nối đến API AI: ' . $e->getMessage());
            }
        }
        # Nếu API trả về đúng định dạng JSON thì Laravel chuyển thành mảng PHP
        $displayData = $this->formatResult($result);

        return back()->with([
            'class' => $displayData['class'],
            'display_class' => $displayData['display_class'],
            'confidence' => $displayData['confidence_percent'],
            'suggestion' => $displayData['suggestion'],
        ]);
    }

    # Hàm xử lý ảnh chụp từ camera
    # Do camera nhận ảnh dưới dạng chuỗi Base64 nên cần xử lý trước khi gửi sang API
    public function detectCamera(Request $request)
    {
        $request->validate([
            'image' => 'required|string', # hàm xử lý ảnh
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
                $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);# Xóa phần đầu <data:image/png;base64,> chỉ giữ lại phần dự liệu ảnh thật
                $imageBinary = base64_decode($imageData);# chuyển thành dữ liệu dạng nhị phân

                $response = Http::attach(
                    'file',
                    $imageBinary,
                    'camera-frame.png'
                )->post(config('services.ai_api.url') . '/predict'); # Dùng hàm POST để gửi ảnh sang API

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

        # Hàm dùng để chuẩn hóa kết quả từ API
        $displayData = $this->formatResult($result);

        return response()->json([
            'success' => true,
            'class' => $displayData['class'],
            'display_class' => $displayData['display_class'],
            'confidence' => $displayData['confidence_percent'],
            'suggestion' => $displayData['suggestion'],
        ]);
    }

    # Hàm để đổi dữ liệu. Lấy nhãn rác từ API. Nếu API không trả class thì dùng mặc định là "Không xác định"
    private function formatResult(array $result)
    {
        $class = $result['class'] ?? 'Khong_xac_dinh';
        $confidence = $result['confidence'] ?? 0;# Lấy độ tin cậy nếu không có thì để là 0

        // Nếu API trả confidence dạng 0.96 thì đổi thành 96
        // Nếu API trả sẵn 96 thì giữ nguyên
        $confidencePercent = $confidence <= 1
            ? round($confidence * 100, 2) # Xử lý giao diện hiển thị thành 96%
            : round($confidence, 2);

        $displayClass = 'Không xác định';
        $suggestion = 'Cần kiểm tra lại kết quả phân loại.';
        
        # Nếu API trả Huu_co thì hiển thị Rác hữu cơ, tương tự với 2 loại rác còn lại
        if ($class === 'Huu_co') {
            $displayClass = 'Rác hữu cơ';
            $suggestion = 'Vui lòng bỏ vào thùng rác hữu cơ hoặc thùng rác màu xanh lá.';
        } elseif ($class === 'Tai_che') {
            $displayClass = 'Rác cái chế';
            $suggestion = 'Vui lòng bỏ vào thùng rác tái chế.';
        } elseif ($class === 'Vo_co') {
            $displayClass = 'Rác vô cơ / Nguy hại';
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