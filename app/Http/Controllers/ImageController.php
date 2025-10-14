<?php

// app/Http/Controllers/ImageController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// ไม่ต้องใช้ use Illuminate\Support\Facades\Storage; แล้ว

class ImageController extends Controller
{
    /**
     * รับรูปภาพ Base64 จาก Frontend และบันทึกใน public/bottles
     */
    public function uploadPhoto(Request $request)
    {
        $base64Image = $request->input('image');
        
        // 1. ถอดรหัส Base64 (เหมือนเดิม)
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            $imageData = substr($base64Image, strpos($base64Image, ',') + 1);
            $type = strtolower($type[1]);
        } else {
            return response()->json(['message' => 'Base64 data format not supported.'], 400);
        }
        
        $imageData = base64_decode($imageData);
        
        if ($imageData === false) {
             return response()->json(['message' => 'Failed to decode Base64 data.'], 400);
        }

        $filename = 'bottle-' . time() . '-' . uniqid() . '.' . ($type === 'jpeg' ? 'jpg' : $type);
        
        // 2. กำหนด Path ปลายทาง
        // public_path() ชี้ไปที่โฟลเดอร์ public/
        $path = public_path('bottles/' . $filename); 

        // 3. บันทึกไฟล์โดยใช้ file_put_contents()
        // นี่คือจุดที่แตกต่างจากการใช้ Storage::put()
        try {
            // ตรวจสอบว่าโฟลเดอร์ bottles มีอยู่ไหม ถ้าไม่ก็สร้าง (เผื่อกรณีลืมสร้าง)
            if (!is_dir(public_path('bottles'))) {
                mkdir(public_path('bottles'), 0777, true);
            }
            
            file_put_contents($path, $imageData);
            
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to save file to public/bottles.', 'error' => $e->getMessage()], 500);
        }
        
        /// **เพิ่ม Logic การคำนวณเงิน (ตรงนี้คือการจำลอง)**
        $calculatedAmount = 5.00; // สมมติว่าขวดนี้มีมูลค่า 5 บาท
        
        $url = asset('bottles/' . $filename);

        return response()->json([
            'message' => 'Image uploaded successfully to public folder', 
            'filename' => $filename,
            'url' => $url, // URL ของรูปภาพ
            'amount' => $calculatedAmount // จำนวนเงินที่ได้รับ
        ]);
    }
}