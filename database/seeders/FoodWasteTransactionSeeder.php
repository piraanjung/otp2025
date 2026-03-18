<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FoodWaste\FoodWasteTransaction;
use Carbon\Carbon;

class FoodWasteTransactionSeeder extends Seeder
{
    public function run()
    {
        $types = ['earn_waste', 'redeem', 'buy_compost'];

        for ($i = 1; $i <= 50; $i++) {
            $randomType = $types[array_rand($types)];

            $points = 0;
            $amount = 0;
            $wasteLogId = null; // เริ่มต้นให้ไม่มี id ขยะ

            if ($randomType == 'earn_waste') {
                $points = rand(10, 100);
                $wasteLogId = rand(1, 20); // สมมติว่าดึงมาจากขยะ ID ที่ 1-20
            } elseif ($randomType == 'redeem') {
                $points = -(rand(50, 500));
            } elseif ($randomType == 'buy_compost') {
                $amount = rand(100, 1000);
            }

            $randomDate = Carbon::now()->subDays(rand(0, 180));

            FoodWasteTransaction::create([
                'fw_pref_id_fk' => 1,
                'waste_log_id' => $wasteLogId, // <--- เพิ่มตรงนี้
                'transaction_type' => $randomType,
                'points' => $points,
                'amount' => $amount,
                'note' => 'ข้อมูลทดสอบระบบที่ ' . $i,
                'staff_id' => 1,
                'created_at' => $randomDate,
                'updated_at' => $randomDate,
            ]);
        }

        $this->command->info('สร้างข้อมูลจำลองแบบสมบูรณ์แล้ว 50 รายการ!');
    }
}
