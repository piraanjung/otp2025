<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session; // เพิ่ม Session

trait BelongsToOrganization
{
    /**
     * Boot the BelongsToOrganization trait for a model.
     * Laravel จะเรียกฟังก์ชันนี้อัตโนมัติ เพราะชื่อมันตรงกับ boot[TraitName]
     *
     * @return void
     */
    public static function bootBelongsToOrganization()
    {
        // 1. Global Scope: Auto Filter (เติม where org_id_fk อัตโนมัติ)
        static::addGlobalScope('org', function (Builder $builder) {
            // เช็คว่า User Login อยู่จริง และมี org_id_fk
            if (Auth::check() && Auth::user()->org_id_fk) {
                $builder->where('org_id_fk', Auth::user()->org_id_fk);
            }
        });

        // 2. Creating Event: Auto Fill (เติม org_id_fk ตอน create อัตโนมัติ)
        // ** สำคัญมาก: ถ้าคุณลบส่วนนี้ เวลา create ข้อมูลใหม่ คุณต้องใส่ org_id_fk เองทุกครั้ง **
        static::creating(function (Model $model) {
            if (Auth::check() && Auth::user()->org_id_fk) {
                // เติมให้เฉพาะตอนที่ยังว่างอยู่
                if (empty($model->org_id_fk)) {
                    $model->org_id_fk = Auth::user()->org_id_fk;
                }
            }
        });
    }

    /* * หมายเหตุ: ผมเอา getConnectionName() ออกแล้ว 
     * เพราะคุณแจ้งว่าเปลี่ยนมาใช้ org_id_fk (Single DB) แทนการสลับ Connection
     * ถ้าขืนใส่ไว้ อาจจะเกิด Error หา Database ไม่เจอครับ
     */
}
// trait BelongsToOrganization
// {
//     // -----------------------------------------------------------
//     // [ใหม่] ส่วนที่ 1: สลับ Connection Database อัตโนมัติ
//     // -----------------------------------------------------------
//     public function getConnectionName()
//     {
//         // ถ้าใน Session มีค่า db_conn ให้ใช้ค่า connection นั้น
//         if (Session::has('db_conn')) {
//             return Session::get('db_conn');
//         }

//         // ถ้าไม่มี ให้ใช้ค่า Default จาก Config
//         return config('database.default');
//     }

//     // -----------------------------------------------------------
//     // ส่วนที่ 2: Logic เดิมของคุณ (Scope + Creating)
//     // -----------------------------------------------------------
//     public static function bootBelongsToOrganization()
//     {
//         // 1. Global Scope: เวลา Select จะเติม where('org_id_fk', ...) อัตโนมัติ
//         static::addGlobalScope('organization', function (Builder $builder) {
//             if (Auth::check()) {
//                 $user = Auth::user();
//                 if (isset($user->org_id_fk)) {
//                     $builder->where('org_id_fk', $user->org_id_fk);
//                 }
//             }
//         });

//         // 2. Creating Event: เวลา Save จะเติม org_id_fk อัตโนมัติ
//         static::creating(function (Model $model) {
//             if (Auth::check()) {
//                 $user = Auth::user();
//                 if (isset($user->org_id_fk) && empty($model->org_id_fk)) {
//                     $model->org_id_fk = $user->org_id_fk;
//                 }
//             }
//         });
//     }
// }