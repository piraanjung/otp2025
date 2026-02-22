<?php

namespace App\Models\Tabwater;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwPricingType extends Model
{
    use HasFactory;

    protected $table    = 'tw_pricing_types';
    protected $fillable = [
        'name',
        'description',
    ];

    public function meterTypeRateConfigs()
    {
        return $this->hasMany(MeterTypeRateConfig::class);
    }
}
