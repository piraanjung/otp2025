<?php

namespace App\Http\Controllers\FoodWaste;

use App\Http\Controllers\Controller;
use App\Models\FoodWaste\FoodAnnualTrashStocks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BinsController extends Controller
{
    public function index(Request $request)
    {
        // เริ่มต้น Query พร้อม Eager Loading ความสัมพันธ์ต่างๆ
        $query = FoodAnnualTrashStocks::with([
            'foodwaste_bin',
            'foodwaste_bin.fw_user_preference',
            'foodwaste_bin.fw_user_preference.user'
        ]);

        // 1. กรองเฉพาะถังหมักเศษอาหาร (ตามที่คุณต้องการตอนแรก)
        $query->where('bin_type', 'compost');

        // 2. ค้นหาจากรหัสถังขยะ (ถ้ามีการกรอกมา)
        $query->when($request->search, function ($q) use ($request) {
            return $q->where('bin_code', 'like', '%' . $request->search . '%');
        });

        // 3. กรองตามสถานะ (ถ้ามีการเลือกมา)
        $query->when($request->status, function ($q) use ($request) {
            return $q->where('status', $request->status);
        });

        // 4. เรียงลำดับจากใหม่ไปเก่า และแบ่งหน้า (Pagination)
        $bins = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('foodwaste.bins.index', compact('bins'));
    }

    public function create()
    {
        return view('foodwaste.bins.create');
    }

    public function store(Request $request)
    {
        // 1. ตรวจสอบข้อมูลให้ตรงกับฟอร์ม
        $request->validate([
            'prefix'      => 'required|string|max:10',
            'quantity'    => 'required|integer|min:1|max:100',
            'status'      => 'required|string',
            'bin_type'    => 'required|in:annual,compost',
            'description' => 'nullable|string',
        ]);

        $prefix = $request->prefix;
        $quantity = $request->quantity;

        DB::beginTransaction();

        try {
            // 2. หาเลขถังล่าสุดอิงจากคอลัมน์ bin_code ในตาราง foodwaste_bin_stocks
            $latestBin = FoodAnnualTrashStocks::where('bin_code', 'like', $prefix . '%')
                ->orderBy('bin_code', 'desc')
                ->first();

            $startNumber = 1;

            if ($latestBin) {
                // ใช้ substr ตัดตัวอักษรด้านหน้าออกตามความยาวของ prefix จะแม่นยำกว่า
                $latestCode = substr($latestBin->bin_code, strlen($prefix));
                $startNumber = (int)$latestCode + 1;
            }

            // 3. วนลูปสร้างตามจำนวน
            for ($i = 0; $i < $quantity; $i++) {
                // รันเลข 4 หลัก เช่น 0001, 0002
                $currentNumber = str_pad($startNumber + $i, 4, '0', STR_PAD_LEFT);
                $newBinCode = $prefix . $currentNumber;

                FoodAnnualTrashStocks::create([
                    'bin_code'    => $newBinCode,
                    'bin_type'    => $request->bin_type,
                    'org_id_fk'   => Auth::user()->org_id_fk,
                    'description' => $request->description,
                    'status'      => $request->status,
                ]);
            }

            DB::commit();

            return redirect()->route('bins.index')->with('success', "สร้างรหัสถังจำนวน $quantity รายการสำเร็จ (ตั้งแต่ $prefix" . str_pad($startNumber, 4, '0', STR_PAD_LEFT) . " ถึง $newBinCode)");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'เกิดข้อผิดพลาดในการสร้างข้อมูล: ' . $e->getMessage()]);
        }
    }

    public function show(FoodAnnualTrashStocks $bin)
    {
        $bin->load('iotbox'); // โหลดข้อมูล IoT Box ที่เกี่ยวข้อง
        return view('foodwaste.bins.show', compact('bin'));
    }

    public function edit($id)
    {
        // ค้นหาถังขยะตาม ID ที่ส่งมา
        $bin = FoodAnnualTrashStocks::findOrFail($id);

        // ส่งข้อมูลไปที่หน้าแก้ไข
        return view('foodwaste.bins.edit', compact('bin'));
    }

    public function update(Request $request, $id)
    {
        // 1. ตรวจสอบข้อมูลที่ส่งมาจากฟอร์ม
        $request->validate([
            // สำคัญมาก: ตรง unique ต้องใส่ ,$id ต่อท้าย เพื่อบอกให้ละเว้นการเช็คซ้ำกับ ID ของตัวเอง
            'bin_code' => 'required|string|max:50|unique:foodwaste_bin_stocks,bin_code,' . $id,
            'status'      => 'required|string|in:active,inactive,damaged,removed',
            'description' => 'nullable|string',
        ], [
            // สามารถกำหนดข้อความแจ้งเตือนภาษาไทยได้ตรงนี้
            'bin_code.unique' => 'รหัสถังขยะนี้มีอยู่ในระบบแล้ว กรุณาใช้รหัสอื่น',
        ]);

        try {
            // 2. ค้นหาข้อมูลถังขยะที่ต้องการแก้ไข
            $bin = FoodAnnualTrashStocks::findOrFail($id);

            // 3. อัปเดตข้อมูล
            $bin->update([
                'bin_code'    => $request->bin_code,
                'status'      => $request->status,
                'description' => $request->description,
            ]);

            // 4. กลับไปหน้า index พร้อมส่งข้อความแจ้งเตือนว่าสำเร็จ
            return redirect()->route('foodwaste.bins.index')
                ->with('success', "อัปเดตข้อมูลถังขยะรหัส {$bin->bin_code} เรียบร้อยแล้ว");
        } catch (\Exception $e) {
            // ถ้ามี Error เกิดขึ้น ให้กลับไปหน้าฟอร์มแก้ไขพร้อมแจ้งเตือน
            return back()->withInput()->withErrors(['error' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            // 1. ค้นหาถังขยะที่ต้องการลบ
            $bin = FoodAnnualTrashStocks::findOrFail($id);

            // เก็บชื่อรหัสไว้ก่อนลบ เพื่อเอาไปแสดงในข้อความแจ้งเตือน
            $deletedCode = $bin->bin_code;

            // 2. สั่งลบข้อมูลออกจาก Database
            $bin->delete();

            // 3. ส่งผู้ใช้กลับไปที่หน้าตาราง (index) พร้อมข้อความแจ้งเตือนสีเขียว
            return redirect()->route('foodwaste.bins.index')
                ->with('success', "ลบข้อมูลถังขยะรหัส {$deletedCode} ออกจากระบบเรียบร้อยแล้ว");
        } catch (\Exception $e) {
            // ดัก Error เผื่อในกรณีที่ลบไม่ได้ (เช่น ข้อมูลนี้ถูกผูกติดกับตารางอื่นอยู่)
            return redirect()->route('foodwaste.bins.index')
                ->withErrors(['error' => 'เกิดข้อผิดพลาด ไม่สามารถลบข้อมูลได้: ' . $e->getMessage()]);
        }
    }

    public function previewCodes(Request $request)
    {
        $prefix = $request->query('prefix', '');
        $quantity = (int) $request->query('quantity', 1);

        if (empty($prefix) || $quantity < 1) {
            return response()->json(['error' => 'Invalid data'], 400);
        }

        // หาเลขล่าสุดใน Database
        $latestBin = FoodAnnualTrashStocks::where('bin_code', 'like', $prefix . '%')
            ->orderBy('bin_code', 'desc')
            ->first();

        $startNumber = 1;
        if ($latestBin) {
            $latestCode = substr($latestBin->bin_code, strlen($prefix));
            $startNumber = (int)$latestCode + 1;
        }

        $endNumber = $startNumber + $quantity - 1;

        // สร้างข้อความพรีวิว
        $startCode = $prefix . str_pad($startNumber, 4, '0', STR_PAD_LEFT);
        $endCode = $prefix . str_pad($endNumber, 4, '0', STR_PAD_LEFT);

        return response()->json([
            'start_code' => $startCode,
            'end_code' => $endCode,
            'message' => $quantity > 1
                ? "ระบบจะสร้างรหัสตั้งแต่ <strong>$startCode</strong> ถึง <strong>$endCode</strong>"
                : "ระบบจะสร้างรหัส <strong>$startCode</strong>"
        ]);
    }

    public function printSelected(Request $request)
    {
        $ids = $request->input('selected_bins'); // รับค่าจาก Checkbox

        if (!$ids || count($ids) == 0) {
            return back()->withErrors(['error' => 'กรุณาเลือกถังขยะที่ต้องการพิมพ์อย่างน้อย 1 รายการ']);
        }

        // ดึงข้อมูลตาม IDs ที่ส่งมา
        $bins = FoodAnnualTrashStocks::whereIn('id', $ids)->get();

        return view('foodwaste.bins.print-qr', compact('bins'));
    }
}
