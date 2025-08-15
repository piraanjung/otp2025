<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\Admin\Staff;
use App\Models\KeptKaya\KpPurchaseShop;
use Illuminate\Http\Request;
use App\Models\KeptKaya\KpSellTransaction;
use App\Models\KeptKaya\KpSellDetail;
use App\Models\KeptKaya\KpTbankItems;
use App\Models\KeptKaya\TbankItemUnit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class KpSellController extends Controller
{
    /**
     * Show the sell form to create a new transaction.
     *
     * @return \Illuminate\View\View
     */
    public function showSellForm()
    {
        // Fetch necessary data for the form
        $recycleItems = KpTbankItems::with('units')->get(); // Load all items with their units
        $staffs = Staff::all(); // Get users with 'staff' role
        $shops =  KpPurchaseShop::where('status', 'active')->get();
        return view('keptkaya.sell.sell_form', compact('recycleItems', 'staffs', 'shops'));
    }

    /**
     * Store the sell transaction and its details from the form.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeSellTransaction(Request $request)
    {
        $validated = $request->validate([
            'shop_name' => 'required',
            'sell_date' => 'required|date',
            'details' => 'required|array|min:1',
            'details.*.kp_recycle_item_id' => 'required|exists:kp_tbank_items,id',
            'details.*.weight' => 'required|numeric|min:0.01',
            'details.*.amount' => 'required|numeric|min:0.01',
            'details.*.price_per_unit' => 'required|numeric|min:0',
            'details.*.comment' => 'nullable|string',
            'recorder_id' => 'required|exists:staffs,user_id',
        ]);
        $totalWeight = array_sum(array_column($validated['details'], 'weight'));
        $totalAmount = array_sum(array_column($validated['details'], 'amount'));

        DB::beginTransaction();
        try {
            // 1. Create the main sell transaction
            $transaction = KpSellTransaction::create([
                'kp_u_trans_no' => 'S-' . Carbon::now()->format('YmdHis') . Str::random(4),
                'shop_id_fk' => $validated['shop_name'],
                'transaction_date' => $validated['sell_date'],
                'total_weight' => $totalWeight,
                'total_amount' => $totalAmount,
                'recorder_id' => $validated['recorder_id'],
            ]);

            // 2. Create the sell details for each item in the transaction
            foreach ($validated['details'] as $detail) {

                KpSellDetail::create([
                    'kp_sell_trans_id' => $transaction->id,
                    'kp_recycle_item_id' => $detail['kp_recycle_item_id'],
                    'weight' => $detail['weight'],
                    'price_per_unit' => $detail['price_per_unit'],
                    'amount' => $detail['amount'],
                    'comment' => $detail['comment'],
                ]);
            }

            DB::commit();
            return redirect()->route('keptkaya.sell.history')->with('success', 'บันทึกการขายขยะเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error saving sell transaction: " . $e->getMessage());
            return back()->with('error', 'เกิดข้อผิดพลาดในการบันทึกการขาย: ' . $e->getMessage());
        }
    }

     
        public function showSellHistory()
        {
            $transactions = KpSellTransaction::with(['recorder'])->orderByDesc('transaction_date')->paginate(20);
            return view('keptkaya.sell.history', compact('transactions'));
        }
        public function showReceipt(KpSellTransaction $transaction)
        {
            $transaction->load(['details.item', 'recorder']);
            return view('keptkaya.sell.receipt', compact('transaction'));
        }

        public function destroy(Request $request, $transaction){

        }
}
