<?php

namespace App\Models\IotCompost;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CP_SensorData extends Model
{
    use HasFactory;

    protected $table = 'cp_sensor_data';
    protected $fillable = [
        "id",
        "temperature",
        "humidity",
        "methane_gas",
        "weight"	
    ];
}
