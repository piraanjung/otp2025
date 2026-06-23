<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;
use App\Http\Controllers\Controller;
class OcrController extends Controller
{
    public function readMeter(Request $request)
    {
        // ตรวจสอบว่ามีการอัปโหลดไฟล์มาหรือไม่
        if (!$request->hasFile('image')) {
            return response()->json(['success' => false, 'message' => 'No image uploaded.'], 400);
        }

        $imageFile = $request->file('image');

        // ประมวลผลรูปภาพด้วย Tesseract
        try {
            $result = (new TesseractOCR())
                ->image($imageFile->getRealPath()) // รับ path ของไฟล์รูปภาพที่อัปโหลด
                ->lang('eng')
                ->run();
            
            // กรองเฉพาะตัวเลขจากผลลัพธ์
            preg_match_all('/\d+/', $result, $matches);
            $numbers = implode(', ', $matches[0]);

            return response()->json([
                'success' => true,
                'numbers' => $numbers,
                'full_text' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'OCR processing failed.'], 500);
        }
    }
}
