<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Imports\InvItemImport;
use Illuminate\Http\Request;
use App\Models\InvItem;
use App\Models\InvCategory;
use App\Models\InvHazardLevel;
use App\Models\InvUnit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class InvItemController extends Controller
{
    // 1. หน้าแสดงรายการพัสดุ (Dashboard ย่อย)
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. เริ่มต้น Query พร้อมจัดเรียงรายละเอียดล็อต (details) ด้านใน
        $query = InvItem::where('org_id_fk', $user->org_id_fk)
            ->with(['category', 'details' => function ($q) {
                // เรียงลำดับล็อตจาก "ล่าสุด ไปหา เก่าสุด" (ตามวันที่รับเข้า/สร้าง)
                $q->where('status', 'ACTIVE')
                    ->orderBy('received_date', 'asc');
            }]);

        // ➕ 1.1 ถ้ามีการคลิกมาจากหน้า Dashboard (filter=expiring)
        if ($request->get('filter') === 'expiring') {
            $query->whereHas('details', function ($q) {
                $q->where('status', 'ACTIVE')
                    ->whereDate('expire_date', '<=', Carbon::now()->addDays(30));
            });
        }

        // 2. ถ้ามีการพิมพ์ค้นหา (Search)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('code', 'like', '%' . $search . '%')
                    ->orWhere('cas_number', 'like', '%' . $search . '%')
                    ->orWhereHas('category', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // 3. ดึงข้อมูล + Pagination
        // return $aa =   $query->orderBy('created_at', 'desc')->get();

        $items = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $categories = InvCategory::where('org_id_fk', $user->org_id_fk)->get();
        $units = InvUnit::where('org_id_fk', $user->org_id_fk)->get();

        return view('inventory.items.index', compact('items', 'categories', 'units'));
    }
    public function iframeIndex(Request $request)
    {
        // ใช้ Logic การดึงข้อมูลและ Search แบบเดิมของคุณทั้งหมดที่นี่
        $user = Auth::user();

        // 1. เริ่มต้น Query
        $query = InvItem::where('org_id_fk', $user->org_id_fk)
            ->with(['category', 'details']); // Eager Load เพื่อลด Query

        // 2. ถ้ามีการพิมพ์ค้นหา (Search)
        if ($request->filled('search')) {
            $search = $request->search;

            // ใช้ Where Group (...) เพื่อไม่ให้ตีกับ org_id
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')      // ค้นจากชื่อพัสดุ
                    ->orWhere('code', 'like', '%' . $search . '%')    // ค้นจากรหัส
                    ->orWhere('cas_number', 'like', '%' . $search . '%') // ค้นจาก CAS No.

                    // ✅ วิธีที่ถูกต้องในการค้นหาข้ามตาราง (Category)
                    ->orWhereHas('category', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // 3. ดึงข้อมูล + Pagination (คงค่า search ไว้ตอนเปลี่ยนหน้า)
        $items = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString(); // ✅ สำคัญ! เพื่อให้กดหน้า 2 แล้วค่าค้นหาไม่หาย

        // ส่งข้อมูลไปที่หน้า View (ต้องส่ง categories, units ไปด้วยถ้าหน้า index มี Popup เพิ่มของ)
        $categories = InvCategory::where('org_id_fk', $user->org_id_fk)->get();
        $units = InvUnit::where('org_id_fk', $user->org_id_fk)->get();
        return view('inventory.partials.inv_item_table', compact('items', 'categories', 'units'));
    }

    // 2. หน้าฟอร์มเพิ่มพัสดุ
    public function create()
    {
        $user = Auth::user();
        $categories = InvCategory::where('org_id_fk', $user->org_id_fk)->get();

        // ✅ ดึงหน่วยนับมาด้วย
        $units = InvUnit::where('org_id_fk', $user->org_id_fk)->orderBy('name')->get();
        $hazards = InvHazardLevel::where('org_id_fk', $user->org_id_fk)->get();
        return view('inventory.items.create', compact('categories', 'units', 'hazards'));
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

            // 3.2 ตั้งชื่อไฟล์ใหม่ไม่ให้ซ้ำกัน (เช่น ใช้ timestamp + สุ่มตัวเลข)
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // 3.3 กำหนดโฟลเดอร์ปลายทางใน public (เช่น public/uploads/items)
            $destinationPath = public_path('inventory/items');

            // ตรวจสอบว่ามีโฟลเดอร์นี้ยัง ถ้ายังไม่มีให้สร้างขึ้นมาอัตโนมัติ
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }

            // 3.4 ย้ายไฟล์ไปไว้ที่โฟลเดอร์ปลายทาง
            $file->move($destinationPath, $filename);

            // 3.5 บันทึก Path สำหรับเก็บลงฐานข้อมูล (เช่น 'uploads/items/ชื่อไฟล์.jpg')
            $imagePath = 'inventory/items/' . $filename;
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

    public function edit($id)
    {
        // 1. ค้นหาพัสดุตาม ID ที่ส่งมา (ถ้าไม่เจอจะแสดงหน้า 404 อัตโนมัติ)
        $item = InvItem::with('hazards')->findOrFail($id);

        // 2. ดึงข้อมูลสำหรับใส่ Dropdown (หมวดหมู่, หน่วยนับ, และความอันตราย)
        $categories = InvCategory::all();
        $units = InvUnit::all();
        $hazards = InvHazardLevel::all();
        // 3. ส่งข้อมูลทั้งหมดไปยังหน้า view('inventory.items.edit')
        return view('inventory.items.edit', compact('item', 'categories', 'units', 'hazards'));
    }

    public function update(Request $request, $id)
    {
        // 1. ตรวจสอบความถูกต้องของข้อมูล (Validation)
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100|unique:inv_items,code,' . $id, // ยกเว้นรหัสตัวเอง
            'inv_category_id_fk' => 'nullable|exists:inv_categories,id',
            'unit' => 'required|string|max:50',
            'min_stock' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // จำกัดขนาดรูปไม่เกิน 2MB
            'cas_number' => 'nullable|string|max:100',
            'msds_link' => 'nullable|url|max:255',
        ]);

        // 2. ค้นหาพัสดุที่ต้องการแก้ไข
        $item = InvItem::findOrFail($id);

        // 3. จัดการอัปโหลดรูปภาพใหม่ (ถ้ามีเลือกไฟล์มา)
        $imagePath = $item->image_path; // ใช้ path เดิมเป็นค่าตั้งต้น

        if ($request->hasFile('image')) {
            // 3.1 ลบรูปภาพเก่าทิ้ง (ถ้ามีไฟล์เดิมอยู่จริง)
            if ($item->image_path && File::exists(public_path($item->image_path))) {
                File::delete(public_path($item->image_path));
            }

            // 3.2 ตั้งชื่อไฟล์ใหม่ไม่ให้ซ้ำกัน (เช่น ใช้ timestamp + สุ่มตัวเลข)
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // 3.3 กำหนดโฟลเดอร์ปลายทางใน public (เช่น public/uploads/items)
            $destinationPath = public_path('inventory/items');

            // ตรวจสอบว่ามีโฟลเดอร์นี้ยัง ถ้ายังไม่มีให้สร้างขึ้นมาอัตโนมัติ
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }

            // 3.4 ย้ายไฟล์ไปไว้ที่โฟลเดอร์ปลายทาง
            $file->move($destinationPath, $filename);

            // 3.5 บันทึก Path สำหรับเก็บลงฐานข้อมูล (เช่น 'uploads/items/ชื่อไฟล์.jpg')
            $imagePath = 'inventory/items/' . $filename;
        }

        // 4. อัปเดตข้อมูลลงในฐานข้อมูล
        $item->update([
            'name' => $request->name,
            'code' => $request->code,
            'inv_category_id_fk' => $request->inv_category_id_fk,
            'unit' => $request->unit,
            'min_stock' => $request->min_stock,
            'description' => $request->inv_description,
            'image_path' => $imagePath,

            // จัดการค่า Checkbox (ถ้าติ๊กส่งค่ามาให้เป็น true/1 ถ้าไม่ติ๊กให้เป็น false/0)
            'return_required' => $request->has('return_required'),
            'is_chemical' => $request->has('is_chemical'),

            // ข้อมูลเฉพาะสารเคมี (ถ้าไม่ได้ติ๊กเป็นสารเคมี ให้เคลียร์ค่าทิ้งหรือปล่อย null)
            'cas_number' => $request->has('is_chemical') ? $request->cas_number : null,
            'msds_link' => $request->has('is_chemical') ? $request->msds_link : null,
        ]);

        // 5. จัดการบันทึกข้อมูลความสัมพันธ์ความเป็นอันตราย (Many-to-Many Table เช่น item_hazard)
        if ($request->has('is_chemical')) {
            // ถ้าติ๊กเป็นสารเคมี ให้ Sync รายการ hazards ที่เลือก (ถ้าไม่เลือกเลย จะเคลียร์ทิ้งทั้งหมด)
            $item->hazards()->sync($request->input('hazards', []));
        } else {
            // ถ้าเอาติ๊กออก (ไม่ใช่สารเคมี) ให้ลบความสัมพันธ์อันตรายทั้งหมดทิ้ง
            $item->hazards()->sync([]);
        }

        // 6. Redirect กลับไปหน้าแสดงรายการพร้อมข้อความแจ้งเตือน (Success Message)
        return redirect()->route('inventory.items.index')
            ->with('success', 'แก้ไขข้อมูลพัสดุเรียบร้อยแล้ว');
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
