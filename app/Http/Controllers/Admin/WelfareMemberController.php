<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WelfareMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WelfareMemberController extends Controller
{
    // 1. แสดงรายชื่อสมาชิกกองทุนปัจจุบัน
    public function index(Request $request)
    {
        $orgId = Auth::user()->org_id_fk;
        
        $members = WelfareMember::where('org_id_fk', $orgId)
            ->with('user')
            ->when($request->search, function($query, $search) {
                $query->whereHas('user', function($q) use ($search) {
                    $q->where('firstname', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->paginate(15);

        return view('admin.welfare.members.index', compact('members'));
    }

    // 2. หน้าจอเลือกสมาชิกกลาง (Master Users) ที่ยังไม่ได้เป็นสมาชิกกองทุน เพื่อมาติ๊กเพิ่ม
    public function create(Request $request)
    {
        $orgId = Auth::user()->org_id_fk;

        // ดึง ID ของคนที่เป็ฯสมาชิกกองทุนไปแล้ว เพื่อเอามาexclude ออก
        $existingUserIds = WelfareMember::where('org_id_fk', $orgId)->pluck('user_id');

        // ดึง User ในองค์กรที่ยังไม่เคยเข้ากองทุนฌาปนกิจ
        $availableUsers = User::where('org_id_fk', $orgId)
            ->whereNotIn('id', $existingUserIds)
            ->when($request->search, function($query, $search) {
                $query->where('firstname', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
            })
            ->paginate(20);

        return view('admin.welfare.members.create', compact('availableUsers'));
    }

    // 3. บันทึกการเพิ่มสมาชิกแบบ Bulk (ติ๊กเลือกหลายคน)
    public function bulkStore(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $orgId = Auth::user()->org_id_fk;
        $count = 0;

        foreach ($request->user_ids as $userId) {
            $exists = WelfareMember::where('org_id_fk', $orgId)->where('user_id', $userId)->exists();

            if (!$exists) {
                // ดึงประเภทสมาชิกที่กำหนดรายคน (ถ้ามี) หรือใช้ค่า default 'both'
                $membershipType = $request->input("membership_type.{$userId}", 'both');

                WelfareMember::create([
                    'org_id_fk' => $orgId,
                    'user_id' => $userId,
                    'membership_type' => $membershipType,
                    'status' => 'active',
                    'registered_at' => now(),
                ]);
                $count++;
            }
        }

        return redirect()->route('admin.welfare.members.index')
            ->with('success', "ลงทะเบียนสมาชิกเข้ากองทุนฌาปนกิจสำเร็จจำนวน {$count} คน");
    }

    // 4. ลบ/ยกเลิกสิทธิ์สมาชิกกองทุน
    public function destroy($id)
    {
        $member = WelfareMember::findOrFail($id);
        $member->delete();

        return back()->with('success', 'ยกเลิกสิทธิ์สมาชิกกองทุนเรียบร้อยแล้ว');
    }
}