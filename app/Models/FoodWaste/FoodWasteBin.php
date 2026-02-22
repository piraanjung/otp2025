<?php

namespace App\Models\FoodWaste;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodWasteBin extends Model
{
    use HasFactory;

    protected $table = 'foodwaste_bins';

    // คอลัมน์ที่อนุญาตให้ Mass Assignment
    protected $fillable = [
        'bin_code_fk',
        'u_pref_id_fk',
        'iotbox_id_fk',
        'bin_type',
        'location_description',
        'latitude',
        'longitude',
        'status',
    ];
 
    public function fw_user_preference(){
        return $this->belongsTo(FoodWasteUserPreference::class, 'u_pref_id_fk');
    }

    public function bin_stock(){
        return $this->belongsTo(FoodwasteBinStocks::class, 'bin_code_fk');
    }

    public function iotbox(){
        return $this->belongsTo(FoodwastIotbox::class, 'iotbox_id_fk', 'id');
    }

    public function iotbox_datas(){
        return $this->hasMany(FoodWastIoTBoxesData::class, 'fwbin_id_fk', 'id');
    }
}