<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvHazardLevel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvHazardLevelController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $hazards = InvHazardLevel::where('org_id_fk', $user->org_id_fk)->get();
        return view('inventory.settings.inv_hazard_index', compact('hazards'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'image' => 'nullable|image|max:1024', // รูปไม่เกิน 1MB
        ]);

        $user = Auth::user();
        $imagePath = null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/hazard_icons');
            $imagePath = str_replace('public/', '', $path);
        }

        InvHazardLevel::create([
            'org_id_fk' => $user->org_id_fk,
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'image_path' => $imagePath
        ]);

        return back()->with('success', 'เพิ่มระดับอันตรายเรียบร้อย');
    }

    public function destroy($id)
    {
        $hazard = InvHazardLevel::findOrFail($id);
        // ลบรูปทิ้งด้วยเพื่อไม่ให้รก Server
        if($hazard->image_path) {
            Storage::delete('public/'.$hazard->image_path);
        }
        $hazard->delete();
        return back()->with('success', 'ลบข้อมูลเรียบร้อย');
    }
}