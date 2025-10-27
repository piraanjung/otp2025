<?php

namespace App\Models\Tabwater;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwMeterType extends Model
{
    use HasFactory;

    protected $table = 'tw_meter_types';
    protected $fillable = ['id','meter_type_name', 'org_id_fk', 'description', 'metersize', 'status']; // ยังคงเหมือนเดิม


    // เพิ่ม Relationship เพื่อเชื่อมโยงกับ MeterTypeRateConfig
    public function rateConfigs()
    {
        return $this->hasMany(MeterTypeRateConfig::class, 'meter_type_id', 'id');
    }

    /**
     * Get the current active rate configuration for this meter type.
     * ดึงข้อมูลอัตราปัจจุบันที่ใช้งานอยู่สำหรับประเภทมิเตอร์นี้
     */
    public function currentRateConfig()
    {
        return $this->hasOne(MeterTypeRateConfig::class)
                    ->where('is_active', true)
                    ->where(function ($query) {
                        $query->whereNull('end_date')
                              ->orWhere('end_date', '>=', now());
                    })
                    ->orderBy('effective_date', 'desc'); // ดึงอันล่าสุด
    }
}
