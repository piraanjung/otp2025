<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InvItem;
use App\Models\InvLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvLocationController extends Controller
{
    public function index()
{
    $user = Auth::user();
    // ดึงข้อมูลเฉพาะขององค์กรตัวเอง
    $locations = InvLocation::where('org_id_fk', $user->org_id_fk)
                    ->orderBy('created_at', 'desc')
                    ->get();
    $departments = InvLocation::where('org_id_fk', $user->org_id_fk)
                    ->whereNotNull('department')
                    ->distinct()
                    ->pluck('department');

    return view('inventory.locations.index', compact('locations','departments'));
}

    public function store(Request $request)
{
    $request->validate([
        'department' => 'required|string|max:255',
        'location'   => 'required|string|max:255',
        'description'=> 'nullable|string',
    ]);

    $user = Auth::user();

    InvLocation::create([
        'org_id_fk'   => $user->org_id_fk,
        'department'  => $request->department,
        'location'        => $request->location,
        'description' => $request->description,
    ]);

    return redirect()->route('inventory.locations.index')
        ->with('success', 'บันทึกข้อมูลพื้นที่จัดเก็บเรียบร้อยแล้ว');
}

    public function destroy($id)
    {
        $user = Auth::user();
        $location = InvLocation::where('org_id_fk', $user->org_id_fk)->findOrFail($id);
        $location->delete();

        return redirect()->back()->with('success', 'ลบพื้นที่จัดเก็บเรียบร้อยแล้ว');
    }


}