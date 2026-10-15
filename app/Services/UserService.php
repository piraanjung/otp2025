<?php

namespace App\Services;

use App\Models\User;
use App\Models\KpBankAccount;
use App\Models\FoodWaste\FoodWasteAccount;
use App\Models\AnnualTrash\AnnualTrashSubscription;
use App\Models\AnnualTrash\AnnualTrashPayratePerMonth;
use App\Services\UserWasteStatusService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserService
{
    protected $wasteStatusService;

    /**
     * Dependency Injection สำหรับ UserWasteStatusService
     */
    public function __construct(UserWasteStatusService $wasteStatusService)
    {
        $this->wasteStatusService = $wasteStatusService;
    }

    /**
     * อัปเดตสิทธิ์บริการของผู้ใช้งานหลายคนพร้อมกัน (Batch Update)
     */
    public function updateServices(array $services)
    {
        DB::beginTransaction();
        try {
            foreach ($services as $userId => $data) {
                $user = User::find($userId);
                if (!$user) continue;

                // 1. ซิงค์สิทธิ์บริการในตารางของแต่ละโมดูล
                $this->syncUserServices($user, $data);

                // 2. เรียกใช้ UserWasteStatusService เพื่ออัปเดตสิทธิ์ในตาราง Preference ให้ตรงกัน
                $this->wasteStatusService->updateOverallUserWasteStatus($user);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * จัดการเปิด/ปิด สิทธิ์บริการรายบุคคลตามโมดูล
     */
    public function syncUserServices(User $user, array $data)
    {
        $org = $user->organization ?? Auth::user()->organization;
        $orgIdStr = substr('000', strlen($org->id ?? 0)) . ($org->id ?? 0);
        $userIdStr = substr('0000', strlen($user->id)) . $user->id;
        $accNumberText = ($org->org_code ?? 'ORG') . "-" . $orgIdStr . $userIdStr;

        // -------------------------------------------------------------
        // 1. ธนาคารขยะรีไซเคิล (Recycle Bank)
        // -------------------------------------------------------------
        if (isset($data['recycle']) && $data['recycle'] == "1") {
            if (collect($user->recycleBankAccount)->isEmpty()) {
                KpBankAccount::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'entity_type' => 'recycle_bank',
                    ],
                    [
                        'account_no' => 'RC-' . $accNumberText,
                        'status' => 'active',
                        'org_id_fk' => $org->id ?? Auth::user()->org_id_fk
                    ]
                );
                if (method_exists($user, 'assignRole')) {
                    $user->assignRole('Recycle Bank User');
                }
            } else {
                $user->recycleBankAccount->update(['status' => 'active']);
            }
        } else {
            if (collect($user->recycleBankAccount)->isNotEmpty()) {
                $user->recycleBankAccount->update(['status' => 'inactive']);
            }
        }

        // -------------------------------------------------------------
        // 2. ธนาคารขยะเปียก / เศษอาหาร (Food Waste Bank)
        // -------------------------------------------------------------
        if (isset($data['food_waste']) && $data['food_waste'] == "1") {
            if (collect($user->foodWasteBankAccount)->isEmpty()) {
                KpBankAccount::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'entity_type' => 'foodwaste_bank',
                    ],
                    [
                        'account_no' => 'FW-' . $accNumberText,
                        'status' => 'active',
                        'org_id_fk' => $org->id ?? Auth::user()->org_id_fk
                    ]
                );
                FoodWasteAccount::firstOrCreate([
                    'user_id' => $user->id,
                    'org_id_fk' => Auth::user()->org_id_fk ?? ($org->id ?? null),
                    'last_contributed_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                $user->foodWasteBankAccount->update(['status' => 'active']);
            }
        } else {
            if (collect($user->foodWasteBankAccount)->isNotEmpty()) {
                $user->foodWasteBankAccount->update(['status' => 'inactive']);
            }
            if ($user->foodWasteAccount()) {
                $user->foodWasteAccount()->delete();
            }
        }

        // -------------------------------------------------------------
        // 3. บริการจัดเก็บขยะรายปี (Annual Trash Subscription)
        // -------------------------------------------------------------
        if (isset($data['annual_trash']) && $data['annual_trash'] == "1") {
            $payRate = AnnualTrashPayratePerMonth::where('status', 1)->latest()->first();
            $monthFee = $payRate ? $payRate->payrate_permonth : 20.00;

            // ตรวจสอบเงื่อนไขยกเว้นค่าธรรมเนียม หากเป็นสมาชิกขยะรีไซเคิล
            $isRecycleMember = isset($data['recycle']) && $data['recycle'] == "1";
            $billingStatus = $isRecycleMember ? 'waived' : 'active';

            AnnualTrashSubscription::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'fiscal_year' => method_exists(AnnualTrashSubscription::class, 'calculateFiscalYear')
                                     ? AnnualTrashSubscription::calculateFiscalYear()
                                     : date('Y'),
                    'payrate_permonth_id_fk' => $payRate->id ?? null,
                    'month_fee' => $monthFee,
                    'annual_fee' => $monthFee * 12,
                    'status' => 'active',
                    'billing_status' => $billingStatus,
                ]
            );
        } else {
            if ($user->annualTrashSubscription()) {
                $user->annualTrashSubscription()->delete();
            }
        }
    }
}