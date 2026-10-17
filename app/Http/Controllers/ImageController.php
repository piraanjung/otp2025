<?php

// app/Http/Controllers/ImageController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; // อย่าลืมใส่ use นี้ไว้ด้านบนสุดของไฟล์ด้วยนะครับ


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

        if ($imageData == false) {
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

    /**
     * รับรูปภาพที่ไม่รู้จัก (Not Found / Low Confidence) เพื่อนำไปเทรน AI ต่อ
     */
   public function uploadUnknownPhoto(Request $request)
    {
        try {
            // 1. รับค่าจาก JSON
            $base64Image = $request->input('image');
            $prob = $request->input('prob', 0);
            $predClass = $request->input('pred_class', 'unknown');

            // 2. เช็คว่ามีข้อมูล Base64 จริงๆ หรือไม่
            if (!$base64Image || !preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                return response()->json(['message' => 'รูปแบบ Base64 ไม่ถูกต้อง หรือไม่มีข้อมูลส่งมา'], 400);
            }

            // 3. ตัด Header ออกและแปลงกลับเป็นไฟล์ภาพ
            $imageData = substr($base64Image, strpos($base64Image, ',') + 1);
            $type = strtolower($type[1]); // ปกติจะได้คำว่า 'jpeg'

            $decodedData = base64_decode($imageData);
            if ($decodedData == false) {
                 return response()->json(['message' => 'ไม่สามารถถอดรหัสภาพได้'], 400);
            }

            // 4. จัดการชื่อไฟล์
            $cleanClass = preg_replace('/[^A-Za-z0-9\-]/', '', $predClass);
            $percent = round(floatval($prob) * 100); // แปลง 0.85 เป็น 85
            $filename = 'unknown-' . date('Ymd_His') . '-' . $cleanClass . '-' . $percent . '.' . ($type === 'jpeg' ? 'jpg' : $type);

            // 5. จัดการโฟลเดอร์
            $directory = public_path('unknown_bottles');
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            $path = $directory . '/' . $filename;

            // 6. บันทึกไฟล์ลงฮาร์ดดิสก์
            if (file_put_contents($path, $decodedData) === false) {
                throw new \Exception("ไม่สามารถเขียนไฟล์ลงดิสก์ได้");
            }

            return response()->json([
                'message' => 'บันทึกรูปภาพเรียบร้อย',
                'filename' => $filename
            ], 200);

        } catch (\Exception $e) {
            // ถ้ามี Error จะพ่นกลับไปให้แอปเห็นว่าพังที่ไหน
            return response()->json([
                'message' => 'เกิดข้อผิดพลาดที่ Server',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * ดึงรูปภาพ Unknown ทั้งหมดมาแสดงที่หน้า Web
     */
    public function indexUnknownImages()
    {
        $directory = public_path('unknown_bottles');
        $images = [];

        if (File::exists($directory)) {
            $files = File::files($directory);

            foreach ($files as $file) {
                $filename = $file->getFilename();

                // ถอดรหัสชื่อไฟล์ (ตัวอย่าง: unknown-20260220_143000-plastic-45.jpg)
                $parts = explode('-', str_replace('.jpg', '', $filename));
                $class = $parts[2] ?? 'N/A';
                $prob = $parts[3] ?? '0';

                $images[] = [
                    'filename' => $filename,
                    'url' => asset('unknown_bottles/' . $filename),
                    'class' => $class,
                    'prob' => $prob,
                    // ดึงเวลาแก้ไขไฟล์ล่าสุด
                    'date' => date('d/m/Y H:i', $file->getMTime()),
                ];
            }
        }

        // เรียงลำดับรูปใหม่ล่าสุดขึ้นก่อน
        usort($images, function($a, $b) {
            return strtotime(str_replace('/', '-', $b['date'])) - strtotime(str_replace('/', '-', $a['date']));
        });

        return view('unknown-images', compact('images'));
    }
}
