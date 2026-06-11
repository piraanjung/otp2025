<?php

namespace App\Imports;

use App\Models\KeptKaya\KpUserWastePreference;
use App\Models\RecycleBankAccount;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UserImport implements ToModel, WithHeadingRow
{

    public function model(array $row)
    {
        // 1. สร้าง User ตาม fillable ที่คุณกำหนด
        $user = new User([
            "org_id_fk"     => isset($row['org_id_fk']) ? (int)$row['org_id_fk'] : Auth::user()->org_id_fk,
            "username"      => trim($row['username']),
            "prefix"        => trim($row['prefix']),
            "firstname"     => trim($row['firstname']),
            "lastname"      => trim($row['lastname']),
            "email"         => trim($row['email']),
            // ป้องกันรหัสผ่านที่เป็นตัวเลขเพียวๆ จาก Excel แปลงค่าผิดรูปแบบด้วยการแคสเป็น (string)
            "password"      => Hash::make(isset($row['password']) ? (string)$row['password'] : '12345678'),
            "id_card"       => isset($row['id_card']) ? (string)$row['id_card'] : null,
            "line_id"       => isset($row['line_id']) ? (string)$row['line_id'] : '0',
            "line_user_id"  => isset($row['line_user_id']) ? (string)$row['line_user_id'] : '0',
            "image"         => $row['image'] ?? null,
            "phone"         => isset($row['phone']) ? (string)$row['phone'] : null,
            "age"           => isset($row['age']) ? (int)$row['age'] : null,
            "weight"        => isset($row['weight']) ? (float)$row['weight'] : null,
            "height"        => isset($row['height']) ? (float)$row['height'] : null,
            // ตรวจสอบว่าค่า gender ใน Excel ตรงกับ 'w' หรือ 'm' ใน enum จริงๆ ไม่ใช่ค่าว่าง
            "gender"        => !empty($row['gender']) ? trim($row['gender']) : 'w',
            "address"       => isset($row['address']) ? (string)$row['address'] : null,
            "zone_id"       => isset($row['zone_id']) ? (int)$row['zone_id'] : null,
            "subzone_id"    => isset($row['subzone_id']) ? (int)$row['subzone_id'] : null,
            "tambon_code"   => isset($row['tambon_code']) ? (int)$row['tambon_code'] : null,
            "district_code" => isset($row['district_code']) ? (int)$row['district_code'] : null,
            "province_code" => isset($row['province_code']) ? (int)$row['province_code'] : null,
            "status"        => 'active', // ล็อคค่าสตริงตรงตามสเปก enum
            "created_at"    => date('Y-m-d H:i:s'),
            "updated_at"    => date('Y-m-d H:i:s'),
        ]);
        // 2. บันทึกข้อมูล
        $user->save();

        $kpuserPref = new KpUserWastePreference([
            "org_id_fk"             => isset($row['org_id_fk']) ? (int)$row['org_id_fk'] : Auth::user()->org_id_fk,
            'user_id'               => $user->id,
            'is_annual_collection'  => 0,
            'is_waste_bank'         => 1,
            "address"               => $user->address,
            "zone_id"               => $user->zone_id,
            "subzone_id"            => $user->subzone_id,
            "tambon_code"           => $user->tambon_code,
            "district_code"         => $user->district_code,
            "province_code"         => $user->province_code,
            "created_at"            => date('Y-m-d H:i:s'),
            "updated_at"            => date('Y-m-d H:i:s'),
        ]);

        $kpuserPref->save();

        RecycleBankAccount::create([
            'user_id'       => $user->id,
            'account_no'    => "002".substr("000",strlen($user->id)).$user->id,
            'balance'       => 0,
            'points'        => 0,
            'status'        => 'active',
            "created_at"    => date('Y-m-d H:i:s'),
            "updated_at"    => date('Y-m-d H:i:s'),
        ]);

        // 3. Assign Role เป็น 'User' (Spatie Permission)
        $user->assignRole('User');



        return $user;
    }
}
