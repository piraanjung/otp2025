<?php

namespace App\Http\Controllers\Inventory;

use App\Exports\InvItemTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\InvItemImport;
use Illuminate\Http\Request;
use App\Models\InvItem;
use App\Models\InvCategory;
use App\Models\InvHazardLevel;
use App\Models\InvUnit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // สำหรับจัดการไฟล์รูป
use Maatwebsite\Excel\Facades\Excel;

class InvItemController extends Controller
{
    // 1. หน้าแสดงรายการพัสดุ (Dashboard ย่อย)
    public function index(Request $request) // ✅ รับ Request เข้ามา
{
    $user = Auth::user();

    // 1. เริ่มต้น Query
    $query = InvItem::where('org_id_fk', $user->org_id_fk)
                    ->with(['category', 'details']); // Eager Load เพื่อลด Query

    // 2. ถ้ามีการพิมพ์ค้นหา (Search)
    if ($request->filled('search')) {
        $search = $request->search;

        // ใช้ Where Group (...) เพื่อไม่ให้ตีกับ org_id
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', '%'.$search.'%')      // ค้นจากชื่อพัสดุ
              ->orWhere('code', 'like', '%'.$search.'%')    // ค้นจากรหัส
              ->orWhere('cas_number', 'like', '%'.$search.'%') // ค้นจาก CAS No.

              // ✅ วิธีที่ถูกต้องในการค้นหาข้ามตาราง (Category)
              ->orWhereHas('category', function ($subQuery) use ($search) {
                  $subQuery->where('name', 'like', '%'.$search.'%');
              });
        });
    }

    // 3. ดึงข้อมูล + Pagination (คงค่า search ไว้ตอนเปลี่ยนหน้า)
    $items = $query->orderBy('created_at', 'desc')
                   ->paginate(10)
                   ->withQueryString(); // ✅ สำคัญ! เพื่อให้กดหน้า 2 แล้วค่าค้นหาไม่หาย

    // ส่งข้อมูลไปที่หน้า View (ต้องส่ง categories, units ไปด้วยถ้าหน้า index มี Popup เพิ่มของ)
    $categories = \App\Models\InvCategory::where('org_id_fk', $user->org_id_fk)->get();
    $units = \App\Models\InvUnit::where('org_id_fk', $user->org_id_fk)->get();

    return view('inventory.inv_item_list', compact('items', 'categories', 'units'));
}

    // 2. หน้าฟอร์มเพิ่มพัสดุ
    public function create()
{
    $user = Auth::user();
    $categories = InvCategory::where('org_id_fk', $user->org_id_fk)->get();

    // ✅ ดึงหน่วยนับมาด้วย
    $units = InvUnit::where('org_id_fk', $user->org_id_fk)->orderBy('name')->get();
    $hazards = InvHazardLevel::where('org_id_fk', $user->org_id_fk)->get();
    return view('inventory.inv_item_form', compact('categories', 'units', 'hazards'));
}

    // 3. ฟังก์ชันบันทึกข้อมูลลง Database
    public function store(Request $request)
    {
        $user = Auth::user();

        // 3.1 ตรวจสอบข้อมูล (Validation)
        $request->validate([
            'name' => 'required|string|max:255',
            'min_stock' => 'required|integer|min:0',
            'unit' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // เช็คไฟล์รูป (ไม่เกิน 2MB)
        ], [
            'name.required' => 'กรุณาระบุชื่อพัสดุ',
            'min_stock.required' => 'กรุณาระบุจำนวน',
            'image.max' => 'รูปภาพต้องมีขนาดไม่เกิน 2MB'
        ]);

        // 3.2 จัดการอัปโหลดรูปภาพ (ถ้ามี)
        $imagePath = null;
        if ($request->hasFile('image')) {
            // บันทึกไฟล์ไปที่ storage/app/public/inventory_images
            $path = $request->file('image')->store('public/inventory_images');
            // แปลง path เพื่อเก็บใน DB (ตัด public/ ออก)
            $imagePath = str_replace('public/', '', $path);
        }

        // 3.3 บันทึกข้อมูล
        $item = InvItem::create([
            'org_id_fk' => $user->org_id_fk, // ✅ Auto Assign Org ID
            'inv_category_id_fk' => $request->inv_category_id_fk,
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'min_stock' => $request->min_stock,
            'unit' => $request->unit,
            'is_chemical' => $request->has('is_chemical') ? 1 : 0, // รับค่าจาก Checkbox
            'return_required' => $request->has('return_required') ? 1 : 0,
            'image_path' => $imagePath,

            // ข้อมูลสารเคมี (ถ้ามี)
            'cas_number' => $request->cas_number,
            'expire_date' => $request->expire_date,
            'msds_link' => $request->msds_link,
        ]);
        // ✅ บันทึกความสัมพันธ์ (ถ้ามีการติ๊กเลือก)
    if ($request->has('hazards')) {
        $item->hazards()->attach($request->hazards);
    }

        // 3.4 ส่งกลับไปหน้าเดิมพร้อมข้อความแจ้งเตือน
        return redirect()->route('inventory.items.index')
                         ->with('success', 'เพิ่มรายการพัสดุเรียบร้อยแล้ว');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new InvItemImport, $request->file('file'));
            return back()->with('success', 'นำเข้าข้อมูลสำเร็จแล้ว!');
        } catch (\Exception $e) {
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    // ฟังก์ชันโหลด Template (Optional: สร้างไฟล์ excel เปล่าๆ ให้ user)
    public function downloadTemplate()
    {
        // คุณอาจจะ create file จริงๆ เก็บไว้ใน storage แล้ว return download
        // หรือใช้ Excel::download ในการ generate สดๆ ก็ได้
        // return Excel::download(new InvItemTemplateExport, 'item_import_template.xlsx');
        return response()->download(public_path('templates/item_import_template.xlsx'));
    }
}
