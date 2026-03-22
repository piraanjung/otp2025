<?php

namespace App\Http\Controllers\KeptKaya;

use App\Exports\KpTbankItemsExport;
use App\Exports\KpTbankItemsTemplateExport;
use App\Imports\KpTbankItemsImport;
use App\Http\Controllers\Controller;
use App\Models\EmissionFactor;
use App\Models\Keptkaya\KpTbankItems;
use App\Models\Keptkaya\KpTbankItemsGroups;
use App\Models\Keptkaya\KpTbankUnits;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class KpTbankItemsController extends Controller
{
    public function index()
    {
        $kp_tbank_items = KpTbankItems::where('org_id_fk', Auth::user()->org_id_fk)
            ->orWhere('org_id_fk', null)
            ->where('status', 'active')->paginate(10);
        return view('keptkayas.tbank.items.index', compact('kp_tbank_items'));
    }

    public function create()
    {
        // 1. ดึงข้อมูลกลุ่มขยะ
        $kp_items_groups = KpTbankItemsGroups::all();

        // 2. ดึงข้อมูลหน่วยนับ (สำคัญ: เช็คชื่อ Model และ Path ให้ถูกต้อง)
        $units = KpTbankUnits::all();

        // 3. ดึงข้อมูลค่า Emission Factor ทั้งหมด
        $emissionFactors = EmissionFactor::orderBy('material_name', 'asc')->get();

        // 4. ส่งตัวแปรทั้งหมดไปที่ View
        return view('keptkayas.tbank.items.create', compact(
            'kp_items_groups',
            'units',
            'emissionFactors'
        ));
    }
    public function store(Request $request)
    {
        // 1. Validation เบื้องต้น (ตรวจสอบว่ามีการส่ง items มาไหม)
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.kp_itemsname' => 'required|string|max:255',
            'items.*.unit_bank_idfk' => 'required|numeric',
            'items.*.kp_items_group_idfk' => 'required|numeric',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // เช็คไฟล์รูปแยกตาม index
        ]);

        try {
            // 2. เริ่มการวนลูปบันทึกข้อมูล
            foreach ($request->items as $index => $itemData) {

                $item = new KpTbankItems();
                $item->kp_itemsname        = $itemData['kp_itemsname'];
                $item->kp_itemscode        = $itemData['kp_itemscode'] ?? 'ITEM-' . time() . $index;
                $item->kp_items_group_idfk = $itemData['kp_items_group_idfk'];
                $item->unit_bank_idfk      = $itemData['unit_bank_idfk'];
                $item->unit_kiosk_idfk     = $itemData['unit_kiosk_idfk'] ?? null;
                $item->ef_id_fk            = !empty($itemData['ef_id_fk']) ? $itemData['ef_id_fk'] : null;
                $item->org_id_fk           = Auth::user()->org_id_fk;
                $item->status              = 'active';
                $item->deleted             = '0';

                // 3. จัดการรูปภาพ (เช็คจาก $request->images โดยใช้ index เดียวกับ item)
                if ($request->hasFile("images.$index")) {
                    $image = $request->file("images.$index");
                    $imageName = time() . '_' . $index . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('keptkaya/items'), $imageName);
                    $item->image = $imageName;
                }

                $item->save();
            }

            return redirect()->route('keptkayas.tbank.items.index')
                ->with('success', 'บันทึกรายการขยะใหม่ทั้งหมดเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            // กรณีเกิดข้อผิดพลาด
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }
    public function edit($id)
    {
        // 1. ดึงข้อมูล Item ที่ต้องการแก้ไข
        $item = KpTbankItems::findOrFail($id);

        // 2. ดึงข้อมูลตัวเลือกสำหรับ Dropdown
        $units = KpTbankUnits::all();
        $groups = KpTbankItemsGroups::all();
        $emissionFactors = EmissionFactor::orderBy('material_name', 'asc')->get();

        // 3. ส่งข้อมูลทั้งหมดไปที่หน้า Edit
        return view('keptkayas.tbank.items.edit', compact(
            'item',
            'units',
            'groups',
            'emissionFactors'
        ));
    }

    public function update(Request $request, $id)
    {
        // 1. Validation เบื้องต้น
        $request->validate([
            'kp_itemscode' => 'required',
            'kp_itemsname' => 'required',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // จำกัดขนาด 2MB
        ]);

        $item = KpTbankItems::findOrFail($id);

        // 2. จัดการรูปภาพ
        if ($request->hasFile('image')) {
            // --- ส่วนการลบรูปเก่า ---
            if ($item->image) {
                $oldPath = public_path('keptkaya/items/' . $item->image);
                if (file_exists($oldPath)) {
                    unlink($oldPath); // ลบไฟล์ออกจาก Folder
                }
            }

            // --- ส่วนการบันทึกรูปใหม่ ---
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension(); // ตั้งชื่อไฟล์ใหม่ตามเวลา
            $image->move(public_path('keptkaya/items'), $imageName);

            // อัปเดตชื่อไฟล์ใน Database
            $item->image = $imageName;
        }

        // 3. อัปเดตข้อมูลส่วนอื่นๆ
        $item->kp_itemscode    = $request->kp_itemscode;
        $item->kp_itemsname    = $request->kp_itemsname;
        $item->unit_bank_idfk  = $request->unit_bank_idfk;
        $item->unit_kiosk_idfk = $request->unit_kiosk_idfk;
        $item->ef_id_fk        = $request->ef_id_fk;
        $item->status          = $request->status;

        $item->save();

        return redirect()->route('keptkayas.tbank.items.index')
            ->with('success', 'อัปเดตข้อมูลและรูปภาพเรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        $item = KpTbankItems::findOrFail($id);
        $item->delete();

        return back()->with('success', 'ลบข้อมูลรายการขยะเรียบร้อยแล้ว');
    }

    public function buyItems(Request $request, $user_id = "")
    {
        if ($user_id != "") {
            $request->session()->put('selected_member', $user_id);
        }
        if ($request->session()->has('selected_member')) {
            $user_id = $request->session()->get('selected_member');
        }
        $member = User::where('id', $user_id)
            // ->with([
            //     'user_kaya_infos.trash_zone' => function($q){
            //         return $q->select('id', 'zone_name');
            //  xpx   },
            //     'user_kaya_infos.trash_subzone' => function($q){
            //         return $q->select('id', 'subzone_name');
            //     },
            // ])
            ->get(['id', 'prefix', 'firstname', 'lastname', 'zone_id', 'subzone_id', 'address'])->first();

        return  $items   = (new KpTbankItems())->setConnection(session('db_conn'))
            ->where('status', 'active')
            ->with([
                'items_price_and_point_infos' =>  function ($q) {
                    return $q->select('id', 'items_id_fk', 'price_form_dealer', 'units_id_fk', 'price_for_member', 'reward_point')
                        ->where('status', 'active');
                },
            ])
            ->get(['id', 'kp_itemscode', 'kp_itemsname', 'kp_items_group_idfk', 'tbank_item_unit_idfk', 'image']);
        $favorite_items = collect($items)->sortBy('favorite');
        return view('items.items', compact('favorite_items', 'member'));
    }

    public function search_items($itemscode)
    {
        $items = (new KpTbankItems())->setConnection(session('db_conn'))
            ->where('itemscode', $itemscode)->first();
        $res     = collect($items)->isNotEmpty() ? 1 : 0;
        return json_encode(['res' => $res, 'items' => $items]);
    }


    public function set_items_pricepoint()
    {
        $items = (new KpTbankItems())->setConnection(session('db_conn'))
            ->get();
        return view('kp_tbanks.items.set_items_pricepoint', compact('items'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new KpTbankItemsImport, $request->file('file'));

            return back()->with('success', 'นำเข้าข้อมูลขยะรีไซเคิลเรียบร้อยแล้ว');
        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function exportTemplate()
    {
        return Excel::download(new KpTbankItemsTemplateExport, 'template_items_import.xlsx');
    }

    public function generateCode($group_id)
    {
        $groupId = $group_id;

        if (!$groupId) {
            return response()->json(['success' => false, 'message' => 'Group ID is required.'], 422);
        }

        $group = (new KpTbankItemsGroups())->setConnection(session('db_conn'))
            ->find($groupId);

        if (!$group) {
            return response()->json(['success' => false, 'message' => 'Invalid Group ID.'], 404);
        }

        // --- ส่วนที่ปรับปรุง: ใช้ item_group_code และ sequence_num จาก Model ---
        DB::beginTransaction();
        try {
            // Get the group and lock it for the duration of the transaction to prevent race conditions
            $group = (new KpTbankItemsGroups())->setConnection(session('db_conn'))
                ->lockForUpdate()->find($groupId);

            if (!$group) {
                return response()->json(['success' => false, 'message' => 'Invalid Group ID.'], 404);
            }

            // Get the item_group_code and current sequence number
            $groupCode = $group->item_group_code;
            $currentSequenceNum = $group->sequence_num;

            if (empty($groupCode)) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Group Code is not set for this group.'], 422);
            }

            // Format the new sequence number (e.g., 0001, 0002)
            $sequenceNumber = str_pad($currentSequenceNum, 4, '0', STR_PAD_LEFT);

            // Construct the new item code
            $newCode = "{$groupCode}-{$sequenceNumber}";

            // Increment the sequence number for the next item
            $group->sequence_num = $currentSequenceNum + 1;
            $group->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'item_code' => $newCode
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error for debugging
            // \Log::error("Error generating item code: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to generate code.'], 500);
        }
        // --- สิ้นสุดส่วนที่ปรับปรุง ---
    }

    // 1. หน้าแสดงรายการที่ถูกลบ (Trash)
    public function trash()
    {
        // ดึงเฉพาะรายการที่ถูก Soft Delete เท่านั้น
        $items = KpTbankItems::onlyTrashed()->get();
        return view('keptkayas.tbank.items.trash', compact('items'));
    }

    // 2. ฟังก์ชันกู้คืนข้อมูล (Restore)
    public function restore($id)
    {
        $item = KpTbankItems::withTrashed()->findOrFail($id);
        $item->restore();

        return redirect()->route('keptkayas.tbank.items.trash')
            ->with('success', 'กู้คืนรายการ ' . $item->kp_itemsname . ' เรียบร้อยแล้ว');
    }

    // 3. ฟังก์ชันลบถาวร (Force Delete) - ระวัง! กู้คืนไม่ได้อีก
    public function forceDelete($id)
    {
        $item = KpTbankItems::withTrashed()->findOrFail($id);

        // ลบรูปภาพออกจากเครื่องจริงๆ
        if ($item->image) {
            $path = public_path('keptkaya/items/' . $item->image);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $item->forceDelete();

        return redirect()->route('keptkayas.tbank.items.trash')
            ->with('success', 'ลบข้อมูลออกจากระบบถาวรแล้ว');
    }

    public function pendingEf(Request $request)
    {
        // 1. ดึงกลุ่มขยะทั้งหมดมาทำตัวกรอง
        $groups = KpTbankItemsGroups::all();

        // 2. ดึงรายการที่ยังไม่มี EF และกรองตามกลุ่ม (ถ้ามีการเลือก)
        $query = KpTbankItems::whereNull('ef_id_fk');

        if ($request->has('group_id') && $request->group_id != '') {
            $query->where('kp_items_group_idfk', $request->group_id);
        }

        $items = $query->get();

        // 3. ดึงค่า EF มาให้เลือก
        $emissionFactors = EmissionFactor::orderBy('material_name', 'asc')->get();

        return view('keptkayas.tbank.items.pending_ef', compact('items', 'groups', 'emissionFactors'));
    }

    public function updateEfBulk(Request $request)
    {
        // รับข้อมูลมาเป็น Array เพื่ออัปเดตทีละหลายรายการ
        foreach ($request->ef_mapping as $itemId => $efId) {
            if (!empty($efId)) {
                $item = KpTbankItems::find($itemId);
                if ($item) {
                    $item->update(['ef_id_fk' => $efId]);
                }
            }
        }

        return redirect()->route('keptkayas.tbank.items.index')
            ->with('success', 'จับคู่ค่าคาร์บอน (EF) เรียบร้อยแล้ว');
    }
}
