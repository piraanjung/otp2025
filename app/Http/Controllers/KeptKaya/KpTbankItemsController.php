<?php

namespace App\Http\Controllers\KeptKaya;

use App\Exports\KpTbankItemsExport;
use App\Imports\KpTbankItemsImport;
use App\Models\Items;
use App\Models\KpItems;
use App\Http\Controllers\Controller;
use App\Models\Keptkaya\KpTbankItems;
use App\Models\Keptkaya\KpTbankItemsGroups;
use App\Models\Keptkaya\KpTbankUnits;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use Spatie\ImageOptimizer\OptimizerChainFactory;

class KpTbankItemsController extends Controller
{
    public function index()
    {
        $kp_tbank_items = KpTbankItems::where('status', 'active')->get();
        return view('keptkaya.tbank.items.index', compact('kp_tbank_items'));
    }

    public function create()
    {
        $kp_items_groups = KpTbankItemsGroups::where('status', 'active')->get();
        $tbank_item_units = KpTbankUnits::where('status', 'active')->get();

        return view('keptkaya.tbank.items.create', compact('kp_items_groups', 'tbank_item_units'));
    }
    public function store(Request $request)
    {

        $validated = $request->validate([
            'kp_itemsname' => 'required|string|unique:kp_tbank_items,kp_itemsname',
            'kp_items_group_idfk' => 'required|exists:kp_tbank_items_groups,id',
            // 'kp_itemscode' => 'required|string|unique:kp_recycle_items,item_code',
            'image' => 'nullable|image|max:2048', // Max 2MB

        ]);

        $group = KpTbankItemsGroups::lockForUpdate()->find($validated['kp_items_group_idfk']);


        if (!$group) {
            return back()->with('error', 'Invalid group selected.')->withInput();
        }
        $groupCode = $group->item_group_code;
        $currentSequenceNum = $group->sequence_num;

        $sequenceNumber = str_pad($currentSequenceNum, 4, '0', STR_PAD_LEFT);
        $newCode = "{$groupCode}-{$sequenceNumber}";
        if (KpTbankItems::where('kp_itemscode', $newCode)->exists()) {
            return back()->with('error', 'Generated code already exists. Please try again.')->withInput();
        }
        // --- ส่วนที่ปรับปรุง: บันทึก image และเก็บ path ---
        $imagePath = null;
        if ($request->hasFile('image')) {

            $imageFile = $request->file('image');
            $extension = $request->file('image')->getClientOriginalExtension();
            $imageName = Str::slug($newCode) . '.' . $extension;
            if ($extension == 'png') {
                $image = imagecreatefrompng($imageFile);
            } else {
                $image = imagecreatefromjpeg($imageFile);
            }

            $width = imagesx($image);
            $height = imagesy($image);

            $newWidth = 150;
            $newHeight = 150;

            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            // Save the resized image to a temporary file
            $tempImagePath = tempnam(sys_get_temp_dir(), 'resized_');
            imagejpeg($resizedImage, $tempImagePath, 90); // Save as JPEG with quality 90

            // Store the temporary file using Laravel's Storage facade
            Storage::disk('public')->put('keptkaya/items/' . $imageName, file_get_contents($tempImagePath));

            // Clean up
            imagedestroy($image);
            imagedestroy($resizedImage);
            unlink($tempImagePath);

            $imagePath = 'keptkaya/items/' . $imageName;

            // $imagePath = $request->file('image')->storeAs('keptkaya/items', $imageName, 'public');
        }

        // --- โค้ดสำหรับบันทึกข้อมูลหลักของ kp_recycle_items ---
        $item = KpTbankItems::create([
            'kp_itemscode' => $newCode,
            'kp_itemsname' => $validated['kp_itemsname'],
            'kp_items_group_idfk' => $validated['kp_items_group_idfk'],
            'tbank_item_unit_idfk' => 1,
            'image_path' => $imagePath, // Save the image path
            'status' => 'active',
            'deleted' => '0',
        ]);

        $group->sequence_num = $currentSequenceNum + 1;
        $group->save();

        // --- โค้ดสำหรับบันทึกหน่วยนับที่เลือก (Assuming a pivot table) ---
        // This will require a pivot table model and relationship
        // $item->units()->attach($validated['tbank_item_unit_ids']);

        return redirect()->route('keptkaya.tbank.items.index');
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

        return  $items   = KpTbankItems::where('status', 'active')
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
        $items = KpTbankItems::where('itemscode', $itemscode)->first();
        $res     = collect($items)->isNotEmpty() ? 1 : 0;
        return json_encode(['res' => $res, 'items' => $items]);
    }


    public function set_items_pricepoint()
    {
        $items = KpTbankItems::get();
        return view('kp_tbanks.items.set_items_pricepoint', compact('items'));
    }


    public function export()
    {
        // กำหนดชื่อไฟล์ Excel ที่จะดาวน์โหลด
        $fileName = 'kp_tbank_items_' . now()->format('YmdHis') . '.xlsx';
        return Excel::download(new KpTbankItemsExport, $fileName);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new KpTbankItemsImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data imported successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing data: ' . $e->getMessage());
        }
    }

    public function generateCode($group_id)
    {
        $groupId = $group_id;

        if (!$groupId) {
            return response()->json(['success' => false, 'message' => 'Group ID is required.'], 422);
        }

        $group = KpTbankItemsGroups::find($groupId);

        if (!$group) {
            return response()->json(['success' => false, 'message' => 'Invalid Group ID.'], 404);
        }

        // --- ส่วนที่ปรับปรุง: ใช้ item_group_code และ sequence_num จาก Model ---
        DB::beginTransaction();
        try {
            // Get the group and lock it for the duration of the transaction to prevent race conditions
            $group = KpTbankItemsGroups::lockForUpdate()->find($groupId);

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
}
