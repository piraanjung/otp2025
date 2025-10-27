<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use App\Models\Admin\Organization;
use App\Models\KeptKaya\KpTbankItems;
use App\Models\KeptKaya\KpTbankItemsPriceAndPoint;
use App\Models\KeptKaya\KpTbankUnits;
use App\Models\Admin\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KpTbankPriceController extends Controller
{
    /**
     * Display a listing of prices.
     */
    public function index()
    {
        $prices = KpTbankItemsPriceAndPoint::with(['item', 'kp_units_info'])
            ->where('org_id_fk', Auth::user()->org_id_fk)
            ->where('status', 'active')
            ->orderByDesc('effective_date')
            ->paginate(20);
        return view('keptkayas.tbank.prices.index', compact('prices'));
    }

    /**
     * Show the form for creating a new price.
     */
    public function create()
    {
        $price = new KpTbankItemsPriceAndPoint();
        $items = KpTbankItems::all();
        $units = KpTbankUnits::all();
        $recorders = Staff::all();

        return view('keptkayas.tbank.prices.create', compact('price', 'items', 'units', 'recorders'));
    }

    /**
     * Store a newly created price in storage.
     */

    public function store(Request $request)
    {
        // 1. Validation
        $request->validate([
            // Validation สำหรับ Global Fields
            'recorder_id' => 'nullable|exists:users,id',
            'comment' => 'nullable|string|max:2000',
            'is_active' => 'nullable|boolean',

            // Validation สำหรับ Item Block (ใช้ Wildcard *)
            'items_data' => 'required|array|min:1',
            'items_data.*.kp_items_idfk' => 'required|exists:kp_tbank_items,id',
            'items_data.*.effective_date' => 'required|date',
            // end_date เป็น nullable เพราะอาจเป็น null เมื่อเลือก 'ตลอดไป'
            'items_data.*.end_date' => 'nullable|date|after_or_equal:items_data.*.effective_date',
            'items_data.*.is_forever_active' => 'nullable|boolean',

            // Validation สำหรับ หน่วยนับ (Price Tiers)
            'items_data.*.units_data' => 'required|array|min:1',
            'items_data.*.units_data.*.kp_units_idfk' => 'required|exists:kp_tbank_items_units,id',
            'items_data.*.units_data.*.price_for_member' => 'required|numeric|min:0',
            'items_data.*.units_data.*.price_from_dealer' => 'required|numeric|min:0',
            'items_data.*.units_data.*.point' => 'required|integer|min:0',
        ]);

        $itemsData = $request->input('items_data');

        // 2. วนลูปบันทึกข้อมูล
        foreach ($itemsData as $itemData) {

            // --- A. จัดการ Logic วันที่สิ้นสุด (ตลอดไป) ---
            $endDate = null;
            if (!isset($itemData['is_forever_active']) || $itemData['is_forever_active'] != 1) {
                // ถ้าไม่ได้เลือก 'ตลอดไป' ให้ใช้ค่า end_date ที่ส่งมา (ซึ่งอาจเป็น null ถ้าไม่ได้กรอก)
                $endDate = $itemData['end_date'];
            }


            $kp_units_idfkArr = KpTbankItemsPriceAndPoint::where('kp_items_idfk', $itemData['kp_items_idfk'])
                ->where('status', 'active')
                ->get()->pluck('kp_units_idfk');


            foreach ($itemData['units_data'] as $unit) {

                if (in_array($unit['kp_units_idfk'], collect($kp_units_idfkArr)->toArray())) {
                    //ให้ inactive ตัวเก่า
                    KpTbankItemsPriceAndPoint::where('kp_items_idfk', $itemData['kp_items_idfk'])
                        ->where('kp_units_idfk', $unit['kp_units_idfk'])->update([
                            'status'        => 'inactive',
                            'updated_at'    => date('Y-m-d H:i:s'),
                        ]);
                }
                KpTbankItemsPriceAndPoint::create([
                    'kp_items_idfk'         =>  $itemData['kp_items_idfk'],
                    'price_from_dealer'     => $unit['price_from_dealer'],
                    'price_for_member'      => $unit['price_for_member'],
                    'point'                 => $unit['point'],
                    'type'                  => 'tbank',
                    'kp_units_idfk'         => $unit['kp_units_idfk'],
                    'status'                => 'active',
                    'deleted'               => '0',
                    'recorder_id'           => $request->get('recorder_id') ?? Auth::id(),
                    'effective_date'        => $itemData['effective_date'],
                    'org_id_fk'             => Auth::user()->org_id_fk,
                    'end_date'              => $endDate,
                    'created_at'            => date('Y-m-d H:i:s'),
                    'updated_at'            => date('Y-m-d H:i:s'),
                ]);
            }
            
        }

        return redirect()->route('keptkayas.tbank.prices.index')
            ->with('success', 'บันทึกรายการกำหนดราคาหลายรายการเรียบร้อยแล้ว');
    }
    // public function store(Request $request)
    // {
    //     // return $request;
    //     $validated = $request->validate([
    //         'kp_items_idfk' => 'required|exists:kp_tbank_items,id',
    //         // 'price_from_dealer' => 'required|numeric|min:0',
    //         // 'price_for_member' => 'required|numeric|min:0',
    //         // 'point' => 'nullable|integer|min:0',
    //         // 'type' => 'required|string|in:fixed,kg,unit', // e.g.,
    //         // 'kp_units_idfk' => 'required|exists:tbank_item_units,id',
    //         'effective_date' => 'required|date',
    //         // 'is_active' => 'boolean',
    //         // 'status' => 'required|in:active,inactive',
    //         'recorder_id' => 'nullable|exists:staffs,user_id',
    //     ]);


    //     // DB::beginTransaction();
    //     // try {
    //     // If new price is set to active, deactivate old active price for the same item
    //     // if ($request->has('is_active') && $request->boolean('is_active')) {

    //     //     KpTbankItemsPriceAndPoint::where('kp_items_idfk', $validated['kp_items_idfk'])
    //     //                              ->where('is_active', true)
    //     //                              ->update([
    //     //                                  'is_active' => false,
    //     //                                  'end_date' => Carbon::parse($validated['effective_date'])->subDay()
    //     //                              ]);
    //     // }
    //     foreach ($request->get('units_data') as $unit) {

    //         KpTbankItemsPriceAndPoint::create([
    //             'kp_items_idfk' =>  $validated['kp_items_idfk'],
    //             'price_from_dealer' => $unit['price_from_dealer'],
    //             'price_for_member' => $unit['price_for_member'],
    //             'point' => $unit['point'],
    //             'type' => 'tbank',
    //             'kp_units_idfk' => $unit['kp_units_idfk'],
    //             'status' => 'active',
    //             'deleted' => '0',
    //             'recorder_id' => $validated['recorder_id'] ?? Auth::id(),
    //             'effective_date' => $request->get('effective_date'),
    //             'end_date' => $request->get('end_date'),
    //             'created_at' => date('Y-m-d H:i:s'),
    //             'updated_at' => date('Y-m-d H:i:s'),
    //         ]);
    //     }





    //     return redirect()->route('keptkayas.tbank.prices.index')
    //         ->with('success', 'ราคารับซื้อถูกสร้างเรียบร้อยแล้ว');
    //     // } catch (\Exception $e) {
    //     //     return back()->with('error', 'เกิดข้อผิดพลาดในการบันทึกราคา: ' . $e->getMessage())->withInput();
    //     // }
    // }

    /**
     * Show the form for editing the specified price.
     */
    public function edit(KpTbankItemsPriceAndPoint $price)
    {
        $items = KpTbankItems::all();
        $units = KpTbankUnits::all();
        $recorders = User::all();
        return view('keptkayas.tbank.prices.edit', compact('price', 'items', 'units', 'recorders'));
    }

    /**
     * Update the specified price in storage.
     */
    public function update(Request $request, KpTbankItemsPriceAndPoint $price)
    {
        $validated = $request->validate([
            'kp_items_idfk' => 'required|exists:kp_recycle_items,id',
            'price_from_dealer' => 'required|numeric|min:0',
            'price_for_member' => 'required|numeric|min:0',
            'point' => 'nullable|integer|min:0',
            'type' => 'required|string|in:fixed,kg,unit', // e.g.,
            'kp_units_idfk' => 'required|exists:tbank_item_units,id',
            'effective_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:effective_date',
            'is_active' => 'boolean',
            'status' => 'required|in:active,inactive',
            'recorder_id' => 'nullable|exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            // Logic to handle old active price is in the model's boot method
            $price->update(array_merge($validated, [
                'is_active' => $request->has('is_active'),
                'recorder_id' => $validated['recorder_id'] ?? Auth::id(),
            ]));

            DB::commit();
            return redirect()->route('keptkayas.tbank.prices.index')
                ->with('success', 'ราคารับซื้อถูกอัปเดตเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาดในการอัปเดตราคา: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified price from storage.
     */
    public function destroy(KpTbankItemsPriceAndPoint $price)
    {
        // Add check if price is used in any transactions before deleting
        // ...

        $price->delete();
        return redirect()->route('keptkayas.tbank.prices.index')
            ->with('success', 'ราคารับซื้อถูกลบเรียบร้อยแล้ว');
    }
}
