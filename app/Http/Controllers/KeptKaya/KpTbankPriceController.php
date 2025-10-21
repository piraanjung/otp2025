<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
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
        return session('db_conn');
        $prices = (new KpTbankItemsPriceAndPoint())->setConnection(session('db_conn'))
            ->with(['item', 'kp_units_info'])
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
        // return $request;
        $validated = $request->validate([
            'kp_items_idfk' => 'required|exists:kp_tbank_items,id',
            // 'price_from_dealer' => 'required|numeric|min:0',
            // 'price_for_member' => 'required|numeric|min:0',
            // 'point' => 'nullable|integer|min:0',
            // 'type' => 'required|string|in:fixed,kg,unit', // e.g.,
            // 'kp_units_idfk' => 'required|exists:tbank_item_units,id',
            'effective_date' => 'required|date',
            // 'is_active' => 'boolean',
            // 'status' => 'required|in:active,inactive',
            'recorder_id' => 'nullable|exists:staffs,user_id',
        ]);


        // DB::beginTransaction();
        // try {
        // If new price is set to active, deactivate old active price for the same item
        // if ($request->has('is_active') && $request->boolean('is_active')) {

        //     KpTbankItemsPriceAndPoint::where('kp_items_idfk', $validated['kp_items_idfk'])
        //                              ->where('is_active', true)
        //                              ->update([
        //                                  'is_active' => false,
        //                                  'end_date' => Carbon::parse($validated['effective_date'])->subDay()
        //                              ]);
        // }
        foreach ($request->get('units_data') as $unit) {

            KpTbankItemsPriceAndPoint::create([
                'kp_items_idfk' =>  $validated['kp_items_idfk'],
                'price_from_dealer' => $unit['price_from_dealer'],
                'price_for_member' => $unit['price_for_member'],
                'point' => $unit['point'],
                'type' => 'tbank',
                'kp_units_idfk' => $unit['kp_units_idfk'],
                'status' => 'active',
                'deleted' => '0',
                'recorder_id' => $validated['recorder_id'] ?? Auth::id(),
                'effective_date' => $request->get('effective_date'),
                'end_date' => $request->get('end_date'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }





        return redirect()->route('keptkayas.tbank.prices.index')
            ->with('success', 'ราคารับซื้อถูกสร้างเรียบร้อยแล้ว');
        // } catch (\Exception $e) {
        //     return back()->with('error', 'เกิดข้อผิดพลาดในการบันทึกราคา: ' . $e->getMessage())->withInput();
        // }
    }

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
