<?php

namespace App\Models\FoodWaste;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodWastIoTBoxesData extends Model
{
    use HasFactory;
    protected $table = 'foodwaste_iotboxes_data';

    protected $fillable = [
        'fwbin_id_fk',
        'esp_device_id',
        'temperature',
        'humidity',
        'methane_gas',
        'weight'
    ];
}
