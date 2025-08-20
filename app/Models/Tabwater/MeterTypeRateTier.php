<?php

namespace App\Models\Tabwater;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeterTypeRateTier extends Model
{
    use HasFactory;

    protected $table     = 'tw_meter_type_rate_tiers';
    protected $fillable  = [
        'meter_type_rate_config_id',
        'min_units',
        'max_units',
        'rate_per_unit',
        'tier_order',
        'comment',
    ];

    public function meterTypeRateConfig()
    {
        return $this->belongsTo(MeterTypeRateConfig::class);
    }
}
