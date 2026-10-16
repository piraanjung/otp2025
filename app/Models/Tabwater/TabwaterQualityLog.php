<?php

namespace App\Models\Tabwater;

use App\Models\Admin\Staff;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabwaterQualityLog extends Model
{
    use HasFactory;

    protected $table = 'tabwater_quality_logs';

    protected $fillable = [
        'org_id_fk',
        'station_id',
        'data_source',
        'staff_id',
        'device_mac',
        'secchi_depth_cm',
        'turbidity_ntu',
        'tds_ppm',
        'ph_value',
        'ph_litmus_image',
        'water_temp_c',
        'water_level_percent',
        'alum_valve_level',
        'lime_bucket_count',
        'alum_used_kg',
        'lime_used_kg',
        'log_date',
        'log_time',
        'remark',
    ];

    protected $casts = [
        'secchi_depth_cm' => 'float',
        'turbidity_ntu' => 'float',
        'tds_ppm' => 'float',
        'ph_value' => 'float',
        'water_temp_c' => 'float',
        'water_level_percent' => 'float',
        'alum_used_kg' => 'float',
        'lime_used_kg' => 'float',
        'log_date' => 'date',
    ];

    // Relationships
    public function station()
    {
        return $this->belongsTo(TabwaterStation::class, 'station_id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    // Accessor & Helper: คำนวณคำแนะนำก๊อกสารส้มและปูนขาวภาษาช่าง
    public function getChemicalRecommendationAttribute()
    {
        $ntu = $this->turbidity_ntu;
        $ph = $this->ph_value;

        // คำแนะนำก๊อกสารส้ม
        if ($ntu <= 5.0) {
            $alumAdvice = 'หรี่ก๊อกสารส้มลงระดับเบาสุด';
            $alumLevel = 'low';
        } elseif ($ntu <= 20.0) {
            $alumAdvice = 'เปิดก๊อกสารส้มระดับปานกลาง';
            $alumLevel = 'medium';
        } else {
            $alumAdvice = 'เปิดก๊อกสารส้มระดับแรง (เร่งการจับตะกอน)';
            $alumLevel = 'high';
        }

        // คำแนะนำปูนขาวพยุง pH
        if ($ph !== null && $ph < 6.5) {
            $limeAdvice = 'ผสมน้ำปูนขาว 1-2 ถังเล็ก หยดพยุงค่า pH ไม่ให้น้ำกัดกร่อนท่อ';
            $limeBuckets = 2;
        } else {
            $limeAdvice = 'ค่า pH ปกติ ไม่ต้องเติมปูนขาวเพิ่ม';
            $limeBuckets = 0;
        }

        return [
            'alum_advice' => $alumAdvice,
            'alum_level' => $alumLevel,
            'lime_advice' => $limeAdvice,
            'recommended_lime_buckets' => $limeBuckets,
        ];
    }
}