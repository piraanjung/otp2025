<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\FoodWaste\FoodWasteLog;
use App\Models\KeptKaya\KpPurchaseTransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpactController extends Controller
{
   public function index($userId)
    {
        // 1. ดึงยอด Carbon จากขยะเปียก (Food Waste)
        $foodWasteCarbon = FoodWasteLog::where('user_id', $userId)->sum('carbon_saved_kg');

        // 2. ดึงยอด Carbon จากขยะรีไซเคิล (Recycle Bank)
        $recycleCarbon = KpPurchaseTransactionDetail::whereHas('transaction', function($q) use ($userId) {
            $q->whereHas('userWastePreference', function($sq) use ($userId) {
                $sq->where('user_id', $userId);
            })->where('status', 1);
        })->sum('carbon_saved');

        $totalCo2Saved = $foodWasteCarbon + $recycleCarbon;

        // 3. สูตรคำนวณเทียบเคียง (Equivalents)
        // ต้นไม้ 1 ต้น ดูดซับ CO2 ได้ประมาณ 12 kg/ปี
        $trees = $totalCo2Saved > 0 ? ($totalCo2Saved / 12) : 0;
        // การใช้น้ำมัน 1 ลิตร ปล่อย CO2 ประมาณ 2.3 kg
        $fuelSaved = $totalCo2Saved > 0 ? ($totalCo2Saved / 2.3) : 0;

        return view('keptkayas.impact', compact('totalCo2Saved', 'foodWasteCarbon', 'recycleCarbon', 'trees', 'fuelSaved'));
    }
}
