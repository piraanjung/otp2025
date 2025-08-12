<?php

namespace App\Http\Controllers\KeptKaya;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// เปลี่ยนชื่อ Model ที่ Import ตรงนี้
use App\Models\Keptkaya\KpUsergroupPayratePerMonth; 
use App\Models\Keptkaya\KpUsergroup;
use App\Models\BudgetYear;

class KpUsergroupPayratePerMonthController extends Controller
{
    // ... (เมธอดต่างๆ เช่น index, create, store, show, edit, update, destroy)

    public function index()
    {
        // เปลี่ยนชื่อ Model ตรงนี้
        $payrates = KpUsergroupPayratePerMonth::with(['kp_usergroup', 'budgetyear'])
                                            ->where('deleted', '0')
                                            ->orderBy('budgetyear_idfk', 'desc')
                                            ->orderBy('kp_usergroup_idfk', 'asc')
                                            ->paginate(10);
        return view('keptkaya.payrate_per_months.index', compact('payrates'));
    }

     public function create()
    {
        // ดึงข้อมูลสำหรับ dropdowns
        $usergroups = KpUsergroup::where('status', 'active')->get();
        $budgetYears = BudgetYear::where('status', 'active')->orderBy('id', 'desc')->get(); // สมมติ BudgetYear มี field 'year'

        return view('keptkaya.payrate_per_months.create', compact('usergroups', 'budgetYears'));
    }


    public function store(Request $request)
    {
        $request->validate([ 
            'payrate_peryear' =>'required'
         ]);
        
        // เปลี่ยนชื่อ Model ตรงนี้
        $existingRecord = KpUsergroupPayratePerMonth::where('kp_usergroup_idfk', $request->kp_usergroup_idfk)
                                                    ->where('budgetyear_idfk', $request->budgetyear_idfk)
                                                    ->first();
        if ($existingRecord) { 
            return redirect()->route('keptkaya.payrate_per_months.index')
            ->with('error', 'xx');
         }

        try {
            // เปลี่ยนชื่อ Model ตรงนี้
            KpUsergroupPayratePerMonth::create([ 
                "kp_usergroup_idfk" => $request->get('kp_usergroup_idfk'),
                "budgetyear_idfk"   => $request->get('budgetyear_idfk'),
                "payrate_permonth"  => $request->get('payrate_permonth'),
                "status"            => $request->get('status'),
                "deleted"           => '0',
                "created_at"        => date('Y-m-d H:i:s'),
                "updated_at"        => date('Y-m-d H:i:s'),

             ]);
            return redirect()->route('keptkaya.payrate_per_months.index')->with('success', 'เพิ่มอัตราค่าบริการสำเร็จแล้ว.');
        } catch (\Exception $e) { 
            return redirect()->route('keptkaya.payrate_per_months.index')->with('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล'.$e);
         }
    }

    public function edit($id)
    {
        // เปลี่ยนชื่อ Model ตรงนี้
        $payrate = KpUsergroupPayratePerMonth::with(['kpUsergroup', 'budgetYear'])->where('deleted', 0)->findOrFail($id);
        // ... (ดึงข้อมูลสำหรับ dropdowns)
        return view('keptkaya.payrate_per_months.edit', compact('payrate', 'usergroups', 'budgetYears'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([ /* ... validation rules ... */ ]);
        
        // เปลี่ยนชื่อ Model ตรงนี้
        $payrate = KpUsergroupPayratePerMonth::where('deleted', 0)->findOrFail($id);

        // เปลี่ยนชื่อ Model ตรงนี้
        $existingRecord = KpUsergroupPayratePerMonth::where('kp_usergroup_idfk', $request->kp_usergroup_idfk)
                                                    ->where('budgetyear_idfk', $request->budgetyear_idfk)
                                                    ->where('id', '!=', $id)
                                                    ->first();
        if ($existingRecord) { /* ... handle duplicate ... */ }

        try {
            $payrate->update([ /* ... data ... */ ]);
            return redirect()->route('keptkaya.payrate_per_months.index')->with('success', 'อัปเดตอัตราค่าบริการสำเร็จแล้ว.');
        } catch (\Exception $e) { /* ... handle error ... */ }
    }

    public function destroy($id)
    {
        try {
            // เปลี่ยนชื่อ Model ตรงนี้
            $payrate = KpUsergroupPayratePerMonth::where('deleted', 0)->findOrFail($id);
            $payrate->update(['deleted' => 1, 'status' => 'inactive']);
            return redirect()->route('keptkaya.payrate_per_months.index')->with('success', 'ลบอัตราค่าบริการสำเร็จแล้ว.');
        } catch (\Exception $e) { /* ... handle error ... */ }
    }
}