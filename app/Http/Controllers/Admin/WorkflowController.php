<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ApprovalWorkflow;
use App\Models\ApprovalWorkflowStep;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\FacadesDB;
use Spatie\Permission\Models\Role;

class WorkflowController extends Controller
{
    // 1. แสดงรายการ Workflow ทั้งหมด (Read)
    public function index()
    {
        $workflows = ApprovalWorkflow::withCount('steps')->get();
        return view('admin.workflows.index', compact('workflows'));
    }

    // 2. หน้าฟอร์มสร้าง Workflow ใหม่ (Create Form)
    // 2. หน้าฟอร์มสร้าง Workflow ใหม่ (Create Form)
    public function create()
    {
        // ➕ ดึง Role และ User มาส่งให้หน้า View ด้วย เพื่อไม่ให้เกิด Error ตัวแปรหาย
        $roles = Role::all();
        $users = User::all();

        return view('admin.workflows.create', compact('roles', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:approval_workflows,name',
            'description' => 'nullable|string',
            'steps' => 'required|array|min:1', // ต้องมีอย่างน้อย 1 ขั้นตอน
            'steps.*.step_order' => 'required|integer',
        ]);

        DB::beginTransaction();
        try {
            // 1. สร้างหัวข้อ Workflow หลัก
            $workflow = ApprovalWorkflow::create([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
            ]);

            // 2. วนลูปบันทึก Steps ทั้งหมดที่ส่งมาจากหน้าฟอร์ม (Array)
            foreach ($request->steps as $stepData) {
                $workflow->steps()->create([
                    'step_order' => $stepData['step_order'],
                    'role_name' => $stepData['role_name'] ?? null,
                    'specific_user_id' => $stepData['specific_user_id'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.workflows.index')->with('success', 'สร้างสายการอนุมัติและขั้นตอนสำเร็จ');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:approval_workflows,name,' . $id,
            'description' => 'nullable|string',
            'steps' => 'required|array|min:1',
            'steps.*.step_order' => 'required|integer',
        ]);
        
        DB::beginTransaction();
        try {
            $workflow = ApprovalWorkflow::findOrFail($id);

            // 1. อัปเดตข้อมูลหลัก
            $workflow->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
            ]);

            // 2. เคลียร์ Steps เก่าทิ้งทั้งหมด แล้วสร้างใหม่ตามที่ส่งมาใน Array
            $workflow->steps()->delete();

            foreach ($request->steps as $stepData) {
                $workflow->steps()->create([
                    'step_order' => $stepData['step_order'],
                    'role_name' => $stepData['role_name'] ?? null,
                    'specific_user_id' => $stepData['specific_user_id'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.workflows.index')->with('success', 'อัปเดตสายการอนุมัติสำเร็จ');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage())->withInput();
        }
    }

    // 4. หน้าจอรายละเอียด / แก้ไข Workflow และจัดการ Steps (Edit)
    public function edit($id)
    {
        $workflow = ApprovalWorkflow::with('steps')->findOrFail($id);
        // ดึง Role หรือ User ทั้งหมดไปให้ Admin เลือกตอนตั้งค่าขั้นอนุมัติ
        $roles = Role::all(); // ตัวอย่างใช้ Spatie Role
        $users = User::all(); // กรณีอยากระบุตัวบุคคลเฉพาะเจาะจง

        return view('admin.workflows.edit', compact('workflow', 'roles', 'users'));
    }

   

    // 6. ลบ Workflow (Delete)
    public function destroy($id)
    {
        $workflow = ApprovalWorkflow::findOrFail($id);
        $workflow->delete(); // ตาราง steps จะถูกลบตามเพราะตั้ง onDelete('cascade')

        return redirect()->route('admin.workflows.index')->with('success', 'ลบสายการอนุมัติเรียบร้อยแล้ว');
    }

    // --- ฟังก์ชันจัดการ Step (เพิ่ม/ลบ ขั้นตอนย่อยใน Workflow นั้นๆ) ---
}