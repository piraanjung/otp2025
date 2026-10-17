<?php

namespace App\Http\Controllers\KeptKaya;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\KeptKaya\KpBarcodeList;

class BarcodeController extends Controller
{
    public function search(Request $request)
    {
        // 1. ตรวจสอบค่าที่ส่งมา
        $request->validate([
            'barcode_number' => 'required|string|max:255',
        ]);

        $barcode = $request->input('barcode_number');

        // 2. ค้นหาในฐานข้อมูล
        $product = KpBarcodeList::where('barcode_number', $barcode)->first();

        // 3. ส่งข้อมูลกลับในรูปแบบ JSON
        if ($product) {
            return response()->json([
                'status' => 'success',
                'product' => $product
            ]);
        }

        return response()->json([
            'status' => 'not_found',
            'message' => 'ไม่พบ Barcode: ' . $barcode
        ], 404); // ส่งสถานะ 404 หากไม่พบ
    }
}