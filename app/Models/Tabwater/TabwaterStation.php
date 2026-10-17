<?php

namespace App\Models\Tabwater;

use App\Models\Admin\Staff;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TabwaterStation extends Model
{
    use HasFactory;

    protected $table = 'tabwater_stations';

    protected $fillable = [
        'org_id_fk',
        'code',
        'name',
        'lat',
        'lng',
        'max_turbidity_ntu',
        'min_ph',
        'max_ph',
        'max_tds_ppm',
        'min_water_level_percent',
        'max_water_level_percent',
        'secchi_k_factor',
        'status',
        'deleted',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
        'max_turbidity_ntu' => 'float',
        'min_ph' => 'float',
        'max_ph' => 'float',
        'max_tds_ppm' => 'float',
        'min_water_level_percent' => 'float',
        'max_water_level_percent' => 'float',
        'secchi_k_factor' => 'float',
        'deleted' => 'boolean',
    ];

    // Relationships
    public function staffs()
    {
        return $this->belongsToMany(Staff::class, 'tabwater_station_staffs', 'station_id', 'staff_id')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function devices()
    {
        return $this->hasMany(TabwaterDevice::class, 'station_id');
    }

    public function rawPumps()
    {
        return $this->hasMany(TabwaterDevice::class, 'station_id')->where('type', 'pump_raw');
    }

    public function qualityLogs()
    {
        return $this->hasMany(TabwaterQualityLog::class, 'station_id');
    }

    public function latestQualityLog()
    {
        return $this->hasOne(TabwaterQualityLog::class, 'station_id')->latestOfMany();
    }

    // Helper Method: แปลงระยะไม้จุ่ม (cm) เป็นค่า NTU โดยอ้างอิง K-Factor ของสถานีนี้
    public function calculateNTUFromDepth($depthCm)
    {
        if (!$depthCm || $depthCm <= 0) {
            return null;
        }

        $kFactor = $this->secchi_k_factor ?? 350.00;
        return round($kFactor / $depthCm, 2);
    }
}