<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\ManagesTenantConnection;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organization extends Model
{
    use HasFactory, ManagesTenantConnection; // 👈 เพิ่ม Trait นี้เข้ามา
    protected $table  = 'organizations';
    protected $fillable = [
        'id',
        'org_code',
        'org_type_name',
        'org_short_type_name',
        'org_name',
        'org_address',
        'org_zone_id_fk',
        'org_tambon_id_fk',
        'org_district_id_fk',
        'org_province_id_fk',
        'org_route',
        'org_zipcode',
        'org_phone',
        'org_dept_name',
        'org_dept_short_name',
        'org_dept_phone',
        'org_head_name',
        'fin_head_name',
        'tw_head_name',
        'fin_head_license_img',
        'org_head_license_img',
        'tw_head_license_img',
        'org_logo_img',
        'meternumbercode',
        'org_database',
        'cutmeter_count',
        'vat',
    ];

    protected $casts = [
        'vat' => 'float:2', // แปลง vat เป็น float และแสดง 2 ตำแหน่ง
    ];
    public function provinces()
    {
        return $this->belongsTo(Province::class, 'org_province_id_fk', 'id');
    }

    public function districts()
    {
        return $this->belongsTo(District::class, 'org_district_id_fk', 'id');
    }

    public function tambons()
    {
        return $this->belongsTo(Tambon::class, 'org_tambon_id_fk', 'id');
    }

    public function zones()
    {
        return $this->belongsTo(Zone::class, 'org_zone_id_fk', 'id');
    }

    public static function getOrgInfos($org_id_fk)
    {
        $organization = Organization::setTenantConnection(session('org_id'));
        $connection = $organization::with(['provinces', 'tambons', 'districts', 'zones'])
            ->get()->first();
        return [
            'id'  => $org_id_fk,
            'org_database' => $connection->org_database,
            'org_zipcode' => $connection->org_zipcode,
            'org_address' => $connection->org_address,
            'org_phone' => $connection->org_phone,
            'org_head_name' =>  $connection->org_head_name,
            'org_code' => $connection->org_code,
            'org_guard' => $connection->org_guard,
            'org_provinces' => $connection->provinces->province_name,
            'org_districts' => $connection->districts->district_name,
            'org_tambons' => $connection->tambons->tambon_name,
            'org_zone' => $connection->zones->zone_name,
            'org_logo_img' => $connection->org_logo_img,
            'org_type_name' => $connection->org_type_name,
            'org_name' => $connection->org_name,
            'org_short_type_name' => $connection->org_short_name,
            'org_dept_name' => $connection->org_dept_name,
            'org_dept_phone' => $connection->org_dept_phone,
            'vat' => $connection->vat
        ];
    }

    public static function getOrgName($org_id_fk)
    {
        $organization = (new Organization())->setConnection('envsogo_super_admin')->where('id', $org_id_fk);
        $connection = $organization->with(['provinces', 'tambons', 'districts', 'zones'])
            ->get()->first();
        return [
            'id'  => $org_id_fk,
            'org_database' => $connection->org_database,
            'org_code' => $connection->org_code,
            'org_zipcode' => $connection->org_zipcode,
            'org_provinces' => $connection->provinces->province_name,
            'org_districts' => $connection->districts->district_name,
            'org_tambons' => $connection->tambons->tambon_name,
            'org_zone' => $connection->zones->zone_name,
            'org_logo_img' => $connection->org_logo_img,
            'org_type_name' => $connection->org_type_name,
            'org_name' => $connection->org_name,
            'org_short_type_name' => $connection->org_short_type_name,
            'org_dept_name' => $connection->org_dept_name,
        ];
    }
}
