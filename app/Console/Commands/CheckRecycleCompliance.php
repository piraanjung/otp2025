<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\RecycleTransaction;
use App\Models\AnnualTrashSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CheckRecycleCompliance extends Command
{
    // คำสั่งที่ใช้รันผ่าน terminal: php artisan recycle:check-waive
    protected $signature = 'recycle:check-waive';
    protected $description = 'ตรวจสอบน้ำหนักขยะรีไซเคิลเพื่อยกเว้นค่าธรรมเนียมขยะรายปี';

    public function handle()
    {
        $this->info('กำลังเริ่มตรวจสอบเงื่อนไขการยกเว้นค่าขยะ...');

        // กำหนดเงื่อนไข (ตัวอย่าง: ต้องส่งขยะรวม >= 5 กก. ในเดือนที่ผ่านมา)
        $minWeight = 5.0;
        $lastMonth = Carbon::now()->subMonth();

        // 1. ดึง User ทั้งหมดที่มีการสมัครสมาชิกขยะรายปี
        $subscriptions = AnnualTrashSubscription::all();

        foreach ($subscriptions as $sub) {
            // 2. คำนวณน้ำหนักขยะรีไซเคิลรวมของเดือนที่แล้ว
            $totalWeight = RecycleTransaction::where('user_id', $sub->user_id)
                ->where('type', 'deposit')
                ->whereMonth('created_at', $lastMonth->month)
                ->whereYear('created_at', $lastMonth->year)
                ->sum('weight_kg');

            // 3. ตัดสินใจสลับสถานะ
            if ($totalWeight >= $minWeight) {
                // ผ่านเกณฑ์ -> ยกเว้นค่าขยะ (Waived)
                $sub->update([
                    'billing_status' => 'waived',
                    'waive_reason' => "ผ่านเกณฑ์เดือน {$lastMonth->format('M')}: ส่งขยะ {$totalWeight} กก.",
                    'last_checked_at' => now(),
                ]);
                $this->line("User ID: {$sub->user_id} - [PASS] น้ำหนัก {$totalWeight} กก. (ได้สิทธิ์ฟรี)");
            } else {
                // ไม่ผ่านเกณฑ์ -> ต้องจ่ายเงิน (Pending)
                $sub->update([
                    'billing_status' => 'pending',
                    'waive_reason' => "ไม่ผ่านเกณฑ์เดือน {$lastMonth->format('M')}: ส่งขยะเพียง {$totalWeight} กก.",
                    'current_debt' => DB::raw("current_debt + monthly_fee"), // เพิ่มหนี้เข้าไป
                    'last_checked_at' => now(),
                ]);
                $this->warn("User ID: {$sub->user_id} - [FAIL] น้ำหนัก {$totalWeight} กก. (ต้องชำระเงิน)");
            }
        }

        $this->info('ตรวจสอบเสร็จสิ้นเรียบร้อยแล้ว!');
    }
}
