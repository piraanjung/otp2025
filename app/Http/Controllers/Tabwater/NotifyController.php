<?php

namespace App\Http\Controllers\Tabwater;

use App\Http\Controllers\Controller;
use App\Models\Admin\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifyController extends Controller
{
   public function index()
    {
        $orgInfos = Organization::getOrgName(Auth::user()->org_id_fk);
        return view('tabwater.notify.index', compact('orgInfos'));
    }

    public function store(Request $request)
    {
        // 1. Validate ข้อมูล
       $request->validate([
            'issue_type' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'photo_camera' => 'nullable|image',
            'photo_gallery' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo_camera')) {
        $file = $request->file('photo_camera');
    }elseif ($request->hasFile('photo_gallery')) {
        $file = $request->file('photo_gallery');
    }
        $imagePath = null;
        if ($file) {
            $imageName = time().'.'.$file->extension();
            $file->move(public_path('uploads/notify'), $imageName);
            $imagePath = 'uploads/notify/' . $imageName;
        }


        return back()->with('success', 'แจ้งเหตุเรียบร้อยแล้ว! พิกัด: ' . $request->latitude . ', ' . $request->longitude);
    }
}