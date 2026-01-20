<?php

namespace App\Models\Tabwater;

use App\Models\Admin\Organization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;
class MeterTypeRateConfig extends Model
{
    use HasFactory;
    use BelongsToOrganization;
    protected $table = 'tw_meter_type_rate_configs';
    protected $fillable = [
        'id',
        'org_id_fk',
        'meter_type_id_fk',
        'pricing_type_id',
        'min_usage_charge', //ค่า reserve meter
        'vat',
        'fixed_rate_per_unit',
        'effective_date',
        'end_date',
        'is_active',
        'comment',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'integer',
    ];

    public function meterType()
    {
        return $this->belongsTo(TwMeterType::class, 'meter_type_id_fk', 'id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id_fk');
    }

    

    public function pricingType()
    {
        return $this->belongsTo(TwPricingType::class);
    }

    public function Ratetiers()
    {
        // ตรวจสอบว่าเรียกใช้ Model MeterTypeRateTier ถูกต้องตาม namespace
        return $this->hasMany(MeterTypeRateTier::class, 'meter_type_rate_config_id', 'id')->orderBy('tier_order');
    }
}
