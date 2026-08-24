<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\FoodWaste\FoodWasteLog;
use App\Models\KeptKaya\KpPurchaseTransaction;
use App\Models\KeptKaya\KpPurchaseTransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpactController extends Controller
{
   public function index($pref_id)
    {
        // 1. ดึงยอด Carbon จากขยะเปียก (Food Waste)
        $foodWasteCarbon = FoodWasteLog::where('user_id', $pref_id)->sum('carbon_saved_kg');

        // 2. ดึงยอด Carbon จากขยะรีไซเคิล (Recycle Bank)
        $transaction = KpPurchaseTransaction::where('kp_user_w_pref_id_fk', $pref_id)
            ->with('details')
            ->get()->first();
        $recycleCarbon = collect($transaction->details)->sum('carbon_saved');


        $totalCo2Saved = $foodWasteCarbon + $recycleCarbon;

        // 3. สูตรคำนวณเทียบเคียง (Equivalents)
        // ต้นไม้ 1 ต้น ดูดซับ CO2 ได้ประมาณ 12 kg/ปี
        $trees = $totalCo2Saved > 0 ? ($totalCo2Saved / 12) : 0;
        // การใช้น้ำมัน 1 ลิตร ปล่อย CO2 ประมาณ 2.3 kg
        $fuelSaved = $totalCo2Saved > 0 ? ($totalCo2Saved / 2.3) : 0;

        return view('keptkayas.impact', compact('totalCo2Saved', 'pref_id', 'foodWasteCarbon', 'recycleCarbon', 'trees', 'fuelSaved'));
    }
}
