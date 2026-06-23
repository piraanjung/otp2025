<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnnualTrashSubscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnualTrashAdminController extends Controller
{
    // หน้าแสดงรายชื่อสมาชิกขยะรายปีทั้งหมด
    public function index(Request $request)
    {
        $query = AnnualTrashSubscription::with('user');

        // ค้นหาตามสถานะ (จ่าย/ฟรี/ค้างชำระ)
        if ($request->status) {
            $query->where('billing_status', $request->status);
        }

        $subscriptions = $query->paginate(20);
        return view('admin.annual_trash.index', compact('subscriptions'));
    }

    // อัปเดตสถานะสิทธิ์ด้วยมือ (Manual Override)
    public function updateStatus(Request $request, $id)
    {
        $sub = AnnualTrashSubscription::findOrFail($id);

        $sub->update([
            'billing_status' => $request->billing_status,
            'waive_reason'   => $request->waive_reason . ' (โดยแอดมิน: ' . Auth::user()->firstname . ')',
            'last_checked_at' => now(),
        ]);

        return back()->with('success', 'อัปเดตสิทธิ์เรียบร้อยแล้ว');
    }
}
