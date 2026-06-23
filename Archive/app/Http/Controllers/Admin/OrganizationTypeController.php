<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\OrganizationType;
use Illuminate\Http\Request;

class OrganizationTypeController extends Controller
{
    public function index()
    {
        $types = OrganizationType::all();
        return view('admin.org_types.index', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:organization_types,name']);
        OrganizationType::create($request->all());
        return back()->with('success', 'บันทึกประเภทหน่วยงานเรียบร้อย');
    }

    public function update(Request $request, $id)
    {
        $type = OrganizationType::findOrFail($id);
        $type->update($request->all());
        return back()->with('success', 'อัปเดตข้อมูลเรียบร้อย');
    }

    public function destroy($id)
    {
        OrganizationType::destroy($id);
        return back()->with('success', 'ลบข้อมูลเรียบร้อย');
    }
}
