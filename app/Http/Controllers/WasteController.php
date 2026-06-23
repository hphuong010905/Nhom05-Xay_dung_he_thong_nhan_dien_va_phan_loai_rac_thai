<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        // Kết quả giả để test giao diện trước khi có API AI của Ngọc
        return back()->with([
            'class' => 'Tai_che',
            'display_class' => 'Rác Tái Chế',
            'confidence' => 96,
            'suggestion' => 'Vui lòng bỏ vào thùng rác tái chế.',
        ]);
    }

    public function detectCamera(Request $request)
    {
        $request->validate([
            'image' => 'required|string',
        ]);

        // Kết quả giả để test camera trước khi ghép API AI thật
        return response()->json([
            'success' => true,
            'class' => 'Tai_che',
            'display_class' => 'Rác Tái Chế',
            'confidence' => 96,
            'suggestion' => 'Vui lòng bỏ vào thùng rác tái chế.',
        ]);
    }
}