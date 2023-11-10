<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeterType extends Model
{
    use HasFactory;

    protected $table = "meter_types";
    protected $fillable = [
        'meter_type_name',
        'price_per_unit',
        'metersize',
    ];
}
