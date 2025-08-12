<?php

namespace App\Services;

use App\Models\User;
use App\Models\KeptKaya\UserWastePreference;
use App\Models\KeptKaya\WasteBin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // สำหรับการ Log

class UserWasteStatusService
{
    /**
     * อัปเดตสถานะ is_annual_collection และ is_waste_bank ของ User
     * โดยอิงจากสถานะถังขยะและ Logic ทางธุรกิจที่ซับซ้อน
     *
     * @param User $user
     * @return void
     */
    public function updateOverallUserWasteStatus(User $user)
    {
        DB::transaction(function () use ($user) {
            // 1. ดึงหรือสร้าง UserWastePreference สำหรับ User นี้
            $preference = $user->wastePreference()->firstOrCreate(['user_id' => $user->id]);

            $oldIsAnnualCollection = $preference->is_annual_collection;
            $oldIsWasteBank = $preference->is_waste_bank;

            // 2. คำนวณสถานะ is_annual_collection ใหม่:
            // ผู้ใช้มี annual_collection เป็น true ถ้ามีถังขยะอย่างน้อย 1 ใบที่ active สำหรับ annual collection
            $newIsAnnualCollection = $user->wasteBins()
                                          ->where('is_active_for_annual_collection', true)
                                          ->exists();

            // 3. คำนวณสถานะ is_waste_bank ใหม่:
            $newIsWasteBank = $oldIsWasteBank; // เริ่มต้นจากค่าเดิม

            // Logic: ถ้า annual_collection (ใหม่) เป็น false
            // และ annual_collection (เก่า) เคยเป็น true
            // และ waste_bank (เก่า) เป็น false
            // -> บังคับให้ waste_bank (ใหม่) เป็น true
            if (!$newIsAnnualCollection && $oldIsAnnualCollection && !$oldIsWasteBank) {
                $newIsWasteBank = true;
                Log::info("User {$user->id}: Annual Collection changed from TRUE to FALSE. Forcing Waste Bank to TRUE.");
            }
            // เคสอื่นๆ: ถ้า user ตั้งใจจะเปิด/ปิด waste_bank เอง, หรือ annual_collection ยังเป็น true
            // หรือ waste_bank เคยเป็น true อยู่แล้ว -> ไม่ต้องไปบังคับ
            // Logic นี้ออกแบบมาเพื่อ "บังคับเปิด" waste_bank เมื่อ annual_collection ถูกปิดเท่านั้น
            // ถ้า user ปิด waste_bank เอง (จาก UI) ก็ให้เป็น false ได้
            // หรือถ้า annual_collection เป็น true อยู่แล้ว ก็ไม่บังคับ waste_bank

            // 4. บันทึกการเปลี่ยนแปลงใน UserWastePreference (ถ้ามีอะไรเปลี่ยน)
            if ($preference->is_annual_collection !== $newIsAnnualCollection ||
                $preference->is_waste_bank !== $newIsWasteBank)
            {
                $preference->update([
                    'is_annual_collection' => $newIsAnnualCollection,
                    'is_waste_bank' => $newIsWasteBank,
                ]);
                Log::info("User {$user->id} Waste Preference Updated: Annual({$oldIsAnnualCollection} -> {$newIsAnnualCollection}), WasteBank({$oldIsWasteBank} -> {$newIsWasteBank})");
            }
        });
    }

  
    public function updateWasteBinAndUserStatus(WasteBin $wasteBin, array $data)
    {
        DB::transaction(function () use ($wasteBin, $data) {
            $wasteBin->update($data); // อัปเดตสถานะถังขยะ

            // หลังจากอัปเดตถังขยะแล้ว ให้เรียกอัปเดตสถานะของ User โดยรวม
            $this->updateOverallUserWasteStatus($wasteBin->user);
        });
    }
}